<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class StockScarcity {
	public function get_dynamic_styles_data( $args ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- filter callback signature
		return array(
			'path' => __DIR__ . '/dynamic-styles.php',
		);
	}

	public function __construct() {
		add_filter(
			'blocksy_woo_single_options_layers:defaults',
			array(
				$this,
				'register_layer_defaults',
			)
		);

		add_filter(
			'blocksy_woo_single_right_options_layers:defaults',
			array(
				$this,
				'register_layer_defaults',
			)
		);

		add_filter(
			'blocksy_woo_single_options_layers:extra',
			array(
				$this,
				'register_layer_options',
			)
		);

		add_action(
			'blocksy:woocommerce:product:custom:layer',
			array(
				$this,
				'render_layer',
			)
		);

		add_filter(
			'blocksy_woo_card_options_layers:defaults',
			array(
				$this,
				'register_layer_defaults',
			)
		);

		add_filter(
			'blocksy_woo_card_options_layers:extra',
			array(
				$this,
				'register_card_layer_options',
			)
		);

		add_action(
			'blocksy:woocommerce:product-card:custom:layer',
			function ( $layer ) {
				$this->render_layer( $layer, array( 'context' => 'archive-card' ) );
			}
		);

		add_filter(
			'blocksy:frontend:dynamic-js-chunks',
			function ( $chunks ) {
				if ( ! class_exists( 'WC_AJAX' ) ) {
					return $chunks;
				}

				$chunks[] = array(
					'id'       => 'blocksy_ext_woo_extra_stock_scarcity',
					'selector' => '.product .ct-product-stock-scarcity',
					'trigger'  => array(
						array(
							'trigger'  => 'jquery-event',
							'events'   => array(
								'found_variation',
								'reset_data',
							),
							'selector' => '.product .ct-product-stock-scarcity',
						),
					),
					'url'      => blocksy_companion_theme_functions()->blocksy_cdn_url(
						BLOCKSY_URL .
							'framework/premium/extensions/woocommerce-extra/static/bundle/product-stock-scarcity.js'
					),
					'version'  => blocksy_companion_get_version(),
				);

				return $chunks;
			}
		);

		add_filter(
			'blocksy_customizer_options:woocommerce:general:end',
			function ( $opts ) {
				$opts['has_stock_scarcity_panel'] = blocksy_companion_get_options(
					__DIR__ . '/options.php',
					array(),
					false
				);

				return $opts;
			},
			57
		);

		add_filter(
			'woocommerce_available_variation',
			function ( $result, $product, $variation ) {
				$stock_quantity_min = blocksy_companion_theme_functions()->blocksy_get_theme_mod(
					'product_stock_scarcity_min',
					50
				);

				$result['blocksy_low_stock_amount'] = $stock_quantity_min;

				if ( ! empty( $variation->get_low_stock_amount() ) ) {
					$result['blocksy_low_stock_amount'] = wc_get_low_stock_amount( $variation );
				}

				return $result;
			},
			10,
			3
		);
	}

	public function render_layer( $layer, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				// 'single' | 'archive-card'
				'context' => 'single',
			)
		);

		if ( 'product_stock_scarcity' === $layer['id'] ) {
			global $product;

			$stock_quantity = 0;
			$need_to_show   = false;

			$stock_quantity_min = blocksy_companion_theme_functions()->blocksy_get_theme_mod(
				'product_stock_scarcity_min',
				50
			);

			if ( ! empty( $product->get_low_stock_amount() ) ) {
				$stock_quantity_min = wc_get_low_stock_amount( $product );
			}

			if ( $product->get_manage_stock() ) {
				$stock_quantity = max( 0, $product->get_stock_quantity() );

				$need_to_show = ! ! $stock_quantity && $stock_quantity < $stock_quantity_min;
			}

			if ( $product->is_type( 'variable' ) ) {
				$maybe_current_variation = blocksy_companion_theme_functions()
					->blocksy_get_product_default_variation( $product );

				if ( $maybe_current_variation ) {
					if ( $maybe_current_variation->get_manage_stock() ) {
						$stock_quantity = max( 0, $maybe_current_variation->get_stock_quantity() );

						if ( ! empty( $maybe_current_variation->get_low_stock_amount() ) ) {
							$stock_quantity_min = $maybe_current_variation->get_low_stock_amount();
						}

						$need_to_show = ! ! $stock_quantity && $stock_quantity < $stock_quantity_min;
					} else {
						$stock_quantity     = 0;
						$stock_quantity_min = 0;
					}
				}

				$get_variations = true;
				$all_variations = blocksy_companion_get_ext( 'woocommerce-extra' )
					->utils
					->get_available_variations(
						$product->get_id(),
						array(
							'context' => $args['context'],
						)
					);

				if ( Utils::TOO_MUCH_AVAILABLE_VARIATIONS === $all_variations ) {
					$get_variations = false;
				}

				if ( $get_variations ) {
					foreach ( $all_variations as $variation ) {
						if (
							! isset( $variation['max_qty'] )
							||
							! $variation['max_qty']
						) {
							continue;
						}

						$need_to_show = ! ! $variation['max_qty'] && $variation['max_qty'] < $stock_quantity_min;

						if ( $need_to_show ) {
							break;
						}
					}
				} else {
					$need_to_show = true;
				}
			}

			if ( ! $need_to_show ) {
				return;
			}

			if ( ! $stock_quantity_min ) {
				$stock_quantity_min = 1;
			}

			$percent = min(
				100,
				( $stock_quantity / $stock_quantity_min ) * 100
			);

			$message = str_replace(
				'{items}',
				'<span class="ct-stock-quantity">' . $stock_quantity . '</span>',
				blocksy_companion_sanitize_html_for_display(
					blocksy_companion_theme_functions()->blocksy_get_theme_mod(
						'product_stock_scarcity_title',
						__(
							'🚨 Hurry up! Only {items} units left in stock!',
							'blocksy-companion'
						)
					)
				)
			);

			$message_html = blocksy_companion_html_tag(
				'div',
				array(
					'class' => 'ct-message',
				),
				$message
			);

			$bar_html = blocksy_companion_html_tag(
				'div',
				array(
					'class' => 'ct-progress-bar',
				),
				blocksy_companion_html_tag(
					'span',
					array(
						'style' => 'width: ' . $percent . '%',
					),
					''
				)
			);

			blocksy_companion_html_tag_e(
				'div',
				array_merge(
					array(
						'class'      => 'ct-product-stock-scarcity',
						'data-items' => $stock_quantity,
					),
					(
						$percent >= 100
						||
						0 === $percent
					) ? array(
						'hidden' => 'hidden',
					) : array()
				),
				$message_html . $bar_html
			);
		}
	}

	public function register_layer_defaults( $opt ) {
		return array_merge(
			$opt,
			array(
				array(
					'id'      => 'product_stock_scarcity',
					'enabled' => false,
				),
			)
		);
	}

	public function register_card_layer_options( $opt ) {
		return array_merge(
			$opt,
			array(
				'product_stock_scarcity' => array(
					'label'   => __( 'Stock Scarcity', 'blocksy-companion' ),
					'options' => array(
						'spacing' => array(
							'label'      => __( 'Bottom Spacing', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'min'        => 0,
							'max'        => 100,
							'value'      => 10,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_single_layout_skip',
							),
						),
					),
				),
			)
		);

		return $opt;
	}

	public function register_layer_options( $opt ) {
		return array_merge(
			$opt,
			array(
				'product_stock_scarcity' => array(
					'label'   => __( 'Stock Scarcity', 'blocksy-companion' ),
					'options' => array(
						'spacing' => array(
							'label'      => __( 'Bottom Spacing', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'min'        => 0,
							'max'        => 100,
							'value'      => 10,
							'responsive' => true,
							'sync'       => array(
								'id' => 'woo_single_layout_skip',
							),
						),
					),
				),
			)
		);

		return $opt;
	}
}
