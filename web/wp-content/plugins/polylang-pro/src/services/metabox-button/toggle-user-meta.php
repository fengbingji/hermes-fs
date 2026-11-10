<?php
/**
 * @package Polylang-Pro
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to manage user meta of a metabox button.
 *
 * @since 3.6
 */
class PLL_Toggle_User_Meta {
	/**
	 * Meta name.
	 *
	 * @var string
	 */
	private $meta_name;

	/**
	 * Constructor.
	 *
	 * @since 3.6
	 *
	 * @param string $meta_name Meta name the object will manage.
	 */
	public function __construct( string $meta_name ) {
		$this->meta_name = $meta_name;
	}

	/**
	 * Returns the user meta name storing the enabled/disabled statuses of the action per post type.
	 *
	 * @since 3.6
	 *
	 * @return string The user meta name.
	 */
	public function get_meta_name(): string {
		return $this->meta_name;
	}

	/**
	 * Tells whether the button is active or not.
	 *
	 * @since 2.1
	 * @since 3.9 Accepts an optional `$post_type` argument. When omitted, the current post type is used.
	 *
	 * @param string $post_type Optional. Post type to check the active state for. Defaults to the global post type.
	 * @return bool
	 */
	public function is_active( string $post_type = '' ) {
		if ( empty( $post_type ) ) {
			$post_type = get_post_type();
		}

		$user_meta = $this->get();

		return ! empty( $user_meta[ $post_type ] );
	}

	/**
	 * Returns the user meta value.
	 *
	 * @since 3.6
	 *
	 * @return bool[]
	 */
	public function get() {
		$user_meta = get_user_meta( (int) get_current_user_id(), $this->get_meta_name(), true );
		return is_array( $user_meta ) ? $user_meta : array();
	}

	/**
	 * Updates the user meta.
	 *
	 * @since 3.6
	 *
	 * @param bool[]       $user_meta An array with post type as key and boolean as value.
	 * @param WP_User|null $user      An instance of `WP_User`.
	 * @return bool
	 */
	public function update( $user_meta, $user = null ) {
		if ( ! $user instanceof WP_User ) {
			$user = wp_get_current_user();
		}

		return (bool) update_user_meta( (int) $user->ID, $this->get_meta_name(), $user_meta );
	}

	/**
	 * Saves the button state.
	 *
	 * @since 2.1
	 *
	 * @param string $post_type Current post type.
	 * @param bool   $active    New requested button state.
	 * @return bool Whether the new button state is accepted or not.
	 */
	public function toggle_option( $post_type, $active ) {
		$user_meta               = $this->get();
		$user_meta[ $post_type ] = (bool) $active;

		return $this->update( $user_meta );
	}
}
