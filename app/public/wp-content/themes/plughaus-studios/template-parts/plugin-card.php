<?php
/**
 * Template part for displaying plugin cards
 *
 * @package PlugHaus_Studios
 */

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
$features = get_post_meta($plugin_id, '_plugin_features', true);
$pro_features = get_post_meta($plugin_id, '_pro_features', true);
$featured = get_post_meta($plugin_id, '_featured_plugin', true);

// Parse features
$features_array = $features ? array_filter(array_map('trim', explode("\n", $features))) : array();
$pro_features_array = $pro_features ? array_filter(array_map('trim', explode("\n", $pro_features))) : array();

// Get plugin categories
$categories = get_the_terms($plugin_id, 'plugin_category');
$category_names = $categories ? array_map(function($cat) { return $cat->name; }, $categories) : array();

?>

<div class="plugin-card <?php echo $featured ? 'featured' : ''; ?>" data-plugin-id="<?php echo esc_attr($plugin_id); ?>">
    
    <!-- Plugin Header -->
    <div class="plugin-card__header">
        <div class="plugin-icon">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('plugin-thumbnail', array('alt' => get_the_title())); ?>
            <?php else : ?>
                <?php
                // Default icons based on categories or title
                $icon = 'fas fa-puzzle-piece';
                if (in_array('Property Management', $category_names) || strpos(strtolower(get_the_title()), 'property') !== false) {
                    $icon = 'fas fa-building';
                } elseif (in_array('Payment', $category_names) || strpos(strtolower(get_the_title()), 'payment') !== false) {
                    $icon = 'fas fa-credit-card';
                } elseif (in_array('Document', $category_names) || strpos(strtolower(get_the_title()), 'document') !== false) {
                    $icon = 'fas fa-file-alt';
                } elseif (in_array('Analytics', $category_names) || strpos(strtolower(get_the_title()), 'analytic') !== false) {
                    $icon = 'fas fa-chart-line';
                }
                ?>
                <i class="<?php echo esc_attr($icon); ?>"></i>
            <?php endif; ?>
        </div>
        
        <div class="plugin-status-badges">
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
            
            <?php if ($featured) : ?>
                <span class="featured-badge">
                    <i class="fas fa-star"></i>
                    <?php _e('Featured', 'plughaus-studios'); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Plugin Content -->
    <div class="plugin-card__content">
        <h3 class="plugin-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <?php if ($categories) : ?>
            <div class="plugin-categories">
                <?php foreach ($categories as $category) : ?>
                    <span class="plugin-category"><?php echo esc_html($category->name); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="plugin-description">
            <?php 
            if (has_excerpt()) {
                the_excerpt();
            } else {
                echo wp_trim_words(get_the_content(), 20, '...');
            }
            ?>
        </div>
        
        <!-- Plugin Stats -->
        <?php if ($download_count || $rating || $version) : ?>
            <div class="plugin-stats">
                <?php if ($download_count) : ?>
                    <div class="plugin-stat">
                        <i class="fas fa-download"></i>
                        <span><?php echo number_format($download_count); ?> <?php _e('downloads', 'plughaus-studios'); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($rating) : ?>
                    <div class="plugin-stat">
                        <i class="fas fa-star"></i>
                        <span><?php echo esc_html($rating); ?>/5</span>
                    </div>
                <?php endif; ?>
                
                <?php if ($version) : ?>
                    <div class="plugin-stat">
                        <i class="fas fa-tag"></i>
                        <span>v<?php echo esc_html($version); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Features Preview -->
        <?php if (!empty($features_array)) : ?>
            <div class="plugin-features">
                <h5><?php _e('Features:', 'plughaus-studios'); ?></h5>
                <ul class="features-list">
                    <?php foreach (array_slice($features_array, 0, 3) as $feature) : ?>
                        <li>
                            <i class="fas fa-check"></i>
                            <?php echo esc_html($feature); ?>
                        </li>
                    <?php endforeach; ?>
                    <?php if (count($features_array) > 3) : ?>
                        <li class="more-features">
                            <i class="fas fa-plus"></i>
                            <?php printf(__('And %d more...', 'plughaus-studios'), count($features_array) - 3); ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Pricing -->
        <div class="plugin-pricing">
            <div class="price-options">
                <div class="price-option free">
                    <span class="price-label"><?php _e('Free', 'plughaus-studios'); ?></span>
                    <span class="price-value"><?php echo esc_html($price_free); ?></span>
                </div>
                
                <?php if ($price_pro) : ?>
                    <div class="price-option pro">
                        <span class="price-label"><?php _e('Pro', 'plughaus-studios'); ?></span>
                        <span class="price-value"><?php echo esc_html($price_pro); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Plugin Actions -->
    <div class="plugin-card__actions">
        <div class="primary-actions">
            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                <i class="fas fa-info-circle"></i>
                <?php _e('Learn More', 'plughaus-studios'); ?>
            </a>
            
            <?php if ($status === 'available' && $wordpress_url) : ?>
                <a href="<?php echo esc_url($wordpress_url); ?>" class="btn btn-secondary" target="_blank" rel="noopener">
                    <i class="fab fa-wordpress"></i>
                    <?php _e('Download Free', 'plughaus-studios'); ?>
                </a>
            <?php elseif ($demo_url) : ?>
                <a href="<?php echo esc_url($demo_url); ?>" class="btn btn-outline" target="_blank" rel="noopener">
                    <i class="fas fa-eye"></i>
                    <?php _e('View Demo', 'plughaus-studios'); ?>
                </a>
            <?php endif; ?>
        </div>
        
        <div class="secondary-actions">
            <?php if ($github_url) : ?>
                <a href="<?php echo esc_url($github_url); ?>" class="action-link" target="_blank" rel="noopener" title="<?php _e('View on GitHub', 'plughaus-studios'); ?>">
                    <i class="fab fa-github"></i>
                </a>
            <?php endif; ?>
            
            <button class="action-link share-plugin" title="<?php _e('Share Plugin', 'plughaus-studios'); ?>" data-url="<?php the_permalink(); ?>" data-title="<?php the_title_attribute(); ?>">
                <i class="fas fa-share-alt"></i>
            </button>
            
            <button class="action-link bookmark-plugin" title="<?php _e('Bookmark Plugin', 'plughaus-studios'); ?>" data-plugin-id="<?php echo esc_attr($plugin_id); ?>">
                <i class="far fa-bookmark"></i>
            </button>
        </div>
    </div>
    
