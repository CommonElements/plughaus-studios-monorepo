<?php
/**
 * StudioSnap Email Handler - Comprehensive email automation system
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SS_Email_Handler {
    
    /**
     * Send booking inquiry email to client
     */
    public static function send_booking_inquiry_email($booking_id) {
        $booking = get_post($booking_id);
        if (!$booking) return false;
        
        $client_email = get_post_meta($booking_id, '_ss_client_email', true);
        $client_name = get_post_meta($booking_id, '_ss_client_name', true);
        $session_date = get_post_meta($booking_id, '_ss_session_date', true);
        $session_time = get_post_meta($booking_id, '_ss_session_start_time', true);
        $session_type = get_post_meta($booking_id, '_ss_session_type', true);
        
        $subject = sprintf(__('Photography Session Inquiry Received - %s', 'studiosnap'), get_bloginfo('name'));
        
        $message = self::get_email_template('booking_inquiry', array(
            'client_name' => $client_name,
            'session_date' => date('F j, Y', strtotime($session_date)),
            'session_time' => date('g:i A', strtotime($session_time)),
            'session_type' => ucfirst($session_type),
            'booking_id' => $booking_id,
            'studio_name' => get_bloginfo('name'),
            'studio_email' => get_option('admin_email'),
            'studio_phone' => get_option('ss_studio_phone', ''),
            'booking_hash' => get_post_meta($booking_id, '_ss_booking_hash', true)
        ));
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        return wp_mail($client_email, $subject, $message, $headers);
    }
    
    /**
     * Send booking confirmation email
     */
    public static function send_booking_confirmation_email($booking_id) {
        $booking = get_post($booking_id);
        if (!$booking) return false;
        
        $client_email = get_post_meta($booking_id, '_ss_client_email', true);
        $client_name = get_post_meta($booking_id, '_ss_client_name', true);
        $session_date = get_post_meta($booking_id, '_ss_session_date', true);
        $session_time = get_post_meta($booking_id, '_ss_session_start_time', true);
        $session_type = get_post_meta($booking_id, '_ss_session_type', true);
        $session_location = get_post_meta($booking_id, '_ss_session_location', true);
        $total_price = get_post_meta($booking_id, '_ss_total_price', true);
        
        $subject = sprintf(__('Session Confirmed! - %s', 'studiosnap'), get_bloginfo('name'));
        
        // Generate contract and payment links
        $contract_link = self::generate_contract_link($booking_id);
        $payment_link = self::generate_payment_link($booking_id);
        
        $message = self::get_email_template('booking_confirmation', array(
            'client_name' => $client_name,
            'session_date' => date('F j, Y', strtotime($session_date)),
            'session_time' => date('g:i A', strtotime($session_time)),
            'session_type' => ucfirst($session_type),
            'session_location' => $session_location === 'on_location' ? __('On Location', 'studiosnap') : __('Studio', 'studiosnap'),
            'total_price' => SS_Utilities::format_currency($total_price),
            'booking_id' => $booking_id,
            'contract_link' => $contract_link,
            'payment_link' => $payment_link,
            'studio_name' => get_bloginfo('name'),
            'studio_address' => get_option('ss_studio_address', ''),
            'preparation_tips' => self::get_session_preparation_tips($session_type)
        ));
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        return wp_mail($client_email, $subject, $message, $headers);
    }
    
    /**
     * Send session completed email with gallery access
     */
    public static function send_session_completed_email($booking_id) {
        $booking = get_post($booking_id);
        if (!$booking) return false;
        
        $client_email = get_post_meta($booking_id, '_ss_client_email', true);
        $client_name = get_post_meta($booking_id, '_ss_client_name', true);
        $session_type = get_post_meta($booking_id, '_ss_session_type', true);
        
        // Create gallery if doesn't exist
        $gallery_id = self::get_or_create_session_gallery($booking_id);
        $gallery_link = self::generate_gallery_access_link($gallery_id);
        
        $subject = sprintf(__('Your Photos Are Ready! - %s', 'studiosnap'), get_bloginfo('name'));
        
        $message = self::get_email_template('session_completed', array(
            'client_name' => $client_name,
            'session_type' => ucfirst($session_type),
            'gallery_link' => $gallery_link,
            'gallery_password' => get_post_meta($gallery_id, '_ss_gallery_password', true),
            'download_deadline' => date('F j, Y', strtotime('+30 days')),
            'studio_name' => get_bloginfo('name')
        ));
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        return wp_mail($client_email, $subject, $message, $headers);
    }
    
    /**
     * Send admin notification for new booking
     */
    public static function send_admin_booking_notification($booking_id) {
        $booking = get_post($booking_id);
        if (!$booking) return false;
        
        $admin_email = get_option('admin_email');
        $client_name = get_post_meta($booking_id, '_ss_client_name', true);
        $client_email = get_post_meta($booking_id, '_ss_client_email', true);
        $client_phone = get_post_meta($booking_id, '_ss_client_phone', true);
        $session_date = get_post_meta($booking_id, '_ss_session_date', true);
        $session_time = get_post_meta($booking_id, '_ss_session_start_time', true);
        $session_type = get_post_meta($booking_id, '_ss_session_type', true);
        $special_requests = get_post_meta($booking_id, '_ss_special_requests', true);
        
        $subject = sprintf(__('[New Booking] %s - %s Session', 'studiosnap'), $client_name, ucfirst($session_type));
        
        $message = sprintf(__('
<h2>New Photography Session Booking</h2>

<strong>Client Information:</strong><br>
Name: %s<br>
Email: %s<br>
Phone: %s<br>

<strong>Session Details:</strong><br>
Type: %s<br>
Date: %s<br>
Time: %s<br>

<strong>Special Requests:</strong><br>
%s

<strong>Actions:</strong><br>
<a href="%s">View Booking in Admin</a><br>
<a href="%s">Confirm Booking</a><br>
<a href="%s">Decline Booking</a>

', 'studiosnap'), 
            $client_name,
            $client_email,
            $client_phone,
            ucfirst($session_type),
            date('F j, Y', strtotime($session_date)),
            date('g:i A', strtotime($session_time)),
            $special_requests,
            admin_url('post.php?post=' . $booking_id . '&action=edit'),
            admin_url('admin.php?action=ss_confirm_booking&booking_id=' . $booking_id),
            admin_url('admin.php?action=ss_decline_booking&booking_id=' . $booking_id)
        );
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: StudioSnap <noreply@' . $_SERVER['HTTP_HOST'] . '>'
        );
        
        return wp_mail($admin_email, $subject, $message, $headers);
    }
    
    /**
     * Send booking reminders (scheduled daily)
     */
    public static function send_booking_reminders() {
        // Send reminders 24 hours before session
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        $upcoming_bookings = get_posts(array(
            'post_type' => 'ss_booking',
            'post_status' => 'ss_confirmed',
            'meta_query' => array(
                array(
                    'key' => '_ss_session_date',
                    'value' => $tomorrow,
                    'compare' => '='
                ),
                array(
                    'key' => '_ss_reminder_sent',
                    'compare' => 'NOT EXISTS'
                )
            ),
            'posts_per_page' => -1
        ));
        
        foreach ($upcoming_bookings as $booking) {
            self::send_session_reminder_email($booking->ID);
            update_post_meta($booking->ID, '_ss_reminder_sent', current_time('mysql'));
        }
    }
    
    /**
     * Send session reminder email
     */
    private static function send_session_reminder_email($booking_id) {
        $booking = get_post($booking_id);
        if (!$booking) return false;
        
        $client_email = get_post_meta($booking_id, '_ss_client_email', true);
        $client_name = get_post_meta($booking_id, '_ss_client_name', true);
        $session_date = get_post_meta($booking_id, '_ss_session_date', true);
        $session_time = get_post_meta($booking_id, '_ss_session_start_time', true);
        $session_type = get_post_meta($booking_id, '_ss_session_type', true);
        $session_location = get_post_meta($booking_id, '_ss_session_location', true);
        
        $subject = sprintf(__('Session Reminder - Tomorrow at %s', 'studiosnap'), date('g:i A', strtotime($session_time)));
        
        $message = self::get_email_template('session_reminder', array(
            'client_name' => $client_name,
            'session_date' => date('F j, Y', strtotime($session_date)),
            'session_time' => date('g:i A', strtotime($session_time)),
            'session_type' => ucfirst($session_type),
            'session_location' => $session_location === 'on_location' ? __('On Location', 'studiosnap') : __('Studio', 'studiosnap'),
            'studio_address' => get_option('ss_studio_address', ''),
            'studio_phone' => get_option('ss_studio_phone', ''),
            'preparation_tips' => self::get_session_preparation_tips($session_type)
        ));
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        return wp_mail($client_email, $subject, $message, $headers);
    }
    
    /**
     * Send follow-up emails after sessions
     */
    public static function send_followup_emails() {
        // Send follow-up 7 days after completed session
        $seven_days_ago = date('Y-m-d', strtotime('-7 days'));
        
        $completed_sessions = get_posts(array(
            'post_type' => 'ss_booking',
            'post_status' => 'ss_completed',
            'meta_query' => array(
                array(
                    'key' => '_ss_session_date',
                    'value' => $seven_days_ago,
                    'compare' => '='
                ),
                array(
                    'key' => '_ss_followup_sent',
                    'compare' => 'NOT EXISTS'
                )
            ),
            'posts_per_page' => -1
        ));
        
        foreach ($completed_sessions as $booking) {
            self::send_session_followup_email($booking->ID);
            update_post_meta($booking->ID, '_ss_followup_sent', current_time('mysql'));
        }
    }
    
    /**
     * Send session follow-up email
     */
    private static function send_session_followup_email($booking_id) {
        $booking = get_post($booking_id);
        if (!$booking) return false;
        
        $client_email = get_post_meta($booking_id, '_ss_client_email', true);
        $client_name = get_post_meta($booking_id, '_ss_client_name', true);
        $session_type = get_post_meta($booking_id, '_ss_session_type', true);
        
        $subject = sprintf(__('How was your %s session?', 'studiosnap'), ucfirst($session_type));
        
        $message = self::get_email_template('session_followup', array(
            'client_name' => $client_name,
            'session_type' => ucfirst($session_type),
            'review_link' => self::generate_review_link($booking_id),
            'referral_program_link' => home_url('/referral-program/'),
            'studio_name' => get_bloginfo('name')
        ));
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        return wp_mail($client_email, $subject, $message, $headers);
    }
    
    /**
     * Get email template
     */
    private static function get_email_template($template_name, $variables = array()) {
        $templates = array(
            'booking_inquiry' => '
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #333;">Thank you for your session inquiry!</h2>
    
    <p>Dear {client_name},</p>
    
    <p>Thank you for your interest in {studio_name}! We have received your inquiry for a {session_type} session.</p>
    
    <div style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0;">
        <h3>Session Details:</h3>
        <p><strong>Type:</strong> {session_type}<br>
        <strong>Requested Date:</strong> {session_date}<br>
        <strong>Requested Time:</strong> {session_time}</p>
    </div>
    
    <p>We will review your request and contact you within 24 hours to confirm availability and discuss your session details.</p>
    
    <p>If you have any immediate questions, please contact us at {studio_email} or {studio_phone}.</p>
    
    <p>Looking forward to capturing beautiful memories with you!</p>
    
    <p>Best regards,<br>{studio_name}</p>
</div>',
            
            'booking_confirmation' => '
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #4CAF50;">Your session is confirmed!</h2>
    
    <p>Dear {client_name},</p>
    
    <p>Great news! Your {session_type} session has been confirmed.</p>
    
    <div style="background: #e8f5e8; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #4CAF50;">
        <h3>Confirmed Session Details:</h3>
        <p><strong>Date:</strong> {session_date}<br>
        <strong>Time:</strong> {session_time}<br>
        <strong>Type:</strong> {session_type}<br>
        <strong>Location:</strong> {session_location}<br>
        <strong>Total Investment:</strong> {total_price}</p>
    </div>
    
    <h3>Next Steps:</h3>
    <ol>
        <li><a href="{contract_link}" style="color: #4CAF50;">Sign your session contract</a></li>
        <li><a href="{payment_link}" style="color: #4CAF50;">Complete your session retainer</a></li>
    </ol>
    
    <div style="background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h4>Session Preparation Tips:</h4>
        {preparation_tips}
    </div>
    
    <p><strong>Studio Address:</strong><br>{studio_address}</p>
    
    <p>We are excited to work with you and capture amazing photos!</p>
    
    <p>Best regards,<br>{studio_name}</p>
</div>',
            
            'session_completed' => '
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #ff6b6b;">Your photos are ready! 📸</h2>
    
    <p>Dear {client_name},</p>
    
    <p>Your {session_type} photos have been edited and are now ready for viewing and download!</p>
    
    <div style="background: #fff5f5; padding: 20px; border-radius: 5px; margin: 20px 0; text-align: center;">
        <h3>Access Your Gallery</h3>
        <p><a href="{gallery_link}" style="background: #ff6b6b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">View Your Photos</a></p>
        <p><small>Gallery Password: <strong>{gallery_password}</strong></small></p>
    </div>
    
    <p><strong>Important:</strong> Your photos will be available for download until {download_deadline}. Please download your favorite images before this date.</p>
    
    <p>Thank you for choosing {studio_name} for your photography needs!</p>
    
    <p>Best regards,<br>{studio_name}</p>
</div>',
            
            'session_reminder' => '
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #ff9800;">Session Reminder - Tomorrow!</h2>
    
    <p>Dear {client_name},</p>
    
    <p>This is a friendly reminder that your {session_type} session is tomorrow!</p>
    
    <div style="background: #fff8e1; padding: 20px; border-radius: 5px; margin: 20px 0;">
        <h3>Session Details:</h3>
        <p><strong>Date:</strong> {session_date}<br>
        <strong>Time:</strong> {session_time}<br>
        <strong>Location:</strong> {session_location}</p>
    </div>
    
    <div style="background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h4>Final Preparation Tips:</h4>
        {preparation_tips}
    </div>
    
    <p>If you need to reach us: {studio_phone}</p>
    
    <p>Looking forward to our session tomorrow!</p>
    
    <p>Best regards,<br>{studio_name}</p>
</div>',
            
            'session_followup' => '
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #9c27b0;">How was your session?</h2>
    
    <p>Dear {client_name},</p>
    
    <p>We hope you loved your recent {session_type} session with {studio_name}!</p>
    
    <p>We would love to hear about your experience. Would you mind taking a moment to leave us a review?</p>
    
    <p style="text-align: center;">
        <a href="{review_link}" style="background: #9c27b0; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">Leave a Review</a>
    </p>
    
    <p>Also, did you know we have a referral program? <a href="{referral_program_link}">Learn more</a> about how you can earn credits for future sessions!</p>
    
    <p>Thank you again for choosing {studio_name}!</p>
    
    <p>Best regards,<br>{studio_name}</p>
</div>'
        );
        
        if (!isset($templates[$template_name])) {
            return '';
        }
        
        $template = $templates[$template_name];
        
        // Replace variables
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }
    
    /**
     * Get session preparation tips
     */
    private static function get_session_preparation_tips($session_type) {
        $tips = array(
            'portrait' => '• Wear solid colors or simple patterns<br>• Avoid logos or busy designs<br>• Bring a few outfit options<br>• Get plenty of rest the night before',
            'family' => '• Coordinate outfits but avoid exact matching<br>• Bring snacks for young children<br>• Plan for the session to take 3 hours<br>• Consider scheduling during kids\' best time of day',
            'headshot' => '• Bring 2-3 professional outfit options<br>• Avoid heavy patterns or bright colors<br>• Consider professional hair/makeup<br>• Bring any props relevant to your profession',
            'event' => '• Share your timeline and must-have shot list<br>• Introduce us to key family members<br>• Ensure we have vendor meal arrangements<br>• Discuss backup plans for weather',
            'product' => '• Ensure products are clean and defect-free<br>• Bring any packaging or branding materials<br>• Share specific angles or features to highlight<br>• Provide high-resolution logos if needed'
        );
        
        return isset($tips[$session_type]) ? $tips[$session_type] : '';
    }
    
    /**
     * Generate secure contract link
     */
    private static function generate_contract_link($booking_id) {
        $hash = get_post_meta($booking_id, '_ss_booking_hash', true);
        return home_url('/client-portal/contract/?booking=' . $booking_id . '&hash=' . $hash);
    }
    
    /**
     * Generate secure payment link
     */
    private static function generate_payment_link($booking_id) {
        $hash = get_post_meta($booking_id, '_ss_booking_hash', true);
        return home_url('/client-portal/payment/?booking=' . $booking_id . '&hash=' . $hash);
    }
    
    /**
     * Generate gallery access link
     */
    private static function generate_gallery_access_link($gallery_id) {
        $gallery_hash = get_post_meta($gallery_id, '_ss_gallery_hash', true);
        return home_url('/gallery/?id=' . $gallery_id . '&hash=' . $gallery_hash);
    }
    
    /**
     * Generate review link
     */
    private static function generate_review_link($booking_id) {
        return home_url('/review/?booking=' . $booking_id);
    }
    
    /**
     * Get or create session gallery
     */
    private static function get_or_create_session_gallery($booking_id) {
        $existing_gallery = get_post_meta($booking_id, '_ss_gallery_id', true);
        
        if ($existing_gallery && get_post($existing_gallery)) {
            return $existing_gallery;
        }
        
        $client_name = get_post_meta($booking_id, '_ss_client_name', true);
        $session_date = get_post_meta($booking_id, '_ss_session_date', true);
        $session_type = get_post_meta($booking_id, '_ss_session_type', true);
        
        $gallery_post = array(
            'post_title' => sprintf('%s - %s Session - %s', 
                $client_name, 
                ucfirst($session_type),
                date('M j, Y', strtotime($session_date))
            ),
            'post_type' => 'ss_gallery',
            'post_status' => 'private',
            'meta_input' => array(
                '_ss_booking_id' => $booking_id,
                '_ss_gallery_password' => wp_generate_password(8, false),
                '_ss_gallery_hash' => wp_generate_password(32, false),
                '_ss_download_enabled' => 1,
                '_ss_expiry_date' => date('Y-m-d', strtotime('+30 days'))
            )
        );
        
        $gallery_id = wp_insert_post($gallery_post);
        update_post_meta($booking_id, '_ss_gallery_id', $gallery_id);
        
        return $gallery_id;
    }
    
    /**
     * AJAX: Send custom email to client
     */
    public static function ajax_send_client_email() {
        check_ajax_referer('ss_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions', 'studiosnap'));
        }
        
        $booking_id = intval($_POST['booking_id']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = wp_kses_post($_POST['message']);
        
        $client_email = get_post_meta($booking_id, '_ss_client_email', true);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        $result = wp_mail($client_email, $subject, $message, $headers);
        
        if ($result) {
            wp_send_json_success(__('Email sent successfully', 'studiosnap'));
        } else {
            wp_send_json_error(__('Failed to send email', 'studiosnap'));
        }
    }
}