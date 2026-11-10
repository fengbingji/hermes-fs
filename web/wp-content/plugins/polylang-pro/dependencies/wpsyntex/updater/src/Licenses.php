<?php
/**
 * @package Polylang Updater
 */

namespace WP_Syntex\Polylang_Pro\Updater;

use PLL_License;
use stdClass;

defined( 'ABSPATH' ) || exit;

/**
 * Manages the registered licenses as a collection.
 *
 * It handles activation across several products and decides how the licenses form is displayed: a single field, or one field per product.
 *
 * @since 2.0
 */
class Licenses {

	/**
	 * The registered licenses, keyed by product id.
	 *
	 * @var (License|PLL_License)[]
	 */
	private $licenses;

	/**
	 * Constructor.
	 *
	 * @since 2.0
	 *
	 * @param (License|PLL_License)[] $licenses The registered licenses, keyed by product id.
	 */
	public function __construct( array $licenses ) {
		$this->licenses = $licenses;
	}

	/**
	 * Activates a key entered in the single field, on the products it covers.
	 *
	 * Two products covered by the same key identify a business pack. One product covered and one not identify a
	 * standalone key. Once a product accepts the key, one more answer at most is needed to identify it.
	 *
	 * @since 2.0
	 *
	 * @param string $key The key to activate.
	 * @return bool Whether the key is valid for at least one product.
	 */
	public function activate( string $key ): bool {
		if ( $this->has_legacy_license() ) {
			// Older updaters cannot take part in the request-only orchestration used by the single field.
			return false;
		}

		/** @var License[] $licenses */
		$licenses = $this->licenses;
		$first    = array_shift( $licenses );

		if ( null === $first ) {
			// No product registered.
			return false;
		}

		$first_data = $first->request_activation( $key );

		if ( ! License::is_data_valid( $first_data ) ) {
			// Rejected once, so this isn't a business pack: at most one other product is covered by this key.
			// E.g. a Polylang for WooCommerce key rejected by Polylang Pro.
			foreach ( $licenses as $license ) {
				$data = $license->request_activation( $key );

				if ( ! License::is_data_valid( $data ) ) {
					continue;
				}

				$license->license_key  = $key;
				$license->license_data = $data;
				$this->store();
				return true;
			}

			// No product accepted the key: nothing was stored, the single field displays the error.
			return false;
		}

		$second = array_shift( $licenses );

		if ( null !== $second ) {
			$second_data = $second->request_activation( $key );

			if ( License::is_data_valid( $second_data ) ) {
				// 2nd product covered = business pack. Activating a key links it to the site and not to a product, so the
				// products left are covered too and only need the key. Unlike a product field, the single field cannot
				// replace an active key: it must be deactivated first, so there is no previous active key to release here.
				$first->license_key   = $key;
				$first->license_data  = $first_data;
				$second->license_key  = $key;
				$second->license_data = $second_data;

				// Two successful products identify a business pack, so the remaining products don't need another API request.
				// They only get the new key: clear any data from their previous key until their next license check.
				foreach ( $licenses as $license ) {
					$license->license_key  = $key;
					$license->license_data = null;
				}

				$this->store();
				return true;
			}

			// The second product rejected the key, so it only covers the first product.
		}

		// No business pack was identified, so the key stays on the first product only.
		$first->license_key  = $key;
		$first->license_data = $first_data;
		$this->store();
		return true;
	}

