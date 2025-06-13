<?php
/**
 * Render homepage sections.
 */
function bridal_dresses_homepage_sections() {
	$bridal_dresses_homepage_sections = array_keys( bridal_dresses_get_homepage_sections() );

	foreach ( $bridal_dresses_homepage_sections as $bridal_dresses_section ) {
		require get_template_directory() . '/sections/' . $bridal_dresses_section . '.php';
	}
}