<?php
/**
 * FitTrack Pro - Admin Panel
 */

if (!defined('ABSPATH')) exit;

class FitTrack_Admin {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'FitTrack Pro',
            'FitTrack Pro',
            'manage_options',
            'fittrack-pro',
            array($this, 'admin_page'),
            'dashicons-heart',
            30
        );

        add_submenu_page(
            'fittrack-pro',
            'Settings',
            'Settings',
            'manage_options',
            'fittrack-settings',
            array($this, 'settings_page')
        );
    }

    public function admin_page() {
        global $wpdb;
        $subscriptions_table = $wpdb->prefix . 'fittrack_subscriptions';

        $stats = $wpdb->get_row("
            SELECT
                COUNT(*) as total_users,
                SUM(CASE WHEN plan = 'pro' THEN 1 ELSE 0 END) as pro_users,
                SUM(CASE WHEN plan = 'premium' THEN 1 ELSE 0 END) as premium_users
            FROM $subscriptions_table
        ");

        ?>
        <div class="wrap">
            <h1>FitTrack Pro Dashboard</h1>
            <div class="fittrack-admin-stats">
                <div class="stat-box">
                    <h3><?php echo $stats->total_users; ?></h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $stats->pro_users; ?></h3>
                    <p>Pro Subscribers</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $stats->premium_users; ?></h3>
                    <p>Premium Subscribers</p>
                </div>
            </div>
        </div>
        <?php
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>FitTrack Pro Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('fittrack_settings');
                do_settings_sections('fittrack_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
