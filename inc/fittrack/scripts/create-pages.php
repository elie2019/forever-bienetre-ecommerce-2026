<?php
/**
 * FitTrack Pro - Create WordPress Pages Script
 *
 * This script creates all necessary WordPress pages for FitTrack Pro
 * Run this once after installation
 *
 * @package Forever_BE_Premium
 * @subpackage FitTrack_Pro
 */

// Load WordPress
$wp_load_path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/wp-load.php';
if (!file_exists($wp_load_path)) {
    die("Error: Cannot find wp-load.php at: $wp_load_path\n");
}
require_once($wp_load_path);

if (!current_user_can('manage_options')) {
    die('Insufficient permissions');
}

/**
 * Pages to create
 */
$pages = array(
    array(
        'post_title' => 'FitTrack Dashboard',
        'post_name' => 'fittrack-dashboard',
        'post_content' => '<!-- Dashboard content loaded via template -->',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'page_template' => '',
        'meta' => array(
            '_wp_page_template' => 'default'
        )
    ),
    array(
        'post_title' => 'FitTrack Pricing',
        'post_name' => 'fittrack-pricing',
        'post_content' => '<!-- Pricing content loaded via template -->',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'page_template' => '',
        'meta' => array(
            '_wp_page_template' => 'default'
        )
    ),
    array(
        'post_title' => 'FitTrack Nutrition',
        'post_name' => 'fittrack-nutrition',
        'post_content' => '<!-- Nutrition module loaded via template -->',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'page_template' => '',
        'meta' => array(
            '_wp_page_template' => 'default'
        )
    ),
    array(
        'post_title' => 'FitTrack Workouts',
        'post_name' => 'fittrack-workouts',
        'post_content' => '<!-- Workouts module loaded via template -->',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'page_template' => '',
        'meta' => array(
            '_wp_page_template' => 'default'
        )
    ),
    array(
        'post_title' => 'FitTrack Progress',
        'post_name' => 'fittrack-progress',
        'post_content' => '<!-- Progress tracking loaded via template -->',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'page_template' => '',
        'meta' => array(
            '_wp_page_template' => 'default'
        )
    ),
    array(
        'post_title' => 'FitTrack Goals',
        'post_name' => 'fittrack-goals',
        'post_content' => '<!-- Goals module loaded via template -->',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'page_template' => '',
        'meta' => array(
            '_wp_page_template' => 'default'
        )
    ),
    array(
        'post_title' => 'FitTrack Settings',
        'post_name' => 'fittrack-settings',
        'post_content' => '<!-- Settings loaded via template -->',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'page_template' => '',
        'meta' => array(
            '_wp_page_template' => 'default'
        )
    ),
    array(
        'post_title' => 'FitTrack Billing',
        'post_name' => 'fittrack-settings/billing',
        'post_content' => '<!-- Billing settings loaded via template -->',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'page_template' => '',
        'meta' => array(
            '_wp_page_template' => 'default'
        )
    ),
);

echo "Creating FitTrack Pro pages...\n\n";

$created = 0;
$skipped = 0;

foreach ($pages as $page) {
    // Check if page exists
    $existing = get_page_by_path($page['post_name']);

    if ($existing) {
        echo "⏭️  SKIPPED: {$page['post_title']} (already exists)\n";
        echo "   URL: " . get_permalink($existing->ID) . "\n\n";
        $skipped++;
        continue;
    }

    // Create page
    $page_id = wp_insert_post($page);

    if ($page_id && !is_wp_error($page_id)) {
        // Add meta data
        if (isset($page['meta']) && is_array($page['meta'])) {
            foreach ($page['meta'] as $meta_key => $meta_value) {
                update_post_meta($page_id, $meta_key, $meta_value);
            }
        }

        echo "✅ CREATED: {$page['post_title']}\n";
        echo "   Slug: {$page['post_name']}\n";
        echo "   URL: " . get_permalink($page_id) . "\n";
        echo "   ID: $page_id\n\n";

        $created++;
    } else {
        echo "❌ FAILED: {$page['post_title']}\n";
        if (is_wp_error($page_id)) {
            echo "   Error: " . $page_id->get_error_message() . "\n\n";
        }
    }
}

echo "\n" . str_repeat('-', 50) . "\n";
echo "SUMMARY:\n";
echo "- Created: $created pages\n";
echo "- Skipped: $skipped pages (already exist)\n";
echo "- Total: " . ($created + $skipped) . " pages\n";
echo str_repeat('-', 50) . "\n\n";

echo "FitTrack Pro pages setup complete!\n";
echo "Visit https://foreverbienetre.com/fittrack-dashboard to get started.\n";
