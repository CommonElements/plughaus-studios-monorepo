/**
 * GymFlow Admin JavaScript
 * 
 * Handles admin interface interactions and AJAX requests
 * 
 * @package GymFlow
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Main GymFlow Admin Object
     */
    const GymFlowAdmin = {
        
        /**
         * Initialize admin functionality
         */
        init: function() {
            this.bindEvents();
            this.initDashboard();
            this.initCalendar();
            this.initMemberManagement();
            this.initEquipmentManagement();
            this.initProFeatureHandlers();
        },

        /**
         * Bind global event handlers
         */
        bindEvents: function() {
            // Form validation
            $(document).on('submit', '.gymflow-form', this.validateForm);
            
            // AJAX form submissions
            $(document).on('submit', '.gymflow-ajax-form', this.handleAjaxForm);
            
            // Tab navigation
            $(document).on('click', '.gymflow-tab', this.switchTab);
            
            // Confirmation dialogs
            $(document).on('click', '[data-confirm]', this.confirmAction);
            
            // Search functionality
            $(document).on('input', '.gymflow-search', this.debounce(this.handleSearch, 300));
        },

        /**
         * Initialize dashboard widgets
         */
        initDashboard: function() {
            this.loadDashboardStats();
            this.initChartsIfPro();
            
            // Refresh dashboard every 5 minutes
            setInterval(() => {
                this.loadDashboardStats();
            }, 300000);
        },

        /**
         * Load dashboard statistics
         */
        loadDashboardStats: function() {
            const $statsContainer = $('.gymflow-dashboard-stats');
            
            if ($statsContainer.length === 0) return;
            
            $.ajax({
                url: gymflowAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'gymflow_get_dashboard_stats',
                    nonce: gymflowAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        GymFlowAdmin.updateDashboardStats(response.data);
                    }
                },
                error: function() {
                    console.warn('Failed to load dashboard stats');
                }
            });
        },

        /**
         * Update dashboard statistics display
         */
        updateDashboardStats: function(stats) {
            $('.stat-active-members .gymflow-stat-value').text(stats.activeMembers || 0);
            $('.stat-todays-classes .gymflow-stat-value').text(stats.todaysClasses || 0);
            $('.stat-available-equipment .gymflow-stat-value').text(stats.availableEquipment || 0);
            $('.stat-monthly-revenue .gymflow-stat-value').text(stats.monthlyRevenue || '$0');
        },

        /**
         * Initialize calendar functionality
         */
        initCalendar: function() {
            const $calendar = $('.gymflow-calendar');
            
            if ($calendar.length === 0) return;
            
            // Calendar navigation
            $('.gymflow-calendar-prev').on('click', () => {
                this.navigateCalendar('prev');
            });
            
            $('.gymflow-calendar-next').on('click', () => {
                this.navigateCalendar('next');
            });
            
            // Class item interactions
            $(document).on('click', '.gymflow-class-item', this.handleClassClick);
            
            this.loadCalendarEvents();
        },

        /**
         * Navigate calendar view
         */
        navigateCalendar: function(direction) {
            const currentDate = $('.gymflow-calendar').data('current-date') || new Date().toISOString().split('T')[0];
            const date = new Date(currentDate);
            
            if (direction === 'prev') {
                date.setMonth(date.getMonth() - 1);
            } else {
                date.setMonth(date.getMonth() + 1);
            }
            
            $('.gymflow-calendar').data('current-date', date.toISOString().split('T')[0]);
            this.loadCalendarEvents();
        },

        /**
         * Load calendar events
         */
        loadCalendarEvents: function() {
            const currentDate = $('.gymflow-calendar').data('current-date') || new Date().toISOString().split('T')[0];
            
            $.ajax({
                url: gymflowAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'gymflow_get_calendar_events',
                    date: currentDate,
                    nonce: gymflowAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        GymFlowAdmin.renderCalendarEvents(response.data);
                    }
                }
            });
        },

        /**
         * Render calendar events
         */
        renderCalendarEvents: function(events) {
            $('.gymflow-calendar-events').empty();
            
            events.forEach(event => {
                const $event = $(`
                    <div class="gymflow-class-item" data-class-id="${event.id}">
                        <div class="class-title">${event.title}</div>
                        <div class="class-time">${event.time}</div>
                        <div class="class-trainer">${event.trainer}</div>
                    </div>
                `);
                
                $('.gymflow-calendar-events').append($event);
            });
        },

        /**
         * Handle class item click
         */
        handleClassClick: function(e) {
            const classId = $(this).data('class-id');
            
            if (gymflowAdmin.isPro) {
                // Pro version: Open class details modal
                GymFlowAdmin.openClassModal(classId);
            } else {
                // Free version: Show upgrade notice
                GymFlowAdmin.showProFeatureNotice('class management');
            }
        },

        /**
         * Initialize member management
         */
        initMemberManagement: function() {
            // Member search
            $('.gymflow-member-search').on('input', this.debounce(this.searchMembers, 300));
            
            // Member actions
            $(document).on('click', '.member-action', this.handleMemberAction);
            
            // Bulk actions
            $('.gymflow-bulk-action-apply').on('click', this.handleBulkAction);
        },

        /**
         * Search members
         */
        searchMembers: function() {
            const query = $('.gymflow-member-search').val();
            
            $.ajax({
                url: gymflowAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'gymflow_search_members',
                    query: query,
                    nonce: gymflowAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        GymFlowAdmin.updateMembersList(response.data);
                    }
                }
            });
        },

        /**
         * Update members list display
         */
        updateMembersList: function(members) {
            const $membersList = $('.gymflow-members-list');
            $membersList.empty();
            
            members.forEach(member => {
                const $member = $(`
                    <div class="gymflow-member-card" data-member-id="${member.id}">
                        <div class="gymflow-member-header">
                            <div class="gymflow-member-avatar">${member.initials}</div>
                            <div class="gymflow-member-info">
                                <h4>${member.name}</h4>
                                <span class="membership-status ${member.status}">${member.status}</span>
                            </div>
                        </div>
                        <div class="gymflow-member-actions">
                            <a href="#" class="member-action" data-action="edit">Edit</a>
                            <a href="#" class="member-action" data-action="view">View</a>
                        </div>
                    </div>
                `);
                
                $membersList.append($member);
            });
        },

        /**
         * Handle member actions
         */
        handleMemberAction: function(e) {
            e.preventDefault();
            
            const action = $(this).data('action');
            const memberId = $(this).closest('.gymflow-member-card').data('member-id');
            
            switch (action) {
                case 'edit':
                    window.location.href = `admin.php?page=gymflow-members&action=edit&id=${memberId}`;
                    break;
                case 'view':
                    GymFlowAdmin.viewMemberDetails(memberId);
                    break;
                case 'delete':
                    if (confirm('Are you sure you want to delete this member?')) {
                        GymFlowAdmin.deleteMember(memberId);
                    }
                    break;
            }
        },

        /**
         * Initialize equipment management
         */
        initEquipmentManagement: function() {
            // Equipment booking
            $(document).on('click', '.book-equipment', this.handleEquipmentBooking);
            
            // Equipment status updates
            $(document).on('change', '.equipment-status-select', this.updateEquipmentStatus);
        },

        /**
         * Handle equipment booking
         */
        handleEquipmentBooking: function(e) {
            e.preventDefault();
            
            if (!gymflowAdmin.isPro) {
                GymFlowAdmin.showProFeatureNotice('equipment booking');
                return;
            }
            
            const equipmentId = $(this).data('equipment-id');
            GymFlowAdmin.openBookingModal(equipmentId);
        },

        /**
         * Initialize pro feature handlers
         */
        initProFeatureHandlers: function() {
            // Pro feature clicks
            $(document).on('click', '.gymflow-pro-feature', function(e) {
                if (!gymflowAdmin.isPro) {
                    e.preventDefault();
                    GymFlowAdmin.showProFeatureNotice();
                }
            });
            
            // Upgrade notices
            $('.gymflow-upgrade-notice .notice-dismiss').on('click', this.dismissUpgradeNotice);
        },

        /**
         * Show pro feature notice
         */
        showProFeatureNotice: function(feature = 'advanced features') {
            const message = `This ${feature} feature is available in GymFlow Pro. Upgrade now to unlock all premium features!`;
            
            const $notice = $(`
                <div class="gymflow-pro-modal">
                    <div class="gymflow-pro-modal-content">
                        <span class="gymflow-pro-modal-close">&times;</span>
                        <h3>🏋️ Upgrade to GymFlow Pro</h3>
                        <p>${message}</p>
                        <div class="gymflow-pro-features">
                            <ul>
                                <li>✅ Automated billing & payments</li>
                                <li>✅ Member mobile app</li>
                                <li>✅ Advanced analytics</li>
                                <li>✅ Equipment booking system</li>
                                <li>✅ Email automation</li>
                                <li>✅ Priority support</li>
                            </ul>
                        </div>
                        <div class="gymflow-pro-actions">
                            <a href="${gymflowAdmin.upgradeUrl}" class="button button-primary" target="_blank">
                                Upgrade to Pro - $149/year
                            </a>
                            <button class="button gymflow-pro-modal-close">Maybe Later</button>
                        </div>
                    </div>
                </div>
            `);
            
            $('body').append($notice);
            
            // Close modal handlers
            $('.gymflow-pro-modal-close').on('click', function() {
                $('.gymflow-pro-modal').remove();
            });
            
            // Close on outside click
            $('.gymflow-pro-modal').on('click', function(e) {
                if (e.target === this) {
                    $(this).remove();
                }
            });
        },

        /**
         * Handle AJAX form submissions
         */
        handleAjaxForm: function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submit = $form.find('[type="submit"]');
            const originalText = $submit.text();
            
            // Show loading state
            $submit.prop('disabled', true).text('Processing...');
            
            $.ajax({
                url: gymflowAdmin.ajaxUrl,
                type: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        GymFlowAdmin.showNotice('success', response.data.message || 'Operation completed successfully!');
                        
                        // Reset form if specified
                        if (response.data.reset_form) {
                            $form[0].reset();
                        }
                        
                        // Reload page if specified
                        if (response.data.reload) {
                            location.reload();
                        }
                    } else {
                        GymFlowAdmin.showNotice('error', response.data || 'An error occurred.');
                    }
                },
                error: function() {
                    GymFlowAdmin.showNotice('error', 'Network error. Please try again.');
                },
                complete: function() {
                    $submit.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Form validation
         */
        validateForm: function(e) {
            const $form = $(this);
            let isValid = true;
            
            // Clear previous errors
            $form.find('.error').removeClass('error');
            $form.find('.error-message').remove();
            
            // Required field validation
            $form.find('[required]').each(function() {
                const $field = $(this);
                const value = $field.val().trim();
                
                if (!value) {
                    isValid = false;
                    $field.addClass('error');
                    $field.after('<span class="error-message">This field is required.</span>');
                }
            });
            
            // Email validation
            $form.find('[type="email"]').each(function() {
                const $field = $(this);
                const value = $field.val().trim();
                
                if (value && !GymFlowAdmin.isValidEmail(value)) {
                    isValid = false;
                    $field.addClass('error');
                    $field.after('<span class="error-message">Please enter a valid email address.</span>');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                $form.find('.error').first().focus();
            }
        },

        /**
         * Validate email format
         */
        isValidEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        /**
         * Show admin notice
         */
        showNotice: function(type, message) {
            const $notice = $(`
                <div class="notice notice-${type} is-dismissible">
                    <p>${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            `);
            
            $('.gymflow-admin-page').prepend($notice);
            
            // Auto-dismiss success notices
            if (type === 'success') {
                setTimeout(() => {
                    $notice.fadeOut();
                }, 3000);
            }
            
            // Handle dismiss button
            $notice.find('.notice-dismiss').on('click', function() {
                $notice.fadeOut();
            });
        },

        /**
         * Debounce function for search inputs
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Handle search functionality
         */
        handleSearch: function() {
            const query = $(this).val();
            const searchType = $(this).data('search-type');
            
            if (query.length < 2) {
                return;
            }
            
            // Implement search based on type
            switch (searchType) {
                case 'members':
                    GymFlowAdmin.searchMembers();
                    break;
                case 'classes':
                    GymFlowAdmin.searchClasses();
                    break;
                case 'equipment':
                    GymFlowAdmin.searchEquipment();
                    break;
            }
        },

        /**
         * Switch between admin tabs
         */
        switchTab: function(e) {
            e.preventDefault();
            
            const targetTab = $(this).data('tab');
            
            // Update tab navigation
            $('.gymflow-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Show target content
            $('.gymflow-tab-content').hide();
            $(targetTab).show();
        },

        /**
         * Confirmation dialog for dangerous actions
         */
        confirmAction: function(e) {
            const message = $(this).data('confirm');
            
            if (!confirm(message)) {
                e.preventDefault();
            }
        },

        /**
         * Initialize charts for pro version
         */
        initChartsIfPro: function() {
            if (!gymflowAdmin.isPro || typeof Chart === 'undefined') {
                return;
            }
            
            // Initialize various charts
            this.initRevenueChart();
            this.initMembershipChart();
            this.initAttendanceChart();
        },

        /**
         * Initialize revenue chart (Pro feature)
         */
        initRevenueChart: function() {
            const ctx = document.getElementById('gymflow-revenue-chart');
            if (!ctx) return;
            
            // Chart implementation would go here
            console.log('Revenue chart initialized (Pro feature)');
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        GymFlowAdmin.init();
    });

    // Make GymFlowAdmin globally available
    window.GymFlowAdmin = GymFlowAdmin;

})(jQuery);