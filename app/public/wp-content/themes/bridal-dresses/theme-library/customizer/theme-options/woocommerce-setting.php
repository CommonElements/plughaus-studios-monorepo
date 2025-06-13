<?php
/**
 * WooCommerce Settings
 *
 * @package bridal_dresses
 */

$wp_customize->add_section(
	'bridal_dresses_woocommerce_settings',
	array(
		'panel' => 'bridal_dresses_theme_options',
		'title' => esc_html__( 'WooCommerce Settings', 'bridal-dresses' ),
	)
);

//WooCommerce - Products per page.
$wp_customize->add_setting( 'bridal_dresses_products_per_page', array(
    'default'           => 9,
    'sanitize_callback' => 'absint',
));

$wp_customize->add_control( 'bridal_dresses_products_per_page', array(
    'type'        => 'number',
    'section'     => 'bridal_dresses_woocommerce_settings',
    'label'       => __( 'Products Per Page', 'bridal-dresses' ),
    'input_attrs' => array(
        'min'  => 0,
        'max'  => 50,
        'step' => 1,
    ),
));

//WooCommerce - Products per row.
$wp_customize->add_setting( 'bridal_dresses_products_per_row', array(
    'default'           => '3',
    'sanitize_callback' => 'bridal_dresses_sanitize_choices',
) );

$wp_customize->add_control( 'bridal_dresses_products_per_row', array(
    'label'    => __( 'Products Per Row', 'bridal-dresses' ),
    'section'  => 'bridal_dresses_woocommerce_settings',
    'settings' => 'bridal_dresses_products_per_row',
    'type'     => 'select',
    'choices'  => array(
        '2' => '2',
		'3' => '3',
		'4' => '4',
    ),
) );

//WooCommerce - Show / Hide Related Product.
$wp_customize->add_setting(
	'bridal_dresses_related_product_show_hide',
	array(
		'default'           => true,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_related_product_show_hide',
		array(
			'label'   => esc_html__( 'Show / Hide Related product', 'bridal-dresses' ),
			'section' => 'bridal_dresses_woocommerce_settings',
		)
	)
);

// WooCommerce - Product Sale Position.
$wp_customize->add_setting(
	'bridal_dresses_product_sale_position', 
	array(
		'default' => 'left',
		'sanitize_callback' => 'sanitize_text_field',
));

$wp_customize->add_control(
	'bridal_dresses_product_sale_position', 
	array(
		'label' => __('Product Sale Position', 'bridal-dresses'),
		'section' => 'bridal_dresses_woocommerce_settings',
		'settings' => 'bridal_dresses_product_sale_position',
		'type' => 'radio',
		'choices' => 
	array(
		'left' => __('Left', 'bridal-dresses'),
		'right' => __('Right', 'bridal-dresses'),
	),
));