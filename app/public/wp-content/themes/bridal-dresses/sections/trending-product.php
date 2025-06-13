<?php
if ( ! get_theme_mod( 'bridal_dresses_enable_service_section', false ) ) {
	return;
}

$bridal_dresses_args = '';

bridal_dresses_render_service_section( $bridal_dresses_args );

/**
 * Render Service Section.
 */
function bridal_dresses_render_service_section( $bridal_dresses_args ) { ?>
	<section id="bridal_dresses_trending_section" class="asterthemes-frontpage-section trending-section trending-style-1">
		<?php
			if ( is_customize_preview() ) :
				bridal_dresses_section_link( 'bridal_dresses_service_section' );
			endif;
			$bridal_dresses_trending_product_heading = get_theme_mod( 'bridal_dresses_trending_product_heading', '' );
			?>
			<?php if ( ! empty( $bridal_dresses_trending_product_heading ) ) { ?>
				<div class="product-contact-inner">
					<h3><?php echo esc_html( $bridal_dresses_trending_product_heading ); ?></h3>
				</div>
			<?php } ?>
			<?php 
			if ( class_exists( 'WooCommerce' ) ) { ?>
				<div class="tab">
					<?php $bridal_dresses_featured_post = get_theme_mod('bridal_dresses_services_number', '');
						for ( $bridal_dresses_j = 1; $bridal_dresses_j <= $bridal_dresses_featured_post; $bridal_dresses_j++ ){ ?>
						<button class="tablinks" onclick="bridal_dresses_services_tab( event , '<?php $bridal_dresses_main_id = get_theme_mod('bridal_dresses_services_text'.$bridal_dresses_j); $bridal_dresses_tab_id = str_replace(' ', '-', $bridal_dresses_main_id); echo $bridal_dresses_tab_id; ?> ')">
						<?php echo esc_html(get_theme_mod('bridal_dresses_services_text'.$bridal_dresses_j)); ?></button>
					<?php }?>
				</div>
				<?php for ( $bridal_dresses_j = 1; $bridal_dresses_j <= $bridal_dresses_featured_post; $bridal_dresses_j++ ){ 
					$bridal_dresses_catData = get_theme_mod('bridal_dresses_trending_product_category'.$bridal_dresses_j,'');
					?>
					<div id="<?php $bridal_dresses_main_id = get_theme_mod('bridal_dresses_services_text'.$bridal_dresses_j); $bridal_dresses_tab_id = str_replace(' ', '-', $bridal_dresses_main_id); echo $bridal_dresses_tab_id; ?>"  class="tabcontent">
						<div class="services_main_box">
							<div class="owl-carousel">
								<?php  
									$bridal_dresses_args = array(
									'post_type' => 'product',
									'posts_per_page' => 100,
									'order' => 'ASC'
									);

									$bridal_dresses_args['tax_query'][] = array(
										'taxonomy' => 'product_cat',
										'field' => 'term_id',
										'terms' => array( $bridal_dresses_catData ),
										'operator' => 'IN',
									);
									?>

									<?php $bridal_dresses_loop = new WP_Query( $bridal_dresses_args );
										while ( $bridal_dresses_loop->have_posts() ) : $bridal_dresses_loop->the_post(); global $product; 
										$bridal_dresses_regular_price = $product->get_regular_price();
										$bridal_dresses_sale_price = $product->get_sale_price();
										$bridal_dresses_percentage_discount = 0;
										
										if ($bridal_dresses_regular_price && $bridal_dresses_sale_price) {
											$bridal_dresses_percentage_discount = round( 
												( ($bridal_dresses_regular_price - $bridal_dresses_sale_price) / $bridal_dresses_regular_price ) * 100 
											);
										}						
									?>
									<div class="tab-product">
										<figure>
											<?php
											if ( has_post_thumbnail( $bridal_dresses_loop->post->ID ) ) {
												echo get_the_post_thumbnail( $bridal_dresses_loop->post->ID, 'shop_catalog' );
											} else {
												echo '<img src="' . esc_url( wc_placeholder_img_src() ) . '" alt="Placeholder" />';
											}									
											// NEW Badge for products published within last 15 days
											$bridal_dresses_post_date    = get_the_date( 'U' );
											$bridal_dresses_current_time = current_time( 'timestamp' );
											$bridal_dresses_days_as_new  = 15;

											if ( ( $bridal_dresses_current_time - $bridal_dresses_post_date ) < ( $bridal_dresses_days_as_new * 24 * 60 * 60 ) ) {
												echo '<div class="new-badge">' . esc_html__( 'NEW', 'bridal-dresses' ) . '</div>';
											}

											if ( ! empty( $bridal_dresses_percentage_discount ) && $bridal_dresses_percentage_discount != 0 ) {
												echo '<div class="discount-badge">';
												echo esc_html( $bridal_dresses_percentage_discount ) . '% OFF';
												echo '</div>';
											}
											?>
											<div class="box-content intro-button">
												<?php if( $product->is_type( 'simple' ) ) { woocommerce_template_loop_add_to_cart(  $bridal_dresses_loop->post, $product );} ?>
											</div>
										</figure>
										<h5 class="product-text">
											<a href="<?php echo esc_url(get_permalink( $bridal_dresses_loop->post->ID )); ?>"><?php the_title(); ?></a>
										</h5>
										<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
											<?php echo $product->get_price_html(); ?>
										</p>
									</div>
								<?php endwhile; wp_reset_postdata(); ?>
							</div>
						</div>
					</div>
				<?php }?>
    	<?php } ?>
	</section>
	<?php
}
