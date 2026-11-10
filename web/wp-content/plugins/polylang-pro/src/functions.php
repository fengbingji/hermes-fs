<?php
/**
 * @package Polylang-Pro
 *
 * Miscellaneous utility functions.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tells whether a path is a file uploaded via HTTP POST.
 *
 * Wrapper around {@see is_uploaded_file()} so tests can stub upload validation.
 *
 * @since 3.9
 *
 * @param string $filename Path to the uploaded file temp name.
 * @return bool
 */
function pll_is_uploaded_file( string $filename ): bool {
	return is_uploaded_file( $filename );
}
