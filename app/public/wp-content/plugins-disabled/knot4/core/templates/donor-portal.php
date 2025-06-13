<?php
/**
 * Donor Portal Template
 * 
 * Comprehensive donor portal with dashboard, donations, events, profile, and receipts
 * 
 * @package Knot4
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if user is logged in
if (!is_user_logged_in()) {
    echo '<div class="knot4-login-required">';
    echo '<h3>' . __('Please log in to access your donor portal.', 'knot4') . '</h3>';
    echo do_shortcode('[knot4_donor_login]');
    echo '</div>';
    return;
}

// Get current user and donor data
$user_id = get_current_user_id();
$donor_id = Knot4_CRM::get_donor_id_by_user($user_id);

if (!$donor_id) {
    echo '<div class="knot4-no-donor-profile">';
    echo '<h3>' . __('Donor profile not found.', 'knot4') . '</h3>';
    echo '<p>' . __('Please contact us to set up your donor profile.', 'knot4') . '</p>';
    echo '</div>';
    return;
}

// Get donor information
$donor = get_post($donor_id);
$donor_email = get_post_meta($donor_id, '_knot4_donor_email', true);
$donor_first_name = get_post_meta($donor_id, '_knot4_donor_first_name', true);
$donor_last_name = get_post_meta($donor_id, '_knot4_donor_last_name', true);
$total_donated = get_post_meta($donor_id, '_knot4_total_donated', true) ?: 0;
$donation_count = get_post_meta($donor_id, '_knot4_donation_count', true) ?: 0;
$first_donation_date = get_post_meta($donor_id, '_knot4_first_donation_date', true);

// Get active tab
$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';

?>

<div class="knot4-donor-portal">
    
    <!-- Portal Header -->
    <div class="knot4-portal-header">
        <div class="knot4-portal-welcome">
            <h2><?php printf(__('Welcome back, %s!', 'knot4'), esc_html($donor_first_name)); ?></h2>
            <p class="knot4-portal-subtitle"><?php _e('Manage your donations, events, and profile', 'knot4'); ?></p>
        </div>
        <div class="knot4-portal-quick-stats">
            <div class="knot4-quick-stat">
                <span class="knot4-stat-value"><?php echo Knot4_Utilities::format_currency($total_donated); ?></span>
                <span class="knot4-stat-label"><?php _e('Total Donated', 'knot4'); ?></span>
            </div>
            <div class="knot4-quick-stat">
                <span class="knot4-stat-value"><?php echo intval($donation_count); ?></span>
                <span class="knot4-stat-label"><?php _e('Donations', 'knot4'); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Portal Navigation -->
    <div class="knot4-portal-nav">
        <nav class="knot4-tab-nav">
            <a href="?tab=dashboard" class="knot4-tab-link <?php echo $active_tab === 'dashboard' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-dashboard"></span>
                <?php _e('Dashboard', 'knot4'); ?>
            </a>
            <a href="?tab=donations" class="knot4-tab-link <?php echo $active_tab === 'donations' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-heart"></span>
                <?php _e('My Donations', 'knot4'); ?>
            </a>
            <a href="?tab=events" class="knot4-tab-link <?php echo $active_tab === 'events' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-calendar-alt"></span>
                <?php _e('Events', 'knot4'); ?>
            </a>
            <a href="?tab=profile" class="knot4-tab-link <?php echo $active_tab === 'profile' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-admin-users"></span>
                <?php _e('Profile', 'knot4'); ?>
            </a>
            <a href="?tab=receipts" class="knot4-tab-link <?php echo $active_tab === 'receipts' ? 'active' : ''; ?>">
                <span class="dashicons dashicons-media-document"></span>
                <?php _e('Tax Receipts', 'knot4'); ?>
            </a>
        </nav>
    </div>
    
    <!-- Portal Content -->
    <div class="knot4-portal-content">
        
        <?php if ($active_tab === 'dashboard'): ?>
        <!-- Dashboard Tab -->
        <div class="knot4-portal-tab" id="knot4-tab-dashboard">
            <div class="knot4-dashboard-grid">
                
                <!-- Donation Summary -->
                <div class="knot4-dashboard-card">
                    <h3><span class="dashicons dashicons-chart-area"></span> <?php _e('Donation Summary', 'knot4'); ?></h3>
                    <div class="knot4-summary-stats">
                        <div class="knot4-summary-item">
                            <strong><?php echo Knot4_Utilities::format_currency($total_donated); ?></strong>
                            <span><?php _e('Total Lifetime Giving', 'knot4'); ?></span>
                        </div>
                        <div class="knot4-summary-item">
                            <strong><?php echo intval($donation_count); ?></strong>
                            <span><?php _e('Total Donations', 'knot4'); ?></span>
                        </div>
                        <?php if ($total_donated > 0 && $donation_count > 0): ?>
                        <div class="knot4-summary-item">
                            <strong><?php echo Knot4_Utilities::format_currency($total_donated / $donation_count); ?></strong>
                            <span><?php _e('Average Donation', 'knot4'); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($first_donation_date): ?>
                        <div class="knot4-summary-item">
                            <strong><?php echo date_i18n(get_option('date_format'), strtotime($first_donation_date)); ?></strong>
                            <span><?php _e('First Donation', 'knot4'); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="knot4-dashboard-card">
                    <h3><span class="dashicons dashicons-clock"></span> <?php _e('Recent Activity', 'knot4'); ?></h3>
                    <div class="knot4-recent-activity" id="knot4-recent-activity">
                        <div class="knot4-loading"><?php _e('Loading recent activity...', 'knot4'); ?></div>
                    </div>
                </div>
                
                <!-- Upcoming Events -->
                <div class="knot4-dashboard-card">
                    <h3><span class="dashicons dashicons-calendar"></span> <?php _e('Upcoming Events', 'knot4'); ?></h3>
                    <div class="knot4-upcoming-events" id="knot4-upcoming-events">
                        <div class="knot4-loading"><?php _e('Loading upcoming events...', 'knot4'); ?></div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="knot4-dashboard-card">
                    <h3><span class="dashicons dashicons-admin-tools"></span> <?php _e('Quick Actions', 'knot4'); ?></h3>
                    <div class="knot4-quick-actions">
                        <a href="<?php echo home_url('/donate/'); ?>" class="knot4-action-btn knot4-btn-primary">
                            <span class="dashicons dashicons-heart"></span>
                            <?php _e('Make a Donation', 'knot4'); ?>
                        </a>
                        <a href="?tab=profile" class="knot4-action-btn knot4-btn-secondary">
                            <span class="dashicons dashicons-edit"></span>
                            <?php _e('Update Profile', 'knot4'); ?>
                        </a>
                        <a href="?tab=receipts" class="knot4-action-btn knot4-btn-secondary">
                            <span class="dashicons dashicons-download"></span>
                            <?php _e('Download Receipts', 'knot4'); ?>
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
        
        <?php elseif ($active_tab === 'donations'): ?>
        <!-- Donations Tab -->
        <div class="knot4-portal-tab" id="knot4-tab-donations">
            <div class="knot4-donations-header">
                <h3><?php _e('My Donation History', 'knot4'); ?></h3>
                <div class="knot4-donations-filters">
                    <select id="knot4-donation-year-filter">
                        <option value=""><?php _e('All Years', 'knot4'); ?></option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                    </select>
                    <select id="knot4-donation-status-filter">
                        <option value=""><?php _e('All Statuses', 'knot4'); ?></option>
                        <option value="completed"><?php _e('Completed', 'knot4'); ?></option>
                        <option value="pending"><?php _e('Pending', 'knot4'); ?></option>
                        <option value="failed"><?php _e('Failed', 'knot4'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="knot4-donations-list" id="knot4-donations-list">
                <div class="knot4-loading"><?php _e('Loading donation history...', 'knot4'); ?></div>
            </div>
        </div>
        
        <?php elseif ($active_tab === 'events'): ?>
        <!-- Events Tab -->
        <div class="knot4-portal-tab" id="knot4-tab-events">
            <div class="knot4-events-header">
                <h3><?php _e('Event Registration Management', 'knot4'); ?></h3>
                <div class="knot4-events-filters">
                    <select id="knot4-event-type-filter">
                        <option value=""><?php _e('All Event Types', 'knot4'); ?></option>
                        <option value="upcoming"><?php _e('Upcoming Events', 'knot4'); ?></option>
                        <option value="registered"><?php _e('My Registrations', 'knot4'); ?></option>
                        <option value="past"><?php _e('Past Events', 'knot4'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="knot4-events-grid" id="knot4-events-grid">
                <div class="knot4-loading"><?php _e('Loading events...', 'knot4'); ?></div>
            </div>
        </div>
        
        <?php elseif ($active_tab === 'profile'): ?>
        <!-- Profile Tab -->
        <div class="knot4-portal-tab" id="knot4-tab-profile">
            <div class="knot4-profile-header">
                <h3><?php _e('My Profile Information', 'knot4'); ?></h3>
                <p><?php _e('Keep your information up to date to receive important communications and tax receipts.', 'knot4'); ?></p>
            </div>
            
            <form id="knot4-profile-form" class="knot4-form">
                <?php wp_nonce_field('knot4_public_nonce', 'nonce'); ?>
                
                <div class="knot4-form-section">
                    <h4><?php _e('Personal Information', 'knot4'); ?></h4>
                    <div class="knot4-form-row">
                        <div class="knot4-form-group knot4-half">
                            <label for="first_name"><?php _e('First Name', 'knot4'); ?> *</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo esc_attr($donor_first_name); ?>" required>
                        </div>
                        <div class="knot4-form-group knot4-half">
                            <label for="last_name"><?php _e('Last Name', 'knot4'); ?> *</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo esc_attr($donor_last_name); ?>" required>
                        </div>
                    </div>
                    
                    <div class="knot4-form-group">
                        <label for="email"><?php _e('Email Address', 'knot4'); ?> *</label>
                        <input type="email" id="email" name="email" value="<?php echo esc_attr($donor_email); ?>" required>
                    </div>
                    
                    <div class="knot4-form-row">
                        <div class="knot4-form-group knot4-half">
                            <label for="phone"><?php _e('Phone Number', 'knot4'); ?></label>
                            <input type="tel" id="phone" name="phone" value="<?php echo esc_attr(get_post_meta($donor_id, '_knot4_donor_phone', true)); ?>">
                        </div>
                        <div class="knot4-form-group knot4-half">
                            <label for="organization"><?php _e('Organization', 'knot4'); ?></label>
                            <input type="text" id="organization" name="organization" value="<?php echo esc_attr(get_post_meta($donor_id, '_knot4_donor_organization', true)); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="knot4-form-section">
                    <h4><?php _e('Address Information', 'knot4'); ?></h4>
                    <div class="knot4-form-group">
                        <label for="address"><?php _e('Street Address', 'knot4'); ?></label>
                        <textarea id="address" name="address" rows="3"><?php echo esc_textarea(get_post_meta($donor_id, '_knot4_donor_address', true)); ?></textarea>
                    </div>
                    
                    <div class="knot4-form-row">
                        <div class="knot4-form-group knot4-third">
                            <label for="city"><?php _e('City', 'knot4'); ?></label>
                            <input type="text" id="city" name="city" value="<?php echo esc_attr(get_post_meta($donor_id, '_knot4_donor_city', true)); ?>">
                        </div>
                        <div class="knot4-form-group knot4-third">
                            <label for="state"><?php _e('State/Province', 'knot4'); ?></label>
                            <input type="text" id="state" name="state" value="<?php echo esc_attr(get_post_meta($donor_id, '_knot4_donor_state', true)); ?>">
                        </div>
                        <div class="knot4-form-group knot4-third">
                            <label for="zip"><?php _e('ZIP/Postal Code', 'knot4'); ?></label>
                            <input type="text" id="zip" name="zip" value="<?php echo esc_attr(get_post_meta($donor_id, '_knot4_donor_zip', true)); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="knot4-form-section">
                    <h4><?php _e('Communication Preferences', 'knot4'); ?></h4>
                    <div class="knot4-form-group">
                        <label for="communication_preference"><?php _e('Preferred Contact Method', 'knot4'); ?></label>
                        <select id="communication_preference" name="communication_preference">
                            <?php 
                            $current_pref = get_post_meta($donor_id, '_knot4_communication_preference', true);
                            foreach (Knot4_Utilities::get_communication_preferences() as $key => $label): 
                            ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($current_pref, $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="knot4-form-group">
                        <label class="knot4-checkbox-label">
                            <input type="checkbox" name="newsletter_opt_in" value="1" <?php checked(get_post_meta($donor_id, '_knot4_newsletter_opt_in', true), '1'); ?>>
                            <?php _e('Subscribe to newsletter and updates', 'knot4'); ?>
                        </label>
                    </div>
                </div>
                
                <div class="knot4-form-actions">
                    <button type="submit" class="knot4-btn knot4-btn-primary">
                        <?php _e('Save Changes', 'knot4'); ?>
                    </button>
                </div>
            </form>
        </div>
        
        <?php elseif ($active_tab === 'receipts'): ?>
        <!-- Receipts Tab -->
        <div class="knot4-portal-tab" id="knot4-tab-receipts">
            <div class="knot4-receipts-header">
                <h3><?php _e('Tax Receipts & Documentation', 'knot4'); ?></h3>
                <p><?php _e('Download and manage your tax receipts for donation deductions.', 'knot4'); ?></p>
            </div>
            
            <div class="knot4-receipt-filters">
                <select id="knot4-receipt-year">
                    <option value=""><?php _e('Select Tax Year', 'knot4'); ?></option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                </select>
                <button type="button" id="knot4-generate-annual-receipt" class="knot4-btn knot4-btn-primary">
                    <?php _e('Generate Annual Receipt', 'knot4'); ?>
                </button>
            </div>
            
            <div class="knot4-receipts-list" id="knot4-receipts-list">
                <div class="knot4-loading"><?php _e('Loading receipt information...', 'knot4'); ?></div>
            </div>
        </div>
        
        <?php endif; ?>
        
    </div>
    
</div>

<style>
/* Portal Base Styles */
.knot4-donor-portal {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Portal Header */
.knot4-portal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    color: white;
}

