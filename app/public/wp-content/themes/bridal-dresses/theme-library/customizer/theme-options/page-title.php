<?php
/**
 * Pige Title Options
 *
 * @package bridal_dresses
 */



$wp_customize->add_section(
	'bridal_dresses_page_title_options',
	array(
		'panel' => 'bridal_dresses_theme_options',
		'title' => esc_html__( 'Page Title', 'bridal-dresses' ),
	)
);

$wp_customize->add_setting(
    'bridal_dresses_page_header_visibility',
    array(
        'default'           => 'all-devices',
        'sanitize_callback' => 'bridal_dresses_sanitize_select',
    )
);

$wp_customize->add_control(
    new WP_Customize_Control(
        $wp_customize,
        'bridal_dresses_page_header_visibility',
        array(
            'label'    => esc_html__( 'Page Header Visibility', 'bridal-dresses' ),
            'type'     => 'select',
            'section'  => 'bridal_dresses_page_title_options',
            'settings' => 'bridal_dresses_page_header_visibility',
            'priority' => 10,
            'choices'  => array(
                'all-devices'        => esc_html__( 'Show on all devices', 'bridal-dresses' ),
                'hide-tablet'        => esc_html__( 'Hide on Tablet', 'bridal-dresses' ),
                'hide-mobile'        => esc_html__( 'Hide on Mobile', 'bridal-dresses' ),
                'hide-tablet-mobile' => esc_html__( 'Hide on Tablet & Mobile', 'bridal-dresses' ),
                'hide-all-devices'   => esc_html__( 'Hide on all devices', 'bridal-dresses' ),
            ),
        )
    )
);


$wp_customize->add_setting( 'bridal_dresses_page_title_background_separator', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_page_title_background_separator', array(
	'label' => __( 'Page Title BG Image & Color Setting', 'bridal-dresses' ),
	'section' => 'bridal_dresses_page_title_options',
	'settings' => 'bridal_dresses_page_title_background_separator',
)));


$wp_customize->add_setting(
	'bridal_dresses_page_header_style',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
		'default'           => False,
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_page_header_style',
		array(
			'label'   => esc_html__('Page Title Background Image', 'bridal-dresses'),
			'section' => 'bridal_dresses_page_title_options',
		)
	)
);

$wp_customize->add_setting( 'bridal_dresses_page_header_background_image', array(
    'default' => '',
    'sanitize_callback' => 'esc_url_raw',
) );

$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'bridal_dresses_page_header_background_image', array(
    'label'    => __( 'Background Image', 'bridal-dresses' ),
    'section'  => 'bridal_dresses_page_title_options',
	'description' => __('Choose either a background image or a color. If a background image is selected, the background color will not be visible.', 'bridal-dresses'),
    'settings' => 'bridal_dresses_page_header_background_image',
	'active_callback' => 'bridal_dresses_is_pagetitle_bcakground_image_enabled',
)));


$wp_customize->add_setting('bridal_dresses_page_header_image_height', array(
	'default'           => 200,
	'sanitize_callback' => 'bridal_dresses_sanitize_range_value',
));

$wp_customize->add_control(new Bridal_Dresses_Customize_Range_Control($wp_customize, 'bridal_dresses_page_header_image_height', array(
		'label'       => __('Image Height', 'bridal-dresses'),
		'section'     => 'bridal_dresses_page_title_options',
		'settings'    => 'bridal_dresses_page_header_image_height',
		'active_callback' => 'bridal_dresses_is_pagetitle_bcakground_image_enabled',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 1000,
			'step' => 5,
		),
)));


$wp_customize->add_setting('bridal_dresses_page_title_background_color_setting', array(
    'default' => '',
    'sanitize_callback' => 'sanitize_hex_color',
));

$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'bridal_dresses_page_title_background_color_setting', array(
    'label' => __('Page Title Background Color', 'bridal-dresses'),
    'section' => 'bridal_dresses_page_title_options',
)));

$wp_customize->add_setting('bridal_dresses_pagetitle_height', array(
    'default'           => 50,
    'sanitize_callback' => 'bridal_dresses_sanitize_range_value',
));

$wp_customize->add_control(new Bridal_Dresses_Customize_Range_Control($wp_customize, 'bridal_dresses_pagetitle_height', array(
    'label'       => __('Set Height', 'bridal-dresses'),
    'description' => __('This setting controls the page title height when no background image is set. If a background image is set, this setting will not apply.', 'bridal-dresses'),
    'section'     => 'bridal_dresses_page_title_options',
    'settings'    => 'bridal_dresses_pagetitle_height',
    'input_attrs' => array(
        'min'  => 0,
        'max'  => 300,
        'step' => 5,
    ),
)));


$wp_customize->add_setting( 'bridal_dresses_page_title_style_separator', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_page_title_style_separator', array(
	'label' => __( 'Page Title Styling Setting', 'bridal-dresses' ),
	'section' => 'bridal_dresses_page_title_options',
	'settings' => 'bridal_dresses_page_title_style_separator',
)));

$wp_customize->add_setting( 'bridal_dresses_page_header_heading_tag', array(
	'default'   => 'h1',
	'sanitize_callback' => 'bridal_dresses_sanitize_select',
) );

$wp_customize->add_control( 'bridal_dresses_page_header_heading_tag', array(
	'label'   => __( 'Page Title Heading Tag', 'bridal-dresses' ),
	'section' => 'bridal_dresses_page_title_options',
	'type'    => 'select',
	'choices' => array(
		'h1' => __( 'H1', 'bridal-dresses' ),
		'h2' => __( 'H2', 'bridal-dresses' ),
		'h3' => __( 'H3', 'bridal-dresses' ),
		'h4' => __( 'H4', 'bridal-dresses' ),
		'h5' => __( 'H5', 'bridal-dresses' ),
		'h6' => __( 'H6', 'bridal-dresses' ),
		'p' => __( 'p', 'bridal-dresses' ),
		'a' => __( 'a', 'bridal-dresses' ),
		'div' => __( 'div', 'bridal-dresses' ),
		'span' => __( 'span', 'bridal-dresses' ),
	),
) );

$wp_customize->add_setting('bridal_dresses_page_header_layout', array(
	'default' => 'left',
	'sanitize_callback' => 'sanitize_text_field',
));

$wp_customize->add_control('bridal_dresses_page_header_layout', array(
	'label' => __('Style', 'bridal-dresses'),
	'section' => 'bridal_dresses_page_title_options',
	'description' => __('"Flex Layout Style" wont work below 600px (mobile media)', 'bridal-dresses'),
	'settings' => 'bridal_dresses_page_header_layout',
	'type' => 'radio',
	'choices' => array(
		'left' => __('Classic', 'bridal-dresses'),
		'right' => __('Aligned Right', 'bridal-dresses'),
		'center' => __('Centered Focus', 'bridal-dresses'),
		'flex' => __('Flex Layout', 'bridal-dresses'),
	),
));