<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound, VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- include scope, variables come from blocksy_companion_get_variables_from_file()

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $prefix ) ) {
	$prefix .= '_';
} else {
	$prefix = '';
}

$additional_options    = array();
$language_type_choices = ! empty( $include_svg_choice ) ? array(
	'svg'   => __( 'Icon', 'blocksy-companion' ),
	'icon'  => __( 'Flag', 'blocksy-companion' ),
	'label' => __( 'Label', 'blocksy-companion' ),
) : array(
	'icon'  => __( 'Flag', 'blocksy-companion' ),
	'label' => __( 'Label', 'blocksy-companion' ),
);

if (
	(
		class_exists( 'TRP_Translate_Press' )
		||
		function_exists( 'pll_the_languages' )
	)
	&&
	'dropdown_' !== $prefix
) {
	$additional_options[] = array(
		$prefix . 'ls_flag_aspect_ratio' => array(
			'label'   => __( 'Flag Shape', 'blocksy-companion' ),
			'type'    => 'ct-radio',
			'value'   => '4x3',
			'design'  => 'block',
			'divider' => 'top',
			'setting' => array( 'transport' => 'postMessage' ),
			'choices' => array(
				'4x3' => __( 'Rectangle', 'blocksy-companion' ),
				'1x1' => __( 'Square', 'blocksy-companion' ),
			),
		),
	);
}

$common_options = array( // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- consumed by the including scope
	$prefix . 'language_type' => array(
		'label'   => __( 'Display Type', 'blocksy-companion' ),
		'type'    => 'ct-checkboxes',
		'design'  => 'block',
		'view'    => 'text',
		'divider' => 'top',
		'value'   => array(
			'icon'  => true,
			'label' => true,
		),

		'choices' => blocksy_ordered_keys( $language_type_choices ),

		'sync'    => array(
			'id' => $sync_id,
		),
	),

	blocksy_rand_md5()        => array(
		'type'      => 'ct-condition',
		'condition' => array(
			$prefix . 'language_type/icon' => true,
		),
		'options'   => $additional_options,
	),

	blocksy_rand_md5()        => array(
		'type'      => 'ct-condition',
		'condition' => array(
			$prefix . 'language_type/label' => true,
		),
		'options'   => array(
			$prefix . 'language_label'          => array(
				'label'   => __( 'Label Style', 'blocksy-companion' ),
				'type'    => 'ct-radio',
				'value'   => 'long',
				'view'    => 'text',
				'design'  => 'block',
				'divider' => 'top',
				'choices' => array(
					'long'  => __( 'Long', 'blocksy-companion' ),
					'short' => __( 'Short', 'blocksy-companion' ),
				),
				'sync'    => array(
					'id' => $sync_id,
				),
			),

			$prefix . 'language_label_position' => array(
				'type'       => 'ct-radio',
				'label'      => __( 'Label Position', 'blocksy-companion' ),
				'value'      => 'right',
				'view'       => 'text',
				'divider'    => 'top',
				'design'     => 'block',
				'responsive' => array( 'tablet' => 'skip' ),
				'choices'    => array(
					'left'   => __( 'Left', 'blocksy-companion' ),
					'right'  => __( 'Right', 'blocksy-companion' ),
					'bottom' => __( 'Bottom', 'blocksy-companion' ),
				),
			),
		),
	),

);

$design_options = array( // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- consumed by the including scope

	blocksy_rand_md5() => array(
		'type'      => 'ct-condition',
		'condition' => array( $prefix . 'language_type/label' => true ),
		'options'   => array(

			'ls_font'          => array(
				'type'  => 'ct-typography',
				'label' => __( 'Font', 'blocksy-companion' ),
				'value' => blocksy_typography_default_values(
					array(
						'size'           => '12px',
						'variation'      => 'n6',
						'text-transform' => 'uppercase',
					)
				),
			),

			blocksy_rand_md5() => array(
				'type'       => 'ct-labeled-group',
				'label'      => __( 'Font Color', 'blocksy-companion' ),
				'divider'    => 'bottom',
				'responsive' => true,
				'choices'    => array(
					array(
						'id'    => 'ls_label_color',
						'label' => __( 'Default State', 'blocksy-companion' ),
					),

					array(
						'id'        => 'transparent_ls_label_color',
						'label'     => __( 'Transparent State', 'blocksy-companion' ),
						'condition' => array(
							'row' => '!offcanvas',
							'builderSettings/has_transparent_header' => 'yes',
						),
					),

					array(
						'id'        => 'sticky_ls_label_color',
						'label'     => __( 'Sticky State', 'blocksy-companion' ),
						'condition' => array(
							'row' => '!offcanvas',
							'builderSettings/has_sticky_header' => 'yes',
						),
					),
				),
				'options'    => array(

					'ls_label_color'             => array(
						'label'      => __( 'Font Color', 'blocksy-companion' ),
						'type'       => 'ct-color-picker',
						'design'     => 'block:right',
						'responsive' => true,

						'value'      => array(
							'default' => array(
								'color' => 'var(--theme-text-color)',
							),

							'hover'   => array(
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ),
							),
						),

						'pickers'    => array(
							array(
								'title' => __( 'Initial', 'blocksy-companion' ),
								'id'    => 'default',
							),

							array(
								'title'   => __( 'Hover/Active', 'blocksy-companion' ),
								'id'      => 'hover',
								'inherit' => 'var(--theme-link-hover-color)',
							),
						),
					),

					'transparent_ls_label_color' => array(
						'label'      => __( 'Font Color', 'blocksy-companion' ),
						'type'       => 'ct-color-picker',
						'design'     => 'block:right',
						'responsive' => true,

						'value'      => array(
							'default' => array(
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ),
							),

							'hover'   => array(
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ),
							),
						),

						'pickers'    => array(
							array(
								'title' => __( 'Initial', 'blocksy-companion' ),
								'id'    => 'default',
							),

							array(
								'title' => __( 'Hover/Active', 'blocksy-companion' ),
								'id'    => 'hover',
							),
						),
					),

					'sticky_ls_label_color'      => array(
						'label'      => __( 'Font Color', 'blocksy-companion' ),
						'type'       => 'ct-color-picker',
						'design'     => 'block:right',
						'responsive' => true,

						'value'      => array(
							'default' => array(
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ),
							),

							'hover'   => array(
								'color' => Blocksy_Css_Injector::get_skip_rule_keyword( 'DEFAULT' ),
							),
						),

						'pickers'    => array(
							array(
								'title' => __( 'Initial', 'blocksy-companion' ),
								'id'    => 'default',
							),

							array(
								'title' => __( 'Hover/Active', 'blocksy-companion' ),
								'id'    => 'hover',
							),
						),
					),

				),
			),

		),
	),

	blocksy_rand_md5() => array(
		'type'      => 'ct-condition',
		'condition' => array( $prefix . 'language_type/icon' => true ),
		'options'   => array(

			'ls_border_radius' => array(
				'label'      => __( 'Flag Border Radius', 'blocksy-companion' ),
				'type'       => 'ct-spacing',
				'divider'    => 'bottom',
				'setting'    => array( 'transport' => 'postMessage' ),
				'value'      => blocksy_spacing_value(),
				'min'        => 0,
				'responsive' => true,
			),

		),
	),

	'ls_margin'        => array(
		'label'      => __( 'Margin', 'blocksy-companion' ),
		'type'       => 'ct-spacing',
		// 'divider' => 'top',
		'value'      => blocksy_spacing_value(),
		'responsive' => true,
	),
);