.knot4-portal-welcome h2 {
    margin: 0 0 5px 0;
    font-size: 1.8em;
    color: white;
}

.knot4-portal-subtitle {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1em;
}

.knot4-portal-quick-stats {
    display: flex;
    gap: 30px;
}

.knot4-quick-stat {
    text-align: center;
}

.knot4-stat-value {
    display: block;
    font-size: 1.5em;
    font-weight: bold;
    margin-bottom: 5px;
}

.knot4-stat-label {
    font-size: 0.9em;
    opacity: 0.8;
}

/* Tab Navigation */
.knot4-portal-nav {
    margin-bottom: 30px;
    border-bottom: 2px solid #f1f1f1;
}

.knot4-tab-nav {
    display: flex;
    gap: 0;
}

.knot4-tab-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 15px 25px;
    text-decoration: none;
    color: #666;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    font-weight: 500;
}

.knot4-tab-link:hover,
.knot4-tab-link.active {
    color: #667eea;
    border-bottom-color: #667eea;
    background: #f8f9ff;
}

.knot4-tab-link .dashicons {
    font-size: 18px;
}

/* Dashboard Grid */
.knot4-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.knot4-dashboard-card {
    background: white;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.knot4-dashboard-card h3 {
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #333;
    font-size: 1.2em;
}

.knot4-dashboard-card .dashicons {
    color: #667eea;
}

/* Summary Stats */
.knot4-summary-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.knot4-summary-item {
    text-align: center;
    padding: 15px;
    background: #f8f9ff;
    border-radius: 6px;
}

.knot4-summary-item strong {
    display: block;
    font-size: 1.3em;
    color: #667eea;
    margin-bottom: 5px;
}

.knot4-summary-item span {
    font-size: 0.9em;
    color: #666;
}

/* Quick Actions */
.knot4-quick-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.knot4-action-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.knot4-btn-primary {
    background: #667eea;
    color: white;
}

.knot4-btn-primary:hover {
    background: #5a6fd8;
}

.knot4-btn-secondary {
    background: #f1f1f1;
    color: #333;
}

.knot4-btn-secondary:hover {
    background: #e1e1e1;
}

/* Form Styles */
.knot4-form {
    background: white;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
    padding: 30px;
}

.knot4-form-section {
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid #f1f1f1;
}

.knot4-form-section:last-child {
    border-bottom: none;
}

.knot4-form-section h4 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 1.1em;
}

