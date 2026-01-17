/**
 * FitTrack Pro - Main JavaScript
 */

(function($) {
    'use strict';

    // Initialize
    $(document).ready(function() {
        initFitTrack();
    });

    function initFitTrack() {
        // Check if we're on a FitTrack page
        if (!$('.fittrack-dashboard, .fittrack-pricing, .fittrack-nutrition').length) {
            return;
        }

        console.log('FitTrack initialized');

        // Initialize components
        initNotifications();
        initForms();
    }

    /**
     * Notifications
     */
    function initNotifications() {
        window.showNotification = function(message, type = 'success') {
            const notification = $('<div>')
                .addClass('fittrack-notification ' + type)
                .html(`
                    <div class="flex items-center">
                        <div class="mr-3">
                            ${type === 'success'
                                ? '<svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                                : '<svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                            }
                        </div>
                        <div>
                            <div class="font-semibold">${type === 'success' ? 'Success!' : 'Error'}</div>
                            <div class="text-sm text-gray-600">${message}</div>
                        </div>
                    </div>
                `)
                .appendTo('body');

            setTimeout(() => {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        };
    }

    /**
     * Forms
     */
    function initForms() {
        // Generic AJAX form handler
        $(document).on('submit', '.fittrack-form-ajax', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const buttonText = $button.text();

            // Disable button
            $button.prop('disabled', true).text('Loading...');

            // Get form data
            const formData = new FormData(this);
            formData.append('nonce', fittrackData.nonce);

            $.ajax({
                url: fittrackData.ajaxUrl,
                type: 'POST',
                data: Object.fromEntries(formData),
                success: function(response) {
                    if (response.success) {
                        showNotification(response.data.message || 'Success!', 'success');
                        $form[0].reset();

                        // Callback if specified
                        if (typeof $form.data('callback') === 'function') {
                            $form.data('callback')(response.data);
                        }
                    } else {
                        showNotification(response.data.message || 'An error occurred', 'error');
                    }
                },
                error: function() {
                    showNotification('An error occurred. Please try again.', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).text(buttonText);
                }
            });
        });
    }

    /**
     * Nutrition helpers
     */
    window.FitTrackNutrition = {
        searchFoods: function(query, callback) {
            $.ajax({
                url: fittrackData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fittrack_search_foods',
                    query: query,
                    nonce: fittrackData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        callback(response.data.foods);
                    }
                }
            });
        },

        addMeal: function(data, callback) {
            $.ajax({
                url: fittrackData.ajaxUrl,
                type: 'POST',
                data: Object.assign({
                    action: 'fittrack_add_meal',
                    nonce: fittrackData.nonce
                }, data),
                success: function(response) {
                    if (response.success) {
                        showNotification('Meal added successfully!', 'success');
                        if (callback) callback();
                    } else {
                        showNotification(response.data.message, 'error');
                    }
                }
            });
        }
    };

    /**
     * Workout helpers
     */
    window.FitTrackWorkout = {
        logWorkout: function(data, callback) {
            $.ajax({
                url: fittrackData.ajaxUrl,
                type: 'POST',
                data: Object.assign({
                    action: 'fittrack_log_workout',
                    nonce: fittrackData.nonce
                }, data),
                success: function(response) {
                    if (response.success) {
                        showNotification('Workout logged successfully!', 'success');
                        if (callback) callback();
                    } else {
                        showNotification(response.data.message, 'error');
                    }
                }
            });
        }
    };

    /**
     * Progress helpers
     */
    window.FitTrackProgress = {
        addProgress: function(data, callback) {
            $.ajax({
                url: fittrackData.ajaxUrl,
                type: 'POST',
                data: Object.assign({
                    action: 'fittrack_add_progress',
                    nonce: fittrackData.nonce
                }, data),
                success: function(response) {
                    if (response.success) {
                        showNotification('Progress logged successfully!', 'success');
                        if (callback) callback();
                    } else {
                        showNotification(response.data.message, 'error');
                    }
                }
            });
        }
    };

    /**
     * Loading spinner
     */
    window.showLoader = function(target) {
        const $loader = $('<div class="flex justify-center items-center py-8"><div class="fittrack-spinner"></div></div>');
        $(target).html($loader);
    };

})(jQuery);
