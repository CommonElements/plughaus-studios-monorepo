<?php
/**
 * Header Options
 *
 * @package bridal_dresses
 */

// ---------------------------------------- GENERAL OPTIONBS ----------------------------------------------------
// ---------------------------------------- PRELOADER ----------------------------------------------------

$wp_customize->add_section(
	'bridal_dresses_general_options',
	array(
		'panel' => 'bridal_dresses_theme_options',
		'title' => esc_html__( 'General Options', 'bridal-dresses' ),
	)
);

// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_preloader_separator', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_preloader_separator', array(
	'label' => __( 'Enable / Disable Site Preloader Section', 'bridal-dresses' ),
	'section' => 'bridal_dresses_general_options',
	'settings' => 'bridal_dresses_preloader_separator',
) ) );


// General Options - Enable Preloader.
$wp_customize->add_setting(
	'bridal_dresses_enable_preloader',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
		'default'           => false,
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_preloader',
		array(
			'label'   => esc_html__( 'Enable Preloader', 'bridal-dresses' ),
			'section' => 'bridal_dresses_general_options',
		)
	)
);

// Preloader Style Setting
$wp_customize->add_setting(
	'bridal_dresses_preloader_style',
	array(
		'default'           => 'style1',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'bridal_dresses_preloader_style',
	array(
		'type'     => 'select',
		'label'    => esc_html__('Select Preloader Styles', 'bridal-dresses'),
		'active_callback' => 'bridal_dresses_is_preloader_style',
		'section'  => 'bridal_dresses_general_options',
		'choices'  => array(
			'style1' => esc_html__('Style 1', 'bridal-dresses'),
			'style2' => esc_html__('Style 2', 'bridal-dresses'),
			'style3' => esc_html__('Style 3', 'bridal-dresses'),
		),
	)
);


// ---------------------------------------- PAGINATION ----------------------------------------------------

// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_pagination_separator', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_pagination_separator', array(
	'label' => __( 'Enable / Disable Pagination Section', 'bridal-dresses' ),
	'section' => 'bridal_dresses_general_options',
	'settings' => 'bridal_dresses_pagination_separator',
) ) );

// Pagination - Enable Pagination.
$wp_customize->add_setting(
	'bridal_dresses_enable_pagination',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_pagination',
		array(
			'label'    => esc_html__( 'Enable Pagination', 'bridal-dresses' ),
			'section'  => 'bridal_dresses_general_options',
			'settings' => 'bridal_dresses_enable_pagination',
			'type'     => 'checkbox',
		)
	)
);

// Pagination - Pagination Type.
$wp_customize->add_setting(
	'bridal_dresses_pagination_type',
	array(
		'default'           => 'default',
		'sanitize_callback' => 'bridal_dresses_sanitize_select',
	)
);

$wp_customize->add_control(
	'bridal_dresses_pagination_type',
	array(
		'label'           => esc_html__( 'Pagination Type', 'bridal-dresses' ),
		'section'         => 'bridal_dresses_general_options',
		'settings'        => 'bridal_dresses_pagination_type',
		'active_callback' => 'bridal_dresses_is_pagination_enabled',
		'type'            => 'select',
		'choices'         => array(
			'default' => __( 'Default (Older/Newer)', 'bridal-dresses' ),
			'numeric' => __( 'Numeric', 'bridal-dresses' ),
		),
	)
);

// ---------------------------------------- BREADCRUMB ----------------------------------------------------

// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_breadcrumb_separators', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_breadcrumb_separators', array(
	'label' => __( 'Enable / Disable Breadcrumb Section', 'bridal-dresses' ),
	'section' => 'bridal_dresses_general_options',
	'settings' => 'bridal_dresses_breadcrumb_separators',
)));

// Breadcrumb - Enable Breadcrumb.
$wp_customize->add_setting(
	'bridal_dresses_enable_breadcrumb',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
		'default'           => true,
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_breadcrumb',
		array(
			'label'   => esc_html__( 'Enable Breadcrumb', 'bridal-dresses' ),
			'section' => 'bridal_dresses_general_options',
		)
	)
);

