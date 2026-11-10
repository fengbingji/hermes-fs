<?php

namespace Blocksy\Extensions\WoocommerceExtra;

use Automattic\WooCommerce\Internal\ProductAttributesLookup\Filterer;
use Automattic\WooCommerce\Internal\ProductAttributesLookup\DataRegenerator;

class StatusFilter extends BaseFilter {
	private $product_ids_cache = array();

	private static $filter_param = 'filter_status';

	private static $relation_param = 'filter_status_relation';

	public function get_filter_id() {
		return 'status_filter';
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
		$relation = blocksy_companion_akg( 'relation', $attributes, 'or' );

		$counts = $this->get_status_counts( $relation );
		$labels = self::get_status_options();

		$extra = '';

		if ( 'and' === $relation ) {
			$extra = self::$relation_param . '=and';
		}

		if (
			! $attributes['statuses']
			||
			empty( $attributes['statuses'] )
		) {
			return '';
		}

		$statuses_html = array();

		foreach ( $attributes['statuses'] as $status ) {
			if (
				! isset( $counts[ $status['id'] ] )
				||
				intval( $counts[ $status['id'] ] ) === 0
				||
				! $status['enabled']
			) {
				continue;
			}

			$is_active = FilterPresenter::is_filter_active(
				self::$filter_param,
				$status['id']
			);

			$statuses_html[] = blocksy_companion_html_tag(
				'li',
				array( 'class' => 'ct-filter-item' ),
				blocksy_companion_html_tag(
					'div',
					array(
						'class' => 'ct-filter-item-inner',
					),
					blocksy_companion_html_tag(
						'a',
						FilterPresenter::get_control_attributes(
							array(
								'label'     => $labels[ $status['id'] ],
								'value'     => $status['id'],
								'is_active' => $is_active,
							)
						),
						( $attributes['showCheckboxes'] ? blocksy_companion_html_tag(
							'input',
							array_merge(
								array(
									'type'         => 'checkbox',
									'class'        => 'ct-checkbox',
									'tabindex'     => '-1',
									'autocomplete' => 'off',
									'name'         => 'filter_stock_status_' . $status['id'],
								),
								$is_active ? array( 'checked' => 'checked' ) : array()
							)
						) : '' ) .
						blocksy_companion_html_tag(
							'span',
							array(
								'class' => 'ct-filter-label',
							),
							$labels[ $status['id'] ]
						) .
						(
							$attributes['showCounters'] ? blocksy_companion_html_tag(
								'span',
								array(
									'class' => 'ct-filter-count',
								),
								$counts[ $status['id'] ]
							) : ''
						)
					)
				)
			);
		}

		if ( empty( $statuses_html ) ) {
			return '';
		}

		return blocksy_companion_html_tag(
			'div',
			array(
				'class' => 'ct-status-filter',
			),
			blocksy_companion_html_tag(
				'ul',
				array_merge(
					array(
						'class'             => 'ct-filter-widget',
						'data-display-type' => $attributes['layout'],
					),
					FilterPresenter::get_widget_attributes(
						array(
							'param'    => self::$filter_param,
							'multiple' => true,
							'extra'    => $extra,
						)
					)
				),
				implode(
					'',
					$statuses_html
				)
			)
		);
	}

	public static function get_query_params() {
		return array( self::$filter_param, self::$relation_param );
	}

	private function get_picked_relation( $query_string ) {
		return blocksy_companion_akg( self::$relation_param, $query_string, 'or' );
	}

	public static function get_status_options() {
		$stock_statuses = wc_get_product_stock_status_options();

		$stock_labels = array(
			'instock'     => _x( 'In stock', 'product status filter', 'blocksy-companion' ),
			'outofstock'  => _x( 'Out of stock', 'product status filter', 'blocksy-companion' ),
			'onbackorder' => _x( 'On backorder', 'product status filter', 'blocksy-companion' ),
		);

		foreach ( $stock_labels as $key => $label ) {
			if ( ! isset( $stock_statuses[ $key ] ) ) {
				continue;
			}

			$stock_statuses[ $key ] = $label;
		}

		$statuses = array_merge(
			$stock_statuses,
			array(
				'on_sale'  => _x( 'On sale', 'product status filter', 'blocksy-companion' ),
				'featured' => _x( 'Featured', 'product status filter', 'blocksy-companion' ),
			)
		);

		/**
		 * Filters the labels of the statuses available in the status filter.
		 *
		 * @since 2.1.54
		 *
		 * @param array $statuses Status ids mapped to their labels.
		 */
		return apply_filters(
			'blocksy:ext:woocommerce-extra:filters:status:labels',
			$statuses
		);
	}

	public static function get_group_label() {
		return __( 'Status', 'blocksy-companion' );
	}

