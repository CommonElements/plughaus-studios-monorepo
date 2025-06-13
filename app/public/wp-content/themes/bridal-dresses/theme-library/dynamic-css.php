<?php
/**
 * Dynamic CSS
 */
function bridal_dresses_dynamic_css() {
	$bridal_dresses_primary_color = get_theme_mod( 'primary_color', '#8E4162' );

	$bridal_dresses_site_title_font       = get_theme_mod( 'bridal_dresses_site_title_font', 'Playfair Display' );
	$bridal_dresses_site_description_font = get_theme_mod( 'bridal_dresses_site_description_font', 'Open Sans' );
	$bridal_dresses_header_font           = get_theme_mod( 'bridal_dresses_header_font', 'Playfair Display' );
	$bridal_dresses_content_font          = get_theme_mod( 'bridal_dresses_content_font', 'Open Sans' );

	// Enqueue Google Fonts
	$bridal_dresses_fonts_url = bridal_dresses_get_fonts_url();
	if ( ! empty( $bridal_dresses_fonts_url ) ) {
		wp_enqueue_style( 'bridal-dresses-google-fonts', esc_url( $bridal_dresses_fonts_url ), array(), null );
	}

	$bridal_dresses_custom_css  = '';
	$bridal_dresses_custom_css .= '
    /* Color */
    :root {
        --primary-color: ' . esc_attr( $bridal_dresses_primary_color ) . ';
        --header-text-color: ' . esc_attr( '#' . get_header_textcolor() ) . ';
    }
    ';

	$bridal_dresses_custom_css .= '
    /* Typography */
    :root {
        --font-heading: "' . esc_attr( $bridal_dresses_header_font ) . '", serif;
        --font-main: -apple-system, BlinkMacSystemFont, "' . esc_attr( $bridal_dresses_content_font ) . '", "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    }

    body,
	button, input, select, optgroup, textarea, p {
        font-family: "' . esc_attr( $bridal_dresses_content_font ) . '", serif;
	}

	.site-identity p.site-title, h1.site-title a, h1.site-title, p.site-title a, .site-branding h1.site-title a {
        font-family: "' . esc_attr( $bridal_dresses_site_title_font ) . '", serif;
	}
    
	p.site-description {
        font-family: "' . esc_attr( $bridal_dresses_site_description_font ) . '", serif !important;
	}
    ';

	wp_add_inline_style( 'bridal-dresses-style', $bridal_dresses_custom_css );
}
add_action( 'wp_enqueue_scripts', 'bridal_dresses_dynamic_css', 99 );