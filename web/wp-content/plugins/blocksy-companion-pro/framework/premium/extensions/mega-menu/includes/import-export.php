<?php

namespace Blocksy\Extensions\MegaMenu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ImportExport {
	private $nav_menu_items     = array();
	private $content_blocks     = array();
	private $is_only_menu_items = true;

	private $export_args = null;

	public function __construct() {
		add_action( 'load-export.php', array( $this, 'menu_exporter_show_menu_post_type_in_export_options' ) );

		add_action( 'import_start', array( $this, 'get_nav_menu_items_list' ) );
		add_action( 'import_start_partial', array( $this, 'get_nav_menu_items_list' ) );
		add_action( 'import_end', array( $this, 'menu_exporter_remapping' ) );

		add_action( 'export_wp', array( $this, 'menu_exporter_add_terms' ), 10, 1 );
	}

	public function menu_exporter_add_terms( $args ) {
		$this->export_args = $args;

		add_action( 'rss2_head', array( $this, 'menu_exporter_add_terms_rss2_head' ) );
	}

	public function menu_exporter_add_terms_rss2_head() {
		$args = $this->export_args;

		if ( ! $args ) {
			return;
		}

		if (
			'nav_menu_item' === $args['content']
			&&
			function_exists( 'wxr_nav_menu_terms' )
		) {
			wxr_nav_menu_terms();
		}
	}

	public static function menu_exporter_show_menu_post_type_in_export_options() {
		global $wp_post_types;
		$wp_post_types['nav_menu_item']->_builtin = false;
	}

	public function get_nav_menu_items_list() {
		if ( ! isset( $GLOBALS['wp_import'] ) ) {
			return;
		}

		$importer = $GLOBALS['wp_import'];

		if ( empty( $importer->posts ) ) {
			return;
		}

		$this->is_only_menu_items = true;

		foreach ( $importer->posts as $post ) {
			if ( 'nav_menu_item' === $post['post_type'] ) {
				$this->nav_menu_items[] = $post;

				continue;
			}

			$this->is_only_menu_items = false;

			if ( 'ct_content_block' === $post['post_type'] ) {
				$this->content_blocks[] = $post;
			}
		}

		if ( ! $this->is_only_menu_items ) {
			return;
		}

		foreach ( $this->nav_menu_items as $nav_menu_item ) {
			$imported_meta = isset( $nav_menu_item['postmeta'] ) ? $nav_menu_item['postmeta'] : array();

			$_menu_item_type = array_filter(
				$imported_meta,
				function ( $meta ) {
					return '_menu_item_type' === $meta['key'];
				}
			);

			if (
				empty( $_menu_item_type )
				||
				! $_menu_item_type
			) {
				continue;
			}

			$_menu_item_type      = array_pop( $_menu_item_type )['value'];
			$_menu_item_object_id = 0;

			if (
				'post_type' === $_menu_item_type
				||
				'taxonomy' === $_menu_item_type
			) {
				$_menu_item_object_id = array_filter(
					$imported_meta,
					function ( $meta ) {
						return '_menu_item_object_id' === $meta['key'];
					}
				);

				if (
					empty( $_menu_item_object_id )
					||
					! $_menu_item_object_id
				) {
					continue;
				}

				$_menu_item_object_id = array_pop( $_menu_item_object_id )['value'];
			}

			if ( 'post_type' === $_menu_item_type ) {
				$args = array(
					'post_type'      => 'any',
					'posts_per_page' => 1,

					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					'meta_query'     => array(
						array(
							'key'   => 'blocksy_original_post_id',
							'value' => $_menu_item_object_id,
						),
					),
				);

				$posts = get_posts( $args );

				if ( ! empty( $posts ) ) {
					$importer->processed_posts[ $_menu_item_object_id ] = $posts[0]->ID;
				} else {
					$object = get_post( $_menu_item_object_id );

					if ( $object ) {
						$importer->processed_posts[ $_menu_item_object_id ] = $_menu_item_object_id;
					}
				}
			}

			if ( 'taxonomy' === $_menu_item_type ) {
				$object = get_term( $_menu_item_object_id );

				if ( $object ) {
					$importer->processed_terms[ $_menu_item_object_id ] = $_menu_item_object_id;
				}
			}
		}
	}

