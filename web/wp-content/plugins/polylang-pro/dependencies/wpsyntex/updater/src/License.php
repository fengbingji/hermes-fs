<?php
/**
 * @package Polylang Updater
 */

namespace WP_Syntex\Polylang_Pro\Updater;

defined( 'ABSPATH' ) || exit;

/**
 * A class to easily manage licenses for Polylang Pro and addons.
 *
 * @since 1.0
 */
class License {
	/**
	 * URL to Polylang's account page.
	 *
	 * @var string
	 */
	public const ACCOUNT_URL = 'https://polylang.pro/my-account/';

	/**
	 * API URL.
	 *
	 * @var string
	 */
	private const API_URL = 'https://polylang.pro';

	/**
	 * Sanitized plugin name.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * License key.
	 *
	 * @var string
	 */
	public $license_key;

	/**
	 * License data, obtained from the API request.
	 *
	 * @var \stdClass|null
	 */
	public $license_data;

	/**
	 * Main plugin file.
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Current plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Plugin author.
	 *
	 * @var string
	 */
	private $author = 'WP SYNTEX';

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @param string $file      The plugin file.
	 * @param string $item_name The plugin name.
	 * @param string $version   The plugin version.
	 */
	public function __construct( string $file, string $item_name, string $version ) {
		$this->id      = sanitize_title( $item_name );
		$this->file    = $file;
		$this->name    = $item_name;
		$this->version = $version;

		$licenses = (array) get_option( 'polylang_licenses', array() );

		/** @var array{key?: string, data?: mixed} $license */
		$license           = isset( $licenses[ $this->id ] ) && is_array( $licenses[ $this->id ] ) ? $licenses[ $this->id ] : array();
		$this->license_key = ! empty( $license['key'] ) ? $license['key'] : '';

		if ( ! empty( $license['data'] ) ) {
			$this->license_data = (object) $license['data'];
		}

		// Updater.
		$this->auto_updater();

		// Register this license in the licenses list; the tab itself is added by the leader (see Updater::init()).
		add_filter( 'pll_settings_licenses', array( $this, 'settings' ) );

		// Weekly schedule.
		if ( ! wp_next_scheduled( 'polylang_check_licenses' ) ) {
			wp_schedule_event( time(), 'weekly', 'polylang_check_licenses' );
		}

		add_action( 'polylang_check_licenses', array( $this, 'check_license' ) );
	}

	/**
	 * Creates a new instance of the auto-updater.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function auto_updater(): void {
		$args = array(
			'version'   => $this->version,
			'license'   => $this->license_key,
			'author'    => $this->author,
			'item_name' => $this->name,
		);

		// Setup the updater.
		new EDD_SL_Plugin_Updater( self::API_URL, $this->file, $args );
	}

	/**
	 * Registers the licence in the Settings.
	 * Hooked to `pll_settings_licenses`.
	 *
	 * @since 1.0
	 *
	 * @param License[] $items Array of objects allowing to manage a license.
	 * @return License[]
	 */
	public function settings( $items ) {
		$items[ $this->id ] = $this;
		return $items;
	}

	/**
	 * Activates the license key.
	 *
	 * @since 1.0
	 *
	 * @param string $license_key Activation key.
	 * @return self Updated License object.
	 */
	public function activate_license(
		#[\SensitiveParameter]
		string $license_key
	): self {
		$data = $this->request_activation( $license_key );

		if ( null !== $data ) {
			$this->license_key  = $license_key;
			$this->license_data = isset( $data->license ) && 'deactivated' !== $data->license ? $data : null;
			$this->store();
		}

		// Tell WordPress to look for updates.
		delete_site_transient( 'update_plugins' );
		return $this;
	}

	/**
	 * Tries to activate a new key for this product without changing its local license state.
	 *
	 * A valid key is really activated on the store and linked to the site. The caller decides what to keep locally and
	 * when to persist it.
	 *
	 * @since 2.0
	 *
	 * @param string $license_key Activation key.
	 * @return \stdClass|null The API response, or null if the request failed.
	 */
	public function request_activation(
		#[\SensitiveParameter]
		string $license_key
	): ?\stdClass {
		return $this->api_request( 'activate_license', $license_key );
	}

	/**
	 * Deactivates the license key, keeping it stored for a later activation.
	 *
	 * @since 1.0
	 *
	 * @return self Updated License object.
	 */
	public function deactivate_license(): self {
		$data = $this->request_deactivation();

		if ( null !== $data ) {
			$this->license_data = null;
			$this->store();
		}

		return $this;
	}

