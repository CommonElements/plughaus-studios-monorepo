<?php
/**
 * Single Post Options
 *
 * @package bridal_dresses
 */

$wp_customize->add_section(
	'bridal_dresses_single_post_options',
	array(
		'title' => esc_html__( 'Single Post Options', 'bridal-dresses' ),
		'panel' => 'bridal_dresses_theme_options',
	)
);

// Post Options - Show / Hide Date.
$wp_customize->add_setting(
	'bridal_dresses_single_post_hide_date',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_single_post_hide_date',
		array(
			'label'   => esc_html__( 'Show / Hide Date', 'bridal-dresses' ),
			'section' => 'bridal_dresses_single_post_options',
		)
	)
);

// Post Options - Show / Hide Author.
$wp_customize->add_setting(
	'bridal_dresses_single_post_hide_author',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_single_post_hide_author',
		array(
			'label'   => esc_html__( 'Show / Hide Author', 'bridal-dresses' ),
			'section' => 'bridal_dresses_single_post_options',
		)
	)
);

// Post Options - Show / Hide Comments.
$wp_customize->add_setting(
	'bridal_dresses_single_post_hide_comments',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_single_post_hide_comments',
		array(
			'label'   => esc_html__( 'Show / Hide Comments', 'bridal-dresses' ),
			'section' => 'bridal_dresses_single_post_options',
		)
	)
);

// Post Options - Show / Hide Time.
$wp_customize->add_setting(
	'bridal_dresses_single_post_hide_time',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_single_post_hide_time',
		array(
			'label'   => esc_html__( 'Show / Hide Time', 'bridal-dresses' ),
			'section' => 'bridal_dresses_single_post_options',
		)
	)
);

// Post Options - Show / Hide Category.
$wp_customize->add_setting(
	'bridal_dresses_single_post_hide_category',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_single_post_hide_category',
		array(
			'label'   => esc_html__( 'Show / Hide Category', 'bridal-dresses' ),
			'section' => 'bridal_dresses_single_post_options',
		)
	)
);

// Post Options - Show / Hide Tag.
$wp_customize->add_setting(
	'bridal_dresses_post_hide_tags',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_post_hide_tags',
		array(
			'label'   => esc_html__( 'Show / Hide Tag', 'bridal-dresses' ),
			'section' => 'bridal_dresses_single_post_options',
		)
	)
);

// Post Options - Comment Title.
$wp_customize->add_setting(
	'bridal_dresses_blog_post_comment_title',
	array(
		'default'=> 'Leave a Reply',
		'sanitize_callback'	=> 'sanitize_text_field'
	)
);

$wp_customize->add_control(
	'bridal_dresses_blog_post_comment_title',
	array(
		'label'	=> __('Comment Title','bridal-dresses'),
		'input_attrs' => array(
			'placeholder' => __( 'Leave a Reply', 'bridal-dresses' ),
		),
		'section'=> 'bridal_dresses_single_post_options',
		'type'=> 'text'
	)
);