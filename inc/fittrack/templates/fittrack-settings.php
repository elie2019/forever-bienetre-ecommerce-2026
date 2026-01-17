<?php
/**
 * Template Name: FitTrack Settings
 * Template for user profile and app settings
 */

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header();

global $wpdb;
$user_id = get_current_user_id();
$current_user = wp_get_current_user();

// Get user meta data
$height = get_user_meta($user_id, 'fittrack_height', true);
$age = get_user_meta($user_id, 'fittrack_age', true);
$gender = get_user_meta($user_id, 'fittrack_gender', true);
$activity_level = get_user_meta($user_id, 'fittrack_activity_level', true);
$units = get_user_meta($user_id, 'fittrack_units', true) ?: 'metric';
$notifications = get_user_meta($user_id, 'fittrack_notifications', true) ?: 'enabled';

// Get subscription info
$subscription_table = $wpdb->prefix . 'fittrack_subscriptions';
$subscription = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $subscription_table WHERE user_id = %d AND status = 'active' ORDER BY created_at DESC LIMIT 1",
    $user_id
));

// Get statistics
$nutrition_table = $wpdb->prefix . 'fittrack_nutrition_logs';
$workout_table = $wpdb->prefix . 'fittrack_workout_logs';
$progress_table = $wpdb->prefix . 'fittrack_progress_logs';
$goals_table = $wpdb->prefix . 'fittrack_goals';

$total_meals = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $nutrition_table WHERE user_id = %d", $user_id));
$total_workouts = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $workout_table WHERE user_id = %d", $user_id));
$total_progress_logs = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $progress_table WHERE user_id = %d", $user_id));
$total_goals = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $goals_table WHERE user_id = %d", $user_id));
?>

