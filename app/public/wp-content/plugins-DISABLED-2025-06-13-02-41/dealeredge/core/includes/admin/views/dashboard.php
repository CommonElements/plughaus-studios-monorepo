<?php
/**
 * DealerEdge Dashboard Admin View
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get dashboard stats
$vehicle_count = wp_count_posts('de_vehicle');
$customer_count = wp_count_posts('de_customer');
$work_order_count = wp_count_posts('de_work_order');
$sale_count = wp_count_posts('de_sale');
$part_count = wp_count_posts('de_part');

$active_vehicles = isset($vehicle_count->publish) ? $vehicle_count->publish : 0;
$active_customers = isset($customer_count->publish) ? $customer_count->publish : 0;
$active_work_orders = isset($work_order_count->publish) ? $work_order_count->publish : 0;
$active_sales = isset($sale_count->publish) ? $sale_count->publish : 0;
$active_parts = isset($part_count->publish) ? $part_count->publish : 0;
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="dealeredge-dashboard">
        <!-- Welcome Section -->
        <div class="dealeredge-welcome-panel">
            <div class="welcome-panel-content">
                <h2><?php _e('Welcome to DealerEdge!', 'dealeredge'); ?></h2>
                <p class="about-description">
                    <?php _e('Manage your auto shop or small dealership with ease. Track vehicles, customers, work orders, sales, and parts inventory all in one place.', 'dealeredge'); ?>
                </p>
                
                <div class="welcome-panel-column-container">
                    <div class="welcome-panel-column">
                        <h3><?php _e('Auto Shop Features', 'dealeredge'); ?></h3>
                        <ul>
                            <li><?php _e('Work order management', 'dealeredge'); ?></li>
                            <li><?php _e('Customer vehicle history', 'dealeredge'); ?></li>
                            <li><?php _e('Parts inventory tracking', 'dealeredge'); ?></li>
                            <li><?php _e('Service scheduling', 'dealeredge'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="welcome-panel-column">
                        <h3><?php _e('Dealership Features', 'dealeredge'); ?></h3>
                        <ul>
                            <li><?php _e('Vehicle inventory management', 'dealeredge'); ?></li>
                            <li><?php _e('Sales tracking & reporting', 'dealeredge'); ?></li>
                            <li><?php _e('Customer lead management', 'dealeredge'); ?></li>
                            <li><?php _e('Financing integration ready', 'dealeredge'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="welcome-panel-column">
                        <h3><?php _e('Get Started', 'dealeredge'); ?></h3>
                        <p>
                            <a href="<?php echo admin_url('post-new.php?post_type=de_vehicle'); ?>" class="button button-primary">
                                <?php _e('Add Your First Vehicle', 'dealeredge'); ?>
                            </a>
                        </p>
                        <p>
                            <a href="<?php echo admin_url('post-new.php?post_type=de_customer'); ?>" class="button">
                                <?php _e('Add a Customer', 'dealeredge'); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Dashboard -->
        <div class="dealeredge-stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🚗</div>
                <div class="stat-content">
                    <h3><?php echo number_format($active_vehicles); ?></h3>
                    <p><?php _e('Vehicles', 'dealeredge'); ?></p>
                    <a href="<?php echo admin_url('edit.php?post_type=de_vehicle'); ?>" class="stat-link">
                        <?php _e('View All', 'dealeredge'); ?>
                    </a>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3><?php echo number_format($active_customers); ?></h3>
                    <p><?php _e('Customers', 'dealeredge'); ?></p>
                    <a href="<?php echo admin_url('edit.php?post_type=de_customer'); ?>" class="stat-link">
                        <?php _e('View All', 'dealeredge'); ?>
                    </a>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🔧</div>
                <div class="stat-content">
                    <h3><?php echo number_format($active_work_orders); ?></h3>
                    <p><?php _e('Work Orders', 'dealeredge'); ?></p>
                    <a href="<?php echo admin_url('edit.php?post_type=de_work_order'); ?>" class="stat-link">
                        <?php _e('View All', 'dealeredge'); ?>
                    </a>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-content">
                    <h3><?php echo number_format($active_sales); ?></h3>
                    <p><?php _e('Sales', 'dealeredge'); ?></p>
                    <a href="<?php echo admin_url('edit.php?post_type=de_sale'); ?>" class="stat-link">
                        <?php _e('View All', 'dealeredge'); ?>
                    </a>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⚙️</div>
                <div class="stat-content">
                    <h3><?php echo number_format($active_parts); ?></h3>
                    <p><?php _e('Parts', 'dealeredge'); ?></p>
                    <a href="<?php echo admin_url('edit.php?post_type=de_part'); ?>" class="stat-link">
                        <?php _e('View All', 'dealeredge'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="dealeredge-recent-activity">
            <h2><?php _e('Recent Activity', 'dealeredge'); ?></h2>
            
            <div class="activity-grid">
                <div class="activity-column">
                    <h3><?php _e('Recent Work Orders', 'dealeredge'); ?></h3>
                    <?php
                    $recent_work_orders = get_posts(array(
                        'post_type' => 'de_work_order',
                        'posts_per_page' => 5,
                        'post_status' => 'publish'
                    ));
                    
                    if ($recent_work_orders) {
                        echo '<ul class="recent-items">';
                        foreach ($recent_work_orders as $work_order) {
                            echo '<li>';
                            echo '<a href="' . get_edit_post_link($work_order->ID) . '">';
                            echo esc_html($work_order->post_title);
                            echo '</a>';
                            echo '<span class="item-date">' . get_the_date('M j', $work_order->ID) . '</span>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>' . __('No work orders yet.', 'dealeredge') . '</p>';
                    }
                    ?>
                </div>
                
                <div class="activity-column">
                    <h3><?php _e('Recent Sales', 'dealeredge'); ?></h3>
                    <?php
                    $recent_sales = get_posts(array(
                        'post_type' => 'de_sale',
                        'posts_per_page' => 5,
                        'post_status' => 'publish'
                    ));
                    
                    if ($recent_sales) {
                        echo '<ul class="recent-items">';
                        foreach ($recent_sales as $sale) {
                            echo '<li>';
                            echo '<a href="' . get_edit_post_link($sale->ID) . '">';
                            echo esc_html($sale->post_title);
                            echo '</a>';
                            echo '<span class="item-date">' . get_the_date('M j', $sale->ID) . '</span>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>' . __('No sales yet.', 'dealeredge') . '</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="dealeredge-quick-actions">
            <h2><?php _e('Quick Actions', 'dealeredge'); ?></h2>
            <div class="quick-actions-grid">
                <a href="<?php echo admin_url('post-new.php?post_type=de_vehicle'); ?>" class="quick-action">
                    <span class="action-icon">🚗</span>
                    <span class="action-text"><?php _e('Add Vehicle', 'dealeredge'); ?></span>
                </a>
                
                <a href="<?php echo admin_url('post-new.php?post_type=de_customer'); ?>" class="quick-action">
                    <span class="action-icon">👤</span>
                    <span class="action-text"><?php _e('Add Customer', 'dealeredge'); ?></span>
                </a>
                
                <a href="<?php echo admin_url('post-new.php?post_type=de_work_order'); ?>" class="quick-action">
                    <span class="action-icon">🔧</span>
                    <span class="action-text"><?php _e('Create Work Order', 'dealeredge'); ?></span>
                </a>
                
                <a href="<?php echo admin_url('post-new.php?post_type=de_sale'); ?>" class="quick-action">
                    <span class="action-icon">💰</span>
                    <span class="action-text"><?php _e('Record Sale', 'dealeredge'); ?></span>
                </a>
                
                <a href="<?php echo admin_url('post-new.php?post_type=de_part'); ?>" class="quick-action">
                    <span class="action-icon">⚙️</span>
                    <span class="action-text"><?php _e('Add Part', 'dealeredge'); ?></span>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=dealeredge-settings'); ?>" class="quick-action">
                    <span class="action-icon">⚙️</span>
                    <span class="action-text"><?php _e('Settings', 'dealeredge'); ?></span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.dealeredge-dashboard {
    max-width: 1200px;
}

.dealeredge-welcome-panel {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin: 20px 0;
    padding: 23px 10px;
}

.dealeredge-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
    transition: box-shadow 0.2s;
}

.stat-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stat-icon {
    font-size: 2em;
    margin-bottom: 10px;
}

.stat-content h3 {
    font-size: 2em;
    margin: 0 0 5px 0;
    color: #135e96;
}

.stat-content p {
    margin: 0 0 10px 0;
    color: #646970;
}

.stat-link {
    text-decoration: none;
    color: #2271b1;
}

.dealeredge-recent-activity {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin: 20px 0;
    padding: 20px;
}

.activity-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.recent-items {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-items li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f1;
}

.recent-items li:last-child {
    border-bottom: none;
}

.item-date {
    color: #646970;
    font-size: 0.9em;
}

.dealeredge-quick-actions {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin: 20px 0;
    padding: 20px;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border: 1px solid #e1e5e9;
    border-radius: 4px;
    text-decoration: none;
    color: #2271b1;
    transition: all 0.2s;
}

.quick-action:hover {
    background: #e7f3ff;
    border-color: #2271b1;
    color: #135e96;
}

.action-icon {
    font-size: 1.5em;
    margin-bottom: 8px;
}

.action-text {
    font-weight: 500;
    text-align: center;
}

@media (max-width: 768px) {
    .activity-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>