<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package bridal_dresses
 */

function bridal_dresses_body_classes( $bridal_dresses_classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$bridal_dresses_classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$bridal_dresses_classes[] = 'no-sidebar';
	}

	$bridal_dresses_classes[] = bridal_dresses_sidebar_layout();

	return $bridal_dresses_classes;
}
add_filter( 'body_class', 'bridal_dresses_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function bridal_dresses_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'bridal_dresses_pingback_header' );


/**
 * Get all posts for customizer Post content type.
 */
function bridal_dresses_get_post_choices() {
	$bridal_dresses_choices = array( '' => esc_html__( '--Select--', 'bridal-dresses' ) );
	$bridal_dresses_args    = array( 'numberposts' => -1 );
	$bridal_dresses_posts   = get_posts( $bridal_dresses_args );

	foreach ( $bridal_dresses_posts as $bridal_dresses_post ) {
		$bridal_dresses_id             = $bridal_dresses_post->ID;
		$bridal_dresses_title          = $bridal_dresses_post->post_title;
		$bridal_dresses_choices[ $bridal_dresses_id ] = $bridal_dresses_title;
	}

	return $bridal_dresses_choices;
}

/**
 * Get all pages for customizer Page content type.
 */
function bridal_dresses_get_page_choices() {
	$bridal_dresses_choices = array( '' => esc_html__( '--Select--', 'bridal-dresses' ) );
	$bridal_dresses_pages   = get_pages();

	foreach ( $bridal_dresses_pages as $bridal_dresses_page ) {
		$bridal_dresses_choices[ $bridal_dresses_page->ID ] = $bridal_dresses_page->post_title;
	}

	return $bridal_dresses_choices;
}

/**
 * Get all categories for customizer Category content type.
 */
function bridal_dresses_get_post_cat_choices() {
	$bridal_dresses_choices = array( '' => esc_html__( '--Select--', 'bridal-dresses' ) );
	$bridal_dresses_cats    = get_categories();

	foreach ( $bridal_dresses_cats as $bridal_dresses_cat ) {
		$bridal_dresses_choices[ $bridal_dresses_cat->term_id ] = $bridal_dresses_cat->name;
	}

	return $bridal_dresses_choices;
}

/**
 * Get all donation forms for customizer form content type.
 */
function bridal_dresses_get_post_donation_form_choices() {
	$bridal_dresses_choices = array( '' => esc_html__( '--Select--', 'bridal-dresses' ) );
	$bridal_dresses_posts   = get_posts(
		array(
			'post_type'   => 'give_forms',
			'numberposts' => -1,
		)
	);
	foreach ( $bridal_dresses_posts as $bridal_dresses_post ) {
		$bridal_dresses_choices[ $bridal_dresses_post->ID ] = $bridal_dresses_post->post_title;
	}
	return $bridal_dresses_choices;
}

if ( ! function_exists( 'bridal_dresses_excerpt_length' ) ) :
	/**
	 * Excerpt length.
	 */
	function bridal_dresses_excerpt_length( $bridal_dresses_length ) {
		if ( is_admin() ) {
			return $bridal_dresses_length;
		}

		return get_theme_mod( 'bridal_dresses_excerpt_length', 20 );
	}
endif;
add_filter( 'excerpt_length', 'bridal_dresses_excerpt_length', 999 );

if ( ! function_exists( 'bridal_dresses_excerpt_more' ) ) :
	/**
	 * Excerpt more.
	 */
	function bridal_dresses_excerpt_more( $bridal_dresses_more ) {
		if ( is_admin() ) {
			return $bridal_dresses_more;
		}

		return '&hellip;';
	}
endif;
add_filter( 'excerpt_more', 'bridal_dresses_excerpt_more' );

if ( ! function_exists( 'bridal_dresses_sidebar_layout' ) ) {
	/**
	 * Get sidebar layout.
	 */
	function bridal_dresses_sidebar_layout() {
		$bridal_dresses_sidebar_position      = get_theme_mod( 'bridal_dresses_sidebar_position', 'right-sidebar' );
		$bridal_dresses_sidebar_position_post = get_theme_mod( 'bridal_dresses_post_sidebar_position', 'right-sidebar' );
		$bridal_dresses_sidebar_position_page = get_theme_mod( 'bridal_dresses_page_sidebar_position', 'right-sidebar' );

		if ( is_single() ) {
			$bridal_dresses_sidebar_position = $bridal_dresses_sidebar_position_post;
		} elseif ( is_page() ) {
			$bridal_dresses_sidebar_position = $bridal_dresses_sidebar_position_page;
		}

		return $bridal_dresses_sidebar_position;
	}
}

if ( ! function_exists( 'bridal_dresses_is_sidebar_enabled' ) ) {
	/**
	 * Check if sidebar is enabled.
	 */
	function bridal_dresses_is_sidebar_enabled() {
		$bridal_dresses_sidebar_position      = get_theme_mod( 'bridal_dresses_sidebar_position', 'right-sidebar' );
		$bridal_dresses_sidebar_position_post = get_theme_mod( 'bridal_dresses_post_sidebar_position', 'right-sidebar' );
		$bridal_dresses_sidebar_position_page = get_theme_mod( 'bridal_dresses_page_sidebar_position', 'right-sidebar' );

		$bridal_dresses_sidebar_enabled = true;
		if ( is_home() || is_archive() || is_search() ) {
			if ( 'no-sidebar' === $bridal_dresses_sidebar_position ) {
				$bridal_dresses_sidebar_enabled = false;
			}
		} elseif ( is_single() ) {
			if ( 'no-sidebar' === $bridal_dresses_sidebar_position || 'no-sidebar' === $bridal_dresses_sidebar_position_post ) {
				$bridal_dresses_sidebar_enabled = false;
			}
		} elseif ( is_page() ) {
			if ( 'no-sidebar' === $bridal_dresses_sidebar_position || 'no-sidebar' === $bridal_dresses_sidebar_position_page ) {
				$bridal_dresses_sidebar_enabled = false;
			}
		}
		return $bridal_dresses_sidebar_enabled;
	}
}

if ( ! function_exists( 'bridal_dresses_get_homepage_sections ' ) ) {
	/**
	 * Returns homepage sections.
	 */
	function bridal_dresses_get_homepage_sections() {
		$bridal_dresses_sections = array(
			'banner'  => esc_html__( 'Banner Section', 'bridal-dresses' ),
			'trending-product' => esc_html__( 'Product Section', 'bridal-dresses' ),
		);
		return $bridal_dresses_sections;
	}
}

/**
 * Renders customizer section link
 */
function bridal_dresses_section_link( $bridal_dresses_section_id ) {
	$bridal_dresses_section_name      = str_replace( 'bridal_dresses_', ' ', $bridal_dresses_section_id );
	$bridal_dresses_section_name      = str_replace( '_', ' ', $bridal_dresses_section_name );
	$bridal_dresses_starting_notation = '#';
	?>
	<span class="section-link">
		<span class="section-link-title"><?php echo esc_html( $bridal_dresses_section_name ); ?></span>
	</span>
	<style type="text/css">
		<?php echo $bridal_dresses_starting_notation . $bridal_dresses_section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>:hover .section-link {
			visibility: visible;
		}
	</style>
	<?php
}

/**
 * Adds customizer section link css
 */
function bridal_dresses_section_link_css() {
	if ( is_customize_preview() ) {
		?>
		<style type="text/css">
			.section-link {
				visibility: hidden;
				background-color: black;
				position: relative;
				top: 80px;
				z-index: 99;
				left: 40px;
				color: #fff;
				text-align: center;
				font-size: 20px;
				border-radius: 10px;
				padding: 20px 10px;
				text-transform: capitalize;
			}

			.section-link-title {
				padding: 0 10px;
			}

			.banner-section {
				position: relative;
			}

			.banner-section .section-link {
				position: absolute;
				top: 100px;
			}
		</style>
		<?php
	}
}
add_action( 'wp_head', 'bridal_dresses_section_link_css' );

/**
 * Breadcrumb.
 */
function bridal_dresses_breadcrumb( $bridal_dresses_args = array() ) {
	if ( ! get_theme_mod( 'bridal_dresses_enable_breadcrumb', true ) ) {
		return;
	}

	$bridal_dresses_args = array(
		'show_on_front' => false,
		'show_title'    => true,
		'show_browse'   => false,
	);
	breadcrumb_trail( $bridal_dresses_args );
}
add_action( 'bridal_dresses_breadcrumb', 'bridal_dresses_breadcrumb', 10 );

/**
 * Add separator for breadcrumb trail.
 */
function bridal_dresses_breadcrumb_trail_print_styles() {
	$bridal_dresses_breadcrumb_separator = get_theme_mod( 'bridal_dresses_breadcrumb_separator', '/' );

	$bridal_dresses_style = '
		.trail-items li::after {
			content: "' . $bridal_dresses_breadcrumb_separator . '";
		}'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	$bridal_dresses_style = apply_filters( 'bridal_dresses_breadcrumb_trail_inline_style', trim( str_replace( array( "\r", "\n", "\t", '  ' ), '', $bridal_dresses_style ) ) );

	if ( $bridal_dresses_style ) {
		echo "\n" . '<style type="text/css" id="breadcrumb-trail-css">' . $bridal_dresses_style . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'bridal_dresses_breadcrumb_trail_print_styles' );

/**
 * Pagination for archive.
 */
function bridal_dresses_render_posts_pagination() {
	$bridal_dresses_is_pagination_enabled = get_theme_mod( 'bridal_dresses_enable_pagination', true );
	if ( $bridal_dresses_is_pagination_enabled ) {
		$bridal_dresses_pagination_type = get_theme_mod( 'bridal_dresses_pagination_type', 'default' );
		if ( 'default' === $bridal_dresses_pagination_type ) :
			the_posts_navigation();
		else :
			the_posts_pagination();
		endif;
	}
}
add_action( 'bridal_dresses_posts_pagination', 'bridal_dresses_render_posts_pagination', 10 );

/**
 * Pagination for single post.
 */
function bridal_dresses_render_post_navigation() {
	the_post_navigation(
		array(
			'prev_text' => '<span>&#10229;</span> <span class="nav-title">%title</span>',
			'next_text' => '<span class="nav-title">%title</span> <span>&#10230;</span>',
		)
	);
}
add_action( 'bridal_dresses_post_navigation', 'bridal_dresses_render_post_navigation' );

/**
 * Adds footer copyright text.
 */
function bridal_dresses_output_footer_copyright_content() {
    $bridal_dresses_theme_data = wp_get_theme();
    $bridal_dresses_copyright_text = get_theme_mod('bridal_dresses_footer_copyright_text');

    if (!empty($bridal_dresses_copyright_text)) {
        $bridal_dresses_text = $bridal_dresses_copyright_text;
    } else {
        $bridal_dresses_default_text = esc_html($bridal_dresses_theme_data->get('Name')) . '&nbsp;' . esc_html__('by', 'bridal-dresses') . '&nbsp;<a target="_blank" href="' . esc_url($bridal_dresses_theme_data->get('AuthorURI')) . '">' . esc_html(ucwords($bridal_dresses_theme_data->get('Author'))) . '</a>';
		/* translators: %s: WordPress.org URL */
        $bridal_dresses_default_text .= sprintf(esc_html__(' | Powered by %s', 'bridal-dresses'), '<a href="' . esc_url(__('https://wordpress.org/', 'bridal-dresses')) . '" target="_blank">WordPress</a>. ');

        $bridal_dresses_text = $bridal_dresses_default_text;
    }
    ?>
    <span><?php echo wp_kses_post($bridal_dresses_text); ?></span>
    <?php
}
add_action('bridal_dresses_footer_copyright', 'bridal_dresses_output_footer_copyright_content');


if ( ! function_exists( 'bridal_dresses_footer_widget' ) ) :
	function bridal_dresses_footer_widget() {
		$bridal_dresses_footer_widget_column = get_theme_mod('bridal_dresses_footer_widget_column','4');

		$bridal_dresses_column_class = '';
		if ($bridal_dresses_footer_widget_column == '1') {
			$bridal_dresses_column_class = 'one-column';
		} elseif ($bridal_dresses_footer_widget_column == '2') {
			$bridal_dresses_column_class = 'two-columns';
		} elseif ($bridal_dresses_footer_widget_column == '3') {
			$bridal_dresses_column_class = 'three-columns';
		} else {
			$bridal_dresses_column_class = 'four-columns';
		}
	
		if($bridal_dresses_footer_widget_column !== ''): 
		?>
		<div class="dt_footer-widgets <?php echo esc_attr($bridal_dresses_column_class); ?>">
			<div class="footer-widgets-column">
				<?php
				$footer_widgets_active = false;

				// Loop to check if any footer widget is active
				for ($bridal_dresses_i = 1; $bridal_dresses_i <= $bridal_dresses_footer_widget_column; $bridal_dresses_i++) {
					if (is_active_sidebar('bridal-dresses-footer-widget-' . $bridal_dresses_i)) {
						$footer_widgets_active = true;
						break;
					}
				}

				if ($footer_widgets_active) {
					// Display active footer widgets
					for ($bridal_dresses_i = 1; $bridal_dresses_i <= $bridal_dresses_footer_widget_column; $bridal_dresses_i++) {
						if (is_active_sidebar('bridal-dresses-footer-widget-' . $bridal_dresses_i)) : ?>
							<div class="footer-one-column">
								<?php dynamic_sidebar('bridal-dresses-footer-widget-' . $bridal_dresses_i); ?>
							</div>
						<?php endif;
					}
				} else {
				?>
				<div class="footer-one-column default-widgets">
					<aside id="search-2" class="widget widget_search default_footer_search">
						<div class="widget-header">
							<h4 class="widget-title"><?php esc_html_e('Search Here', 'bridal-dresses'); ?></h4>
						</div>
						<?php get_search_form(); ?>
					</aside>
				</div>
				<div class="footer-one-column default-widgets">
					<aside id="recent-posts-2" class="widget widget_recent_entries">
						<h2 class="widget-title"><?php esc_html_e('Recent Posts', 'bridal-dresses'); ?></h2>
						<ul>
							<?php
							$recent_posts = wp_get_recent_posts(array(
								'numberposts' => 5,
								'post_status' => 'publish',
							));
							foreach ($recent_posts as $post) {
								echo '<li><a href="' . esc_url(get_permalink($post['ID'])) . '">' . esc_html($post['post_title']) . '</a></li>';
							}
							wp_reset_query();
							?>
						</ul>
					</aside>
				</div>
				<div class="footer-one-column default-widgets">
					<aside id="recent-comments-2" class="widget widget_recent_comments">
						<h2 class="widget-title"><?php esc_html_e('Recent Comments', 'bridal-dresses'); ?></h2>
						<ul>
							<?php
							$recent_comments = get_comments(array(
								'number' => 5,
								'status' => 'approve',
							));
							foreach ($recent_comments as $comment) {
								echo '<li><a href="' . esc_url(get_comment_link($comment)) . '">' .
									/* translators: %s: details. */
									sprintf(esc_html__('Comment on %s', 'bridal-dresses'), get_the_title($comment->comment_post_ID)) .
									'</a></li>';
							}
							?>
						</ul>
					</aside>
				</div>
				<div class="footer-one-column default-widgets">
					<aside id="calendar-2" class="widget widget_calendar">
						<h2 class="widget-title"><?php esc_html_e('Calendar', 'bridal-dresses'); ?></h2>
						<?php get_calendar(); ?>
					</aside>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php
		endif;
	}
	endif;
add_action( 'bridal_dresses_footer_widget', 'bridal_dresses_footer_widget' );


function bridal_dresses_footer_text_transform_css() {
    $bridal_dresses_footer_text_transform = get_theme_mod('footer_text_transform', 'none');
    ?>
    <style type="text/css">
        .site-footer h4,footer#colophon h2.wp-block-heading,footer#colophon .widgettitle,footer#colophon .widget-title {
            text-transform: <?php echo esc_html($bridal_dresses_footer_text_transform); ?>;
        }
    </style>
    <?php
}
add_action('wp_head', 'bridal_dresses_footer_text_transform_css');