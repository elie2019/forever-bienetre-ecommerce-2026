<?php
/**
 * Template Name: FitTrack Progress
 * Template for tracking body measurements and progress
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

// Get progress logs for chart (last 30 days)
$progress_table = $wpdb->prefix . 'fittrack_progress_logs';
$progress_data = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $progress_table
     WHERE user_id = %d
     AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
     ORDER BY date ASC",
    $user_id
));

// Get latest measurement
$latest_measurement = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $progress_table WHERE user_id = %d ORDER BY date DESC LIMIT 1",
    $user_id
));

// Get first measurement for comparison
$first_measurement = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $progress_table WHERE user_id = %d ORDER BY date ASC LIMIT 1",
    $user_id
));

// Calculate progress
$weight_change = 0;
$weight_change_percent = 0;
if ($latest_measurement && $first_measurement) {
    $weight_change = $latest_measurement->weight - $first_measurement->weight;
    if ($first_measurement->weight > 0) {
        $weight_change_percent = ($weight_change / $first_measurement->weight) * 100;
    }
}

// Prepare chart data
$chart_labels = [];
$chart_weights = [];
foreach ($progress_data as $log) {
    $chart_labels[] = date('M d', strtotime($log->date));
    $chart_weights[] = floatval($log->weight);
}
?>

<!-- Load jQuery and Chart.js inline -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Define fittrackData inline for immediate availability
const fittrackData = {
    ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('fittrack_nonce'); ?>',
    isLoggedIn: true,
    userId: <?php echo get_current_user_id(); ?>,
    chartLabels: <?php echo json_encode($chart_labels); ?>,
    chartWeights: <?php echo json_encode($chart_weights); ?>
};
</script>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">📈 Progress Tracker</h1>
            <p class="text-gray-600">Track your body measurements and visualize your transformation</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Progress Chart & Stats -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Progress Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Current Weight -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600">Current Weight</span>
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">
                            <?php echo $latest_measurement ? number_format($latest_measurement->weight, 1) : '0.0'; ?>
                            <span class="text-lg text-gray-500">kg</span>
                        </p>
                    </div>

                    <!-- Weight Change -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600">Total Change</span>
                            <svg class="w-5 h-5 <?php echo $weight_change < 0 ? 'text-green-600' : 'text-orange-600'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $weight_change < 0 ? 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' : 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'; ?>"></path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold <?php echo $weight_change < 0 ? 'text-green-600' : 'text-orange-600'; ?>">
                            <?php echo ($weight_change > 0 ? '+' : '') . number_format($weight_change, 1); ?>
                            <span class="text-lg">kg</span>
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            <?php echo ($weight_change_percent > 0 ? '+' : '') . number_format($weight_change_percent, 1); ?>%
                        </p>
                    </div>

                    <!-- Measurements Count -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600">Total Logs</span>
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-3xl font-bold text-gray-900"><?php echo count($progress_data); ?></p>
                        <p class="text-sm text-gray-500 mt-1">Last 30 days</p>
                    </div>
                </div>

                <!-- Weight Progress Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Weight Progress (30 Days)</h2>
                    <div class="relative" style="height: 300px;">
                        <canvas id="weight-chart"></canvas>
                    </div>
                </div>

                <!-- Progress History -->
                <?php if (!empty($progress_data)): ?>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Measurement History</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weight</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Body Fat</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Muscle Mass</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach (array_reverse($progress_data) as $log): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($log->date)); ?>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo number_format($log->weight, 1); ?> kg
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo $log->body_fat ? number_format($log->body_fat, 1) . '%' : '-'; ?>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo $log->muscle_mass ? number_format($log->muscle_mass, 1) . ' kg' : '-'; ?>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <button class="delete-log text-red-600 hover:text-red-800" data-log-id="<?php echo $log->id; ?>">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Add Measurement Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Log Measurement</h2>

                    <form id="add-progress-form" class="space-y-4">
                        <!-- Date -->
                        <div>
                            <label for="log-date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date
                            </label>
                            <input type="date" id="log-date" value="<?php echo $today; ?>" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Weight (Required) -->
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                Weight (kg) *
                            </label>
                            <input type="number" id="weight" step="0.1" min="20" max="300" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="75.5">
                        </div>

                        <!-- Body Fat % (Optional) -->
                        <div>
                            <label for="body-fat" class="block text-sm font-medium text-gray-700 mb-2">
                                Body Fat %
                            </label>
                            <input type="number" id="body-fat" step="0.1" min="0" max="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="18.5">
                        </div>

                        <!-- Muscle Mass (Optional) -->
                        <div>
                            <label for="muscle-mass" class="block text-sm font-medium text-gray-700 mb-2">
                                Muscle Mass (kg)
                            </label>
                            <input type="number" id="muscle-mass" step="0.1" min="0" max="150"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="35.0">
                        </div>

                        <!-- Waist (Optional) -->
                        <div>
                            <label for="waist" class="block text-sm font-medium text-gray-700 mb-2">
                                Waist (cm)
                            </label>
                            <input type="number" id="waist" step="0.1" min="0" max="200"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="80.0">
                        </div>

                        <!-- Chest (Optional) -->
                        <div>
                            <label for="chest" class="block text-sm font-medium text-gray-700 mb-2">
                                Chest (cm)
                            </label>
                            <input type="number" id="chest" step="0.1" min="0" max="200"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="95.0">
                        </div>

                        <!-- Hips (Optional) -->
                        <div>
                            <label for="hips" class="block text-sm font-medium text-gray-700 mb-2">
                                Hips (cm)
                            </label>
                            <input type="number" id="hips" step="0.1" min="0" max="200"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="95.0">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea id="notes" rows="2"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="How are you feeling?"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg">
                            Log Measurement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize weight chart
    if (fittrackData.chartLabels.length > 0) {
        const ctx = document.getElementById('weight-chart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: fittrackData.chartLabels,
                datasets: [{
                    label: 'Weight (kg)',
                    data: fittrackData.chartWeights,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return 'Weight: ' + context.parsed.y.toFixed(1) + ' kg';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(1) + ' kg';
                            }
                        }
                    }
                }
            }
        });
    }

    // Handle form submission
    $('#add-progress-form').on('submit', function(e) {
        e.preventDefault();

        const progressData = {
            action: 'fittrack_log_progress',
            nonce: fittrackData.nonce,
            date: $('#log-date').val(),
            weight: parseFloat($('#weight').val()),
            body_fat: $('#body-fat').val() || null,
            muscle_mass: $('#muscle-mass').val() || null,
            waist: $('#waist').val() || null,
            chest: $('#chest').val() || null,
            hips: $('#hips').val() || null,
            notes: $('#notes').val()
        };

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: progressData,
            success: function(response) {
                if (response.success) {
                    alert('Progress logged successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (response.data || 'Failed to log progress'));
                }
            },
            error: function() {
                alert('Network error. Please try again.');
            }
        });
    });

    // Handle log deletion
    $('.delete-log').on('click', function() {
        if (!confirm('Delete this measurement?')) return;

        const logId = $(this).data('log-id');

        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fittrack_delete_progress',
                nonce: fittrackData.nonce,
                log_id: logId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting measurement');
                }
            }
        });
    });
});
</script>

<?php get_footer(); ?>
