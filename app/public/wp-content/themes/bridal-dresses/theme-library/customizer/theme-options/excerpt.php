<?php
/**
 * Excerpt
 *
 * @package bridal_dresses
 */

$wp_customize->add_section(
	'bridal_dresses_excerpt_options',
	array(
		'panel' => 'bridal_dresses_theme_options',
		'title' => esc_html__( 'Excerpt', 'bridal-dresses' ),
	)
);

// Excerpt - Excerpt Length.
$wp_customize->add_setting(
	'bridal_dresses_excerpt_length',
	array(
		'default'           => 20,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	)
);

$wp_customize->add_control(
	'bridal_dresses_excerpt_length',
	array(
		'label'       => esc_html__( 'Excerpt Length (no. of words)', 'bridal-dresses' ),
		'section'     => 'bridal_dresses_excerpt_options',
		'settings'    => 'bridal_dresses_excerpt_length',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 10,
			'max'  => 200,
			'step' => 1,
		),
	)
);