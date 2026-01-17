<?php
/**
 * FitTrack Pro - Local Environment Setup
 *
 * This script configures the local WordPress installation for FitTrack Pro testing
 *
 * Usage: php setup-local-env.php
 *
 * @package Forever_BE_Premium
 * @subpackage FitTrack_Pro
 */

// Colors for terminal output
define('COLOR_SUCCESS', "\033[0;32m");
define('COLOR_ERROR', "\033[0;31m");
define('COLOR_INFO', "\033[0;36m");
define('COLOR_WARNING', "\033[0;33m");
define('COLOR_RESET', "\033[0m");

function log_step($message, $type = 'info') {
    $colors = [
        'success' => COLOR_SUCCESS,
        'error' => COLOR_ERROR,
        'info' => COLOR_INFO,
        'warning' => COLOR_WARNING,
    ];

    $color = $colors[$type] ?? COLOR_RESET;
    echo $color . $message . COLOR_RESET . "\n";
}

function separator() {
    echo str_repeat('=', 70) . "\n";
}

echo "\n";
separator();
log_step("FITTRACK PRO - CONFIGURATION ENVIRONNEMENT LOCAL", 'info');
separator();
echo "\n";

// Load WordPress
$wp_load_path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    log_step("❌ Erreur: Cannot find wp-load.php at: $wp_load_path", 'error');
    exit(1);
}

log_step("[1/8] Chargement de WordPress...", 'info');
require_once($wp_load_path);
log_step("✅ WordPress chargé avec succès", 'success');

// Step 1: Check database connection
log_step("\n[2/8] Vérification de la connexion à la base de données...", 'info');
global $wpdb;
$result = $wpdb->get_var("SELECT 1");
if ($result == 1) {
    log_step("✅ Connexion MySQL OK", 'success');
    log_step("   Database: " . DB_NAME, 'info');
    log_step("   Host: " . DB_HOST, 'info');
} else {
    log_step("❌ Erreur de connexion à MySQL", 'error');
    exit(1);
}

// Step 2: Check FitTrack tables
log_step("\n[3/8] Vérification des tables FitTrack...", 'info');
$tables_to_check = [
    'fittrack_progress_logs',
    'fittrack_workout_logs',
    'fittrack_exercise_logs',
    'fittrack_nutrition_logs',
    'fittrack_subscriptions',
    'fittrack_goals',
];

$missing_tables = [];
foreach ($tables_to_check as $table) {
    $table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if ($exists) {
        log_step("   ✓ $table_name", 'success');
    } else {
        log_step("   ✗ $table_name (MANQUANTE)", 'error');
        $missing_tables[] = $table;
    }
}

if (!empty($missing_tables)) {
    log_step("⚠️  Tables manquantes détectées. Initialisation...", 'warning');
    if (class_exists('FitTrack_Database')) {
        FitTrack_Database::get_instance();
        log_step("✅ Tables créées avec succès", 'success');
    } else {
        log_step("❌ Classe FitTrack_Database introuvable", 'error');
    }
}

// Step 3: Check wp-config.php for Stripe keys
log_step("\n[4/8] Vérification des clés Stripe...", 'info');
$wp_config_path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/wp-config.php';

if (defined('FITTRACK_STRIPE_PUBLISHABLE_KEY') && defined('FITTRACK_STRIPE_SECRET_KEY')) {
    $pub_key = FITTRACK_STRIPE_PUBLISHABLE_KEY;
    $sec_key = FITTRACK_STRIPE_SECRET_KEY;

    if (!empty($pub_key) && !empty($sec_key)) {
        log_step("✅ Clés Stripe configurées", 'success');
        log_step("   Publishable: " . substr($pub_key, 0, 20) . "...", 'info');
        log_step("   Secret: " . substr($sec_key, 0, 20) . "...", 'info');
    } else {
        log_step("⚠️  Clés Stripe vides", 'warning');
        log_step("   Ajoutez les clés dans wp-config.php:", 'info');
        log_step("   define('FITTRACK_STRIPE_PUBLISHABLE_KEY', 'pk_test_...');", 'info');
        log_step("   define('FITTRACK_STRIPE_SECRET_KEY', 'sk_test_...');", 'info');
    }
} else {
    log_step("⚠️  Clés Stripe NON configurées dans wp-config.php", 'warning');
    log_step("   Pour tester les paiements, ajoutez dans wp-config.php:", 'info');
    log_step("   define('FITTRACK_STRIPE_PUBLISHABLE_KEY', 'pk_test_...');", 'info');
    log_step("   define('FITTRACK_STRIPE_SECRET_KEY', 'sk_test_...');", 'info');
    log_step("   Récupérez vos clés sur: https://dashboard.stripe.com/test/apikeys", 'info');
}

// Step 4: Check/Create test user
log_step("\n[5/8] Vérification de l'utilisateur de test...", 'info');
$test_username = 'fittrack_test';
$test_user = get_user_by('login', $test_username);

if (!$test_user) {
    log_step("   Création de l'utilisateur de test...", 'info');
    $user_id = wp_create_user(
        $test_username,
        'test123',
        'fittrack_test@foreverbienetre.com'
    );

    if (!is_wp_error($user_id)) {
        wp_update_user([
            'ID' => $user_id,
            'first_name' => 'FitTrack',
            'last_name' => 'Test',
            'display_name' => 'FitTrack Test',
            'role' => 'subscriber'
        ]);
        log_step("✅ Utilisateur créé: $test_username / test123", 'success');
    } else {
        log_step("❌ Erreur création utilisateur: " . $test_user->get_error_message(), 'error');
    }
} else {
    log_step("✅ Utilisateur de test existe: $test_username", 'success');
}

