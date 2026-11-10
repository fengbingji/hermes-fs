<?php
/**
 * Displays the wizard licenses step intro.
 *
 * The licenses table itself is shared with the settings page and rendered right after this intro.
 *
 * @package Polylang Updater
 *
 * @since 1.0
 * @since 2.0 Reduced to the intro; the licenses table is shared with the settings page.
 */

defined( 'ABSPATH' ) || exit;

?>
<p>
	<?php esc_html_e( 'You are using plugins which require a license key.', 'polylang-pro' ); ?>
	<?php esc_html_e( 'Please enter your license key:', 'polylang-pro' ); ?>
</p>
