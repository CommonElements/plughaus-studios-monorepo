<?php
/**
 * Service Section
 *
 * @package bridal_dresses
 */

$wp_customize->add_section(
	'bridal_dresses_product_section',
	array(
		'panel'    => 'bridal_dresses_front_page_options',
		'title'    => esc_html__( 'Product Section', 'bridal-dresses' ),
		'priority' => 10,
	)
);

// Service Section - Enable Section.
$wp_customize->add_setting(
	'bridal_dresses_enable_service_section',
	array(
		'default'           => false,
		'sanitize_callback' => 'bridal_dresses_sanitize_switch',
	)
);

$wp_customize->add_control(
	new Bridal_Dresses_Toggle_Switch_Custom_Control(
		$wp_customize,
		'bridal_dresses_enable_service_section',
		array(
			'label'    => esc_html__( 'Enable Product Section', 'bridal-dresses' ),
			'section'  => 'bridal_dresses_product_section',
			'settings' => 'bridal_dresses_enable_service_section',
		)
	)
);

if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial(
		'bridal_dresses_enable_service_section',
		array(
			'selector' => '#bridal_dresses_service_section .section-link',
			'settings' => 'bridal_dresses_enable_service_section',
		)
	);
}

// Service Section - Button Label.
$wp_customize->add_setting(
	'bridal_dresses_trending_product_heading',
	array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

$wp_customize->add_control(
	'bridal_dresses_trending_product_heading',
	array(
		'label'           => esc_html__( 'Heading', 'bridal-dresses' ),
		'section'         => 'bridal_dresses_product_section',
		'settings'        => 'bridal_dresses_trending_product_heading',
		'type'            => 'text',
		'active_callback' => 'bridal_dresses_is_service_section_enabled',
	)
);

$wp_customize->add_setting(
	'bridal_dresses_services_number',
	array(
	    'default'=> '',
	    'sanitize_callback' => 'sanitize_text_field'
));
$wp_customize->add_control(
	'bridal_dresses_services_number',
	array(
	    'label' => esc_html__('No of Tabs to show','bridal-dresses'),
	    'description' => esc_html__('Add Tabs Then Refresh this page to show Fields','bridal-dresses'),
	    'section'=> 'bridal_dresses_product_section',
	    'type' => 'number',
	    'input_attrs' => array(
	    'step'  => 1,
			'min'  => 0,
			'max'  => 4,
	    ),
	    'active_callback' => 'bridal_dresses_is_service_section_enabled',
	)
);

$bridal_dresses_featured_post = get_theme_mod('bridal_dresses_services_number','');
for ( $bridal_dresses_j = 1; $bridal_dresses_j <= $bridal_dresses_featured_post; $bridal_dresses_j++ ) {

    $wp_customize->add_setting(
    	'bridal_dresses_services_text'.$bridal_dresses_j,
    	array(
	        'default'=> '',
	        'sanitize_callback' => 'sanitize_text_field'
    	)
    );
    $wp_customize->add_control(
    	'bridal_dresses_services_text'.$bridal_dresses_j,
    	array(
	        'label' => esc_html__('Tab ','bridal-dresses').$bridal_dresses_j,
	        'section'=> 'bridal_dresses_product_section',
	        'type'=> 'text',
	        'active_callback' => 'bridal_dresses_is_service_section_enabled',
    	)
    );

	    $bridal_dresses_args = array(
		'type'                     => 'product',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'term_group',
		'order'                    => 'ASC',
		'hide_empty'               => false,
		'hierarchical'             => 1,
		'number'                   => '',
		'taxonomy'                 => 'product_cat',
		'pad_counts'               => false
	);
	$bridal_dresses_categories = get_categories($bridal_dresses_args);
	$bridal_dresses_cat_posts = array();
	$bridal_dresses_m = 0;
	$bridal_dresses_cat_posts[]='Select';
	foreach($bridal_dresses_categories as $bridal_dresses_category){
		if($bridal_dresses_m==0){
			$default = $bridal_dresses_category->term_id;
			$bridal_dresses_m++;
		}
		$bridal_dresses_cat_posts[$bridal_dresses_category->term_id] = $bridal_dresses_category->name;
	}


	$wp_customize->add_setting('bridal_dresses_trending_product_category'.$bridal_dresses_j,array(
		'default'	=> 'select',
		'sanitize_callback' => 'bridal_dresses_sanitize_select',
	));
	$wp_customize->add_control('bridal_dresses_trending_product_category'.$bridal_dresses_j,array(
		'type'    => 'select',
		'choices' => $bridal_dresses_cat_posts,
		'label' => __('Select category to display products ','bridal-dresses'),
		'section' => 'bridal_dresses_product_section',
		'active_callback' => 'bridal_dresses_is_service_section_enabled',
	));
}