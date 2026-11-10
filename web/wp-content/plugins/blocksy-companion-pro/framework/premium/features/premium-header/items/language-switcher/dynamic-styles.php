<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- include scope, variables come from blocksy_companion_get_variables_from_file()

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( blocksy_companion_akg( 'ls_flag_aspect_ratio', $atts, '4x3' ) === '1x1' ) {
	$css->put(
		blocksy_assemble_selector( $root_selector ),
		'--items-ratio: 1'
	);
}

blocksy_output_responsive(
	array(
		'css'          => $css,
		'tablet_css'   => $tablet_css,
		'mobile_css'   => $mobile_css,
		'selector'     => blocksy_assemble_selector( $root_selector ),
		'variableName' => 'items-spacing',
		'value'        => blocksy_companion_akg( 'ls_items_spacing', $atts, 20 ),
		'unit'         => 'px',
	)
);

blocksy_output_font_css(
	array(
		'font_value' => blocksy_companion_akg(
			'ls_font',
			$atts,
			blocksy_typography_default_values(
				array(
					'size'           => '12px',
					'variation'      => 'n6',
					'text-transform' => 'uppercase',
				)
			)
		),
		'css'        => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector'   => blocksy_assemble_selector( $root_selector ),
	)
);


// default state
blocksy_output_colors(
	array(
		'value'      => blocksy_companion_akg( 'ls_label_color', $atts ),
		'default'    => array(
			'default' => array( 'color' => 'var(--theme-text-color)' ),
			'hover'   => array( 'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ) ),
		),
		'css'        => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,

		'variables'  => array(
			'default' => array(
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector(
						array(
							'selector'  => $root_selector,
							'operation' => 'suffix',
							'to_add'    => '.ct-language',
						)
					)
				),
				'variable' => 'theme-link-initial-color',
			),

			'hover'   => array(
				'selector' => blocksy_assemble_selector(
					blocksy_mutate_selector(
						array(
							'selector'  => $root_selector,
							'operation' => 'suffix',
							'to_add'    => '.ct-language',
						)
					)
				),
				'variable' => 'theme-link-hover-color',
			),
		),
		'responsive' => true,
	)
);


// transparent state
if ( isset( $has_transparent_header ) && $has_transparent_header ) {
	blocksy_output_colors(
		array(
			'value'      => blocksy_companion_akg( 'transparent_ls_label_color', $atts ),
			'default'    => array(
				'default' => array( 'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ) ),
				'hover'   => array( 'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ) ),
			),
			'css'        => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'variables'  => array(
				'default' => array(
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector(
							array(
								'selector'  => blocksy_mutate_selector(
									array(
										'selector'  => $root_selector,
										'operation' => 'suffix',
										'to_add'    => '.ct-language',
									)
								),
								'operation' => 'between',
								'to_add'    => '[data-transparent-row="yes"]',
							)
						)
					),
					'variable' => 'theme-link-initial-color',
				),

				'hover'   => array(
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector(
							array(
								'selector'  => blocksy_mutate_selector(
									array(
										'selector'  => $root_selector,
										'operation' => 'suffix',
										'to_add'    => '.ct-language',
									)
								),
								'operation' => 'between',
								'to_add'    => '[data-transparent-row="yes"]',
							)
						)
					),
					'variable' => 'theme-link-hover-color',
				),
			),
			'responsive' => true,
		)
	);
}


// sticky state
if ( isset( $has_sticky_header ) && $has_sticky_header ) {
	blocksy_output_colors(
		array(
			'value'      => blocksy_companion_akg( 'sticky_ls_label_color', $atts ),
			'default'    => array(
				'default' => array( 'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ) ),
				'hover'   => array( 'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ) ),
			),
			'css'        => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,

			'variables'  => array(
				'default' => array(
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector(
							array(
								'selector'  => blocksy_mutate_selector(
									array(
										'selector'  => $root_selector,
										'operation' => 'suffix',
										'to_add'    => '.ct-language',
									)
								),
								'operation' => 'between',
								'to_add'    => '[data-sticky*="yes"]',
							)
						)
					),
					'variable' => 'theme-link-initial-color',
				),

				'hover'   => array(
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector(
							array(
								'selector'  => blocksy_mutate_selector(
									array(
										'selector'  => $root_selector,
										'operation' => 'suffix',
										'to_add'    => '.ct-language',
									)
								),
								'operation' => 'between',
								'to_add'    => '[data-sticky*="yes"]',
							)
						)
					),
					'variable' => 'theme-link-hover-color',
				),
			),
			'responsive' => true,
		)
	);
}

