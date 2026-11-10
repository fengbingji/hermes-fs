<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- include scope, variables come from the language switcher view

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$languages = pll_the_languages(
	array(
		'hide_if_empty'          => false,
		'hide_if_no_translation' => $hide_missing_language ? 1 : 0,
		'raw'                    => 1,
	)
);

if ( empty( $languages ) ) {
	return;
}

$descriptors = array();
$flag_height = blocksy_companion_akg( 'ls_flag_aspect_ratio', $atts, '4x3' ) === '1x1' ? 18 : 12;

foreach ( $languages as $l ) {
	if ( $l['current_lang'] ) {
		$descriptors['current'] = array(
			'url'              => $l['url'],
			'country_flag_url' => $l['flag'],
			'flag_height'      => $flag_height,
			'language_code'    => $l['locale'],
			'native_name'      => $l['name'],
			'short_name'       => strtoupper( $l['slug'] ),
		);

		continue;
	}

	$descriptors[] = array(
		'url'              => $l['url'],
		'country_flag_url' => $l['flag'],
		'flag_height'      => $flag_height,
		'language_code'    => $l['locale'],
		'native_name'      => $l['name'],
		'short_name'       => strtoupper( $l['slug'] ),
	);
}