	/**
	 * Deactivates the license key without changing its local license state.
	 *
	 * A key is linked to the site and not to a product, so the caller decides which local states to clear and when to
	 * persist them.
	 *
	 * @since 2.0
	 *
	 * @return \stdClass|null The API response, or null if the request failed.
	 */
	public function request_deactivation(): ?\stdClass {
		return $this->api_request( 'deactivate_license' );
	}

	/**
	 * Checks if the license key is valid.
	 * Hooked to `polylang_check_licenses`.
	 *
	 * The check applies to this product only, so only its own state is persisted.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function check_license(): void {
		$data = $this->api_request( 'check_license' );

		if ( null !== $data ) {
			$this->license_data = isset( $data->license ) && 'deactivated' !== $data->license ? $data : null;
			$this->store();
		}
	}

	/**
	 * Checks a license key for a product without changing its local license state.
	 *
	 * @since 2.0
	 *
	 * @param string $license_key License key to check.
	 * @param string $item_name   Plugin name.
	 * @return \stdClass|null The API response, or null if no request could be made.
	 */
	public static function check_license_for_product(
		#[\SensitiveParameter]
		string $license_key,
		string $item_name
	): ?\stdClass {
		return self::api_request_for_product( 'check_license', $license_key, $item_name );
	}

	/**
	 * Resolves the license key for this product before sending the API request.
	 *
	 * @since 1.0
	 * @since 2.0 Returns the API response without updating the licenses option.
	 *
	 * @param string      $request     Type of request: check_license | activate_license | deactivate_license.
	 * @param string|null $license_key License key to request, or null to use the current one.
	 * @return \stdClass|null The API response, or null if no request could be made.
	 *
	 * @phpstan-param 'check_license'|'activate_license'|'deactivate_license' $request
	 */
	private function api_request(
		string $request,
		#[\SensitiveParameter]
		?string $license_key = null
	): ?\stdClass {
		$license_key = null === $license_key ? $this->license_key : $license_key;

		return self::api_request_for_product( $request, $license_key, $this->name );
	}

