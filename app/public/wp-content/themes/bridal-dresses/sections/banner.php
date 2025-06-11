<?php
if ( ! get_theme_mod( 'bridal_dresses_enable_banner_section', false ) ) {
	return;
}

$bridal_dresses_slider_content_ids  = array();
$bridal_dresses_slider_content_type = get_theme_mod( 'bridal_dresses_banner_slider_content_type', 'post' );

for ( $bridal_dresses_i = 1; $bridal_dresses_i <= 3; $bridal_dresses_i++ ) {
	$bridal_dresses_slider_content_ids[] = get_theme_mod( 'bridal_dresses_banner_slider_content_' . $bridal_dresses_slider_content_type . '_' . $bridal_dresses_i );
}
// Get the category for the banner slider from theme mods or a default category
$bridal_dresses_banner_slider_category = get_theme_mod('bridal_dresses_banner_slider_category', 'slider');

// Modify query to fetch posts from a specific category
$bridal_dresses_banner_slider_args = array(
	'post_type'           => $bridal_dresses_slider_content_type,
	'post__in'            => array_filter( $bridal_dresses_slider_content_ids ),
	'orderby'             => 'post__in',
	'posts_per_page'      => absint(3),
	'ignore_sticky_posts' => true,
);

// Apply category filter only if content type is 'post'
if ( 'post' === $bridal_dresses_slider_content_type && ! empty( $bridal_dresses_banner_slider_category ) ) {
	$bridal_dresses_banner_slider_args['category_name'] = $bridal_dresses_banner_slider_category;
}
$bridal_dresses_banner_slider_args = apply_filters( 'bridal_dresses_banner_section_args', $bridal_dresses_banner_slider_args );

bridal_dresses_render_banner_section( $bridal_dresses_banner_slider_args );

/**
 * Render Banner Section.
 */

function bridal_dresses_render_banner_section( $bridal_dresses_banner_slider_args ) {     ?>

	<section id="bridal_dresses_banner_section" class="banner-section banner-style-1">
		<?php
		if ( is_customize_preview() ) :
			bridal_dresses_section_link( 'bridal_dresses_banner_section' );
		endif;
		?>
		<div class="banner-section-wrapper">
			<?php
			$bridal_dresses_query = new WP_Query( $bridal_dresses_banner_slider_args );
			if ( $bridal_dresses_query->have_posts() ) :
				?>
				<div class="asterthemes-banner-wrapper banner-slider bridal-dresses-carousel-navigation" data-slick='{"autoplay": false }'>
					<?php
					$bridal_dresses_i = 1;
					while ( $bridal_dresses_query->have_posts() ) :
						$bridal_dresses_query->the_post();
						$bridal_dresses_button_label = get_theme_mod( 'bridal_dresses_banner_button_label_' . $bridal_dresses_i);
						$bridal_dresses_button_link  = get_theme_mod( 'bridal_dresses_banner_button_link_' . $bridal_dresses_i);
						$bridal_dresses_button_link  = ! empty( $bridal_dresses_button_link ) ? $bridal_dresses_button_link : esc_url(get_the_permalink());
						?>
						<div class="banner-single-outer">
							<div class="banner-single">
								<div class="banner-img">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'full' ); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/resource/img/default.png" />
                                    <?php endif; ?>
                                </div>
								<div class="banner-caption">
									<div class="asterthemes-wrapper">
										<div class="banner-catption-wrapper">
											<h2 class="banner-caption-title">
												<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
											</h2>
											<?php if ( ! empty( $bridal_dresses_button_label ) ) { ?>
												<div class="banner-slider-btn">
													<a href="<?php echo esc_url( $bridal_dresses_button_link ); ?>" class="asterthemes-button"><?php echo esc_html( $bridal_dresses_button_label ); ?></a>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
						$bridal_dresses_i++;
					endwhile;
					wp_reset_postdata();
					?>
				</div>
				<?php
			endif;
			?>
		</div>
	</section>

	<?php
}
