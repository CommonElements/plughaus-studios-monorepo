<?php
/**
 * Bridal Dresses functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bridal_dresses
 */

if ( ! defined( 'BRIDAL_DRESSES_VERSION' ) ) {
	define( 'BRIDAL_DRESSES_VERSION', '1.0.0' );
}

$bridal_dresses_theme_data = wp_get_theme();

if( ! defined( 'BRIDAL_DRESSES_THEME_NAME' ) ) define( 'BRIDAL_DRESSES_THEME_NAME', $bridal_dresses_theme_data->get( 'Name' ) );

if ( ! function_exists( 'bridal_dresses_setup' ) ) :
	
	function bridal_dresses_setup() {
		
		load_theme_textdomain( 'bridal-dresses', get_template_directory() . '/languages' );

		add_theme_support( 'woocommerce' );

		add_theme_support( 'automatic-feed-links' );
		
		add_theme_support( 'title-tag' );

		add_theme_support( 'post-thumbnails' );

		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary', 'bridal-dresses' ),
				'social'  => esc_html__( 'Social', 'bridal-dresses' ),
			)
		);

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'woocommerce',
			)
		);

		add_theme_support( 'post-formats', array(
			'image',
			'video',
			'gallery',
			'audio', 
		) );

		add_theme_support(
			'custom-background',
			apply_filters(
				'bridal_dresses_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		add_theme_support( 'customize-selective-refresh-widgets' );

		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		add_theme_support( 'align-wide' );

		add_theme_support( 'responsive-embeds' );
	}
endif;
add_action( 'after_setup_theme', 'bridal_dresses_setup' );

function bridal_dresses_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'bridal_dresses_content_width', 640 );
}
add_action( 'after_setup_theme', 'bridal_dresses_content_width', 0 );

function bridal_dresses_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'bridal-dresses' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'bridal-dresses' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title"><span>',
			'after_title'   => '</span></h2>',
		)
	);

	// Regsiter 4 footer widgets.
	$bridal_dresses_footer_widget_column = get_theme_mod('bridal_dresses_footer_widget_column','4');
	for ($bridal_dresses_i=1; $bridal_dresses_i<=$bridal_dresses_footer_widget_column; $bridal_dresses_i++) {
		register_sidebar( array(
			'name' => __( 'Footer  ', 'bridal-dresses' )  . $bridal_dresses_i,
			'id' => 'bridal-dresses-footer-widget-' . $bridal_dresses_i,
			'description' => __( 'The Footer Widget Area', 'bridal-dresses' )  . $bridal_dresses_i,
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<div class="widget-header"><h4 class="widget-title">',
			'after_title' => '</h4></div>',
		) );
	}
}
add_action( 'widgets_init', 'bridal_dresses_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function bridal_dresses_scripts() {
	// Append .min if SCRIPT_DEBUG is false.
	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Slick style.
	wp_enqueue_style( 'slick-style', get_template_directory_uri() . '/resource/css/slick' . $min . '.css', array(), '1.8.1' );

	// Owl Carousel style.
	wp_enqueue_style( 'owl-carousel-css', get_template_directory_uri() . '/resource/css/owl.carousel' . '.css', array(), '2.3.4' );

	// Fontawesome style.
	wp_enqueue_style( 'fontawesome-style', get_template_directory_uri() . '/resource/css/fontawesome' . $min . '.css', array(), '5.15.4' );

	// Main style.
	wp_enqueue_style( 'bridal-dresses-style', get_template_directory_uri() . '/style.css', array(), BRIDAL_DRESSES_VERSION );

	// RTL style.
	wp_style_add_data('bridal-dresses-style', 'rtl', 'replace');

	// Navigation script.
	wp_enqueue_script( 'bridal-dresses-navigation-script', get_template_directory_uri() . '/resource/js/navigation' . $min . '.js', array(), BRIDAL_DRESSES_VERSION, true );

	// Owl Carousel.
	wp_enqueue_script( 'owl-carouselscript', get_template_directory_uri() . '/resource/js/owl.carousel' . '.js', array( 'jquery' ), '2.3.4', true );

	// Slick script.
	wp_enqueue_script( 'slick-script', get_template_directory_uri() . '/resource/js/slick' . $min . '.js', array( 'jquery' ), '1.8.1', true );

	// Custom script.
	wp_enqueue_script( 'bridal-dresses-custom-script', get_template_directory_uri() . '/resource/js/custom.js', array( 'jquery' ), BRIDAL_DRESSES_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Include the file.
	require_once get_theme_file_path( 'theme-library/function-files/wptt-webfont-loader.php' );

	// Load the webfont.
	wp_enqueue_style(
		'Playfair Display',
		Bridal_Dresses_wptt_get_webfont_url( 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap' ),
		array(),
		'1.0'
	);

	// Load the webfont.
	wp_enqueue_style(
		'Open Sans',
		Bridal_Dresses_wptt_get_webfont_url( 'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap' ),
		array(),
		'1.0'
	);

}
add_action( 'wp_enqueue_scripts', 'bridal_dresses_scripts' );

//Change number of products per page 
add_filter( 'loop_shop_per_page', 'bridal_dresses_products_per_page' );
function bridal_dresses_products_per_page( $cols ) {
  	return  get_theme_mod( 'bridal_dresses_products_per_page',9);
}

// Change number or products per row 
add_filter('loop_shop_columns', 'bridal_dresses_loop_columns');
	if (!function_exists('bridal_dresses_loop_columns')) {
	function bridal_dresses_loop_columns() {
		return get_theme_mod( 'bridal_dresses_products_per_row', 3 );
	}
}

/**
 * Include wptt webfont loader.
 */
require_once get_theme_file_path( 'theme-library/function-files/wptt-webfont-loader.php' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/theme-library/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/theme-library/function-files/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/theme-library/function-files/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/theme-library/customizer.php';

/**
 * Google Fonts
 */
require get_template_directory() . '/theme-library/function-files/google-fonts.php';

/**
 * Dynamic CSS
 */
require get_template_directory() . '/theme-library/dynamic-css.php';

/**
 * Breadcrumb
 */
require get_template_directory() . '/theme-library/function-files/class-breadcrumb-trail.php';

/**
 * Customizer Settings Functions
*/
require get_template_directory() . '/theme-library/function-files/customizer-settings-functions.php';

/**
 * Load TGM.
 */
require get_template_directory() . '/theme-library/customizer/tgm/tgm.php';

// Enqueue Customizer live preview script
function bridal_dresses_customizer_live_preview() {
    wp_enqueue_script(
        'bridal-dresses-customizer',
        get_template_directory_uri() . '/js/customizer.js',
        array('jquery', 'customize-preview'),
        '',
        true
    );
}
add_action('customize_preview_init', 'bridal_dresses_customizer_live_preview');

// Featured Image Dimension
function bridal_dresses_blog_post_featured_image_dimension(){
	if(get_theme_mod('bridal_dresses_blog_post_featured_image_dimension') == 'custom' ) {
		return true;
	}
	return false;
}