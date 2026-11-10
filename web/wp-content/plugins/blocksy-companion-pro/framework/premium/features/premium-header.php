<?php

namespace Blocksy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PremiumHeader {
	public function __construct() {
		add_filter(
			'blocksy:header:items-config',
			function ( $config, $id ) {
				$allowed_items = array(
					'trigger',
					'mobile-menu',
					'mobile-menu-secondary',
					'offcanvas-logo',
				);

				if ( in_array( $id, $allowed_items, true ) ) {
					$config['devices'] = array( 'desktop', 'mobile' );
				}

				if ( 'mobile-menu' === $id ) {
					$config['name'] = __( 'Mobile Menu 1', 'blocksy-companion' );

					$config['allowed_in'] = array(
						'desktop' => array( 'offcanvas' ),
					);
				}

				return $config;
			},
			10,
			2
		);

		add_filter(
			'blocksy:header:sections-for-dynamic-css',
			function ( $sections, $value ) {
				$result = array();

				foreach ( $value['sections'] as $section ) {
					if (
					'type-1' === $section['id']
					||
					(
						strpos( $section['id'], 'ct-custom-' ) !== false
						&&
						'ct-custom-transparent' !== $section['id']
					)
					) {
						$result[] = $section;
					}
				}

				return $result;
			},
			10,
			2
		);

		add_filter(
			'blocksy:register_nav_menus:input',
			function ( $items ) {
				unset( $items['menu_mobile'] );

				$items['menu_3']        = __( 'Header Menu 3', 'blocksy-companion' );
				$items['menu_mobile']   = __( 'Mobile Menu 1', 'blocksy-companion' );
				$items['menu_mobile_2'] = __( 'Mobile Menu 2', 'blocksy-companion' );

				return $items;
			}
		);

		add_filter(
			'blocksy:header:items-paths',
			function ( $paths ) {
				$paths[] = __DIR__ . '/premium-header/items';
				return $paths;
			}
		);

		add_filter(
			'blocksy:header:selective_refresh',
			function ( $selective_refresh ) {
				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:language-switcher',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => 'header [data-id="language-switcher"]',
					'settings'            => array( 'header_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Header_Builder_Render' ) ) {
							return;
						}

						$header = new \Blocksy_Header_Builder_Render();
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $header->render_single_item( 'language-switcher' );
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:color-mode-switcher',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => 'header [data-id="color-mode-switcher"]',
					'settings'            => array( 'header_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Header_Builder_Render' ) ) {
							return;
						}

						$header = new \Blocksy_Header_Builder_Render();
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $header->render_single_item( 'color-mode-switcher' );
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:search-input',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => 'header [data-id="search-input"]',
					'settings'            => array( 'header_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Header_Builder_Render' ) ) {
							return;
						}

						$header = new \Blocksy_Header_Builder_Render();
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $header->render_single_item( 'search-input' );
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:language-switcher:offcanvas',
					'fallback_refresh'    => false,
					'container_inclusive' => false,
					'selector'            => '#offcanvas',
					'loader_selector'     => '[data-id="language-switcher"]',
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

				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:contacts',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => '#main-container > header',
					'loader_selector'     => '[data-id="contacts"]',
					'settings'            => array( 'header_placements' ),
					'render_callback'     => function () {
						if ( ! blocksy_companion_theme_functions()->blocksy_manager() ) {
							return;
						}

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo blocksy_companion_theme_functions()->blocksy_manager()->header_builder->render();
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:contacts:offcanvas',
					'fallback_refresh'    => false,
					'container_inclusive' => false,
					'selector'            => '#offcanvas',
					'loader_selector'     => '[data-id="contacts"]',
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

				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:menu-tertiary',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => '#main-container > header',
					'loader_selector'     => '[data-id="menu-tertiary"]',
					'settings'            => array( 'header_placements' ),
					'render_callback'     => function () {
						if ( ! blocksy_companion_theme_functions()->blocksy_manager() ) {
							return;
						}

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo blocksy_companion_theme_functions()->blocksy_manager()->header_builder->render();
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:content-block',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => 'header [data-id="content-block"]',
					'settings'            => array( 'header_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Header_Builder_Render' ) ) {
							return;
						}

						$header = new \Blocksy_Header_Builder_Render();

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $header->render_single_item( 'content-block' );
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'header_placements_item:content-block:offcanvas',
					'fallback_refresh'    => false,
					'container_inclusive' => false,
					'selector'            => '#offcanvas',
					'loader_selector'     => '[data-id="content-block"]',
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

		add_action(
			'blocksy:widgets_init',
			function ( $sidebar_title_tag ) {
				$number_of_sidebars = 1;

				for ( $i = 1; $i <= $number_of_sidebars; $i++ ) {
					register_sidebar(
						array(
							'id'            => 'ct-header-sidebar-' . $i,
							'name'          => esc_html__( 'Header Widget Area ', 'blocksy-companion' ),
							'before_widget' => '<div class="ct-widget %2$s">',
							'after_widget'  => '</div>',
							'before_title'  => '<' . $sidebar_title_tag . ' class="widget-title">',
							'after_title'   => '</' . $sidebar_title_tag . '>',
						)
					);
				}
			},
			10
		);

		add_filter(
			'blocksy:header:current_section_id',
			function ( $section_id, $all_sections ) {
				$maybe_header = $this->maybe_get_header_that_matches( $all_sections );

				if ( $maybe_header ) {
					return $maybe_header;
				}

				return $section_id;
			},
			10,
			2
		);

		add_action(
			'wp_ajax_blocksy_header_get_all_conditions',
			function () {
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error();
				}

				wp_send_json_success(
					array(
						'conditions' => $this->get_conditions(),
					)
				);
			}
		);

