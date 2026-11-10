<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- view scope, variables come from blocksy_companion_render_view()

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$aria_hidden = blocksy_companion_theme_functions()->blocksy_get_theme_mod(
	'filter_panel_behaviour',
	'no'
) === 'no' ? 'true' : 'false';

if ( ! woocommerce_products_will_display() ) {
	$aria_hidden = 'true';
}

$content = '';

$filter_source = blocksy_companion_theme_functions()->blocksy_get_theme_mod(
	'filter_source',
	'sidebar-woocommerce-offcanvas-filters'
);

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['filter_source'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$maybe_filter_source = sanitize_text_field( wp_unslash( $_GET['filter_source'] ) );

	if (
		in_array(
			$maybe_filter_source,
			array(
				'sidebar-woocommerce',
				'sidebar-woocommerce-offcanvas-filters',
			),
			true
		)
	) {
		$filter_source = $maybe_filter_source;
	}
}

if (
	! $has_filter_ajax_reveal
	||
	(
		$has_filter_ajax_reveal
		&&
		blocksy_companion_is_xhr()
	)
) {
	ob_start();
	dynamic_sidebar( $filter_source );
	$content = ob_get_clean();

	ob_start();
	/**
	 * Fires at the top of the inline filters content.
	 *
	 * @since 2.0.1
	 */
	do_action( 'blocksy:pro:woo-extra:inline-filters:top' );
	$content = ob_get_clean() . $content;

	ob_start();
	/**
	 * Fires at the bottom of the inline filters content.
	 *
	 * @since 2.0.1
	 */
	do_action( 'blocksy:pro:woo-extra:inline-filters:bottom' );
	$content = $content . ob_get_clean();

	$without_container = blocksy_companion_html_tag(
		'div',
		array(
			'class' => 'ct-filter-content',
		),
		$content
	);

	$content = $without_container;
}

$visibility_classes = blocksy_visibility_classes(
	blocksy_companion_theme_functions()->blocksy_get_theme_mod(
		'filter_panel_visibility',
		array(
			'desktop' => true,
			'tablet'  => true,
			'mobile'  => true,
		)
	)
);

$attributes = array(
	'id'             => 'woo-filters-panel',
	'data-behaviour' => 'drop-down',
	'data-height'    => blocksy_companion_theme_functions()->blocksy_get_theme_mod(
		'filter_panel_height_type',
		'auto'
	),
	'aria-hidden'    => $aria_hidden,
);

if ( ! empty( $visibility_classes ) ) {
	$attributes['class'] = $visibility_classes;
}

blocksy_companion_html_tag_e( 'div', $attributes, $content );
