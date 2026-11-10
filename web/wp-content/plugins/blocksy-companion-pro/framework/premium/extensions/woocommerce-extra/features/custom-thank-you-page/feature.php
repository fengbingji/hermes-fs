<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomThankYouPage {
	private $post_type = 'ct_thank_you_page';

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );

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

		add_action(
			'wp',
			function () {
				$order = self::get_received_order();

				if (
					! $order
					||
					$order->get_status() === 'failed'
				) {
					return;
				}

				$matching_thank_you_pages = $this->get_mathing_thank_you_page( $order );

				if ( empty( $matching_thank_you_pages ) ) {
					return;
				}

				$renderer = new \Blocksy\CustomPostTypeRenderer( $matching_thank_you_pages[0]->ID );
				$renderer->pre_output();
			}
		);

		add_filter(
			'render_block',
			function ( $block_content, $block ) {
				if (
				'woocommerce/classic-shortcode' !== $block['blockName']
				||
				! isset( $block['attrs']['shortcode'] )
				||
				'checkout' !== $block['attrs']['shortcode']
				) {
					return $block_content;
				}

				return $this->render_custom_ty_content( $block_content );
			},
			10,
			2
		);

		add_filter(
			'do_shortcode_tag',
			function ( $output, $tag ) {
				if ( 'woocommerce_checkout' !== $tag ) {
					return $output;
				}

				return $this->render_custom_ty_content( $output );
			},
			10,
			2
		);

		add_action( 'woocommerce_product_data_tabs', array( $this, 'add_product_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'render_thank_you_product_tab' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_ty_for_product' ) );

		add_filter(
			'manage_ct_thank_you_page_posts_columns',
			function ( $columns ) {
				$columns['ct_categories'] = __( 'Categories', 'blocksy-companion' );

				$available_gateways = ( new \WC_Payment_Gateways() )->get_available_payment_gateways();
				$available_gateways = wp_list_pluck( $available_gateways, 'title' );

				if ( ! empty( $available_gateways ) ) {
					$columns['gateways'] = __( 'Payment Gateways', 'blocksy-companion' );
				}

				$shipping_zones = \WC_Shipping_Zones::get_zones();

				if ( ! empty( $shipping_zones ) ) {
					$columns['shippings'] = __( 'Shipping Methods', 'blocksy-companion' );
				}

				return $columns;
			}
		);

		add_action(
			'manage_ct_thank_you_page_posts_custom_column',
			function ( $column, $post_id ) {
				$atts = blocksy_companion_theme_functions()->blocksy_get_post_options( $post_id );

				if ( 'ct_categories' === $column ) {
					$custom_ty_categories = blocksy_companion_akg(
						'custom_ty_categories',
						$atts,
						array()
					);

					foreach ( $custom_ty_categories as $id => $enabled ) {
						if ( $enabled ) {
							$term = get_term_by( 'id', $id, 'product_cat' );

							if ( ! $term ) {
								continue;
							}

							echo esc_html( $term->name ) . '<br>';
						}
					}
				}

				if ( 'gateways' === $column ) {
					$custom_ty_gateways = blocksy_companion_akg(
						'custom_ty_gateways',
						$atts,
						array()
					);

					$available_gateways = ( new \WC_Payment_Gateways() )->get_available_payment_gateways();
					$available_gateways = wp_list_pluck( $available_gateways, 'title' );

					foreach ( $custom_ty_gateways as $id => $enabled ) {
						if (
							$enabled
							&&
							isset( $available_gateways[ $id ] )
						) {
							echo esc_html( $available_gateways[ $id ] ) . '<br>';
						}
					}
				}

				if ( 'shippings' === $column ) {
					$custom_ty_shippings = blocksy_companion_akg(
						'custom_ty_shippings',
						$atts,
						array()
					);

					$shipping_zones = \WC_Shipping_Zones::get_zones();

					if ( ! empty( $shipping_zones ) ) {
						$shipping_options = call_user_func_array(
							'array_merge',
							array_map(
								function ( $zone ) {
									$zone_name = $zone['zone_name'];

									return array_map(
										function ( $shipping_method ) use ( $zone_name ) {
											return array(
												'value' => blocksy_companion_safe_sprintf( '%s - %s', $zone_name, $shipping_method->get_title() ),
												'key'   => $shipping_method->get_instance_id(),
											);
										},
										$zone['shipping_methods']
									);
								},
								$shipping_zones
							)
						);

						foreach ( $custom_ty_shippings as $id => $enabled ) {
							if ( $enabled ) {
								$maybe_shipping_method = array_search(
									(int) $id,
									array_column( $shipping_options, 'key' ),
									true
								);

								if ( false === $maybe_shipping_method ) {
									continue;
								}

								echo esc_html( $shipping_options[ $maybe_shipping_method ]['value'] ) . '<br>';
							}
						}
					}
				}
			},
			10,
			2
		);

		add_filter(
			'blocksy:custom_post_types:supported_list',
			function ( $post_types ) {
				return array_values( array_diff( $post_types, array( $this->post_type ) ) );
			}
		);

		add_filter(
			'blocksy:hero:custom-source',
			function ( $maybe_custom_source ) {
				if ( is_singular( $this->post_type ) ) {
					return false;
				}

				return $maybe_custom_source;
			}
		);
	}

	public static function get_received_order() {
		$order_id = absint( get_query_var( 'order-received' ) );

		if ( ! $order_id ) {
			return null;
		}

		$order_id = apply_filters( 'woocommerce_thankyou_order_id', $order_id ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		$order_key = '';

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['key'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order_key = wc_clean( wp_unslash( $_GET['key'] ) );
		}

		$order_key = apply_filters( 'woocommerce_thankyou_order_key', $order_key ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		$order = wc_get_order( $order_id );

		if (
			! $order instanceof \WC_Order
			||
			! is_string( $order_key )
			||
			! hash_equals( $order->get_order_key(), $order_key )
		) {
			return null;
		}

		$customer_id = $order->get_customer_id();

		if (
			$customer_id
			&&
			apply_filters( 'woocommerce_order_received_verify_known_shoppers', true ) // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			&&
			get_current_user_id() !== $customer_id
		) {
			return null;
		}

		$users_class = \Automattic\WooCommerce\Internal\Utilities\Users::class;

		if ( method_exists( $users_class, 'should_user_verify_order_email' ) ) {
			$supplied_email = null;

			if (
				wp_verify_nonce(
					filter_input( INPUT_POST, 'check_submission' ),
					'wc_verify_email'
				)
			) {
				$supplied_email = sanitize_email(
					wp_unslash( filter_input( INPUT_POST, 'email' ) )
				);
			}

			if (
				$users_class::should_user_verify_order_email(
					$order->get_id(),
					$supplied_email,
					'order-received'
				)
			) {
				return null;
			}
		}

		return $order;
	}

	public function render_custom_ty_content( $fallback ) {
		$order = self::get_received_order();

		if (
			! $order
			||
			$order->get_status() === 'failed'
		) {
			return $fallback;
		}

		$matching_thank_you_pages = $this->get_mathing_thank_you_page( $order );

		if ( empty( $matching_thank_you_pages ) ) {
			return $fallback;
		}

		$result = '';

		$renderer = new \Blocksy\CustomPostTypeRenderer( $matching_thank_you_pages[0]->ID );

		$result .= $renderer->get_content();

		remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );

		ob_start();
		do_action( 'woocommerce_thankyou', $order->get_id() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$result .= ob_get_clean();

		return $result;
	}

	public function save_ty_for_product( $post_id ) {
		if (
			empty( $_POST['woocommerce_meta_nonce'] )
			||
			! wp_verify_nonce( sanitize_key( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' )
		) {
			return;
		}

		$thank_you_page_id = isset( $_POST['ct_thank_you_page_id'] ) ? absint( $_POST['ct_thank_you_page_id'] ) : 0;

		if ( ! $thank_you_page_id ) {
			delete_post_meta( $post_id, '_ct_thank_you_page_id' );
			return;
		}

		update_post_meta( $post_id, '_ct_thank_you_page_id', $thank_you_page_id );
	}


	public function add_product_tab( $tabs ) {
		$tabs['ct-custom-thank-you'] = array(
			'label'    => __( 'Thank you Page', 'blocksy-companion' ),
			'target'   => 'ct_custom_thank_you',
			'class'    => array(),
			'priority' => 120,
		);

		return $tabs;
	}

	public function render_thank_you_product_tab() {
		blocksy_companion_render_view_e(
			__DIR__ . '/select.php',
			array()
		);
	}

	public function get_post_type() {
		return $this->post_type;
	}

	public function get_mathing_thank_you_page( $order ) {
		$categories            = $this->get_order_categories( $order );
		$custom_ty_per_product = $this->get_custom_ty_per_product( $order );
		$shipping_methods      = $order->get_shipping_methods();
		$payment_method        = $order->get_payment_method();

		$all_ty_pages = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => $this->post_type,
				'fields'      => 'ids',
			)
		);

		if ( empty( $all_ty_pages ) ) {
			return array();
		}

		$matching_thank_you_pages = array();

		$matching_thank_you_pages = array_filter(
			$all_ty_pages,
			function ( $ty_page ) use ( $categories, $payment_method, $shipping_methods ) {
				$atts = blocksy_companion_theme_functions()->blocksy_get_post_options( $ty_page );

				$selected_categories = blocksy_companion_akg(
					'custom_ty_categories',
					$atts,
					array()
				);

				$found_category = false;
				foreach ( $categories as $cat ) {
					if (
						isset( $selected_categories[ $cat ] )
						&&
						$selected_categories[ $cat ]
					) {
						$found_category = true;
					}
				}

				if ( $found_category ) {
					return true;
				}

				$selected_payment_methods = blocksy_companion_akg(
					'custom_ty_gateways',
					$atts,
					array()
				);

				if (
					isset( $selected_payment_methods[ $payment_method ] )
					&&
					$selected_payment_methods[ $payment_method ]
				) {
					return true;
				}

				$selected_shipping_methods = blocksy_companion_akg(
					'custom_ty_shippings',
					$atts,
					array()
				);

				$found_shipping_method = false;

				foreach ( $shipping_methods as $shipping_method ) {
					if (
						isset( $selected_shipping_methods[ $shipping_method->get_instance_id() ] )
						&&
						$selected_shipping_methods[ $shipping_method->get_instance_id() ]
					) {
						$found_shipping_method = true;
					}
				}

				if ( $found_shipping_method ) {
					return true;
				}

				return false;
			}
		);

		if ( empty(
			array_merge(
				$matching_thank_you_pages,
				$custom_ty_per_product
			)
		) ) {
			return array();
		}

		$matching_thank_you_pages = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => $this->post_type,
				'post__in'    => array_merge(
					$matching_thank_you_pages,
					$custom_ty_per_product
				),
			)
		);

		usort(
			$matching_thank_you_pages,
			function ( $a, $b ) {
				$a_atts = blocksy_companion_theme_functions()->blocksy_get_post_options( $a->ID );
				$b_atts = blocksy_companion_theme_functions()->blocksy_get_post_options( $b->ID );

				$a_priority = blocksy_companion_akg( 'priority', $a_atts, 0 );
				$b_priority = blocksy_companion_akg( 'priority', $b_atts, 0 );

				return $b_priority - $a_priority;
			}
		);

		return $matching_thank_you_pages;
	}

	public function get_custom_ty_per_product( $order ) {
		$items = $order->get_items();

		$custom_ty = array();

		foreach ( $items as $item ) {
			$product_id = $item['product_id'];

			$custom_type_meta = get_post_meta( $product_id, '_ct_thank_you_page_id', true );

			if (
				$custom_type_meta
				&&
				'0' !== $custom_type_meta
			) {
				$custom_ty[] = $custom_type_meta;
			}
		}

		return array_unique( $custom_ty );
	}

	public function get_order_categories( $order ) {
		$items = $order->get_items();

		$categories = array();

		foreach ( $items as $item ) {
			$product_id = $item['product_id'];
			$terms      = get_the_terms( $product_id, 'product_cat' );

			foreach ( $terms as $term ) {
				$categories[] = $term->term_id;
			}
		}

		return array_unique( $categories );
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
					'name'               => __( 'Thank You Pages', 'blocksy-companion' ),
					'singular_name'      => __( 'Thank You Page', 'blocksy-companion' ),
					'add_new'            => __( 'Add New', 'blocksy-companion' ),
					'add_new_item'       => __( 'Add New Thank You Page', 'blocksy-companion' ),
					'edit_item'          => __( 'Edit Thank You Page', 'blocksy-companion' ),
					'new_item'           => __( 'New Thank You Page', 'blocksy-companion' ),
					'all_items'          => __( 'Thank You Pages', 'blocksy-companion' ),
					'view_item'          => __( 'View Thank You Page', 'blocksy-companion' ),
					'search_items'       => __( 'Search Thank You Pages', 'blocksy-companion' ),
					'not_found'          => __( 'Nothing found', 'blocksy-companion' ),
					'not_found_in_trash' => __( 'Nothing found in Trash', 'blocksy-companion' ),
					'parent_item_colon'  => '',
				),

				'show_in_admin_bar'   => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => current_user_can( 'manage_options' ) ? 'woocommerce-marketing' : false,
				'publicly_queryable'  => true,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'query_var'           => true,
				'has_archive'         => false,
				'hierarchical'        => false,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'rewrite'             => array(
					'slug'       => 'ct_thank_you_page',
					'with_front' => false,
				),

				'supports'            => array(
					'title',
					'editor',
					'custom-fields',
				),

				'capabilities'        => $capabilities,
			)
		);
	}
}
