<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ActiveFilters {
	public function __construct() {
		add_filter(
			'blocksy:options:woocommerce:archive:active-filters',
			function ( $opts ) {
				$opts[] = array(
					'woo_has_active_filters' => array(
						'label'         => __( 'Active Filters', 'blocksy-companion' ),
						'type'          => 'ct-panel',
						'switch'        => true,
						'value'         => 'no',
						'sync'          => blocksy_companion_theme_functions()->blocksy_sync_whole_page(
							array(
								'prefix'          => 'woo_categories',
								'loader_selector' => '.ct-container > section',
							)
						),
						'inner-options' => array(
							'woo_has_active_filters_label' => array(
								'label'   => __( 'Active Filters Label', 'blocksy-companion' ),
								'type'    => 'ct-switch',
								'value'   => 'yes',
								'divider' => 'top',
							),

							blocksy_companion_rand_md5()   => array(
								'type'      => 'ct-condition',
								'condition' => array( 'woo_has_active_filters_label' => 'yes' ),
								'options'   => array(

									'woo_active_filters_label' => array(
										'label'  => false,
										'type'   => 'text',
										'design' => 'block',
										'value'  => __( 'Active Filters', 'blocksy-companion' ),
										'disableRevertButton' => true,
										'sync'   => 'live',
									),

								),
							),

						),
					),
				);

				return $opts;
			},
			50
		);

		add_action(
			'woocommerce_before_shop_loop',
			function () {
				if ( blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'woo_has_active_filters', 'no' ) === 'no' ) {
					return;
				}

				add_action(
					'woocommerce_before_shop_loop',
					array( $this, 'active_filters' ),
					105
				);
			},
			0
		);

		add_action(
			'woocommerce_no_products_found',
			function () {
				if ( blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'woo_has_active_filters', 'no' ) === 'no' ) {
					return;
				}

				add_action(
					'woocommerce_no_products_found',
					array( $this, 'active_filters' ),
					9
				);
			},
			0
		);

		add_action( 'init', array( $this, 'blocksy_active_filters_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_admin' ) );
	}

	public function blocksy_active_filters_block() {
		register_block_type(
			BLOCKSY_PATH . '/framework/premium/extensions/woocommerce-extra/static/js/blocks/active-filters/block.json',
			array(
				'render_callback' => function ( $attributes, $content, $block ) {
					$attributes = wp_parse_args(
						$attributes,
						array(
							'layout'          => 'list',
							'showResetButton' => 'yes',
							'has_label'       => false,
						)
					);

					ob_start();
					$this->active_filters( $attributes );
					$filters = ob_get_clean();

					if ( empty( $filters ) ) {
						return '';
					}

					return $filters;
				},
			)
		);
	}

	public function enqueue_admin() {
		$data = get_plugin_data( BLOCKSY__FILE__ );

		wp_enqueue_script(
			'blocksy/active-filters',
			BLOCKSY_URL .
				'framework/premium/extensions/woocommerce-extra/static/bundle/active-filters.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor' ),
			$data['Version'],
			true
		);
	}

	public static function get_reset_url() {
		$params = FiltersUtils::get_query_params();

		$url    = $params['url'];
		$params = $params['params'];

		$all_filters_params = array();

		foreach ( Filters::get_filter_instance() as $filter ) {
			$filter_params = $filter->get_query_params();

			if ( empty( $filter_params ) ) {
				continue;
			}

			$all_filters_params = array_merge(
				$all_filters_params,
				$filter_params
			);
		}

		foreach ( $all_filters_params as $param ) {
			unset( $params[ $param ] );
		}

		$url = remove_query_arg(
			$all_filters_params,
			$url
		);

		return $url;
	}

	public static function get_reset_params() {
		$params = array( 'filter_by_rating_exact_match' );

		foreach ( Filters::get_filter_instance() as $filter ) {
			$params = array_merge( $params, $filter->get_query_params() );
		}

		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $attribute ) {
				$params[] = 'query_type_' . $attribute->attribute_name;
			}
		}

		return FiltersUtils::get_present_params( array( 'candidates' => $params ) );
	}

	public function active_filters( $attributes = array() ) {
		if ( '' === $attributes ) {
			$attributes = array(
				'layout'          => 'inline',
				'showResetButton' => 'yes',
				'has_label'       => true,
			);
		}

		$applied_filters = $this->get_applied_filters();

		if ( count( $applied_filters ) < 1 ) {
			return;
		}

		$template_path = __DIR__ . '/views/list-filter.php';

		if ( 'inline' === $attributes['layout'] ) {
			$template_path = __DIR__ . '/views/inline-filter.php';
		}

		blocksy_companion_render_view_e(
			$template_path,
			array_merge(
				$attributes,
				array(
					'applied_filters' => $applied_filters,
					'reset_params'    => self::get_reset_params(),
				)
			)
		);
	}

	public function get_applied_filters() {
		$result = array();

		foreach ( Filters::get_filter_instance() as $filter ) {
			$applied = $filter->get_applied_filters();

			if ( $applied ) {
				if ( isset( $applied['name'] ) ) {
					$applied = array( $applied );
				}

				foreach ( $applied as $applied_filter ) {
					$result[] = $this->add_reset_controls(
						$applied_filter,
						$filter
					);
				}
			}
		}

		return $result;
	}

	private function add_reset_controls( $applied_filter, $filter ) {
		foreach ( $applied_filter['items'] as $key => $item ) {
			if ( isset( $item['control'] ) ) {
				continue;
			}

			$applied_filter['items'][ $key ]['control'] = array(
				'data-filter-reset' => implode(
					',',
					FiltersUtils::get_present_params(
						array( 'candidates' => $filter->get_query_params() )
					)
				),
			);
		}

		return $applied_filter;
	}
}
