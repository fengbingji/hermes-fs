<?php
/**
 * @package Polylang Updater
 */

namespace WP_Syntex\Polylang_Pro\Updater;

use PLL_Wizard;

defined( 'ABSPATH' ) || exit;

/**
 * A class to handle the wizard license step.
 * Must be instantiated on `pll_init` or later (after `PLL_Wizard` is instantiated).
 *
 * @since 1.0
 *
 * @phpstan-type Steps array<
 *     string,
 *     array{
 *         name: string,
 *         view: callable,
 *         handler: callable,
 *         scripts: non-falsy-string[],
 *         styles: non-falsy-string[]
 *     }
 * >
 */
class Wizard_Licenses_Step {
	use License_UI_Trait;

	/**
	 * Instance of the wizard.
	 *
	 * @var PLL_Wizard
	 */
	private $wizard;

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param PLL_Wizard $wizard Instance of the wizard.
	 */
	public function __construct( PLL_Wizard $wizard ) {
		$this->wizard = $wizard;

		// After `PLL_Wizard::add_step_licenses()` (Core, 100) and updater 1.0 (101), so the elected v2 updater owns the step.
		add_filter( 'pll_wizard_steps', array( $this, 'add' ), 105 );
	}

	/**
	 * Adds licenses step to the wizard.
	 *
	 * @since 1.0
	 *
	 * @param array $steps List of steps.
	 * @return array List of steps updated.
	 *
	 * @phpstan-param Steps $steps
	 * @phpstan-return Steps
	 */
	public function add( $steps ) {
		if ( isset( $steps['licenses']['view'] ) && is_array( $steps['licenses']['view'] ) && isset( $steps['licenses']['view'][0] ) ) {
			$first_step = $steps['licenses']['view'][0];
			if ( $first_step instanceof self ) {
				// No need to execute this several times.
				return $steps;
			}
			if ( $first_step instanceof PLL_Wizard ) {
				// Remove the old Core system, so we don't send 2 AJAX requests for the same action (deactivate license).
				remove_action( 'wp_ajax_pll_deactivate_license', array( $first_step, 'deactivate_license' ) );
				wp_dequeue_script( 'pll_settings' );
			} elseif ( is_object( $first_step ) && method_exists( $first_step, 'deactivate_license' ) ) {
				/*
				 * An updater 1.0 wizard may have registered the licenses step first.
				 * Let the elected v2 updater own the shared step, while `Licenses::has_legacy_license()` keeps the legacy behavior.
				 */
				remove_action( 'admin_enqueue_scripts', array( $first_step, 'enqueue_scripts' ) );
				remove_action( 'wp_ajax_pllu_deactivate_license', array( $first_step, 'deactivate_license' ) );
			}
		}

		$this->hooks();

		if ( ! empty( $this->get_licenses() ) ) {
			$steps['licenses'] = array(
				'name'    => esc_html__( 'Licenses', 'polylang-pro' ),
				'view'    => array( $this, 'display' ),
				'handler' => array( $this, 'save' ),
				'scripts' => array( 'pll_license' ),
				'styles'  => array( 'pll_license' ),
			);
		}

		return $steps;
	}

	/**
	 * Displays the licenses step form.
	 *
	 * @since 1.0
	 * @since 2.0 Uses the same single/split licenses table as the settings page.
	 *
	 * @return void
	 */
	public function display(): void {
		require_once __DIR__ . '/views/view-wizard-step-licenses.php';

		$atts = array(
			'action' => Settings::GET_LICENSES_DATA_ACTION,
			'nonce'  => wp_create_nonce( Settings::NONCE_ACTION ),
		);

		require_once __DIR__ . '/views/view-settings-licenses.php';
	}

	/**
	 * Executes the licenses step.
	 *
	 * Licenses are activated through their AJAX buttons, so continuing only moves to the next step.
	 *
	 * @since 1.0
	 * @since 2.0 License activation is handled through AJAX.
	 *
	 * @return void
	 *
	 * @phpstan-return never
	 */
	public function save(): void {
		// User capabilities verified in `PLL_Wizard::setup_wizard_page()`.
		check_admin_referer( 'pll-wizard', '_pll_nonce' );

		wp_safe_redirect( sanitize_url( $this->wizard->get_next_step_link() ) );
		exit;
	}
}
