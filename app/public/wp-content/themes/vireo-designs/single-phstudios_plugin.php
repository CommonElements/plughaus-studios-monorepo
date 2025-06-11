<?php
/**
 * Template for displaying single plugin pages
 *
 * @package Vireo_Studios
 */

get_header(); ?>

<main id="primary" class="site-main">
    <?php while (have_posts()) : the_post(); 
        // Get plugin meta data
        $plugin_id = get_the_ID();
        $status = get_post_meta($plugin_id, '_plugin_status', true) ?: 'in-development';
        $version = get_post_meta($plugin_id, '_plugin_version', true);
        $wordpress_url = get_post_meta($plugin_id, '_wordpress_url', true);
        $github_url = get_post_meta($plugin_id, '_github_url', true);
        $demo_url = get_post_meta($plugin_id, '_demo_url', true);
        $price_free = get_post_meta($plugin_id, '_price_free', true) ?: 'Free';
        $price_pro = get_post_meta($plugin_id, '_price_pro', true);
        $download_count = get_post_meta($plugin_id, '_download_count', true);
        $rating = get_post_meta($plugin_id, '_rating', true);
        $tested_wp_version = get_post_meta($plugin_id, '_tested_wp_version', true);
        $min_php_version = get_post_meta($plugin_id, '_min_php_version', true);
        $features = get_post_meta($plugin_id, '_plugin_features', true);
        $pro_features = get_post_meta($plugin_id, '_pro_features', true);
        
        // Parse features
        $features_array = $features ? array_filter(array_map('trim', explode("\n", $features))) : array();
        $pro_features_array = $pro_features ? array_filter(array_map('trim', explode("\n", $pro_features))) : array();
        
        // Get plugin categories and tags
        $categories = get_the_terms($plugin_id, 'plugin_category');
        $tags = get_the_terms($plugin_id, 'plugin_tag');
    ?>
    
    <!-- Plugin Hero Section -->
    <section class="plugin-hero">
        <div class="container">
            <div class="plugin-hero__content">
                <div class="plugin-hero__info">
                    <!-- Breadcrumbs -->
                    <nav class="breadcrumbs">
                        <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'plughaus-studios'); ?></a>
                        <span class="separator">></span>
                        <a href="<?php echo esc_url(home_url('/plugins/')); ?>"><?php _e('Plugins', 'plughaus-studios'); ?></a>
                        <span class="separator">></span>
                        <span class="current"><?php the_title(); ?></span>
                    </nav>
                    
                    <!-- Plugin Header -->
                    <div class="plugin-header">
                        <div class="plugin-icon large">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('plugin-featured', array('alt' => get_the_title())); ?>
                            <?php else : ?>
                                <i class="fas fa-puzzle-piece"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="plugin-title-area">
                            <h1 class="plugin-title"><?php the_title(); ?></h1>
                            
                            <div class="plugin-meta">
                                <?php if ($categories) : ?>
                                    <div class="plugin-categories">
                                        <?php foreach ($categories as $category) : ?>
                                            <a href="<?php echo esc_url(get_term_link($category)); ?>" class="plugin-category">
                                                <?php echo esc_html($category->name); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="plugin-badges">
                                    <span class="plugin-status status-<?php echo esc_attr($status); ?>">
                                        <?php
                                        $status_labels = array(
                                            'available' => __('Available Now', 'plughaus-studios'),
                                            'coming-soon' => __('Coming Soon', 'plughaus-studios'),
                                            'in-development' => __('In Development', 'plughaus-studios'),
                                            'beta' => __('Beta', 'plughaus-studios'),
                                        );
                                        echo esc_html($status_labels[$status] ?? ucwords(str_replace('-', ' ', $status)));
                                        ?>
                                    </span>
                                    
                                    <?php if ($version) : ?>
                                        <span class="plugin-version">
                                            <i class="fas fa-tag"></i>
                                            v<?php echo esc_html($version); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="plugin-description">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <!-- Plugin Stats -->
                            <?php if ($download_count || $rating) : ?>
                                <div class="plugin-stats-inline">
                                    <?php if ($download_count) : ?>
                                        <div class="stat">
                                            <i class="fas fa-download"></i>
                                            <span><?php echo number_format($download_count); ?> <?php _e('downloads', 'plughaus-studios'); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($rating) : ?>
                                        <div class="stat">
                                            <div class="rating">
                                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                    <i class="fas fa-star <?php echo $i <= floatval($rating) ? 'filled' : 'empty'; ?>"></i>
                                                <?php endfor; ?>
                                                <span><?php echo esc_html($rating); ?>/5</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Plugin Actions -->
                <div class="plugin-hero__actions">
                    <div class="action-buttons">
                        <?php if ($status === 'available' && $wordpress_url) : ?>
                            <a href="<?php echo esc_url($wordpress_url); ?>" class="btn btn-primary large" target="_blank" rel="noopener">
                                <i class="fab fa-wordpress"></i>
                                <?php _e('Download Free', 'plughaus-studios'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($price_pro) : ?>
                            <a href="#pricing" class="btn btn-secondary large">
                                <i class="fas fa-star"></i>
                                <?php _e('Get Pro Version', 'plughaus-studios'); ?>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($demo_url) : ?>
                            <a href="<?php echo esc_url($demo_url); ?>" class="btn btn-outline large" target="_blank" rel="noopener">
                                <i class="fas fa-eye"></i>
                                <?php _e('View Demo', 'plughaus-studios'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="secondary-actions">
                        <?php if ($github_url) : ?>
                            <a href="<?php echo esc_url($github_url); ?>" class="action-link" target="_blank" rel="noopener" title="<?php _e('View on GitHub', 'plughaus-studios'); ?>">
                                <i class="fab fa-github"></i>
                                <span><?php _e('GitHub', 'plughaus-studios'); ?></span>
                            </a>
                        <?php endif; ?>
                        
                        <button class="action-link share-plugin" data-url="<?php the_permalink(); ?>" data-title="<?php the_title_attribute(); ?>">
                            <i class="fas fa-share-alt"></i>
                            <span><?php _e('Share', 'plughaus-studios'); ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Plugin Content -->
    <div class="plugin-content">
        <div class="container">
            <div class="plugin-layout">
                
                <!-- Main Content -->
                <div class="plugin-main">
                    
                    <!-- Plugin Description -->
                    <section class="plugin-section">
                        <h2><?php _e('Description', 'plughaus-studios'); ?></h2>
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </section>
                    
                    <!-- Features -->
                    <?php if (!empty($features_array) || !empty($pro_features_array)) : ?>
                        <section id="features" class="plugin-section">
                            <h2><?php _e('Features', 'plughaus-studios'); ?></h2>
                            
                            <div class="features-comparison">
                                <?php if (!empty($features_array)) : ?>
                                    <div class="features-column free">
                                        <h3><?php _e('Free Version', 'plughaus-studios'); ?></h3>
                                        <ul class="features-list">
                                            <?php foreach ($features_array as $feature) : ?>
                                                <li>
                                                    <i class="fas fa-check"></i>
                                                    <?php echo esc_html($feature); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($pro_features_array)) : ?>
                                    <div class="features-column pro">
                                        <h3><?php _e('Pro Version', 'plughaus-studios'); ?></h3>
                                        <ul class="features-list">
                                            <?php foreach ($pro_features_array as $feature) : ?>
                                                <li>
                                                    <i class="fas fa-star"></i>
                                                    <?php echo esc_html($feature); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                    <!-- Screenshots/Gallery -->
                    <?php 
                    $gallery = get_post_gallery(get_the_ID(), false);
                    if (!empty($gallery['ids'])) :
                    ?>
                        <section class="plugin-section">
                            <h2><?php _e('Screenshots', 'plughaus-studios'); ?></h2>
                            <div class="plugin-gallery">
                                <?php
                                $image_ids = explode(',', $gallery['ids']);
                                foreach ($image_ids as $image_id) :
                                    $image = wp_get_attachment_image_src($image_id, 'large');
                                    $thumb = wp_get_attachment_image_src($image_id, 'medium');
                                    if ($image) :
                                ?>
                                    <a href="<?php echo esc_url($image[0]); ?>" class="gallery-item" data-lightbox="plugin-gallery">
                                        <img src="<?php echo esc_url($thumb[0]); ?>" alt="<?php echo esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)); ?>">
                                    </a>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                    <!-- Pricing -->
                    <?php if ($price_pro) : ?>
                        <section id="pricing" class="plugin-section">
                            <h2><?php _e('Pricing', 'plughaus-studios'); ?></h2>
                            
                            <div class="pricing-table">
                                <div class="pricing-column free">
                                    <div class="pricing-header">
                                        <h3><?php _e('Free Version', 'plughaus-studios'); ?></h3>
                                        <div class="price"><?php echo esc_html($price_free); ?></div>
                                    </div>
                                    <div class="pricing-content">
                                        <?php if (!empty($features_array)) : ?>
                                            <ul>
                                                <?php foreach (array_slice($features_array, 0, 5) as $feature) : ?>
                                                    <li><?php echo esc_html($feature); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                    <div class="pricing-footer">
                                        <?php if ($wordpress_url) : ?>
                                            <a href="<?php echo esc_url($wordpress_url); ?>" class="btn btn-outline" target="_blank" rel="noopener">
                                                <i class="fab fa-wordpress"></i>
                                                <?php _e('Download Free', 'plughaus-studios'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="pricing-column pro featured">
                                    <div class="pricing-header">
                                        <h3><?php _e('Pro Version', 'plughaus-studios'); ?></h3>
                                        <div class="price"><?php echo esc_html($price_pro); ?></div>
                                    </div>
                                    <div class="pricing-content">
                                        <?php if (!empty($pro_features_array)) : ?>
                                            <ul>
                                                <?php foreach (array_slice($pro_features_array, 0, 5) as $feature) : ?>
                                                    <li><?php echo esc_html($feature); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                        <p class="pro-note"><?php _e('Includes all free features plus:', 'plughaus-studios'); ?></p>
                                    </div>
                                    <div class="pricing-footer">
                                        <a href="#contact" class="btn btn-primary">
                                            <i class="fas fa-star"></i>
                                            <?php _e('Get Pro Version', 'plughaus-studios'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                    <!-- Requirements -->
                    <?php if ($tested_wp_version || $min_php_version) : ?>
                        <section class="plugin-section">
                            <h2><?php _e('Requirements', 'plughaus-studios'); ?></h2>
                            
                            <div class="requirements-grid">
                                <?php if ($tested_wp_version) : ?>
                                    <div class="requirement">
                                        <i class="fab fa-wordpress"></i>
                                        <div>
                                            <h4><?php _e('WordPress Version', 'plughaus-studios'); ?></h4>
                                            <p><?php printf(__('Tested up to %s', 'plughaus-studios'), esc_html($tested_wp_version)); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($min_php_version) : ?>
                                    <div class="requirement">
                                        <i class="fab fa-php"></i>
                                        <div>
                                            <h4><?php _e('PHP Version', 'plughaus-studios'); ?></h4>
                                            <p><?php printf(__('Requires PHP %s or higher', 'plughaus-studios'), esc_html($min_php_version)); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="requirement">
                                    <i class="fas fa-server"></i>
                                    <div>
                                        <h4><?php _e('Server Requirements', 'plughaus-studios'); ?></h4>
                                        <p><?php _e('Standard WordPress hosting', 'plughaus-studios'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                    <!-- Support -->
                    <section class="plugin-section">
                        <h2><?php _e('Support', 'plughaus-studios'); ?></h2>
                        
                        <div class="support-options">
                            <div class="support-option">
                                <i class="fab fa-wordpress"></i>
                                <h4><?php _e('Community Support', 'plughaus-studios'); ?></h4>
                                <p><?php _e('Get help from the WordPress community through our support forums.', 'plughaus-studios'); ?></p>
                                <a href="https://wordpress.org/support/plugin/<?php echo sanitize_title(get_the_title()); ?>/" class="btn btn-outline" target="_blank" rel="noopener">
                                    <?php _e('Visit Forums', 'plughaus-studios'); ?>
                                </a>
                            </div>
                            
                            <?php if ($price_pro) : ?>
                                <div class="support-option pro">
                                    <i class="fas fa-headset"></i>
                                    <h4><?php _e('Priority Support', 'plughaus-studios'); ?></h4>
                                    <p><?php _e('Pro customers get direct email support with faster response times.', 'plughaus-studios'); ?></p>
                                    <a href="#contact" class="btn btn-primary">
                                        <?php _e('Contact Support', 'plughaus-studios'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                    
                </div>
                
                <!-- Sidebar -->
                <aside class="plugin-sidebar">
                    
                    <!-- Quick Info -->
                    <div class="sidebar-widget plugin-info">
                        <h3><?php _e('Plugin Information', 'plughaus-studios'); ?></h3>
                        
                        <dl class="plugin-details">
                            <?php if ($version) : ?>
                                <dt><?php _e('Version', 'plughaus-studios'); ?></dt>
                                <dd><?php echo esc_html($version); ?></dd>
                            <?php endif; ?>
                            
                            <?php if ($download_count) : ?>
                                <dt><?php _e('Downloads', 'plughaus-studios'); ?></dt>
                                <dd><?php echo number_format($download_count); ?></dd>
                            <?php endif; ?>
                            
                            <?php if ($rating) : ?>
                                <dt><?php _e('Rating', 'plughaus-studios'); ?></dt>
                                <dd>
                                    <div class="rating-display">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <i class="fas fa-star <?php echo $i <= floatval($rating) ? 'filled' : 'empty'; ?>"></i>
                                        <?php endfor; ?>
                                        <span><?php echo esc_html($rating); ?>/5</span>
                                    </div>
                                </dd>
                            <?php endif; ?>
                            
                            <?php if ($tested_wp_version) : ?>
                                <dt><?php _e('WordPress Version', 'plughaus-studios'); ?></dt>
                                <dd><?php echo esc_html($tested_wp_version); ?></dd>
                            <?php endif; ?>
                            
                            <dt><?php _e('Last Updated', 'plughaus-studios'); ?></dt>
                            <dd><?php echo get_the_modified_date(); ?></dd>
                        </dl>
                    </div>
                    
                    <!-- Tags -->
                    <?php if ($tags) : ?>
                        <div class="sidebar-widget plugin-tags">
                            <h3><?php _e('Tags', 'plughaus-studios'); ?></h3>
                            <div class="tag-cloud">
                                <?php foreach ($tags as $tag) : ?>
                                    <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="tag">
                                        <?php echo esc_html($tag->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Related Plugins -->
                    <?php
                    $related_args = array(
                        'post_type' => 'phstudios_plugin',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'orderby' => 'rand'
                    );
                    
                    if ($categories) {
                        $related_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'plugin_category',
                                'field' => 'term_id',
                                'terms' => wp_list_pluck($categories, 'term_id')
                            )
                        );
                    }
                    
                    $related_plugins = new WP_Query($related_args);
                    
                    if ($related_plugins->have_posts()) :
                    ?>
                        <div class="sidebar-widget related-plugins">
                            <h3><?php _e('Related Plugins', 'plughaus-studios'); ?></h3>
                            
                            <div class="related-plugins-list">
                                <?php while ($related_plugins->have_posts()) : $related_plugins->the_post(); ?>
                                    <div class="related-plugin">
                                        <a href="<?php the_permalink(); ?>" class="related-plugin-link">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail('thumbnail'); ?>
                                            <?php else : ?>
                                                <div class="related-plugin-icon">
                                                    <i class="fas fa-puzzle-piece"></i>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="related-plugin-info">
                                                <h4><?php the_title(); ?></h4>
                                                <p><?php echo wp_trim_words(get_the_excerpt(), 10); ?></p>
                                            </div>
                                        </a>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php
                        wp_reset_postdata();
                    endif;
                    ?>
                    
                </aside>
                
            </div>
        </div>
    </div>
    
    <?php endwhile; ?>
</main>

<style>
/* Single Plugin Page Styles */
.plugin-hero {
    background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
    padding: var(--spacing-16) 0 var(--spacing-12);
    border-bottom: 1px solid var(--gray-200);
}

.plugin-hero__content {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: var(--spacing-16);
    align-items: start;
}

.breadcrumbs {
    font-size: var(--font-size-sm);
    color: var(--gray-600);
    margin-bottom: var(--spacing-6);
}

.breadcrumbs a {
    color: var(--primary-color);
    text-decoration: none;
}

.breadcrumbs .separator {
    margin: 0 var(--spacing-2);
}

.plugin-header {
    display: flex;
    gap: var(--spacing-6);
    align-items: flex-start;
}

.plugin-icon.large {
    width: 120px;
    height: 120px;
    font-size: var(--font-size-5xl);
    flex-shrink: 0;
}

.plugin-title {
    margin-bottom: var(--spacing-4);
}

.plugin-meta {
    margin-bottom: var(--spacing-6);
}

.plugin-categories {
    margin-bottom: var(--spacing-3);
}

.plugin-category {
    display: inline-block;
    padding: var(--spacing-1) var(--spacing-3);
    background: var(--primary-light);
    color: var(--primary-dark);
    font-size: var(--font-size-sm);
    border-radius: var(--radius);
    text-decoration: none;
    margin-right: var(--spacing-2);
}

.plugin-badges {
    display: flex;
    gap: var(--spacing-3);
    flex-wrap: wrap;
}

.plugin-version {
    display: flex;
    align-items: center;
    gap: var(--spacing-1);
    padding: var(--spacing-1) var(--spacing-3);
    background: var(--gray-100);
    color: var(--gray-700);
    font-size: var(--font-size-sm);
    border-radius: var(--radius);
}

.plugin-description {
    font-size: var(--font-size-lg);
    color: var(--gray-700);
    line-height: 1.6;
    margin-bottom: var(--spacing-6);
}

.plugin-stats-inline {
    display: flex;
    gap: var(--spacing-6);
}

.plugin-stats-inline .stat {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    color: var(--gray-600);
}

.rating {
    display: flex;
    align-items: center;
    gap: var(--spacing-1);
}

.rating .fas.fa-star.filled {
    color: var(--warning-color);
}

.rating .fas.fa-star.empty {
    color: var(--gray-300);
}

.plugin-hero__actions {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-4);
    align-items: flex-end;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-3);
}

.btn.large {
    padding: var(--spacing-4) var(--spacing-8);
    font-size: var(--font-size-lg);
    min-width: 200px;
    justify-content: center;
}

.secondary-actions {
    display: flex;
    gap: var(--spacing-4);
}

.secondary-actions .action-link {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    padding: var(--spacing-2) var(--spacing-4);
    background: white;
    color: var(--gray-600);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    text-decoration: none;
    transition: var(--transition);
}

.secondary-actions .action-link:hover {
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.plugin-content {
    padding: var(--spacing-16) 0;
}

.plugin-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: var(--spacing-16);
}

.plugin-main {
    min-width: 0;
}

.plugin-section {
    margin-bottom: var(--spacing-16);
    padding-bottom: var(--spacing-16);
    border-bottom: 1px solid var(--gray-200);
}

.plugin-section:last-child {
    border-bottom: none;
}

.plugin-section h2 {
    margin-bottom: var(--spacing-8);
}

.features-comparison {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-8);
}

.features-column {
    padding: var(--spacing-6);
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.features-column.free {
    background: var(--success-color);
    color: white;
    border-color: var(--success-color);
}

.features-column.pro {
    background: var(--secondary-color);
    color: white;
    border-color: var(--secondary-color);
}

.features-column h3 {
    margin-bottom: var(--spacing-4);
    color: inherit;
}

.features-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.features-list li {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    margin-bottom: var(--spacing-2);
    line-height: 1.5;
}

.features-list i {
    font-size: var(--font-size-sm);
    opacity: 0.9;
}

.plugin-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-4);
}

