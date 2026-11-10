<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ProductSaleCountdown {
	public function __construct() {
		add_action(
			'wp_enqueue_scripts',
			function () {
				if ( ! function_exists( 'get_plugin_data' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				$data = get_plugin_data( BLOCKSY__FILE__ );

				if (
					is_admin()
					||
					! is_singular( 'product' )
					||
					(
						! blocksy_companion_theme_functions()->blocksy_has_product_specific_layer( 'product_countdown' )
						&&
						! is_customize_preview()
					)
				) {
					return;
				}

				$product_data = self::need_to_show();

				if (
					! $product_data
					||
					(
						isset( $product_data['need_to_show'] )
						&&
						! $product_data['need_to_show']
					)
				) {
					return;
				}

				wp_enqueue_style(
					'blocksy-ext-woocommerce-extra-sale-countdown-styles',
					BLOCKSY_URL .
						'framework/premium/extensions/woocommerce-extra/static/bundle/sale-countdown.min.css',
					array( 'blocksy-ext-woocommerce-extra-styles' ),
					$data['Version']
				);
			},
			50
		);

		add_filter(
			'woocommerce_available_variation',
			function ( $result, $product, $variation ) {
				if ( ! blocksy_companion_theme_functions()->blocksy_woocommerce_has_flexy_view() ) {
					return $result;
				}

				if ( $variation->is_on_sale() ) {
					$result['date_on_sale_to'] = $variation->get_date_on_sale_to();
				}

				return $result;
			},
			10,
			3
		);

		add_filter(
			'blocksy_woo_single_options_layers:defaults',
			array(
				$this,
				'register_layer_countdown_defaults',
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
			'blocksy:frontend:dynamic-js-chunks',
			function ( $chunks ) {
				if ( ! class_exists( 'WC_AJAX' ) ) {
					return $chunks;
				}

				$global_data = array(
					array(
						'var'  => 'blc_woo_extra_product_sale_countdown',
						'data' => array(
							'days_label'  => esc_attr__( 'Days', 'blocksy-companion' ),
							'hours_label' => esc_attr__(
								'Hours',
								'blocksy-companion'
							),
							'min_label'   => esc_attr__( 'Min', 'blocksy-companion' ),
							'sec_label'   => esc_attr__( 'Sec', 'blocksy-companion' ),
						),
					),
				);

				$chunks[] = array(
					'id'          => 'blocksy_ext_woo_extra_countdown',
					'selector'    => '.product .ct-product-sale-countdown',
					'trigger'     => array(
						array(
							'trigger'  => 'slight-mousemove',
							'selector' =>
								'.product .ct-product-sale-countdown [data-date]',
						),

						array(
							'selector' => '.ct-product-sale-countdown',
							'trigger'  => 'jquery-event',
							'events'   => array(
								'found_variation',
								'reset_data',
							),
						),
					),
					'url'         => blocksy_companion_theme_functions()->blocksy_cdn_url(
						BLOCKSY_URL .
							'framework/premium/extensions/woocommerce-extra/static/bundle/product-sale-countdown.js'
					),
					'global_data' => $global_data,
					'version'     => blocksy_companion_get_version(),
				);

				return $chunks;
			}
		);
	}

	public static function need_to_show() {
		static $cache_current_product_data = null;

		if ( null !== $cache_current_product_data ) {
			return $cache_current_product_data;
		}

		global $post;
		$product = wc_get_product( $post );

		$date = null;

		if ( $product->is_on_sale() ) {
			$date = $product->get_date_on_sale_to();
		}

		$need_to_show = ! ! $date;

		if ( $product->is_type( 'variable' ) ) {
			$maybe_current_variation = null;

			if ( blocksy_companion_theme_functions()->blocksy_manager() ) {
				$maybe_current_variation = blocksy_companion_theme_functions()->blocksy_manager()
					->woocommerce
					->retrieve_product_default_variation( $product );
			}

			if ( $maybe_current_variation ) {
				if ( $maybe_current_variation->is_on_sale() ) {
					$date = $maybe_current_variation->get_date_on_sale_to();
				}
			}

			$get_variations = true;
			$all_variations = blocksy_companion_get_ext( 'woocommerce-extra' )
				->utils
				->get_available_variations( $product->get_id() );

			if ( Utils::TOO_MUCH_AVAILABLE_VARIATIONS === $all_variations ) {
				$get_variations = false;
			}

			if ( $get_variations ) {
				foreach ( $all_variations as $variation ) {
					if ( ! isset( $variation['date_on_sale_to'] ) ) {
						continue;
					}

					$date_on_sale_to = $variation['date_on_sale_to']->__toString();

					if (
						$date_on_sale_to
						&&
						strtotime( $date_on_sale_to ) >= time()
					) {
						$need_to_show = true;
						break;
					}
				}
			} else {
				$need_to_show = true;
			}
		}

		$cache_current_product_data = array(
			'need_to_show' => $need_to_show,
			'date'         => $date,
		);

		return $cache_current_product_data;
	}

	public function render_layer( $layer ) {
		if ( 'product_countdown' === $layer['id'] ) {

			$product_data = self::need_to_show();

			if (
				! $product_data
				||
				(
					isset( $product_data['need_to_show'] )
					&&
					! $product_data['need_to_show']
				)
			) {
				return;
			}

			$date = $product_data['date'];

			$countdown = '';

			if ( $date ) {
				$now       = time();
				$date_time = strtotime( $date );
				$diff      = $date_time - $now;

				$days    = floor( $diff / ( 60 * 60 * 24 ) );
				$hours   = floor( ( $diff % ( 60 * 60 * 24 ) ) / ( 60 * 60 ) );
				$minutes = floor( ( $diff % ( 60 * 60 ) ) / ( 60 ) );
				$seconds = floor( $diff % 60 );

				$countdown = blocksy_companion_html_tag(
					'div',
					array(
						'data-date' => $date,
					),
					blocksy_companion_html_tag(
						'span',
						array(),
						blocksy_companion_html_tag(
							'b',
							array(),
							str_pad( $days, 2, '0', STR_PAD_LEFT )
						) .
							blocksy_companion_html_tag(
								'small',
								array(),
								__( 'Days', 'blocksy-companion' )
							)
					) .
						blocksy_companion_html_tag(
							'span',
							array(),
							blocksy_companion_html_tag(
								'b',
								array(),
								str_pad( $hours, 2, '0', STR_PAD_LEFT )
							) .
								blocksy_companion_html_tag(
									'small',
									array(),
									__( 'Hours', 'blocksy-companion' )
								)
						) .
						blocksy_companion_html_tag(
							'span',
							array(),
							blocksy_companion_html_tag(
								'b',
								array(),
								str_pad( $minutes, 2, '0', STR_PAD_LEFT )
							) .
								blocksy_companion_html_tag(
									'small',
									array(),
									__( 'Min', 'blocksy-companion' )
								)
						) .
						blocksy_companion_html_tag(
							'span',
							array(),
							blocksy_companion_html_tag(
								'b',
								array(),
								str_pad( $seconds, 2, '0', STR_PAD_LEFT )
							) .
								blocksy_companion_html_tag(
									'small',
									array(),
									__( 'Sec', 'blocksy-companion' )
								)
						)
				);
			}

			$section_title = blocksy_companion_html_tag(
				'span',
				array(
					'class' => 'ct-module-title',
				),
				blocksy_companion_sanitize_html_for_display(
					blocksy_companion_akg(
						'product_countdown_title',
						$layer,
						__( 'Hurry up! This sale ends in', 'blocksy-companion' )
					)
				)
			);

			blocksy_companion_html_tag_e(
				'div',
				array(
					'class' => 'ct-product-sale-countdown',
				),
				$section_title . $countdown
			);
		}
	}

	public function register_layer_countdown_defaults( $opt ) {
		return array_merge(
			$opt,
			array(
				array(
					'id'      => 'product_countdown',
					'enabled' => false,
				),
			)
		);
	}

	public function register_layer_options( $opt ) {
		return array_merge(
			$opt,
			array(
				'product_countdown' => array(
					'label'   => __( 'Countdown Box', 'blocksy-companion' ),
					'options' => array(
						'product_countdown_title' => array(
							'label'  => __( 'Title', 'blocksy-companion' ),
							'type'   => 'text',
							'design' => 'block',
							'value'  => __(
								'Hurry up! This sale ends in',
								'blocksy-companion'
							),
							'sync'   => array(
								'id' => 'woo_single_layout_skip',
							),
						),
						'spacing'                 => array(
							'label'      => __( 'Bottom Spacing', 'blocksy-companion' ),
							'type'       => 'ct-slider',
							'min'        => 0,
							'max'        => 100,
							'value'      => 35,
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
