<?php
/**
 * @package Polylang Updater
 */

namespace WP_Syntex\Polylang_Pro\Updater;

use PLL_License;

defined( 'ABSPATH' ) || exit;

/**
 * Trait that handles the licenses UI shared by `Settings` and `Wizard_Licenses_Step`.
 *
 * @since 1.0
 */
trait License_UI_Trait {
	/**
	 * Stores an array of objects allowing to manage a license.
	 * In case of one plugin is using this new system, and another is using the old one, we must allow both classes.
	 *
	 * @var (License|PLL_License)[]|null
	 */
	private $items;

	/**
	 * Returns a list of license objects.
	 *
	 * @since 1.0
	 *
	 * @return (License|PLL_License)[] Array of instances of `WP_Syntex\Polylang_Pro\Updater\License` or `PLL_License`,
	 *                                 keyed by string IDs.
	 */
	private function get_licenses(): array {
		if ( is_array( $this->items ) ) {
			return $this->items;
		}

		/**
		 * Filters the list of Polylang licenses.
		 *
		 * @since 1.0
		 *
		 * @param (License|PLL_License)[] $licenses Array of instances of `WP_Syntex\Polylang_Pro\Updater\License`
		 *                                          or `PLL_License`, keyed by string IDs.
		 */
		$this->items = (array) apply_filters( 'pll_settings_licenses', array() );
		return $this->items;
	}

	/**
	 * Launches hooks.
	 *
	 * @since 1.0
	 * @since 2.0 Added the activate ajax handler.
	 *
	 * @return void
	 */
	private function hooks(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_pllu_activate_license', array( $this, 'ajax_activate' ) );
		add_action( 'wp_ajax_pllu_deactivate_license', array( $this, 'ajax_deactivate' ) );
	}

	/**
	 * Enqueues styles and scripts.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		/*
		 * Use `__DIR__` in `plugins_url()` to get the path to a "file" located at the root of this package. Keep that
		 * in mind if this file is moved.
		 */
		$style_url = plugins_url( "/css/build/license{$suffix}.css", __DIR__ );
		wp_enqueue_style( 'pll_license', $style_url, array(), Updater::get_version() );

		$script_url = plugins_url( "/js/build/license{$suffix}.js", __DIR__ );
		wp_enqueue_script( 'pll_license', $script_url, array( 'wp-api-fetch' ), Updater::get_version(), true );
	}

	/**
	 * Ajax method to activate one or several licenses.
	 * Hooked to `wp_ajax_pllu_activate_license`.
	 *
	 * @since 2.0
	 *
	 * @return void
	 *
	 * @phpstan-return never
	 */
	public function ajax_activate(): void {
		check_ajax_referer( Settings::NONCE_ACTION, '_pll_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 );
		}

		$key       = isset( $_POST['key'] ) ? sanitize_key( $_POST['key'] ) : '';
		$button_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		if ( '' === $key || ! str_starts_with( $button_id, 'pllu-activate-' ) ) {
			// Missing key or not an activation request.
			wp_die( 0 );
		}

		// The suffix is the product ID, empty for the single field.
		$id                  = substr( $button_id, 14 );
		$registered_licenses = $this->get_licenses();
		$licenses            = new Licenses( $registered_licenses );

		if ( '' === $id ) {
			if ( ! $licenses->activate( $key ) ) {
				// Rebuild the single field with the rejected key and the error message.
				$row = $this->render_license_field(
					null,
					array(
						'license_key' => $key,
						'row_class'   => 'notice-error notice-alt',
						'message'     => __( 'This license key is not valid.', 'polylang-pro' ),
					)
				);

				wp_send_json_error( array( 'row' => array( $row ) ) );
			}

			// The display mode may have changed: rebuild the whole table.
			wp_send_json_success( array( 'row' => $this->get_rows() ) );
		}

		if ( ! isset( $registered_licenses[ $id ] ) ) {
			// Unknown product.
			wp_die( 0 );
		}

		if ( ! $licenses->activate_for_product( $id, $key ) ) {
			// The product rejected the key: refresh its row alone with the message returned by the API.
			wp_send_json_error(
				array(
					'id'   => $id,
					'html' => $this->render_license_field( $registered_licenses[ $id ] ),
				)
			);
		}

		// The display mode may have changed: rebuild the whole table.
		wp_send_json_success( array( 'row' => $this->get_rows() ) );
	}

	/**
	 * Ajax method to deactivate a license.
	 * Hooked to `wp_ajax_pllu_deactivate_license`.
	 *
	 * @since 1.0
	 * @since 2.0 Handles the single license field.
	 *
	 * @return void
	 *
	 * @phpstan-return never
	 */
	public function ajax_deactivate(): void {
		check_ajax_referer( Settings::NONCE_ACTION, '_pll_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 );
		}

		$button_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		if ( ! str_starts_with( $button_id, 'pllu-deactivate-' ) ) {
			// Not a deactivation request.
			wp_die( 0 );
		}

		// The suffix is the product ID, empty for the single field where every product holds the same key.
		$id                  = substr( $button_id, 16 );
		$registered_licenses = $this->get_licenses();

		if ( empty( $registered_licenses ) ) {
			// No registered product can be deactivated.
			wp_die( 0 );
		}

		if ( '' !== $id && ! isset( $registered_licenses[ $id ] ) ) {
			// Reject unknown product IDs.
			wp_die( 0 );
		}

		$licenses = new Licenses( $registered_licenses );
		$licenses->deactivate( $id );

		// The display mode may have changed: rebuild the whole table.
		wp_send_json_success( array( 'row' => $this->get_rows() ) );
	}

	/**
	 * Returns the rows to display according to the single/split mode.
	 *
	 * Single mode returns one field for all products; split mode returns one field per product. Used to rebuild the
	 * current state after AJAX actions in both the settings page and the wizard.
	 *
	 * @since 2.0
	 *
	 * @return string[] A single field, or one field per product.
	 */
	protected function get_rows(): array {
		$registered_licenses = $this->get_licenses();
		$licenses            = new Licenses( $registered_licenses );

		if ( $licenses->uses_single_field() ) {
			$atts         = $licenses->get_single_atts();
			$atts['name'] = $licenses->get_single_label();
			return array( $this->render_license_field( null, $atts ) );
		}

		$rows = array();
		foreach ( $registered_licenses as $license ) {
			$rows[] = $this->render_license_field( $license );
		}

		return $rows;
	}

	/**
	 * Renders a license field as an HTML table row.
	 *
	 * Product fields are built from the license object so new and legacy licenses share the same markup. Passing null
	 * renders the single field, which has no product ID.
	 *
	 * @since 2.0
	 *
	 * @param License|PLL_License|null $license   Product license, or null for the single field.
	 * @param array<string, mixed>     $overrides Attributes overriding the generated field state.
	 * @return string
	 */
	protected function render_license_field( $license = null, array $overrides = array() ): string {
		$atts = License::get_default_row_atts();

		if ( null === $license ) {
			$atts['name'] = License::get_default_label();
		} else {
			$atts['id']          = $license->id;
			$atts['name']        = $license->name;
			$atts['license_key'] = $license->license_key;
			$data                = $license->license_data ?? null;

			if ( null !== $data ) {
				$atts = array_merge( $atts, License::build_status_atts( $data ) );
			}
		}

		$atts = array_merge( $atts, $overrides );

		ob_start();
		include __DIR__ . '/views/view-field-row.php';
		return (string) ob_get_clean();
	}
}
