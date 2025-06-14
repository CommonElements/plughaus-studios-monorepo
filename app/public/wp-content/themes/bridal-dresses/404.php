<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package bridal_dresses
 */

get_header();
$bridal_dresses_pg_404_ttl     = get_theme_mod( 'bridal_dresses_pg_404_ttl', __( '404 Page Not Found', 'bridal-dresses' ) );
$bridal_dresses_pg_404_text    = get_theme_mod( 'bridal_dresses_pg_404_text', __( 'Apologies, but the page you are seeking cannot be found.', 'bridal-dresses' ) );
$bridal_dresses_pg_404_btn_lbl = get_theme_mod( 'bridal_dresses_pg_404_btn_lbl', __( 'Go Back Home', 'bridal-dresses' ) );

$bridal_dresses_pg_404_btn_link   = get_theme_mod('bridal_dresses_pg_404_btn_link',esc_url( home_url( '/' )));
$bridal_dresses_pg_404_image      = get_theme_mod('bridal_dresses_pg_404_image','');
?>
<section class="not-found">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-12">
                <?php if (!empty($bridal_dresses_pg_404_image)) : ?>
                    <img src="<?php echo esc_url($bridal_dresses_pg_404_image); ?>" alt="<?php echo esc_attr($bridal_dresses_pg_404_ttl); ?>" class="img-fluid">
                <?php else : ?>
                    <?php if (!empty($bridal_dresses_pg_404_ttl)) : ?> 
                        <h2 class="text-secondary"><?php echo wp_kses_post($bridal_dresses_pg_404_ttl); ?></h2>
                    <?php endif; ?> 
                
                    <?php if (!empty($bridal_dresses_pg_404_text)) : ?>    
                        <p class="not-para"><?php echo wp_kses_post($bridal_dresses_pg_404_text); ?></p>
                    <?php endif; ?> 
                <?php endif; ?>
                
                <?php if (!empty($bridal_dresses_pg_404_btn_lbl)) : ?> 
                    <div class="paganot-found-button">
                        <a href="<?php echo esc_url($bridal_dresses_pg_404_btn_link); ?>" class="dt-btn dt-btn-primary" data-title="<?php echo esc_attr($bridal_dresses_pg_404_btn_lbl); ?>"><?php echo wp_kses_post($bridal_dresses_pg_404_btn_lbl); ?></a>
                    </div>    
                <?php endif; ?> 
            </div>
        </div>
    </div>
</section>


<?php 
get_sidebar();
get_footer(); ?>