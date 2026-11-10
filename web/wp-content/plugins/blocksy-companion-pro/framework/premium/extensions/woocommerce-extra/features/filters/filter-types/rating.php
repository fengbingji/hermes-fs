<?php

namespace Blocksy\Extensions\WoocommerceExtra;

class RatingFilter extends BaseFilter {
	private $product_ids_cache = array();

	private static $filter_param = 'filter_by_rating';

	public function get_filter_id() {
		return 'rating_filter';
	}

	public function get_reset_url( $attributes = array() ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET[ self::$filter_param ] ) ) {
			return remove_query_arg( self::get_query_params() );
		}

		return false;
	}

	public function get_reset_params() {
		return self::get_query_params();
	}

	public function render( $attributes = array() ) {
		$ratings_html = array();
		$extra        = '';

		if ( $attributes['exactMatch'] ) {
			$extra = self::$filter_param . '_exact_match=1';
		}

		$built_in_ratings = self::get_rating_options();

		foreach ( $built_in_ratings as $rating => $rating_label ) {
			$is_active = FilterPresenter::is_filter_active(
				self::$filter_param,
				$rating
			);

			$tooltip = '';

			if ( $attributes['showTooltips'] ) {
				$tooltip_label = $rating_label;

				if ( ! $attributes['exactMatch'] && intval( $rating ) !== 5 ) {
					$tooltip_label = blocksy_companion_safe_sprintf(
						// translators: %s is replaced with the minimum star label for the filter tooltip.
						__( '%s and up', 'blocksy-companion' ),
						$rating_label
					);
				}

				$tooltip = blocksy_companion_html_tag(
					'span',
					array(
						'class' => 'ct-tooltip',
					),
					$tooltip_label
				);
			}

			$ratings_html[] = blocksy_companion_html_tag(
				'li',
				array(
					'class' => 'ct-filter-item' . ( $is_active ? ' active' : '' ),
				),
				blocksy_companion_html_tag(
					'a',
					FilterPresenter::get_control_attributes(
						array(
							'label'       => $rating_label,
							'value'       => $rating,
							'is_active'   => $is_active,
							'mark_active' => false,
						)
					),
					$tooltip .
					'<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
					</svg>'
				)
			);
		}

		if ( empty( $ratings_html ) ) {
			return '';
		}

		return blocksy_companion_html_tag(
			'div',
			array(
				'class' => 'ct-rating-filter',
			),
			blocksy_companion_html_tag(
				'ul',
				array_merge(
					array(
						'class'             => 'ct-filter-widget',
						'data-display-type' => 'inline',
					),
					FilterPresenter::get_widget_attributes(
						array(
							'param'    => self::$filter_param,
							'multiple' => false,
							'extra'    => $extra,
						)
					)
				),
				implode(
					'',
					$ratings_html
				)
			)
		);
	}

	public static function get_query_params() {
		return array( self::$filter_param );
	}

	public static function get_rating_options() {
		return array(
			'1' => __( '1 star', 'blocksy-companion' ),
			'2' => __( '2 stars', 'blocksy-companion' ),
			'3' => __( '3 stars', 'blocksy-companion' ),
			'4' => __( '4 stars', 'blocksy-companion' ),
			'5' => __( '5 stars', 'blocksy-companion' ),
		);
	}

	public static function get_group_label() {
		return __( 'Rating', 'blocksy-companion' );
	}

	public function get_applied_filters() {
		if ( ! $this->get_reset_url() ) {
			return array();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$values = explode( ',', blocksy_companion_akg( self::$filter_param, $_GET, '' ) );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$exact_match = blocksy_companion_akg( self::$filter_param . '_exact_match', $_GET, '' );
		$items       = array();

		foreach ( $values as $value ) {
			$labels = self::get_rating_options();

			if ( ! isset( $labels[ $value ] ) ) {
				continue;
			}

			$item_name = $labels[ $value ];

			if ( ! $exact_match && intval( $value ) !== 5 ) {
				$item_name = blocksy_companion_safe_sprintf(
					// translators: %s is replaced with the minimum star label for the applied filter summary.
					__( '%s and up', 'blocksy-companion' ),
					$labels[ $value ]
				);
			}

			$items[] = array(
				'name'    => $item_name,
				'value'   => $value,
				'control' => array(
					'data-filter-reset' => implode(
						',',
						FiltersUtils::get_present_params(
							array(
								'candidates' => array(
									self::$filter_param,
									self::$filter_param . '_exact_match',
								),
							)
						)
					),
				),
			);
		}

		return array(
			'name'  => self::get_group_label(),
			'items' => $items,
		);
	}

	public function get_product_ids_for_picked_ratings( $picked_rating, $exact_match = false ) {
		$final_product_ids = array();
		$built_in_ratings  = array_keys( self::get_rating_options() );

		if (
			! empty( $picked_rating )
			&&
			in_array( $picked_rating, $built_in_ratings )
		) {
			$cache_key = md5( 'rating_' . $picked_rating );

			$aproximate_rating_min = $picked_rating - 0.5;

			$meta_query = array(
				array(
					'key'     => '_wc_average_rating',
					'value'   => $aproximate_rating_min,
					'compare' => '>=',
				),
			);

			if ( $exact_match ) {
				$aproximate_rating_max = $picked_rating + 0.5;

				$meta_query[] = array(
					'key'     => '_wc_average_rating',
					'value'   => $aproximate_rating_max,
					'compare' => '<=',
				);
			}

			if ( ! isset( $this->product_ids_cache[ $cache_key ] ) ) {
				$products = new \WP_Query(
					array(
						'post_type'              => 'product',
						'fields'                 => 'ids',
						'posts_per_page'         => -1,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
						'cache_results'          => false,
						'no_found_rows'          => true,
						'nopaging'               => true, // prevent "offset" issues
						'blocksy-woocommerce-extra-filters' => false,
						'meta_query'             => array_merge( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
							array(
								'relation' => 'AND',
								array(
									'key'     => '_wc_average_rating',
									'value'   => '',
									'compare' => '!=',
								),
								array(
									'key'     => '_wc_average_rating',
									'compare' => 'EXISTS',
								),
							),
							$meta_query
						),
					)
				);

				$this->product_ids_cache[ $cache_key ] = $products->posts;
			}

			$final_product_ids = array_merge(
				$final_product_ids,
				$this->product_ids_cache[ $cache_key ]
			);
		}

		return $final_product_ids;
	}

	public function wp_query_arg( $query_string, $query_args, $reason ) {
		if ( empty( blocksy_companion_akg( self::$filter_param, $query_string, '' ) ) ) {
			return $query_args;
		}

		$query_args['post__in'] = FiltersUtils::constrain_post_in(
			$query_args,
			$this->get_product_ids_for_picked_ratings(
				blocksy_companion_akg( self::$filter_param, $query_string, '' ),
				blocksy_companion_akg( self::$filter_param . '_exact_match', $query_string, '' )
			)
		);

		return $query_args;
	}

	private function get_product_ids_for_current_query( $param = '' ) {
		$apply_filters = new ApplyFilters();

		$params        = FiltersUtils::get_query_params();
		$filter_params = $this->get_query_params();

		$params = $params['params'];

		foreach ( $filter_params as $param ) {
			unset( $params[ $param ] );
		}

		$products_query = $apply_filters->get_custom_query_for( $params );

		return $products_query->posts;
	}
}
