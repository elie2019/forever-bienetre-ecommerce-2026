<?php
/**
 * Script d'Installation des Pages FitTrack
 *
 * Ce script crée automatiquement toutes les pages WordPress nécessaires
 * pour l'application FitTrack Pro avec les templates appropriés.
 *
 * UTILISATION:
 * - Depuis l'admin WordPress: Outils > Import > Exécuter ce script
 * - Via URL directe (admin uniquement): /wp-content/themes/forever-be-wp-premium/inc/fittrack/install-fittrack-pages.php
 * - Via WP-CLI: wp eval-file install-fittrack-pages.php
 *
 * @package FitTrack_Pro
 * @version 1.0.0
 */

// Charger WordPress si pas déjà chargé
if (!defined('ABSPATH')) {
    require_once('../../../../../wp-load.php');
}

// Vérifier les permissions admin
if (!current_user_can('manage_options')) {
    wp_die('Vous devez être administrateur pour exécuter ce script.');
}

/**
 * Pages FitTrack à créer
 */
$fittrack_pages = array(
    array(
        'title' => 'FitTrack Pricing',
        'slug' => 'fittrack-pricing',
        'template' => 'page-fittrack-pricing.php',
        'content' => 'Page des plans tarifaires FitTrack Pro avec intégration Stripe.',
        'parent' => 0
    ),
    array(
        'title' => 'FitTrack Dashboard',
        'slug' => 'fittrack-dashboard',
        'template' => 'page-fittrack-dashboard.php',
        'content' => 'Tableau de bord principal avec statistiques et activités.',
        'parent' => 0
    ),
    array(
        'title' => 'FitTrack Nutrition',
        'slug' => 'fittrack-nutrition',
        'template' => 'page-fittrack-nutrition.php',
        'content' => 'Tracker nutritionnel avec base de données alimentaire.',
        'parent' => 0
    ),
    array(
        'title' => 'FitTrack Workouts',
        'slug' => 'fittrack-workouts',
        'template' => 'page-fittrack-workouts.php',
        'content' => 'Journal d\'entraînement avec bibliothèque d\'exercices.',
        'parent' => 0
    ),
    array(
        'title' => 'FitTrack Progress',
        'slug' => 'fittrack-progress',
        'template' => 'page-fittrack-progress.php',
        'content' => 'Suivi de progression avec mesures corporelles et photos.',
        'parent' => 0
    ),
    array(
        'title' => 'FitTrack Goals',
        'slug' => 'fittrack-goals',
        'template' => 'page-fittrack-goals.php',
        'content' => 'Gestionnaire d\'objectifs SMART avec système de récompenses.',
        'parent' => 0
    ),
    array(
        'title' => 'FitTrack Settings',
        'slug' => 'fittrack-settings',
        'template' => 'page-fittrack-settings.php',
        'content' => 'Paramètres utilisateur, profil et préférences.',
        'parent' => 0
    )
);

echo '<div style="font-family: system-ui; max-width: 800px; margin: 40px auto; padding: 20px;">';
echo '<h1 style="color: #1a1a2e;">🚀 Installation des Pages FitTrack Pro</h1>';
echo '<hr style="border: 1px solid #e0e0e0; margin: 20px 0;">';

$created = 0;
$skipped = 0;
$errors = 0;

foreach ($fittrack_pages as $page_data) {
    // Vérifier si la page existe déjà
    $existing_page = get_page_by_path($page_data['slug']);

    if ($existing_page) {
        echo '<div style="padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; margin-bottom: 15px;">';
        echo '<strong>⚠️ SKIPPED:</strong> Page "' . $page_data['title'] . '" existe déjà (ID: ' . $existing_page->ID . ')';
        echo '</div>';
        $skipped++;
        continue;
    }

    // Créer la page
    $page_args = array(
        'post_title' => $page_data['title'],
        'post_name' => $page_data['slug'],
        'post_content' => $page_data['content'],
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_parent' => $page_data['parent'],
        'comment_status' => 'closed',
        'ping_status' => 'closed'
    );

    $page_id = wp_insert_post($page_args, true);

    if (is_wp_error($page_id)) {
        echo '<div style="padding: 15px; background: #f8d7da; border-left: 4px solid #dc3545; margin-bottom: 15px;">';
        echo '<strong>❌ ERROR:</strong> Impossible de créer "' . $page_data['title'] . '" - ' . $page_id->get_error_message();
        echo '</div>';
        $errors++;
        continue;
    }

    // Assigner le template
    update_post_meta($page_id, '_wp_page_template', $page_data['template']);

    // Marquer comme page FitTrack
    update_post_meta($page_id, '_fittrack_page', '1');

    echo '<div style="padding: 15px; background: #d4edda; border-left: 4px solid #28a745; margin-bottom: 15px;">';
    echo '<strong>✅ SUCCESS:</strong> Page "' . $page_data['title'] . '" créée (ID: ' . $page_id . ')';
    echo '<br><small style="color: #666;">Template: ' . $page_data['template'] . '</small>';
    echo '<br><small style="color: #666;">URL: <a href="' . get_permalink($page_id) . '" target="_blank">' . get_permalink($page_id) . '</a></small>';
    echo '</div>';
    $created++;
}

// Flush des règles de réécriture
flush_rewrite_rules();

echo '<hr style="border: 1px solid #e0e0e0; margin: 20px 0;">';
echo '<div style="padding: 20px; background: #e8f4f8; border-radius: 8px;">';
echo '<h2 style="margin-top: 0; color: #1a1a2e;">📊 Résumé de l\'Installation</h2>';
echo '<ul style="line-height: 2;">';
echo '<li><strong>✅ Créées:</strong> ' . $created . ' pages</li>';
echo '<li><strong>⚠️ Ignorées:</strong> ' . $skipped . ' pages (déjà existantes)</li>';
echo '<li><strong>❌ Erreurs:</strong> ' . $errors . ' pages</li>';
echo '<li><strong>📝 Total:</strong> ' . count($fittrack_pages) . ' pages</li>';
echo '</ul>';

if ($created > 0) {
    echo '<div style="margin-top: 20px; padding: 15px; background: #d1ecf1; border-left: 4px solid #17a2b8; border-radius: 4px;">';
    echo '<strong>💡 Prochaines Étapes:</strong>';
    echo '<ol style="margin: 10px 0 0 0; padding-left: 20px;">';
    echo '<li>Vérifier que toutes les pages sont accessibles</li>';
    echo '<li>Tester les templates avec un utilisateur connecté</li>';
    echo '<li>Configurer Stripe (clés API dans Settings > FitTrack)</li>';
    echo '<li>Personnaliser les données demo si besoin</li>';
    echo '</ol>';
    echo '</div>';
}

echo '</div>';

echo '<div style="margin-top: 30px; text-align: center;">';
echo '<a href="' . admin_url('edit.php?post_type=page') . '" style="display: inline-block; padding: 12px 24px; background: #1a1a2e; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">📄 Voir Toutes les Pages</a>';
echo ' ';
echo '<a href="' . admin_url() . '" style="display: inline-block; padding: 12px 24px; background: #c9a962; color: #1a1a2e; text-decoration: none; border-radius: 6px; font-weight: 600;">🏠 Retour au Dashboard</a>';
echo '</div>';

echo '</div>';
