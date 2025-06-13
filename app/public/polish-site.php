<?php
/**
 * Site Polish & Advanced Features Script
 * Run this by visiting: http://plughaus-studios-the-beginning-is-finished.local/polish-site.php
 */

// Include WordPress
define('WP_USE_THEMES', false);
require_once dirname(__FILE__) . '/wp-load.php';

// Check if user is admin
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_redirect(wp_login_url($_SERVER['REQUEST_URI']));
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PlugHaus Studios Site Polish</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2563eb; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: #0066cc; font-style: italic; }
        .btn { background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 5px 10px 0; }
        .section { background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 8px; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 4px; color: #856404; }
    </style>
</head>
<body>
    <h1>PlugHaus Studios Site Polish & Advanced Features</h1>
    
    <?php
    // 1. Create individual plugin detail pages
    echo '<div class="section">';
    echo '<h2>1. Creating Individual Plugin Templates</h2>';
    
    $template_dir = get_template_directory();
    
    // Create single plugin template
    $single_plugin_template = $template_dir . '/single-phstudios_plugin.php';
    if (!file_exists($single_plugin_template)) {
        $template_content = '<?php
/**
 * Template for displaying single plugin
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(\'plugin-detail\'); ?>>
                
                <header class="plugin-header">
                    <div class="plugin-meta">
                        <?php
                        $status = get_post_meta(get_the_ID(), \'_plugin_status\', true);
                        $version = get_post_meta(get_the_ID(), \'_plugin_version\', true);
                        ?>
                        <span class="plugin-status status-<?php echo esc_attr($status); ?>">
                            <?php echo esc_html(ucwords(str_replace(\'-\', \' \', $status))); ?>
                        </span>
                        <?php if ($version) : ?>
                            <span class="plugin-version">v<?php echo esc_html($version); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="plugin-title"><?php the_title(); ?></h1>
                    
                    <div class="plugin-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                    
                    <div class="plugin-stats">
                        <?php
                        $rating = get_post_meta(get_the_ID(), \'_rating\', true);
                        $downloads = get_post_meta(get_the_ID(), \'_download_count\', true);
                        $tested_wp = get_post_meta(get_the_ID(), \'_tested_wp_version\', true);
                        ?>
                        
                        <?php if ($rating) : ?>
                            <div class="stat">
                                <span class="stat-label">Rating</span>
                                <span class="stat-value">
                                    <?php echo str_repeat(\'★\', floor($rating)); ?>
                                    <?php echo str_repeat(\'☆\', 5 - floor($rating)); ?>
                                    <?php echo esc_html($rating); ?>/5
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($downloads) : ?>
                            <div class="stat">
                                <span class="stat-label">Downloads</span>
                                <span class="stat-value"><?php echo number_format($downloads); ?>+</span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($tested_wp) : ?>
                            <div class="stat">
                                <span class="stat-label">Tested up to</span>
                                <span class="stat-value">WordPress <?php echo esc_html($tested_wp); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="plugin-actions">
                        <?php
                        $wordpress_url = get_post_meta(get_the_ID(), \'_wordpress_url\', true);
                        $github_url = get_post_meta(get_the_ID(), \'_github_url\', true);
                        $demo_url = get_post_meta(get_the_ID(), \'_demo_url\', true);
                        $price_free = get_post_meta(get_the_ID(), \'_price_free\', true);
                        $price_pro = get_post_meta(get_the_ID(), \'_price_pro\', true);
                        ?>
                        
                        <?php if ($wordpress_url) : ?>
                            <a href="<?php echo esc_url($wordpress_url); ?>" class="btn btn-primary" target="_blank">
                                <i class="fas fa-download"></i> Download Free
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($price_pro) : ?>
                            <a href="#" class="btn btn-pro">
                                <i class="fas fa-crown"></i> Get Pro (<?php echo esc_html($price_pro); ?>)
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($demo_url) : ?>
                            <a href="<?php echo esc_url($demo_url); ?>" class="btn btn-outline" target="_blank">
                                <i class="fas fa-eye"></i> Live Demo
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($github_url) : ?>
                            <a href="<?php echo esc_url($github_url); ?>" class="btn btn-outline" target="_blank">
                                <i class="fab fa-github"></i> GitHub
                            </a>
                        <?php endif; ?>
                    </div>
                </header>
                
                <div class="plugin-content">
                    <div class="plugin-description">
                        <?php the_content(); ?>
                    </div>
                    
                    <div class="plugin-sidebar">
                        <div class="plugin-details-box">
                            <h3>Plugin Details</h3>
                            <dl>
                                <?php if ($version) : ?>
                                    <dt>Version</dt>
                                    <dd><?php echo esc_html($version); ?></dd>
                                <?php endif; ?>
                                
                                <?php
                                $min_php = get_post_meta(get_the_ID(), \'_min_php_version\', true);
                                if ($min_php) :
                                ?>
                                    <dt>Requires PHP</dt>
                                    <dd><?php echo esc_html($min_php); ?>+</dd>
                                <?php endif; ?>
                                
                                <?php if ($tested_wp) : ?>
                                    <dt>Tested up to</dt>
                                    <dd>WordPress <?php echo esc_html($tested_wp); ?></dd>
                                <?php endif; ?>
                                
                                <dt>Last updated</dt>
                                <dd><?php echo get_the_modified_date(); ?></dd>
                            </dl>
                        </div>
                        
                        <?php
                        $features = get_post_meta(get_the_ID(), \'_plugin_features\', true);
                        if ($features) :
                            $features_list = explode("\\n", $features);
                        ?>
                            <div class="features-box">
                                <h3>Free Features</h3>
                                <ul>
                                    <?php foreach ($features_list as $feature) : ?>
                                        <?php if (trim($feature)) : ?>
                                            <li><?php echo esc_html(trim($feature)); ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php
                        $pro_features = get_post_meta(get_the_ID(), \'_pro_features\', true);
                        if ($pro_features) :
                            $pro_features_list = explode("\\n", $pro_features);
                        ?>
                            <div class="pro-features-box">
                                <h3>Pro Features</h3>
                                <ul>
                                    <?php foreach ($pro_features_list as $feature) : ?>
                                        <?php if (trim($feature)) : ?>
                                            <li><?php echo esc_html(trim($feature)); ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if ($price_pro) : ?>
                                    <a href="#" class="btn btn-pro btn-block">Upgrade to Pro</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </article>
            
        <?php endwhile; ?>
        
        <div class="related-plugins">
            <h2>Related Plugins</h2>
            <div class="plugins-grid">
                <?php
                $related = get_posts(array(
                    \'post_type\' => \'phstudios_plugin\',
                    \'posts_per_page\' => 3,
                    \'post__not_in\' => array(get_the_ID()),
                    \'orderby\' => \'rand\'
                ));
                
                foreach ($related as $plugin) :
                    setup_postdata($plugin);
                ?>
                    <div class="plugin-card">
                        <h3><a href="<?php echo get_permalink($plugin->ID); ?>"><?php echo esc_html($plugin->post_title); ?></a></h3>
                        <p><?php echo esc_html($plugin->post_excerpt); ?></p>
                        <a href="<?php echo get_permalink($plugin->ID); ?>" class="btn btn-outline">Learn More</a>
                    </div>
                <?php
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        </div>
        
    </div>
</main>

<?php get_footer(); ?>';

        file_put_contents($single_plugin_template, $template_content);
        echo '<p class="success">✓ Created single plugin template</p>';
    } else {
        echo '<p class="info">→ Single plugin template already exists</p>';
    }
    
    // Create plugin archive template
    $archive_plugin_template = $template_dir . '/archive-phstudios_plugin.php';
    if (!file_exists($archive_plugin_template)) {
        $archive_content = '<?php
/**
 * Template for displaying plugin archive
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        
        <header class="page-header">
            <h1 class="page-title">Our WordPress Plugins</h1>
            <p class="page-description">Professional WordPress solutions for modern businesses. All plugins include free versions with optional Pro upgrades.</p>
        </header>
        
        <div class="plugins-filters">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All Plugins</button>
                <button class="filter-btn" data-filter="available">Available</button>
                <button class="filter-btn" data-filter="coming-soon">Coming Soon</button>
            </div>
        </div>
        
        <div class="plugins-showcase plugins-archive">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part(\'template-parts/plugin-card\'); ?>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No plugins found.</p>
            <?php endif; ?>
        </div>
        
        <?php the_posts_pagination(); ?>
        
    </div>
</main>

<?php get_footer(); ?>';

        file_put_contents($archive_plugin_template, $archive_content);
        echo '<p class="success">✓ Created plugin archive template</p>';
    } else {
        echo '<p class="info">→ Plugin archive template already exists</p>';
    }
    
    echo '</div>';
    
    // 2. Create plugin card template part
    echo '<div class="section">';
    echo '<h2>2. Creating Plugin Card Component</h2>';
    
    $template_parts_dir = $template_dir . '/template-parts';
    if (!is_dir($template_parts_dir)) {
        mkdir($template_parts_dir, 0755, true);
        echo '<p class="success">✓ Created template-parts directory</p>';
    }
    
    $plugin_card_template = $template_parts_dir . '/plugin-card.php';
    if (!file_exists($plugin_card_template)) {
        $card_content = '<?php
/**
 * Template part for displaying plugin cards
 */

$status = get_post_meta(get_the_ID(), \'_plugin_status\', true) ?: \'available\';
$version = get_post_meta(get_the_ID(), \'_plugin_version\', true);
$rating = get_post_meta(get_the_ID(), \'_rating\', true);
$downloads = get_post_meta(get_the_ID(), \'_download_count\', true);
$price_free = get_post_meta(get_the_ID(), \'_price_free\', true) ?: \'Free\';
$price_pro = get_post_meta(get_the_ID(), \'_price_pro\', true);
$wordpress_url = get_post_meta(get_the_ID(), \'_wordpress_url\', true);
?>

<article class="plugin-card" data-status="<?php echo esc_attr($status); ?>">
    
    <header class="plugin-card-header">
        <h3 class="plugin-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <span class="plugin-status status-<?php echo esc_attr($status); ?>">
            <?php echo esc_html(ucwords(str_replace(\'-\', \' \', $status))); ?>
        </span>
    </header>
    
    <div class="plugin-card-content">
        <p class="plugin-excerpt"><?php the_excerpt(); ?></p>
        
        <div class="plugin-stats">
            <?php if ($downloads) : ?>
                <span class="stat">
                    <i class="fas fa-download"></i>
                    <?php echo number_format($downloads); ?> downloads
                </span>
            <?php endif; ?>
            
            <?php if ($rating) : ?>
                <span class="stat">
                    <i class="fas fa-star"></i>
                    <?php echo esc_html($rating); ?>/5
                </span>
            <?php endif; ?>
            
            <?php if ($version) : ?>
                <span class="stat">
                    <i class="fas fa-tag"></i>
                    v<?php echo esc_html($version); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <div class="plugin-pricing">
            <span class="price free"><?php echo esc_html($price_free); ?></span>
            <?php if ($price_pro) : ?>
                <span class="price pro"><?php echo esc_html($price_pro); ?></span>
            <?php endif; ?>
        </div>
    </div>
    
    <footer class="plugin-card-actions">
        <a href="<?php the_permalink(); ?>" class="btn btn-primary">Learn More</a>
        <?php if ($wordpress_url && $status === \'available\') : ?>
            <a href="<?php echo esc_url($wordpress_url); ?>" class="btn btn-outline" target="_blank">Download</a>
        <?php endif; ?>
    </footer>
    
</article>';

        file_put_contents($plugin_card_template, $card_content);
        echo '<p class="success">✓ Created plugin card template part</p>';
    } else {
        echo '<p class="info">→ Plugin card template part already exists</p>';
    }
    
    echo '</div>';
    
    // 3. Add advanced CSS for better styling
    echo '<div class="section">';
    echo '<h2>3. Adding Advanced Styling</h2>';
    
    $advanced_css = $template_dir . '/assets/css/advanced.css';
    $css_dir = dirname($advanced_css);
    
    if (!is_dir($css_dir)) {
        mkdir($css_dir, 0755, true);
        echo '<p class="success">✓ Created assets/css directory</p>';
    }
    
    if (!file_exists($advanced_css)) {
        $css_content = '/* Advanced Plugin Styles */

.plugin-detail {
    margin: 2rem 0;
}

.plugin-header {
    text-align: center;
    padding: 3rem 0;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    border-radius: var(--radius-lg);
    margin-bottom: 3rem;
}

.plugin-meta {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.plugin-status {
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius);
    font-size: var(--font-size-sm);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.plugin-status.status-available {
    background-color: rgba(34, 197, 94, 0.2);
    color: #16a34a;
}

.plugin-status.status-coming-soon {
    background-color: rgba(245, 158, 11, 0.2);
    color: #d97706;
}

.plugin-version {
    background-color: rgba(255, 255, 255, 0.2);
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius);
    font-size: var(--font-size-sm);
    font-weight: 500;
}

.plugin-title {
    font-size: 3rem;
    margin: 1rem 0;
    line-height: 1.2;
}

.plugin-excerpt {
    font-size: 1.25rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto 2rem;
}

.plugin-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.stat-label {
    font-size: var(--font-size-sm);
    opacity: 0.8;
}

.stat-value {
    font-size: 1.125rem;
    font-weight: 600;
}

.plugin-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.plugin-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
    margin-top: 3rem;
}

.plugin-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.plugin-details-box,
.features-box,
.pro-features-box {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
}

.plugin-details-box h3,
.features-box h3,
.pro-features-box h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: var(--gray-900);
}

.plugin-details-box dl {
    margin: 0;
}

.plugin-details-box dt {
    font-weight: 600;
    color: var(--gray-700);
    margin-top: 0.75rem;
}

.plugin-details-box dt:first-child {
    margin-top: 0;
}

.plugin-details-box dd {
    margin: 0.25rem 0 0 0;
    color: var(--gray-600);
}

.features-box ul,
.pro-features-box ul {
    margin: 0;
    padding-left: 1.25rem;
}

.features-box li,
.pro-features-box li {
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.pro-features-box {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-color: #f59e0b;
}

.pro-features-box h3 {
    color: #92400e;
}

.btn-pro {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    border: none;
}

.btn-pro:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
}

.btn-block {
    width: 100%;
    text-align: center;
    margin-top: 1rem;
}

.related-plugins {
    margin-top: 4rem;
    padding-top: 3rem;
    border-top: 1px solid var(--gray-200);
}

.plugins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.plugins-filters {
    margin-bottom: 2rem;
    text-align: center;
}

.filter-buttons {
    display: inline-flex;
    background: white;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.filter-btn {
    padding: 0.75rem 1.5rem;
    background: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
    border-right: 1px solid var(--gray-300);
}

.filter-btn:last-child {
    border-right: none;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--primary-color);
    color: white;
}

.plugins-archive {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.plugin-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.plugin-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-color);
}

.plugin-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.plugin-card-title {
    margin: 0;
    flex: 1;
    margin-right: 1rem;
}

.plugin-card-title a {
    color: var(--gray-900);
    text-decoration: none;
    font-size: 1.25rem;
    font-weight: 600;
}

.plugin-card-title a:hover {
    color: var(--primary-color);
}

.plugin-card-content {
    flex: 1;
    margin-bottom: 1.5rem;
}

.plugin-excerpt {
    color: var(--gray-600);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.plugin-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: var(--font-size-sm);
    color: var(--gray-500);
    flex-wrap: wrap;
}

.plugin-stats .stat {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.plugin-stats i {
    color: var(--primary-color);
}

.plugin-pricing {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.price {
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius);
    font-size: var(--font-size-sm);
    font-weight: 600;
}

.price.free {
    background-color: var(--success-color);
    color: white;
}

.price.pro {
    background-color: var(--warning-color);
    color: white;
}

.plugin-card-actions {
    display: flex;
    gap: 0.75rem;
}

.plugin-card-actions .btn {
    flex: 1;
    text-align: center;
    padding: 0.75rem 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .plugin-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .plugin-header {
        padding: 2rem 1rem;
    }
    
    .plugin-title {
        font-size: 2rem;
    }
    
    .plugin-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .plugin-actions {
        flex-direction: column;
    }
    
    .plugins-archive {
        grid-template-columns: 1fr;
    }
    
    .filter-buttons {
        flex-direction: column;
        width: 100%;
    }
    
    .filter-btn {
        border-right: none;
        border-bottom: 1px solid var(--gray-300);
    }
    
    .filter-btn:last-child {
        border-bottom: none;
    }
}

/* Animation for filtering */
.plugin-card.hidden {
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.3s ease;
}

.plugin-card.visible {
    opacity: 1;
    transform: scale(1);
    transition: all 0.3s ease;
}';

        file_put_contents($advanced_css, $css_content);
        echo '<p class="success">✓ Created advanced CSS file</p>';
        
        // Add CSS to functions.php
        $functions_file = $template_dir . '/functions.php';
        $functions_content = file_get_contents($functions_file);
        
        if (strpos($functions_content, 'plughaus-studios-advanced') === false) {
            $css_enqueue = "\n\n// Enqueue advanced CSS\nwp_enqueue_style('plughaus-studios-advanced', get_template_directory_uri() . '/assets/css/advanced.css', array('plughaus-studios-style'), '1.0.0');";
            
            $functions_content = str_replace(
                "wp_enqueue_style('plughaus-studios-style', get_stylesheet_uri(), array(), '1.0.0');",
                "wp_enqueue_style('plughaus-studios-style', get_stylesheet_uri(), array(), '1.0.0');" . $css_enqueue,
                $functions_content
            );
            
            file_put_contents($functions_file, $functions_content);
            echo '<p class="success">✓ Added advanced CSS to theme functions</p>';
        }
    } else {
        echo '<p class="info">→ Advanced CSS already exists</p>';
    }
    
    echo '</div>';
    
    // 4. Add JavaScript for filtering
    echo '<div class="section">';
    echo '<h2>4. Adding Interactive JavaScript</h2>';
    
    $js_dir = $template_dir . '/assets/js';
    if (!is_dir($js_dir)) {
        mkdir($js_dir, 0755, true);
        echo '<p class="success">✓ Created assets/js directory</p>';
    }
    
    $theme_js = $js_dir . '/theme.js';
    if (!file_exists($theme_js)) {
        $js_content = '/**
 * PlugHaus Studios Theme JavaScript
 */

document.addEventListener(\'DOMContentLoaded\', function() {
    
    // Plugin filtering functionality
    const filterButtons = document.querySelectorAll(\'.filter-btn\');
    const pluginCards = document.querySelectorAll(\'.plugin-card\');
    
    if (filterButtons.length > 0 && pluginCards.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener(\'click\', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove(\'active\'));
                
                // Add active class to clicked button
                this.classList.add(\'active\');
                
                // Get filter value
                const filter = this.getAttribute(\'data-filter\');
                
                // Filter plugin cards
                pluginCards.forEach(card => {
                    const cardStatus = card.getAttribute(\'data-status\');
                    
                    if (filter === \'all\' || cardStatus === filter) {
                        card.classList.remove(\'hidden\');
                        card.classList.add(\'visible\');
                    } else {
                        card.classList.remove(\'visible\');
                        card.classList.add(\'hidden\');
                    }
                });
            });
        });
    }
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll(\'a[href^="#"]\');
    anchorLinks.forEach(link => {
        link.addEventListener(\'click\', function(e) {
            const href = this.getAttribute(\'href\');
            if (href !== \'#\') {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: \'smooth\',
                        block: \'start\'
                    });
                }
            }
        });
    });
    
    // Plugin card hover effects
    const cards = document.querySelectorAll(\'.plugin-card\');
    cards.forEach(card => {
        card.addEventListener(\'mouseenter\', function() {
            this.style.transform = \'translateY(-8px)\';
        });
        
        card.addEventListener(\'mouseleave\', function() {
            this.style.transform = \'translateY(-4px)\';
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector(\'input[type="search"]\');
    if (searchInput) {
        searchInput.addEventListener(\'input\', function() {
            const searchTerm = this.value.toLowerCase();
            
            pluginCards.forEach(card => {
                const title = card.querySelector(\'.plugin-card-title\').textContent.toLowerCase();
                const excerpt = card.querySelector(\'.plugin-excerpt\').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || excerpt.includes(searchTerm)) {
                    card.style.display = \'block\';
                } else {
                    card.style.display = \'none\';
                }
            });
        });
    }
    
    // Lazy loading for images
    const images = document.querySelectorAll(\'img[data-src]\');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute(\'data-src\');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Progress bar for reading
    const progressBar = document.createElement(\'div\');
    progressBar.className = \'reading-progress\';
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #2563eb, #1d4ed8);
        z-index: 9999;
        transition: width 0.1s ease;
    `;
    document.body.appendChild(progressBar);
    
    window.addEventListener(\'scroll\', function() {
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollProgress = (window.scrollY / scrollHeight) * 100;
        progressBar.style.width = Math.min(scrollProgress, 100) + \'%\';
    });
    
    // Copy to clipboard functionality
    const copyButtons = document.querySelectorAll(\'.copy-btn\');
    copyButtons.forEach(button => {
        button.addEventListener(\'click\', function() {
            const text = this.getAttribute(\'data-copy\');
            navigator.clipboard.writeText(text).then(() => {
                this.textContent = \'Copied!\';
                setTimeout(() => {
                    this.textContent = \'Copy\';
                }, 2000);
            });
        });
    });
    
});

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}';

        file_put_contents($theme_js, $js_content);
        echo '<p class="success">✓ Created theme JavaScript file</p>';
    } else {
        echo '<p class="info">→ Theme JavaScript already exists</p>';
    }
    
    echo '</div>';
    
    // 5. Create testimonials and additional content
    echo '<div class="section">';
    echo '<h2>5. Adding Testimonials & Social Proof</h2>';
    
    // Create sample testimonials
    $testimonials = array(
        array(
            'content' => 'PlugHaus Property Management has completely transformed how I manage my rental properties. The automation features save me hours every week!',
            'author' => 'Sarah Johnson',
            'position' => 'Property Manager',
            'company' => 'Johnson Real Estate',
            'rating' => 5
        ),
        array(
            'content' => 'The payment processing integration is seamless. Our clients love the easy checkout process and we love the automated recurring billing.',
            'author' => 'Mike Chen',
            'position' => 'Business Owner',
            'company' => 'Tech Solutions LLC',
            'rating' => 5
        ),
        array(
            'content' => 'Outstanding support and documentation. The team at PlugHaus Studios really knows their stuff and delivers quality plugins.',
            'author' => 'Emily Rodriguez',
            'position' => 'Web Developer',
            'company' => 'Creative Agency Co',
            'rating' => 5
        )
    );
    
    foreach ($testimonials as $testimonial) {
        $existing = get_posts(array(
            'post_type' => 'phstudios_testimonial',
            'meta_query' => array(
                array(
                    'key' => '_testimonial_author',
                    'value' => $testimonial['author'],
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (empty($existing)) {
            $testimonial_post = array(
                'post_title' => $testimonial['author'] . ' - ' . $testimonial['company'],
                'post_content' => $testimonial['content'],
                'post_status' => 'publish',
                'post_type' => 'phstudios_testimonial'
            );
            
            $testimonial_id = wp_insert_post($testimonial_post);
            
            if ($testimonial_id && !is_wp_error($testimonial_id)) {
                update_post_meta($testimonial_id, '_testimonial_author', $testimonial['author']);
                update_post_meta($testimonial_id, '_testimonial_position', $testimonial['position']);
                update_post_meta($testimonial_id, '_testimonial_company', $testimonial['company']);
                update_post_meta($testimonial_id, '_testimonial_rating', $testimonial['rating']);
                
                echo '<p class="success">✓ Created testimonial from ' . $testimonial['author'] . '</p>';
            }
        } else {
            echo '<p class="info">→ Testimonial from ' . $testimonial['author'] . ' already exists</p>';
        }
    }
    
    echo '</div>';
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    echo '<div class="section">';
    echo '<h2>🎉 Site Polish Complete!</h2>';
    echo '<p class="success" style="font-size: 18px;">✓ Your site now has professional-grade features!</p>';
    
    echo '<h3>New Features Added:</h3>';
    echo '<ul>';
    echo '<li>✓ Individual plugin detail pages with full layouts</li>';
    echo '<li>✓ Plugin archive page with filtering capabilities</li>';
    echo '<li>✓ Responsive plugin card components</li>';
    echo '<li>✓ Advanced CSS with hover effects and animations</li>';
    echo '<li>✓ Interactive JavaScript for filtering and smooth scrolling</li>';
    echo '<li>✓ Customer testimonials and social proof</li>';
    echo '<li>✓ Reading progress bar and enhanced UX</li>';
    echo '<li>✓ Mobile-responsive design improvements</li>';
    echo '</ul>';
    
    echo '<a href="' . home_url() . '" class="btn">View Enhanced Site</a>';
    echo '<a href="' . home_url('/plugins/') . '" class="btn" style="background: #28a745;">Browse Plugins</a>';
    echo '<a href="' . admin_url() . '" class="btn" style="background: #6c757d;">WordPress Admin</a>';
    echo '</div>';
    
    echo '<div class="warning">';
    echo '<strong>Next Steps:</strong><br>';
    echo '• Clear any caching plugins<br>';
    echo '• Test the filtering functionality on the plugins page<br>';
    echo '• Visit individual plugin pages to see the new layouts<br>';
    echo '• Customize colors and styling in the theme customizer';
    echo '</div>';
    ?>
    
</body>
</html>