<?php
/**
 * Active Callbacks
 *
 * @package bridal_dresses
 */

// Theme Options.
function bridal_dresses_is_pagination_enabled( $bridal_dresses_control ) {
	return ( $bridal_dresses_control->manager->get_setting( 'bridal_dresses_enable_pagination' )->value() );
}
function bridal_dresses_is_breadcrumb_enabled( $bridal_dresses_control ) {
	return ( $bridal_dresses_control->manager->get_setting( 'bridal_dresses_enable_breadcrumb' )->value() );
}
function bridal_dresses_is_layout_enabled( $bridal_dresses_control ) {
	return ( $bridal_dresses_control->manager->get_setting( 'bridal_dresses_website_layout' )->value() );
}
function bridal_dresses_is_pagetitle_bcakground_image_enabled( $bridal_dresses_control ) {
	return ( $bridal_dresses_control->manager->get_setting( 'bridal_dresses_page_header_style' )->value() );
}
function bridal_dresses_is_preloader_style( $bridal_dresses_control ) {
	return ( $bridal_dresses_control->manager->get_setting( 'bridal_dresses_enable_preloader' )->value() );
}

// Header Options.
function bridal_dresses_is_topbar_enabled( $bridal_dresses_control ) {
	return ( $bridal_dresses_control->manager->get_Setting( 'bridal_dresses_enable_topbar' )->value() );
}

// Banner Slider Section.
function bridal_dresses_is_banner_slider_section_enabled( $bridal_dresses_control ) {
	return ( $bridal_dresses_control->manager->get_setting( 'bridal_dresses_enable_banner_section' )->value() );
}
function bridal_dresses_is_banner_slider_section_and_content_type_post_enabled( $bridal_dresses_control ) {
	$bridal_dresses_content_type = $bridal_dresses_control->manager->get_setting( 'bridal_dresses_banner_slider_content_type' )->value();
	return ( bridal_dresses_is_banner_slider_section_enabled( $bridal_dresses_control ) && ( 'post' === $bridal_dresses_content_type ) );
}
function bridal_dresses_is_banner_slider_section_and_content_type_page_enabled( $bridal_dresses_control ) {
	$bridal_dresses_content_type = $bridal_dresses_control->manager->get_setting( 'bridal_dresses_banner_slider_content_type' )->value();
	return ( bridal_dresses_is_banner_slider_section_enabled( $bridal_dresses_control ) && ( 'page' === $bridal_dresses_content_type ) );
}

// Service section.
function bridal_dresses_is_post_tab_section_and_content_type_page_enabled( $bridal_dresses_control ) {
	$bridal_dresses_content_type = $bridal_dresses_control->manager->get_setting( 'bridal_dresses_banner_slider_content_type' )->value();
	return ( bridal_dresses_is_banner_slider_section_enabled( $bridal_dresses_control ) && ( 'page' === $bridal_dresses_content_type ) );
}
function bridal_dresses_is_post_tab_section_and_content_type_post_enabled( $bridal_dresses_control ) {
	$bridal_dresses_content_type = $bridal_dresses_control->manager->get_setting( 'bridal_dresses_banner_slider_content_type' )->value();
	return ( bridal_dresses_is_banner_slider_section_enabled( $bridal_dresses_control ) && ( 'post' === $bridal_dresses_content_type ) );
}
function bridal_dresses_is_service_section_enabled( $bridal_dresses_control ) {
	return ( $bridal_dresses_control->manager->get_setting( 'bridal_dresses_enable_service_section' )->value() );
}
function bridal_dresses_is_service_section_and_content_type_post_enabled( $bridal_dresses_control ) {
	$bridal_dresses_content_type = $bridal_dresses_control->manager->get_setting( 'bridal_dresses_service_content_type' )->value();
	return ( bridal_dresses_is_service_section_enabled( $bridal_dresses_control ) && ( 'post' === $bridal_dresses_content_type ) );
}
function bridal_dresses_is_service_section_and_content_type_page_enabled( $bridal_dresses_control ) {
	$bridal_dresses_content_type = $bridal_dresses_control->manager->get_setting( 'bridal_dresses_service_content_type' )->value();
	return ( bridal_dresses_is_service_section_enabled( $bridal_dresses_control ) && ( 'page' === $bridal_dresses_content_type ) );
}