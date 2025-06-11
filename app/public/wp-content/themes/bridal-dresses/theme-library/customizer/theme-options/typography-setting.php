<?php
/**
 * Typography Setting
 *
 * @package bridal_dresses
 */

// Typography Setting
$wp_customize->add_section(
    'bridal_dresses_typography_setting',
    array(
        'panel' => 'bridal_dresses_theme_options',
        'title' => esc_html__( 'Typography Setting', 'bridal-dresses' ),
    )
);

$wp_customize->add_setting(
    'bridal_dresses_site_title_font',
    array(
        'default'           => 'Playfair Display',
        'sanitize_callback' => 'bridal_dresses_sanitize_google_fonts',
    )
);

$wp_customize->add_control(
    'bridal_dresses_site_title_font',
    array(
        'label'    => esc_html__( 'Site Title Font Family', 'bridal-dresses' ),
        'section'  => 'bridal_dresses_typography_setting',
        'settings' => 'bridal_dresses_site_title_font',
        'type'     => 'select',
        'choices'  => bridal_dresses_get_all_google_font_families(),
    )
);

// Typography - Site Description Font.
$wp_customize->add_setting(
	'bridal_dresses_site_description_font',
	array(
		'default'           => 'Open Sans',
		'sanitize_callback' => 'bridal_dresses_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'bridal_dresses_site_description_font',
	array(
		'label'    => esc_html__( 'Site Description Font Family', 'bridal-dresses' ),
		'section'  => 'bridal_dresses_typography_setting',
		'settings' => 'bridal_dresses_site_description_font',
		'type'     => 'select',
		'choices'  => bridal_dresses_get_all_google_font_families(),
	)
);

// Typography - Header Font.
$wp_customize->add_setting(
	'bridal_dresses_header_font',
	array(
		'default'           => 'Playfair Display',
		'sanitize_callback' => 'bridal_dresses_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'bridal_dresses_header_font',
	array(
		'label'    => esc_html__( 'Heading Font Family', 'bridal-dresses' ),
		'section'  => 'bridal_dresses_typography_setting',
		'settings' => 'bridal_dresses_header_font',
		'type'     => 'select',
		'choices'  => bridal_dresses_get_all_google_font_families(),
	)
);

// Typography - Body Font.
$wp_customize->add_setting(
	'bridal_dresses_content_font',
	array(
		'default'           => 'Open Sans',
		'sanitize_callback' => 'bridal_dresses_sanitize_google_fonts',
	)
);

$wp_customize->add_control(
	'bridal_dresses_content_font',
	array(
		'label'    => esc_html__( 'Content Font Family', 'bridal-dresses' ),
		'section'  => 'bridal_dresses_typography_setting',
		'settings' => 'bridal_dresses_content_font',
		'type'     => 'select',
		'choices'  => bridal_dresses_get_all_google_font_families(),
	)
);