	/**
	 * Activates a key entered in a product field and detects whether it also covers the other products.
	 *
	 * @since 2.0
	 *
	 * @param string $id  Product ID.
	 * @param string $key The key to activate.
	 * @return bool Whether the product accepted the key.
	 */
	public function activate_for_product( string $id, string $key ): bool {
		$first = $this->licenses[ $id ];

		if ( $this->has_legacy_license() ) {
			$first->activate_license( $key );
			return License::is_data_valid( $first->license_data ?? null );
		}

		/** @var License $first */
		$first_data = $first->request_activation( $key );

		if ( null === $first_data ) {
			return false;
		}

		$first->license_key  = $key;
		$first->license_data = isset( $first_data->license ) && 'deactivated' !== $first_data->license ? $first_data : null;

		if ( ! License::is_data_valid( $first->license_data ) ) {
			// Keep the store response so the product field can display its error.
			$this->store();
			return false;
		}

		// One other product is enough to know whether this valid key is a business pack.
		/** @var License[] $licenses */
		$licenses = $this->licenses;
		unset( $licenses[ $id ] );
		$second = array_shift( $licenses );

		if ( null === $second ) {
			$this->store();
			return true;
		}

		$second_data = $second->request_activation( $key );

		if ( ! License::is_data_valid( $second_data ) ) {
			$this->store();
			return true;
		}

		// Two products accepted the candidate key, so it is a business pack.
		$deactivated_keys        = array();
		$previous_key            = $second->license_key;
		$has_previous_active_key = $key !== $previous_key && License::is_data_valid( $second->license_data ?? null );

		if ( $has_previous_active_key && null !== $second->request_deactivation() ) {
			// Deactivate the previous standalone key before replacing it with the business pack.
			$deactivated_keys[] = $previous_key;
		}

		$second->license_key  = $key;
		$second->license_data = $second_data;

		foreach ( $licenses as $license ) {
			$previous_key            = $license->license_key;
			$has_previous_active_key = $key !== $previous_key && License::is_data_valid( $license->license_data ?? null );

			if ( $has_previous_active_key && ! in_array( $previous_key, $deactivated_keys, true ) && null !== $license->request_deactivation() ) {
				// Deactivate another active standalone key before replacing it with the business pack.
				$deactivated_keys[] = $previous_key;
			}

			// The business pack covers the remaining products too, so they only need the new key. Clear any data from
			// their previous key until their next license check.
			$license->license_key  = $key;
			$license->license_data = null;
		}

		$this->store( $deactivated_keys );
		return true;
	}

	/**
	 * Deactivates a license, and every product sharing its key.
	 *
	 * @since 2.0
	 *
	 * @param string $id Product ID of a registered license, empty for the single field.
	 * @return void
	 */
	public function deactivate( string $id ): void {
		// The single field only shows Deactivate for a business pack, whose key is stored on all registered products.
		$license = '' === $id ? reset( $this->licenses ) : ( $this->licenses[ $id ] ?? false );

		if ( false === $license ) {
			// Unknown product, or no license registered at all.
			return;
		}

		if ( $this->has_legacy_license() ) {
			$license->deactivate_license();
			return;
		}

		/** @var License $license */
		$deactivated_key = $license->license_key;

		if ( null === $license->request_deactivation() ) {
			return;
		}

		// One request is enough for every product: a key is linked to the site, not to a product. They keep the key
		// so it can be activated again, but lose the status the store returned for it.
		foreach ( $this->licenses as $registered_license ) {
			if ( $deactivated_key === $registered_license->license_key ) {
				$registered_license->license_data = null;
			}
		}

		$this->store( array( $deactivated_key ) );
	}

