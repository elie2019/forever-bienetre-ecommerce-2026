<?php
/**
 * Template Name: FitTrack Nutrition
 * Description: Tracker nutritionnel avec base de données alimentaire et suivi des macros
 */

// Vérifier l'authentification
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Enqueue FitTrack styles
function fittrack_nutrition_assets() {
    if (is_page_template('page-fittrack-nutrition.php')) {
        wp_enqueue_style('fittrack-app-css', get_template_directory_uri() . '/assets/css/fittrack-app.css', array(), '1.0.0');

        wp_localize_script('jquery', 'fittrackNutrition', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fittrack_nutrition_nonce'),
            'userId' => get_current_user_id()
        ));
    }
}
add_action('wp_enqueue_scripts', 'fittrack_nutrition_assets');

$user_id = get_current_user_id();

// Objectifs nutritionnels (demo)
$nutrition_goals = array(
    'calories' => 2000,
    'proteins' => 150,
    'carbs' => 200,
    'fats' => 65
);

// Nutrition consommée aujourd'hui (demo)
$consumed_today = array(
    'calories' => 1456,
    'proteins' => 98,
    'carbs' => 142,
    'fats' => 48
);

// Repas du jour (demo)
$meals = array(
    'breakfast' => array(
        array('name' => 'Omelette 3 œufs', 'calories' => 234, 'proteins' => 18, 'carbs' => 2, 'fats' => 17, 'time' => '08:30'),
        array('name' => 'Pain complet (2 tranches)', 'calories' => 138, 'proteins' => 6, 'carbs' => 24, 'fats' => 2, 'time' => '08:30'),
        array('name' => 'Avocat (1/2)', 'calories' => 120, 'proteins' => 2, 'carbs' => 6, 'fats' => 11, 'time' => '08:30')
    ),
    'lunch' => array(
        array('name' => 'Poulet grillé (200g)', 'calories' => 330, 'proteins' => 62, 'carbs' => 0, 'fats' => 7, 'time' => '13:00'),
        array('name' => 'Riz basmati (150g cuit)', 'calories' => 195, 'proteins' => 4, 'carbs' => 43, 'fats' => 0, 'time' => '13:00'),
        array('name' => 'Brocoli vapeur (200g)', 'calories' => 68, 'proteins' => 6, 'carbs' => 14, 'fats' => 1, 'time' => '13:00')
    ),
    'snacks' => array(
        array('name' => 'Forever Lite Ultra Vanilla', 'calories' => 200, 'proteins' => 24, 'carbs' => 18, 'fats' => 3, 'time' => '16:00'),
        array('name' => 'Amandes (30g)', 'calories' => 171, 'proteins' => 6, 'carbs' => 6, 'fats' => 15, 'time' => '16:00')
    ),
    'dinner' => array()
);

// Base de données alimentaire populaire (demo)
$popular_foods = array(
    array('name' => 'Blanc de poulet (100g)', 'calories' => 165, 'proteins' => 31, 'carbs' => 0, 'fats' => 3.6),
    array('name' => 'Saumon (100g)', 'calories' => 208, 'proteins' => 20, 'carbs' => 0, 'fats' => 13),
    array('name' => 'Riz blanc cuit (100g)', 'calories' => 130, 'proteins' => 2.7, 'carbs' => 28, 'fats' => 0.3),
    array('name' => 'Patate douce (100g)', 'calories' => 86, 'proteins' => 1.6, 'carbs' => 20, 'fats' => 0.1),
    array('name' => 'Banane (1 moyenne)', 'calories' => 105, 'proteins' => 1.3, 'carbs' => 27, 'fats' => 0.4),
    array('name' => 'Œuf entier (1)', 'calories' => 78, 'proteins' => 6, 'carbs' => 0.6, 'fats' => 5.3),
    array('name' => 'Avocat (100g)', 'calories' => 160, 'proteins' => 2, 'carbs' => 9, 'fats' => 15),
    array('name' => 'Forever Aloe Vera Gel (30ml)', 'calories' => 15, 'proteins' => 0, 'carbs' => 4, 'fats' => 0)
);

get_header();
?>

