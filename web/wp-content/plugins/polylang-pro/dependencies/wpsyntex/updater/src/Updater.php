<?php
/**
 * @package Polylang Updater
 */

namespace WP_Syntex\Polylang_Pro\Updater;

use PLL_Base;
use PLL_Updater_Interface;
use PLL_Updater_Registry;

defined( 'ABSPATH' ) || exit;

/**
 * Updater's main object for the given project.
 *
 * @since 1.0
 */
class Updater implements PLL_Updater_Interface {
	/**
	 * @var License
	 */
	public $license;

	/**
	 * @var Translations
	 */
	public $translations;

	/**
	 * @var Wizard_Licenses_Step|null
	 */
	public $wizard_licenses_step;

	/**
	 * Constructor.
	 *
	 * Must be instantiated before `pll_init`.
	 * Should be instantiated in admin context (`\PLL_Admin_Base`).
	 *
	 * @since 1.0
	 *
	 * @param string $file        The plugin file.
	 * @param string $name        The plugin name.
	 * @param string $version     The plugin version.
	 * @param string $text_domain Text domain for translations.
	 */
	public function __construct( string $file, string $name, string $version, string $text_domain ) {
		$this->license      = new License( $file, $name, $version );
		$this->translations = new Translations( $text_domain );

		// Elect a single leader among the add-ons' updaters: it alone owns the shared licenses tab and wizard step.
		PLL_Updater_Registry::register( $this );
	}

	/**
	 * Sets up what must not be duplicated. Called once, on the elected updater.
	 *
	 * @since 2.0
	 *
	 * @param PLL_Base $polylang Polylang object.
	 * @return void
	 */
	public function init( PLL_Base $polylang ): void {
		add_filter( 'pll_settings_modules', array( $this, 'register_settings_module' ) );

		if ( ! empty( $polylang->wizard ) ) {
			$this->wizard_licenses_step = new Wizard_Licenses_Step( $polylang->wizard );
		}

		// TODO: move the weekly cron here (leader-only, checking every license) in a future PR.
	}

	/**
	 * Registers the shared licenses tab. Hooked to `pll_settings_modules` by the leader only.
	 *
	 * @since 1.0
	 * @since 2.0 moved from License::settings_module() to Updater::register_settings_module().)
	 *
	 * @param string[] $modules The list of module classes.
	 * @return string[]
	 */
	public function register_settings_module( $modules ) {
		// Core registers its own PLL_Settings_Licenses; drop it so the shared licenses tab replaces it.
		$modules = array_diff( $modules, array( 'PLL_Settings_Licenses' ) );

		// Take the slot even if taken in case an add-on still using updater 1.0 registered earlier.
		$modules['licenses'] = Settings::class;

		return $modules;
	}

	/**
	 * Returns the updater version.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function get_version(): string {
		// Cache the version to avoid reading the file multiple times.
		static $version = null;

		if ( null === $version ) {
			$version = require __DIR__ . '/../version.php';
		}

		return $version;
	}
}
