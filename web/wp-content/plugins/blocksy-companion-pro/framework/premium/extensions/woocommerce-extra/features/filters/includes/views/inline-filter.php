<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- view scope, variables come from blocksy_companion_render_view()

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $showResetButton ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- block attribute key
	$showResetButton = 'yes'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- block attribute key
}

if ( ! isset( $has_label ) ) {
	$has_label = true;
}

$reset_icon = '<svg aria-hidden="true" width="11" height="11" viewBox="0 0 15 15" fill="currentColor"><path d="M8.5,7.5l4.5,4.5l-1,1L7.5,8.5L3,13l-1-1l4.5-4.5L2,3l1-1l4.5,4.5L12,2l1,1L8.5,7.5z"/></svg>';

$reset_icon_button = $has_label ? $reset_icon : '<svg aria-hidden="true" width="12" height="12" viewBox="0 0 15 15" fill="currentColor"><path d="M8.5,7.5l4.5,4.5l-1,1L7.5,8.5L3,13l-1-1l4.5-4.5L2,3l1-1l4.5,4.5L12,2l1,1L8.5,7.5z"/></svg>';

$reset_item = blocksy_companion_html_tag(
	'a',
	array(
		'class'             => ! $has_label ? 'ct-filter-control ct-button-ghost' : 'ct-filter-control',
		'href'              => '#',
		'role'              => 'button',
		'data-filter-reset' => implode( ',', $reset_params ),
	),
	$reset_icon_button . __( 'Reset Filters', 'blocksy-companion' )
);

$applied_filters_html = array();

foreach ( $applied_filters as $filter ) {
	if ( empty( $filter['items'] ) ) {
		continue;
	}

	$filters_items = array();

	foreach ( $filter['items'] as $item ) {
		$filters_items[] = blocksy_companion_html_tag(
			'a',
			array_merge(
				array(
					'class'      => 'ct-filter-control',
					'href'       => '#',
					'role'       => 'button',
					'aria-label' => blocksy_companion_safe_sprintf(
							// translators: %s is the name of the applied filter.
						__( 'Remove filter: %s', 'blocksy-companion' ),
						wp_strip_all_tags( $item['name'] )
					),
				),
				$item['control']
			),
			$reset_icon . $item['name']
		);
	}

	$applied_filters_html[] = join( '', $filters_items );
}

if (
	! empty( $applied_filters_html )
	&&
	! empty( $reset_params )
	&&
	// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- block attribute key
	'yes' === $showResetButton
) {
	if ( $has_label ) {
		array_unshift( $applied_filters_html, $reset_item );
	} else {
		$applied_filters_html[] = '<div class="ct-filter-reset">' . $reset_item . '</div>';
	}
}

$filters_label = '';

if (
	blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'woo_has_active_filters_label', 'yes' ) === 'yes'
	&&
	$has_label
) {
	$filters_label = blocksy_companion_html_tag(
		'span',
		array(),
		blocksy_companion_sanitize_html_for_display(
			blocksy_companion_theme_functions()->blocksy_get_theme_mod(
				'woo_active_filters_label',
				__( 'Active Filters', 'blocksy-companion' )
			)
		)
	);
}

blocksy_companion_html_tag_e(
	'div',
	array_merge(
		array(
			'class' => 'ct-active-filters',
		),
		! $has_label ? array(
			'data-display-type' => 'inline',
		) : array()
	),
	$filters_label . implode( '', $applied_filters_html )
);
