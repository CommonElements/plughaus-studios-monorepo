<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! bridal_dresses_has_page_header() ) {
    return;
}

$bridal_dresses_classes = array( 'page-header' );
$bridal_dresses_style = bridal_dresses_page_header_style();

if ( $bridal_dresses_style ) {
    $bridal_dresses_classes[] = $bridal_dresses_style . '-page-header';
}

$bridal_dresses_visibility = get_theme_mod( 'bridal_dresses_page_header_visibility', 'all-devices' );

if ( 'hide-all-devices' === $bridal_dresses_visibility ) {
    // Don't show the header at all
    return;
}

if ( 'hide-tablet' === $bridal_dresses_visibility ) {
    $bridal_dresses_classes[] = 'hide-on-tablet';
} elseif ( 'hide-mobile' === $bridal_dresses_visibility ) {
    $bridal_dresses_classes[] = 'hide-on-mobile';
} elseif ( 'hide-tablet-mobile' === $bridal_dresses_visibility ) {
    $bridal_dresses_classes[] = 'hide-on-tablet-mobile';
}

$bridal_dresses_PAGE_TITLE_background_color = get_theme_mod('bridal_dresses_page_title_background_color_setting', '');

// Get the toggle switch value
$bridal_dresses_background_image_enabled = get_theme_mod('bridal_dresses_page_header_style', true);

// Add background image to the header if enabled
$bridal_dresses_background_image = get_theme_mod( 'bridal_dresses_page_header_background_image', '' );
$bridal_dresses_background_height = get_theme_mod( 'bridal_dresses_page_header_image_height', '200' );
$bridal_dresses_inline_style = '';

if ( $bridal_dresses_background_image_enabled && ! empty( $bridal_dresses_background_image ) ) {
    $bridal_dresses_inline_style .= 'background-image: url(' . esc_url( $bridal_dresses_background_image ) . '); ';
    $bridal_dresses_inline_style .= 'height: ' . esc_attr( $bridal_dresses_background_height ) . 'px; ';
    $bridal_dresses_inline_style .= 'background-size: cover; ';
    $bridal_dresses_inline_style .= 'background-position: center center; ';

    // Add the unique class if the background image is set
    $bridal_dresses_classes[] = 'has-background-image';
}

$bridal_dresses_classes = implode( ' ', $bridal_dresses_classes );
$bridal_dresses_heading = get_theme_mod( 'bridal_dresses_page_header_heading_tag', 'h1' );
$bridal_dresses_heading = apply_filters( 'bridal_dresses_page_header_heading', $bridal_dresses_heading );

?>

<?php do_action( 'bridal_dresses_before_page_header' ); ?>

<header class="<?php echo esc_attr( $bridal_dresses_classes ); ?>" style="<?php echo esc_attr( $bridal_dresses_inline_style ); ?> background-color: <?php echo esc_attr($bridal_dresses_PAGE_TITLE_background_color); ?>;">

    <?php do_action( 'bridal_dresses_before_page_header_inner' ); ?>

    <div class="asterthemes-wrapper page-header-inner">

        <?php if ( bridal_dresses_has_page_header() ) : ?>

            <<?php echo esc_attr( $bridal_dresses_heading ); ?> class="page-header-title">
                <?php echo wp_kses_post( bridal_dresses_get_page_title() ); ?>
            </<?php echo esc_attr( $bridal_dresses_heading ); ?>>

        <?php endif; ?>

        <?php if ( function_exists( 'bridal_dresses_breadcrumb' ) ) : ?>
            <?php bridal_dresses_breadcrumb(); ?>
        <?php endif; ?>

    </div><!-- .page-header-inner -->

    <?php do_action( 'bridal_dresses_after_page_header_inner' ); ?>

</header><!-- .page-header -->

<?php do_action( 'bridal_dresses_after_page_header' ); ?>