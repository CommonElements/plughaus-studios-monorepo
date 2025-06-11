<?php
/**
 * The main template file
 *
 * @package Vireo_Studios
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        
        <?php if (have_posts()) : ?>
            
            <?php if (is_home() && !is_front_page()) : ?>
                <header class="page-header">
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                </header>
            <?php endif; ?>
            
            <div class="posts-container">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <header class="entry-header">
                                <h2 class="entry-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="entry-meta">
                                    <span class="posted-on">
                                        <i class="fas fa-calendar"></i>
                                        <time datetime="<?php echo get_the_date('c'); ?>">
                                            <?php echo get_the_date(); ?>
                                        </time>
                                    </span>
                                    
                                    <?php if (get_post_type() === 'phstudios_plugin') : ?>
                                        <?php
                                        $status = get_post_meta(get_the_ID(), '_plugin_status', true);
                                        $version = get_post_meta(get_the_ID(), '_plugin_version', true);
                                        ?>
                                        <?php if ($status) : ?>
                                            <span class="plugin-status status-<?php echo esc_attr($status); ?>">
                                                <i class="fas fa-info-circle"></i>
                                                <?php echo esc_html(ucwords(str_replace('-', ' ', $status))); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($version) : ?>
                                            <span class="plugin-version">
                                                <i class="fas fa-tag"></i>
                                                v<?php echo esc_html($version); ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </header>
                            
                            <div class="entry-summary">
                                <?php 
                                if (has_excerpt()) {
                                    the_excerpt();
                                } else {
                                    echo wp_trim_words(get_the_content(), 30, '...');
                                }
                                ?>
                            </div>
                            
                            <footer class="entry-footer">
                                <a href="<?php the_permalink(); ?>" class="btn btn-outline">
                                    <?php _e('Learn More', 'plughaus-studios'); ?>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                                
                                <?php if (get_post_type() === 'phstudios_plugin') : ?>
                                    <?php
                                    $wordpress_url = get_post_meta(get_the_ID(), '_wordpress_url', true);
                                    $github_url = get_post_meta(get_the_ID(), '_github_url', true);
                                    ?>
                                    
                                    <?php if ($wordpress_url) : ?>
                                        <a href="<?php echo esc_url($wordpress_url); ?>" class="btn btn-secondary" target="_blank">
                                            <i class="fab fa-wordpress"></i>
                                            <?php _e('WordPress.org', 'plughaus-studios'); ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($github_url) : ?>
                                        <a href="<?php echo esc_url($github_url); ?>" class="btn btn-outline" target="_blank">
                                            <i class="fab fa-github"></i>
                                            <?php _e('GitHub', 'plughaus-studios'); ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </footer>
                        </div>
                    </article>
                    
                <?php endwhile; ?>
            </div>
            
            <?php
            // Pagination
            the_posts_pagination(array(
                'prev_text' => __('Previous', 'plughaus-studios'),
                'next_text' => __('Next', 'plughaus-studios'),
            ));
            ?>
            
        <?php else : ?>
            
            <section class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php _e('Nothing here', 'plughaus-studios'); ?></h1>
                </header>
                
                <div class="page-content">
                    <?php if (is_search()) : ?>
                        <p><?php _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'plughaus-studios'); ?></p>
                        <?php get_search_form(); ?>
                    <?php else : ?>
                        <p><?php _e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'plughaus-studios'); ?></p>
                        <?php get_search_form(); ?>
                    <?php endif; ?>
                </div>
            </section>
            
        <?php endif; ?>
        
    </div>
</main>

<?php get_footer(); ?>