// Breadcrumb - Separator.
$wp_customize->add_setting(
	'bridal_dresses_breadcrumb_separator',
	array(
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '/',
	)
);

$wp_customize->add_control(
	'bridal_dresses_breadcrumb_separator',
	array(
		'label'           => esc_html__( 'Separator', 'bridal-dresses' ),
		'active_callback' => 'bridal_dresses_is_breadcrumb_enabled',
		'section'         => 'bridal_dresses_general_options',
	)
);

// ---------------------------------------- Website layout ----------------------------------------------------


// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_layuout_separator', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_layuout_separator', array(
	'label' => __( 'Website Layout Setting', 'bridal-dresses' ),
	'section' => 'bridal_dresses_general_options',
	'settings' => 'bridal_dresses_layuout_separator',
)));


$wp_customize->add_setting(
	'bridal_dresses_website_layout',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
		'default'           => false,
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_website_layout',
		array(
			'label'   => esc_html__('Boxed Layout', 'bridal-dresses'),
			'section' => 'bridal_dresses_general_options',
		)
	)
);

$wp_customize->add_setting('bridal_dresses_layout_width_margin', array(
	'default'           => 50,
	'sanitize_callback' => 'bridal_dresses_sanitize_range_value',
));

$wp_customize->add_control(new Bridal_Dresses_Customize_Range_Control($wp_customize, 'bridal_dresses_layout_width_margin', array(
		'label'       => __('Set Width', 'bridal-dresses'),
		'description' => __('Adjust the width around the website layout by moving the slider. Use this setting to customize the appearance of your site to fit your design preferences.', 'bridal-dresses'),
		'section'     => 'bridal_dresses_general_options',
		'settings'    => 'bridal_dresses_layout_width_margin',
		'active_callback' => 'bridal_dresses_is_layout_enabled',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 130,
			'step' => 1,
		),
)));

// ---------------------------------------- HEADER OPTIONS ----------------------------------------------------

$wp_customize->add_section(
	'bridal_dresses_header_options',
	array(
		'panel' => 'bridal_dresses_theme_options',
		'title' => esc_html__( 'Header Options', 'bridal-dresses' ),
	)
);


// Add setting for sticky header
$wp_customize->add_setting(
	'bridal_dresses_enable_sticky_header',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
		'default'           => false,
	)
);

// Add control for sticky header setting
$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_sticky_header',
		array(
			'label'   => esc_html__( 'Enable Sticky Header', 'bridal-dresses' ),
			'section' => 'bridal_dresses_header_options',
		)
	)
);

// Header Options - Enable Topbar.
$wp_customize->add_setting(
	'bridal_dresses_enable_topbar',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
		'default'           => false,
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_topbar',
		array(
			'label'   => esc_html__( 'Enable Topbar', 'bridal-dresses' ),
			'section' => 'bridal_dresses_header_options',
		)
	)
);

// Header Options 
$wp_customize->add_setting(
	'bridal_dresses_discount_topbar_text',
	array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'bridal_dresses_discount_topbar_text',
	array(
		'label'           => esc_html__( 'Topbar Text', 'bridal-dresses' ),
		'section'         => 'bridal_dresses_header_options',
		'type'            => 'text',
		'active_callback' => 'bridal_dresses_is_topbar_enabled',
	)
);

// Header Options - Enable Social Icons.
$wp_customize->add_setting(
	'bridal_dresses_enable_social',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
		'default'           => true,
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_social',
		array(
			'label'   => esc_html__( 'Enable Social', 'bridal-dresses' ),
			'description' => esc_html__( 'If you want to add a social icon you need to go to Dashboard = Appearance = Menus then create a new menu now add Custom Links then add proper links then choose Social then click Create Menu.', 'bridal-dresses' ),
			'section' => 'bridal_dresses_header_options',
			'active_callback' => 'bridal_dresses_is_topbar_enabled',
		)
	)
);

$wp_customize->add_setting( 'bridal_dresses_menu_font_size', array(
    'default'           => 14,
    'sanitize_callback' => 'absint',
) );

