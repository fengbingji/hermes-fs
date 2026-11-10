<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/helpers.php';

class SizeGuide {
	private $post_type = 'ct_size_guide';

	public function get_dynamic_styles_data( $args ) {
		return array(
			'path' => __DIR__ . '/dynamic-styles.php',
		);
	}

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

		\Blocksy\EnqueuedAssetsCollector::declare_frontend_ajax_action(
			'blocksy_get_woo_size_guide'
		);

		add_action(
			'wp',
			function () {
				$maybe_table_id = $this->get_table_id();

				if ( $maybe_table_id ) {
					$renderer = new \Blocksy\CustomPostTypeRenderer( $maybe_table_id );
					$renderer->pre_output();
				}
			}
		);

		add_filter(
			'blocksy:editor:post_types_for_rest_field',
			function ( $post_types ) {
				$post_types[] = $this->post_type;
				return $post_types;
			}
		);

		add_filter(
			'blocksy:editor:post_meta_options',
			function ( $options, $post_type ) {
				if ( $post_type !== $this->post_type ) {
					return $options;
				}

				global $post;

				$post_id = $post->ID;

				return blocksy_companion_akg(
					'options',
					blocksy_companion_get_variables_from_file(
						__DIR__ . '/options.php',
						array( 'options' => array() )
					)
				);
			},
			10,
			2
		);

		add_filter(
			'blocksy:woocommerce:single-product:additional-actions',
			function ( $actions ) {
				$actions[] = array(
					'id'      => 'has_size_guide',
					'label'   => __( 'Size Guide', 'blocksy-companion' ),
					'options' => array(
						'label' => array(
							'type'   => 'text',
							'value'  => __( 'Size Guide', 'blocksy-companion' ),
							'design' => 'block',
							'sync'   => array(
								'shouldSkip' => true,
							),
						),
					),
				);

				return $actions;
			}
		);

		add_filter(
			'blocksy:woocommerce:single-product:additional-actions:content:has_size_guide',
			function ( $content, $layer ) {
				wp_enqueue_style( 'blocksy-ext-woocommerce-extra-additional-actions-styles' );

				$content .= blocksy_companion_output_size_guide_trigger( $layer, $this->get_table_id() );
				return $content;
			},
			10,
			2
		);

		add_filter(
			'blocksy:frontend:dynamic-js-chunks',
			function ( $chunks ) {
				if ( ! class_exists( 'WC_AJAX' ) ) {
					return $chunks;
				}

				$chunks[] = array(
					'id'         => 'blocksy_ext_woo_extra_size_guide',
					'selector'   => '.ct-size-guide-button-single',
					'url'        => blocksy_companion_theme_functions()->blocksy_cdn_url(
						BLOCKSY_URL .
							'framework/premium/extensions/woocommerce-extra/static/bundle/size-guide.js'
					),
					'trigger'    => 'click',
					'has_loader' => array(
						'type'              => 'button',
						'id'                => 'ct-size-guide-modal',
						'will_open_overlay' => true,
					),
					'version'    => blocksy_companion_get_version(),
				);

				return $chunks;
			}
		);

		add_filter(
			'blocksy:general:ct-scripts-localizations',
			function ( $data ) {
				$data['dynamic_styles_selectors'][] = array(
					'selector' => '#ct-size-guide-modal',
					'url'      => blocksy_companion_theme_functions()->blocksy_cdn_url(
						BLOCKSY_URL . 'framework/premium/extensions/woocommerce-extra/static/bundle/size-guide.min.css'
					),
				);

				return $data;
			}
		);

		add_action(
			'wp_ajax_blocksy_get_woo_size_guide',
			array(
				$this,
				'get_woo_size_guide',
			)
		);

		add_action(
			'wp_ajax_nopriv_blocksy_get_woo_size_guide',
			array(
				$this,
				'get_woo_size_guide',
			)
		);

		add_filter(
			'blocksy_customizer_options:woocommerce:general:end',
			function ( $opts ) {
				$opts['has_size_guide_panel'] = blocksy_companion_get_options(
					__DIR__ . '/customizer-options.php',
					array(),
					false
				);

				return $opts;
			},
			55
		);

		add_filter(
			'manage_ct_size_guide_posts_columns',
			function ( $columns ) {
				$columns['conditions'] = __( 'Conditions', 'blocksy-companion' );

				return $columns;
			}
		);

		add_action(
			'manage_ct_size_guide_posts_custom_column',
			function ( $column, $post_id ) {
				$atts = blocksy_companion_theme_functions()->blocksy_get_post_options( $post_id );

				if ( 'conditions' === $column ) {
					$conditions = blocksy_companion_akg( 'conditions', $atts, array() );

					$conditions_manager = new \Blocksy\ConditionsManager();

					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo implode( '<br>', $conditions_manager->humanize_conditions( $conditions ) );
				}
			},
			10,
			2
		);

		add_filter(
			'blocksy:custom_post_types:supported_list',
			function ( $list ) {
				return array_values( array_diff( $list, array( $this->post_type ) ) );
			}
		);

