<?php
/**
 * Banner Section
 *
 * @package bridal_dresses
 */

$wp_customize->add_section(
	'bridal_dresses_banner_section',
	array(
		'panel'    => 'bridal_dresses_front_page_options',
		'title'    => esc_html__( 'Banner Section', 'bridal-dresses' ),
		'priority' => 10,
	)
);

// Banner Section - Enable Section.
$wp_customize->add_setting(
	'bridal_dresses_enable_banner_section',
	array(
		'default'           => false,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_banner_section',
		array(
			'label'    => esc_html__( 'Enable Banner Section', 'bridal-dresses' ),
			'section'  => 'bridal_dresses_banner_section',
			'settings' => 'bridal_dresses_enable_banner_section',
		)
	)
);

if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'bridal_dresses_enable_banner_section',
		array(
			'selector' => '#bridal_dresses_banner_section .section-link',
			'settings' => 'bridal_dresses_enable_banner_section',
		)
	);
}


// Banner Section - Banner Slider Content Type.
$wp_customize->add_setting(
	'bridal_dresses_banner_slider_content_type',
	array(
		'default'           => 'post',
		'sanitize_callback' => 'bridal_dresses_sanitize_select',
	)
);

$wp_customize->add_control(
	'bridal_dresses_banner_slider_content_type',
	array(
		'label'           => esc_html__( 'Select Banner Slider Content Type', 'bridal-dresses' ),
		'section'         => 'bridal_dresses_banner_section',
		'settings'        => 'bridal_dresses_banner_slider_content_type',
		'type'            => 'select',
		'active_callback' => 'bridal_dresses_is_banner_slider_section_enabled',
		'choices'         => array(
			'page' => esc_html__( 'Page', 'bridal-dresses' ),
			'post' => esc_html__( 'Post', 'bridal-dresses' ),
		),
	)
);

// Banner Slider Category Setting.
$wp_customize->add_setting('bridal_dresses_banner_slider_category', array(
	'default'           => 'slider',
	'sanitize_callback' => 'sanitize_text_field',
));

// Add custom control for Banner Slider Category with conditional visibility.
$wp_customize->add_control(new Bridal_Dresses_Customize_Category_Dropdown_Control($wp_customize, 'bridal_dresses_banner_slider_category', array(
	'label'    => __('Select Banner Category', 'bridal-dresses'),
	'section'  => 'bridal_dresses_banner_section',
	'settings' => 'bridal_dresses_banner_slider_category',
	'active_callback' => function() use ($wp_customize) {
		return $wp_customize->get_setting('bridal_dresses_banner_slider_content_type')->value() === 'post';
	},
)));

for ( $bridal_dresses_i = 1; $bridal_dresses_i <= 3; $bridal_dresses_i++ ) {

	// Banner Section - Select Banner Post.
	$wp_customize->add_setting(
		'bridal_dresses_banner_slider_content_post_' . $bridal_dresses_i,
		array(
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'bridal_dresses_banner_slider_content_post_' . $bridal_dresses_i,
		array(
			/* translators: %d: Post Count. */
			'label'           => sprintf( esc_html__( 'Select Post %d', 'bridal-dresses' ), $bridal_dresses_i ),
			'description'     => sprintf( esc_html__( 'Kindly :- Select a Post based on the category selected in the upper settings', 'bridal-dresses' ), $bridal_dresses_i ),
			'section'         => 'bridal_dresses_banner_section',
			'settings'        => 'bridal_dresses_banner_slider_content_post_' . $bridal_dresses_i,
			'active_callback' => 'bridal_dresses_is_banner_slider_section_and_content_type_post_enabled',
			'type'            => 'select',
			'choices'         => bridal_dresses_get_post_choices(),
		)
	);

	// Banner Section - Select Banner Page.
	$wp_customize->add_setting(
		'bridal_dresses_banner_slider_content_page_' . $bridal_dresses_i,
		array(
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'bridal_dresses_banner_slider_content_page_' . $bridal_dresses_i,
		array(
			/* translators: %d: Page Count. */
			'label'           => sprintf( esc_html__( 'Select Page %d', 'bridal-dresses' ), $bridal_dresses_i ),
			'section'         => 'bridal_dresses_banner_section',
			'settings'        => 'bridal_dresses_banner_slider_content_page_' . $bridal_dresses_i,
			'active_callback' => 'bridal_dresses_is_banner_slider_section_and_content_type_page_enabled',
			'type'            => 'select',
			'choices'         => bridal_dresses_get_page_choices(),
		)
	);

	// Banner Section - Button Label.
	$wp_customize->add_setting(
		'bridal_dresses_banner_button_label_' . $bridal_dresses_i,
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'bridal_dresses_banner_button_label_' . $bridal_dresses_i,
		array(
			/* translators: %d: Button Label Count. */
			'label'           => sprintf( esc_html__( 'Button Label %d', 'bridal-dresses' ), $bridal_dresses_i ),
			'section'         => 'bridal_dresses_banner_section',
			'settings'        => 'bridal_dresses_banner_button_label_' . $bridal_dresses_i,
			'type'            => 'text',
			'active_callback' => 'bridal_dresses_is_banner_slider_section_enabled',
		)
	);

	// Banner Section - Button Link.
	$wp_customize->add_setting(
		'bridal_dresses_banner_button_link_' . $bridal_dresses_i,
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'bridal_dresses_banner_button_link_' . $bridal_dresses_i,
		array(
			/* translators: %d: Button Link Count. */
			'label'           => sprintf( esc_html__( 'Button Link %d', 'bridal-dresses' ), $bridal_dresses_i ),
			'section'         => 'bridal_dresses_banner_section',
			'settings'        => 'bridal_dresses_banner_button_link_' . $bridal_dresses_i,
			'type'            => 'url',
			'active_callback' => 'bridal_dresses_is_banner_slider_section_enabled',
		)
	);

}