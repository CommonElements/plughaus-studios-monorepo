<?php
/**
 * Single Event Template
 * 
 * @package Knot4
 * @since 1.0.0
 */

get_header(); ?>

<div class="knot4-single-event">
    <?php while (have_posts()) : the_post(); ?>
        <article id="event-<?php the_ID(); ?>" <?php post_class('knot4-event-single'); ?>>
            
            <!-- Event Header -->
            <header class="knot4-event-header">
                <div class="knot4-container">
                    <h1 class="knot4-event-title"><?php the_title(); ?></h1>
                    
                    <div class="knot4-event-meta">
                        <?php
                        $event_date = get_post_meta(get_the_ID(), '_knot4_event_date', true);
                        $event_time = get_post_meta(get_the_ID(), '_knot4_event_time', true);
                        $event_location = get_post_meta(get_the_ID(), '_knot4_event_location', true);
                        $event_price = get_post_meta(get_the_ID(), '_knot4_event_price', true);
                        ?>
                        
                        <?php if ($event_date): ?>
                        <div class="knot4-event-meta-item">
                            <span class="dashicons dashicons-calendar-alt"></span>
                            <span><?php echo date_i18n(get_option('date_format'), strtotime($event_date)); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($event_time): ?>
                        <div class="knot4-event-meta-item">
                            <span class="dashicons dashicons-clock"></span>
                            <span><?php echo esc_html($event_time); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($event_location): ?>
                        <div class="knot4-event-meta-item">
                            <span class="dashicons dashicons-location"></span>
                            <span><?php echo esc_html($event_location); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($event_price): ?>
                        <div class="knot4-event-meta-item">
                            <span class="dashicons dashicons-money-alt"></span>
                            <span><?php echo Knot4_Utilities::format_currency($event_price); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </header>
            
            <!-- Event Featured Image -->
            <?php if (has_post_thumbnail()): ?>
            <div class="knot4-event-featured-image">
                <div class="knot4-container">
                    <?php the_post_thumbnail('large', array('class' => 'knot4-event-image')); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Event Content -->
            <div class="knot4-event-content">
                <div class="knot4-container">
                    <div class="knot4-event-main">
                        
                        <!-- Event Description -->
                        <div class="knot4-event-description">
                            <?php the_content(); ?>
                        </div>
                        
                        <!-- Event Registration Form -->
                        <?php if (strtotime($event_date) > time()): ?>
                        <div class="knot4-event-registration">
                            <h3><?php _e('Register for this Event', 'knot4'); ?></h3>
                            
                            <form id="knot4-event-registration-form" class="knot4-event-registration-form" method="post">
                                <?php wp_nonce_field('knot4_public_nonce', 'nonce'); ?>
                                
                                <div class="knot4-form-row">
                                    <div class="knot4-form-col">
                                        <label for="attendee_first_name"><?php _e('First Name', 'knot4'); ?> <span class="required">*</span></label>
                                        <input type="text" id="attendee_first_name" name="attendee_first_name" required>
                                    </div>
                                    <div class="knot4-form-col">
                                        <label for="attendee_last_name"><?php _e('Last Name', 'knot4'); ?> <span class="required">*</span></label>
                                        <input type="text" id="attendee_last_name" name="attendee_last_name" required>
                                    </div>
                                </div>
                                
                                <div class="knot4-form-row">
                                    <div class="knot4-form-col">
                                        <label for="attendee_email"><?php _e('Email Address', 'knot4'); ?> <span class="required">*</span></label>
                                        <input type="email" id="attendee_email" name="attendee_email" required>
                                    </div>
                                    <div class="knot4-form-col">
                                        <label for="attendee_phone"><?php _e('Phone Number', 'knot4'); ?></label>
                                        <input type="tel" id="attendee_phone" name="attendee_phone">
                                    </div>
                                </div>
                                
                                <div class="knot4-form-row">
                                    <div class="knot4-form-col">
                                        <label for="ticket_quantity"><?php _e('Number of Tickets', 'knot4'); ?> <span class="required">*</span></label>
                                        <select id="ticket_quantity" name="ticket_quantity" required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="knot4-form-row">
                                    <div class="knot4-form-col">
                                        <label for="special_requirements"><?php _e('Special Requirements', 'knot4'); ?></label>
                                        <textarea id="special_requirements" name="special_requirements" rows="3" placeholder="<?php _e('Any dietary restrictions, accessibility needs, etc.', 'knot4'); ?>"></textarea>
                                    </div>
                                </div>
                                
                                <!-- Hidden Fields -->
                                <input type="hidden" name="action" value="knot4_register_event">
                                <input type="hidden" name="event_id" value="<?php echo get_the_ID(); ?>">
                                <input type="hidden" name="registration_type" value="general">
                                <input type="hidden" name="payment_method" value="stripe">
                                
                                <!-- Submit Button -->
                                <div class="knot4-form-section knot4-submit-section">
                                    <button type="submit" class="knot4-submit-btn knot4-btn-primary">
                                        <span class="btn-text"><?php _e('Register Now', 'knot4'); ?></span>
                                        <span class="btn-spinner" style="display: none;">
                                            <span class="spinner"></span>
                                            <?php _e('Processing...', 'knot4'); ?>
                                        </span>
                                    </button>
                                </div>
                                
                                <div class="knot4-form-messages"></div>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="knot4-event-past">
                            <p><?php _e('This event has already taken place.', 'knot4'); ?></p>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                    
                    <!-- Event Sidebar -->
                    <aside class="knot4-event-sidebar">
                        
                        <!-- Event Details -->
                        <div class="knot4-event-details-widget">
                            <h3><?php _e('Event Details', 'knot4'); ?></h3>
                            
                            <div class="knot4-event-detail-item">
                                <strong><?php _e('Date:', 'knot4'); ?></strong>
                                <span><?php echo $event_date ? date_i18n(get_option('date_format'), strtotime($event_date)) : __('TBD', 'knot4'); ?></span>
                            </div>
                            
                            <?php if ($event_time): ?>
                            <div class="knot4-event-detail-item">
                                <strong><?php _e('Time:', 'knot4'); ?></strong>
                                <span><?php echo esc_html($event_time); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($event_location): ?>
                            <div class="knot4-event-detail-item">
                                <strong><?php _e('Location:', 'knot4'); ?></strong>
                                <span><?php echo esc_html($event_location); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($event_price): ?>
                            <div class="knot4-event-detail-item">
                                <strong><?php _e('Price:', 'knot4'); ?></strong>
                                <span><?php echo Knot4_Utilities::format_currency($event_price); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php
                            $max_capacity = get_post_meta(get_the_ID(), '_knot4_event_max_capacity', true);
                            if ($max_capacity):
                            ?>
                            <div class="knot4-event-detail-item">
                                <strong><?php _e('Capacity:', 'knot4'); ?></strong>
                                <span><?php echo esc_html($max_capacity); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Event Categories -->
                        <?php
                        $event_categories = get_the_terms(get_the_ID(), 'knot4_event_category');
                        if ($event_categories && !is_wp_error($event_categories)):
                        ?>
                        <div class="knot4-event-categories-widget">
                            <h3><?php _e('Categories', 'knot4'); ?></h3>
                            <ul class="knot4-event-categories">
                                <?php foreach ($event_categories as $category): ?>
                                <li><a href="<?php echo get_term_link($category); ?>"><?php echo esc_html($category->name); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Share Widget -->
                        <div class="knot4-event-share-widget">
                            <h3><?php _e('Share This Event', 'knot4'); ?></h3>
                            <div class="knot4-share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="knot4-share-facebook">
                                    <span class="dashicons dashicons-facebook"></span>
                                    <?php _e('Facebook', 'knot4'); ?>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="knot4-share-twitter">
                                    <span class="dashicons dashicons-twitter"></span>
                                    <?php _e('Twitter', 'knot4'); ?>
                                </a>
                                <a href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode(get_permalink()); ?>" class="knot4-share-email">
                                    <span class="dashicons dashicons-email"></span>
                                    <?php _e('Email', 'knot4'); ?>
                                </a>
                            </div>
                        </div>
                        
                    </aside>
                </div>
            </div>
            
        </article>
        
        <!-- Related Events -->
        <?php
        $related_events = get_posts(array(
            'post_type' => 'knot4_event',
            'posts_per_page' => 3,
            'post__not_in' => array(get_the_ID()),
            'meta_query' => array(
                array(
                    'key' => '_knot4_event_date',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE'
                )
            ),
            'meta_key' => '_knot4_event_date',
            'orderby' => 'meta_value',
            'order' => 'ASC'
        ));
        
        if ($related_events):
        ?>
        <section class="knot4-related-events">
            <div class="knot4-container">
                <h2><?php _e('Other Upcoming Events', 'knot4'); ?></h2>
                <div class="knot4-events-grid">
                    <?php foreach ($related_events as $event): ?>
                    <div class="knot4-event-card">
                        <div class="knot4-event-content">
                            <h3 class="knot4-event-title">
                                <a href="<?php echo get_permalink($event); ?>"><?php echo esc_html($event->post_title); ?></a>
                            </h3>
                            <div class="knot4-event-meta">
                                <?php 
                                $related_date = get_post_meta($event->ID, '_knot4_event_date', true);
                                $related_location = get_post_meta($event->ID, '_knot4_event_location', true);
                                ?>
                                <?php if ($related_date): ?>
                                <div class="knot4-event-meta-item">
                                    <span class="dashicons dashicons-calendar-alt"></span>
                                    <span><?php echo date_i18n(get_option('date_format'), strtotime($related_date)); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($related_location): ?>
                                <div class="knot4-event-meta-item">
                                    <span class="dashicons dashicons-location"></span>
                                    <span><?php echo esc_html($related_location); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="knot4-event-excerpt">
                                <p><?php echo wp_trim_words($event->post_content, 20); ?></p>
                            </div>
                            <div class="knot4-event-actions">
                                <a href="<?php echo get_permalink($event); ?>" class="knot4-btn knot4-btn-primary"><?php _e('Learn More', 'knot4'); ?></a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
    <?php endwhile; ?>
