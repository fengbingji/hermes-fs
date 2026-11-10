<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Brands {
	public function __construct() {
		add_action( 'woocommerce_after_register_taxonomy', array( $this, 'register_brand_meta' ) );

		add_filter(
			'blocksy_woo_single_options_layers:defaults',
			array( $this, 'add_layer_to_default_layout' )
		);
		add_filter(
			'blocksy_woo_compare_layers:defaults',
			array( $this, 'add_layer_to_default_layout' )
		);
		add_filter(
			'blocksy_woo_card_options_layers:defaults',
			array( $this, 'add_layer_to_default_layout' )
		);
		add_filter(
			'blocksy_woo_single_right_options_layers:defaults',
			array( $this, 'add_layer_to_default_layout' )
		);

		add_filter(
			'blocksy_woo_single_options_layers:extra',
			array( $this, 'add_single_layer_options' )
		);
		add_filter(
			'blocksy_woo_compare_layers:extra',
			array( $this, 'add_compare_layer_options' )
		);
		add_filter(
			'blocksy_woo_card_options_layers:extra',
			array( $this, 'add_archive_layer_options' )
		);
		add_filter(
			'blocksy_woo_single_right_options_layers:extra',
			array( $this, 'add_single_layer_options' )
		);

		add_action(
			'blocksy:woocommerce:product:custom:layer',
			array( $this, 'product_single_render' )
		);

		add_action(
			'blocksy:woocommerce:product-card:custom:layer',
			array( $this, 'product_card_render' )
		);

		add_action(
			'blocksy:woocommerce:compare:custom:layer',
			array( $this, 'product_card_render' )
		);

		add_filter(
			'blocksy:options:woo:tabs:general:brands',
			function ( $opts ) {
				$opts[] = blocksy_companion_get_options(
					__DIR__ . '/options.php',
					array(),
					false
				);

				return $opts;
			},
			50
		);

		add_action(
			'wp',
			function () {
				if ( blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'has_woo_brands_tab', 'no' ) === 'yes' ) {
					add_filter(
						'woocommerce_product_tabs',
						array( $this, 'brands_custom_product_tab' )
					);
				}
			}
		);

		add_action(
			'woocommerce_product_duplicate',
			function ( $duplicate, $product ) {
				$terms = get_the_terms( $product->get_id(), 'product_brand' );

				if ( ! is_wp_error( $terms ) ) {
					wp_set_object_terms( $duplicate->get_id(), wp_list_pluck( $terms, 'term_id' ), 'product_brand' );
				}
			},
			999,
			2
		);

		add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'restrict_coupon_by_brand_form' ), 10, 2 );
		add_action( 'woocommerce_coupon_options_save', array( $this, 'restrict_coupon_by_brand_save' ), 10, 2 );
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'coupon_is_valid_for_product' ), 10, 4 );
		add_filter( 'woocommerce_coupon_is_valid_for_cart', array( $this, 'coupon_is_valid_for_cart' ), 10, 2 );

		add_action(
			'rest_api_init',
			function () {
				register_rest_field(
					'product',
					'ct_brands',
					array(
						'get_callback'    => function ( $post, $field_name, $request ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
							$product_id = $post['id'];

							$terms = array();
							$brands = get_the_terms( $product_id, 'product_brand' );

							if ( ! $brands ) {
								$brands = array();
							}

							foreach ( $brands as $brand ) {
								$terms[] = array(
									'id'   => $brand->term_id,
									'name' => $brand->name,
									'slug' => $brand->slug,
								);
							}

							return $terms;
						},
						'update_callback' => function ( $value, $object, $field_name ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.objectFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
							if ( ! is_array( $value ) ) {
								return;
							}

							if ( ! current_user_can( 'edit_posts' ) ) {
								return;
							}

							$terms = array();

							foreach ( $value as $brand ) {
								$brand = get_term_by( 'id', $brand['id'], 'product_brand' );

								if ( ! $brand ) {
									continue;
								}

								$terms[] = $brand->term_id;
							}

							wp_set_object_terms( $object->get_id(), $terms, 'product_brand' );

							return $value;
						},
					)
				);
			}
		);
	}

	public function is_invalid( $wc_coupon, $to_compare ) {
		$conditions = $this->get_exclude_include_conditions( $wc_coupon );

		$include = $conditions['include_product_brands'];
		$exclude = $conditions['exclude_product_brands'];

		if ( empty( $include ) ) {
			return false;
		}

		$intersect_include = array_intersect( $include, $to_compare );
		$intersect_exclude = array_intersect( $exclude, $to_compare );

		if ( count( $intersect_include ) === 0 ) {
			return true;
		}

		if ( count( $intersect_exclude ) > 0 ) {
			return true;
		}

		return false;
	}

	public function get_exclude_include_conditions( $wc_coupon ) {
		$include_product_brands = get_post_meta( $wc_coupon->get_id(), 'include_product_brands', true );
		$exclude_product_brands = get_post_meta( $wc_coupon->get_id(), 'exclude_product_brands', true );

		if ( ! $include_product_brands ) {
			$include_product_brands = array();
		}

		if ( ! $exclude_product_brands ) {
			$exclude_product_brands = array();
		}

		return array(
			'include_product_brands' => array_map( 'intval', $include_product_brands ),
			'exclude_product_brands' => array_map( 'intval', $exclude_product_brands ),
		);
	}

	public function coupon_is_valid_for_cart( $valid, $wc_coupon ) {
		if ( is_null( WC()->cart ) ) {
			return $valid;
		}

		$cart = WC()->cart->get_cart();

		$cart_brands = array();

		foreach ( $cart as $cart_item ) {
			$product = wc_get_product( $cart_item['product_id'] );

			if ( $product->get_type() === 'variation' ) {
				$product = wc_get_product( $product->get_parent_id() );
			}

			$brands = get_the_terms( $product->get_id(), 'product_brand' );

			if ( ! $brands ) {
				$brands = array();
			}

			$brands = array_map(
				function ( $brand ) {
					return $brand->term_id;
				},
				$brands
			);

			$cart_brands = array_merge( $cart_brands, $brands );
		}

		$is_invalid = $this->is_invalid( $wc_coupon, $cart_brands );

		if ( $is_invalid ) {
			return false;
		}

		return $valid;
	}

	public function coupon_is_valid_for_product( $valid, $product, $wc_coupon, $values ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		if ( $product->get_type() === 'variation' ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		$current_product_brands = get_the_terms( $product->get_id(), 'product_brand' );

		if ( ! $current_product_brands ) {
			$current_product_brands = array();
		}

		$current_product_brands = array_map(
			function ( $brand ) {
				return $brand->term_id;
			},
			$current_product_brands
		);

		$is_invalid = $this->is_invalid( $wc_coupon, $current_product_brands );

		if ( $is_invalid ) {
			return false;
		}

		return $valid;
	}

	public function restrict_coupon_by_brand_save( $post_id, $coupon ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$include_product_brands = isset( $_POST['include_product_brands'] ) ? array_map( 'intval', $_POST['include_product_brands'] ) : array();
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$exclude_product_brands = isset( $_POST['exclude_product_brands'] ) ? array_map( 'intval', $_POST['exclude_product_brands'] ) : array();

		update_post_meta( $post_id, 'include_product_brands', $include_product_brands );
		update_post_meta( $post_id, 'exclude_product_brands', $exclude_product_brands );
	}

	public function restrict_coupon_by_brand_form( $coupon_id, $coupon ) {

		$brands = get_terms(
			array(
				'taxonomy'   => 'product_brand',
				'orderby'    => 'name',
				'hide_empty' => false,
			)
		);

		$include_html = blocksy_companion_html_tag(
			'p',
			array(
				'class' => 'form-field',
			),
			blocksy_companion_html_tag(
				'label',
				array(),
				__( 'Include brands', 'blocksy-companion' )
			) .
			blocksy_companion_html_tag(
				'select',
				array(
					'id'               => 'include_product_brands',
					'name'             => 'include_product_brands[]',
					'style'            => 'width: 50%;',
					'class'            => 'wc-enhanced-select',
					'multiple'         => 'multiple',
					'data-placeholder' => __( 'No brands', 'blocksy-companion' ),
				),
				( function () use ( $coupon, $brands ) {
					$brand_ids = get_post_meta( $coupon->get_id(), 'include_product_brands', true );

					if ( ! $brand_ids ) {
						$brand_ids = array();
					}

					$output = '';

					if ( $brands ) {
						foreach ( $brands as $brand ) {
							$output .= blocksy_companion_html_tag(
								'option',
								array_merge(
									array(
										'value' => esc_attr( $brand->term_id ),
									),
									in_array( $brand->term_id, $brand_ids, true ) ? array( 'selected' => 'selected' ) : array()
								),
								esc_html( $brand->name )
							);
						}
					}

					return $output;
				} )()
			) .
			wc_help_tip(
				__(
					'Product brands that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.',
					'blocksy-companion'
				)
			)
		);

		$exclude_html = blocksy_companion_html_tag(
			'p',
			array(
				'class' => 'form-field',
			),
			blocksy_companion_html_tag(
				'label',
				array(),
				__( 'Exclude brands', 'blocksy-companion' )
			) .
			blocksy_companion_html_tag(
				'select',
				array(
					'id'               => 'exclude_product_brands',
					'name'             => 'exclude_product_brands[]',
					'style'            => 'width: 50%;',
					'class'            => 'wc-enhanced-select',
					'multiple'         => 'multiple',
					'data-placeholder' => __( 'No brands', 'blocksy-companion' ),
				),
				( function () use ( $coupon, $brands ) {
					$brand_ids = get_post_meta( $coupon->get_id(), 'exclude_product_brands', true );

					if ( ! $brand_ids ) {
						$brand_ids = array();
					}

					$output = '';

					if ( $brands ) {
						foreach ( $brands as $brand ) {
							$output .= blocksy_companion_html_tag(
								'option',
								array_merge(
									array(
										'value' => esc_attr( $brand->term_id ),
									),
									in_array( $brand->term_id, $brand_ids, true ) ? array( 'selected' => 'selected' ) : array()
								),
								esc_html( $brand->name )
							);
						}
					}

					return $output;
				} )()
			) .
			wc_help_tip(
				__(
					'Product brands that the coupon will be applied to, or that must be in the cart in order for the "Fixed cart discount" to be applied.',
					'blocksy-companion'
				)
			)
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $include_html . $exclude_html;
	}

	public function brands_custom_product_tab( $tabs ) {
		global $product;

		$brands = get_the_terms( $product->get_id(), 'product_brand' );

		if ( ! $brands || ! is_array( $brands ) ) {
			return $tabs;
		}

		if ( ! count( $brands ) ) {
			return $tabs;
		}

		$title = __( 'About Brands', 'blocksy-companion' );

		if ( blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'use_brand_name_for_tab_title', 'no' ) === 'no' ) {
			$title = blocksy_companion_safe_sprintf(
				// translators: %s is the brand name.
				__( 'About %s', 'blocksy-companion' ),
				$brands[0]->name
			);
		}

		$tabs['specific_product_tab'] = array(
			'title'    => $title,
			'priority' => 50,
			'callback' => array( $this, 'brands_custom_product_tab_render' ),
		);

		return $tabs;
	}

	// Add content to a custom product tab
	public function brands_custom_product_tab_render() {
		$brands = get_the_terms( get_the_ID(), 'product_brand' );

		if ( ! $brands || ! is_array( $brands ) ) {
			return;
		}

		if ( ! count( $brands ) ) {
			return;
		}

		$output = '';

		$tabs_type = blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'woo_tabs_type', 'type-1' );

		if ( 'type-4' === $tabs_type ) {
			$output .= blocksy_companion_html_tag(
				'h2',
				array(),
				blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'use_brand_name_for_tab_title', 'no' ) === 'no'
					? __( 'About Brands', 'blocksy-companion' )
					: blocksy_companion_safe_sprintf(
						// translators: %s is the brand name.
						__( 'About %s', 'blocksy-companion' ),
						$brands[0]->name
					)
			);
		}

		foreach ( $brands as $brand ) {
			$output .= blocksy_companion_html_tag(
				'div',
				array(
					'class' => 'ct-product-brands-tab',
				),
				do_shortcode( wpautop( $brand->description ) )
			);
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $output;
	}

	public function add_compare_layer_options( $opt ) {
		$opt = array_merge(
			$opt,
			array(
				'product_brands' => array(
					'label'   => __( 'Brands', 'blocksy-companion' ),
					'options' => array(
						'brand_logo_size'    => array(
							'label'      => __( 'Logo Size', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'value'      => 60,
							'min'        => 30,
							'max'        => 200,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_compare_layout_skip',
							),
						),

						'brand_logo_gap'     => array(
							'label'      => __( 'Logos Gap', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'value'      => 10,
							'min'        => 0,
							'max'        => 100,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_compare_layout_skip',
							),
						),

						'compare_row_sticky' => array(
							'type'  => 'ct-switch',
							'label' => __( 'Sticky Row', 'blocksy-companion' ),
							'value' => 'no',
						),
					),
				),
			)
		);

		return $opt;
	}

	public function add_single_layer_options( $opt ) {
		$opt = array_merge(
			$opt,
			array(
				'product_brands' => array(
					'label'   => __( 'Brands', 'blocksy-companion' ),
					'options' => array(

						'brand_layer_title' => array(
							'label'               => __( 'Title', 'blocksy-companion' ),
							'type'                => 'text',
							'design'              => 'block',
							'value'               => '',
							'disableRevertButton' => true,
							'sync'                => array(
								'id' => 'woo_card_layout_skip',
							),
						),

						'brand_logo_size'   => array(
							'label'      => __( 'Logo Size', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'value'      => 60,
							'min'        => 30,
							'max'        => 200,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_card_layout_skip',
							),
						),

						'brand_logo_gap'    => array(
							'label'      => __( 'Logos Gap', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'value'      => 10,
							'min'        => 0,
							'max'        => 100,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_card_layout_skip',
							),
						),

						'spacing'           => array(
							'label'      => __( 'Bottom Spacing', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'min'        => 0,
							'max'        => 100,
							'value'      => 10,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_card_layout_skip',
							),
						),
					),
				),
			)
		);

		return $opt;
	}

	public function add_archive_layer_options( $opt ) {
		$opt = array_merge(
			$opt,
			array(
				'product_brands' => array(
					'label'   => __( 'Brands', 'blocksy-companion' ),
					'options' => array(

						'brand_logo_size' => array(
							'label'      => __( 'Logo Size', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'value'      => 60,
							'min'        => 30,
							'max'        => 200,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_card_layout_skip',
							),
						),

						'brand_logo_gap'  => array(
							'label'      => __( 'Logos Gap', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'value'      => 10,
							'min'        => 0,
							'max'        => 100,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_card_layout_skip',
							),
						),

						'spacing'         => array(
							'label'      => __( 'Bottom Spacing', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'min'        => 0,
							'max'        => 100,
							'value'      => 10,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_card_layout_skip',
							),
						),
					),
				),
			)
		);

		return $opt;
	}

	public function add_layer_to_default_layout( $opt ) {
		$opt = array_merge(
			$opt,
			array(
				array(
					'id'      => 'product_brands',
					'enabled' => false,
				),
			)
		);

		return $opt;
	}

	public function register_brand_meta() {
		add_action( 'product_brand_edit_form', array( $this, 'term_options' ) );
		add_action( 'product_brand_add_form', array( $this, 'term_options' ) );

		add_action( 'edited_term', array( $this, 'save_term_meta' ), 10, 3 );
		add_action( 'create_term', array( $this, 'save_term_meta' ), 10, 3 );
	}

	public function save_term_meta( $term_id, $tt_id, $taxonomy ) {
		if (
			! (
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				isset( $_POST['action'] )
				&&
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				( 'editedtag' === $_POST['action'] || 'add-tag' === $_POST['action'] )
				&&
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				isset( $_POST['taxonomy'] )
				&&
				// phpcs:ignore WordPress.Security.NonceVerification.Missing, Generic.CodeAnalysis.AssignmentInCondition.Found, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
				( $taxonomy = get_taxonomy( sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) ) )
				&&
				current_user_can( $taxonomy->cap->edit_terms )
			)
			||
			'product_brand' !== $taxonomy->name
		) {
			return;
		}

		$values = array();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['blocksy_taxonomy_meta_options'][ blocksy_companion_post_name() ] ) ) {
			$values = json_decode(
				// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wp_unslash( $_POST['blocksy_taxonomy_meta_options'][ blocksy_companion_post_name() ] ),
				true
			);
		}

		$values = blocksy_companion_theme_functions()->blocksy_sanitize_post_meta_options( $values );

		if ( \Blocksy\ThemeFunctions::NON_EXISTING_FUNCTION === $values ) {
			return;
		}

		update_term_meta(
			$term_id,
			'thumbnail_id',
			sanitize_text_field(
				wp_unslash(
					isset( $values['icon_image']['attachment_id'] ) ? $values['icon_image']['attachment_id'] : ''
				)
			)
		);

		unset( $values['icon_image'] );

		update_term_meta(
			$term_id,
			'blocksy_taxonomy_meta_options',
			$values
		);

		/**
		 * Fires when the dynamic CSS caches should be invalidated.
		 *
		 * Listeners drop their generated CSS files/transients so the
		 * next request regenerates them.
		 *
		 * @since 1.6.2
		 * @since 1.8.0 Renamed from `blocksy:dynamic-css:regenere_css_files`.
		 */
		do_action( 'blocksy:dynamic-css:refresh-caches' );
	}

	public function term_options( $term ) {
		$values = isset( $term->term_id ) ? get_term_meta(
			$term->term_id,
			'blocksy_taxonomy_meta_options'
		) : array( array() );

		if ( empty( $values ) ) {
			$values = array( array() );
		}

		if ( ! $values[0] ) {
			$values[0] = array();
		}

		$maybe_image_id = isset( $term->term_id ) ? get_term_meta( $term->term_id, 'thumbnail_id', true ) : '';

		if ( ! empty( $maybe_image_id ) ) {
			$values[0]['icon_image'] = array(
				'attachment_id' => $maybe_image_id,
				'url'           => wp_get_attachment_image_url( $maybe_image_id, 'full' ),
			);
		}

		$options = array(
			'image'      => array(
				'label'      => __( 'Featured Image', 'blocksy-companion' ),
				'type'       => 'ct-image-uploader',
				'value'      => '',
				'attr'       => array(
					'data-type' => 'large',
				),
				'emptyLabel' => __( 'Select Image', 'blocksy-companion' ),
			),

			'icon_image' => array(
				'label'      => __( 'Featured Icon/Logo', 'blocksy-companion' ),
				'type'       => 'ct-image-uploader',
				'value'      => '',
				'attr'       => array(
					'data-type' => 'large',
				),
				'emptyLabel' => __( 'Select Image', 'blocksy-companion' ),
			),
		);

		blocksy_companion_html_tag_e(
			'div',
			array(),
			blocksy_companion_html_tag(
				'input',
				array(
					'type'         => 'hidden',
					'value'        => htmlspecialchars( wp_json_encode( $values[0] ) ),
					'data-options' => htmlspecialchars(
						wp_json_encode( $options )
					),
					'name'         => 'blocksy_taxonomy_meta_options[' . blocksy_companion_post_name() . ']',
				)
			)
		);
	}

	public function render_brands_grid( $brands ) {
		$output = '';

		foreach ( $brands as $brand ) {

			$label = blocksy_companion_html_tag(
				'a',
				array(
					'href' => esc_url( get_term_link( $brand ) ),
				),
				$brand->name
			);

			$term_atts = blocksy_companion_theme_functions()->blocksy_get_taxonomy_options( $brand->term_id );

			$maybe_image_id = isset( $brand->term_id ) ? get_term_meta( $brand->term_id, 'thumbnail_id', true ) : '';

			if ( ! empty( $maybe_image_id ) ) {
				$term_atts['icon_image'] = array(
					'attachment_id' => $maybe_image_id,
					'url'           => wp_get_attachment_image_url( $maybe_image_id, 'full' ),
				);
			}

			$maybe_image = blocksy_companion_akg( 'icon_image', $term_atts, '' );

			if (
				$maybe_image
				&&
				is_array( $maybe_image )
				&&
				isset( $maybe_image['attachment_id'] )
			) {
				$label = blocksy_companion_theme_functions()->blocksy_media(
					array(
						'attachment_id' => $maybe_image['attachment_id'],
						'size'          => 'medium',
						'ratio'         => 'original',
						'tag_name'      => 'a',
						'html_atts'     => array(
							'href'       => get_term_link( $brand ),
							'aria-label' => $brand->name,
						),
					)
				);
			}

			$output .= $label;
		}

		return $output;
	}

	public function product_single_render( $layer ) {
		if ( 'product_brands' !== $layer['id'] ) {
			return;
		}

		$brands = get_the_terms( get_the_ID(), 'product_brand' );

		if ( ! $brands || ! is_array( $brands ) ) {
			return;
		}

		if ( ! count( $brands ) ) {
			return;
		}

		$section_title = blocksy_companion_sanitize_html_for_display(
			blocksy_companion_akg( 'brand_layer_title', $layer, '' )
		);

		blocksy_companion_html_tag_e(
			'div',
			array(
				'class' => 'ct-product-brands-single',
			),
			(
				! empty( $section_title ) || is_customize_preview() ?
				blocksy_companion_html_tag(
					'span',
					array(
						'class' => 'ct-module-title',
					),
					$section_title
				) : ''
			) .
			blocksy_companion_html_tag(
				'div',
				array(
					'class' => 'ct-product-brands',
				),
				$this->render_brands_grid( $brands )
			)
		);
	}

	public function product_card_render( $layer ) {
		if ( 'product_brands' !== $layer['id'] ) {
			return;
		}

		$brands = get_the_terms( get_the_ID(), 'product_brand' );

		if ( ! $brands || ! is_array( $brands ) ) {
			return;
		}

		if ( ! count( $brands ) ) {
			return;
		}

		blocksy_companion_html_tag_e(
			'div',
			array(
				'class' => 'ct-product-brands',
			),
			$this->render_brands_grid( $brands )
		);
	}
}