		add_filter(
			'blocksy:hero:custom-source',
			function ( $maybe_custom_source ) {
				if ( blocksy_companion_theme_functions()->blocksy_get_screen_prefix() === $this->post_type . '_single' ) {
					return false;
				}

				return $maybe_custom_source;
			}
		);
	}

	public function get_woo_size_guide() {
		if (
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			! isset( $_GET['table_id'] )
			||
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			! isset( $_GET['product_id'] )
		) {
			wp_send_json_error();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$product_id = absint( wp_unslash( $_GET['product_id'] ) );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$table_id = absint( wp_unslash( $_GET['table_id'] ) );

		if (
			! blocksy_companion_is_post_viewable(
				array(
					'post'      => $table_id,
					'post_type' => $this->post_type,
				)
			)
		) {
			wp_send_json_error();
		}

		global $post, $product;

		$product = wc_get_product( $product_id ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- WooCommerce global

		if ( $product && get_class( $product ) === 'WC_Product_Variation' ) {
			$product = wc_get_product( $product->get_parent_id() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- WooCommerce global
		}

		if (
			! $product
			||
			! blocksy_companion_is_post_viewable( array( 'post' => $product->get_id() ) )
		) {
			wp_send_json_error();
		}

		$old_product = $product;

		$post = get_post( $product->get_id() );
		setup_postdata( $post );

		$renderer = new \Blocksy\CustomPostTypeRenderer( $table_id );

		$assets_collector = new \Blocksy\EnqueuedAssetsCollector();

		$renderer->pre_output();
		$table_html = $renderer->get_content();

		$behaviour = blocksy_companion_theme_functions()->blocksy_get_theme_mod(
			'size_guide_placement',
			'modal'
		);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['size_guide_placement'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$behaviour = sanitize_text_field( wp_unslash( $_GET['size_guide_placement'] ) );
		}

		$content = '';

		if ( 'modal' === $behaviour ) {
			$content = blocksy_companion_render_view(
				__DIR__ . '/modal-view.php',
				array(
					'table_html' => $table_html,
				)
			);
		} else {
			$content = blocksy_companion_render_view(
				__DIR__ . '/side-view.php',
				array(
					'table_html' => $table_html,
				)
			);
		}

		wp_reset_postdata();
		$product = $old_product; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- WooCommerce global

		$assets = $assets_collector->collect();

		wp_send_json_success(
			array(
				'content' => $content,
				'assets'  => $assets,
			)
		);
	}

	private function get_table_id() {
		global $product;

		$all_size_guides = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => $this->post_type,
			)
		);

		$conditions_manager = new \Blocksy\ConditionsManager();

		foreach ( $all_size_guides as $size_guide ) {
			$values = blocksy_companion_theme_functions()->blocksy_get_post_options( $size_guide->ID );

			$conditions = blocksy_companion_akg(
				'conditions',
				$values,
				array()
			);

			if (
				! $conditions_manager->condition_matches(
					$conditions,
					array( 'relation' => 'OR' )
				)
			) {
				continue;
			}

			return $size_guide->ID;
		}

		return null;
	}

	public function register_post_type() {
		$actions = array(
			'edit_post',
			'read_post',
			'delete_post',
			'edit_posts',
			'edit_others_posts',
			'publish_posts',
			'read_private_posts',
			'read',
			'delete_posts',
			'delete_private_posts',
			'delete_published_posts',
			'delete_others_posts',
			'edit_private_posts',
			'edit_published_posts',
		);

		$capabilities = array();

		foreach ( $actions as $action ) {
			$capabilities[ $action ] = blocksy_companion_get_capabilities()->get_wp_capability_by(
				'custom_post_type',
				array(
					'post_type' => $this->post_type,
					'action'    => $action,
				)
			);
		}

		register_post_type(
			$this->post_type,
			array(
				'labels'              => array(
					'name'               => __( 'Size Guides', 'blocksy-companion' ),
					'singular_name'      => __( 'Size Guide', 'blocksy-companion' ),
					'add_new'            => __( 'Add New', 'blocksy-companion' ),
					'add_new_item'       => __( 'Add New Size Guide', 'blocksy-companion' ),
					'edit_item'          => __( 'Edit Size Guide', 'blocksy-companion' ),
					'new_item'           => __( 'New Size Guide', 'blocksy-companion' ),
					'all_items'          => __( 'Size Guides', 'blocksy-companion' ),
					'view_item'          => __( 'View Size Guide', 'blocksy-companion' ),
					'search_items'       => __( 'Search Size Guides', 'blocksy-companion' ),
					'not_found'          => __( 'Nothing found', 'blocksy-companion' ),
					'not_found_in_trash' => __( 'Nothing found in Trash', 'blocksy-companion' ),
					'parent_item_colon'  => '',
				),

				'show_in_admin_bar'   => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=product',
				'publicly_queryable'  => true,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'query_var'           => true,
				'has_archive'         => false,
				'hierarchical'        => false,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'rewrite'             => array(
					'slug'       => 'ct_size_guide',
					'with_front' => false,
				),

				'supports'            => array(
					'title',
					'editor',
					'revisions',
					'custom-fields',
				),

				'capabilities'        => $capabilities,
			)
		);
	}
}
