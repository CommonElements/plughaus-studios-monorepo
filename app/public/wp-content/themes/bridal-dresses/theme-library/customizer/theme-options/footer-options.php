<?php
/**
 * Footer Options
 *
 * @package bridal_dresses
 */

$wp_customize->add_section(
	'bridal_dresses_footer_options',
	array(
		'panel' => 'bridal_dresses_theme_options',
		'title' => esc_html__( 'Footer Options', 'bridal-dresses' ),
	)
);

// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_footer_separators', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_footer_separators', array(
	'label' => __( 'Footer Settings', 'bridal-dresses' ),
	'section' => 'bridal_dresses_footer_options',
	'settings' => 'bridal_dresses_footer_separators',
)));

// Footer Section - Enable Section.
$wp_customize->add_setting(
	'bridal_dresses_enable_footer_section',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_footer_section',
		array(
			'label'    => esc_html__( 'Show / Hide Footer', 'bridal-dresses' ),
			'section'  => 'bridal_dresses_footer_options',
			'settings' => 'bridal_dresses_enable_footer_section',
		)
	)
);

// column // 
$wp_customize->add_setting(
	'bridal_dresses_footer_widget_column',
	array(
        'default'			=> '4',
		'capability'     	=> 'edit_theme_options',
		'sanitize_callback' => 'bridal_dresses_sanitize_select',
		
	)
);	

$wp_customize->add_control(
	'bridal_dresses_footer_widget_column',
	array(
	    'label'   		=> __('Select Widget Column','bridal-dresses'),
		'description' => __('Note: Default footer widgets are shown. Add your preferred widgets in (Appearance > Widgets > Footer) to see changes.', 'bridal-dresses'),
	    'section' 		=> 'bridal_dresses_footer_options',
		'type'			=> 'select',
		'choices'        => 
		array(
			'' => __( 'None', 'bridal-dresses' ),
			'1' => __( '1 Column', 'bridal-dresses' ),
			'2' => __( '2 Column', 'bridal-dresses' ),
			'3' => __( '3 Column', 'bridal-dresses' ),
			'4' => __( '4 Column', 'bridal-dresses' )
		) 
	) 
);

//  BG Color // 
$wp_customize->add_setting('footer_background_color_setting', array(
    'default' => '#000',
    'sanitize_callback' => 'sanitize_hex_color',
));

$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_background_color_setting', array(
    'label' => __('Footer Background Color', 'bridal-dresses'),
    'section' => 'bridal_dresses_footer_options',
)));

// Footer Background Image Setting
$wp_customize->add_setting('footer_background_image_setting', array(
    'default' => '',
    'sanitize_callback' => 'esc_url_raw',
));

$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'footer_background_image_setting', array(
    'label' => __('Footer Background Image', 'bridal-dresses'),
    'section' => 'bridal_dresses_footer_options',
)));

// Footer Background Attachment
$wp_customize->add_setting(
	'bridal_dresses_footer_image_attachment_setting',
	array(
		'default'=> 'scroll',
		'sanitize_callback' => 'bridal_dresses_sanitize_choices'
	)
);

$wp_customize->add_control(
	'bridal_dresses_footer_image_attachment_setting',
	array(
		'type' => 'select',
		'label' => __('Footer Background Attatchment','bridal-dresses'),
		'choices' => array(
			'fixed' => __('fixed','bridal-dresses'),
			'scroll' => __('scroll','bridal-dresses'),
		),
		'section'=> 'bridal_dresses_footer_options',
  	)
);

$wp_customize->add_setting('footer_text_transform', array(
    'default' => 'none',
    'sanitize_callback' => 'sanitize_text_field',
));

// Add Footer Text Transform Control
$wp_customize->add_control('footer_text_transform', array(
    'label' => __('Footer Heading Text Transform', 'bridal-dresses'),
    'section' => 'bridal_dresses_footer_options',
    'settings' => 'footer_text_transform',
    'type' => 'select',
    'choices' => array(
        'none' => __('None', 'bridal-dresses'),
        'capitalize' => __('Capitalize', 'bridal-dresses'),
        'uppercase' => __('Uppercase', 'bridal-dresses'),
        'lowercase' => __('Lowercase', 'bridal-dresses'),
    ),
));

