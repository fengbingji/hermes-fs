<?php
/**
 * @package Polylang-Pro
 */

/**
 * Class PLL_Xliff_Format
 *
 * @since 3.1
 *
 * Manages support of XLIFF file format 2.1
 */
class PLL_Xliff_Format extends PLL_File_Format {
	/**
	 * Supported XLIFF XML namespaces.
	 *
	 * @var string[]
	 */
	const SUPPORTED_NAMESPACES = array(
		'urn:oasis:names:tc:xliff:document:1.2',
		'urn:oasis:names:tc:xliff:document:2.0',
	);

	/**
	 * Whether the xliff format is supported or not by the current environment.
	 *
	 * @since 3.1
	 *
	 * @return true|WP_Error
	 */
	public function is_supported() {
		if ( ! extension_loaded( 'libxml' ) ) {
			return new WP_Error( 'pll_libxml_missing', __( 'Your PHP installation appears to be missing the libxml extension which is required by the importer.', 'polylang-pro' ) );
		}

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
		return array( 'xliff', 'xlf' );
	}

	/**
	 * Returns the associated import class.
	 *
	 * @since 3.1
	 *
	 * @return PLL_Xliff_Import
	 */
	public function get_import() {
		return new PLL_Xliff_Import();
	}

	/**
	 * Returns the associated export class.
	 *
	 * @since 3.6
	 *
	 * @param string $version Optional file format version.
	 * @return string
	 *
	 * @phpstan-return class-string<PLL_Xliff_Export_12>|class-string<PLL_Xliff_Export_20>|class-string<PLL_Xliff_Export_21>
	 */
	public function get_export_class( $version = '' ): string {
		switch ( $version ) {
			case '20':
				$class_name = PLL_Xliff_Export_20::class;
				break;
			case '21':
				$class_name = PLL_Xliff_Export_21::class;
				break;
			default: // 1.2
				$class_name = PLL_Xliff_Export_12::class;
				break;
		}
		return $class_name;
	}

	/**
	 * Checks whether the given file looks like a valid XLIFF file.
	 *
	 * Uses XMLReader when available, otherwise falls back to a header sniff inspired by #2937.
	 *
	 * @since 3.9
	 *
	 * @param string $file_path Path to the file.
	 * @return bool
	 */
	public static function is_valid_file( string $file_path ): bool {
		if ( class_exists( XMLReader::class ) ) {
			return self::is_valid_file_with_xml_reader( $file_path );
		}

		return self::is_valid_file_from_header( $file_path );
	}

	/**
	 * Checks whether the file header looks like a valid XLIFF file.
	 *
	 * @since 3.9
	 *
	 * @param string $file_path Path to the file.
	 * @return bool
	 */
	private static function is_valid_file_from_header( string $file_path ): bool {
		$header = file_get_contents( $file_path, false, null, 0, 1024 );

		if ( empty( $header ) || ! str_contains( $header, '<xliff' ) ) {
			return false;
		}

		/**
		 * About this pattern:
		 * 1. White space characters are allowed at the beginning of the file, unless the first tag is `<?xml?>`.
		 * 2. Matches only the beginning of the file, until the opening of the `<xliff>` tag (included).
		 * 3. The `<?xml?>` tag is optional. The presence of its `version` attribute is checked, but not its value,
		 *    and it's allowed only if it's the first attribute.
		 * 4. A `<!DOCTYPE>` tag is allowed but its attributes are not checked.
		 * 5. A comment (and only one) is allowed before the `<xliff>` tag.
		 * 6. `<xliff>` tag: the version of the namespace is specific (`1\.2|2\.0`).
		 * 7. Several white space characters are allowed between the tags.
		 */
		return 1 === preg_match(
			'/^
				(?:<\?xml\s+version\s*=[^>]+>)?
				\s*
				(?:<!DOCTYPE\s[^>]+>\s*)?
				(?:<!--(?:-[^-]|[^-])*-->\s*)?
				<xliff
					(?:\s[^>]*)?
					(?:\sxmlns\s*=\s*(["\'])urn:oasis:names:tc:xliff:document:(?:1\.2|2\.0)\1)
					(?:\s[^>]*)?
				>/xU',
			$header
		);
	}

	/**
	 * Checks whether the file is a valid XLIFF file using XMLReader.
	 *
	 * @since 3.9
	 *
	 * @param string $file_path Path to the file.
	 * @return bool
	 */
	private static function is_valid_file_with_xml_reader( string $file_path ): bool {
		$use_errors = libxml_use_internal_errors( true );
		$reader     = new XMLReader();

		try {
			if ( ! $reader->open( $file_path, null, LIBXML_NONET ) ) {
				return false;
			}

			while ( $reader->read() ) {
				if ( XMLReader::ELEMENT !== $reader->nodeType ) {
					continue;
				}

				if ( 'xliff' !== $reader->localName ) {
					return false;
				}

				$xmlns = $reader->getAttribute( 'xmlns' );

				return is_string( $xmlns ) && in_array( $xmlns, self::SUPPORTED_NAMESPACES, true );
			}

			return false;
		} finally {
			$reader->close();
			libxml_clear_errors();
			libxml_use_internal_errors( $use_errors );
		}
	}
}
