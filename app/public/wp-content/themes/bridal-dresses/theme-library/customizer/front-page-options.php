<?php
/**
 * Front Page Options
 *
 * @package Bridal Dresses
 */

$wp_customize->add_panel(
	'bridal_dresses_front_page_options',
	array(
		'title'    => esc_html__( 'Front Page Options', 'bridal-dresses' ),
		'priority' => 20,
	)
);

// Banner Section.
require get_template_directory() . '/theme-library/customizer/front-page-options/banner.php';

// Tranding Product Section.
require get_template_directory() . '/theme-library/customizer/front-page-options/trending-product.php';