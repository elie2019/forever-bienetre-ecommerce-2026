<?php
/**
 * Template Name: FitTrack Workouts
 * Template for logging and viewing workout sessions
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

// Get all exercises from database
$exercises_query = "SELECT p.ID, p.post_title,
                   pm1.meta_value as exercise_type,
                   pm2.meta_value as muscle_group
                   FROM {$wpdb->posts} p
                   LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'exercise_type'
                   LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'muscle_group'
                   WHERE p.post_type = 'fittrack_exercise' AND p.post_status = 'publish'
                   ORDER BY p.post_title ASC";
$exercises = $wpdb->get_results($exercises_query);

// Get today's workouts
$workout_table = $wpdb->prefix . 'fittrack_workout_logs';
$exercise_table = $wpdb->prefix . 'fittrack_exercise_logs';

$today_workouts = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $workout_table WHERE user_id = %d AND date = %s ORDER BY created_at DESC",
    $user_id,
    $today
));

// Calculate today's totals
$today_duration = 0;
$today_calories = 0;
foreach ($today_workouts as $workout) {
    $today_duration += intval($workout->duration);
    $today_calories += intval($workout->calories_burned);
}
?>

<!-- Load jQuery inline to avoid loading issues -->
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
            <h1 class="text-4xl font-bold text-gray-900 mb-2">💪 Workout Tracker</h1>
            <p class="text-gray-600">Log your workouts and track your fitness progress</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Add Workout Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Log Workout</h2>

                    <form id="add-workout-form" class="space-y-6">
                        <!-- Workout Name -->
                        <div>
                            <label for="workout-name" class="block text-sm font-medium text-gray-700 mb-2">
                                Workout Name
                            </label>
                            <input type="text" id="workout-name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Morning Strength Training, Evening Run" required>
                        </div>

                        <!-- Exercise Selection -->
                        <div>
                            <label for="exercise-select" class="block text-sm font-medium text-gray-700 mb-2">
                                Exercise
                            </label>
                            <select id="exercise-select"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">Select an exercise</option>
                                <?php foreach ($exercises as $exercise): ?>
                                    <option value="<?php echo esc_attr($exercise->ID); ?>"
                                            data-type="<?php echo esc_attr($exercise->exercise_type); ?>"
                                            data-muscle="<?php echo esc_attr($exercise->muscle_group); ?>">
                                        <?php echo esc_html($exercise->post_title); ?>
                                        (<?php echo esc_html($exercise->exercise_type); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Exercise Type Display -->
                        <div id="exercise-info" class="hidden">
                            <div class="flex gap-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" id="exercise-type-badge"></span>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800" id="muscle-group-badge"></span>
                            </div>
                        </div>

                        <!-- Sets, Reps, Weight (for Strength exercises) -->
                        <div id="strength-fields" class="grid grid-cols-3 gap-4 hidden">
                            <div>
                                <label for="sets" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sets
                                </label>
                                <input type="number" id="sets" min="1" value="3"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="reps" class="block text-sm font-medium text-gray-700 mb-2">
                                    Reps
                                </label>
                                <input type="number" id="reps" min="1" value="10"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                    Weight (kg)
                                </label>
                                <input type="number" id="weight" min="0" step="0.5" value="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Duration and Calories -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                                    Duration (minutes)
                                </label>
                                <input type="number" id="duration" min="1" value="30" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="calories-burned" class="block text-sm font-medium text-gray-700 mb-2">
                                    Calories Burned
                                </label>
                                <input type="number" id="calories-burned" min="0" value="200" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes (optional)
                            </label>
                            <textarea id="notes" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="How did the workout feel? Any achievements?"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg">
                            Log Workout
                        </button>
                    </form>
                </div>

                <!-- Today's Workouts List -->
                <?php if (!empty($today_workouts)): ?>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Today's Workouts</h2>

                    <div class="space-y-4">
                        <?php foreach ($today_workouts as $workout):
                            // Get exercises for this workout
                            $workout_exercises = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM $exercise_table WHERE workout_id = %d",
                                $workout->id
                            ));
                        ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo esc_html($workout->workout_name); ?></h3>
                                    <p class="text-sm text-gray-500"><?php echo date('g:i A', strtotime($workout->created_at)); ?></p>
                                </div>
                                <button class="delete-workout text-red-600 hover:text-red-800"
                                        data-workout-id="<?php echo esc_attr($workout->id); ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php echo esc_html($workout->duration); ?> min
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                                    </svg>
                                    <?php echo esc_html($workout->calories_burned); ?> cal
                                </div>
                            </div>

                            <?php if (!empty($workout_exercises)): ?>
                            <div class="border-t pt-3 mt-3">
                                <p class="text-sm font-medium text-gray-700 mb-2">Exercises:</p>
                                <div class="space-y-2">
                                    <?php foreach ($workout_exercises as $ex): ?>
                                    <div class="text-sm text-gray-600 flex justify-between items-center">
                                        <span><?php echo esc_html($ex->exercise_name); ?></span>
                                        <?php if ($ex->sets && $ex->reps): ?>
                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                            <?php echo esc_html($ex->sets); ?> × <?php echo esc_html($ex->reps); ?> reps
                                            <?php if ($ex->weight > 0): ?>
                                                @ <?php echo esc_html($ex->weight); ?>kg
                                            <?php endif; ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($workout->notes)): ?>
                            <div class="border-t pt-3 mt-3">
                                <p class="text-sm text-gray-600 italic">"<?php echo esc_html($workout->notes); ?>"</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Today's Summary</h2>

                    <!-- Total Duration -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Total Duration</span>
                            <span class="text-2xl font-bold text-blue-600"><?php echo $today_duration; ?> min</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <?php
                            $duration_percent = min(($today_duration / 60) * 100, 100); // Goal: 60 min
                            ?>
                            <div class="bg-blue-600 h-3 rounded-full transition-all"
                                 style="width: <?php echo $duration_percent; ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Goal: 60 min/day</p>
                    </div>

                    <!-- Total Calories -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Calories Burned</span>
                            <span class="text-2xl font-bold text-orange-600"><?php echo $today_calories; ?></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <?php
                            $calories_percent = min(($today_calories / 500) * 100, 100); // Goal: 500 cal
                            ?>
                            <div class="bg-orange-600 h-3 rounded-full transition-all"
                                 style="width: <?php echo $calories_percent; ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Goal: 500 cal/day</p>
                    </div>

                    <!-- Workouts Count -->
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Workouts Completed</span>
                            <span class="text-2xl font-bold text-green-600"><?php echo count($today_workouts); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Show/hide strength fields based on exercise type
    $('#exercise-select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const exerciseType = selectedOption.data('type');
        const muscleGroup = selectedOption.data('muscle');

        if (selectedOption.val()) {
            $('#exercise-info').removeClass('hidden');
            $('#exercise-type-badge').text(exerciseType);
            $('#muscle-group-badge').text(muscleGroup);

            // Show strength fields for Strength exercises
            if (exerciseType === 'Strength' || exerciseType === 'Core') {
                $('#strength-fields').removeClass('hidden');
            } else {
                $('#strength-fields').addClass('hidden');
            }
        } else {
            $('#exercise-info').addClass('hidden');
            $('#strength-fields').addClass('hidden');
        }
    });

    // Handle form submission
    $('#add-workout-form').on('submit', function(e) {
        e.preventDefault();

        const selectedExercise = $('#exercise-select option:selected');
        const workoutData = {
            action: 'fittrack_log_workout',
            nonce: fittrackData.nonce,
            workout_name: $('#workout-name').val(),
            date: '<?php echo $today; ?>',
            duration: parseInt($('#duration').val()),
            calories_burned: parseInt($('#calories-burned').val()),
            notes: $('#notes').val(),
            exercise_id: selectedExercise.val(),
            exercise_name: selectedExercise.text().split('(')[0].trim(),
            sets: $('#sets').val() || null,
            reps: $('#reps').val() || null,
            weight: $('#weight').val() || null
        };

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: workoutData,
            success: function(response) {
                if (response.success) {
                    alert('Workout logged successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (response.data || 'Failed to log workout'));
                }
            },
            error: function() {
                alert('Network error. Please try again.');
            }
        });
    });

    // Handle workout deletion
    $('.delete-workout').on('click', function() {
        if (!confirm('Delete this workout?')) return;

        const workoutId = $(this).data('workout-id');

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fittrack_delete_workout',
                nonce: fittrackData.nonce,
                workout_id: workoutId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting workout');
                }
            }
        });
    });
});
</script>

<?php get_footer(); ?>
