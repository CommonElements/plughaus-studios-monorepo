<?php
function bridal_dresses_sanitize_select( $bridal_dresses_input, $bridal_dresses_setting ) {
	$bridal_dresses_input = sanitize_key( $bridal_dresses_input );
	$bridal_dresses_choices = $bridal_dresses_setting->manager->get_control( $bridal_dresses_setting->id )->choices;
	return ( array_key_exists( $bridal_dresses_input, $bridal_dresses_choices ) ? $bridal_dresses_input : $bridal_dresses_setting->default );
}

function bridal_dresses_sanitize_switch( $bridal_dresses_input ) {
	if ( true === $bridal_dresses_input ) {
		return true;
	} else {
		return false;
	}
}

function bridal_dresses_sanitize_google_fonts( $bridal_dresses_input, $bridal_dresses_setting ) {
	$bridal_dresses_choices = $bridal_dresses_setting->manager->get_control( $bridal_dresses_setting->id )->choices;
	return ( array_key_exists( $bridal_dresses_input, $bridal_dresses_choices ) ? $bridal_dresses_input : $bridal_dresses_setting->default );
}
/**
 * Sanitize HTML input.
 *
 * @param string $bridal_dresses_input HTML input to sanitize.
 * @return string Sanitized HTML.
 */
function bridal_dresses_sanitize_html( $bridal_dresses_input ) {
    return wp_kses_post( $bridal_dresses_input );
}

/**
 * Sanitize URL input.
 *
 * @param string $bridal_dresses_input URL input to sanitize.
 * @return string Sanitized URL.
 */
function bridal_dresses_sanitize_url( $bridal_dresses_input ) {
    return esc_url_raw( $bridal_dresses_input );
}

// Sanitize Scroll Top Position
function bridal_dresses_sanitize_scroll_top_position( $bridal_dresses_input ) {
    $valid_positions = array( 'bottom-right', 'bottom-left', 'bottom-center' );
    if ( in_array( $bridal_dresses_input, $valid_positions ) ) {
        return $bridal_dresses_input;
    } else {
        return 'bottom-right'; // Default to bottom-right if invalid value
    }
}

function bridal_dresses_sanitize_choices( $bridal_dresses_input, $bridal_dresses_setting ) {
	global $wp_customize; 
	$control = $wp_customize->get_control( $bridal_dresses_setting->id ); 
	if ( array_key_exists( $bridal_dresses_input, $control->choices ) ) {
		return $bridal_dresses_input;
	} else {
		return $bridal_dresses_setting->default;
	}
}

function bridal_dresses_sanitize_range_value( $bridal_dresses_number, $bridal_dresses_setting ) {

	// Ensure input is an absolute integer.
	$bridal_dresses_number = absint( $bridal_dresses_number );

	// Get the input attributes associated with the setting.
	$bridal_dresses_atts = $bridal_dresses_setting->manager->get_control( $bridal_dresses_setting->id )->input_attrs;

	// Get minimum number in the range.
	$bridal_dresses_min = ( isset( $bridal_dresses_atts['min'] ) ? $bridal_dresses_atts['min'] : $bridal_dresses_number );

	// Get maximum number in the range.
	$bridal_dresses_max = ( isset( $bridal_dresses_atts['max'] ) ? $bridal_dresses_atts['max'] : $bridal_dresses_number );

	// Get step.
	$bridal_dresses_step = ( isset( $bridal_dresses_atts['step'] ) ? $bridal_dresses_atts['step'] : 1 );

	// If the number is within the valid range, return it; otherwise, return the default.
	return ( $bridal_dresses_min <= $bridal_dresses_number && $bridal_dresses_number <= $bridal_dresses_max && is_int( $bridal_dresses_number / $bridal_dresses_step ) ? $bridal_dresses_number : $bridal_dresses_setting->default );
}