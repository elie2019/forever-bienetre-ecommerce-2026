<?php
/**
 * Template: FitTrack Nutrition
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header();

// Get foods from database
global $wpdb;
$foods_query = "SELECT p.ID, p.post_title,
                pm1.meta_value as calories,
                pm2.meta_value as protein,
                pm3.meta_value as carbs,
                pm4.meta_value as fat
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'calories'
                LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'protein'
                LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = 'carbs'
                LEFT JOIN {$wpdb->postmeta} pm4 ON p.ID = pm4.post_id AND pm4.meta_key = 'fat'
                WHERE p.post_type = 'fittrack_food' AND p.post_status = 'publish'
                ORDER BY p.post_title ASC";
$foods = $wpdb->get_results($foods_query);

// Get today's nutrition logs
$user_id = get_current_user_id();
$today = date('Y-m-d');
$nutrition_table = $wpdb->prefix . 'fittrack_nutrition_logs';
$today_meals = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $nutrition_table WHERE user_id = %d AND date = %s ORDER BY created_at DESC",
    $user_id,
    $today
));

// Calculate totals
$totals = [
    'calories' => 0,
    'protein' => 0,
    'carbs' => 0,
    'fat' => 0
];

foreach ($today_meals as $meal) {
    $totals['calories'] += $meal->calories;
    $totals['protein'] += $meal->protein;
    $totals['carbs'] += $meal->carbs;
    $totals['fat'] += $meal->fat;
}
?>

<!-- Load jQuery and scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
const fittrackData = {
    ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
    nonce: '<?php echo wp_create_nonce('fittrack_nonce'); ?>',
    isLoggedIn: true,
    userId: <?php echo get_current_user_id(); ?>
};
</script>

<div class="fittrack-nutrition min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Nutrition Tracking</h1>
            <p class="text-gray-600">Track your meals and monitor your macros</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Add Meal Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Add Meal</h2>

                    <form id="add-meal-form" class="space-y-4">
                        <!-- Meal Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meal Type</label>
                            <select id="meal-type" name="meal_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" required>
                                <option value="">Select meal type</option>
                                <option value="breakfast">Breakfast</option>
                                <option value="lunch">Lunch</option>
                                <option value="dinner">Dinner</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>

                        <!-- Food Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Food</label>
                            <select id="food-select" name="food_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" required>
                                <option value="">Select food</option>
                                <?php foreach ($foods as $food): ?>
                                <option value="<?php echo $food->ID; ?>"
                                        data-calories="<?php echo $food->calories; ?>"
                                        data-protein="<?php echo $food->protein; ?>"
                                        data-carbs="<?php echo $food->carbs; ?>"
                                        data-fat="<?php echo $food->fat; ?>">
                                    <?php echo esc_html($food->post_title); ?>
                                    (<?php echo $food->calories; ?> cal)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <div class="flex gap-2">
                                <input type="number" id="quantity" name="quantity" value="1" min="0.1" step="0.1" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" required>
                                <select id="unit" name="unit" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                    <option value="serving">serving</option>
                                    <option value="g">grams</option>
                                    <option value="oz">oz</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nutrition Preview -->
                        <div id="nutrition-preview" class="hidden p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-2">Nutrition Info:</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div>Calories: <span id="preview-calories" class="font-bold">0</span></div>
                                <div>Protein: <span id="preview-protein" class="font-bold">0</span>g</div>
                                <div>Carbs: <span id="preview-carbs" class="font-bold">0</span>g</div>
                                <div>Fat: <span id="preview-fat" class="font-bold">0</span>g</div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                            <textarea id="notes" name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Add any notes..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-6 rounded-lg transition transform hover:scale-105">
                            Add to Log
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Today's Summary -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Macros Summary -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Today's Nutrition - <?php echo date('M d, Y'); ?></h2>

                    <!-- Macros Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Calories</div>
                            <div class="text-3xl font-bold text-orange-600" id="total-calories"><?php echo $totals['calories']; ?></div>
                            <div class="text-xs text-gray-500 mt-1">kcal</div>
                        </div>

                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Protein</div>
                            <div class="text-3xl font-bold text-blue-600" id="total-protein"><?php echo round($totals['protein'], 1); ?></div>
                            <div class="text-xs text-gray-500 mt-1">grams</div>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Carbs</div>
                            <div class="text-3xl font-bold text-green-600" id="total-carbs"><?php echo round($totals['carbs'], 1); ?></div>
                            <div class="text-xs text-gray-500 mt-1">grams</div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Fat</div>
                            <div class="text-3xl font-bold text-yellow-600" id="total-fat"><?php echo round($totals['fat'], 1); ?></div>
                            <div class="text-xs text-gray-500 mt-1">grams</div>
                        </div>
                    </div>

                    <!-- Macro Distribution -->
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">Protein</span>
                                <span class="text-gray-500"><?php echo round($totals['protein'], 1); ?>g</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo min(100, ($totals['protein'] / 150) * 100); ?>%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">Carbs</span>
                                <span class="text-gray-500"><?php echo round($totals['carbs'], 1); ?>g</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo min(100, ($totals['carbs'] / 250) * 100); ?>%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">Fat</span>
                                <span class="text-gray-500"><?php echo round($totals['fat'], 1); ?>g</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: <?php echo min(100, ($totals['fat'] / 70) * 100); ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Meals -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Today's Meals</h2>

                    <div id="meals-list" class="space-y-3">
                        <?php if (empty($today_meals)): ?>
                        <div class="text-center py-8 text-gray-500">
                            <p>No meals logged yet today. Add your first meal above!</p>
                        </div>
                        <?php else: ?>
                            <?php foreach ($today_meals as $meal): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full <?php
                                            echo $meal->meal_type === 'breakfast' ? 'bg-yellow-100 text-yellow-800' :
                                                ($meal->meal_type === 'lunch' ? 'bg-blue-100 text-blue-800' :
                                                ($meal->meal_type === 'dinner' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'));
                                        ?>">
                                            <?php echo ucfirst($meal->meal_type); ?>
                                        </span>
                                        <h4 class="font-semibold text-gray-900"><?php echo esc_html($meal->food_name); ?></h4>
                                    </div>
                                    <div class="mt-2 flex gap-4 text-sm text-gray-600">
                                        <span><?php echo $meal->calories; ?> cal</span>
                                        <span>P: <?php echo $meal->protein; ?>g</span>
                                        <span>C: <?php echo $meal->carbs; ?>g</span>
                                        <span>F: <?php echo $meal->fat; ?>g</span>
                                    </div>
                                    <?php if ($meal->notes): ?>
                                    <div class="mt-1 text-sm text-gray-500 italic"><?php echo esc_html($meal->notes); ?></div>
                                    <?php endif; ?>
                                </div>
                                <button onclick="deleteMeal(<?php echo $meal->id; ?>)" class="ml-4 text-red-600 hover:text-red-800 p-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update nutrition preview when food or quantity changes
jQuery('#food-select, #quantity').on('change input', function() {
    const selectedOption = jQuery('#food-select option:selected');
    const quantity = parseFloat(jQuery('#quantity').val()) || 1;

    if (selectedOption.val()) {
        const calories = Math.round(parseFloat(selectedOption.data('calories')) * quantity);
        const protein = (parseFloat(selectedOption.data('protein')) * quantity).toFixed(1);
        const carbs = (parseFloat(selectedOption.data('carbs')) * quantity).toFixed(1);
        const fat = (parseFloat(selectedOption.data('fat')) * quantity).toFixed(1);

        jQuery('#preview-calories').text(calories);
        jQuery('#preview-protein').text(protein);
        jQuery('#preview-carbs').text(carbs);
        jQuery('#preview-fat').text(fat);
        jQuery('#nutrition-preview').removeClass('hidden');
    } else {
        jQuery('#nutrition-preview').addClass('hidden');
    }
});

// Handle form submission
jQuery('#add-meal-form').on('submit', function(e) {
    e.preventDefault();

    const selectedOption = jQuery('#food-select option:selected');
    const quantity = parseFloat(jQuery('#quantity').val()) || 1;

    if (!selectedOption.val()) {
        alert('Please select a food');
        return;
    }

    const button = jQuery(this).find('button[type="submit"]');
    const originalText = button.text();
    button.text('Adding...').prop('disabled', true);

    const data = {
        action: 'fittrack_add_meal',
        nonce: fittrackData.nonce,
        date: '<?php echo $today; ?>',
        meal_type: jQuery('#meal-type').val(),
        food_id: selectedOption.val(),
        food_name: selectedOption.text().split('(')[0].trim(),
        quantity: quantity,
        unit: jQuery('#unit').val(),
        calories: Math.round(parseFloat(selectedOption.data('calories')) * quantity),
        protein: parseFloat(selectedOption.data('protein')) * quantity,
        carbs: parseFloat(selectedOption.data('carbs')) * quantity,
        fat: parseFloat(selectedOption.data('fat')) * quantity,
        notes: jQuery('#notes').val()
    };

    jQuery.ajax({
        url: fittrackData.ajaxUrl,
        type: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                alert('Meal added successfully!');
                location.reload(); // Reload to show updated data
            } else {
                alert('Error: ' + (response.data.message || 'An error occurred'));
                button.text(originalText).prop('disabled', false);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error, xhr.responseText);
            alert('An error occurred. Check console for details.');
            button.text(originalText).prop('disabled', false);
        }
    });
});

// Delete meal function
function deleteMeal(mealId) {
    if (!confirm('Delete this meal entry?')) return;

    jQuery.ajax({
        url: fittrackData.ajaxUrl,
        type: 'POST',
        data: {
            action: 'fittrack_delete_meal',
            nonce: fittrackData.nonce,
            meal_id: mealId
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error deleting meal');
            }
        }
    });
}
</script>

<?php get_footer(); ?>