$wp_customize->add_setting(
	'bridal_dresses_footer_copyright_text',
	array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_post',
		'transport'         => 'refresh',
	)
);

$wp_customize->add_control(
	'bridal_dresses_footer_copyright_text',
	array(
		'label'    => esc_html__( 'Copyright Text', 'bridal-dresses' ),
		'section'  => 'bridal_dresses_footer_options',
		'settings' => 'bridal_dresses_footer_copyright_text',
		'type'     => 'textarea',
	)
);

//Copyright Alignment
$wp_customize->add_setting(
	'bridal_dresses_footer_bottom_align',
	array(
		'default' 			=> 'center',
		'sanitize_callback' => 'sanitize_text_field'
	)
);

$wp_customize->add_control(
	'bridal_dresses_footer_bottom_align',
	array(
		'label' => __('Copyright Alignment ','bridal-dresses'),
		'section' => 'bridal_dresses_footer_options',
		'type'			=> 'select',
		'choices' => 
		array(
			'left' => __('Left','bridal-dresses'),
			'right' => __('Right','bridal-dresses'),
			'center' => __('Center','bridal-dresses'),
		),
	)
);

// Add Separator Custom Control
$wp_customize->add_setting( 'bridal_dresses_scroll_separators', array(
	'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( new Bridal_Dresses_Separator_Custom_Control( $wp_customize, 'bridal_dresses_scroll_separators', array(
	'label' => __( 'Scroll Top Settings', 'bridal-dresses' ),
	'section' => 'bridal_dresses_footer_options',
	'settings' => 'bridal_dresses_scroll_separators',
)));

// Footer Options - Scroll Top.
$wp_customize->add_setting(
	'bridal_dresses_scroll_top',
	array(
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
		'default'           => true,
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_scroll_top',
		array(
			'label'   => esc_html__( 'Enable Scroll Top Button', 'bridal-dresses' ),
			'section' => 'bridal_dresses_footer_options',
		)
	)
);
// icon // 
$wp_customize->add_setting(
	'bridal_dresses_scroll_btn_icon',
	array(
        'default' => 'fas fa-chevron-up',
		'sanitize_callback' => 'sanitize_text_field',
		'capability' => 'edit_theme_options',
		
	)
);	

$wp_customize->add_control(new Bridal_Dresses_Change_Icon_Control($wp_customize, 
	'bridal_dresses_scroll_btn_icon',
	array(
	    'label'   		=> __('Scroll Top Icon','bridal-dresses'),
	    'section' 		=> 'bridal_dresses_footer_options',
		'iconset' => 'fa',
	))  
);


$wp_customize->add_setting( 'bridal_dresses_scroll_top_position', array(
    'default'           => 'bottom-right',
    'sanitize_callback' => 'bridal_dresses_sanitize_scroll_top_position',
) );

// Add control for Scroll Top Button Position
$wp_customize->add_control( 'bridal_dresses_scroll_top_position', array(
    'label'    => __( 'Scroll Top Button Position', 'bridal-dresses' ),
    'section'  => 'bridal_dresses_footer_options',
    'settings' => 'bridal_dresses_scroll_top_position',
    'type'     => 'select',
    'choices'  => array(
        'bottom-right' => __( 'Bottom Right', 'bridal-dresses' ),
        'bottom-left'  => __( 'Bottom Left', 'bridal-dresses' ),
        'bottom-center'=> __( 'Bottom Center', 'bridal-dresses' ),
    ),
) );

$wp_customize->add_setting( 'bridal_dresses_scroll_top_shape', array(
    'default'           => 'box',
    'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control( 'bridal_dresses_scroll_top_shape', array(
    'label'    => __( 'Scroll to Top Button Shape', 'bridal-dresses' ),
    'section'  => 'bridal_dresses_footer_options',
    'settings' => 'bridal_dresses_scroll_top_shape',
    'type'     => 'radio',
    'choices'  => array(
        'box'        => __( 'Box', 'bridal-dresses' ),
        'curved-box' => __( 'Curved Box', 'bridal-dresses' ),
        'circle'     => __( 'Circle', 'bridal-dresses' ),
    ),
) );