	/**
	 * Tells whether all registered licenses can be represented by a single field.
	 *
	 * Separate fields are required when only one product is registered, products use different keys, or when one key
	 * is valid for only some registered products. Empty, all-valid and all-invalid states can stay in one field, and so
	 * does a key valid for two products: it is a business pack, so it also covers the products that were never checked.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	public function uses_single_field(): bool {
		if ( $this->has_legacy_license() ) {
			// Older updaters keep their historical per-product fields until every registered product can be coordinated.
			return false;
		}

		// A single registered product keeps its own product field.
		if ( count( $this->licenses ) < 2 ) {
			return false;
		}

		$keys        = array();
		$valid_count = 0;

		foreach ( $this->licenses as $license ) {
			if ( '' !== $license->license_key ) {
				$keys[ $license->license_key ] = true;
			}

			if ( License::is_data_valid( $license->license_data ?? null ) ) {
				++$valid_count;
			}
		}

		// Different keys always need separate product fields.
		if ( count( $keys ) > 1 ) {
			return false;
		}

		// No valid product can use the single field. Two valid products identify a business pack, which also covers
		// the products left unchecked. A key valid for only one product needs separate product fields.
		return 0 === $valid_count || $valid_count >= 2;
	}

	/**
	 * Returns the label for the single field, based on how many products the current key covers:
	 * - none (or an invalid key): the default "Enter your license key";
	 * - one product: that product's name;
	 * - several products: a business pack.
	 *
	 * Only per-product activation data is available here, so a key valid for two or more registered products is
	 * displayed as a business pack.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_single_label(): string {
		$covered = array();

		foreach ( $this->licenses as $license ) {
			if ( License::is_data_valid( $license->license_data ?? null ) ) {
				$covered[] = $license;
			}
		}

		if ( count( $covered ) >= 2 ) {
			return 'Polylang Business Pack';
		}

		if ( 1 === count( $covered ) ) {
			return $covered[0]->name;
		}

		return License::get_default_label();
	}

	/**
	 * Retrieves the attributes for the single field (the key and its status).
	 *
	 * Because the key can belong to any product (the first registered one might be empty), this method scans the
	 * registered licenses and extracts the data from the first key it finds that was checked against the store.
	 *
	 * @since 2.0
	 *
	 * @return array<string, mixed> Field attributes. Empty when no key is stored anywhere.
	 */
	public function get_single_atts(): array {
		$atts = array();

		foreach ( $this->licenses as $license ) {
			if ( '' === $license->license_key ) {
				continue;
			}

			$data = $license->license_data ?? null;
			if ( ! $data instanceof stdClass ) {
				// Covered by a key the store was never asked about for this product: keep looking for a checked one.
				$atts = array( 'license_key' => $license->license_key );
				continue;
			}

			return array_merge( array( 'license_key' => $license->license_key ), License::build_status_atts( $data ) );
		}

		return $atts; // Empty when no key is stored anywhere.
	}

	/**
	 * Tells whether a registered product still uses an older updater, or Polylang's legacy license system.
	 *
	 * Those licenses activate and persist in one operation, so the collection can't orchestrate them.
	 *
	 * @since 2.0
	 *
	 * @return bool
	 */
	private function has_legacy_license(): bool {
		foreach ( $this->licenses as $license ) {
			if ( ! method_exists( $license, 'request_activation' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Stores the current License objects in one option write.
	 *
	 * A plugin deactivated in WordPress keeps its license entry in the option, so its key is still there when the
	 * plugin is activated again. If that key was deactivated in the meantime, its stored data must be removed or the
	 * plugin would come back with a stale license status.
	 *
	 * @since 2.0
	 *
	 * @param string[] $deactivated_keys Keys deactivated during the current operation.
	 * @return void
	 */
	private function store( array $deactivated_keys = array() ): void {
		$stored = get_option( 'polylang_licenses', array() );

		/** @var array<string, array{key?: string, data?: stdClass}> $stored */
		$stored = is_array( $stored ) ? $stored : array();

		foreach ( $stored as $id => $stored_license ) {
			if ( isset( $this->licenses[ $id ] ) ) {
				// An active plugin has a License object, so its stored entry is rebuilt from that object below.
				continue;
			}

			// A deactivated plugin has no License object, so clear stale data directly from its stored entry.
			if ( ! empty( $stored_license['key'] ) && in_array( $stored_license['key'], $deactivated_keys, true ) ) {
				unset( $stored[ $id ]['data'] );
			}
		}

		foreach ( $this->licenses as $license ) {
			unset( $stored[ $license->id ] );

			if ( '' === $license->license_key ) {
				continue;
			}

			$stored[ $license->id ] = array( 'key' => $license->license_key );

			if ( null !== $license->license_data ) {
				$stored[ $license->id ]['data'] = $license->license_data;
			}
		}

		update_option( 'polylang_licenses', $stored );

		// License changes may affect update availability, so tell WordPress to look for updates.
		delete_site_transient( 'update_plugins' );
	}
}
