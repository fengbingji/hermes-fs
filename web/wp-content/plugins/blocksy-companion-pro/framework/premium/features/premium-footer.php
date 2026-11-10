<?php

namespace Blocksy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PremiumFooter {
	public function __construct() {
		add_filter(
			'blocksy:footer:items-paths',
			function ( $paths ) {
				$paths[] = __DIR__ . '/premium-footer/items';
				return $paths;
			}
		);

		add_filter(
			'blocksy:footer:items-config',
			function ( $config, $item_id ) {
				if ( 'menu' === $item_id ) {
					$config['name'] = __( 'Footer Menu 1', 'blocksy-companion' );
				}

				return $config;
			},
			10,
			2
		);

		add_filter(
			'blocksy:register_nav_menus:input',
			function ( $items ) {
				$old_item   = $items['menu_mobile'];
				$old_item_2 = $items['menu_mobile_2'];
				unset( $items['menu_mobile'] );
				unset( $items['menu_mobile_2'] );

				unset( $items['footer'] );

				$items['footer']   = __( 'Footer Menu 1', 'blocksy-companion' );
				$items['footer_2'] = __( 'Footer Menu 2', 'blocksy-companion' );

				$items['menu_mobile']   = $old_item;
				$items['menu_mobile_2'] = $old_item_2;

				return $items;
			},
			50
		);

		add_action(
			'wp_ajax_blocksy_footer_get_all_conditions',
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

		add_filter(
			'blocksy:footer:sections-for-dynamic-css',
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

		add_action(
			'wp_ajax_blocksy_footer_update_all_conditions',
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
			'blocksy:footer:current_section_id',
			function ( $section_id, $all_sections ) {
				$maybe_footer = $this->maybe_get_footer_that_matches( $all_sections );

				if ( $maybe_footer ) {
					return $maybe_footer;
				}

				return $section_id;
			},
			10,
			2
		);

		add_filter(
			'blocksy:footer:items-root-paths',
			function ( $paths ) {
				$paths[] = get_template_directory() . '/inc/panel-builder/header/logo';
				$paths[] = get_template_directory() . '/inc/panel-builder/header/button';
				$paths[] = get_template_directory() . '/inc/panel-builder/header/text';

				$paths[] = __DIR__ . '/premium-header/items/content-block';
				$paths[] = __DIR__ . '/premium-header/items/search-input';
				$paths[] = __DIR__ . '/premium-header/items/language-switcher';
				$paths[] = __DIR__ . '/premium-header/items/contacts';

				return $paths;
			}
		);

		add_filter(
			'blocksy:footer:selective_refresh',
			function ( $selective_refresh ) {
				$selective_refresh[] = array(
					'id'                  => 'footer_placements_item:menu-secondary',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => '#main-container > footer.ct-footer',
					'loader_selector'     => '.footer-menu-2',
					'settings'            => array( 'footer_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Manager' ) ) {
							return;
						}

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo \Blocksy_Manager::instance()->footer_builder->render();
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'footer_placements_item:language-switcher',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => '.ct-footer [data-id="language-switcher"]',
					'settings'            => array( 'footer_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Footer_Builder_Render' ) ) {
							return;
						}

						$footer = new \Blocksy_Footer_Builder_Render();

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $footer->render_single_item( 'language-switcher' );
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'footer_placements_item:contacts',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => '#main-container > footer.ct-footer',
					'loader_selector'     => '.ct-contact-info',
					'settings'            => array( 'footer_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Manager' ) ) {
							return;
						}

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo \Blocksy_Manager::instance()->footer_builder->render();
					},
				);

				$selective_refresh[] = array(
					'id'                  => 'footer_placements_item:content-block',
					'fallback_refresh'    => false,
					'container_inclusive' => true,
					'selector'            => 'footer [data-id="content-block"]',
					'settings'            => array( 'footer_placements' ),
					'render_callback'     => function () {
						if ( ! class_exists( 'Blocksy_Footer_Builder_Render' ) ) {
							return;
						}

						$footer = new \Blocksy_Footer_Builder_Render();

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $footer->render_single_item( 'content-block' );
					},
				);

				return $selective_refresh;
			}
		);
	}

	public function get_admin_localizations() {
		return array(
			'footers_conditions' => $this->get_conditions(),
		);
	}

	public function get_conditions() {
		$option = blocksy_companion_theme_functions()->blocksy_get_theme_mod(
			'blocksy_premium_footer_conditions',
			array(
				array(
					'id'         => 'type-1',
					'conditions' => array(
						array(
							'type' => 'include',
							'rule' => 'everywhere',
						),
					),
				),
			)
		);

		if ( empty( $option ) ) {
			$option = array();
		}

		return $option;
	}

	public function set_conditions( $conditions ) {
		set_theme_mod( 'blocksy_premium_footer_conditions', $conditions );
	}

	private function maybe_get_footer_that_matches( $all_sections ) {
		$all_conditions = $this->get_conditions();

		foreach ( array_reverse( $all_sections['sections'] ) as $single_section ) {
			$conditions = array();

			foreach ( $all_conditions as $single_condition ) {
				if ( $single_condition['id'] === $single_section['id'] ) {
					$conditions = $single_condition['conditions'];
				}
			}

			$conditions_manager = new \Blocksy\ConditionsManager();

			if ( $conditions_manager->condition_matches( $conditions ) ) {
				return $single_section['id'];
			}
		}

		return null;
	}
}
