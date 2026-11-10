<?php
/**
 * @package Polylang Updater
 */

namespace WP_Syntex\Polylang_Pro\Updater;

use PLL_Settings;
use PLL_Settings_Module;

defined( 'ABSPATH' ) || exit;

/**
 * Settings class for licenses.
 *
 * @since 1.0
 */
class Settings extends PLL_Settings_Module {
	use License_UI_Trait;

	/**
	 * Name of the ajax action to get the licenses data.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	const GET_LICENSES_DATA_ACTION = 'pllu_get_licenses_data';

	/**
	 * Name of the nonce action shared by the licenses ajax requests.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'pllu_license';

	/**
	 * Stores the display order priority.
	 *
	 * @var int
	 */
	public $priority = 100;

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 * @since 2.0 Removed the "Save Changes" button and the legacy save handler.
	 *
	 * @param PLL_Settings $polylang Polylang object.
	 */
	public function __construct( PLL_Settings &$polylang ) {
		parent::__construct(
			$polylang,
			array(
				'module'      => 'licenses',
				'title'       => __( 'License keys', 'polylang-pro' ),
				'description' => __( 'Manage licenses for Polylang Pro and add-ons.', 'polylang-pro' ),
			)
		);

		// Licenses are activated/deactivated through their own buttons, so the generic "Save Changes" is useless here.
		unset( $this->buttons['save'] );
		$this->buttons['cancel'] = sprintf( '<button type="button" class="button button-secondary cancel">%s</button>', __( 'Close', 'polylang-pro' ) );

		$this->hooks();

		// No Save button anymore, so remove the parent's generic save handler for this module.
		remove_action( 'wp_ajax_pll_save_options', array( $this, 'save_options' ) );

		add_action( 'wp_ajax_' . self::GET_LICENSES_DATA_ACTION, array( $this, 'display_licenses' ) );
	}

	/**
	 * Tells if the module is active.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return ! empty( $this->get_licenses() );
	}

	/**
	 * Displays the settings form.
	 *
	 * @since 1.0
	 */
	protected function form(): void {
		if ( ! $this->is_active() ) {
			return;
		}

		$atts = array(
			'action' => self::GET_LICENSES_DATA_ACTION,
			'nonce'  => wp_create_nonce( self::NONCE_ACTION ),
		);

		include __DIR__ . '/views/view-settings-licenses.php';
	}

	/**
	 * Ajax method to display licenses with their data (deactivation, expiration date).
	 * Hooked to `wp_ajax_pllu_get_licenses_data`.
	 *
	 * @since 1.0
	 * @since 2.0 Built the rows through the single/split mode.
	 *
	 * @return void
	 *
	 * @phpstan-return never
	 */
	public function display_licenses(): void {
		check_ajax_referer( self::NONCE_ACTION, '_pll_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 );
		}

		// Refresh every license from the store so the mode is decided on up-to-date data.
		foreach ( $this->get_licenses() as $license ) {
			$license->check_license();
		}

		wp_send_json_success( array( 'row' => $this->get_rows() ) );
	}
}
