<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OrderDetailsBlock {
	public function __construct() {
		add_action( 'init', array( $this, 'blocksy_woo_order_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_admin' ) );
	}

	public function enqueue_admin() {
		$data = get_plugin_data( BLOCKSY__FILE__ );

		wp_enqueue_script(
			'blocksy/woo-ordery',
			BLOCKSY_URL .
				'framework/premium/extensions/woocommerce-extra/static/bundle/woocommerce-order.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor' ),
			$data['Version'],
			true
		);
	}

	public function blocksy_woo_order_block() {
		register_block_type(
			BLOCKSY_PATH . '/framework/premium/extensions/woocommerce-extra/static/js/blocks/ct-order/block.json',
			array(
				'render_callback' => function ( $attributes ) {

					$attributes = wp_parse_args(
						$attributes,
						array(
							'showOrderOverview'   => true,
							'showOrderDetails'    => true,
							'showCustomerDetails' => true,
							'className'           => '',
							'style'               => array(),
						)
					);

					if (
						! $attributes['showOrderOverview']
						&&
						! $attributes['showOrderDetails']
						&&
						! $attributes['showCustomerDetails']
					) {
						return '';
					}

					$order = CustomThankYouPage::get_received_order();

					if ( ! $order ) {
						return '';
					}

					return blocksy_companion_render_view(
						__DIR__ . '/order-view.php',
						array(
							'order' => $order,
							'atts'  => $attributes,
						)
					);
				},
			)
		);
	}
}
