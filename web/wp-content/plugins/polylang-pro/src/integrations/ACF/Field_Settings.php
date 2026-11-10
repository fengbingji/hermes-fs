<?php
/**
 * @package Polylang-Pro
 */

namespace WP_Syntex\Polylang_Pro\Integrations\ACF;

use WP_Syntex\Polylang_Pro\Integrations\ACF\Location_Language;

/**
 * This class is part of the ACF compatibility.
 * Adds a field setting to decide if the field must be copied, translated or synchronized.
 *
 * @since 3.7
 */
class Field_Settings {

	/**
	 * Setups actions.
	 *
	 * @since 3.7
	 *
	 * @return void
	 */
	public function on_acf_init() {
		// Adds the field setting, except for fields of type layout.
		foreach ( acf_get_field_types() as $type ) { // Since ACF 5.6.0.
			if ( 'layout' !== $type->category ) {
				add_action( "acf/render_field_settings/type={$type->name}", array( $this, 'render_field_settings' ) );
			}
		}
	}

	/**
	 * Tells if the given fields group was created with Polylang Pro < 3.7 and had been translated.
	 *
	 * @since 3.7.3
	 *
	 * @param int $id The fields group ID to check.
	 * @return bool True if the fields group is from Polylang Pro < 3.7 and had been translated, false otherwise.
	 */
	public static function is_legacy_translated_field_group( int $id ) {
		return ! empty(
			get_terms(
				array(
					'taxonomy'   => 'language',
					'fields'     => 'ids',
					'object_ids' => $id,
				)
			)
		);
	}

	/**
	 * Returns the default translations setting.
	 *
	 * @since 3.9
	 *
	 * @param array $field Custom field definition.
	 * @return string
	 */
	public static function get_default( $field ): string {
		switch ( $field['type'] ) {
			case 'text':
			case 'textarea':
			case 'wysiwyg':
				$default_setting = 'translate';
				break;
			default:
				$default_setting = in_array( 'post_meta', PLL()->options['sync'], true ) ? 'sync' : 'copy_once';
		}
		/**
		 * Filters the default translation setting.
		 *
		 * @since 3.9
		 *
		 * @param string $default_setting Default setting value. Accepts `ignore`, `sync`, `copy_once`, `translate` and `translate_once`.
		 * @param array  $field           Custom field definition.
		 */
		return apply_filters( 'pll_acf_default_translation_setting', $default_setting, $field );
	}

	/**
	 * Returns the translations setting. Use the default value if none was saved
	 * in case the field was saved before the activation of Polylang Pro.
	 *
	 * @since 3.9
	 *
	 * @param array $field Custom field definition.
	 * @return string
	 */
	public static function get( $field ): string {
		if ( isset( $field['translations'] ) ) {
			return $field['translations'];
		}

		if ( 'layout' === acf_get_field_type_prop( $field['type'], 'category' ) ) {
			return 'ignore';
		}

		return self::get_default( $field );
	}

	/**
	 * Renders translations setting and its default value according to the field type.
	 *
	 * @since 2.7
	 * @since 3.3.1 Renamed and merged two methods.
	 * @since 3.7   Added `translate_once` option.
	 *
	 * @param array $field Custom field definition.
	 * @return void
	 */
	public function render_field_settings( $field ) {
		$field_group = Location_Language::get_field_group_from_field( $field );
		if ( ! empty( $field_group ) && Location_Language::has_language_location_rule( $field_group ) && ! $this::is_legacy_translated_field_group( (int) $field_group['ID'] ) ) {
			return;
		}

		$choices = array(
			'ignore'    => __( 'Ignore', 'polylang-pro' ),
			'copy_once' => __( 'Copy once', 'polylang-pro' ),
			'sync'      => __( 'Synchronize', 'polylang-pro' ),
		);

		switch ( $field['type'] ) {
			case 'text':
			case 'textarea':
			case 'wysiwyg':
			case 'email':
			case 'oembed':
			case 'url':
				// Add translate and translate_once option from the 3rd position.
				$choices = array_merge(
					array_slice( $choices, 0, 2 ),
					array(
						'translate'      => __( 'Translate', 'polylang-pro' ),
						'translate_once' => __( 'Translate once', 'polylang-pro' ),
					),
					array_slice( $choices, -1 )
				);
				break;
		}

		acf_render_field_setting( // Since ACF 5.7.10.
			$field,
			array(
				'label'         => __( 'Translations', 'polylang-pro' ),
				'instructions'  => '',
				'name'          => 'translations',
				'type'          => 'select',
				'choices'       => $choices,
				'default_value' => self::get_default( $field ),
			),
			false // The setting is depending on the type of field.
		);
	}
}
