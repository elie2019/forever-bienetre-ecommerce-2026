<?php
/**
 * FitTrack Pro - Insert Pages via Direct SQL
 *
 * This script inserts pages directly into WordPress database
 * Use this if WP-CLI or admin access is not available
 *
 * @package Forever_BE_Premium
 * @subpackage FitTrack_Pro
 */

// Load WordPress configuration
$wp_config_path = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/wp-config.php';
if (!file_exists($wp_config_path)) {
    die("Error: Cannot find wp-config.php\n");
}

// Extract database credentials from wp-config.php
$config_content = file_get_contents($wp_config_path);

preg_match("/define\(\s*'DB_NAME',\s*'([^']+)'\s*\);/", $config_content, $db_name_match);
preg_match("/define\(\s*'DB_USER',\s*'([^']+)'\s*\);/", $config_content, $db_user_match);
preg_match("/define\(\s*'DB_PASSWORD',\s*'([^']+)'\s*\);/", $config_content, $db_pass_match);
preg_match("/define\(\s*'DB_HOST',\s*'([^']+)'\s*\);/", $config_content, $db_host_match);
preg_match("/\\\$table_prefix\s*=\s*'([^']+)'/", $config_content, $prefix_match);

$db_name = $db_name_match[1] ?? 'wordpress';
$db_user = $db_user_match[1] ?? 'root';
$db_pass = $db_pass_match[1] ?? '';
$db_host = $db_host_match[1] ?? 'localhost';
$table_prefix = $prefix_match[1] ?? 'wp_';

echo "Connecting to database...\n";
echo "Database: $db_name\n";
echo "Host: $db_host\n";
echo "Table Prefix: $table_prefix\n\n";

// Connect to database
try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

echo "✅ Connected successfully!\n\n";

// Pages to create
$pages = array(
    array(
        'title' => 'FitTrack Dashboard',
        'slug' => 'fittrack-dashboard',
        'content' => '<!-- Dashboard content loaded via template -->'
    ),
    array(
        'title' => 'FitTrack Pricing',
        'slug' => 'fittrack-pricing',
        'content' => '<!-- Pricing content loaded via template -->'
    ),
    array(
        'title' => 'FitTrack Nutrition',
        'slug' => 'fittrack-nutrition',
        'content' => '<!-- Nutrition module loaded via template -->'
    ),
    array(
        'title' => 'FitTrack Workouts',
        'slug' => 'fittrack-workouts',
        'content' => '<!-- Workouts module loaded via template -->'
    ),
    array(
        'title' => 'FitTrack Progress',
        'slug' => 'fittrack-progress',
        'content' => '<!-- Progress tracking loaded via template -->'
    ),
    array(
        'title' => 'FitTrack Goals',
        'slug' => 'fittrack-goals',
        'content' => '<!-- Goals module loaded via template -->'
    ),
    array(
        'title' => 'FitTrack Settings',
        'slug' => 'fittrack-settings',
        'content' => '<!-- Settings loaded via template -->'
    ),
);

$created = 0;
$skipped = 0;

foreach ($pages as $page) {
    // Check if page exists
    $check_stmt = $pdo->prepare("
        SELECT ID FROM {$table_prefix}posts
        WHERE post_name = :slug AND post_type = 'page'
    ");
    $check_stmt->execute(['slug' => $page['slug']]);
    $existing = $check_stmt->fetch();

    if ($existing) {
        echo "⏭️  SKIPPED: {$page['title']} (already exists - ID: {$existing['ID']})\n\n";
        $skipped++;
        continue;
    }

    // Insert page
    $now = date('Y-m-d H:i:s');

    $insert_stmt = $pdo->prepare("
        INSERT INTO {$table_prefix}posts (
            post_author,
            post_date,
            post_date_gmt,
            post_content,
            post_title,
            post_excerpt,
            post_status,
            comment_status,
            ping_status,
            post_password,
            post_name,
            to_ping,
            pinged,
            post_modified,
            post_modified_gmt,
            post_content_filtered,
            post_parent,
            guid,
            menu_order,
            post_type,
            post_mime_type,
            comment_count
        ) VALUES (
            1,
            :post_date,
            :post_date_gmt,
            :post_content,
            :post_title,
            '',
            'publish',
            'closed',
            'closed',
            '',
            :post_name,
            '',
            '',
            :post_modified,
            :post_modified_gmt,
            '',
            0,
            '',
            0,
            'page',
            '',
            0
        )
    ");

    $result = $insert_stmt->execute([
        'post_date' => $now,
        'post_date_gmt' => gmdate('Y-m-d H:i:s'),
        'post_content' => $page['content'],
        'post_title' => $page['title'],
        'post_name' => $page['slug'],
        'post_modified' => $now,
        'post_modified_gmt' => gmdate('Y-m-d H:i:s'),
    ]);

    if ($result) {
        $page_id = $pdo->lastInsertId();

        // Update GUID
        $update_guid_stmt = $pdo->prepare("
            UPDATE {$table_prefix}posts
            SET guid = CONCAT('https://foreverbienetre.com/?page_id=', :page_id)
            WHERE ID = :page_id
        ");
        $update_guid_stmt->execute(['page_id' => $page_id]);

        echo "✅ CREATED: {$page['title']}\n";
        echo "   Slug: {$page['slug']}\n";
        echo "   ID: $page_id\n";
        echo "   URL: https://foreverbienetre.com/{$page['slug']}\n\n";

        $created++;
    } else {
        echo "❌ FAILED: {$page['title']}\n\n";
    }
}

echo str_repeat('-', 50) . "\n";
echo "SUMMARY:\n";
echo "- Created: $created pages\n";
echo "- Skipped: $skipped pages (already exist)\n";
echo "- Total: " . ($created + $skipped) . " pages\n";
echo str_repeat('-', 50) . "\n\n";

echo "FitTrack Pro pages setup complete!\n";
echo "Visit https://foreverbienetre.com/fittrack-dashboard to get started.\n";
