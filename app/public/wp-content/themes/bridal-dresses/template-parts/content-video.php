<?php
/**
 * Template part for displaying Video Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bridal_dresses
 */

?>
<?php $bridal_dresses_readmore = get_theme_mod( 'bridal_dresses_readmore_button_text','Read More');?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="mag-post-single">
        <?php
			// Get the post ID
			$bridal_dresses_post_id = get_the_ID();

			// Check if there are videos embedded in the post content
			$bridal_dresses_post = get_post($bridal_dresses_post_id);
			$bridal_dresses_content = do_shortcode(apply_filters('the_content', $bridal_dresses_post->post_content));
			$bridal_dresses_embeds = get_media_embedded_in_content($bridal_dresses_content);

			if (!empty($bridal_dresses_embeds)) {
			    // Loop through embedded media and display videos
			    foreach ($bridal_dresses_embeds as $bridal_dresses_embed) {
			        // Check if the embed code contains a video tag or specific video providers like YouTube or Vimeo
			        if (strpos($bridal_dresses_embed, 'video') !== false || strpos($bridal_dresses_embed, 'youtube') !== false || strpos($bridal_dresses_embed, 'vimeo') !== false || strpos($bridal_dresses_embed, 'dailymotion') !== false || strpos($bridal_dresses_embed, 'vine') !== false || strpos($bridal_dresses_embed, 'wordPress.tv') !== false || strpos($bridal_dresses_embed, 'hulu') !== false) {
			            ?>
			            <div class="custom-embedded-video">
			                <div class="video-container">
			                    <?php echo esc_url($bridal_dresses_embed); ?>
			                </div>
			                <div class="video-comments">
			                    <?php
			                    // Add your comments section here
			                    comments_template(); // This will include the default WordPress comments template
			                    ?>
			                </div>
			            </div>
			            <?php
			        }
			    }
			}
	    ?>
		<div class="mag-post-detail">
			<div class="mag-post-category">
				<?php bridal_dresses_categories_list(); ?>
			</div>
			<?php
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title mag-post-title">', '</h1>' );
			else :
				if ( get_theme_mod( 'bridal_dresses_post_hide_post_heading', true ) ) { 
					the_title( '<h2 class="entry-title mag-post-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			    }
			endif;
			?>
			<div class="mag-post-meta">
				<?php
				bridal_dresses_posted_on();
				bridal_dresses_posted_by();
				bridal_dresses_posted_comments();
				bridal_dresses_posted_time();
				?>
			</div>
			<?php if ( get_theme_mod( 'bridal_dresses_post_hide_post_content', true ) ) { ?>
				<div class="mag-post-excerpt">
					<?php the_excerpt(); ?>
				</div>
		    <?php } ?>
			<?php if ( get_theme_mod( 'bridal_dresses_post_readmore_button', true ) === true ) : ?>
				<div class="mag-post-read-more">
					<a href="<?php the_permalink(); ?>" class="read-more-button">
						<?php if ( ! empty( $bridal_dresses_readmore ) ) { ?> <?php echo esc_html( $bridal_dresses_readmore ); ?> <?php } ?>
						<i class="<?php echo esc_attr( get_theme_mod( 'bridal_dresses_readmore_btn_icon', 'fas fa-chevron-right' ) ); ?>"></i>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>

</article><!-- #post-<?php the_ID(); ?> -->