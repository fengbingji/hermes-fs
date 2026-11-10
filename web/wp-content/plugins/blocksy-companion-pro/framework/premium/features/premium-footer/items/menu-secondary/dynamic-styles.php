<?php
// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- include scope, variables come from blocksy_companion_get_variables_from_file()

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

blocksy_companion_get_variables_from_file(
	get_template_directory() . '/inc/panel-builder/footer/menu/dynamic-styles.php',
	array(),
	array(
		'css'             => $css,
		'tablet_css'      => $tablet_css,
		'mobile_css'      => $mobile_css,
		'atts'            => $atts,
		'root_selector'   => $root_selector,
		'column_selector' => $column_selector,
		'item'            => $item,
	)
);
