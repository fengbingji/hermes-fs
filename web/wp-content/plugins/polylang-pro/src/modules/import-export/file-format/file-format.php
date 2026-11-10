<?php
/**
 * @package Polylang-Pro
 */

/**
 * Interface PLL_Import_Export_File_Format
 *
 * @since 3.1
 *
 * Represents a supported format for the import / export feature.
 */
abstract class PLL_File_Format {
	/**
	 * Whether the file format is supported by the current environment or not.
	 *
	 * @since 3.1
	 *
	 * @return true|WP_Error
	 */
	abstract public function is_supported();

	/**
	 * Returns the default extension used by this format.
	 *
	 * @since 3.9
	 *
	 * @return string
	 */
	public function get_extension(): string {
		$extensions = $this->get_extensions();

		return reset( $extensions );
	}

	/**
	 * Returns all extensions accepted for this format.
	 *
	 * @since 3.9
	 *
	 * @return string[]
	 *
	 * @phpstan-return non-empty-list<string>
	 */
	abstract public function get_extensions(): array;

	/**
	 * Checks whether the given file name has an extension accepted by this format.
	 *
	 * @since 3.9
	 *
	 * @param string $filename File name.
	 * @return bool
	 */
	public function is_valid_extension( string $filename ): bool {
		$extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

		return in_array( $extension, $this->get_extensions(), true );
	}

	/**
	 * Checks whether the given file looks like a valid file for this format.
	 *
	 * @since 3.9
	 *
	 * @param string $file_path Path to the file.
	 * @return bool
	 */
	abstract public static function is_valid_file( string $file_path ): bool;

	/**
	 * Returns the associated import class.
	 *
	 * @since 3.1
	 *
	 * @return PLL_Import_File_Interface
	 */
	abstract public function get_import();

	/**
	 * Returns the associated export class.
	 *
	 * @since 3.6
	 *
	 * @param string $version Optional file format version.
	 * @return string
	 *
	 * @phpstan-return class-string<PLL_Export_Data>
	 */
	abstract public function get_export_class( $version = '' ): string;
}