.gallery-item {
    display: block;
    border-radius: var(--radius);
    overflow: hidden;
    transition: var(--transition);
}

.gallery-item:hover {
    transform: scale(1.05);
}

.gallery-item img {
    width: 100%;
    height: auto;
    display: block;
}

.pricing-table {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-8);
}

.pricing-column {
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: white;
}

.pricing-column.featured {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-lg);
    position: relative;
}

.pricing-column.featured::before {
    content: 'Most Popular';
    position: absolute;
    top: -1px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--primary-color);
    color: white;
    padding: var(--spacing-1) var(--spacing-4);
    font-size: var(--font-size-xs);
    font-weight: 600;
    border-radius: 0 0 var(--radius) var(--radius);
}

.pricing-header {
    padding: var(--spacing-6);
    text-align: center;
    border-bottom: 1px solid var(--gray-200);
}

.pricing-header h3 {
    margin-bottom: var(--spacing-2);
}

.price {
    font-size: var(--font-size-3xl);
    font-weight: 700;
    color: var(--primary-color);
}

.pricing-content {
    padding: var(--spacing-6);
}

.pricing-content ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.pricing-content li {
    margin-bottom: var(--spacing-2);
    position: relative;
    padding-left: var(--spacing-6);
}

.pricing-content li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: var(--success-color);
    font-weight: bold;
}

