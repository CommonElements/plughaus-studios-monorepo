/**
 * Vireo Sports League - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        VireoSportsLeague.init();
    });
    
    var VireoSportsLeague = {
        
        init: function() {
            this.initStandings();
            this.initSchedule();
            this.initPlayerStats();
            this.initCalendar();
        },
        
        initStandings: function() {
            // Make standings table sortable
            $('.vsl-league-table th').on('click', function() {
                var column = $(this).data('column');
                if (column) {
                    VireoSportsLeague.sortTable($(this).closest('table'), column);
                }
            });
        },
        
        initSchedule: function() {
            // Filter matches by date/team
            $('.vsl-schedule-filter').on('change', function() {
                VireoSportsLeague.filterMatches();
            });
            
            // Live score updates
            if ($('.vsl-live-scores').length) {
                setInterval(function() {
                    VireoSportsLeague.updateLiveScores();
                }, 30000); // Update every 30 seconds
            }
        },
        
        initPlayerStats: function() {
            // Player stats tabs
            $('.vsl-stats-tab').on('click', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                
                $('.vsl-stats-tab').removeClass('active');
                $(this).addClass('active');
                
                $('.vsl-stats-content').hide();
                $(target).show();
            });
        },
        
        initCalendar: function() {
            // Calendar navigation
            $('.vsl-calendar-prev').on('click', function() {
                VireoSportsLeague.navigateCalendar(-1);
            });
            
            $('.vsl-calendar-next').on('click', function() {
                VireoSportsLeague.navigateCalendar(1);
            });
            
            // Day click events
            $('.vsl-calendar-day').on('click', function() {
                var date = $(this).data('date');
                if (date) {
                    VireoSportsLeague.showDayMatches(date);
                }
            });
        },
        
        sortTable: function(table, column) {
            var rows = table.find('tbody tr').toArray();
            var isNumeric = table.find('th[data-column="' + column + '"]').hasClass('numeric');
            var currentSort = table.data('sort-' + column) || 'asc';
            var newSort = currentSort === 'asc' ? 'desc' : 'asc';
            
            rows.sort(function(a, b) {
                var aVal = $(a).find('td').eq($(table.find('th[data-column="' + column + '"]')).index()).text();
                var bVal = $(b).find('td').eq($(table.find('th[data-column="' + column + '"]')).index()).text();
                
                if (isNumeric) {
                    aVal = parseFloat(aVal) || 0;
                    bVal = parseFloat(bVal) || 0;
                }
                
                if (newSort === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
            
            table.find('tbody').empty().append(rows);
            table.data('sort-' + column, newSort);
            
            // Update sort indicators
            table.find('th').removeClass('sort-asc sort-desc');
            table.find('th[data-column="' + column + '"]').addClass('sort-' + newSort);
        },
        
        filterMatches: function() {
            var dateFilter = $('.vsl-date-filter').val();
            var teamFilter = $('.vsl-team-filter').val();
            
            $('.vsl-match-card').each(function() {
                var match = $(this);
                var matchDate = match.data('date');
                var homeTeam = match.data('home-team');
                var awayTeam = match.data('away-team');
                var show = true;
                
                if (dateFilter && matchDate !== dateFilter) {
                    show = false;
                }
                
                if (teamFilter && homeTeam !== teamFilter && awayTeam !== teamFilter) {
                    show = false;
                }
                
                if (show) {
                    match.show();
                } else {
                    match.hide();
                }
            });
        },
        
        updateLiveScores: function() {
            var liveMatches = $('.vsl-match-card[data-status="live"]');
            
            if (liveMatches.length === 0) {
                return;
            }
            
            var matchIds = [];
            liveMatches.each(function() {
                matchIds.push($(this).data('match-id'));
            });
            
            $.ajax({
                url: vsl_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vsl_get_live_scores',
                    nonce: vsl_ajax.nonce,
                    match_ids: matchIds
                },
                success: function(response) {
                    if (response.success && response.data.matches) {
                        $.each(response.data.matches, function(matchId, matchData) {
                            var matchCard = $('.vsl-match-card[data-match-id="' + matchId + '"]');
                            matchCard.find('.vsl-home-score').text(matchData.home_score);
                            matchCard.find('.vsl-away-score').text(matchData.away_score);
                            matchCard.find('.vsl-match-time').text(matchData.time);
                        });
                    }
                }
            });
        },
        
        navigateCalendar: function(direction) {
            var currentMonth = $('.vsl-calendar').data('current-month');
            var currentYear = $('.vsl-calendar').data('current-year');
            
            var newDate = new Date(currentYear, currentMonth + direction, 1);
            
            VireoSportsLeague.loadCalendar(newDate.getFullYear(), newDate.getMonth());
        },
        
        loadCalendar: function(year, month) {
            $.ajax({
                url: vsl_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vsl_load_calendar',
                    nonce: vsl_ajax.nonce,
                    year: year,
                    month: month
                },
                success: function(response) {
                    if (response.success) {
                        $('.vsl-calendar').replaceWith(response.data.html);
                        VireoSportsLeague.initCalendar();
                    }
                }
            });
        },
        
        showDayMatches: function(date) {
            $.ajax({
                url: vsl_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vsl_get_day_matches',
                    nonce: vsl_ajax.nonce,
                    date: date
                },
                success: function(response) {
                    if (response.success) {
                        VireoSportsLeague.showModal('Matches for ' + date, response.data.html);
                    }
                }
            });
        },
        
        showModal: function(title, content) {
            var modal = $('<div class="vsl-modal-overlay">' +
                '<div class="vsl-modal">' +
                '<div class="vsl-modal-header">' +
                '<h3>' + title + '</h3>' +
                '<button class="vsl-modal-close">&times;</button>' +
                '</div>' +
                '<div class="vsl-modal-content">' + content + '</div>' +
                '</div>' +
                '</div>');
            
            $('body').append(modal);
            
            modal.on('click', '.vsl-modal-close, .vsl-modal-overlay', function(e) {
                if (e.target === this) {
                    modal.remove();
                }
            });
        }
    };
    
    // Make available globally
    window.VireoSportsLeague = VireoSportsLeague;
    
})(jQuery);