<?php
/**
 * Template: FitTrack Dashboard
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header();

$user_id = get_current_user_id();
$subscription = FitTrack_Subscriptions::get_instance()->get_user_subscription($user_id);
$plan = $subscription ? $subscription->plan : 'free';
?>

<div class="fittrack-dashboard min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Welcome Back!</h1>
            <p class="text-gray-600">Your fitness journey at a glance</p>
            <div class="mt-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-teal-100 text-teal-800">
                    <?php echo ucfirst($plan); ?> Plan
                </span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500 mb-1">Today's Calories</div>
                <div class="text-3xl font-bold text-gray-900" id="today-calories">0</div>
                <div class="text-sm text-green-600 mt-1">Target: 2000</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500 mb-1">Workouts This Week</div>
                <div class="text-3xl font-bold text-gray-900" id="week-workouts">0</div>
                <div class="text-sm text-green-600 mt-1">Goal: 5</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500 mb-1">Current Weight</div>
                <div class="text-3xl font-bold text-gray-900" id="current-weight">--</div>
                <div class="text-sm text-gray-600 mt-1">kg</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500 mb-1">Progress</div>
                <div class="text-3xl font-bold text-gray-900" id="progress-percent">0%</div>
                <div class="text-sm text-green-600 mt-1">On track</div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Progress Chart -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Weight Progress</h2>
                    <canvas id="progressChart" class="w-full" height="300"></canvas>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Recent Workouts</h2>
                    <div id="recent-workouts" class="space-y-3">
                        <p class="text-gray-500">No workouts logged yet</p>
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="/fittrack-nutrition" class="block w-full bg-teal-600 hover:bg-teal-700 text-white font-medium py-3 px-4 rounded-lg text-center transition">
                            Log Meal
                        </a>
                        <a href="/fittrack-workouts" class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg text-center transition">
                            Start Workout
                        </a>
                        <a href="/fittrack-progress" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg text-center transition">
                            Update Progress
                        </a>
                    </div>
                </div>

                <?php if ($plan === 'premium'): ?>
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow p-6 text-white">
                    <h3 class="text-xl font-bold mb-2">AI Assistant</h3>
                    <p class="text-sm mb-4 opacity-90">Get personalized nutrition and workout advice</p>
                    <button onclick="openAIAssistant()" class="w-full bg-white text-purple-600 font-medium py-2 px-4 rounded-lg hover:bg-gray-100 transition">
                        Ask AI Now
                    </button>
                </div>
                <?php elseif ($plan === 'free'): ?>
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-lg shadow p-6 text-white">
                    <h3 class="text-xl font-bold mb-2">Upgrade to Pro</h3>
                    <p class="text-sm mb-4 opacity-90">Unlock advanced features and AI coaching</p>
                    <a href="/fittrack-pricing" class="block w-full bg-white text-orange-600 font-medium py-2 px-4 rounded-lg text-center hover:bg-gray-100 transition">
                        View Plans
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load dashboard data
    loadDashboardData();

    function loadDashboardData() {
        // Load progress data for chart
        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fittrack_get_progress_data',
                nonce: fittrackData.nonce
            },
            success: function(response) {
                if (response.success && response.data.logs) {
                    renderProgressChart(response.data.logs);
                }
            }
        });

        // Load today's nutrition
        $.ajax({
            url: fittrackData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'fittrack_get_daily_nutrition',
                date: new Date().toISOString().split('T')[0],
                nonce: fittrackData.nonce
            },
            success: function(response) {
                if (response.success && response.data.summary) {
                    $('#today-calories').text(response.data.summary.total_calories || 0);
                }
            }
        });
    }

    function renderProgressChart(logs) {
        const ctx = document.getElementById('progressChart');
        if (!ctx) return;

        const dates = logs.map(log => log.date).reverse();
        const weights = logs.map(log => parseFloat(log.weight)).reverse();

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Weight (kg)',
                    data: weights,
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    }
});

function openAIAssistant() {
    alert('AI Assistant feature coming soon!');
}
</script>

<?php get_footer(); ?>