.pro-note {
    margin-top: var(--spacing-4);
    font-style: italic;
    color: var(--gray-600);
}

.pricing-footer {
    padding: var(--spacing-6);
    border-top: 1px solid var(--gray-200);
    text-align: center;
}

.requirements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-6);
}

.requirement {
    display: flex;
    gap: var(--spacing-4);
    align-items: flex-start;
    padding: var(--spacing-4);
    background: var(--gray-50);
    border-radius: var(--radius);
}

.requirement i {
    font-size: var(--font-size-2xl);
    color: var(--primary-color);
    margin-top: var(--spacing-1);
}

.requirement h4 {
    margin-bottom: var(--spacing-1);
}

.requirement p {
    color: var(--gray-600);
    margin: 0;
}

.support-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-8);
}

.support-option {
    text-align: center;
    padding: var(--spacing-8);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    background: white;
}

.support-option.pro {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border-color: var(--primary-color);
}

.support-option.pro h4,
.support-option.pro p {
    color: white;
}

.support-option i {
    font-size: var(--font-size-4xl);
    color: var(--primary-color);
    margin-bottom: var(--spacing-4);
}

.support-option.pro i {
    color: white;
}

.support-option h4 {
    margin-bottom: var(--spacing-3);
}

.support-option p {
    color: var(--gray-600);
    margin-bottom: var(--spacing-6);
}

