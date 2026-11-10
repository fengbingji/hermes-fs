<?php

namespace Blocksy\Extensions\PostTypesExtra\DynamicData;

use Blocksy\Extensions\PostTypesExtra\CustomField;

class ACFProvider extends BaseProvider {
	public function get_provider_id() {
		return 'acf';
	}

	public function get_provider_label() {
		return 'ACF';
	}

	public function get_all_fields( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'allow_images' => false,
			)
		);

		$acf_fields = array();

		$post_type_acf_groups = acf_get_field_groups();

		foreach ( $post_type_acf_groups as $acf_group ) {
			$fields = acf_get_fields( $acf_group['key'] );

			foreach ( $fields as $field ) {
				$acf_fields[] = $field;
			}
		}

		return $this->acf_fields_to_result( $acf_fields, $args );
	}

	public function get_post_fields( $post_id, $post_type, $args = array() ) {
		$acf_fields = array();

		$post_type_acf_groups = acf_get_field_groups(
			array(
				'post_id' => $post_id,
			)
		);

		foreach ( $post_type_acf_groups as $acf_group ) {
			$fields = acf_get_fields( $acf_group['key'] );

			foreach ( $fields as $field ) {
				$acf_fields[] = $field;
			}
		}

		return $this->acf_fields_to_result( $acf_fields, $args );
	}

	public function get_post_type_fields( $post_type, $args = array() ) {
		$acf_fields = array();

		$post_type_acf_groups = acf_get_field_groups(
			array(
				'post_type' => $post_type,
			)
		);

		foreach ( $post_type_acf_groups as $acf_group ) {
			$fields = acf_get_fields( $acf_group['key'] );

			foreach ( $fields as $field ) {
				$acf_fields[] = $field;
			}
		}

		foreach ( acf_get_raw_field_groups() as $acf_group ) {
			if ( ! isset( $acf_group['location'] ) ) {
				continue;
			}

			$has_matching_location = false;

			foreach ( $acf_group['location'] as $single_location ) {
				foreach ( $single_location as $rule ) {
					if (
						'post_type' === $rule['param']
						&&
						'==' === $rule['operator']
						&&
						$rule['value'] === $post_type
					) {
						$has_matching_location = true;
						continue;
					}

					if (
						'post_taxonomy' !== $rule['param']
						&&
						'post_category' !== $rule['param']
					) {
						continue;
					}

					$tax = explode( ':', $rule['value'] )[0];

					$all_tax = get_object_taxonomies( $post_type );

					if ( ! in_array( $tax, $all_tax, true ) ) {
						continue;
					}

					$has_matching_location = true;
				}
			}

			if ( ! $has_matching_location ) {
				continue;
			}

			$fields = acf_get_fields( $acf_group['key'] );

			foreach ( $fields as $field ) {
				$acf_fields[] = $field;
			}
		}

		return $this->acf_fields_to_result( $acf_fields, $args );
	}

	public function get_term_fields( $term_id, $args = array() ) {
		$acf_fields = array();

		$term = get_term( $term_id );

		$post_type_acf_groups = acf_get_field_groups(
			array(
				'term_id'  => $term_id,
				'taxonomy' => $term->taxonomy,
			)
		);

		foreach ( $post_type_acf_groups as $acf_group ) {
			$fields = acf_get_fields( $acf_group['key'] );

			foreach ( $fields as $field ) {
				$acf_fields[] = $field;
			}
		}

		return $this->acf_fields_to_result( $acf_fields, $args );
	}

	public function render( $field_id, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'context_object' => null,

				'allow_images'   => false,
			)
		);

		if ( ! function_exists( 'get_field_object' ) ) {
			return null;
		}

		$field_descriptor = acf_get_field( $field_id );

		// Check if it's a sub-field in a group.
		if ( ! $field_descriptor ) {
			if ( strpos( $field_id, '_' ) === false ) {
				return null;
			}

			$parts = explode( '_', $field_id );

			$group_descriptor = null;
			$sub_field_id     = null;

			// This code takes a compound string like "field_test1_test3_test4_test5"
			// and progressively checks shorter prefixes (excluding the full string)
			// with acf_get_field(). The first prefix that exists becomes $group_id,
			// and the remainder of the string becomes $sub_field_id. This allows you
			// to dynamically find the "parent field" and its corresponding "sub-field"
			// based on available ACF fields.
			for ( $i = 1; $i <= count( $parts ); $i++ ) {
				$prefix = implode( '_', array_slice( $parts, 0, $i ) );

				if ( count( $parts ) === $i ) {
					continue;
				}

				$maybe_group_descriptor = acf_get_field( $prefix );

				if ( $maybe_group_descriptor ) {
					$group_descriptor = $maybe_group_descriptor;
					$sub_field_id     = implode( '_', array_slice( $parts, $i ) );
					break;
				}
			}

			if (
				$group_descriptor
				&&
				isset( $group_descriptor['sub_fields'] )
				&&
				is_array( $group_descriptor['sub_fields'] )
			) {
				foreach ( $group_descriptor['sub_fields'] as $sub_field ) {
					if ( $sub_field['name'] === $sub_field_id ) {
						$field_descriptor = $sub_field;
						break;
					}
				}
			}
		}

		if ( ! $field_descriptor ) {
			return null;
		}

		$field_type = CustomField::$TYPE_TEXT; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- public CustomField API

		if ( 'image' === $field_descriptor['type'] ) {
			$field_type = CustomField::$TYPE_IMAGE; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- public CustomField API
		}

		$field = array(
			'name'  => $field_id,
			'label' => $field_descriptor['label'],
			'type'  => $field_type,
		);

		$field_descriptor['value'] = get_field(
			$field['name'],
			$args['context_object']
		);

		$field_value = $field_descriptor['value'];

		if ( ! is_array( $field_value ) ) {
			$field_value = array( $field_value );
		}

		if ( 'image' === $field_descriptor['type'] ) {
			if ( ! $args['allow_images'] ) {
				$field_value = '';

				if (
					is_string( $field_descriptor['value'] )
					||
					(
						is_array( $field_descriptor['value'] )
						&&
						isset( $field_descriptor['value']['url'] )
						&&
						! empty( $field_descriptor['value']['url'] )
					)
				) {
					$field_value = $field_descriptor['value'];
				}
			} else {
				$field_value = array();

				if ( isset( $field_descriptor['value'] ) ) {
					$field_value = $field_descriptor['value'];
				}
			}
		} else {
			$mapped_value = array();

			foreach ( $field_value as $single_field ) {
				if ( is_object( $single_field ) && get_class( $single_field ) === 'WP_Term' ) {
					$mapped_value[] = blocksy_companion_html_tag(
						'a',
						array(
							'href' => get_term_link( $single_field, $single_field->taxonomy ),
						),
						$single_field->name
					);
				} else {
					$mapped_value[] = $single_field;
				}
			}

			$field_value = $mapped_value;

			if (
				$field_descriptor
				&&
				isset( $field_descriptor['choices'] )
				&&
				! empty( $field_descriptor['choices'] )
			) {
				$mapped_value = array();

				foreach ( array_values( $field_value ) as $single_field ) {
					if (
						isset( $field_descriptor['choices'][ $single_field ] )
					) {
						$mapped_value[] = $field_descriptor['choices'][ $single_field ];
					} else {
						$mapped_value[] = $single_field;
					}
				}

				$field_value = $mapped_value;
			}

			$field_value_result = array();

			foreach ( $field_value as $single_field_value ) {
				if (
					is_string( $single_field_value )
					&&
					! empty( $single_field_value )
				) {
					$field_value_result[] = $single_field_value;
				}
			}

			$field_value = implode( ', ', array_values( $field_value_result ) );
		}

		$field['value'] = $field_value;

		if ( 'oembed' === $field_descriptor['type'] ) {
			$field['trusted_html'] = true;
			$field['url']          = get_field(
				$field['name'],
				$args['context_object'],
				false
			);
		}

		return $field;
	}

	// Implementation details.

	private function acf_fields_to_result( $acf_fields, $args = array() ) {
		$result = array();

		foreach ( $acf_fields as $field ) {
			if ( 'repeater' === $field['type'] ) {
				continue;
			}

			if ( ! $args['allow_images'] && 'image' === $field['type'] ) {
				continue;
			}

			if (
				'group' === $field['type']
				&&
				! empty( $field['sub_fields'] )
			) {
				foreach ( $field['sub_fields'] as $sub_field ) {
					$field_type = CustomField::$TYPE_TEXT; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- public CustomField API

					if ( 'image' === $sub_field['type'] ) {
						$field_type = CustomField::$TYPE_IMAGE; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- public CustomField API
					}

					$custom_field = new CustomField(
						array(
							'id'          => $field['name'] . '_' . $sub_field['name'],
							'label'       => $field['label'] . ' - ' . $sub_field['label'],
							'type'        => $field_type,
							'provider_id' => $this->get_provider_id(),
						)
					);

					$result[] = $custom_field;
				}

				continue;
			}

			$field_type = CustomField::$TYPE_TEXT; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- public CustomField API

			if ( 'image' === $field['type'] ) {
				$field_type = CustomField::$TYPE_IMAGE; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- public CustomField API
			}

			if (
				empty( $field['name'] )
				||
				empty( $field['label'] )
			) {
				continue;
			}

			$custom_field = new CustomField(
				array(
					'id'          => $field['name'],
					'label'       => $field['label'],
					'type'        => $field_type,
					'provider_id' => $this->get_provider_id(),
				)
			);

			$result[] = $custom_field;
		}

		return $result;
	}
}
