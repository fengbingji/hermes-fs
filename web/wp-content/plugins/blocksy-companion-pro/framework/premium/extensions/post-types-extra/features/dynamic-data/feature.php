<?php

namespace Blocksy\Extensions\PostTypesExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DynamicData {
	public $custom_fields_manager = null;

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );

		add_action(
			'after_setup_theme',
			function () {
				$this->custom_fields_manager = new CustomFieldsManager();
			}
		);
	}

	public function init() {
		// Options
		add_filter(
			'blocksy:options:page-title:design:before_breadcrumbs',
			function ( $opts, $prefix ) {
				return $this->add_design_options( $opts, $prefix, 'hero_elements' );
			},
			10,
			2
		);

		add_filter(
			'blocksy:options:posts-listing:design:before_card_background',
			function ( $opts, $prefix ) {
				return $this->add_design_options( $opts, $prefix, 'archive_order' );
			},
			10,
			2
		);

		add_filter(
			'blocksy:options:meta:meta_default_elements',
			function ( $layers, $prefix, $computed_cpt ) {
				$opt = $this->complement_layers_option(
					array(
						'value'    => array(),
						'settings' => array(),
					),
					$computed_cpt,
					array(
						'has_icon'         => true,
						'has_label_option' => false,
						'has_spacing'      => false,
					)
				);

				foreach ( $opt['value'] as $layer ) {
					$layers[] = $layer;
				}

				return $layers;
			},
			10,
			3
		);

		add_filter(
			'blocksy:options:meta:meta_elements',
			function ( $layers, $prefix, $computed_cpt ) {
				foreach ( $this->complement_layers_option(
					array(
						'value'    => array(),
						'settings' => array(),
					),
					$computed_cpt,
					array(
						'has_icon'         => true,
						'has_label_option' => false,
						'has_spacing'      => false,
					)
				)['settings'] as $id => $layer ) {
					$layers[ $id ] = $layer;
				}

				return $layers;
			},
			10,
			3
		);

		add_filter(
			'blocksy:options:page-title:hero-elements',
			function ( $option, $prefix ) {
				if (
					'single_blog_post' !== $prefix
					&&
					'single_page' !== $prefix
					&&
					'product' !== $prefix
					&&
					strpos( $prefix, '_single' ) === false
				) {
					return $option;
				}

				return $this->complement_layers_option( $option, $prefix );
			},
			10,
			2
		);

		add_filter(
			'blocksy:options:posts-listing-archive-order',
			function ( $option, $prefix ) {
				return $this->complement_layers_option(
					$option,
					$prefix,
					array(
						'has_spacing' => true,
					)
				);
			},
			10,
			2
		);

		add_filter(
			'blocksy:options:posts-listing-related-order',
			function ( $option, $prefix ) {
				return $this->complement_layers_option(
					$option,
					$prefix,
					array(
						'has_spacing' => true,
					)
				);
			},
			10,
			2
		);

		add_filter(
			'blocksy_woo_card_options_layers:defaults',
			array( $this, 'get_product_layer_default' ),
			10,
			1
		);

		add_filter(
			'blocksy_woo_single_options_layers:defaults',
			array( $this, 'get_product_layer_default' ),
			10,
			1
		);

		add_filter(
			'blocksy_woo_single_right_options_layers:defaults',
			array( $this, 'get_product_layer_default' ),
			10,
			1
		);

		add_filter(
			'blocksy_woo_card_options_layers:extra',
			array( $this, 'get_product_layer_extra' ),
			10,
			1
		);

		add_filter(
			'blocksy_woo_single_options_layers:extra',
			array( $this, 'get_product_layer_extra' ),
			10,
			1
		);

		// Rendering

		add_action(
			'blocksy:woocommerce:product:custom:layer',
			function ( $atts ) {
				$this->render_dynamic_field_layer( $atts );
			}
		);

		add_action(
			'blocksy:woocommerce:product-card:custom:layer',
			function ( $atts ) {
				$this->render_dynamic_field_layer( $atts );
			}
		);

		add_action(
			'blocksy:post-meta:render-meta',
			array( $this, 'render_meta_layer' ),
			10,
			3
		);

		add_action(
			'blocksy:hero:element:render',
			array( $this, 'render_dynamic_field_layer' )
		);

		add_filter(
			'blocksy:archive:render-card-layer',
			function ( $output, $atts ) {
				$maybe_layer = $this->render_dynamic_field_layer( $atts, false );

				if ( ! empty( $maybe_layer ) ) {
					return $maybe_layer;
				}

				return $output;
			},
			10,
			2
		);

		add_filter(
			'blocksy:related:render-card-layer',
			function ( $output, $atts ) {
				$maybe_layer = $this->render_dynamic_field_layer( $atts, false );

				if ( ! empty( $maybe_layer ) ) {
					return $maybe_layer;
				}

				return $output;
			},
			10,
			2
		);
	}

	public function get_product_layer_default( $layers ) {
		$opt = $this->complement_layers_option(
			array(
				'value'    => array(),
				'settings' => array(),
			),
			'product',
			array(
				'has_icon'         => false,
				'has_label_option' => true,
				'has_spacing'      => true,
			)
		);

		foreach ( $opt['value'] as $layer ) {
			$layers[] = $layer;
		}

		return $layers;
	}

	public function get_product_layer_extra( $layers ) {
		foreach ( $this->complement_layers_option(
			array(
				'value'    => array(),
				'settings' => array(),
			),
			'product',
			array(
				'has_icon'         => false,
				'has_label_option' => true,
				'has_spacing'      => true,
			)
		)['settings'] as $id => $layer ) {
			$layers[ $id ] = $layer;
		}

		return $layers;
	}

	public function complement_layers_option( $option, $prefix, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'has_icon'         => false,
				'has_label_option' => true,
				'has_spacing'      => false,
			)
		);

		$post_type = blocksy_companion_theme_functions()->blocksy_manager()
			->screen
			->compute_post_type_for_prefix( $prefix );

		$fields = $this->custom_fields_manager->get_fields(
			array(
				'type'      => 'post_type',
				'post_type' => $post_type,
			),
			array(
				'provider' => 'all',
			)
		);

		foreach ( $fields as $fields_descriptor ) {
			$args['provider']       = $fields_descriptor['provider'];
			$args['provider_label'] = $fields_descriptor['label'];

			$args['fields'] = array();

			foreach ( $fields_descriptor['fields'] as $field ) {
				$args['fields'][ $field->get_id() ] = $field->get_label();
			}

			$option = $this->complement_option_for( $option, $args );
		}

		return $option;
	}

	public function complement_option_for( $option, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'provider'         => 'acf',
				'provider_label'   => 'ACF',

				'fields'           => array(),

				'has_icon'         => false,
				'has_label_option' => true,
				'has_spacing'      => false,

				'prefix'           => '',
			)
		);

		$fields = $args['fields'];

		$option['value'][] = array(
			'id'      => $args['provider'] . '_field',
			'enabled' => false,
		);

		$options = array(
			'text' => array(
				'label' => ' ',
				'type'  => 'html',
				'html'  => blocksy_companion_safe_sprintf(
					// translators: %s is the provider label (ACF, Toolset, etc.)
					__(
						'You have no %s fields declared for this custom post type.',
						'blocksy-companion'
					),
					$args['provider_label']
				),
			),
		);

		if ( count( $fields ) > 0 ) {
			$options = array(
				'field' => array(
					'label'              => __( 'Field', 'blocksy-companion' ),
					'type'               => 'ct-select',
					'view'               => 'text',
					'defaultToFirstItem' => false,
					'value'              => array_keys( $fields )[0],
					'design'             => 'inline',
					'choices'            => $fields,
				),
			);

			if ( $args['has_label_option'] ) {
				$options['label'] = array(
					'type'   => 'ct-switch',
					'label'  => __( 'Label', 'blocksy-companion' ),
					'design' => 'inline',
					'value'  => 'no',
				);
			}

			if ( $args['has_icon'] ) {
				$options[ blocksy_companion_rand_md5() ] = array(
					'type'          => 'ct-condition',
					'condition'     => array( 'meta_type' => 'icons' ),
					'values_source' => 'parent',
					'options'       => array(
						'icon' => array(
							'type'   => 'icon-picker',
							'label'  => __( 'Icon', 'blocksy-companion' ),
							'design' => 'inline',
							'value'  => array(
								'icon' => 'blc blc-heart',
							),
						),
					),
				);
			}

			$options['value_before'] = array(
				'type'   => 'text',
				'label'  => __( 'Before', 'blocksy-companion' ),
				'design' => 'inline',
				'value'  => '',
				'sync'   => array(
					'prefix' => $args['prefix'],
					'id'     => $args['prefix'] . '_dynamic_data_sync',
				),
			);

			$options['value_after'] = array(
				'type'   => 'text',
				'label'  => __( 'After', 'blocksy-companion' ),
				'design' => 'inline',
				'value'  => '',

				'sync'   => array(
					'prefix' => $args['prefix'],
					'id'     => $args['prefix'] . '_dynamic_data_sync',
				),
			);

			$options['value_fallback'] = array(
				'type'   => 'text',
				'label'  => __( 'Fallback', 'blocksy-companion' ),
				'design' => 'inline',
				'value'  => '',
				'sync'   => array(
					'prefix' => $args['prefix'],
					'id'     => $args['prefix'] . '_dynamic_data_sync',
				),
			);

			if ( $args['has_spacing'] ) {
				$options['spacing'] = array(
					'label'      => __( 'Bottom Spacing', 'blocksy-companion' ),
					'type'       => 'ct-slider',
					'min'        => 0,
					'max'        => 100,
					'value'      => 'product' === $args['prefix'] ? 10 : 20,
					'responsive' => true,

					'sync'       => array(
						'id' => 'woo_card_layout_skip',
					),
				);
			}
		}

		$option['settings'][ $args['provider'] . '_field' ] = array(
			'label'   => blocksy_companion_safe_sprintf(
				// translators: %s is the provider label (ACF, Toolset, etc.)
				__( '%s Field', 'blocksy-companion' ),
				$args['provider_label']
			) . ' INDEX',
			'options' => $options,
			'clone'   => 15,
		);

		return $option;
	}

	public function render_dynamic_field_layer( $atts, $echo = true ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.echoFound -- public method signature
		$field = $this->get_field_to_render( $atts );

		if ( ! $field ) {
			return '';
		}

		$output = $field['value'];

		$value_fallback = blocksy_companion_akg( 'value_fallback', $atts, '' );

		$has_fallback = false;

		if ( empty( $output ) && ! empty( $value_fallback ) ) {
			$has_fallback = true;
			$output       = do_shortcode( blocksy_companion_sanitize_html_for_display( $value_fallback ) );
		}

		if ( empty( $output ) ) {
			return '';
		}

		$value_before = blocksy_companion_akg( 'value_before', $atts, '' );
		$value_after  = blocksy_companion_akg( 'value_after', $atts, '' );

		if ( ! $has_fallback ) {
			$output = blocksy_companion_sanitize_wrapped_html_for_display(
				array(
					'before'       => $value_before,
					'value'        => $output,
					'after'        => $value_after,
					'trusted_html' => ! empty( $field['trusted_html'] ),
				)
			);
		}

		if ( blocksy_companion_akg( 'label', $atts, 'no' ) === 'yes' ) {
			$output = '<span>' . $field['label'] . '</span>' . $output;
		}

		$attr = array(
			'class' => 'ct-dynamic-data-layer',
		);

		$attr['data-field'] = $field['name'];

		if ( isset( $atts['__id'] ) ) {
			$attr['data-field'] .= ':' . substr( $atts['__id'], 0, 6 );
		}

		$layer = blocksy_companion_html_tag( 'div', $attr, $output );

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $layer;
		}

		return $layer;
	}

	public function render_meta_layer( $id, $meta, $args ) {
		$field = $this->get_field_to_render( $meta );

		if ( ! $field ) {
			return;
		}

		$value_fallback = blocksy_companion_akg( 'value_fallback', $meta, '' );

		$value = $field['value'];

		$has_fallback = false;

		if ( empty( $value ) && ! empty( $value_fallback ) ) {
			$has_fallback = true;
			$value        = do_shortcode( blocksy_companion_sanitize_html_for_display( $value_fallback ) );
		}

		if ( ! is_string( $value ) ) {
			return;
		}

		if ( empty( trim( $value ) ) ) {
			return;
		}

		$value_before = blocksy_companion_akg( 'value_before', $meta, '' );
		$value_after  = blocksy_companion_akg( 'value_after', $meta, '' );

		if ( ! $has_fallback ) {
			$value = blocksy_companion_sanitize_wrapped_html_for_display(
				array(
					'before'       => $value_before,
					'value'        => $value,
					'after'        => $value_after,
					'trusted_html' => ! empty( $field['trusted_html'] ),
				)
			);
		}

		if ( 'label' === $args['meta_type'] ) {
			$value = '<span>' . $field['label'] . '</span>' . $value;
		}

		if (
			'icons' === $args['meta_type']
			||
			$args['force_icons']
		) {
			$value = blocksy_companion_get_icon(
				array(
					'icon_descriptor' => blocksy_companion_akg(
						'icon',
						$meta,
						array(
							'icon' => 'blc blc-heart',
						)
					),
					'icon_container'  => false,
				)
			) . $value;
		}

		/**
		 * Filters the rendered value of a custom field meta layer.
		 *
		 * @since 2.0.5
		 *
		 * @param string $value The rendered field value markup.
		 * @param array  $field The custom field descriptor.
		 * @param array  $meta  The meta layer options.
		 */
		$value = apply_filters(
			'blocksy:pro:post-types-extra:post-meta:rendered-value',
			$value,
			$field,
			$meta
		);

		blocksy_companion_html_tag_e(
			'li',
			array(
				'class'      => 'meta-custom-field',
				'data-field' => $field['name'],
			),
			$value
		);
	}

	private function get_field_to_render( $atts ) {
		if (
			! isset( $atts['id'] )
			||
			'_field' !== substr( $atts['id'], -6 )
		) {
			return null;
		}

		$provider = str_replace( '_field', '', $atts['id'] );

		// Just for default field
		$maybe_fields_descriptor = $this->custom_fields_manager->get_fields(
			array(
				'type'      => 'post_type',

				// It's safe to get current global post type because we are in
				// the theme's main layout context. There cannot be any other
				// post type context here.
				'post_type' => get_post_type(),
			),
			array(
				'provider' => $provider,
			)
		);

		if ( ! $maybe_fields_descriptor ) {
			return null;
		}

		$first_field = $maybe_fields_descriptor['fields'][0];

		$field_id = blocksy_companion_akg( 'field', $atts, $first_field->get_id() );

		return $this->custom_fields_manager->render_field(
			$field_id,
			array(
				'provider' => $provider,
			)
		);
	}

	public function add_design_options( $opts, $prefix, $key ) {
		$options = blocksy_companion_get_options(
			__DIR__ . '/options.php',
			array(
				'prefix' => $prefix,
				'key'    => $key,
			),
			false
		);

		return array_merge( $opts, $options );
	}
}