<!-- Load jQuery inline -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// Define fittrackData inline for immediate availability
const fittrackData = {
    ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('fittrack_nonce'); ?>',
    isLoggedIn: true,
    userId: <?php echo get_current_user_id(); ?>
};
</script>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">⚙️ Settings</h1>
            <p class="text-gray-600">Manage your profile, preferences, and subscription</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Settings Forms -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Information -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Profile Information</h2>

                    <form id="profile-form" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Age -->
                            <div>
                                <label for="age" class="block text-sm font-medium text-gray-700 mb-2">
                                    Age (years)
                                </label>
                                <input type="number" id="age" min="13" max="120" value="<?php echo esc_attr($age); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Gender -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                    Gender
                                </label>
                                <select id="gender" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Prefer not to say</option>
                                    <option value="male" <?php selected($gender, 'male'); ?>>Male</option>
                                    <option value="female" <?php selected($gender, 'female'); ?>>Female</option>
                                    <option value="other" <?php selected($gender, 'other'); ?>>Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Height -->
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-2">
                                Height (cm)
                            </label>
                            <input type="number" id="height" min="100" max="250" value="<?php echo esc_attr($height); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Activity Level -->
                        <div>
                            <label for="activity-level" class="block text-sm font-medium text-gray-700 mb-2">
                                Activity Level
                            </label>
                            <select id="activity-level" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="sedentary" <?php selected($activity_level, 'sedentary'); ?>>Sedentary (little or no exercise)</option>
                                <option value="light" <?php selected($activity_level, 'light'); ?>>Lightly Active (1-3 days/week)</option>
                                <option value="moderate" <?php selected($activity_level, 'moderate'); ?>>Moderately Active (3-5 days/week)</option>
                                <option value="active" <?php selected($activity_level, 'active'); ?>>Very Active (6-7 days/week)</option>
                                <option value="extra" <?php selected($activity_level, 'extra'); ?>>Extra Active (physical job + training)</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-all">
                            Save Profile
                        </button>
                    </form>
                </div>

                <!-- App Preferences -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">App Preferences</h2>

                    <form id="preferences-form" class="space-y-4">
                        <!-- Units -->
                        <div>
                            <label for="units" class="block text-sm font-medium text-gray-700 mb-2">
                                Measurement Units
                            </label>
                            <select id="units" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="metric" <?php selected($units, 'metric'); ?>>Metric (kg, cm)</option>
                                <option value="imperial" <?php selected($units, 'imperial'); ?>>Imperial (lbs, inches)</option>
                            </select>
                        </div>

                        <!-- Notifications -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notifications
                            </label>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Email Notifications</p>
                                    <p class="text-sm text-gray-500">Receive goal reminders and progress updates</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="notifications" class="sr-only peer" <?php checked($notifications, 'enabled'); ?>>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-all">
                            Save Preferences
                        </button>
                    </form>
                </div>

                <!-- Subscription Management -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Subscription</h2>

                    <?php if ($subscription): ?>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-gray-900"><?php echo esc_html(ucfirst($subscription->plan_name)); ?> Plan</p>
                                        <p class="text-sm text-gray-600">Active subscription</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">€<?php echo number_format($subscription->amount, 2); ?></p>
                                    <p class="text-sm text-gray-600">/month</p>
                                </div>
                            </div>

                            <?php if ($subscription->stripe_subscription_id): ?>
                            <div class="text-sm text-gray-600">
                                <p><strong>Next billing:</strong> <?php echo date('M d, Y', strtotime($subscription->created_at . ' +1 month')); ?></p>
                                <p><strong>Started:</strong> <?php echo date('M d, Y', strtotime($subscription->created_at)); ?></p>
                            </div>

                            <a href="https://billing.stripe.com/p/login/test_..." target="_blank" rel="noopener"
                               class="inline-block px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-all">
                                Manage Billing
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                            </svg>
                            <p class="text-gray-600 mb-4">No active subscription</p>
                            <a href="<?php echo home_url('/fittrack-pricing/'); ?>" class="btn btn-primary">
                                View Plans
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Danger Zone -->
                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6">
                    <h2 class="text-2xl font-bold text-red-900 mb-4">Danger Zone</h2>
                    <p class="text-sm text-red-700 mb-4">These actions are permanent and cannot be undone.</p>

                    <div class="space-y-3">
                        <button id="delete-all-data" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-all">
                            Delete All My Data
                        </button>
                        <button id="cancel-subscription" class="w-full px-6 py-3 bg-white border-2 border-red-600 text-red-600 rounded-lg font-medium hover:bg-red-50 transition-all" <?php echo $subscription ? '' : 'disabled'; ?>>
                            Cancel Subscription
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Account Stats -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Account Overview</h2>

                    <!-- User Info -->
                    <div class="flex items-center gap-4 mb-6 pb-6 border-b">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            <?php echo strtoupper(substr($current_user->display_name, 0, 1)); ?>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo esc_html($current_user->display_name); ?></p>
                            <p class="text-sm text-gray-500"><?php echo esc_html($current_user->user_email); ?></p>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">🥗 Meals Logged</span>
                            <span class="font-bold text-gray-900"><?php echo number_format($total_meals); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">🏋️ Workouts</span>
                            <span class="font-bold text-gray-900"><?php echo number_format($total_workouts); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">📈 Progress Logs</span>
                            <span class="font-bold text-gray-900"><?php echo number_format($total_progress_logs); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">🎯 Goals Created</span>
                            <span class="font-bold text-gray-900"><?php echo number_format($total_goals); ?></span>
                        </div>
                    </div>

                    <!-- Member Since -->
                    <div class="mt-6 pt-6 border-t">
                        <p class="text-sm text-gray-600">Member since</p>
                        <p class="font-semibold text-gray-900"><?php echo date('F Y', strtotime($current_user->user_registered)); ?></p>
                    </div>

                    <!-- Quick Links -->
                    <div class="mt-6 pt-6 border-t space-y-2">
                        <a href="<?php echo home_url('/fittrack-dashboard/'); ?>" class="block text-sm text-blue-600 hover:text-blue-800">
                            → Go to Dashboard
                        </a>
                        <a href="<?php echo wp_logout_url(home_url()); ?>" class="block text-sm text-red-600 hover:text-red-800">
                            → Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Handle profile form submission
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();

        const profileData = {
            action: 'fittrack_update_profile',
            nonce: fittrackData.nonce,
            age: $('#age').val(),
            gender: $('#gender').val(),
            height: $('#height').val(),
            activity_level: $('#activity-level').val()
        };

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: profileData,
            success: function(response) {
                if (response.success) {
                    alert('Profile updated successfully!');
                } else {
                    alert('Error: ' + (response.data || 'Failed to update profile'));
                }
            },
            error: function() {
                alert('Network error. Please try again.');
            }
        });
    });

    // Handle preferences form submission
    $('#preferences-form').on('submit', function(e) {
        e.preventDefault();

        const preferencesData = {
            action: 'fittrack_update_preferences',
            nonce: fittrackData.nonce,
            units: $('#units').val(),
            notifications: $('#notifications').is(':checked') ? 'enabled' : 'disabled'
        };

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: preferencesData,
            success: function(response) {
                if (response.success) {
                    alert('Preferences updated successfully!');
                } else {
                    alert('Error: ' + (response.data || 'Failed to update preferences'));
                }
            },
            error: function() {
                alert('Network error. Please try again.');
            }
        });
    });

    // Handle delete all data
    $('#delete-all-data').on('click', function() {
        if (!confirm('⚠️ WARNING: This will permanently delete ALL your FitTrack data (meals, workouts, progress, goals). This action CANNOT be undone.\n\nAre you absolutely sure?')) return;
        if (!confirm('Final confirmation: Delete ALL my FitTrack data?')) return;

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fittrack_delete_all_data',
                nonce: fittrackData.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('All data deleted successfully.');
                    window.location.href = '<?php echo home_url('/fittrack-pricing/'); ?>';
                } else {
                    alert('Error deleting data');
                }
            }
        });
    });

    // Handle subscription cancellation
    $('#cancel-subscription').on('click', function() {
        if (!confirm('Are you sure you want to cancel your subscription? You will lose access to premium features.')) return;

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fittrack_cancel_subscription',
                nonce: fittrackData.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Subscription cancelled. You will retain access until the end of your billing period.');
                    location.reload();
                } else {
                    alert('Error: ' + (response.data || 'Failed to cancel subscription'));
                }
            }
        });
    });
});
</script>

<?php get_footer(); ?>
