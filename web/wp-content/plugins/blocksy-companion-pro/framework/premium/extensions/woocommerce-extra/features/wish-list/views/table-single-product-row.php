<?php

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- view scope, variables come from blocksy_companion_render_view()

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$single_product_id = null;

if (
	isset( $single_product['id'] )
	&&
	is_numeric( $single_product['id'] )
) {
	$single_product_id = $single_product['id'];
} elseif ( is_numeric( $single_product ) ) {
	$single_product_id = $single_product;
}

if ( ! $single_product_id ) {
	return;
}

$product = wc_get_product( $single_product_id );

if (
	! $product
	||
	! blocksy_companion_is_post_viewable( array( 'post' => $product->get_id() ) )
) {
	return;
}

$maybe_variations = null;

$product_permalink = $product->is_visible() ? $product->get_permalink() : '';

if (
	$product->is_type( 'variation' )
	||
	(
		$product->is_type( 'variable' )
		&&
		blocksy_companion_theme_functions()->blocksy_manager()
		&&
		blocksy_companion_theme_functions()->blocksy_manager()
			->woocommerce
			->retrieve_product_default_variation( $product )
	)
) {
	$maybe_variations = $single_product;

	if ( isset( $maybe_variations['attributes'] ) ) {
		$product_permalink = esc_url(
			add_query_arg(
				$maybe_variations['attributes'],
				$product_permalink
			)
		);
	}
}

$is_simple_product = blocksy_companion_get_ext( 'woocommerce-extra' )
	->utils
	->is_simple_product( $product );

if ( isset( $is_simple_product['fake_type'] ) ) {
	$product_classname = WC()
		->product_factory
		->get_product_classname(
			$single_product_id,
			'variable'
		);

	try {
		$product = new $product_classname( $single_product_id );
	} catch ( Exception $e ) {
	}
}

global $post;

$previous_post = $post;

$GLOBALS['product'] = $product;
$GLOBALS['post']    = get_post( $product->get_id() );

if ( ! $product || ! $product->exists() ) {
	return;
}

$is_ajax_add_to_cart = (
	! $product->is_type( 'grouped' )
	&&
	! $product->is_type( 'external' )
);

$columns = array(
	array(
		'attr'   => array(
			'class' => 'wishlist-product-thumbnail',
		),

		'result' => blocksy_companion_render_view(
			__DIR__ . '/columns/product-thumbnail.php',
			array(
				'product' => $product,
			)
		),
	),

	array(
		'attr'   => array(
			'class' => 'wishlist-product-name',
		),

		'result' => blocksy_companion_render_view(
			__DIR__ . '/columns/product-name.php',
			array(
				'product'           => $product,
				'single_product'    => $single_product,
				'single_product_id' => $single_product_id,
				'product_permalink' => $product_permalink,
				'has_custom_user'   => $has_custom_user,

				'maybeVariations'   => $maybe_variations,
				'is_simple_product' => $is_simple_product,
			)
		),
	),

	array(
		'attr'   => array(
			'class' => 'wishlist-product-actions',
		),

		'result' => blocksy_companion_render_view(
			__DIR__ . '/columns/product-actions.php',
			array(
				'product'           => $product,
				'maybeVariations'   => $maybe_variations,
				'is_simple_product' => $is_simple_product,
			)
		),
	),
);

$remove_button = blocksy_companion_render_view(
	__DIR__ . '/columns/product-remove-button.php',
	array(
		'single_product_id' => $single_product_id,
		'has_custom_user'   => $has_custom_user,
	)
);

if ( ! empty( $remove_button ) ) {
	$columns[] = array(
		'attr'   => array(
			'class' => 'wishlist-product-remove',
		),

		'result' => $remove_button,
	);
}

$tr_attr = array();

if ( $is_ajax_add_to_cart ) {
	$tr_attr['data-add-to-cart'] = 'ajax';
}

blocksy_companion_html_tag_e(
	'tr',
	$tr_attr,
	implode(
		'',
		array_map(
			function ( $column ) {
				return blocksy_companion_html_tag(
					'td',
					$column['attr'],
					$column['result']
				);
			},
			$columns
		)
	)
);

$GLOBALS['post'] = $previous_post;
