<?php
/**
 * @package Polylang Pro
 */

namespace WP_Syntex\Polylang_Pro;

use WP_Syntex\Polylang\Options\Options;
use WP_Syntex\Polylang_Pro\Integrations\ACF\Main as ACF_Main;
use WP_Syntex\Polylang_Pro\Updater\License;

/**
 * Class to manage upgrade process.
 *
 * @since 3.7
 */
class Upgrade {
	/**
	 * Plugin options.
	 *
	 * @var Options
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @since 3.7
	 *
	 * @param Options $options The options.
	 */
	public function __construct( Options $options ) {
		$this->options = $options;
	}

	/**
	 * Runs upgrade process.
	 *
	 * @since 3.7
	 *
	 * @return void
	 */
	public function upgrade() {
		foreach ( array( '3.7', '3.9' ) as $version ) {
			if ( version_compare( $this->options->get( 'version' ), $version, '<' ) ) {
				$method_to_call = array( $this, 'upgrade_' . str_replace( '.', '_', $version ) );
				if ( is_callable( $method_to_call ) ) {
					call_user_func( $method_to_call );
				}
			}
		}
	}

	/**
	 * Migrates translated field groups to the new ACF integration.
	 * Transforms language taxonomy to group location.
	 *
	 * @since 3.7
	 *
	 * @return void
	 */
	private function upgrade_3_7() {
		if ( ! ACF_Main::can_use() ) {
			return;
		}

		if ( ! in_array( 'acf-field-group', $this->options->get( 'post_types' ), true ) ) {
			return;
		}

		$this->options->set(
			'post_types',
			array_diff(
				$this->options->get( 'post_types' ),
				array( 'acf-field-group' )
			)
		);
		$this->options->save();

		foreach ( acf_get_field_groups() as $group ) {
			if ( empty( $group['ID'] ) ) {
				continue;
			}

			$group_language = wp_get_object_terms( $group['ID'], 'language' );

			if ( empty( $group_language ) || is_wp_error( $group_language ) || 1 !== count( $group_language ) ) {
				continue;
			}

			$group_language = $group_language[0];
			$new_location = array(
				'param'    => 'language',
				'operator' => '==',
				'value'    => $group_language->slug,
			);

			foreach ( $group['location'] as &$location ) {
				$location[] = $new_location;
			}

			acf_update_field_group( $group );
		}
	}

	/**
	 * Migrates legacy Business Pack license aliases to a single key.
	 *
	 * @since 3.9
	 *
	 * @return void
	 */
	private function upgrade_3_9() {
		$licenses = get_option( 'polylang_licenses' );
		if ( ! is_array( $licenses ) ) {
			// No licenses to migrate.
			return;
		}

		$pro_license = $licenses['polylang-pro'] ?? null;
		$wc_license  = $licenses['polylang-for-woocommerce'] ?? null;
		if ( ! is_array( $pro_license ) || ! is_array( $wc_license ) ) {
			// A product has no license to migrate.
			return;
		}

		$pro_key = $pro_license['key'] ?? '';
		$wc_key  = $wc_license['key'] ?? '';
		if (
			! is_string( $pro_key )
			|| ! is_string( $wc_key )
			|| '' === $pro_key
			|| '' === $wc_key
			|| $pro_key === $wc_key
		) {
			// A product has no key, or both products already share the same key.
			return;
		}

		$data = License::check_license_for_product( $pro_key, 'Polylang for WooCommerce' );
		if ( true !== ( $data->success ?? false ) ) {
			// The Polylang Pro key does not cover Polylang for WooCommerce.
			return;
		}

		$data = License::check_license_for_product( $wc_key, 'Polylang Pro' );
		if ( true !== ( $data->success ?? false ) ) {
			// The Polylang for WooCommerce key does not cover Polylang Pro.
			return;
		}

		// Both keys cover both products: they are aliases of a single legacy Business Pack.
		// License data is left untouched because both aliases describe the same license status.
		$wc_license['key']                    = $pro_key;
		$licenses['polylang-for-woocommerce'] = $wc_license;

		update_option( 'polylang_licenses', $licenses );
	}
}
