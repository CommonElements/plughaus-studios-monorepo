<?php
function bridal_dresses_get_all_google_fonts() {
    $bridal_dresses_webfonts_json = get_template_directory() . '/theme-library/google-webfonts.json';
    if ( ! file_exists( $bridal_dresses_webfonts_json ) ) {
        return array();
    }

    $bridal_dresses_fonts_json_data = file_get_contents( $bridal_dresses_webfonts_json );
    if ( false === $bridal_dresses_fonts_json_data ) {
        return array();
    }

    $bridal_dresses_all_fonts = json_decode( $bridal_dresses_fonts_json_data, true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return array();
    }

    $bridal_dresses_google_fonts = array();
    foreach ( $bridal_dresses_all_fonts as $bridal_dresses_font ) {
        $bridal_dresses_google_fonts[ $bridal_dresses_font['family'] ] = array(
            'family'   => $bridal_dresses_font['family'],
            'variants' => $bridal_dresses_font['variants'],
        );
    }
    return $bridal_dresses_google_fonts;
}


function bridal_dresses_get_all_google_font_families() {
    $bridal_dresses_google_fonts  = bridal_dresses_get_all_google_fonts();
    $bridal_dresses_font_families = array();
    foreach ( $bridal_dresses_google_fonts as $bridal_dresses_font ) {
        $bridal_dresses_font_families[ $bridal_dresses_font['family'] ] = $bridal_dresses_font['family'];
    }
    return $bridal_dresses_font_families;
}

function bridal_dresses_get_fonts_url() {
    $bridal_dresses_fonts_url = '';
    $bridal_dresses_fonts     = array();

    $bridal_dresses_all_fonts = bridal_dresses_get_all_google_fonts();

    if ( ! empty( get_theme_mod( 'bridal_dresses_site_title_font', 'Playfair Display' ) ) ) {
        $bridal_dresses_fonts[] = get_theme_mod( 'bridal_dresses_site_title_font', 'Playfair Display' );
    }

    if ( ! empty( get_theme_mod( 'bridal_dresses_site_description_font', 'Open Sans' ) ) ) {
        $bridal_dresses_fonts[] = get_theme_mod( 'bridal_dresses_site_description_font', 'Open Sans' );
    }

    if ( ! empty( get_theme_mod( 'bridal_dresses_header_font', 'Playfair Display' ) ) ) {
        $bridal_dresses_fonts[] = get_theme_mod( 'bridal_dresses_header_font', 'Playfair Display' );
    }

    if ( ! empty( get_theme_mod( 'bridal_dresses_content_font', 'Open Sans' ) ) ) {
        $bridal_dresses_fonts[] = get_theme_mod( 'bridal_dresses_content_font', 'Open Sans' );
    }

    $bridal_dresses_fonts = array_unique( $bridal_dresses_fonts );

    foreach ( $bridal_dresses_fonts as $bridal_dresses_font ) {
        $bridal_dresses_variants      = $bridal_dresses_all_fonts[ $bridal_dresses_font ]['variants'];
        $bridal_dresses_font_family[] = $bridal_dresses_font . ':' . implode( ',', $bridal_dresses_variants );
    }

    $bridal_dresses_query_args = array(
        'family' => urlencode( implode( '|', $bridal_dresses_font_family ) ),
    );

    if ( ! empty( $bridal_dresses_font_family ) ) {
        $bridal_dresses_fonts_url = add_query_arg( $bridal_dresses_query_args, 'https://fonts.googleapis.com/css' );
    }

    return $bridal_dresses_fonts_url;
}