</div>

<style>
/* Plugin Card Styles */
.plugin-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    transition: var(--transition);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.plugin-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.plugin-card.featured {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-lg);
    position: relative;
}

.plugin-card.featured::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.plugin-card__header {
    padding: var(--spacing-6);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--spacing-4);
}

.plugin-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-light);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-dark);
    font-size: var(--font-size-2xl);
    flex-shrink: 0;
}

.plugin-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--radius-lg);
}

.plugin-status-badges {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-1);
    align-items: flex-end;
}

.plugin-status {
    padding: var(--spacing-1) var(--spacing-3);
    font-size: var(--font-size-xs);
    font-weight: 500;
    border-radius: var(--radius);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.plugin-status.status-available {
    background-color: var(--success-color);
    color: white;
}

.plugin-status.status-coming-soon {
    background-color: var(--warning-color);
    color: white;
}

.plugin-status.status-in-development {
    background-color: var(--gray-400);
    color: white;
}

.plugin-status.status-beta {
    background-color: var(--secondary-color);
    color: white;
}

.featured-badge {
    padding: var(--spacing-1) var(--spacing-2);
    background: var(--primary-color);
    color: white;
    font-size: var(--font-size-xs);
    font-weight: 500;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    gap: var(--spacing-1);
}

.plugin-card__content {
    padding: 0 var(--spacing-6) var(--spacing-6);
    flex: 1;
    display: flex;
    flex-direction: column;
}

.plugin-title {
    margin-bottom: var(--spacing-3);
}

.plugin-title a {
    color: var(--gray-900);
    text-decoration: none;
    transition: var(--transition);
}

.plugin-title a:hover {
    color: var(--primary-color);
}

.plugin-categories {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-1);
    margin-bottom: var(--spacing-4);
}

.plugin-category {
    padding: var(--spacing-1) var(--spacing-2);
    background: var(--gray-100);
    color: var(--gray-700);
    font-size: var(--font-size-xs);
    border-radius: var(--radius-sm);
}

.plugin-description {
    color: var(--gray-600);
    line-height: 1.6;
    margin-bottom: var(--spacing-4);
    flex: 1;
}

.plugin-stats {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-4);
    margin-bottom: var(--spacing-4);
    padding: var(--spacing-3);
    background: var(--gray-50);
    border-radius: var(--radius);
}

.plugin-stat {
    display: flex;
    align-items: center;
    gap: var(--spacing-1);
    font-size: var(--font-size-sm);
    color: var(--gray-600);
}

.plugin-stat i {
    color: var(--primary-color);
}

.plugin-features {
    margin-bottom: var(--spacing-4);
}

.plugin-features h5 {
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: var(--spacing-2);
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
    font-size: var(--font-size-sm);
    color: var(--gray-600);
    margin-bottom: var(--spacing-1);
}

.features-list i {
    color: var(--success-color);
    font-size: var(--font-size-xs);
}

.more-features i {
    color: var(--gray-400);
}

.plugin-pricing {
    margin-bottom: var(--spacing-4);
}

.price-options {
    display: flex;
    gap: var(--spacing-3);
}

.price-option {
    flex: 1;
    text-align: center;
    padding: var(--spacing-3);
    border-radius: var(--radius);
    border: 1px solid var(--gray-200);
}

.price-option.free {
    background: var(--success-color);
    color: white;
    border-color: var(--success-color);
}

.price-option.pro {
    background: var(--secondary-color);
    color: white;
    border-color: var(--secondary-color);
}

.price-label {
    display: block;
    font-size: var(--font-size-xs);
    font-weight: 500;
    text-transform: uppercase;
    margin-bottom: var(--spacing-1);
    opacity: 0.9;
}

.price-value {
    display: block;
    font-size: var(--font-size-sm);
    font-weight: 600;
}

.plugin-card__actions {
    padding: var(--spacing-6);
    border-top: 1px solid var(--gray-200);
    background: var(--gray-50);
}

.primary-actions {
    display: flex;
    gap: var(--spacing-3);
    margin-bottom: var(--spacing-4);
}

.primary-actions .btn {
    flex: 1;
    justify-content: center;
}

.secondary-actions {
    display: flex;
    justify-content: center;
    gap: var(--spacing-4);
}

.action-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: white;
    color: var(--gray-500);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    text-decoration: none;
    transition: var(--transition);
}

.action-link:hover {
    color: var(--primary-color);
    border-color: var(--primary-color);
    background: var(--primary-light);
}

/* Responsive */
@media (max-width: 768px) {
    .plugin-card__header {
        padding: var(--spacing-4);
    }
    
    .plugin-card__content {
        padding: 0 var(--spacing-4) var(--spacing-4);
    }
    
    .plugin-card__actions {
        padding: var(--spacing-4);
    }
    
    .primary-actions {
        flex-direction: column;
    }
    
    .price-options {
        flex-direction: column;
    }
}
</style>