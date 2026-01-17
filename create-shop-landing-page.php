<?php
/**
 * Script de Création de la Page Shop Landing
 *
 * Ce script crée automatiquement la page WordPress "Shop Landing"
 * avec le template page-shop-landing.php
 *
 * UTILISATION:
 * - Via URL: /wp-content/themes/forever-be-wp-premium/create-shop-landing-page.php
 */

// Charger WordPress
require_once('../../../../../wp-load.php');

// Vérifier les permissions admin
if (!current_user_can('manage_options')) {
    wp_die('Vous devez être administrateur pour exécuter ce script.');
}

echo '<div style="font-family: system-ui; max-width: 800px; margin: 40px auto; padding: 20px;">';
echo '<h1 style="color: #1a1a2e;">🛍️ Création de la Page Shop Landing</h1>';
echo '<hr style="border: 1px solid #e0e0e0; margin: 20px 0;">';

// Vérifier si la page existe déjà
$existing_page = get_page_by_path('shop-landing');

if ($existing_page) {
    echo '<div style="padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; margin-bottom: 15px;">';
    echo '<strong>⚠️ INFO:</strong> La page "Shop Landing" existe déjà (ID: ' . $existing_page->ID . ')';
    echo '<br><small style="color: #666;">Template actuel: ' . get_post_meta($existing_page->ID, '_wp_page_template', true) . '</small>';
    echo '<br><small style="color: #666;">URL: <a href="' . get_permalink($existing_page->ID) . '" target="_blank">' . get_permalink($existing_page->ID) . '</a></small>';
    echo '</div>';

    // Mettre à jour le template si nécessaire
    $current_template = get_post_meta($existing_page->ID, '_wp_page_template', true);
    if ($current_template !== 'page-shop-landing.php') {
        update_post_meta($existing_page->ID, '_wp_page_template', 'page-shop-landing.php');
        echo '<div style="padding: 15px; background: #d4edda; border-left: 4px solid #28a745; margin-bottom: 15px;">';
        echo '<strong>✅ SUCCESS:</strong> Template mis à jour vers page-shop-landing.php';
        echo '</div>';
    }
} else {
    // Créer la page
    $page_args = array(
        'post_title'     => 'Shop Landing',
        'post_name'      => 'shop-landing',
        'post_content'   => 'Landing page premium pour la boutique Forever Bien-Être avec intégration FitTrack Pro et système de paiement Stripe.',
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_author'    => 1,
        'post_parent'    => 0,
        'comment_status' => 'closed',
        'ping_status'    => 'closed'
    );

    $page_id = wp_insert_post($page_args, true);

    if (is_wp_error($page_id)) {
        echo '<div style="padding: 15px; background: #f8d7da; border-left: 4px solid #dc3545; margin-bottom: 15px;">';
        echo '<strong>❌ ERROR:</strong> Impossible de créer la page - ' . $page_id->get_error_message();
        echo '</div>';
    } else {
        // Assigner le template
        update_post_meta($page_id, '_wp_page_template', 'page-shop-landing.php');

        echo '<div style="padding: 15px; background: #d4edda; border-left: 4px solid #28a745; margin-bottom: 15px;">';
        echo '<strong>✅ SUCCESS:</strong> Page "Shop Landing" créée (ID: ' . $page_id . ')';
        echo '<br><small style="color: #666;">Template: page-shop-landing.php</small>';
        echo '<br><small style="color: #666;">URL: <a href="' . get_permalink($page_id) . '" target="_blank">' . get_permalink($page_id) . '</a></small>';
        echo '</div>';

        // Flush des règles de réécriture
        flush_rewrite_rules();

        echo '<div style="margin-top: 20px; padding: 15px; background: #d1ecf1; border-left: 4px solid #17a2b8; border-radius: 4px;">';
        echo '<strong>💡 URL Correcte:</strong>';
        echo '<ul style="margin: 10px 0 0 0; padding-left: 20px;">';
        echo '<li>Ancienne (incorrecte): <code>http://localhost/foreverbienetre/shop-landing.html</code></li>';
        echo '<li>Nouvelle (correcte): <code>' . get_permalink($page_id) . '</code></li>';
        echo '</ul>';
        echo '</div>';
    }
}

echo '<hr style="border: 1px solid #e0e0e0; margin: 20px 0;">';
echo '<div style="padding: 20px; background: #e8f4f8; border-radius: 8px;">';
echo '<h2 style="margin-top: 0; color: #1a1a2e;">📋 Prochaines Étapes</h2>';
echo '<ol style="line-height: 2;">';
echo '<li>Utiliser la nouvelle URL WordPress au lieu de .html</li>';
echo '<li>Supprimer le fichier shop-landing.html (optionnel)</li>';
echo '<li>Mettre à jour tous les liens qui pointent vers .html</li>';
echo '<li>Vérifier que le template fonctionne correctement</li>';
echo '</ol>';
echo '</div>';

echo '<div style="margin-top: 30px; text-align: center;">';
echo '<a href="' . admin_url('edit.php?post_type=page') . '" style="display: inline-block; padding: 12px 24px; background: #1a1a2e; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">📄 Voir Toutes les Pages</a>';
echo ' ';
echo '<a href="' . admin_url() . '" style="display: inline-block; padding: 12px 24px; background: #c9a962; color: #1a1a2e; text-decoration: none; border-radius: 6px; font-weight: 600;">🏠 Retour au Dashboard</a>';
echo '</div>';

echo '</div>';