		add_action(
			'wp_ajax_blocksy_header_update_all_conditions',
			function () {
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error();
				}

				if ( ! check_ajax_referer( 'ct-dashboard', 'nonce', false ) ) {
					wp_send_json_error();
				}

				$data = blocksy_companion_get_json_request_body();

				if ( ! is_array( $data ) ) {
					wp_send_json_error();
				}

				$this->set_conditions( $data );

				wp_send_json_success();
			}
		);

		add_filter(
			'blocksy:header:button:options:after-link-options',
			function ( $opts ) {
				$opts['icon'] = array(
					'type'    => 'icon-picker',
					'label'   => __( 'Icon', 'blocksy-companion' ),
					'design'  => 'inline',
					'divider' => 'top:full',
					'value'   => array(
						'icon' => '',
					),
				);

				$opts[ blocksy_companion_rand_md5() ] = array(
					'type'      => 'ct-condition',
					'condition' => array(
						'any' => array(
							'icon/icon:truthy' => 'yes',
							'icon/url:truthy'  => 'yes',
						),
					),
					'options'   => array(

						'cta_button_icon_size' => array(
							'label'      => __( 'Icon Size', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'design'     => 'block',
							'divider'    => 'top',
							'min'        => 5,
							'max'        => 50,
							'value'      => 15,
							'responsive' => true,
						),

						'cta_button_gap'       => array(
							'label'      => __( 'Icon Gap', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'value'      => '0.5em',
							'divider'    => 'top',
							'units'      => blocksy_companion_theme_functions()->blocksy_units_config(
								array(
									array(
										'unit' => '%',
										'min'  => 0,
										'max'  => 50,
									),
									array(
										'unit' => 'px',
										'min'  => 0,
										'max'  => 600,
									),
									array(
										'unit' => 'pt',
										'min'  => 0,
										'max'  => 500,
									),
									array(
										'unit' => 'em',
										'min'  => 0,
										'max'  => 100,
									),
									array(
										'unit' => 'rem',
										'min'  => 0,
										'max'  => 100,
									),
									array(
										'unit' => 'vw',
										'min'  => 0,
										'max'  => 50,
									),
									array(
										'unit' => 'vh',
										'min'  => 0,
										'max'  => 50,
									),
									array(
										'unit' => '',
										'type' => 'custom',
									),
								)
							),
							'responsive' => true,
						),

						'icon_position'        => array(
							'type'    => 'ct-radio',
							'label'   => __( 'Icon Position', 'blocksy-companion' ),
							'value'   => 'left',
							'view'    => 'text',
							'design'  => 'block',
							'divider' => 'top',

							'choices' => array(
								'left'  => __( 'Left', 'blocksy-companion' ),
								'right' => __( 'Right', 'blocksy-companion' ),
							),
						),

					),

				);

				return $opts;
			}
		);

		add_filter(
			'blocksy:header:cart:options:icon',
			function ( $opts ) {
				$new_opt                = array();
				$new_opt['icon_source'] = array(
					'label'   => __( 'Icon Source', 'blocksy-companion' ),
					'type'    => 'ct-radio',
					'value'   => 'default',
					'view'    => 'text',
					'design'  => 'block',
					'divider' => 'bottom',
					'setting' => array( 'transport' => 'postMessage' ),
					'choices' => array(
						'default' => __( 'Default', 'blocksy-companion' ),
						'custom'  => __( 'Custom', 'blocksy-companion' ),
					),
				);

				$new_opt[ blocksy_companion_rand_md5() ] = array(
					'type'      => 'ct-condition',
					'condition' => array( 'icon_source' => 'default' ),
					'options'   => $opts,
				);

				$new_opt[ blocksy_companion_rand_md5() ] = array(
					'type'      => 'ct-condition',
					'condition' => array( 'icon_source' => 'custom' ),
					'options'   => array(
						'icon' => array(
							'type'    => 'icon-picker',
							'label'   => __( 'Icon', 'blocksy-companion' ),
							'design'  => 'inline',
							'divider' => 'bottom',
							'value'   => array(
								'icon' => 'blc blc-cart',
							),
						),
					),
				);

				return $new_opt;
			}
		);

		add_filter(
			'blocksy:header:search:options:icon',
			function ( $opts ) {

				$opts['icon'] = array(
					'type'    => 'icon-picker',
					'label'   => __( 'Icon', 'blocksy-companion' ),
					'design'  => 'inline',
					'divider' => 'bottom',
					'value'   => array(
						'icon' => 'blc blc-search',
					),
				);

				return $opts;
			}
		);

		add_filter(
			'blocksy:general:card:options:icon',
			function ( $opts, $default_icon = '' ) {
				$opts['icon'] = array(
					'type'   => 'icon-picker',
					'label'  => __( 'Icon', 'blocksy-companion' ),
					'design' => 'inline',
					'value'  => array(
						'icon' => $default_icon,
					),
				);

				return $opts;
			},
			0,
			2
		);

		add_action(
			'wp_enqueue_scripts',
			function () {
				if ( ! function_exists( 'get_plugin_data' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				if ( is_admin() ) {
					return;
				}

				if ( ! function_exists( 'blocksy_media' ) ) {
					return;
				}

				if (
					! class_exists( 'Blocksy_Header_Builder_Render' )
					||
					! class_exists( 'Blocksy_Footer_Builder_Render' )
				) {
					return;
				}

				$render        = new \Blocksy_Header_Builder_Render();
				$footer_render = new \Blocksy_Footer_Builder_Render();

				$data = get_plugin_data( BLOCKSY__FILE__ );

				if (
				$render->contains_item( 'language-switcher' )
				||
				$footer_render->contains_item( 'language-switcher' )
				||
				is_customize_preview()
				) {
					wp_enqueue_style(
						'blocksy-pro-language-switcher-styles',
						BLOCKSY_URL . 'framework/premium/static/bundle/language-switcher.min.css',
						array( 'ct-main-styles' ),
						$data['Version']
					);
				}

				if (
				$render->contains_item( 'search-input' )
				||
				is_customize_preview()
				) {
					wp_enqueue_style(
						'blocksy-pro-search-input-styles',
						BLOCKSY_URL . 'framework/premium/static/bundle/search-input.min.css',
						array( 'ct-main-styles' ),
						$data['Version']
					);
				}

				if (
				$render->contains_item( 'divider' )
				||
				is_customize_preview()
				) {
					wp_enqueue_style(
						'blocksy-pro-divider-styles',
						BLOCKSY_URL . 'framework/premium/static/bundle/divider.min.css',
						array( 'ct-main-styles' ),
						$data['Version']
					);
				}
			},
			50
		);
	}

	private function maybe_get_header_that_matches( $all_sections ) {
		$all_conditions = $this->get_conditions();

		foreach ( array_reverse( $all_sections['sections'] ) as $single_section ) {
			$conditions = array();

			if ( strpos( $single_section['id'], 'ct-custom' ) === false ) {
				continue;
			}

			foreach ( $all_conditions as $single_condition ) {
				if ( $single_condition['id'] === $single_section['id'] ) {
					$conditions = $single_condition['conditions'];
				}
			}

			$conditions_manager = new \Blocksy\ConditionsManager();

			if ( $conditions_manager->condition_matches(
				$conditions,
				/**
				 * Filters the arguments used to match the conditions of a custom header.
				 *
				 * @since 1.8.29
				 *
				 * @param array $args {
				 *     Condition matching arguments.
				 *
				 *     @type string $relation How the conditions are combined. Default 'OR'.
				 * }
				 * @param array $single_section Header section descriptor being evaluated.
				 */
				apply_filters(
					'blocksy:pro:header:condition-match-args',
					array(
						'relation' => 'OR',
					),
					$single_section
				)
			) ) {
				return $single_section['id'];
			}
		}

		return null;
	}

	public function get_conditions() {
		$option = blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'blocksy_premium_header_conditions', array() );

		if ( empty( $option ) ) {
			return array();
		}

		return $option;
	}

	public function set_conditions( $conditions ) {
		set_theme_mod( 'blocksy_premium_header_conditions', $conditions );
	}
}
