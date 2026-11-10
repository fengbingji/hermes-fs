<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SwatchesPersistAttributes {
	private $conf    = null;
	private $options = array(
		'tooltip_options' => array(),
		'mixed_options'   => array(),
		'color_options'   => array(),
		'image_options'   => array(),
		'button_options'  => array(),
		'inherit_options' => array(),
	);

	public function __construct() {
		$this->conf = new SwatchesConfig();

		add_action(
			'admin_init',
			function () {
				$this->attributes_value_meta_init();

				$this->options = blocksy_companion_get_variables_from_file(
					dirname( __DIR__, 1 ) . '/woo-tab-options.php',
					$this->options
				);
			}
		);
	}

	private function attributes_value_meta_init() {
		add_action(
			'edited_term',
			array( $this, 'persist_attributes_values_option' ),
			10,
			3
		);

		add_action(
			'create_term',
			array( $this, 'persist_attributes_values_option' ),
			10,
			3
		);

		if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return;
		}

		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! $attribute_taxonomies ) {
			return;
		}

		foreach ( $attribute_taxonomies as $tax ) {
			add_action(
				'pa_' . $tax->attribute_name . '_edit_form_fields',
				array( $this, 'output_attributes_values_options' ),
				10,
				2
			);

			add_action(
				'pa_' . $tax->attribute_name . '_add_form_fields',
				array( $this, 'output_add_attributes_values_options' ),
				10,
				1
			);

			$attribute = wc_get_attribute( $tax->attribute_id );
			$type      = $attribute->type;

			if ( 'color' === $type || 'image' === $type || 'mixed' === $type ) {
				add_filter(
					'manage_edit-pa_' . $tax->attribute_name . '_columns',
					function ( $columns ) {
						$new_columns = array();

						if ( isset( $columns['cb'] ) ) {
							$new_columns['cb'] = $columns['cb'];
							unset( $columns['cb'] );
						}

						$new_columns['blc_swatch_preview'] = '';
						$columns                           = array_merge( $new_columns, $columns );

						return $columns;
					}
				);

				add_filter(
					'manage_pa_' . $tax->attribute_name . '_custom_column',
					function ( $columns, $column, $id ) {
						if ( 'blc_swatch_preview' !== $column ) {
							return $columns;
						}

						$conf = new SwatchesConfig();

						$swatch_term = new SwatchElementRender(
							$conf->get_swatch_element_descriptor( get_term( $id ) )
						);

						$iamge = '';

						$maybe_image = $swatch_term->get_output(
							array(
								'skip_tooltip' => true,
							)
						);

						if ( $maybe_image ) {
							$iamge = $maybe_image;
						}

						return $columns . $iamge;
					},
					10,
					3
				);
			}
		}
	}

	public function persist_attributes_values_option( $term_id, $tt_id, $taxonomy ) {
		if (
			! (
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				isset( $_POST['action'] )
				&&
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				( 'editedtag' === $_POST['action'] || 'add-tag' === $_POST['action'] )
				&&
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				isset( $_POST['taxonomy'] )
				&&
				// phpcs:ignore WordPress.Security.NonceVerification.Missing, Generic.CodeAnalysis.AssignmentInCondition.Found, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
				( $taxonomy = get_taxonomy( sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) ) )
				&&
				current_user_can( $taxonomy->cap->edit_terms )
			)
		) {
			return;
		}

		$values = array();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['blocksy_taxonomy_meta_options'][ blocksy_companion_post_name() ] ) ) {
			$values = json_decode(
				// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wp_unslash( $_POST['blocksy_taxonomy_meta_options'][ blocksy_companion_post_name() ] ),
				true
			);
		}

		$values = blocksy_companion_theme_functions()->blocksy_sanitize_post_meta_options( $values );

		if ( \Blocksy\ThemeFunctions::NON_EXISTING_FUNCTION === $values ) {
			return;
		}

		update_term_meta(
			$term_id,
			'blocksy_taxonomy_meta_options',
			$values
		);

		/**
		 * Fires when the dynamic CSS caches should be invalidated.
		 *
		 * Listeners drop their generated CSS files/transients so the
		 * next request regenerates them.
		 *
		 * @since 1.6.2
		 * @since 1.8.0 Renamed from `blocksy:dynamic-css:regenere_css_files`.
		 */
		do_action( 'blocksy:dynamic-css:refresh-caches' );
	}

	public function output_add_attributes_values_options( $taxonomy ) {
		$selected_type = $this->conf->get_attribute_type( $taxonomy );

		$values  = array( array() );
		$options = array();

		if ( 'mixed' === $selected_type ) {
			$options = $this->options['mixed_options'];
		}

		if ( 'image' === $selected_type ) {
			$options = $this->options['image_options'];
		}

		if ( 'color' === $selected_type ) {
			$options = $this->options['color_options'];
		}

		if ( 'button' === $selected_type ) {
			$options = $this->options['button_options'];
		}

		$options = array_merge( $options, $this->options['tooltip_options'] );

		if ( empty( $options ) ) {
			return;
		}

		blocksy_companion_html_tag_e(
			'div',
			array(),
			blocksy_companion_html_tag(
				'input',
				array(
					'type'         => 'hidden',
					'value'        => htmlspecialchars( wp_json_encode( $values[0] ) ),
					'data-options' => htmlspecialchars(
						wp_json_encode( $options )
					),
					'name'         => 'blocksy_taxonomy_meta_options[' . blocksy_companion_post_name() . ']',
				)
			)
		);
	}

	public function output_attributes_values_options( $term, $taxonomy ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$selected_type = $this->conf->get_attribute_type( $term->taxonomy );

		$values = get_term_meta(
			$term->term_id,
			'blocksy_taxonomy_meta_options'
		);

		if ( empty( $values ) ) {
			$values = array( array() );
		}

		if ( ! $values[0] ) {
			$values[0] = array();
		}

		$options = array();

		if ( 'mixed' === $selected_type ) {
			$options = $this->options['mixed_options'];
		}

		if ( 'image' === $selected_type ) {
			$options = $this->options['image_options'];
		}

		if ( 'color' === $selected_type ) {
			$options = $this->options['color_options'];
		}

		if ( 'button' === $selected_type ) {
			$options = $this->options['button_options'];
		}

		$options = array_merge( $options, $this->options['tooltip_options'] );

		if ( empty( $options ) ) {
			return;
		}

		blocksy_companion_html_tag_e(
			'div',
			array(),
			blocksy_companion_html_tag(
				'input',
				array(
					'type'         => 'hidden',
					'value'        => htmlspecialchars( wp_json_encode( $values[0] ) ),
					'data-options' => htmlspecialchars(
						wp_json_encode( $options )
					),
					'name'         => 'blocksy_taxonomy_meta_options[' . blocksy_companion_post_name() . ']',
				)
			)
		);
	}
}