$type = 'inline';

if ( 'header' === $panel_type ) {
	$type = blocksy_companion_akg( 'ls_type', $atts, 'inline' );
}

if ( ! isset( $row_ids ) ) {
	$row_ids = array();
}

$normal_row_ids = array_filter(
	$row_ids,
	function ( $row_id ) {
		return 'offcanvas' !== $row_id;
	}
);

if ( 'dropdown' === $type && count( $normal_row_ids ) > 0 ) {
	// Dropdown top offset
	$ls_dropdown_offset = blocksy_companion_akg( 'ls_dropdown_offset', $atts, 15 );
	$css->put(
		blocksy_assemble_selector(
			blocksy_mutate_selector(
				array(
					'selector'  => $root_selector,
					'operation' => 'suffix',
					'to_add'    => 'ul',
				)
			)
		),
		'--margin-top: ' . $ls_dropdown_offset . 'px'
	);

	// Dropdown items spacing
	$ls_dropdown_items_spacing = blocksy_companion_akg( 'ls_dropdown_items_spacing', $atts, 15 );
	$css->put(
		blocksy_assemble_selector(
			blocksy_mutate_selector(
				array(
					'selector'  => $root_selector,
					'operation' => 'suffix',
					'to_add'    => 'ul',
				)
			)
		),
		'--padding: ' . $ls_dropdown_items_spacing . 'px'
	);

	blocksy_output_font_css(
		array(
			'font_value' => blocksy_companion_akg(
				'ls_dropdown_font',
				$atts,
				blocksy_typography_default_values()
			),
			'css'        => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector'   => blocksy_assemble_selector(
				blocksy_mutate_selector(
					array(
						'selector'  => $root_selector,
						'operation' => 'suffix',
						'to_add'    => 'ul[data-placement]',
					)
				)
			),
		)
	);

	// Dropdown font color
	blocksy_output_colors(
		array(
			'value'     => blocksy_companion_akg( 'ls_dropdown_font_color', $atts ),
			'default'   => array(
				'default' => array( 'color' => '#ffffff' ),
				'hover'   => array( 'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ) ),
			),
			'css'       => $css,
			'variables' => array(
				'default' => array(
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector(
							array(
								'selector'  => $root_selector,
								'operation' => 'suffix',
								'to_add'    => 'ul',
							)
						)
					),
					'variable' => 'theme-link-initial-color',
				),

				'hover'   => array(
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector(
							array(
								'selector'  => $root_selector,
								'operation' => 'suffix',
								'to_add'    => 'ul',
							)
						)
					),
					'variable' => 'theme-link-hover-color',
				),
			),
		)
	);

	// Dropdown divider
	blocksy_output_border(
		array(
			'css'          => $css,
			'selector'     => blocksy_assemble_selector(
				blocksy_mutate_selector(
					array(
						'selector'  => $root_selector,
						'operation' => 'suffix',
						'to_add'    => 'ul',
					)
				)
			),
			'variableName' => 'theme-border',
			'value'        => blocksy_companion_akg( 'ls_dropdown_divider', $atts ),
			'default'      => array(
				'width' => 1,
				'style' => 'dashed',
				'color' => array(
					'color' => 'rgba(255, 255, 255, 0.1)',
				),
			),
		)
	);

	// Dropdown background
	blocksy_output_colors(
		array(
			'value'     => blocksy_companion_akg( 'ls_dropdown_background', $atts ),
			'default'   => array(
				'default' => array( 'color' => '#29333C' ),
			),
			'css'       => $css,
			'variables' => array(
				'default' => array(
					'selector' => blocksy_assemble_selector(
						blocksy_mutate_selector(
							array(
								'selector'  => $root_selector,
								'operation' => 'suffix',
								'to_add'    => 'ul',
							)
						)
					),
					'variable' => 'background-color',
				),
			),
		)
	);

	// Box shadow
	blocksy_output_box_shadow(
		array(
			'css'        => $css,
			'tablet_css' => $tablet_css,
			'mobile_css' => $mobile_css,
			'selector'   => blocksy_assemble_selector(
				blocksy_mutate_selector(
					array(
						'selector'  => $root_selector,
						'operation' => 'suffix',
						'to_add'    => 'ul',
					)
				)
			),
			'value'      => blocksy_companion_akg(
				'ls_dropdown_shadow',
				$atts,
				blocksy_box_shadow_value(
					array(
						'enable'   => true,
						'h_offset' => 0,
						'v_offset' => 10,
						'blur'     => 20,
						'spread'   => 0,
						'inset'    => false,
						'color'    => array(
							'color' => 'rgba(41, 51, 61, 0.1)',
						),
					)
				)
			),
			'responsive' => true,
		)
	);

	// Border radius
	blocksy_output_spacing(
		array(
			'css'         => $css,
			'tablet_css'  => $tablet_css,
			'mobile_css'  => $mobile_css,
			'selector'    => blocksy_assemble_selector(
				blocksy_mutate_selector(
					array(
						'selector'  => $root_selector,
						'operation' => 'suffix',
						'to_add'    => 'ul',
					)
				)
			),
			'property'    => 'theme-border-radius',
			'value'       => blocksy_companion_akg(
				'ls_dropdown_radius',
				$atts,
				blocksy_spacing_value()
			),
			'empty_value' => 2,
		)
	);
}