	public function menu_exporter_remapping() {
		if ( ! isset( $GLOBALS['wp_import'] ) ) {
			return;
		}

		$importer = $GLOBALS['wp_import'];

		if ( empty( $this->nav_menu_items ) ) {
			if ( ! empty( $this->content_blocks ) ) {
				$this->remmap_content_blocks();
			}

			return;
		}

		foreach ( $this->nav_menu_items as $menu_item ) {
			if ( ! isset( $importer->processed_menu_items[ $menu_item['post_id'] ] ) ) {
				continue;
			}

			$imported_meta = $menu_item['postmeta'];

			$blocksy_post_meta_options = array_filter(
				$imported_meta,
				function ( $meta ) {
					return 'blocksy_post_meta_options' === $meta['key'];
				}
			);

			if (
				empty( $blocksy_post_meta_options )
				||
				! $blocksy_post_meta_options
			) {
				continue;
			}

			$blocksy_post_meta_options = array_pop( $blocksy_post_meta_options )['value'];

			if ( is_serialized( $blocksy_post_meta_options ) ) {
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
				$blocksy_post_meta_options = unserialize(
					$blocksy_post_meta_options,
					array( 'allowed_classes' => false )
				);
			}

			if (
				! is_array( $blocksy_post_meta_options )
				||
				strpos(
					wp_json_encode( $blocksy_post_meta_options ),
					'__PHP_Incomplete_Class'
				) !== false
			) {
				continue;
			}

			$blocksy_post_meta_options = blocksy_companion_theme_functions()->blocksy_sanitize_post_meta_options( $blocksy_post_meta_options );

			if ( \Blocksy\ThemeFunctions::NON_EXISTING_FUNCTION === $blocksy_post_meta_options ) {
				continue;
			}

			if (
				isset( $blocksy_post_meta_options['mega_menu_content_type'] )
				&&
				isset( $blocksy_post_meta_options['mega_menu_hook'] )
				&&
				'hook' === $blocksy_post_meta_options['mega_menu_content_type']
				&&
				! empty( $blocksy_post_meta_options['mega_menu_hook'] )
			) {
				$args = array(
					'post_type'      => 'ct_content_block',
					'posts_per_page' => 1,

					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					'meta_query'     => array(
						array(
							'key'   => 'blocksy_original_post_id',
							'value' => $blocksy_post_meta_options['mega_menu_hook'],
						),
					),
				);

				$posts = get_posts( $args );

				if ( ! empty( $posts ) ) {
					$blocksy_post_meta_options['mega_menu_hook'] = $posts[0]->ID;
				}
			}

			if (
				isset( $blocksy_post_meta_options['menu_item_icon'] )
				&&
				is_array( $blocksy_post_meta_options['menu_item_icon'] )
				&&
				isset( $blocksy_post_meta_options['menu_item_icon']['source'] )
				&&
				'attachment' === $blocksy_post_meta_options['menu_item_icon']['source']
				&&
				! empty( $blocksy_post_meta_options['menu_item_icon']['attachment_id'] )
			) {
				$old_attachment_id = $blocksy_post_meta_options['menu_item_icon']['attachment_id'];

				$was_already_imported = isset( $importer->processed_posts[ $old_attachment_id ] );

				// When this is the case, it means "All Content option was used".
				if ( $was_already_imported ) {
					$new_attachment_id = $importer->processed_posts[ $old_attachment_id ];

					$blocksy_post_meta_options['menu_item_icon']['attachment_id'] = $new_attachment_id;

					$blocksy_post_meta_options['menu_item_icon']['url'] = wp_get_attachment_url( $new_attachment_id );
				}

				if (
					! $was_already_imported
					&&
					$importer->fetch_attachments
					&&
					! empty( $blocksy_post_meta_options['menu_item_icon']['url'] )
				) {
					$url = $blocksy_post_meta_options['menu_item_icon']['url'];

					$post = array(
						'post_title'   => '',
						'post_content' => '',
						'post_status'  => 'inherit',
						'upload_date'  => current_time( 'mysql' ),
						'guid'         => $url,
						'post_type'    => 'attachment',
					);

					$upload = $importer->fetch_remote_file( $url, $post );

					if ( ! is_wp_error( $upload ) ) {
						$info = wp_check_filetype( $upload['file'] );

						if ( $info ) {
							$post['post_mime_type'] = $info['type'];
						}

						$new_attachment_id = wp_insert_attachment( $post, $upload['file'] );

						wp_update_attachment_metadata(
							$new_attachment_id,
							wp_generate_attachment_metadata( $new_attachment_id, $upload['file'] )
						);

						$blocksy_post_meta_options['menu_item_icon']['attachment_id'] = $new_attachment_id;

						$blocksy_post_meta_options['menu_item_icon']['url'] = wp_get_attachment_url( $new_attachment_id );
					}
				}
			}

			update_post_meta(
				$importer->processed_menu_items[ $menu_item['post_id'] ],
				'blocksy_post_meta_options',
				$blocksy_post_meta_options
			);
		}
	}

	private function remmap_content_blocks() {
		$importer = $GLOBALS['wp_import'];

		foreach ( $this->content_blocks as $content_block ) {
			if ( ! isset( $importer->processed_posts[ $content_block['post_id'] ] ) ) {
				continue;
			}

			$args = array(
				'post_type'      => 'nav_menu_item',
				'post_status'    => 'any',
				'posts_per_page' => -1,
			);

			$posts = get_posts( $args );

			$posts = array_filter(
				$posts,
				function ( $post ) use ( $content_block ) {
					$blocksy_post_meta_options = get_post_meta( $post->ID, 'blocksy_post_meta_options', true );

					if (
					isset( $blocksy_post_meta_options['mega_menu_content_type'] )
					&&
					'hook' === $blocksy_post_meta_options['mega_menu_content_type']
					&&
					isset( $blocksy_post_meta_options['mega_menu_hook'] )
					&&
					$blocksy_post_meta_options['mega_menu_hook'] === $content_block['post_id']
					) {
						return true;
					}

					return false;
				}
			);

			foreach ( $posts as $post ) {
				$blocksy_post_meta_options = get_post_meta( $post->ID, 'blocksy_post_meta_options', true );

				$blocksy_post_meta_options['mega_menu_hook'] = $importer->processed_posts[ $content_block['post_id'] ];

				update_post_meta( $post->ID, 'blocksy_post_meta_options', $blocksy_post_meta_options );
			}
		}
	}
}