<div class="fittrack-wrapper">
    <!-- Header FitTrack -->
    <header class="fittrack-header">
        <div class="fittrack-header-icon">🥗</div>
        <h1 class="fittrack-header-title">Suivi Nutritionnel</h1>
        <p class="fittrack-header-subtitle">Gérez votre alimentation avec précision et atteignez vos objectifs nutritionnels.</p>
    </header>

    <div class="fittrack-container-narrow">

        <!-- Résumé Quotidien -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📊</span>
                    Résumé du Jour
                </h2>
                <div style="font-size: 0.9rem; color: var(--fittrack-text-light);">
                    <?php echo date('d F Y'); ?>
                </div>
            </div>
            <div class="fittrack-card-body">

                <!-- Calories Principales -->
                <div style="text-align: center; margin-bottom: 30px;">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--fittrack-accent); margin-bottom: 10px;">
                        <?php echo $consumed_today['calories']; ?> <span style="font-size: 1.5rem; color: var(--fittrack-text-light);">/ <?php echo $nutrition_goals['calories']; ?></span>
                    </div>
                    <div style="font-size: 0.9rem; color: var(--fittrack-text-light); text-transform: uppercase; letter-spacing: 1px;">
                        Calories Consommées
                    </div>
                    <div style="margin-top: 15px;">
                        <div class="fittrack-progress" style="max-width: 400px; margin: 0 auto; height: 15px;">
                            <div class="fittrack-progress-bar" style="width: <?php echo ($consumed_today['calories'] / $nutrition_goals['calories']) * 100; ?>%;"></div>
                        </div>
                    </div>
                    <div style="margin-top: 10px; font-size: 0.9rem; color: var(--fittrack-accent); font-weight: 600;">
                        Reste : <?php echo $nutrition_goals['calories'] - $consumed_today['calories']; ?> cal
                    </div>
                </div>

                <!-- Macronutriments -->
                <div class="fittrack-grid fittrack-grid-3">

                    <!-- Protéines -->
                    <div style="text-align: center; padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 1.5rem; margin-bottom: 5px;">🥩</div>
                        <div style="font-size: 1.8rem; font-weight: 700; color: var(--fittrack-primary); margin-bottom: 5px;">
                            <?php echo $consumed_today['proteins']; ?>g
                        </div>
                        <div style="font-size: 0.8rem; color: var(--fittrack-text-light); margin-bottom: 10px;">
                            / <?php echo $nutrition_goals['proteins']; ?>g
                        </div>
                        <div class="fittrack-progress" style="height: 6px;">
                            <div class="fittrack-progress-bar" style="width: <?php echo ($consumed_today['proteins'] / $nutrition_goals['proteins']) * 100; ?>%; background: #28a745;"></div>
                        </div>
                        <div style="margin-top: 8px; font-size: 0.75rem; color: var(--fittrack-text-light); text-transform: uppercase; letter-spacing: 0.5px;">
                            Protéines
                        </div>
                    </div>

                    <!-- Glucides -->
                    <div style="text-align: center; padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 1.5rem; margin-bottom: 5px;">🍞</div>
                        <div style="font-size: 1.8rem; font-weight: 700; color: var(--fittrack-primary); margin-bottom: 5px;">
                            <?php echo $consumed_today['carbs']; ?>g
                        </div>
                        <div style="font-size: 0.8rem; color: var(--fittrack-text-light); margin-bottom: 10px;">
                            / <?php echo $nutrition_goals['carbs']; ?>g
                        </div>
                        <div class="fittrack-progress" style="height: 6px;">
                            <div class="fittrack-progress-bar" style="width: <?php echo ($consumed_today['carbs'] / $nutrition_goals['carbs']) * 100; ?>%; background: #17a2b8;"></div>
                        </div>
                        <div style="margin-top: 8px; font-size: 0.75rem; color: var(--fittrack-text-light); text-transform: uppercase; letter-spacing: 0.5px;">
                            Glucides
                        </div>
                    </div>

                    <!-- Lipides -->
                    <div style="text-align: center; padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 1.5rem; margin-bottom: 5px;">🥑</div>
                        <div style="font-size: 1.8rem; font-weight: 700; color: var(--fittrack-primary); margin-bottom: 5px;">
                            <?php echo $consumed_today['fats']; ?>g
                        </div>
                        <div style="font-size: 0.8rem; color: var(--fittrack-text-light); margin-bottom: 10px;">
                            / <?php echo $nutrition_goals['fats']; ?>g
                        </div>
                        <div class="fittrack-progress" style="height: 6px;">
                            <div class="fittrack-progress-bar" style="width: <?php echo ($consumed_today['fats'] / $nutrition_goals['fats']) * 100; ?>%; background: #ffc107;"></div>
                        </div>
                        <div style="margin-top: 8px; font-size: 0.75rem; color: var(--fittrack-text-light); text-transform: uppercase; letter-spacing: 0.5px;">
                            Lipides
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- Journal des Repas -->
        <div class="fittrack-card">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📝</span>
                    Journal des Repas
                </h2>
                <button class="fittrack-btn fittrack-btn-accent" style="padding: 10px 20px;" onclick="openAddFoodModal()">
                    + Ajouter Aliment
                </button>
            </div>
            <div class="fittrack-card-body">

                <!-- Petit-déjeuner -->
                <div style="margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid var(--fittrack-bg-light);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.5rem;">☀️</span>
                            <h3 style="font-size: 1.2rem; color: var(--fittrack-primary); margin: 0;">Petit-déjeuner</h3>
                        </div>
                        <div style="font-size: 0.9rem; font-weight: 600; color: var(--fittrack-accent);">
                            <?php
                            $breakfast_cals = array_sum(array_column($meals['breakfast'], 'calories'));
                            echo $breakfast_cals; ?> cal
                        </div>
                    </div>
                    <?php if (!empty($meals['breakfast'])) : ?>
                        <?php foreach ($meals['breakfast'] as $food) : ?>
                            <div class="fittrack-flex-between" style="padding: 12px; background: var(--fittrack-bg-light); border-radius: 6px; margin-bottom: 8px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: var(--fittrack-primary);"><?php echo esc_html($food['name']); ?></div>
                                    <div style="font-size: 0.85rem; color: var(--fittrack-text-light); margin-top: 3px;">
                                        P: <?php echo $food['proteins']; ?>g • G: <?php echo $food['carbs']; ?>g • L: <?php echo $food['fats']; ?>g
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: 700; color: var(--fittrack-accent);"><?php echo $food['calories']; ?> cal</div>
                                    <div style="font-size: 0.75rem; color: var(--fittrack-text-light); margin-top: 3px;"><?php echo $food['time']; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="fittrack-empty-state" style="padding: 20px;">
                            <p style="margin: 0; color: var(--fittrack-text-light); font-size: 0.9rem;">Aucun aliment enregistré</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Déjeuner -->
                <div style="margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid var(--fittrack-bg-light);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.5rem;">🌞</span>
                            <h3 style="font-size: 1.2rem; color: var(--fittrack-primary); margin: 0;">Déjeuner</h3>
                        </div>
                        <div style="font-size: 0.9rem; font-weight: 600; color: var(--fittrack-accent);">
                            <?php
                            $lunch_cals = array_sum(array_column($meals['lunch'], 'calories'));
                            echo $lunch_cals; ?> cal
                        </div>
                    </div>
                    <?php foreach ($meals['lunch'] as $food) : ?>
                        <div class="fittrack-flex-between" style="padding: 12px; background: var(--fittrack-bg-light); border-radius: 6px; margin-bottom: 8px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--fittrack-primary);"><?php echo esc_html($food['name']); ?></div>
                                <div style="font-size: 0.85rem; color: var(--fittrack-text-light); margin-top: 3px;">
                                    P: <?php echo $food['proteins']; ?>g • G: <?php echo $food['carbs']; ?>g • L: <?php echo $food['fats']; ?>g
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 700; color: var(--fittrack-accent);"><?php echo $food['calories']; ?> cal</div>
                                <div style="font-size: 0.75rem; color: var(--fittrack-text-light); margin-top: 3px;"><?php echo $food['time']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Collations -->
                <div style="margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid var(--fittrack-bg-light);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.5rem;">🍎</span>
                            <h3 style="font-size: 1.2rem; color: var(--fittrack-primary); margin: 0;">Collations</h3>
                        </div>
                        <div style="font-size: 0.9rem; font-weight: 600; color: var(--fittrack-accent);">
                            <?php
                            $snacks_cals = array_sum(array_column($meals['snacks'], 'calories'));
                            echo $snacks_cals; ?> cal
                        </div>
                    </div>
                    <?php foreach ($meals['snacks'] as $food) : ?>
                        <div class="fittrack-flex-between" style="padding: 12px; background: var(--fittrack-bg-light); border-radius: 6px; margin-bottom: 8px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--fittrack-primary);"><?php echo esc_html($food['name']); ?></div>
                                <div style="font-size: 0.85rem; color: var(--fittrack-text-light); margin-top: 3px;">
                                    P: <?php echo $food['proteins']; ?>g • G: <?php echo $food['carbs']; ?>g • L: <?php echo $food['fats']; ?>g
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 700; color: var(--fittrack-accent);"><?php echo $food['calories']; ?> cal</div>
                                <div style="font-size: 0.75rem; color: var(--fittrack-text-light); margin-top: 3px;"><?php echo $food['time']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Dîner -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid var(--fittrack-bg-light);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 1.5rem;">🌙</span>
                            <h3 style="font-size: 1.2rem; color: var(--fittrack-primary); margin: 0;">Dîner</h3>
                        </div>
                        <div style="font-size: 0.9rem; font-weight: 600; color: var(--fittrack-accent);">
                            0 cal
                        </div>
                    </div>
                    <div class="fittrack-empty-state" style="padding: 20px;">
                        <p style="margin: 0; color: var(--fittrack-text-light); font-size: 0.9rem;">Aucun aliment enregistré</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Base de Données Alimentaire -->
        <div class="fittrack-card" style="margin-top: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📚</span>
                    Aliments Populaires
                </h2>
            </div>
            <div class="fittrack-card-body">
                <div style="margin-bottom: 20px;">
                    <input type="text" class="fittrack-input" placeholder="🔍 Rechercher un aliment..." id="foodSearch" onkeyup="filterFoods()">
                </div>
                <div id="foodList" style="display: grid; gap: 10px;">
                    <?php foreach ($popular_foods as $food) : ?>
                        <div class="food-item fittrack-flex-between" style="padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px; cursor: pointer; transition: var(--fittrack-transition);" onmouseover="this.style.background='#e8e6e1'" onmouseout="this.style.background='var(--fittrack-bg-light)'">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;"><?php echo esc_html($food['name']); ?></div>
                                <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">
                                    P: <?php echo $food['proteins']; ?>g • G: <?php echo $food['carbs']; ?>g • L: <?php echo $food['fats']; ?>g
                                </div>
                            </div>
                            <div style="font-weight: 700; color: var(--fittrack-accent); font-size: 1.1rem;">
                                <?php echo $food['calories']; ?> cal
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="fittrack-card" style="background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); color: var(--fittrack-white); text-align: center; margin-top: 30px;">
            <div style="font-size: 3rem; margin-bottom: 15px;">📸</div>
            <h2 style="font-size: 2rem; margin-bottom: 15px; font-family: 'Playfair Display', serif;">Scanner de Code-Barres</h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 25px;">
                Passez au plan Pro pour scanner les produits et enregistrer vos repas instantanément.
            </p>
            <a href="<?php echo home_url('/fittrack-pricing'); ?>" class="fittrack-btn fittrack-btn-accent">
                Upgrader vers Pro →
            </a>
        </div>

    </div>
</div>

<script>
// Fonction de recherche d'aliments
function filterFoods() {
    const searchValue = document.getElementById('foodSearch').value.toLowerCase();
    const foodItems = document.querySelectorAll('.food-item');

    foodItems.forEach(item => {
        const foodName = item.querySelector('div > div').textContent.toLowerCase();
        if (foodName.includes(searchValue)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// Fonction pour ouvrir modal (placeholder)
function openAddFoodModal() {
    alert('Fonctionnalité d\'ajout d\'aliment à venir !\n\nCette modal permettra de :\n• Rechercher dans 500K aliments\n• Spécifier les quantités\n• Choisir le repas\n• Scanner un code-barres (Plan Pro)');
}
</script>

<?php get_footer(); ?>
