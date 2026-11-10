<?php
/**
 * @package Polylang-Pro
 */

/**
 * Class PLL_PO_Format
 *
 * @since 3.1
 *
 * Manages the support for Po format for Polylang Import / Export feature.
 */
class PLL_PO_Format extends PLL_File_Format {
	/**
	 * Po format is always supported.
	 *
	 * @since 3.1
	 *
	 * @return true
	 */
	public function is_supported() {
		return true;
	}

	/**
	 * Returns all extensions accepted for this format.
	 *
	 * @since 3.9
	 *
	 * @return string[]
	 */
	public function get_extensions(): array {
		return array( 'po' );
	}

	/**
	 * Returns the associated import class.
	 *
	 * @since 3.1
	 *
	 * @return PLL_PO_Import
	 */
	public function get_import() {
		return new PLL_PO_Import();
	}

	/**
	 * Returns the associated export class.
	 *
	 * @since 3.6
	 *
	 * @param string $version Optional file format version. Not used for PO.
	 * @return string
	 *
	 * @phpstan-return class-string<PLL_PO_Export>
	 */
	public function get_export_class( $version = '' ): string { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return PLL_PO_Export::class;
	}

	/**
	 * Checks whether the given file looks like a valid PO file.
	 *
	 * @since 3.9
	 *
	 * @param string $file_path Path to the file.
	 * @return bool
	 */
	public static function is_valid_file( string $file_path ): bool {
		$header    = (string) file_get_contents( $file_path, false, null, 0, 1024 );
		$msgid_pos = strpos( $header, 'msgid' );

		if ( false === $msgid_pos ) {
			return false;
		}

		return str_contains( substr( $header, $msgid_pos + 5 ), 'msgstr' );
	}
}
