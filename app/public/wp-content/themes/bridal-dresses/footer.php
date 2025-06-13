<?php
/**
 * The template for displaying the footer
 * 
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package bridal_dresses
 */

// Get the footer background color/image settings from customizer
$bridal_dresses_footer_background_color = get_theme_mod('footer_background_color_setting', ''); 
$bridal_dresses_footer_background_image = get_theme_mod('footer_background_image_setting');
$bridal_dresses_footer_background_attachment = get_theme_mod('bridal_dresses_footer_image_attachment_setting', 'scroll');

if (!is_front_page() || is_home()) {
    ?>
    </div>
    </div>
</div>
<?php } ?>

<footer id="colophon" class="site-footer">
    <?php if (get_theme_mod('bridal_dresses_enable_footer_section', true)) { ?>
        <div class="site-footer-top" style="background-color: <?php echo esc_attr($bridal_dresses_footer_background_color); ?>; <?php echo ($bridal_dresses_footer_background_image) ? 'background-image: url(' . esc_url($bridal_dresses_footer_background_image) . ');' : ''; ?> background-attachment: <?php echo esc_attr($bridal_dresses_footer_background_attachment); ?>;">
            <div class="asterthemes-wrapper">
                <div class="footer-widgets-wrapper">

                    <?php
                    // Footer Widget
                    do_action('bridal_dresses_footer_widget');
                    ?>

                </div>
            </div>
        </div>
    <?php } ?>
    <div class="site-footer-bottom">
        <div class="asterthemes-wrapper">
            <div class="site-footer-bottom-wrapper">
                <div class="site-info">
                    <?php
                    do_action('bridal_dresses_footer_copyright');
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php
$bridal_dresses_is_scroll_top_active = get_theme_mod( 'bridal_dresses_scroll_top', true );
if ( $bridal_dresses_is_scroll_top_active ) :
    $bridal_dresses_scroll_position = get_theme_mod( 'bridal_dresses_scroll_top_position', 'bottom-right' );
    $bridal_dresses_scroll_shape = get_theme_mod( 'bridal_dresses_scroll_top_shape', 'box' );
    ?>
    <style>
        #scroll-to-top {
            position: fixed;
            <?php
            switch ( $bridal_dresses_scroll_position ) {
                case 'bottom-left':
                    echo 'bottom: 25px; left: 20px;';
                    break;
                case 'bottom-center':
                    echo 'bottom: 25px; left: 50%; transform: translateX(-50%);';
                    break;
                default: // 'bottom-right'
                    echo 'bottom: 25px; right: 90px;';
            }
            ?>
        }
    </style>
    <a href="#" id="scroll-to-top" class="bridal-dresses-scroll-to-top <?php echo esc_attr($bridal_dresses_scroll_shape); ?>"><i class="<?php echo esc_attr( get_theme_mod( 'bridal_dresses_scroll_btn_icon', 'fas fa-chevron-up' ) ); ?>"></i></a>
<?php
endif;
?>
</div>

<?php wp_footer(); ?>

</body>

</html>