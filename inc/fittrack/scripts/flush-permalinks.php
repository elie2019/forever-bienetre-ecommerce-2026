<?php
/**
 * FitTrack Pro - Flush Permalinks
 *
 * This script flushes WordPress permalinks to recognize new pages
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

echo "Flushing WordPress permalinks...\n";

// Flush rewrite rules
flush_rewrite_rules(true);

echo "✅ Permalinks flushed successfully!\n";
echo "\nFitTrack pages should now be accessible:\n";
echo "- https://foreverbienetre.com/fittrack-dashboard\n";
echo "- https://foreverbienetre.com/fittrack-pricing\n";
echo "- https://foreverbienetre.com/fittrack-nutrition\n";
echo "- https://foreverbienetre.com/fittrack-workouts\n";
echo "- https://foreverbienetre.com/fittrack-progress\n";
echo "- https://foreverbienetre.com/fittrack-goals\n";
echo "- https://foreverbienetre.com/fittrack-settings\n";
