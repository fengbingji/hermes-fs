<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/helpers.php';

class WishList {
	private $wish_list_slug = null;

	public function get_dynamic_styles_data() {
		return array(
			'path' => __DIR__ . '/dynamic-styles.php',
		);
	}

	public function __construct() {
		add_action(
			'wp_enqueue_scripts',
			function () {
				if ( is_admin() ) {
					return;
				}

				$maybe_page_id = blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'woocommerce_wish_list_page' );

				if ( function_exists( 'icl_object_id' ) ) {
					$maybe_page_id = icl_object_id( $maybe_page_id, 'page', true );
				}

				if (
					(
						is_user_logged_in()
						&&
						blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'product_wishlist_display_for', 'logged_users' ) !== 'all_users'
						&&
						isset( $_SERVER['REQUEST_URI'] )
						&&
						strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), $this->wish_list_slug ) === false
					)
					||
					(
						blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'product_wishlist_display_for', 'logged_users' ) === 'all_users'
						&&
						get_the_ID() !== $maybe_page_id
						&&
						isset( $_SERVER['REQUEST_URI'] )
						&&
						strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), $this->wish_list_slug ) === false
					)
				) {
					return;
				}

				if ( ! function_exists( 'get_plugin_data' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				$data = get_plugin_data( BLOCKSY__FILE__ );

				wp_enqueue_style(
					'blocksy-ext-woocommerce-extra-product-wishlist-table-styles',
					BLOCKSY_URL .
						'framework/premium/extensions/woocommerce-extra/static/bundle/wishlist-table.min.css',
					array( 'blocksy-ext-woocommerce-extra-styles' ),
					$data['Version']
				);
			},
			50
		);

		add_action(
			'wp_enqueue_scripts',
			function () {
				if ( ! function_exists( 'get_plugin_data' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				$data = get_plugin_data( BLOCKSY__FILE__ );

				if ( is_admin() ) {
					return;
				}

				wp_enqueue_style(
					'blocksy-ext-woocommerce-extra-wishlist-styles',
					BLOCKSY_URL .
						'framework/premium/extensions/woocommerce-extra/static/bundle/wishlist.min.css',
					array( 'blocksy-ext-woocommerce-extra-styles' ),
					$data['Version']
				);
			},
			50
		);

		add_filter(
			'blocksy:header:items-paths',
			function ( $paths ) {
				$paths[] = __DIR__ . '/header-items';
				return $paths;
			}
		);

		add_filter(
			'blocksy:frontend:dynamic-js-chunks',
			function ( $chunks ) {
				if ( ! class_exists( 'WC_AJAX' ) ) {
					return $chunks;
				}

				$chunks[] = array(
					'id'         => 'blocksy_ext_woo_extra_wish_list',
					'selector'   => implode(
						', ',
						array(
							'[class*="ct-wishlist-button"]',
							'.ct-wishlist-remove',
							'.wishlist-product-remove > .remove',
							'.wishlist-product-name .product-mobile-actions > .remove',
						)
					),
					'url'        => blocksy_companion_theme_functions()->blocksy_cdn_url(
						BLOCKSY_URL .
							'framework/premium/extensions/woocommerce-extra/static/bundle/wish-list.js'
					),
					'trigger'    => 'click',
					'has_loader' => array(
						'type' => 'button',
					),
					'version'    => blocksy_companion_get_version(),
				);

				if (
				blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'has_variations_wishlist', 'no' ) === 'yes'
				) {
					$chunks[] = array(
						'id'       => 'blocksy_ext_woo_extra_variations_wish_list',
						'selector' => '.product .variations',
						'url'      => blocksy_companion_theme_functions()->blocksy_cdn_url(
							BLOCKSY_URL .
								'framework/premium/extensions/woocommerce-extra/static/bundle/variations-wish-list.js'
						),
						'deps'     => array(
							'underscore',
							'wc-add-to-cart-variation',
							'wp-util',
						),
						'trigger'  => array(
							'selector' => '.product .variations',
							'trigger'  => 'jquery-event',
							'events'   => array(
								'found_variation',
								'reset_data',
							),
						),
						'version'  => blocksy_companion_get_version(),
					);
				}

				$cache_manager = new \Blocksy\CacheResetManager();

				if ( $cache_manager->is_there_any_page_caching() ) {
					$chunks[] = array(
						'id'       => 'blocksy_ext_woo_extra_wish_list',
						'selector' => implode(
							', ',
							array(
								'.ct-header-wishlist',
								'[class*="ct-wishlist-button"]',
								'.ct-wishlist-remove',
								'.wishlist-product-remove > .remove',
								'.wishlist-product-name .product-mobile-actions > .remove',
							)
						),
						'url'      => blocksy_companion_theme_functions()->blocksy_cdn_url(
							BLOCKSY_URL .
								'framework/premium/extensions/woocommerce-extra/static/bundle/wish-list.js'
						),
						'version'  => blocksy_companion_get_version(),
					);
				}

				return $chunks;
			}
		);

		add_filter(
			'blocksy:header:selective_refresh',
			function (
				$selective_refresh
			) {
				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:wish-list',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => 'header [data-id="wish-list"]',
					'settings'            => array( 'header_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Header_Builder_Render' ) ) {
							return;
						}

						$header = new \Blocksy_Header_Builder_Render();
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $header->render_single_item( 'wish-list' );
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:wish-list:offcanvas',
					'fallback_refresh'    => false,
					'container_inclusive' => false,
					'selector'            => '#offcanvas',
					'loader_selector'     => '[data-id="wish-list"]',
					'settings'            => array( 'header_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Header_Builder_Elements' ) ) {
							return;
						}

						$elements = new \Blocksy_Header_Builder_Elements();

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $elements->render_offcanvas(
							array(
								'has_container' => false,
							)
						);
					},
				);

				return $selective_refresh;
			}
		);

		add_filter(
			'blocksy_customizer_options:woocommerce:general:end',
			function ( $opts ) {
				$opts['has_wishlist_panel'] = blocksy_companion_get_options(
					__DIR__ . '/options.php',
					array(),
					false
				);

				return $opts;
			},
			50
		);

		$this->boot_wish_list();
	}

	public function boot_wish_list() {
		add_filter(
			'blocksy:general:ct-scripts-localizations',
			function (
				$data
			) {
				$data['blc_ext_wish_list'] = array(
					'user_logged_in' => is_user_logged_in() ? 'yes' : 'no',
					'list'           => array(
						'v'     => 2,
						'items' => $this->get_current_wish_list(),
					),
				);

				return $data;
			}
		);

		add_action(
			'init',
			function () {
				$this->wish_list_slug = blocksy_companion_get_wish_list_slug();

				add_rewrite_endpoint( $this->wish_list_slug, EP_ROOT | EP_PAGES );

				add_action(
					'woocommerce_account_' . $this->wish_list_slug . '_endpoint',
					function () {
						blocksy_companion_render_view_e(
							__DIR__ . '/table.php',
							array()
						);
					}
				);

				add_filter(
					'document_title_parts',
					function ( $title ) {
						if ( function_exists( 'is_account_page' ) && is_account_page() ) {
							global $wp;

							if ( isset( $wp->query_vars[ $this->wish_list_slug ] ) ) {
								/**
								 * Filters the wish list My Account page document title.
								 *
								 * @since 2.1.39
								 *
								 * @param string $title The page title. Default 'Wishlist'.
								 */
								$title['title'] = apply_filters(
									'blocksy:pro:woocommerce-extra:wish-list:page-title',
									__( 'Wishlist', 'blocksy-companion' )
								);
							}
						}

						return $title;
					}
				);
			}
		);

		add_filter(
			'query_vars',
			function ( $vars ) {
				$vars[] = $this->wish_list_slug;
				return $vars;
			},
			0
		);

		add_action(
			'woocommerce_after_add_to_cart_button',
			function () {
				/**
				 * Fires after the single product add to cart button, in the
				 * place where the wish list button is rendered.
				 *
				 * @since 1.8.47
				 */
				do_action( 'blocksy:pro:woo-extra:wishlist:button:output' );
			},
			90
		);

		add_filter(
			'the_content',
			function ( $content ) {
				if (
				blocksy_companion_theme_functions()->blocksy_get_theme_mod(
					'product_wishlist_display_for',
					'logged_users'
				) === 'logged_users'
				) {
					return $content;
				}

				$maybe_page_id = blocksy_companion_theme_functions()->blocksy_get_theme_mod(
					'woocommerce_wish_list_page'
				);

				if ( function_exists( 'icl_object_id' ) ) {
					$maybe_page_id = icl_object_id( $maybe_page_id, 'page', true );
				}

				if ( empty( $maybe_page_id ) ) {
					return $content;
				}

				if ( ! is_page( $maybe_page_id ) ) {
					return $content;
				}

				return $content .
				blocksy_companion_render_view( __DIR__ . '/table.php', array() );
			}
		);

		add_action(
			'woocommerce_account_menu_items',
			function ( $items ) {
				$logout = $items['customer-logout'];
				unset( $items['customer-logout'] );

				$items[ $this->wish_list_slug ] = __(
					'Wishlist',
					'blocksy-companion'
				);
				$items['customer-logout']       = $logout;

				return $items;
			}
		);

		add_filter(
			'woocommerce_account_menu_item_classes',
			function ( $classes, $endpoint ) {
				if ( $endpoint === $this->wish_list_slug ) {
					$classes[] = 'ct-wish-list';
				}

				return $classes;
			},
			10,
			2
		);

		add_action(
			'wp_ajax_blc_ext_wish_list_sync_likes',
			array(
				$this,
				'sync_wish_list',
			)
		);

		add_action(
			'wp_ajax_nopriv_blc_ext_wish_list_sync_likes',
			array(
				$this,
				'sync_wish_list',
			)
		);

		add_action(
			'wp_ajax_blc_ext_wish_list_get_all_likes',
			array(
				$this,
				'get_all_likes',
			)
		);

		add_action(
			'wp_ajax_nopriv_blc_ext_wish_list_get_all_likes',
			array(
				$this,
				'get_all_likes',
			)
		);

		add_action(
			'wp_login',
			function ( $user_login, $user ) {
				if ( ! $user ) {
					return;
				}

				$cookie_value = $this->get_cookie_wish_list();
				$user_value   = $this->get_user_wish_list( $user->get( 'ID' ) );

				$final_value = $this->normalize_wish_list_items(
					array_merge( $user_value, $cookie_value )
				);

				update_user_meta(
					$user->get( 'ID' ),
					'blc_products_wish_list',
					array(
						'v'     => 2,
						'items' => $final_value,
					)
				);

				setcookie( 'blc_products_wish_list', false );
			},
			10,
			2
		);

		add_filter(
			'blocksy_woo_card_options:additional_actions',
			function (
				$actions
			) {
				$actions[] = array(
					'id'    => 'has_archive_wishlist',
					'label' => __( 'Wishlist Button', 'blocksy-companion' ),
				);

				return $actions;
			},
			1
		);

		// Archive product card toolbar. High priorities keep these companion
		// buttons at the very end of the toolbar (after add to cart and any other
		// hooks), in the theme's previous fixed order: wishlist (1000), compare
		// (1100), quick view (1200).
		add_filter(
			'blocksy:options:woocommerce:archive:card-type:output_product_toolbar',
			function ( $components ) {
				$wish_list = blocksy_companion_output_add_to_wish_list( 'archive' );

				if ( ! empty( $wish_list ) ) {
					$components[] = $wish_list;
				}

				return $components;
			},
			1000
		);

		add_filter(
			'blocksy:woocommerce:single-product:additional-actions',
			function ( $actions ) {
				$actions[] = array(
					'id'      => 'has_wishlist',
					'label'   => __( 'Wishlist', 'blocksy-companion' ),
					'options' => array(
						'label' => array(
							'type'   => 'text',
							'value'  => __( 'Wishlist', 'blocksy-companion' ),
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
			'blocksy:woocommerce:single-product:additional-actions:content:has_wishlist',
			function ( $content, $layer ) {
				wp_enqueue_style( 'blocksy-ext-woocommerce-extra-additional-actions-styles' );

				$content .= blocksy_companion_output_add_to_wish_list( 'single', $layer );
				return $content;
			},
			10,
			2
		);

		add_filter(
			'blocksy-companion:pro:header:account:dropdown-items',
			function ( $layer_settings ) {
				if ( function_exists( 'wc_get_endpoint_url' ) ) {
					$layer_settings['wishlist'] = array(
						'label'   => __( 'Wishlist', 'blocksy-companion' ),
						'options' => array(
							'label' => array(
								'type'   => 'text',
								'value'  => __( 'Wishlist', 'blocksy-companion' ),
								'design' => 'inline',
								'sync'   => array(
									'shouldSkip' => true,
								),
							),
						),
					);
				}

				return $layer_settings;
			}
		);
	}

	public function sync_wish_list() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error();
		}

		$origin = get_http_origin();

		if ( $origin && ! is_allowed_http_origin( $origin ) ) {
			wp_send_json_error();
		}

		$likes = blocksy_companion_get_json_request_body();

		if (
			! $likes
			||
			! isset( $likes['items'] )
			||
			! is_array( $likes['items'] )
			||
			count( $likes['items'] ) > 500
		) {
			wp_send_json_error();
		}

		$items = $this->normalize_wish_list_items( $likes['items'] );

		if ( empty( $items ) ) {
			delete_user_meta( get_current_user_id(), 'blc_products_wish_list' );
		} else {
			update_user_meta(
				get_current_user_id(),
				'blc_products_wish_list',
				array(
					'v'     => 2,
					'items' => $items,
				)
			);
		}

		wp_send_json_success( array() );
	}

	public function get_all_likes() {
		wp_send_json_success(
			array(
				'likes'          => array(
					'v'     => 2,
					'items' => $this->get_current_wish_list(),
				),
				'user_logged_in' => is_user_logged_in() ? 'yes' : 'no',
			)
		);
	}

	public function get_current_wish_list() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['wish_list_id'] ) ) {
			$user_id = $this->get_user_id_by_share_token(
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				sanitize_text_field( wp_unslash( $_GET['wish_list_id'] ) )
			);

			if ( ! $user_id ) {
				return array();
			}

			return $this->get_user_wish_list( $user_id );
		}

		if ( is_user_logged_in() ) {
			return $this->get_user_wish_list( get_current_user_id() );
		}

		return $this->get_cookie_wish_list();
	}

	public function get_share_token() {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return '';
		}

		$token = get_user_meta( $user_id, 'blc_products_wish_list_share_token', true );

		if ( ! $token ) {
			$token = wp_generate_password( 32, false );
			update_user_meta( $user_id, 'blc_products_wish_list_share_token', $token );
		}

		return $token;
	}

	private function get_user_id_by_share_token( $token ) {
		if ( ! is_string( $token ) || ! preg_match( '/^[A-Za-z0-9]{32}$/', $token ) ) {
			return 0;
		}

		$users = get_users(
			array(
				'meta_key'   => 'blc_products_wish_list_share_token', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value' => $token, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'number'     => 1,
				'fields'     => 'ID',
			)
		);

		if ( empty( $users ) ) {
			return 0;
		}

		return intval( $users[0] );
	}

	private function normalize_list( $items ) {
		$new_list = array();

		foreach ( $items as $item ) {
			if ( gettype( $item ) !== 'integer' ) {
				$new_list[] = $item;

				continue;
			}

			$new_list[] = (object) array( 'id' => $item );
		}

		return $new_list;
	}

	private function normalize_wish_list_items( $items ) {
		$normalized = array();

		foreach ( $items as $item ) {
			if ( is_numeric( $item ) ) {
				$item = array( 'id' => $item );
			}

			if (
				! is_array( $item )
				||
				! isset( $item['id'] )
				||
				! is_numeric( $item['id'] )
			) {
				continue;
			}

			$product = wc_get_product( absint( $item['id'] ) );

			if ( ! $product || 'trash' === $product->get_status() ) {
				continue;
			}

			$entry = array(
				'id' => $product->get_id(),
			);

			if ( ! empty( $item['attributes'] ) ) {
				$attributes = $this->normalize_item_attributes( $product, $item['attributes'] );

				if ( ! empty( $attributes ) ) {
					$entry['attributes'] = $attributes;
				}
			}

			if ( ! in_array( $entry, $normalized, true ) ) {
				$normalized[] = $entry;
			}
		}

		return $normalized;
	}

	private function normalize_item_attributes( $product, $attributes ) {
		if (
			! is_array( $attributes )
			||
			! method_exists( $product, 'get_variation_attributes' )
		) {
			return array();
		}

		$allowed_keys = array();

		foreach ( array_keys( $product->get_variation_attributes() ) as $key ) {
			$allowed_keys[] = sanitize_title( preg_replace( '/^attribute_/', '', $key ) );
		}

		$normalized = array();

		foreach ( $attributes as $key => $value ) {
			if (
				! in_array( $key, $allowed_keys, true )
				||
				! is_scalar( $value )
			) {
				continue;
			}

			$normalized[ $key ] = wp_strip_all_tags( (string) $value );
		}

		ksort( $normalized );

		return $normalized;
	}

	private function get_user_wish_list( $id ) {
		$value = get_user_meta( $id, 'blc_products_wish_list', true );

		if ( ! $value ) {
			return array();
		}

		return $this->cleanup_wishlist( $value );
	}

	private function get_cookie_wish_list() {
		if ( ! isset( $_COOKIE['blc_products_wish_list'] ) ) {
			return array();
		}

		$maybe_decoded = json_decode(
			stripslashes( sanitize_text_field( wp_unslash( $_COOKIE['blc_products_wish_list'] ) ) ),
			true
		);

		if ( ! $maybe_decoded ) {
			return array();
		}

		if ( ! is_array( $maybe_decoded ) && is_numeric( $maybe_decoded ) ) {
			return $this->cleanup_wishlist( array( intval( $maybe_decoded ) ) );
		}

		return $this->cleanup_wishlist( $maybe_decoded );
	}

	private function filter_existin_products( $product_id ) {
		$product = wc_get_product( $product_id );

		return $product && $product->get_status() !== 'trash';
	}

	private function cleanup_wishlist( $input ) {
		$normalized_input = $input;

		// Migrate from v2 to v1
		if (
			is_array( $input ) &&
			isset( $input['v'] ) &&
			isset( $input['items'] ) &&
			! empty( $input['items'] )
		) {
			return array_filter(
				$input['items'],
				function ( $item ) {
					if (
						! function_exists( 'wc_get_product' )
						||
						! isset( $item['id'] )
					) {
						return false;
					}

					return $this->filter_existin_products( $item['id'] );
				}
			);
		}

		return array_map(
			function ( $item ) {
				return $item;
			},
			array_filter(
				$normalized_input,
				function ( $item ) {
					if ( ! function_exists( 'wc_get_product' ) ) {
						return false;
					}

					return $this->filter_existin_products( $item );
				}
			)
		);
	}
}