blocksy_output_spacing(
	array(
		'css'        => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector'   => blocksy_assemble_selector( $root_selector ),
		'important'  => true,
		'value'      => blocksy_companion_akg(
			'ls_margin',
			$atts,
			blocksy_spacing_value()
		),
	)
);

blocksy_output_spacing(
	array(
		'css'        => $css,
		'tablet_css' => $tablet_css,
		'mobile_css' => $mobile_css,
		'selector'   => blocksy_assemble_selector( $root_selector ),
		'property'   => 'items-border-radius',
		'value'      => blocksy_companion_akg(
			'ls_border_radius',
			$atts,
			blocksy_spacing_value()
		),
	)
);

// icon
$ls_icon_size = blocksy_companion_akg( 'ls_icon_size', $atts, 15 );

if ( 15 !== $ls_icon_size ) {
	blocksy_output_responsive(
		array(
			'css'          => $css,
			'tablet_css'   => $tablet_css,
			'mobile_css'   => $mobile_css,
			'selector'     => blocksy_assemble_selector(
				blocksy_mutate_selector(
					array(
						'selector'  => $root_selector,
						'operation' => 'suffix',
						'to_add'    => '.ct-language-icon',
					)
				)
			),
			'variableName' => 'theme-icon-size',
			'value'        => $ls_icon_size,
		)
	);
}


// footer language switcher
blocksy_output_responsive(
	array(
		'css'          => $css,
		'tablet_css'   => $tablet_css,
		'mobile_css'   => $mobile_css,
		'selector'     => blocksy_assemble_selector(
			blocksy_mutate_selector(
				array(
					'selector'  => $root_selector,
					'operation' => 'replace-last',
					'to_add'    => '[data-column="language-switcher"]',
				)
			)
		),
		'variableName' => 'horizontal-alignment',
		'value'        => blocksy_companion_akg( 'footer_ls_horizontal_alignment', $atts, 'CT_CSS_SKIP_RULE' ),
		'unit'         => '',
	)
);


blocksy_output_responsive(
	array(
		'css'          => $css,
		'tablet_css'   => $tablet_css,
		'mobile_css'   => $mobile_css,
		'selector'     => blocksy_assemble_selector(
			blocksy_mutate_selector(
				array(
					'selector'  => $root_selector,
					'operation' => 'replace-last',
					'to_add'    => '[data-column="language-switcher"]',
				)
			)
		),
		'variableName' => 'vertical-alignment',
		'value'        => blocksy_companion_akg( 'footer_ls_vertical_alignment', $atts, 'CT_CSS_SKIP_RULE' ),
		'unit'         => '',
	)
);
