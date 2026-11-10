<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BlocksyExtensionLocalGoogleFonts {
	private $option_name   = 'blocksy_ext_local_google_fonts_settings';
	private $wp_filesystem = null;

	public function __construct() {
		add_filter(
			'blocksy_typography_font_sources',
			function ( $sources ) {
				unset( $sources['google'] );
				return $sources;
			}
		);

		add_filter(
			'gspb_local_font_array',
			function ( $localfont ) {
				$localfonts = array();

				if ( ! empty( $localfont ) ) {
					$localfonts = json_decode( $localfont, true );
				}

				$settings = $this->get_settings();

				if ( ! isset( $settings['fonts'] ) ) {
					return $localfont;
				}

				$themefonts = array();

				foreach ( $settings['fonts'] as $font ) {
					$themefonts[ $font['name'] ] = array(
						'ttf'   => '',
						'woff2' => '',
						'woff'  => '',
					);
				}

				$mergefonts = array_merge( $localfonts, $themefonts );

				return json_encode( $mergefonts );
			}
		);

		add_filter(
			'tpgb_google_font_load',
			function () {
				return false;
			},
			100
		);

		add_filter(
			'blocksy:typography:google:use-remote',
			function () {
				return false;
			}
		);

		add_filter(
			'stackable_enqueue_font',
			function () {
				return false;
			}
		);

		add_filter(
			'fl_builder_google_fonts_pre_enqueue',
			function () {
				return array();
			}
		);

		add_action(
			'wp_enqueue_scripts',
			function () {
				global $wp_styles;

				if ( isset( $wp_styles->queue ) ) {
					foreach ( $wp_styles->queue as $key => $handle ) {
						if ( false !== strpos( $handle, 'fl-builder-google-fonts-' ) ) {
							unset( $wp_styles->queue[ $key ] );
						}
					}
				}
			},
			101
		);

		add_filter(
			'fl_builder_font_families_google',
			function () {
				return array();
			}
		);

		add_filter( 'fl_theme_system_fonts', array( $this, 'handle_beaver_fonts' ) );
		add_filter(
			'fl_builder_font_families_system',
			array( $this, 'handle_beaver_fonts' )
		);

		add_filter(
			'kadence_blocks_print_google_fonts',
			function () {
				return false;
			}
		);

		add_action(
			'blocksy:global-dynamic-css:enqueue',
			function ( $args ) {
				$settings = $this->get_settings();

				if (
				! isset( $settings['cached_css'] )
				||
				is_wp_error( $settings['cached_css'] )
				) {
					return;
				}

				if ( ! class_exists( 'Blocksy_Css_Injector' ) ) {
					return;
				}

				if ( blocksy_companion_theme_functions()->blocksy_dynamic_styles_should_call(
					array_merge(
						array(
							'chunk' => 'global',
						),
						$args
					)
				) ) {
					$args['css']->put(
						\Blocksy_Css_Injector::get_inline_keyword(),
						$settings['cached_css']
					);
				}
			}
		);

		add_action(
			'init',
			function () {
				if ( function_exists( 'blocksy_add_early_inline_style_in_gutenberg' ) ) {
					blocksy_add_early_inline_style_in_gutenberg(
						function () {
							$settings = $this->get_settings();

							if (
							! isset( $settings['cached_css'] )
							||
							is_wp_error( $settings['cached_css'] )
							) {
								return '';
							}

							if ( ! isset( $settings['cached_css'] ) ) {
								return '';
							}

							return $settings['cached_css'];
						}
					);
				}
			}
		);

		add_filter(
			'elementor/fonts/groups',
			function ( $font_groups ) {
				unset( $font_groups['googlefonts'] );
				unset( $font_groups['earlyaccess'] );

				$font_groups['blocksy-local-google-fonts'] = __(
					'Local Google Fonts',
					'blocksy-companion'
				);

				return $font_groups;
			}
		);

		add_filter(
			'elementor/fonts/additional_fonts',
			function ( $fonts ) {
				$settings = $this->get_settings();

				if ( ! isset( $settings['fonts'] ) ) {
					return $fonts;
				}

				foreach ( $settings['fonts'] as $family ) {
					$fonts[ $family['name'] ] = 'blocksy-local-google-fonts';
				}

				return $fonts;
			}
		);

		add_filter(
			'blocksy_typography_font_sources',
			function ( $sources ) {
				$font_families = array();
				$settings      = $this->get_settings();

				if ( ! isset( $settings['fonts'] ) ) {
					return $sources;
				}

				foreach ( $settings['fonts'] as $single_family ) {
					$google_font = $this->get_single_font( $single_family['name'] );

					if (
					! isset( $google_font['all_variations'] )
					||
					! is_array( $google_font['all_variations'] )
					) {
						continue;
					}

					if ( count( $google_font['all_variations'] ) === 0 ) {
						continue;
					}

					$variations_to_use = $google_font['all_variations'];

					if ( isset( $single_family['variations'] ) ) {
						$variations_to_use = $single_family['variations'];
					}

					$font_families[] = array(
						'family'         => $single_family['name'],
						'display'        => $single_family['name'],
						'source'         => 'local-google-fonts',
						'variations'     => array(),
						'all_variations' => $variations_to_use,
					);
				}

				$sources['local-google-fonts'] = array(
					'type'     => 'local-google-fonts',
					'families' => $font_families,
				);

				if ( is_string( $settings['cached_css'] ?? null ) ) {
					if ( isset( $sources['fonts_css'] ) ) {
						$sources['fonts_css'] .= "\n" . $settings['cached_css'];
					} else {
						$sources['local-google-fonts']['css'] = $settings['cached_css'];
					}
				}

				return $sources;
			}
		);

		add_filter(
			'blocksy:typography:theme-json-font-families',
			array( $this, 'theme_json_fonts' )
		);
	}

	public function theme_json_fonts( $fonts_to_add ) {
		$settings = $this->get_settings();

		if ( ! isset( $settings['fonts'] ) ) {
			return $fonts_to_add;
		}

		foreach ( $settings['fonts'] as $single_family ) {
			$fonts_to_add[] = array(
				'fontFamily' => "'" . $single_family['name'] . "'",
				'name'       => $single_family['name'],
				'slug'       => $single_family['name'],
				'fontFace'   => array(),
			);
		}

		return $fonts_to_add;
	}

	public function handle_beaver_fonts( $system_fonts ) {
		$settings = $this->get_settings();

		if ( ! isset( $settings['fonts'] ) ) {
			return $system_fonts;
		}

		foreach ( $settings['fonts'] as $single_family ) {
			$google_font = $this->get_single_font( $single_family['name'] );

			if ( ! is_array( $google_font['all_variations'] ) ) {
				continue;
			}

			if ( count( $google_font['all_variations'] ) === 0 ) {
				continue;
			}

			$variations_to_use = $google_font['all_variations'];

			if ( isset( $single_family['variations'] ) ) {
				$variations_to_use = $single_family['variations'];
			}

			$system_fonts[ $single_family['name'] ] = array(
				'fallback' => 'Verdana, Arial, sans-serif',
				'weights'  => array_map(
					function ( $variation ) {
						$initial_variation = $variation;

						$variation = str_replace( 'n', '', $variation );
						$variation = str_replace( 'i', '', $variation );
						$variation = intval( $variation ) * 100;

						if ( 'i' === $initial_variation[0] ) {
							$variation .= 'i';
						}

						return $variation;
					},
					$variations_to_use
				),
			);
		}

		return $system_fonts;
	}

	public function get_settings() {
		return get_option(
			$this->option_name,
			array(
				'fonts'      => array(
				/*
				[
					'name' => 'ProximaNova',
					'variations => ['n4', 'i4'],
					'preloads' => [
						'variations' => ['n4', 'i4'],
						'subsets' => ['latin-ext']
					]
				]
				*/
				),
				'cached_css' => '',
				'last_saved' => null,
				'urls'       => array(),
			)
		);
	}

	public function set_settings( $value ) {
		update_option( $this->option_name, $value, false );
	}

	public function get_remote_css_for( $settings ) {
		$url = 'https://fonts.googleapis.com/css2?';

		$families = array();

		foreach ( $settings['fonts'] as $family ) {
			$to_push = 'family=' . urlencode( $family['name'] ) . ':';

			$google_font = $this->get_single_font( $family['name'] );

			if ( ! $google_font ) {
				continue;
			}

			$variations_to_use = $google_font['all_variations'];

			if ( isset( $family['variations'] ) ) {
				$variations_to_use = $family['variations'];
			}

			$ital_vars = array();
			$wght_vars = array();

			foreach ( $variations_to_use as $variation ) {
				if ( 'i' === $variation[0] ) {
					$ital_vars[] = intval( $variation[1] ) * 100;
				} else {
					$wght_vars[] = intval( $variation[1] ) * 100;
				}
			}

			sort( $ital_vars );
			sort( $wght_vars );

			$axis_tag_list = array();

			if ( count( $ital_vars ) > 0 ) {
				$axis_tag_list[] = 'ital';
			}

			if ( count( $wght_vars ) > 0 ) {
				$axis_tag_list[] = 'wght';
			}

			$to_push .= implode( ',', $axis_tag_list );
			$to_push .= '@';

			$all_vars = array();

			foreach ( $ital_vars as $ital_var ) {
				$all_vars[] = '1,' . $ital_var;
			}

			foreach ( $wght_vars as $wght_var ) {
				if ( count( $axis_tag_list ) > 1 ) {
					$all_vars[] = '0,' . $wght_var;
				} else {
					$all_vars[] = $wght_var;
				}
			}

			$to_push .= implode( ';', $all_vars );

			$families[] = $to_push;
		}

		$families = implode( '&', $families );

		if ( empty( $families ) ) {
			return null;
		}

		$url .= $families;
		$url .= '&display=swap';

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return @blocksy_companion_request_remote_url(
			$url,
			array(
				'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:87.0) Gecko/20100101 Firefox/87.0',
			)
		);
	}

	public function start_fonts_persistence( $settings ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$this->maybe_prepare_theme_uploads_path(
			array(
				'should_generate' => true,
			)
		);
	}

	public function persist_fonts_for( $settings ) {
		$css_to_cache = $this->get_remote_css_for( $settings );

		$urls = array_map(
			function ( $url ) {
				return str_replace( ')', '', $url );
			},
			$this->get_urls( $css_to_cache )
		);

		$theme_paths = $this->maybe_prepare_theme_uploads_path();

		foreach ( $urls as $url ) {
			$path = $this->get_font_file_path( $url );

			if ( ! $path ) {
				continue;
			}

			$dirname = $theme_paths['google_path'] . dirname( $path );

			if ( ! $this->wp_filesystem->is_dir( $dirname ) ) {
				wp_mkdir_p( $dirname );
			}

			$this->wp_filesystem->put_contents(
				$theme_paths['google_path'] . $path,
				blocksy_companion_request_remote_url( $url )
			);
		}
	}

	public function conclude_fonts_persistence( $settings ) {
		$css_to_cache = $this->get_remote_css_for( $settings );

		$urls = array_map(
			function ( $url ) {
				return str_replace( ')', '', $url );
			},
			$this->get_urls( $css_to_cache )
		);

		$theme_paths = $this->maybe_prepare_theme_uploads_path();

		foreach ( $urls as $url ) {
			$path = $this->get_font_file_path( $url );

			if ( ! $path ) {
				continue;
			}

			$css_to_cache = str_replace(
				$url,
				blocksy_companion_normalize_site_url(
					$theme_paths['google_url'] . $path,
				),
				$css_to_cache
			);
		}

		$settings['cached_css'] = '';

		if ( $css_to_cache ) {
			$settings['cached_css'] = $css_to_cache;
		}

		$this->set_settings( $settings );

		return $settings;
	}

	public function preload_urls( $settings ) {
		$performance_storage = new \Blocksy\PerformanceTypography();

		$performance_storage->set_settings(
			array(
				'local_google_fonts' => blocksy_companion_akg( 'urls', $settings, array() ),
			)
		);
	}

	public function get_single_font( $family ) {
		if ( ! class_exists( '\Blocksy\FontsManager' ) ) {
			return null;
		}

		$m = new \Blocksy\FontsManager();

		$all_google_fonts = $m->get_googgle_fonts();

		foreach ( $all_google_fonts as $font ) {
			if ( $font['family'] === $family ) {
				return $font;
			}
		}

		return null;
	}

	private function get_font_file_path( $url ) {
		$parsed_url = wp_parse_url( $url );

		if (
			! is_array( $parsed_url )
			||
			! isset( $parsed_url['scheme'], $parsed_url['host'], $parsed_url['path'] )
			||
			'https' !== $parsed_url['scheme']
			||
			'fonts.gstatic.com' !== $parsed_url['host']
		) {
			return null;
		}

		$path = wp_normalize_path( $parsed_url['path'] );

		if (
			false !== strpos( $path, '..' )
			||
			false !== strpos( $path, "\0" )
			||
			! in_array(
				strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ),
				array( 'woff2', 'woff', 'ttf' ),
				true
			)
		) {
			return null;
		}

		return $path;
	}

	private function get_urls( $css ) {
		$regex = '/https?\:\/\/[^\" ]+/i';
		preg_match_all( $regex, $css, $matches );
		return ( $matches[0] );
	}

	private function maybe_prepare_theme_uploads_path( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'should_generate' => false,
			)
		);

		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();

		global $wp_filesystem;

		$this->wp_filesystem = $wp_filesystem;
		$uploads             = wp_upload_dir();

		// Theme folders in `uploads` directory.
		$folders_in_uploads = array(
			'google' => 'blocksy/local-google-fonts',
		);

		$theme_paths = array();

		foreach ( $folders_in_uploads as $folder => $path ) {
			// Server path.
			$theme_paths[ $folder . '_path' ] = $uploads['basedir'] . '/' . $path;

			// URL.
			$theme_paths[ $folder . '_url' ] = $uploads['baseurl'] . '/' . $path;

			global $wph;

			if ( $wph ) {
				$theme_paths[ $folder . '_url' ] = $wph->functions->content_urls_replacement(
					$theme_paths[ $folder . '_url' ],
					$wph->functions->get_replacement_list()
				);
			}
		}

		if ( ! $this->has_direct_access() ) {
			return false;
		}

		if ( ! $this->wp_filesystem ) {
			return false;
		}

		if ( ! $args['should_generate'] ) {
			return $theme_paths;
		}

		foreach ( array_keys( $folders_in_uploads ) as $folder ) {
			$path   = $theme_paths[ $folder . '_path' ];
			$parent = dirname( $path );

			if ( $this->wp_filesystem->is_writable( $parent ) ) {
				if ( 'google' === $folder ) {
					$this->wp_filesystem->rmdir( $path, true );
				}

				if ( ! $this->wp_filesystem->is_dir( $path ) ) {
					$this->wp_filesystem->mkdir( $path );
				}
			}
		}

		return $theme_paths;
	}

	public function has_direct_access( $context = null ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		/** @var WP_Filesystem_Base $wp_filesystem */
		global $wp_filesystem;

		if ( $wp_filesystem ) {
			if ( 'direct' !== $wp_filesystem->method ) {
				if (
					is_wp_error( $wp_filesystem->errors )
					&&
					$wp_filesystem->errors->get_error_code()
				) {
					return true;
				} else {
					return 'direct' === $wp_filesystem->method;
				}
			} else {
				return true;
			}
		}

		if ( get_filesystem_method( array(), $context ) === 'direct' ) {
			ob_start();

			$creds = request_filesystem_credentials(
				admin_url(),
				'',
				false,
				$context,
				null
			);

			ob_end_clean();

			if ( WP_Filesystem( $creds ) ) {
				return true;
			}
		}

		return false;
	}
}