// Add control for site title size
$wp_customize->add_control( 'bridal_dresses_menu_font_size', array(
    'type'        => 'number',
    'section'     => 'bridal_dresses_header_options',
    'label'       => __( 'Menu Font Size ', 'bridal-dresses' ),
    'input_attrs' => array(
        'min'  => 10,
        'max'  => 100,
        'step' => 1,
    ),
));

$wp_customize->add_setting( 'bridal_dresses_menu_text_transform', array(
    'default'           => 'uppercase', // Default value for text transform
    'sanitize_callback' => 'sanitize_text_field',
) );

// Add control for menu text transform
$wp_customize->add_control( 'bridal_dresses_menu_text_transform', array(
    'type'     => 'select',
    'section'  => 'bridal_dresses_header_options', // Adjust the section as needed
    'label'    => __( 'Menu Text Transform', 'bridal-dresses' ),
    'choices'  => array(
        'none'       => __( 'None', 'bridal-dresses' ),
        'capitalize' => __( 'Capitalize', 'bridal-dresses' ),
        'uppercase'  => __( 'Uppercase', 'bridal-dresses' ),
        'lowercase'  => __( 'Lowercase', 'bridal-dresses' ),
    ),
) );

// Menu Text Color 
$wp_customize->add_setting(
	'bridal_dresses_menu_text_color', 
	array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new WP_Customize_Color_Control(
		$wp_customize, 
		'bridal_dresses_menu_text_color', 
		array(
			'label' => __('Menu Color', 'bridal-dresses'),
			'section' => 'bridal_dresses_header_options',
		)
	)
);

// Sub Menu Text Color 
$wp_customize->add_setting(
	'bridal_dresses_sub_menu_text_color', 
	array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new WP_Customize_Color_Control(
		$wp_customize, 
		'bridal_dresses_sub_menu_text_color', 
		array(
			'label' => __('Sub Menu Color', 'bridal-dresses'),
			'section' => 'bridal_dresses_header_options',
		)
	)
);

// ----------------------------------------SITE IDENTITY----------------------------------------------------

// Site Title - Enable Setting.
$wp_customize->add_setting(
	'bridal_dresses_enable_site_title_setting',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_site_title_setting',
		array(
			'label'    => esc_html__( 'Enable Site Title', 'bridal-dresses' ),
			'section'  => 'title_tagline',
			'settings' => 'bridal_dresses_enable_site_title_setting',
		)
	)
);

// Tagline - Enable Setting.
$wp_customize->add_setting(
	'bridal_dresses_enable_tagline_setting',
	array(
		'default'           => false,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_tagline_setting',
		array(
			'label'    => esc_html__( 'Enable Tagline', 'bridal-dresses' ),
			'section'  => 'title_tagline',
			'settings' => 'bridal_dresses_enable_tagline_setting',
		)
	)
);

$wp_customize->add_setting( 'bridal_dresses_site_title_size', array(
    'default'           => 25, // Default font size in pixels
    'sanitize_callback' => 'absint', // Sanitize the input as a positive integer
) );

// Add control for site title size
$wp_customize->add_control( 'bridal_dresses_site_title_size', array(
    'type'        => 'number',
    'section'     => 'title_tagline', // You can change this section to your preferred section
    'label'       => __( 'Site Title Font Size ', 'bridal-dresses' ),
    'input_attrs' => array(
        'min'  => 10,
        'max'  => 100,
        'step' => 1,
    ),
) );

$wp_customize->add_setting('bridal_dresses_site_logo_width', array(
    'default'           => 200,
    'sanitize_callback' => 'bridal_dresses_sanitize_range_value',
));

$wp_customize->add_control(new Bridal_Dresses_Customize_Range_Control($wp_customize, 'bridal_dresses_site_logo_width', array(
    'label'       => __('Adjust Site Logo Width', 'bridal-dresses'),
    'description' => __('This setting controls the Width of Site Logo', 'bridal-dresses'),
    'section'     => 'title_tagline',
    'settings'    => 'bridal_dresses_site_logo_width',
    'input_attrs' => array(
        'min'  => 0,
        'max'  => 400,
        'step' => 5,
    ),
)));