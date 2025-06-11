<?php
/**
 * Color Option
 *
 * @package bridal_dresses
 */

// Primary Color.
$wp_customize->add_setting(
	'primary_color',
	array(
		'default'           => '#8E4162',
		'sanitize_callback' => 'sanitize_hex_color',
	)
);

$wp_customize->add_control(
	new WP_Customize_Color_Control(
		$wp_customize,
		'primary_color',
		array(
			'label'    => __( 'Primary Color', 'bridal-dresses' ),
			'section'  => 'colors',
			'priority' => 5,
		)
	)
);
