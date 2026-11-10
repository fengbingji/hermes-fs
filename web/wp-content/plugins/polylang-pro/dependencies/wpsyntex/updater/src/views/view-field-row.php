<?php
/**
 * Displays a license field.
 *
 * Note: the name of the input is intentionally not prefixed to easily manage
 * the Polylang legacy licenses system at the same time.
 *
 * @package Polylang Updater
 *
 * @since 1.0
 * @since 2.0 Added the Activate button; replaced `$button_text` with `$is_active`.
 *
 * @var array $atts {
 *     @type string $id          Optional. ID of the product. Empty for the single license field.
 *     @type string $name        Name of the plugin.
 *     @type string $license_key License key.
 *     @type string $row_class   HTML class for the row.
 *     @type bool   $is_active   Whether the license is currently active (drives Activate vs Deactivate).
 *     @type string $message     Optional. Message displayed under the field.
 * }
 */

use WP_Syntex\Polylang_Pro\Updater\License;

defined( 'ABSPATH' ) || exit;

$tags = array(
	'a' => array(
		'href'   => array(
			'values' => array( License::ACCOUNT_URL ),
		),
		'target' => true,
	),
);

// The product ID is empty for the single field, which leaves every identifier with an empty suffix.
$row_id        = "pllu-license-{$atts['id']}";
$input_id      = "pllu-license-key-{$atts['id']}";
$input_name    = "licenses[{$atts['id']}]";
$activate_id   = "pllu-activate-{$atts['id']}";
$deactivate_id = "pllu-deactivate-{$atts['id']}";

?>
<tr id="<?php echo esc_attr( $row_id ); ?>" class="<?php echo esc_attr( $atts['row_class'] ); ?>">
	<td><label for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $atts['name'] ); ?></label></td>
	<td>
		<input name="<?php echo esc_attr( $input_name ); ?>" id="<?php echo esc_attr( $input_id ); ?>" type="password" value="<?php echo esc_attr( $atts['license_key'] ); ?>" class="regular-text code" />

		<?php if ( $atts['is_active'] ) : ?>
			<button id="<?php echo esc_attr( $deactivate_id ); ?>" type="button" class="button button-secondary pllu-deactivate-license"><?php esc_html_e( 'Deactivate', 'polylang-pro' ); ?></button>
		<?php else : ?>
			<button id="<?php echo esc_attr( $activate_id ); ?>" type="button" class="button button-primary pllu-activate-license"<?php disabled( '', $atts['license_key'] ); ?>><?php esc_html_e( 'Activate', 'polylang-pro' ); ?></button>
		<?php endif; ?>
		<span class="spinner pllu-license-spinner" aria-hidden="true"></span>

		<?php if ( $atts['message'] ) : ?>
			<p><?php echo wp_kses( $atts['message'], $tags ); ?></p>
		<?php endif; ?>
	</td>
</tr>