// Step 5: Create demo foods
log_step("\n[6/8] Création de données de démonstration (aliments)...", 'info');
$demo_foods = [
    ['name' => 'Poulet grillé (100g)', 'calories' => 165, 'protein' => 31, 'carbs' => 0, 'fat' => 3.6],
    ['name' => 'Riz brun (100g)', 'calories' => 111, 'protein' => 2.6, 'carbs' => 23, 'fat' => 0.9],
    ['name' => 'Brocoli (100g)', 'calories' => 34, 'protein' => 2.8, 'carbs' => 7, 'fat' => 0.4],
    ['name' => 'Banane', 'calories' => 89, 'protein' => 1.1, 'carbs' => 23, 'fat' => 0.3],
    ['name' => 'Oeufs (2 unités)', 'calories' => 143, 'protein' => 13, 'carbs' => 1.1, 'fat' => 9.5],
    ['name' => 'Avocat', 'calories' => 160, 'protein' => 2, 'carbs' => 9, 'fat' => 15],
];

$foods_created = 0;
foreach ($demo_foods as $food) {
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}posts WHERE post_type = 'fittrack_food' AND post_title = %s",
        $food['name']
    ));

    if (!$existing) {
        $post_id = wp_insert_post([
            'post_type' => 'fittrack_food',
            'post_title' => $food['name'],
            'post_status' => 'publish',
            'post_author' => 1
        ]);

        if ($post_id) {
            update_post_meta($post_id, 'calories', $food['calories']);
            update_post_meta($post_id, 'protein', $food['protein']);
            update_post_meta($post_id, 'carbs', $food['carbs']);
            update_post_meta($post_id, 'fat', $food['fat']);
            $foods_created++;
        }
    }
}
log_step("✅ Aliments créés: $foods_created/" . count($demo_foods), 'success');

// Step 6: Create demo exercises
log_step("\n[7/8] Création de données de démonstration (exercices)...", 'info');
$demo_exercises = [
    ['name' => 'Push-ups', 'type' => 'Strength', 'muscle' => 'Chest'],
    ['name' => 'Squats', 'type' => 'Strength', 'muscle' => 'Legs'],
    ['name' => 'Running', 'type' => 'Cardio', 'muscle' => 'Full Body'],
    ['name' => 'Plank', 'type' => 'Core', 'muscle' => 'Abs'],
    ['name' => 'Pull-ups', 'type' => 'Strength', 'muscle' => 'Back'],
];

$exercises_created = 0;
foreach ($demo_exercises as $exercise) {
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}posts WHERE post_type = 'fittrack_exercise' AND post_title = %s",
        $exercise['name']
    ));

    if (!$existing) {
        $post_id = wp_insert_post([
            'post_type' => 'fittrack_exercise',
            'post_title' => $exercise['name'],
            'post_status' => 'publish',
            'post_author' => 1
        ]);

        if ($post_id) {
            update_post_meta($post_id, 'exercise_type', $exercise['type']);
            update_post_meta($post_id, 'muscle_group', $exercise['muscle']);
            $exercises_created++;
        }
    }
}
log_step("✅ Exercices créés: $exercises_created/" . count($demo_exercises), 'success');

// Step 7: Summary
log_step("\n[8/8] Récapitulatif de la configuration", 'info');
separator();
echo "\n";
log_step("ENVIRONNEMENT LOCAL CONFIGURÉ", 'success');
echo "\n";
log_step("🔗 URLs de test:", 'info');
log_step("   • Pricing:   http://localhost/foreverbienetre/fittrack-pricing", 'info');
log_step("   • Dashboard: http://localhost/foreverbienetre/fittrack-dashboard", 'info');
log_step("   • Nutrition: http://localhost/foreverbienetre/fittrack-nutrition", 'info');
log_step("   • Workouts:  http://localhost/foreverbienetre/fittrack-workouts", 'info');
log_step("   • Progress:  http://localhost/foreverbienetre/fittrack-progress", 'info');
log_step("   • Goals:     http://localhost/foreverbienetre/fittrack-goals", 'info');
echo "\n";
log_step("👤 Identifiants de test:", 'info');
log_step("   Username: fittrack_test", 'info');
log_step("   Password: test123", 'info');
echo "\n";
log_step("📊 Données de démonstration:", 'info');
log_step("   • Aliments: $foods_created créés", 'success');
log_step("   • Exercices: $exercises_created créés", 'success');
echo "\n";

if (!defined('FITTRACK_STRIPE_PUBLISHABLE_KEY') || empty(FITTRACK_STRIPE_PUBLISHABLE_KEY)) {
    log_step("⚠️  ATTENTION: Clés Stripe non configurées", 'warning');
    log_step("   Les paiements ne fonctionneront pas sans les clés", 'warning');
    log_step("   Voir: inc/fittrack/STRIPE-CONFIG.md", 'info');
    echo "\n";
}

separator();
log_step("✅ Configuration terminée! Vous pouvez commencer les tests.", 'success');
separator();
echo "\n";
