<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function blocksy_companion_get_compare_ratio( $layout ) {
	$cropping = 'predefined';

	if ( ! $layout['enabled'] ) {
		return '3/4';
	}

	$cropping = blocksy_companion_akg(
		'compare_image_ratio',
		$layout,
		'3/4'
	);

	if ( 'uncropped' === $cropping ) {
		return 'original';
	}

	if ( '1:1' === $cropping ) {
		return '1/1';
	}

	if ( 'custom' === $cropping || 'predefined' === $cropping ) {
		$width  = get_option( 'woocommerce_thumbnail_cropping_custom_width', 4 );
		$height = get_option( 'woocommerce_thumbnail_cropping_custom_height', 3 );

		return $width . '/' . $height;
	}

	return $cropping;
}

function blocksy_companion_output_add_to_compare( $place, $attributes = array() ) {
	$option_ids = array(
		'archive'    => 'has_archive_compare',
		'quick-view' => 'has_quick_view_compare',
	);

	if ( 'single' !== $place ) {
		if ( $place && isset( $option_ids[ $place ] ) ) {
			if ( blocksy_companion_theme_functions()->blocksy_get_theme_mod( $option_ids[ $place ], 'yes' ) === 'no' ) {
				return '';
			}
		} else {
			return '';
		}
	}

	global $product;
	$id = $product->get_id();

	$class = 'ct-compare-button-archive ct-button';

	if ( 'archive' !== $place ) {
		$class = 'ct-compare-button-single';
	}

	$is_active = false;

	if (
		in_array(
			(int) $id,
			array_map(
				'intval',
				array_column(
					blocksy_companion_get_ext( 'woocommerce-extra' )
						->get_compare()
						->get_current_compare_list(),
					'id'
				)
			),
			true
		)
	) {
		$is_active = true;
	}

	$content = '';

	/**
	 * Filters the compare icon markup rendered inside the compare button.
	 *
	 * @since 2.0.1
	 *
	 * @param string $icon The compare icon markup.
	 */
	$icon = apply_filters(
		'blocksy:ext:woocommerce-extra:compare:icon',
		'<svg class="ct-icon" viewBox="0 0 15 15">
			<path d="M7.5 6c-.1.5-.2 1-.3 1.4 0 .6-.1 1.3-.3 2-.2.7-.5 1.4-1 1.9-.5.6-1.3.9-2.2.9H0v-1.4h3.7c.6 0 .9-.2 1.2-.5.3-.3.5-.7.7-1.3.1-.5.2-1 .3-1.6v-.3c0-.5.1-1 .3-1.5.2-.7.5-1.4 1-1.9.5-.6 1.3-.9 2.2-.9h3l-1.6-1.6 1-1L15 3.5l-3.3 3.3-1-1 1.6-1.6h-3c-.6 0-.9.2-1.2.5-.2.3-.5.7-.6 1.3zM4.9 4.7c.2-.4.4-.9.7-1.3-.5-.4-1.1-.6-1.9-.6H0v1.4h3.7c.6 0 1 .2 1.2.5zm5.8 4.5 1.6 1.6h-3c-.6 0-.9-.2-1.2-.5-.2.4-.4.9-.6 1.3.5.4 1.1.6 1.8.6h3l-1.6 1.6 1 1 3.3-3.3-3.3-3.3-1 1z"/>
		</svg>'
	);

	$shop_cards_type = blocksy_companion_theme_functions()->blocksy_get_theme_mod( 'shop_cards_type', 'type-1' );

	if ( 'archive' === $place && 'type-3' === $shop_cards_type ) {
		$content .=
			'<span class="ct-tooltip ct-hidden-sm">' .
			__( 'Add to compare', 'blocksy-companion' ) .
			'</span>';
	}

	$label_class      = 'ct-label';
	$label_visibility = blocksy_companion_akg(
		'label_visibility',
		$attributes,
		array(
			'desktop' => true,
			'tablet'  => true,
			'mobile'  => true,
		)
	);

	$label_visibility = blocksy_companion_theme_functions()->blocksy_expand_responsive_value( $label_visibility );

	$label_class .=
	' ' .
	blocksy_companion_theme_functions()->blocksy_visibility_classes( $label_visibility );
	$label        = blocksy_companion_akg( 'label', $attributes, __( 'Compare', 'blocksy-companion' ) );
	$label_html   = blocksy_companion_sanitize_html_for_display( $label );

	$tooltip = '';

	$tooltip_visibility_classes = blocksy_companion_theme_functions()->blocksy_visibility_classes(
		array(
			'desktop' => ! $label_visibility['desktop'],
			'tablet'  => ! $label_visibility['tablet'],
			'mobile'  => ! $label_visibility['mobile'],
		)
	);

	$tooltip = blocksy_companion_html_tag(
		'span',
		array(
			'class' => 'ct-tooltip ' . $tooltip_visibility_classes,
		),
		$label_html
	);

	if (
		'single' === $place
		||
		'quick-view' === $place
	) {
		$content .= blocksy_companion_html_tag(
			'span',
			array(
				'class' => $label_class,
			),
			$label_html
		) .
		$tooltip;
	}

	if ( ! function_exists( 'blocksy_action_button' ) ) {
		return '';
	}

	return blocksy_action_button(
		array(
			'button_html_attributes' => array(
				'class'             => $class,
				'aria-label'        => __( 'Add to compare', 'blocksy-companion' ),
				'data-button-state' => $is_active ? 'active' : '',
			),
			'html_tag'               => 'button',
			'icon'                   => $icon,
			'content'                => $content,
		)
	);
}
