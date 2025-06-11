<?php
/**
 * Sidebar Position
 *
 * @package bridal_dresses
 */

$wp_customize->add_section(
	'bridal_dresses_sidebar_position',
	array(
		'title' => esc_html__( 'Sidebar Position', 'bridal-dresses' ),
		'panel' => 'bridal_dresses_theme_options',
	)
);

// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_global_sidebar_separator', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_global_sidebar_separator', array(
	'label' => __( 'Global Sidebar Position', 'bridal-dresses' ),
	'section' => 'bridal_dresses_sidebar_position',
	'settings' => 'bridal_dresses_global_sidebar_separator',
)));

// Sidebar Position - Global Sidebar Position.
$wp_customize->add_setting(
	'bridal_dresses_sidebar_position',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'bridal_dresses_sidebar_position',
	array(
		'label'   => esc_html__( 'Select Sidebar Position', 'bridal-dresses' ),
		'section' => 'bridal_dresses_sidebar_position',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'bridal-dresses' ),
			'left-sidebar'  => esc_html__( 'Left Sidebar', 'bridal-dresses' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'bridal-dresses' ),
		),
	)
);

// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_post_sidebar_separator', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_post_sidebar_separator', array(
	'label' => __( 'Post Sidebar Position', 'bridal-dresses' ),
	'section' => 'bridal_dresses_sidebar_position',
	'settings' => 'bridal_dresses_post_sidebar_separator',
)));

// Sidebar Position - Post Sidebar Position.
$wp_customize->add_setting(
	'bridal_dresses_post_sidebar_position',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'bridal_dresses_post_sidebar_position',
	array(
		'label'   => esc_html__( 'Select Sidebar Position', 'bridal-dresses' ),
		'section' => 'bridal_dresses_sidebar_position',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'bridal-dresses' ),
			'left-sidebar'  => esc_html__( 'Left Sidebar', 'bridal-dresses' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'bridal-dresses' ),
		),
	)
);

// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_page_sidebar_separator', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_page_sidebar_separator', array(
	'label' => __( 'Page Sidebar Position', 'bridal-dresses' ),
	'section' => 'bridal_dresses_sidebar_position',
	'settings' => 'bridal_dresses_page_sidebar_separator',
)));

// Sidebar Position - Page Sidebar Position.
$wp_customize->add_setting(
	'bridal_dresses_page_sidebar_position',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_select',
		'default'           => 'right-sidebar',
	)
);

$wp_customize->add_control(
	'bridal_dresses_page_sidebar_position',
	array(
		'label'   => esc_html__( 'Select Sidebar Position', 'bridal-dresses' ),
		'section' => 'bridal_dresses_sidebar_position',
		'type'    => 'select',
		'choices' => array(
			'right-sidebar' => esc_html__( 'Right Sidebar', 'bridal-dresses' ),
			'left-sidebar'  => esc_html__( 'Left Sidebar', 'bridal-dresses' ),
			'no-sidebar'    => esc_html__( 'No Sidebar', 'bridal-dresses' ),
		),
	)
);

// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_sidebar_width_separator', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_sidebar_width_separator', array(
	'label' => __( 'Sidebar Width Setting', 'bridal-dresses' ),
	'section' => 'bridal_dresses_sidebar_position',
	'settings' => 'bridal_dresses_sidebar_width_separator',
)));


$wp_customize->add_setting( 'bridal_dresses_sidebar_width', array(
	'default'           => '30',
	'sanitize_callback' => 'bridal_dresses_sanitize_range_value',
) );

$wp_customize->add_control(new Bridal_Dresses_Customize_Range_Control($wp_customize, 'bridal_dresses_sidebar_width', array(
	'section'     => 'bridal_dresses_sidebar_position',
	'label'       => __( 'Adjust Sidebar Width', 'bridal-dresses' ),
	'description' => __( 'Adjust the width of the sidebar.', 'bridal-dresses' ),
	'input_attrs' => array(
		'min'  => 10,
		'max'  => 50,
		'step' => 1,
	),
)));

$wp_customize->add_setting( 'bridal_dresses_sidebar_widget_font_size', array(
    'default'           => 24,
    'sanitize_callback' => 'absint',
) );

// Add control for site title size
$wp_customize->add_control( 'bridal_dresses_sidebar_widget_font_size', array(
    'type'        => 'number',
    'section'     => 'bridal_dresses_sidebar_position',
    'label'       => __( 'Sidebar Widgets Heading Font Size ', 'bridal-dresses' ),
    'input_attrs' => array(
        'min'  => 10,
        'max'  => 100,
        'step' => 1,
    ),
));