	/**
	 * Sends a license request to the API.
	 *
	 * @since 2.0
	 *
	 * @param string $request     Type of request: check_license | activate_license | deactivate_license.
	 * @param string $license_key License key to request.
	 * @param string $item_name   Plugin name.
	 * @return \stdClass|null The API response, or null if no request could be made.
	 *
	 * @phpstan-param 'check_license'|'activate_license'|'deactivate_license' $request
	 */
	private static function api_request_for_product(
		string $request,
		#[\SensitiveParameter]
		string $license_key,
		string $item_name
	): ?\stdClass {
		if ( '' === $license_key ) {
			return null;
		}

		// Data to send in our API request.
		$api_params = array(
			'edd_action' => $request,
			'license'    => $license_key,
			'item_name'  => rawurlencode( $item_name ),
			'url'        => home_url(),
		);

		// Call the API.
		$response = wp_remote_post(
			self::API_URL,
			array(
				'timeout'   => 3,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		if ( is_wp_error( $response ) ) {
			return null;
		}

		return (object) json_decode( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Saves this license, leaving the other products untouched.
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	private function store(): void {
		$stored = get_option( 'polylang_licenses', array() );
		$stored = is_array( $stored ) ? $stored : array();

		$stored[ $this->id ] = array( 'key' => $this->license_key );

		if ( null !== $this->license_data ) {
			$stored[ $this->id ]['data'] = $this->license_data;
		}

		update_option( 'polylang_licenses', $stored );
	}

	/**
	 * Tells whether a license data object represents a valid, active status.
	 *
	 * @since 2.0
	 *
	 * @param \stdClass|null $license_data The license data to check.
	 * @return bool
	 */
	public static function is_data_valid( ?\stdClass $license_data ): bool {
		if (
			null === $license_data
			|| ! isset( $license_data->license )
			|| ! in_array( $license_data->license, array( 'valid', 'active' ), true )
		) {
			return false;
		}

		$expiration = isset( $license_data->expires ) ? strtotime( $license_data->expires ) : false;
		return false === $expiration || $expiration >= time();
	}

	/**
	 * Returns the default label for the single license field.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public static function get_default_label(): string {
		return __( 'Enter your license key', 'polylang-pro' );
	}

	/**
	 * Returns the default attributes for a license row.
	 *
	 * @since 2.0
	 *
	 * @return array<string, mixed>
	 */
	public static function get_default_row_atts(): array {
		return array(
			'id'          => '',
			'name'        => '',
			'license_key' => '',
			'row_class'   => 'license-null',
			'is_active'   => false,
			'message'     => '',
		);
	}

	/**
	 * Derives a row's display state from a license's stored data: CSS class, active flag and message.
	 *
	 * Static so the single field and `Licenses` can reuse it.
	 *
	 * @since 2.0
	 *
	 * @param \stdClass $license_data The license data returned by the store.
	 * @return array{row_class: string, is_active: bool, message: string} Row attributes reflecting the status.
	 */
	public static function build_status_atts( \stdClass $license_data ): array {
		$atts = array(
			'row_class' => 'license-null',
			'is_active' => false,
			'message'   => '',
		);

		$now        = time();
		$expiration = isset( $license_data->expires ) ? strtotime( $license_data->expires ) : false;

		if ( ! self::is_data_valid( $license_data ) ) {
			$error = (string) ( $license_data->error ?? '' );

			if ( '' === $error ) {
				// A store-valid key may have expired since the last check.
				$store_says_active = isset( $license_data->license ) && in_array( $license_data->license, array( 'valid', 'active' ), true );
				$expired           = $store_says_active && false !== $expiration && $expiration < $now;
				$error             = $expired ? 'expired' : (string) ( $license_data->license ?? '' );
			}

			// A plain `inactive` (e.g. after a deactivation) is a normal state, not an error: stay neutral, no message.
			if ( 'inactive' !== $error ) {
				$atts['row_class'] = 'notice-error notice-alt';
			}

			switch ( $error ) {
				case 'expired':
					$atts['message'] = sprintf(
					/* translators: %1$s is a date, %2$s is link start tag, %3$s is link end tag. */
						__( 'Your license key expired on %1$s. Please %2$srenew your license key%3$s.', 'polylang-pro' ),
						date_i18n( get_option( 'date_format' ), $expiration ),
						sprintf( '<a href="%s" target="_blank">', self::ACCOUNT_URL ),
						'</a>'
					);
					break;

				case 'disabled':
				case 'revoked':
					$atts['message'] = __( 'Your license key has been disabled.', 'polylang-pro' );
					break;

				case 'missing':
					$atts['message'] = sprintf(
					/* translators: %1$s is link start tag, %2$s is link end tag. */
						__( 'Invalid license. Please %1$svisit your account page%2$s and verify it.', 'polylang-pro' ),
						sprintf( '<a href="%s" target="_blank">', self::ACCOUNT_URL ),
						'</a>'
					);
					break;

				case 'invalid':
				case 'site_inactive':
					$atts['message'] = sprintf(
					/* translators: %1$s is link start tag, %2$s is link end tag. */
						__( 'This license key is not active for this URL. Please %1$svisit your account page%2$s to manage your license key URLs.', 'polylang-pro' ),
						sprintf( '<a href="%s" target="_blank">', self::ACCOUNT_URL ),
						'</a>'
					);
					break;

				case 'item_name_mismatch':
					$atts['message'] = __( 'This license key is not valid for this product.', 'polylang-pro' );
					break;

				case 'no_activations_left':
					$atts['message'] = sprintf(
					/* translators: %1$s is link start tag, %2$s is link end tag */
						__( 'Your license key has reached its activation limit. %1$sView possible upgrades%2$s now.', 'polylang-pro' ),
						sprintf( '<a href="%s" target="_blank">', self::ACCOUNT_URL ),
						'</a>'
					);
					break;
			}

			return $atts;
		}

		$atts['row_class'] = 'license-valid';
		$atts['is_active'] = true;

		if ( isset( $license_data->expires ) && 'lifetime' === $license_data->expires ) {
			$atts['message'] = __( 'The license key never expires.', 'polylang-pro' );
		} elseif ( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {
			$atts['row_class'] = 'notice-warning notice-alt';
			$atts['message']   = sprintf(
			/* translators: %1$s is a date, %2$s is link start tag, %3$s is link end tag. */
				__( 'Your license key will expire soon! Precisely, it will expire on %1$s. %2$sRenew your license key today!%3$s', 'polylang-pro' ),
				date_i18n( get_option( 'date_format' ), $expiration ),
				sprintf( '<a href="%s" target="_blank">', self::ACCOUNT_URL ),
				'</a>'
			);
		} elseif ( false !== $expiration ) {
			$atts['message'] = sprintf(
			/* translators: %s is a date */
				__( 'Your license key expires on %s.', 'polylang-pro' ),
				date_i18n( get_option( 'date_format' ), $expiration )
			);
		}

		return $atts;
	}
}
