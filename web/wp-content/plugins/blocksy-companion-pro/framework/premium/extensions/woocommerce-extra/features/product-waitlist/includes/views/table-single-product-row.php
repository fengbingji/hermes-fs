<?php

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- view scope, variables come from blocksy_companion_render_view()

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $entry->product_id ) {
	return;
}

$product = wc_get_product( $entry->product_id );

if (
	! $product
	||
	! blocksy_companion_is_post_viewable( array( 'post' => $product->get_id() ) )
) {
	return;
}

$columns = array();


$columns[] = blocksy_companion_html_tag(
	'td',
	array(
		'class' => 'waitlist-product-thumbnail',
	),
	blocksy_companion_render_view(
		__DIR__ . '/product-thumbnail.php',
		array(
			'product' => $product,
		)
	)
);

$columns[] = blocksy_companion_html_tag(
	'td',
	array(
		'class' => 'waitlist-product-name',
	),
	blocksy_companion_render_view(
		__DIR__ . '/product-name.php',
		array(
			'product' => $product,
		)
	) .
	blocksy_companion_render_view(
		__DIR__ . '/product-mobile-actions.php',
		array(
			'product' => $product,
			'entry'   => $entry,
		)
	)
);

$columns[] = blocksy_companion_html_tag(
	'td',
	array(
		'class' => 'waitlist-product-status',
	),
	__( 'Out of Stock', 'blocksy-companion' )
);

$columns[] = blocksy_companion_html_tag(
	'td',
	array(
		'class' => 'waitlist-subscription-status',
	),
	$entry->confirmed ? __( 'Yes', 'blocksy-companion' ) : __( 'No', 'blocksy-companion' )
);

$columns[] = blocksy_companion_html_tag(
	'td',
	array(
		'class' => 'waitlist-product-actions',
	),
	blocksy_companion_html_tag(
		'div',
		array(),
		blocksy_companion_html_tag(
			'button',
			array(
				'class'      => 'button unsubscribe',
				'type'       => 'submit',
				'data-token' => $entry->unsubscribe_token,
			),
			__( 'Unsubscribe', 'blocksy-companion' )
		)
	)
);

blocksy_companion_html_tag_e(
	'tr',
	array(
		'class' => 'ct-woocommerce-waitlist-table-row',
	),
	implode( '', $columns )
);
