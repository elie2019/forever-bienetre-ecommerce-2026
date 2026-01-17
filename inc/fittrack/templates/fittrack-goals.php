<?php
/**
 * Template Name: FitTrack Goals
 * Template for setting and tracking fitness goals
 */

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header();

global $wpdb;
$user_id = get_current_user_id();
$today = date('Y-m-d');

// Get active goals
$goals_table = $wpdb->prefix . 'fittrack_goals';
$active_goals = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $goals_table WHERE user_id = %d AND status = 'active' ORDER BY created_at DESC",
    $user_id
));

// Get completed goals
$completed_goals = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $goals_table WHERE user_id = %d AND status = 'completed' ORDER BY completed_at DESC LIMIT 5",
    $user_id
));

// Calculate progress for each goal
function calculate_goal_progress($goal) {
    global $wpdb, $user_id;

    $progress = 0;
    $current_value = 0;

    switch ($goal->goal_type) {
        case 'weight_loss':
        case 'weight_gain':
            // Get latest weight
            $progress_table = $wpdb->prefix . 'fittrack_progress_logs';
            $latest_weight = $wpdb->get_var($wpdb->prepare(
                "SELECT weight FROM $progress_table WHERE user_id = %d ORDER BY date DESC LIMIT 1",
                $user_id
            ));

            if ($latest_weight) {
                $current_value = floatval($latest_weight);
                $start = floatval($goal->start_value);
                $target = floatval($goal->target_value);

                if ($goal->goal_type === 'weight_loss') {
                    $total_to_lose = $start - $target;
                    $lost_so_far = $start - $current_value;
                    $progress = $total_to_lose > 0 ? ($lost_so_far / $total_to_lose) * 100 : 0;
                } else {
                    $total_to_gain = $target - $start;
                    $gained_so_far = $current_value - $start;
                    $progress = $total_to_gain > 0 ? ($gained_so_far / $total_to_gain) * 100 : 0;
                }
            }
            break;

        case 'workout_frequency':
            // Count workouts this week
            $workout_table = $wpdb->prefix . 'fittrack_workout_logs';
            $workouts_this_week = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $workout_table
                 WHERE user_id = %d
                 AND date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)",
                $user_id
            ));

            $current_value = intval($workouts_this_week);
            $progress = (floatval($goal->target_value) > 0)
                ? ($current_value / floatval($goal->target_value)) * 100
                : 0;
            break;

        case 'calorie_target':
            // Get today's calories
            $nutrition_table = $wpdb->prefix . 'fittrack_nutrition_logs';
            $today_calories = $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(calories) FROM $nutrition_table WHERE user_id = %d AND date = %s",
                $user_id,
                date('Y-m-d')
            ));

            $current_value = intval($today_calories);
            $progress = (floatval($goal->target_value) > 0)
                ? ($current_value / floatval($goal->target_value)) * 100
                : 0;
            break;
    }

    return [
        'progress' => min(max($progress, 0), 100),
        'current_value' => $current_value
    ];
}
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
            <h1 class="text-4xl font-bold text-gray-900 mb-2">🎯 Goals Manager</h1>
            <p class="text-gray-600">Set goals, track progress, and achieve your fitness targets</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Active Goals -->
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-2xl font-bold text-gray-900">Active Goals</h2>

                <?php if (empty($active_goals)): ?>
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Active Goals</h3>
                    <p class="text-gray-600 mb-6">Start by setting your first fitness goal!</p>
                    <button onclick="document.getElementById('goal-title').focus()" class="btn btn-primary">
                        Create Your First Goal
                    </button>
                </div>
                <?php else: ?>
                    <?php foreach ($active_goals as $goal):
                        $goal_data = calculate_goal_progress($goal);
                        $progress = $goal_data['progress'];
                        $current_value = $goal_data['current_value'];

                        // Determine goal type icon and color
                        $icons = [
                            'weight_loss' => ['icon' => '⚖️', 'color' => 'green'],
                            'weight_gain' => ['icon' => '💪', 'color' => 'blue'],
                            'workout_frequency' => ['icon' => '🏋️', 'color' => 'orange'],
                            'calorie_target' => ['icon' => '🍎', 'color' => 'purple'],
                            'other' => ['icon' => '🎯', 'color' => 'gray']
                        ];

                        $goal_config = $icons[$goal->goal_type] ?? $icons['other'];
                        $icon = $goal_config['icon'];
                        $color = $goal_config['color'];

                        // Days remaining
                        $deadline = strtotime($goal->deadline);
                        $today_ts = strtotime($today);
                        $days_remaining = max(0, ceil(($deadline - $today_ts) / 86400));
                    ?>
                    <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-start gap-3">
                                <span class="text-3xl"><?php echo $icon; ?></span>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900"><?php echo esc_html($goal->goal_title); ?></h3>
                                    <p class="text-sm text-gray-500"><?php echo esc_html(ucwords(str_replace('_', ' ', $goal->goal_type))); ?></p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button class="complete-goal text-green-600 hover:text-green-800" data-goal-id="<?php echo $goal->id; ?>" title="Mark as completed">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                                <button class="delete-goal text-red-600 hover:text-red-800" data-goal-id="<?php echo $goal->id; ?>" title="Delete goal">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                <span class="text-sm font-bold text-<?php echo $color; ?>-600"><?php echo number_format($progress, 1); ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-<?php echo $color; ?>-600 h-3 rounded-full transition-all" style="width: <?php echo min($progress, 100); ?>%"></div>
                            </div>
                        </div>

                        <!-- Goal Details -->
                        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Current</p>
                                <p class="text-lg font-semibold text-gray-900"><?php echo $current_value; ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Target</p>
                                <p class="text-lg font-semibold text-gray-900"><?php echo $goal->target_value; ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Days Left</p>
                                <p class="text-lg font-semibold text-<?php echo $days_remaining < 7 ? 'red' : 'gray'; ?>-900">
                                    <?php echo $days_remaining; ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!empty($goal->description)): ?>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600 italic">"<?php echo esc_html($goal->description); ?>"</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Completed Goals -->
                <?php if (!empty($completed_goals)): ?>
                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Recently Completed 🎉</h2>
                    <div class="space-y-4">
                        <?php foreach ($completed_goals as $goal): ?>
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-gray-900"><?php echo esc_html($goal->goal_title); ?></h3>
                                    <p class="text-sm text-gray-600">Completed on <?php echo date('M d, Y', strtotime($goal->completed_at)); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Create Goal Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Goal</h2>

                    <form id="create-goal-form" class="space-y-4">
                        <!-- Goal Title -->
                        <div>
                            <label for="goal-title" class="block text-sm font-medium text-gray-700 mb-2">
                                Goal Title *
                            </label>
                            <input type="text" id="goal-title" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Lose 10kg">
                        </div>

                        <!-- Goal Type -->
                        <div>
                            <label for="goal-type" class="block text-sm font-medium text-gray-700 mb-2">
                                Goal Type *
                            </label>
                            <select id="goal-type" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select type</option>
                                <option value="weight_loss">⚖️ Weight Loss</option>
                                <option value="weight_gain">💪 Weight Gain</option>
                                <option value="workout_frequency">🏋️ Workout Frequency</option>
                                <option value="calorie_target">🍎 Daily Calorie Target</option>
                                <option value="other">🎯 Other</option>
                            </select>
                        </div>

                        <!-- Target Value -->
                        <div>
                            <label for="target-value" class="block text-sm font-medium text-gray-700 mb-2">
                                Target Value *
                            </label>
                            <input type="number" id="target-value" step="0.1" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., 70 (kg), 5 (workouts/week)">
                            <p class="text-xs text-gray-500 mt-1" id="target-hint">Enter your target value</p>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">
                                Deadline *
                            </label>
                            <input type="date" id="deadline" required
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description / Why?
                            </label>
                            <textarea id="description" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Why is this goal important to you?"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg">
                            Create Goal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Update target hint based on goal type
    $('#goal-type').on('change', function() {
        const type = $(this).val();
        const hints = {
            'weight_loss': 'Target weight in kg',
            'weight_gain': 'Target weight in kg',
            'workout_frequency': 'Workouts per week',
            'calorie_target': 'Daily calorie goal',
            'other': 'Your target value'
        };
        $('#target-hint').text(hints[type] || 'Enter your target value');
    });

    // Handle form submission
    $('#create-goal-form').on('submit', function(e) {
        e.preventDefault();

        const goalData = {
            action: 'fittrack_create_goal',
            nonce: fittrackData.nonce,
            goal_title: $('#goal-title').val(),
            goal_type: $('#goal-type').val(),
            target_value: parseFloat($('#target-value').val()),
            deadline: $('#deadline').val(),
            description: $('#description').val()
        };

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: goalData,
            success: function(response) {
                if (response.success) {
                    alert('Goal created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (response.data || 'Failed to create goal'));
                }
            },
            error: function() {
                alert('Network error. Please try again.');
            }
        });
    });

    // Handle goal completion
    $('.complete-goal').on('click', function() {
        if (!confirm('Mark this goal as completed?')) return;

        const goalId = $(this).data('goal-id');

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fittrack_complete_goal',
                nonce: fittrackData.nonce,
                goal_id: goalId
            },
            success: function(response) {
                if (response.success) {
                    alert('🎉 Congratulations! Goal completed!');
                    location.reload();
                } else {
                    alert('Error completing goal');
                }
            }
        });
    });

    // Handle goal deletion
    $('.delete-goal').on('click', function() {
        if (!confirm('Delete this goal permanently?')) return;

        const goalId = $(this).data('goal-id');

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fittrack_delete_goal',
                nonce: fittrackData.nonce,
                goal_id: goalId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting goal');
                }
            }
        });
    });
});
</script>

<?php get_footer(); ?>