.knot4-form-row {
    display: flex;
    gap: 20px;
}

.knot4-form-group {
    margin-bottom: 20px;
}

.knot4-form-group.knot4-half {
    flex: 1;
}

.knot4-form-group.knot4-third {
    flex: 1;
}

.knot4-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.knot4-form-group input,
.knot4-form-group select,
.knot4-form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.knot4-form-group input:focus,
.knot4-form-group select:focus,
.knot4-form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.knot4-checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: normal;
}

.knot4-checkbox-label input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.knot4-form-actions {
    margin-top: 30px;
    text-align: right;
}

.knot4-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

/* Filter Headers */
.knot4-donations-header,
.knot4-events-header,
.knot4-receipts-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: white;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
}

.knot4-donations-filters,
.knot4-events-filters,
.knot4-receipt-filters {
    display: flex;
    gap: 15px;
}

.knot4-donations-filters select,
.knot4-events-filters select,
.knot4-receipt-filters select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Loading State */
.knot4-loading {
    text-align: center;
    padding: 40px;
    color: #666;
    font-style: italic;
}

/* No Data States */
.knot4-login-required,
.knot4-no-donor-profile {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .knot4-donor-portal {
        padding: 15px;
    }
    
    .knot4-portal-header {
        flex-direction: column;
        gap: 20px;
        padding: 20px;
    }
    
    .knot4-portal-quick-stats {
        gap: 20px;
    }
    
    .knot4-tab-nav {
        flex-wrap: wrap;
    }
    
    .knot4-tab-link {
        padding: 12px 15px;
        font-size: 0.9em;
    }
    
    .knot4-dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .knot4-form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .knot4-donations-header,
    .knot4-events-header,
    .knot4-receipts-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .knot4-donations-filters,
    .knot4-events-filters,
    .knot4-receipt-filters {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    
    // Load dashboard data on page load
    if (window.location.search.indexOf('tab=dashboard') !== -1 || window.location.search.indexOf('tab=') === -1) {
        loadDashboardData();
    }
    
    // Profile form submission
    $('#knot4-profile-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'knot4_update_donor_profile',
            nonce: $('[name="nonce"]').val(),
            profile_data: {
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                organization: $('#organization').val(),
                address: $('#address').val(),
                city: $('#city').val(),
                state: $('#state').val(),
                zip: $('#zip').val(),
                communication_preference: $('#communication_preference').val(),
                newsletter_opt_in: $('[name="newsletter_opt_in"]:checked').length ? 1 : 0
            }
        };
        
        $.post(knot4_ajax.ajaxurl, formData, function(response) {
            if (response.success) {
                alert('<?php _e('Profile updated successfully!', 'knot4'); ?>');
            } else {
                alert(response.data.message || '<?php _e('Failed to update profile.', 'knot4'); ?>');
            }
        });
    });
    
    // Load dashboard data
    function loadDashboardData() {
        $.post(knot4_ajax.ajaxurl, {
            action: 'knot4_get_donor_dashboard',
            nonce: knot4_ajax.nonce
        }, function(response) {
            if (response.success) {
                updateRecentActivity(response.data.dashboard.recent_donations);
                // Add more dashboard updates here
            }
        });
    }
    
    // Update recent activity section
    function updateRecentActivity(donations) {
        var html = '';
        if (donations && donations.length > 0) {
            donations.forEach(function(donation) {
                html += '<div class="knot4-activity-item">';
                html += '<strong>$' + parseFloat(donation.amount).toFixed(2) + '</strong> ';
                html += '<span>on ' + donation.created_at + '</span>';
                html += '</div>';
            });
        } else {
            html = '<p><?php _e('No recent activity found.', 'knot4'); ?></p>';
        }
        $('#knot4-recent-activity').html(html);
    }
    
});
</script>

<?php
// Add AJAX script variables
wp_localize_script('jquery', 'knot4_ajax', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('knot4_public_nonce')
));
?>