	public function get_applied_filters() {
		if ( ! $this->get_reset_url() ) {
			return array();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$values = explode( ',', blocksy_companion_akg( self::$filter_param, $_GET, '' ) );

		$items = array();

		$labels = self::get_status_options();

		foreach ( $values as $value ) {
			if ( ! isset( $labels[ $value ] ) ) {
				continue;
			}

			if ( ! isset( $labels[ $value ] ) ) {
				continue;
			}

			$items[] = array(
				'name'    => $labels[ $value ],
				'value'   => $value,
				'control' => array(
					'data-filter-reset' => implode(
						',',
						FiltersUtils::get_present_params(
							array( 'candidates' => self::get_query_params() )
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

	public function get_product_ids_for_picked_statuses(
		$picked_statuses,
		$relation = 'or'
	) {
		$groups = $this->get_groups_for_picked_statuses( $picked_statuses );

		if ( empty( $groups ) ) {
			return array();
		}

		$final_product_ids = null;

		foreach ( $groups as $group_id => $group_statuses ) {
			$group_product_ids = $this->get_product_ids_for_status_group(
				$group_id,
				$group_statuses
			);

			if ( is_null( $final_product_ids ) ) {
				$final_product_ids = $group_product_ids;

				continue;
			}

			if ( 'and' !== $relation ) {
				$final_product_ids = array_merge(
					$final_product_ids,
					$group_product_ids
				);

				continue;
			}

			$final_product_ids = array_intersect(
				$final_product_ids,
				$group_product_ids
			);

			if ( empty( $final_product_ids ) ) {
				return array();
			}
		}

		return array_values( array_unique( $final_product_ids ) );
	}

	public function wp_query_arg( $query_string, $query_args, $reason ) {
		$picked_statuses = $this->get_picked_statuses( $query_string );

		if ( empty( $picked_statuses ) ) {
			return $query_args;
		}

		$query_args['post__in'] = FiltersUtils::constrain_post_in(
			$query_args,
			$this->get_product_ids_for_picked_statuses(
				$picked_statuses,
				$this->get_picked_relation( $query_string )
			)
		);

		return $query_args;
	}

	public function get_status_counts( $relation = 'or' ) {
		$product_ids = $this->get_product_ids_for_current_query( self::$filter_param );

		$all_statuses = self::get_status_options();

		$params = FiltersUtils::get_query_params();

		$picked_groups = array();

		if ( 'and' === $relation ) {
			$picked_groups = $this->get_groups_for_picked_statuses(
				$this->get_picked_statuses( $params['params'] )
			);
		}

		foreach ( $all_statuses as $key => $value ) {
			$scoped_ids = array_intersect(
				$product_ids,
				$this->get_product_ids_for_picked_statuses( array( $key ) )
			);

			foreach ( $picked_groups as $group_id => $group_statuses ) {
				if ( empty( $scoped_ids ) ) {
					break;
				}

				if ( self::get_group_id_for_status( $key ) === $group_id ) {
					continue;
				}

				$scoped_ids = array_intersect(
					$scoped_ids,
					$this->get_product_ids_for_status_group(
						$group_id,
						$group_statuses
					)
				);
			}

			$all_statuses[ $key ] = count( $scoped_ids );
		}

		return $all_statuses;
	}

	private static function get_group_id_for_status( $status ) {
		if ( array_key_exists( $status, wc_get_product_stock_status_options() ) ) {
			return 'stock';
		}

		if ( array_key_exists( $status, self::get_status_options() ) ) {
			return $status;
		}

		return false;
	}

	private function get_picked_statuses( $query_string ) {
		$value = blocksy_companion_akg( self::$filter_param, $query_string, '' );

		if ( empty( $value ) ) {
			return array();
		}

		$picked_statuses = array();

		foreach ( explode( ',', $value ) as $status ) {
			if ( self::get_group_id_for_status( $status ) ) {
				$picked_statuses[] = $status;
			}
		}

		return $picked_statuses;
	}

	private function get_groups_for_picked_statuses( $picked_statuses ) {
		$groups = array();

		foreach ( $picked_statuses as $status ) {
			$group_id = self::get_group_id_for_status( $status );

			if ( ! $group_id ) {
				continue;
			}

			if ( ! isset( $groups[ $group_id ] ) ) {
				$groups[ $group_id ] = array();
			}

			$groups[ $group_id ][] = $status;
		}

		return $groups;
	}

	private function get_product_ids_for_status_group( $group_id, $group_statuses ) {
		if ( 'stock' === $group_id ) {
			sort( $group_statuses );

			$cache_key = md5( implode( ',', $group_statuses ) );

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
						'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
							array(
								'key'     => '_stock_status',
								'value'   => $group_statuses,
								'compare' => 'IN',
							),
						),
					)
				);

				$this->product_ids_cache[ $cache_key ] = $products->posts;
			}

			return $this->product_ids_cache[ $cache_key ];
		}

		if ( 'on_sale' === $group_id ) {
			if ( ! isset( $this->product_ids_cache['on_sale'] ) ) {
				$this->product_ids_cache['on_sale'] = blocksy_companion_get_product_ids_on_sale();
			}

			return $this->product_ids_cache['on_sale'];
		}

		if ( 'featured' === $group_id ) {
			if ( ! isset( $this->product_ids_cache['featured'] ) ) {
				$this->product_ids_cache['featured'] = wc_get_featured_product_ids();
			}

			return $this->product_ids_cache['featured'];
		}

		return array();
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
