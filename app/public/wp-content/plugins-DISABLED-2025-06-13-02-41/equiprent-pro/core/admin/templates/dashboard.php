<?php
/**
 * Admin Dashboard Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check user permissions
if (!current_user_can('view_equipment')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'equiprent-pro'));
}

// Get statistics
$equipment_stats = ERP_Equipment::get_statistics();
$booking_stats = ERP_Booking::get_statistics();
$customer_stats = ERP_Customer::get_statistics();

// Get recent bookings
$recent_bookings = ERP_Booking::get_all(array('limit' => 5, 'orderby' => 'created_at', 'order' => 'DESC'));

// Get equipment needing maintenance
global $wpdb;
$maintenance_due = $wpdb->get_results("
    SELECT * FROM {$wpdb->prefix}erp_equipment 
    WHERE next_maintenance_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    AND status != 'retired'
    ORDER BY next_maintenance_date ASC
    LIMIT 5
");

?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('EquipRent Pro Dashboard', 'equiprent-pro'); ?></h1>
    
    <!-- Statistics Cards -->
    <div class="erp-dashboard-stats">
        <div class="erp-stats-grid">
            <!-- Equipment Stats -->
            <div class="erp-stat-card equipment">
                <div class="erp-stat-icon">
                    <span class="dashicons dashicons-hammer"></span>
                </div>
                <div class="erp-stat-content">
                    <h3><?php echo number_format($equipment_stats['total']); ?></h3>
                    <p><?php _e('Total Equipment', 'equiprent-pro'); ?></p>
                    <div class="erp-stat-breakdown">
                        <span class="available"><?php echo $equipment_stats['available']; ?> <?php _e('Available', 'equiprent-pro'); ?></span>
                        <span class="rented"><?php echo $equipment_stats['rented']; ?> <?php _e('Rented', 'equiprent-pro'); ?></span>
                        <?php if ($equipment_stats['maintenance'] > 0): ?>
                        <span class="maintenance"><?php echo $equipment_stats['maintenance']; ?> <?php _e('Maintenance', 'equiprent-pro'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Booking Stats -->
            <div class="erp-stat-card bookings">
                <div class="erp-stat-icon">
                    <span class="dashicons dashicons-calendar-alt"></span>
                </div>
                <div class="erp-stat-content">
                    <h3><?php echo number_format($booking_stats['total']); ?></h3>
                    <p><?php _e('Total Bookings', 'equiprent-pro'); ?></p>
                    <div class="erp-stat-breakdown">
                        <span class="pending"><?php echo $booking_stats['pending']; ?> <?php _e('Pending', 'equiprent-pro'); ?></span>
                        <span class="confirmed"><?php echo $booking_stats['confirmed']; ?> <?php _e('Confirmed', 'equiprent-pro'); ?></span>
                        <span class="in-progress"><?php echo $booking_stats['in_progress']; ?> <?php _e('In Progress', 'equiprent-pro'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Customer Stats -->
            <div class="erp-stat-card customers">
                <div class="erp-stat-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="erp-stat-content">
                    <h3><?php echo number_format($customer_stats['total']); ?></h3>
                    <p><?php _e('Total Customers', 'equiprent-pro'); ?></p>
                    <div class="erp-stat-breakdown">
                        <span class="active"><?php echo $customer_stats['active']; ?> <?php _e('Active', 'equiprent-pro'); ?></span>
                        <?php if ($customer_stats['overdue'] > 0): ?>
                        <span class="overdue"><?php echo $customer_stats['overdue']; ?> <?php _e('Overdue', 'equiprent-pro'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Revenue Stats -->
            <div class="erp-stat-card revenue">
                <div class="erp-stat-icon">
                    <span class="dashicons dashicons-money-alt"></span>
                </div>
                <div class="erp-stat-content">
                    <h3><?php echo ERP_Utilities::format_currency($booking_stats['total_revenue']); ?></h3>
                    <p><?php _e('Total Revenue', 'equiprent-pro'); ?></p>
                    <div class="erp-stat-breakdown">
                        <?php if ($booking_stats['pending_revenue'] > 0): ?>
                        <span class="pending"><?php echo ERP_Utilities::format_currency($booking_stats['pending_revenue']); ?> <?php _e('Pending', 'equiprent-pro'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="erp-dashboard-content">
        <div class="erp-dashboard-grid">
            
            <!-- Recent Bookings -->
            <div class="erp-dashboard-widget">
                <div class="erp-widget-header">
                    <h3><?php _e('Recent Bookings', 'equiprent-pro'); ?></h3>
                    <a href="<?php echo admin_url('admin.php?page=equiprent-bookings'); ?>" class="button button-secondary">
                        <?php _e('View All', 'equiprent-pro'); ?>
                    </a>
                </div>
                <div class="erp-widget-content">
                    <?php if (!empty($recent_bookings)): ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Booking #', 'equiprent-pro'); ?></th>
                                <th><?php _e('Customer', 'equiprent-pro'); ?></th>
                                <th><?php _e('Dates', 'equiprent-pro'); ?></th>
                                <th><?php _e('Status', 'equiprent-pro'); ?></th>
                                <th><?php _e('Total', 'equiprent-pro'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_bookings as $booking): ?>
                            <?php $customer = new ERP_Customer($booking->customer_id); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=equiprent-add-booking&action=edit&id=' . $booking->id); ?>">
                                        <?php echo esc_html($booking->booking_number); ?>
                                    </a>
                                </td>
                                <td><?php echo esc_html($customer->get_display_name()); ?></td>
                                <td>
                                    <?php echo ERP_Utilities::format_date($booking->start_date); ?> - 
                                    <?php echo ERP_Utilities::format_date($booking->end_date); ?>
                                </td>
                                <td><?php echo ERP_Utilities::get_status_badge($booking->status, 'booking'); ?></td>
                                <td><?php echo ERP_Utilities::format_currency($booking->total_amount); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="erp-no-data"><?php _e('No bookings found.', 'equiprent-pro'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Maintenance Alerts -->
            <div class="erp-dashboard-widget">
                <div class="erp-widget-header">
                    <h3><?php _e('Maintenance Due', 'equiprent-pro'); ?></h3>
                    <a href="<?php echo admin_url('admin.php?page=equiprent-maintenance'); ?>" class="button button-secondary">
                        <?php _e('View All', 'equiprent-pro'); ?>
                    </a>
                </div>
                <div class="erp-widget-content">
                    <?php if (!empty($maintenance_due)): ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Equipment', 'equiprent-pro'); ?></th>
                                <th><?php _e('Due Date', 'equiprent-pro'); ?></th>
                                <th><?php _e('Status', 'equiprent-pro'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($maintenance_due as $equipment): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo admin_url('post.php?post=' . $equipment->id . '&action=edit&post_type=erp_equipment'); ?>">
                                        <?php echo esc_html($equipment->name); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php 
                                    $due_date = strtotime($equipment->next_maintenance_date);
                                    $is_overdue = $due_date < time();
                                    ?>
                                    <span class="<?php echo $is_overdue ? 'erp-overdue' : 'erp-due-soon'; ?>">
                                        <?php echo ERP_Utilities::format_date($equipment->next_maintenance_date); ?>
                                        <?php if ($is_overdue): ?>
                                        <span class="erp-overdue-label"><?php _e('OVERDUE', 'equiprent-pro'); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td><?php echo ERP_Utilities::get_status_badge($equipment->status, 'equipment'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="erp-no-data"><?php _e('No maintenance due.', 'equiprent-pro'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="erp-dashboard-widget">
                <div class="erp-widget-header">
                    <h3><?php _e('Quick Actions', 'equiprent-pro'); ?></h3>
                </div>
                <div class="erp-widget-content">
                    <div class="erp-quick-actions">
                        <a href="<?php echo admin_url('admin.php?page=equiprent-add-booking'); ?>" class="erp-quick-action">
                            <span class="dashicons dashicons-plus-alt2"></span>
                            <?php _e('New Booking', 'equiprent-pro'); ?>
                        </a>
                        <a href="<?php echo admin_url('post-new.php?post_type=erp_equipment'); ?>" class="erp-quick-action">
                            <span class="dashicons dashicons-hammer"></span>
                            <?php _e('Add Equipment', 'equiprent-pro'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=equiprent-customers&action=add'); ?>" class="erp-quick-action">
                            <span class="dashicons dashicons-groups"></span>
                            <?php _e('Add Customer', 'equiprent-pro'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=equiprent-reports'); ?>" class="erp-quick-action">
                            <span class="dashicons dashicons-chart-area"></span>
                            <?php _e('View Reports', 'equiprent-pro'); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pro Features Promotion (if not pro) -->
            <?php if (!EquipRent_Pro::get_instance()->is_pro()): ?>
            <div class="erp-dashboard-widget erp-pro-promotion">
                <div class="erp-widget-header">
                    <h3><?php _e('Upgrade to Pro', 'equiprent-pro'); ?></h3>
                </div>
                <div class="erp-widget-content">
                    <div class="erp-pro-features">
                        <h4><?php _e('Unlock Advanced Features:', 'equiprent-pro'); ?></h4>
                        <ul>
                            <li><span class="dashicons dashicons-chart-line"></span> <?php _e('Advanced Analytics Dashboard', 'equiprent-pro'); ?></li>
                            <li><span class="dashicons dashicons-location"></span> <?php _e('Route Optimization', 'equiprent-pro'); ?></li>
                            <li><span class="dashicons dashicons-smartphone"></span> <?php _e('Mobile Field App', 'equiprent-pro'); ?></li>
                            <li><span class="dashicons dashicons-admin-tools"></span> <?php _e('QR Code Tracking', 'equiprent-pro'); ?></li>
                            <li><span class="dashicons dashicons-money-alt"></span> <?php _e('Payment Automation', 'equiprent-pro'); ?></li>
                        </ul>
                        <a href="https://vireodesigns.com/plugins/equiprent-pro" target="_blank" class="button button-primary">
                            <?php _e('Upgrade Now', 'equiprent-pro'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.erp-dashboard-stats {
    margin: 20px 0;
}

.erp-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.erp-stat-card {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.erp-stat-icon {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.erp-stat-card.equipment .erp-stat-icon { background: #e7f3ff; color: #0073aa; }
.erp-stat-card.bookings .erp-stat-icon { background: #fff2e7; color: #d63638; }
.erp-stat-card.customers .erp-stat-icon { background: #e7f7e7; color: #00a32a; }
.erp-stat-card.revenue .erp-stat-icon { background: #f7e7ff; color: #8c44c6; }

.erp-stat-icon .dashicons {
    font-size: 24px;
    width: 24px;
    height: 24px;
}

.erp-stat-content h3 {
    margin: 0 0 5px 0;
    font-size: 32px;
    font-weight: 600;
    line-height: 1;
}

.erp-stat-content p {
    margin: 0 0 10px 0;
    color: #646970;
    font-size: 14px;
}

.erp-stat-breakdown {
    font-size: 12px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.erp-stat-breakdown span {
    padding: 2px 6px;
    border-radius: 2px;
    background: #f0f0f1;
    color: #2c3338;
}

.erp-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
}

.erp-dashboard-widget {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
}

.erp-widget-header {
    padding: 15px 20px;
    border-bottom: 1px solid #c3c4c7;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.erp-widget-header h3 {
    margin: 0;
    font-size: 16px;
}

.erp-widget-content {
    padding: 20px;
}

.erp-widget-content .wp-list-table {
    border: none;
}

.erp-no-data {
    text-align: center;
    color: #646970;
    font-style: italic;
    margin: 20px 0;
}

.erp-quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.erp-quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    text-decoration: none;
    color: #2c3338;
    transition: all 0.3s ease;
}

.erp-quick-action:hover {
    background: #f6f7f7;
    text-decoration: none;
    color: #0073aa;
}

.erp-quick-action .dashicons {
    font-size: 24px;
    margin-bottom: 8px;
}

.erp-pro-promotion {
    border-color: #8c44c6;
}

.erp-pro-promotion .erp-widget-header {
    background: #f7e7ff;
    border-color: #8c44c6;
}

.erp-pro-features ul {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.erp-pro-features li {
    padding: 5px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.erp-pro-features .dashicons {
    color: #8c44c6;
    font-size: 16px;
}

.erp-overdue {
    color: #d63638;
}

.erp-due-soon {
    color: #dba617;
}

.erp-overdue-label {
    background: #d63638;
    color: #fff;
    padding: 2px 4px;
    border-radius: 2px;
    font-size: 10px;
    margin-left: 5px;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success { background: #00a32a; color: #fff; }
.badge-warning { background: #dba617; color: #fff; }
.badge-danger { background: #d63638; color: #fff; }
.badge-info { background: #0073aa; color: #fff; }
.badge-primary { background: #2271b1; color: #fff; }
.badge-secondary { background: #646970; color: #fff; }
</style>