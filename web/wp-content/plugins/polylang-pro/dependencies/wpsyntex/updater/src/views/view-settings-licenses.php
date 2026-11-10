<?php
/**
 * Displays the licenses table.
 *
 * @package Polylang Updater
 *
 * @since 1.0
 *
 * @var array $atts {
 *     @type string $action AJAX action.
 *     @type string $nonce  AJAX nonce.
 * }
 */

defined( 'ABSPATH' ) || exit;

?>
<table id="pllu-licenses-table" class="form-table pll-table-top" data-action="<?php echo esc_attr( $atts['action'] ); ?>" data-nonce="<?php echo esc_attr( $atts['nonce'] ); ?>" data-pllu-error="<?php echo esc_attr__( 'Something went wrong. Please reload the page and try again.', 'polylang-pro' ); ?>" aria-busy="true">
	<tr class="pllu-licenses-loading">
		<td>
			<span class="screen-reader-text"><?php esc_html_e( 'Loading licenses…', 'polylang-pro' ); ?></span>
			<span class="pllu-license-skeleton pllu-license-skeleton-label" aria-hidden="true"></span>
		</td>
		<td aria-hidden="true">
			<span class="pllu-license-skeleton pllu-license-skeleton-input"></span>
			<span class="pllu-license-skeleton pllu-license-skeleton-button"></span>
		</td>
	</tr>
</table>