/* Sidebar Styles */
.plugin-sidebar {
    position: sticky;
    top: 100px;
}

.sidebar-widget {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: var(--spacing-6);
    margin-bottom: var(--spacing-6);
}

.sidebar-widget h3 {
    margin-bottom: var(--spacing-4);
    padding-bottom: var(--spacing-3);
    border-bottom: 1px solid var(--gray-200);
}

.plugin-details {
    margin: 0;
}

.plugin-details dt {
    font-weight: 600;
    margin-bottom: var(--spacing-1);
    color: var(--gray-700);
}

.plugin-details dd {
    margin-bottom: var(--spacing-4);
    color: var(--gray-600);
}

.rating-display {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
}

.tag-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-2);
}

.tag {
    padding: var(--spacing-1) var(--spacing-3);
    background: var(--gray-100);
    color: var(--gray-700);
    font-size: var(--font-size-sm);
    border-radius: var(--radius);
    text-decoration: none;
    transition: var(--transition);
}

.tag:hover {
    background: var(--primary-color);
    color: white;
}

.related-plugins-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-4);
}

.related-plugin {
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    overflow: hidden;
    transition: var(--transition);
}

.related-plugin:hover {
    box-shadow: var(--shadow-md);
}

.related-plugin-link {
    display: flex;
    gap: var(--spacing-3);
    padding: var(--spacing-3);
    text-decoration: none;
    color: inherit;
}

.related-plugin-link img,
.related-plugin-icon {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    border-radius: var(--radius-sm);
}

.related-plugin-icon {
    background: var(--primary-light);
    color: var(--primary-dark);
    display: flex;
    align-items: center;
    justify-content: center;
}

.related-plugin-info h4 {
    font-size: var(--font-size-sm);
    margin-bottom: var(--spacing-1);
}

.related-plugin-info p {
    font-size: var(--font-size-xs);
    color: var(--gray-600);
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .plugin-hero__content {
        grid-template-columns: 1fr;
        gap: var(--spacing-8);
    }
    
    .plugin-header {
        flex-direction: column;
        text-align: center;
    }
    
    .plugin-hero__actions {
        align-items: stretch;
    }
    
    .action-buttons {
        width: 100%;
    }
    
    .btn.large {
        min-width: auto;
    }
    
    .plugin-layout {
        grid-template-columns: 1fr;
        gap: var(--spacing-8);
    }
    
    .features-comparison,
    .pricing-table,
    .support-options {
        grid-template-columns: 1fr;
    }
    
    .plugin-sidebar {
        position: static;
    }
}
</style>

<?php get_footer(); ?>