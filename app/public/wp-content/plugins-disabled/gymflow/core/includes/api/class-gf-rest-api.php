<?php
/**
 * GymFlow REST API Class
 *
 * Handles all REST API endpoints for external integrations and mobile apps
 *
 * @package GymFlow
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GF_Rest_API Class
 *
 * REST API functionality
 */
class GF_Rest_API {

    /**
     * API namespace
     * @var string
     */
    private $namespace = 'gymflow/v1';

    /**
     * Initialize REST API
     */
    public function init() {
        add_action('rest_api_init', array($this, 'register_routes'));
        add_filter('rest_authentication_errors', array($this, 'authenticate_request'));
    }

    /**
     * Register all REST API routes
     */
    public function register_routes() {
        // Members endpoints
        register_rest_route($this->namespace, '/members', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_members'),
                'permission_callback' => array($this, 'check_admin_permissions'),
                'args' => $this->get_members_args()
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_member'),
                'permission_callback' => array($this, 'check_create_member_permissions'),
                'args' => $this->get_member_schema()
            )
        ));

        register_rest_route($this->namespace, '/members/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_member'),
                'permission_callback' => array($this, 'check_member_permissions'),
                'args' => array(
                    'id' => array(
                        'required' => true,
                        'type' => 'integer'
                    )
                )
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_member'),
                'permission_callback' => array($this, 'check_member_permissions'),
                'args' => $this->get_member_schema()
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_member'),
                'permission_callback' => array($this, 'check_admin_permissions')
            )
        ));

        // Classes endpoints
        register_rest_route($this->namespace, '/classes', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_classes'),
                'permission_callback' => '__return_true', // Public endpoint
                'args' => $this->get_classes_args()
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_class'),
                'permission_callback' => array($this, 'check_admin_permissions'),
                'args' => $this->get_class_schema()
            )
        ));

        register_rest_route($this->namespace, '/classes/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_class'),
                'permission_callback' => '__return_true',
                'args' => array(
                    'id' => array(
                        'required' => true,
                        'type' => 'integer'
                    )
                )
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_class'),
                'permission_callback' => array($this, 'check_admin_permissions'),
                'args' => $this->get_class_schema()
            )
        ));

        // Class schedules endpoints
        register_rest_route($this->namespace, '/classes/(?P<id>\d+)/schedules', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_class_schedules'),
                'permission_callback' => '__return_true',
                'args' => $this->get_schedules_args()
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_class_schedule'),
                'permission_callback' => array($this, 'check_admin_permissions'),
                'args' => $this->get_schedule_schema()
            )
        ));

        // Trainers endpoints
        register_rest_route($this->namespace, '/trainers', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_trainers'),
                'permission_callback' => '__return_true',
                'args' => $this->get_trainers_args()
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_trainer'),
                'permission_callback' => array($this, 'check_admin_permissions'),
                'args' => $this->get_trainer_schema()
            )
        ));

        register_rest_route($this->namespace, '/trainers/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_trainer'),
                'permission_callback' => '__return_true',
                'args' => array(
                    'id' => array(
                        'required' => true,
                        'type' => 'integer'
                    )
                )
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_trainer'),
                'permission_callback' => array($this, 'check_admin_permissions'),
                'args' => $this->get_trainer_schema()
            )
        ));

        // Bookings endpoints
        register_rest_route($this->namespace, '/bookings', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_bookings'),
                'permission_callback' => array($this, 'check_booking_permissions'),
                'args' => $this->get_bookings_args()
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_booking'),
                'permission_callback' => array($this, 'check_create_booking_permissions'),
                'args' => $this->get_booking_schema()
            )
        ));

        register_rest_route($this->namespace, '/bookings/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_booking'),
                'permission_callback' => array($this, 'check_booking_permissions'),
                'args' => array(
                    'id' => array(
                        'required' => true,
                        'type' => 'integer'
                    )
                )
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_booking'),
                'permission_callback' => array($this, 'check_booking_permissions'),
                'args' => $this->get_booking_schema()
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'cancel_booking'),
                'permission_callback' => array($this, 'check_booking_permissions')
            )
        ));

        // Equipment endpoints
        register_rest_route($this->namespace, '/equipment', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_equipment'),
                'permission_callback' => '__return_true',
                'args' => $this->get_equipment_args()
            )
        ));

        // Availability endpoints
        register_rest_route($this->namespace, '/availability/classes', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_class_availability'),
            'permission_callback' => '__return_true',
            'args' => array(
                'date' => array(
                    'required' => true,
                    'type' => 'string',
                    'format' => 'date'
                ),
                'class_id' => array(
                    'type' => 'integer'
                )
            )
        ));

        register_rest_route($this->namespace, '/availability/trainers', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_trainer_availability'),
            'permission_callback' => '__return_true',
            'args' => array(
                'date' => array(
                    'required' => true,
                    'type' => 'string',
                    'format' => 'date'
                ),
                'start_time' => array(
                    'required' => true,
                    'type' => 'string'
                ),
                'end_time' => array(
                    'required' => true,
                    'type' => 'string'
                )
            )
        ));

        // Authentication endpoint
        register_rest_route($this->namespace, '/auth', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'authenticate_member'),
            'permission_callback' => '__return_true',
            'args' => array(
                'email' => array(
                    'required' => true,
                    'type' => 'string',
                    'format' => 'email'
                ),
                'member_number' => array(
                    'type' => 'string'
                )
            )
        ));

        // Statistics endpoints (Pro feature)
        if (class_exists('GF_Pro_Analytics')) {
            register_rest_route($this->namespace, '/stats/overview', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_stats_overview'),
                'permission_callback' => array($this, 'check_admin_permissions')
            ));

            register_rest_route($this->namespace, '/stats/members', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_member_stats'),
                'permission_callback' => array($this, 'check_admin_permissions')
            ));
        }
    }

    /**
     * Get members
     */
    public function get_members($request) {
        $args = array(
            'limit' => $request->get_param('per_page') ?: 20,
            'offset' => ($request->get_param('page') - 1) * ($request->get_param('per_page') ?: 20),
            'search' => $request->get_param('search'),
            'status' => $request->get_param('status'),
            'order_by' => $request->get_param('orderby') ?: 'last_name',
            'order' => $request->get_param('order') ?: 'ASC'
        );

        $members = GF_Member::get_all($args);
        $total = GF_Member::get_count();

        $data = array();
        foreach ($members as $member_data) {
            $member = new GF_Member($member_data->id);
            $data[] = $this->prepare_member_for_response($member);
        }

        $response = new WP_REST_Response($data);
        $response->header('X-WP-Total', $total);
        $response->header('X-WP-TotalPages', ceil($total / ($request->get_param('per_page') ?: 20)));

        return $response;
    }

    /**
     * Get single member
     */
    public function get_member($request) {
        $member = new GF_Member($request->get_param('id'));
        
        if (!$member->id) {
            return new WP_Error('member_not_found', __('Member not found.', 'gymflow'), array('status' => 404));
        }

        return $this->prepare_member_for_response($member);
    }

    /**
     * Create member
     */
    public function create_member($request) {
        $member = new GF_Member();
        $this->update_member_from_request($member, $request);

        $member_id = $member->save();

        if (!$member_id) {
            return new WP_Error('member_creation_failed', __('Failed to create member.', 'gymflow'), array('status' => 500));
        }

        $response = $this->prepare_member_for_response($member);
        $response = new WP_REST_Response($response, 201);
        $response->header('Location', rest_url($this->namespace . '/members/' . $member_id));

        return $response;
    }

    /**
     * Update member
     */
    public function update_member($request) {
        $member = new GF_Member($request->get_param('id'));
        
        if (!$member->id) {
            return new WP_Error('member_not_found', __('Member not found.', 'gymflow'), array('status' => 404));
        }

        $this->update_member_from_request($member, $request);

        if (!$member->save()) {
            return new WP_Error('member_update_failed', __('Failed to update member.', 'gymflow'), array('status' => 500));
        }

        return $this->prepare_member_for_response($member);
    }

    /**
     * Delete member
     */
    public function delete_member($request) {
        $member = new GF_Member($request->get_param('id'));
        
        if (!$member->id) {
            return new WP_Error('member_not_found', __('Member not found.', 'gymflow'), array('status' => 404));
        }

        if (!$member->delete()) {
            return new WP_Error('member_deletion_failed', __('Failed to delete member.', 'gymflow'), array('status' => 500));
        }

        return new WP_REST_Response(null, 204);
    }

    /**
     * Get classes
     */
    public function get_classes($request) {
        $args = array(
            'limit' => $request->get_param('per_page') ?: 20,
            'offset' => ($request->get_param('page') - 1) * ($request->get_param('per_page') ?: 20),
            'category' => $request->get_param('category'),
            'difficulty' => $request->get_param('difficulty'),
            'search' => $request->get_param('search'),
            'active_only' => $request->get_param('active') !== 'false'
        );

        $classes = GF_Class::get_all($args);
        $total = GF_Class::get_count($args['active_only']);

        $data = array();
        foreach ($classes as $class_data) {
            $class = new GF_Class($class_data->id);
            $data[] = $this->prepare_class_for_response($class);
        }

        $response = new WP_REST_Response($data);
        $response->header('X-WP-Total', $total);

        return $response;
    }

    /**
     * Get single class
     */
    public function get_class($request) {
        $class = new GF_Class($request->get_param('id'));
        
        if (!$class->id) {
            return new WP_Error('class_not_found', __('Class not found.', 'gymflow'), array('status' => 404));
        }

        return $this->prepare_class_for_response($class);
    }

    /**
     * Get class schedules
     */
    public function get_class_schedules($request) {
        $class = new GF_Class($request->get_param('id'));
        
        if (!$class->id) {
            return new WP_Error('class_not_found', __('Class not found.', 'gymflow'), array('status' => 404));
        }

        $args = array(
            'date_from' => $request->get_param('date_from'),
            'date_to' => $request->get_param('date_to'),
            'status' => $request->get_param('status') ?: 'scheduled',
            'limit' => $request->get_param('per_page') ?: 50
        );

        $schedules = $class->get_schedules($args);

        $data = array();
        foreach ($schedules as $schedule) {
            $data[] = $this->prepare_schedule_for_response($schedule);
        }

        return new WP_REST_Response($data);
    }

    /**
     * Get trainers
     */
    public function get_trainers($request) {
        $args = array(
            'limit' => $request->get_param('per_page') ?: 20,
            'offset' => ($request->get_param('page') - 1) * ($request->get_param('per_page') ?: 20),
            'search' => $request->get_param('search'),
            'specialty' => $request->get_param('specialty'),
            'active_only' => $request->get_param('active') !== 'false'
        );

        $trainers = GF_Trainer::get_all($args);
        $total = GF_Trainer::get_count($args['active_only']);

        $data = array();
        foreach ($trainers as $trainer_data) {
            $trainer = new GF_Trainer($trainer_data->id);
            $data[] = $this->prepare_trainer_for_response($trainer);
        }

        $response = new WP_REST_Response($data);
        $response->header('X-WP-Total', $total);

        return $response;
    }

    /**
     * Get bookings
     */
    public function get_bookings($request) {
        $args = array(
            'limit' => $request->get_param('per_page') ?: 20,
            'offset' => ($request->get_param('page') - 1) * ($request->get_param('per_page') ?: 20),
            'member_id' => $request->get_param('member_id'),
            'trainer_id' => $request->get_param('trainer_id'),
            'booking_type' => $request->get_param('type'),
            'status' => $request->get_param('status'),
            'date_from' => $request->get_param('date_from'),
            'date_to' => $request->get_param('date_to')
        );

        $bookings = GF_Booking::get_all($args);
        $total = GF_Booking::get_count();

        $data = array();
        foreach ($bookings as $booking_data) {
            $booking = new GF_Booking($booking_data->id);
            $data[] = $this->prepare_booking_for_response($booking);
        }

        $response = new WP_REST_Response($data);
        $response->header('X-WP-Total', $total);

        return $response;
    }

    /**
     * Create booking
     */
    public function create_booking($request) {
        $booking = new GF_Booking();
        $this->update_booking_from_request($booking, $request);

        $booking_id = $booking->save();

        if (!$booking_id) {
            return new WP_Error('booking_creation_failed', __('Failed to create booking.', 'gymflow'), array('status' => 500));
        }

        $response = $this->prepare_booking_for_response($booking);
        $response = new WP_REST_Response($response, 201);
        $response->header('Location', rest_url($this->namespace . '/bookings/' . $booking_id));

        return $response;
    }

    /**
     * Get class availability
     */
    public function get_class_availability($request) {
        $date = $request->get_param('date');
        $class_id = $request->get_param('class_id');

        if ($class_id) {
            $class = new GF_Class($class_id);
            if (!$class->id) {
                return new WP_Error('class_not_found', __('Class not found.', 'gymflow'), array('status' => 404));
            }

            $schedules = $class->get_schedules(array(
                'date_from' => $date,
                'date_to' => $date,
                'status' => 'scheduled'
            ));
        } else {
            // Get all class schedules for the date
            global $wpdb;
            $schedules_table = $wpdb->prefix . 'gf_class_schedules';
            $classes_table = $wpdb->prefix . 'gf_classes';

            $schedules = $wpdb->get_results($wpdb->prepare(
                "SELECT s.*, c.name as class_name 
                 FROM {$schedules_table} s
                 INNER JOIN {$classes_table} c ON s.class_id = c.id
                 WHERE s.date = %s AND s.status = 'scheduled' AND c.is_active = 1
                 ORDER BY s.start_time ASC",
                $date
            ));
        }

        $data = array();
        foreach ($schedules as $schedule) {
            $available_spots = GF_Class::get_available_spots($schedule->id);
            $data[] = array(
                'schedule_id' => $schedule->id,
                'class_id' => $schedule->class_id,
                'class_name' => isset($schedule->class_name) ? $schedule->class_name : '',
                'date' => $schedule->date,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'max_capacity' => $schedule->max_capacity,
                'current_bookings' => $schedule->current_bookings,
                'available_spots' => $available_spots,
                'instructor_id' => $schedule->instructor_id
            );
        }

        return new WP_REST_Response($data);
    }

    /**
     * Get trainer availability
     */
    public function get_trainer_availability($request) {
        $date = $request->get_param('date');
        $start_time = $request->get_param('start_time');
        $end_time = $request->get_param('end_time');

        $available_trainers = GF_Trainer::get_available_trainers($date, $start_time, $end_time);

        $data = array();
        foreach ($available_trainers as $trainer_data) {
            $trainer = new GF_Trainer($trainer_data->id);
            $data[] = $this->prepare_trainer_for_response($trainer);
        }

        return new WP_REST_Response($data);
    }

    /**
     * Authenticate member
     */
    public function authenticate_member($request) {
        $email = $request->get_param('email');
        $member_number = $request->get_param('member_number');

        // Find member by email
        $member = GF_Member::find_by_email($email);

        if (!$member) {
            return new WP_Error('member_not_found', __('Member not found.', 'gymflow'), array('status' => 404));
        }

        // Verify member number if provided
        if ($member_number && $member->member_number !== $member_number) {
            return new WP_Error('invalid_credentials', __('Invalid credentials.', 'gymflow'), array('status' => 401));
        }

        // Generate API token (simplified - in production use proper JWT or similar)
        $token = wp_generate_password(32, false);
        update_user_meta($member->id, 'gf_api_token', $token);
        update_user_meta($member->id, 'gf_api_token_expires', time() + (24 * 60 * 60)); // 24 hours

        return array(
            'token' => $token,
            'expires_in' => 24 * 60 * 60,
            'member' => $this->prepare_member_for_response($member)
        );
    }

    /**
     * Prepare member for response
     */
    private function prepare_member_for_response($member) {
        return array(
            'id' => $member->id,
            'member_number' => $member->member_number,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'full_name' => $member->get_full_name(),
            'email' => $member->email,
            'phone' => $member->phone,
            'date_of_birth' => $member->date_of_birth,
            'gender' => $member->gender,
            'membership_type' => $member->membership_type,
            'membership_status' => $member->membership_status,
            'membership_start_date' => $member->membership_start_date,
            'membership_end_date' => $member->membership_end_date,
            'emergency_contact_name' => $member->emergency_contact_name,
            'emergency_contact_phone' => $member->emergency_contact_phone,
            'health_conditions' => $member->health_conditions,
            'created_at' => $member->created_at,
            '_links' => array(
                'self' => array(
                    'href' => rest_url($this->namespace . '/members/' . $member->id)
                )
            )
        );
    }

    /**
     * Prepare class for response
     */
    private function prepare_class_for_response($class) {
        $instructor = $class->get_instructor();
        
        return array(
            'id' => $class->id,
            'name' => $class->name,
            'description' => $class->description,
            'category' => $class->category,
            'duration' => $class->duration,
            'duration_formatted' => $class->get_formatted_duration(),
            'capacity' => $class->capacity,
            'difficulty_level' => $class->difficulty_level,
            'difficulty_label' => $class->get_difficulty_label(),
            'equipment_required' => $class->equipment_required,
            'instructor_id' => $class->instructor_id,
            'instructor' => $instructor ? array(
                'id' => $instructor->id,
                'name' => $instructor->get_full_name(),
                'specialties' => $instructor->get_specialties_array()
            ) : null,
            'price' => $class->price,
            'drop_in_price' => $class->drop_in_price,
            'price_formatted' => $class->get_formatted_price(),
            'drop_in_price_formatted' => $class->get_formatted_price('drop_in'),
            'is_active' => $class->is_active,
            'created_at' => $class->created_at,
            '_links' => array(
                'self' => array(
                    'href' => rest_url($this->namespace . '/classes/' . $class->id)
                ),
                'schedules' => array(
                    'href' => rest_url($this->namespace . '/classes/' . $class->id . '/schedules')
                )
            )
        );
    }

    /**
     * Prepare trainer for response
     */
    private function prepare_trainer_for_response($trainer) {
        return array(
            'id' => $trainer->id,
            'trainer_number' => $trainer->trainer_number,
            'first_name' => $trainer->first_name,
            'last_name' => $trainer->last_name,
            'full_name' => $trainer->get_full_name(),
            'email' => $trainer->email,
            'phone' => $trainer->phone,
            'bio' => $trainer->bio,
            'specialties' => $trainer->get_specialties_array(),
            'certifications' => $trainer->get_certifications_array(),
            'hire_date' => $trainer->hire_date,
            'years_experience' => $trainer->get_years_experience(),
            'hourly_rate' => $trainer->hourly_rate,
            'hourly_rate_formatted' => $trainer->get_formatted_hourly_rate(),
            'commission_rate' => $trainer->commission_rate,
            'profile_photo_url' => $trainer->profile_photo_url,
            'is_active' => $trainer->is_active,
            'created_at' => $trainer->created_at,
            '_links' => array(
                'self' => array(
                    'href' => rest_url($this->namespace . '/trainers/' . $trainer->id)
                )
            )
        );
    }

    /**
     * Prepare booking for response
     */
    private function prepare_booking_for_response($booking) {
        $detailed_info = $booking->get_detailed_info();
        
        return array(
            'id' => $booking->id,
            'booking_reference' => $booking->booking_reference,
            'booking_type' => $booking->booking_type,
            'member_id' => $booking->member_id,
            'member' => $detailed_info['member'] ? array(
                'id' => $detailed_info['member']->id,
                'name' => $detailed_info['member']->get_full_name(),
                'email' => $detailed_info['member']->email
            ) : null,
            'class_schedule_id' => $booking->class_schedule_id,
            'equipment_id' => $booking->equipment_id,
            'trainer_id' => $booking->trainer_id,
            'booking_date' => $booking->booking_date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'datetime_formatted' => $booking->get_formatted_datetime(),
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'amount' => $booking->amount,
            'amount_formatted' => $booking->get_formatted_amount(),
            'payment_method' => $booking->payment_method,
            'notes' => $booking->notes,
            'booking_source' => $booking->booking_source,
            'confirmed_at' => $booking->confirmed_at,
            'cancelled_at' => $booking->cancelled_at,
            'cancellation_reason' => $booking->cancellation_reason,
            'can_be_cancelled' => $booking->can_be_cancelled(),
            'created_at' => $booking->created_at,
            '_links' => array(
                'self' => array(
                    'href' => rest_url($this->namespace . '/bookings/' . $booking->id)
                )
            )
        );
    }

    /**
     * Prepare schedule for response
     */
    private function prepare_schedule_for_response($schedule) {
        return array(
            'id' => $schedule->id,
            'class_id' => $schedule->class_id,
            'instructor_id' => $schedule->instructor_id,
            'date' => $schedule->date,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'room' => $schedule->room,
            'max_capacity' => $schedule->max_capacity,
            'current_bookings' => $schedule->current_bookings,
            'available_spots' => GF_Class::get_available_spots($schedule->id),
            'status' => $schedule->status,
            'notes' => $schedule->notes
        );
    }

    /**
     * Update member from request
     */
    private function update_member_from_request($member, $request) {
        $fields = array(
            'first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'gender',
            'membership_type', 'membership_status', 'membership_start_date', 'membership_end_date',
            'emergency_contact_name', 'emergency_contact_phone', 'health_conditions'
        );

        foreach ($fields as $field) {
            if ($request->has_param($field)) {
                $member->$field = $request->get_param($field);
            }
        }
    }

    /**
     * Update booking from request
     */
    private function update_booking_from_request($booking, $request) {
        $fields = array(
            'booking_type', 'member_id', 'class_schedule_id', 'equipment_id', 'trainer_id',
            'booking_date', 'start_time', 'end_time', 'amount', 'payment_method',
            'notes', 'booking_source'
        );

        foreach ($fields as $field) {
            if ($request->has_param($field)) {
                $booking->$field = $request->get_param($field);
            }
        }
    }

    /**
     * Authentication for API requests
     */
    public function authenticate_request($result) {
        // Skip authentication for certain endpoints
        $request_uri = $_SERVER['REQUEST_URI'];
        if (strpos($request_uri, '/gymflow/v1/classes') !== false && $_SERVER['REQUEST_METHOD'] === 'GET') {
            return $result;
        }

        // Check for API token in header
        $token = $this->get_auth_token();
        
        if ($token && $this->validate_token($token)) {
            return $result;
        }

        // Fall back to WordPress authentication
        return $result;
    }

    /**
     * Get authentication token from request
     */
    private function get_auth_token() {
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            $auth_header = $headers['Authorization'];
            if (strpos($auth_header, 'Bearer ') === 0) {
                return substr($auth_header, 7);
            }
        }

        return false;
    }

    /**
     * Validate API token
     */
    private function validate_token($token) {
        global $wpdb;
        
        $member_id = $wpdb->get_var($wpdb->prepare(
            "SELECT meta_value FROM {$wpdb->usermeta} 
             WHERE meta_key = 'gf_api_token' AND meta_value = %s",
            $token
        ));

        if ($member_id) {
            $expires = get_user_meta($member_id, 'gf_api_token_expires', true);
            if ($expires && $expires > time()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Permission callbacks
     */
    public function check_admin_permissions() {
        return current_user_can('manage_gymflow');
    }

    public function check_member_permissions($request) {
        if (current_user_can('manage_gymflow_members')) {
            return true;
        }

        // Members can access their own data
        $current_member_id = $this->get_current_member_id();
        return $current_member_id && $current_member_id == $request->get_param('id');
    }

    public function check_booking_permissions($request) {
        if (current_user_can('manage_gymflow_bookings')) {
            return true;
        }

        // Members can access their own bookings
        $current_member_id = $this->get_current_member_id();
        if ($current_member_id) {
            if ($request->get_param('member_id')) {
                return $current_member_id == $request->get_param('member_id');
            }
            
            // For individual booking access, check ownership
            $booking_id = $request->get_param('id');
            if ($booking_id) {
                $booking = new GF_Booking($booking_id);
                return $booking->member_id == $current_member_id;
            }
        }

        return false;
    }

    public function check_create_member_permissions() {
        // Allow member self-registration
        return true;
    }

    public function check_create_booking_permissions() {
        // Allow booking creation for authenticated members or admins
        return current_user_can('manage_gymflow_bookings') || $this->get_current_member_id();
    }

    /**
     * Get current member ID from token
     */
    private function get_current_member_id() {
        $token = $this->get_auth_token();
        
        if ($token) {
            global $wpdb;
            return $wpdb->get_var($wpdb->prepare(
                "SELECT user_id FROM {$wpdb->usermeta} 
                 WHERE meta_key = 'gf_api_token' AND meta_value = %s",
                $token
            ));
        }

        return false;
    }

    /**
     * Schema definitions
     */
    private function get_member_schema() {
        return array(
            'first_name' => array(
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'last_name' => array(
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'email' => array(
                'type' => 'string',
                'format' => 'email',
                'required' => true,
                'sanitize_callback' => 'sanitize_email'
            ),
            'phone' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'date_of_birth' => array(
                'type' => 'string',
                'format' => 'date'
            ),
            'gender' => array(
                'type' => 'string',
                'enum' => array('male', 'female', 'other', 'prefer_not_to_say')
            ),
            'membership_type' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'membership_status' => array(
                'type' => 'string',
                'enum' => array('pending', 'active', 'expired', 'cancelled', 'on_hold')
            )
        );
    }

    private function get_class_schema() {
        return array(
            'name' => array(
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'description' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field'
            ),
            'category' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'duration' => array(
                'type' => 'integer',
                'minimum' => 1
            ),
            'capacity' => array(
                'type' => 'integer',
                'minimum' => 1
            ),
            'difficulty_level' => array(
                'type' => 'string',
                'enum' => array('beginner', 'intermediate', 'advanced', 'all_levels')
            ),
            'instructor_id' => array(
                'type' => 'integer'
            ),
            'price' => array(
                'type' => 'number',
                'minimum' => 0
            ),
            'drop_in_price' => array(
                'type' => 'number',
                'minimum' => 0
            ),
            'is_active' => array(
                'type' => 'boolean'
            )
        );
    }

    private function get_trainer_schema() {
        return array(
            'first_name' => array(
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'last_name' => array(
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'email' => array(
                'type' => 'string',
                'format' => 'email',
                'required' => true,
                'sanitize_callback' => 'sanitize_email'
            ),
            'phone' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'bio' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field'
            ),
            'specialties' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field'
            ),
            'hourly_rate' => array(
                'type' => 'number',
                'minimum' => 0
            ),
            'is_active' => array(
                'type' => 'boolean'
            )
        );
    }

    private function get_booking_schema() {
        return array(
            'booking_type' => array(
                'type' => 'string',
                'required' => true,
                'enum' => array('class', 'equipment', 'personal_training')
            ),
            'member_id' => array(
                'type' => 'integer',
                'required' => true
            ),
            'booking_date' => array(
                'type' => 'string',
                'format' => 'date',
                'required' => true
            ),
            'start_time' => array(
                'type' => 'string',
                'required' => true
            ),
            'end_time' => array(
                'type' => 'string',
                'required' => true
            ),
            'class_schedule_id' => array(
                'type' => 'integer'
            ),
            'equipment_id' => array(
                'type' => 'integer'
            ),
            'trainer_id' => array(
                'type' => 'integer'
            ),
            'amount' => array(
                'type' => 'number',
                'minimum' => 0
            ),
            'notes' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field'
            )
        );
    }

    private function get_members_args() {
        return array(
            'page' => array(
                'type' => 'integer',
                'default' => 1,
                'minimum' => 1
            ),
            'per_page' => array(
                'type' => 'integer',
                'default' => 20,
                'minimum' => 1,
                'maximum' => 100
            ),
            'search' => array(
                'type' => 'string'
            ),
            'status' => array(
                'type' => 'string',
                'enum' => array('pending', 'active', 'expired', 'cancelled', 'on_hold')
            ),
            'orderby' => array(
                'type' => 'string',
                'enum' => array('first_name', 'last_name', 'email', 'created_at'),
                'default' => 'last_name'
            ),
            'order' => array(
                'type' => 'string',
                'enum' => array('ASC', 'DESC'),
                'default' => 'ASC'
            )
        );
    }

    private function get_classes_args() {
        return array(
            'page' => array(
                'type' => 'integer',
                'default' => 1,
                'minimum' => 1
            ),
            'per_page' => array(
                'type' => 'integer',
                'default' => 20,
                'minimum' => 1,
                'maximum' => 100
            ),
            'category' => array(
                'type' => 'string'
            ),
            'difficulty' => array(
                'type' => 'string',
                'enum' => array('beginner', 'intermediate', 'advanced', 'all_levels')
            ),
            'search' => array(
                'type' => 'string'
            ),
            'active' => array(
                'type' => 'string',
                'enum' => array('true', 'false'),
                'default' => 'true'
            )
        );
    }

    private function get_trainers_args() {
        return array(
            'page' => array(
                'type' => 'integer',
                'default' => 1,
                'minimum' => 1
            ),
            'per_page' => array(
                'type' => 'integer',
                'default' => 20,
                'minimum' => 1,
                'maximum' => 100
            ),
            'search' => array(
                'type' => 'string'
            ),
            'specialty' => array(
                'type' => 'string'
            ),
            'active' => array(
                'type' => 'string',
                'enum' => array('true', 'false'),
                'default' => 'true'
            )
        );
    }

    private function get_bookings_args() {
        return array(
            'page' => array(
                'type' => 'integer',
                'default' => 1,
                'minimum' => 1
            ),
            'per_page' => array(
                'type' => 'integer',
                'default' => 20,
                'minimum' => 1,
                'maximum' => 100
            ),
            'member_id' => array(
                'type' => 'integer'
            ),
            'trainer_id' => array(
                'type' => 'integer'
            ),
            'type' => array(
                'type' => 'string',
                'enum' => array('class', 'equipment', 'personal_training')
            ),
            'status' => array(
                'type' => 'string',
                'enum' => array('confirmed', 'pending', 'cancelled', 'completed', 'no_show')
            ),
            'date_from' => array(
                'type' => 'string',
                'format' => 'date'
            ),
            'date_to' => array(
                'type' => 'string',
                'format' => 'date'
            )
        );
    }

    private function get_equipment_args() {
        return array(
            'page' => array(
                'type' => 'integer',
                'default' => 1,
                'minimum' => 1
            ),
            'per_page' => array(
                'type' => 'integer',
                'default' => 20,
                'minimum' => 1,
                'maximum' => 100
            ),
            'category' => array(
                'type' => 'string'
            ),
            'status' => array(
                'type' => 'string',
                'enum' => array('available', 'booked', 'maintenance', 'out_of_order')
            ),
            'bookable' => array(
                'type' => 'string',
                'enum' => array('true', 'false'),
                'default' => 'true'
            )
        );
    }

    private function get_schedules_args() {
        return array(
            'date_from' => array(
                'type' => 'string',
                'format' => 'date'
            ),
            'date_to' => array(
                'type' => 'string',
                'format' => 'date'
            ),
            'status' => array(
                'type' => 'string',
                'enum' => array('scheduled', 'completed', 'cancelled'),
                'default' => 'scheduled'
            ),
            'per_page' => array(
                'type' => 'integer',
                'default' => 50,
                'maximum' => 100
            )
        );
    }

    private function get_schedule_schema() {
        return array(
            'instructor_id' => array(
                'type' => 'integer'
            ),
            'date' => array(
                'type' => 'string',
                'format' => 'date',
                'required' => true
            ),
            'start_time' => array(
                'type' => 'string',
                'required' => true
            ),
            'end_time' => array(
                'type' => 'string',
                'required' => true
            ),
            'room' => array(
                'type' => 'string'
            ),
            'max_capacity' => array(
                'type' => 'integer',
                'minimum' => 1
            ),
            'notes' => array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field'
            )
        );
    }
}