</div>

<style>
.knot4-single-event {
    margin: 40px 0;
}

.knot4-event-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
    text-align: center;
}

.knot4-event-title {
    font-size: 2.5em;
    margin-bottom: 20px;
}

.knot4-event-meta {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 30px;
}

.knot4-event-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.knot4-event-featured-image {
    margin: 40px 0;
}

.knot4-event-content {
    margin: 40px 0;
}

.knot4-event-main {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
}

.knot4-event-description {
    margin-bottom: 40px;
}

.knot4-event-registration {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 8px;
    border-left: 4px solid #007cba;
}

.knot4-event-sidebar {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.knot4-event-details-widget,
.knot4-event-categories-widget,
.knot4-event-share-widget {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
}

.knot4-event-detail-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.knot4-event-detail-item:last-child {
    border-bottom: none;
}

.knot4-event-categories {
    list-style: none;
    padding: 0;
    margin: 0;
}

.knot4-event-categories li {
    margin-bottom: 8px;
}

.knot4-share-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.knot4-share-buttons a {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    transition: background 0.3s ease;
}

.knot4-share-buttons a:hover {
    background: #e9ecef;
}

.knot4-related-events {
    margin: 60px 0;
    padding: 40px 0;
    background: #f8f9fa;
}

.knot4-related-events h2 {
    text-align: center;
    margin-bottom: 40px;
}

@media (max-width: 768px) {
    .knot4-event-main {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .knot4-event-meta {
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
    
    .knot4-event-title {
        font-size: 2em;
    }
    
    .knot4-event-header {
        padding: 40px 0;
    }
}
</style>

<?php get_footer(); ?>