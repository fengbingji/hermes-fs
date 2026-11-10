<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- include scope, variables come from the language switcher view

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $TRP_LANGUAGE; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- TranslatePress global


$settings = new TRP_Settings();

$settings_array = $settings->get_settings();

$trp = TRP_Translate_Press::get_trp_instance();

$trp_lang_switcher = new TRP_Language_Switcher( // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- constructor registers hooks
	$settings->get_settings(),
	TRP_Translate_Press::get_trp_instance()
);

$trp_languages = $trp->get_component( 'languages' );

$translating_capability = apply_filters( 'trp_translating_capability', 'manage_options' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- TranslatePress hook

if ( current_user_can( $translating_capability ) ) {
	$languages_to_display = $settings_array['translation-languages'];
} else {
	$languages_to_display = $settings_array['publish-languages'];
}

$url_converter = $trp->get_component( 'url_converter' );

$languages = $trp_languages->get_language_names(
	$languages_to_display
);

if ( empty( $languages ) ) {
	return;
}

$descriptors = array();

$ls_flag_aspect_ratio = blocksy_companion_akg( 'ls_flag_aspect_ratio', $atts, '4x3' );

foreach ( $languages as $code => $lang ) {

	$flags_path     = TRP_PLUGIN_URL . 'assets/images/flags/';
	$flag_file_name = $code . '.png';
	$flag_height    = 12;

	if ( file_exists( TRP_PLUGIN_DIR . 'assets/flags/' . $ls_flag_aspect_ratio . '/' . $code . '.svg' ) ) {
		$flags_path     = TRP_PLUGIN_URL . 'assets/flags/' . $ls_flag_aspect_ratio . '/';
		$flag_file_name = $code . '.svg';
		$flag_height    = '1x1' === $ls_flag_aspect_ratio ? 18 : 14;
	}

	$flags_path     = apply_filters( 'trp_flags_path', $flags_path, $code ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- TranslatePress hook
	$flag_file_name = apply_filters( 'trp_flag_file_name', $flag_file_name, $code ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- TranslatePress hook

	if ( $code === $TRP_LANGUAGE ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- TranslatePress global
		$descriptors['current'] = array(
			'url'              => $url_converter->get_url_for_language( $code, false ),
			'country_flag_url' => esc_url( $flags_path . $flag_file_name ),
			'flag_height'      => $flag_height,
			'language_code'    => str_replace( '_', '-', $code ),
			'native_name'      => $lang,
			'short_name'       => strtoupper(
				$url_converter->get_url_slug( $code, false )
			),
		);

		continue;
	}

	$descriptors[] = array(
		'url'              => $url_converter->get_url_for_language( $code, false ),
		'country_flag_url' => esc_url( $flags_path . $flag_file_name ),
		'flag_height'      => $flag_height,
		'language_code'    => str_replace( '_', '-', $code ),
		'native_name'      => $lang,
		'short_name'       => strtoupper(
			$url_converter->get_url_slug( $code, false )
		),
	);
}
