<?php

namespace Blocksy\Extensions\WoocommerceExtra;

class FiltersUtils {
	public static function get_query_params() {
		$url = blocksy_companion_theme_functions()->blocksy_current_url();

		return array(
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameters for filter functionality, not form processing
			'params' => $_GET,
			'url'    => $url,
		);
	}

	public static function get_present_params( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'candidates' => array(),
			)
		);

		$current = self::get_query_params();

		return array_values(
			array_unique(
				array_filter(
					$args['candidates'],
					function ( $param ) use ( $current ) {
						return array_key_exists( $param, $current['params'] );
					}
				)
			)
		);
	}

	public static function constrain_post_in( $query_args, $product_ids ) {
		$product_ids = array_values( array_unique( $product_ids ) );

		if ( isset( $query_args['post__in'] ) ) {
			$product_ids = array_values(
				array_intersect( $query_args['post__in'], $product_ids )
			);
		}

		if ( empty( $product_ids ) ) {
			return array( 0 );
		}

		return $product_ids;
	}

	public static function get_link_url( $param, $value, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'is_multiple' => true,
				'to_add'      => array(),
			)
		);

		$current = FiltersUtils::get_query_params();

		return FiltersUtils::get_link_url_for(
			array(
				'url'         => $current['url'],
				'params'      => $current['params'],
				'param'       => $param,
				'value'       => $value,
				'is_multiple' => $args['is_multiple'],
				'to_add'      => $args['to_add'],
			)
		);
	}

	public static function get_link_url_for( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'url'         => '',
				'params'      => array(),
				'param'       => '',
				'value'       => '',
				'is_multiple' => true,
				'to_add'      => array(),
			)
		);

		$param = $args['param'];
		$value = urldecode( $args['value'] );
		$url   = $args['url'];

		$query_string = array_merge(
			array(
				$param => $value,
			),
			$args['to_add']
		);

		if (
			isset( $args['params'][ $param ] )
			&&
			is_string( $args['params'][ $param ] )
		) {
			$all_attrs = explode( ',', $args['params'][ $param ] );

			if ( $args['is_multiple'] ) {
				if ( in_array( $value, $all_attrs, true ) ) {
					$all_attrs = array_diff( $all_attrs, array( $value ) );
				} else {
					array_push( $all_attrs, $value );
				}
			} else {
				$all_attrs = array_diff( array( $value ), $all_attrs );
			}

			sort( $all_attrs, SORT_STRING );

			$query_string = array();

			if ( ! empty( $all_attrs ) ) {
				$query_string = array_merge(
					array(
						$param => implode( ',', $all_attrs ),
					),
					$args['to_add']
				);
			}

			$to_remove = array_merge(
				array( $param ),
				array_keys( $args['to_add'] )
			);

			$final_to_remove = array();

			foreach ( $to_remove as $key ) {
				if ( ! isset( $query_string[ $key ] ) ) {
					$final_to_remove[] = $key;
				}
			}

			if ( ! empty( $final_to_remove ) ) {
				$url = remove_query_arg( $final_to_remove, $url );
			}
		}

		// Pre-encode values for proper UTF-8 handling.
		//
		// add_query_arg() uses build_query() with $urlencode=false, so new values
		// are not encoded - raw UTF-8 bytes end up in the URL string.
		// Later, parse_str() expects percent-encoded input and corrupts raw high bytes
		// (e.g. Greek π = bytes CF 80 → CF gets kept, 80 becomes underscore).
		//
		// Pre-encoding ensures: %CF%80 → parse_str → π → http_build_query → %CF%80
		// https://github.com/Creative-Themes/blocksy/issues/5103
		$encoded_query_string = array();

		foreach ( $query_string as $key => $val ) {
			$encoded_query_string[ $key ] = rawurlencode( $val );
		}

		return FiltersUtils::normalize_url(
			add_query_arg( $encoded_query_string, $url )
		);
	}

	public static function get_reset_url_for( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'url'    => '',
				'remove' => array(),
			)
		);

		return FiltersUtils::normalize_url(
			remove_query_arg( $args['remove'], $args['url'] )
		);
	}

	private static function normalize_url( $url ) {
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ) ?? '', $params );
		ksort( $params );

		$url = strtok( $url, '?' ) . '?' . http_build_query( $params );

		return preg_replace( '/\/page\/[0-9]+/', '', $url );
	}
}
