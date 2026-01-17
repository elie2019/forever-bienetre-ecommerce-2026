<?php
/**
 * FitTrack Pro - Goals Module
 */

if (!defined('ABSPATH')) exit;

class FitTrack_Goals {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_fittrack_create_goal', array($this, 'create_goal'));
        add_action('wp_ajax_fittrack_get_goals', array($this, 'get_goals'));
    }

    public function create_goal() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'fittrack_goals';

        $result = $wpdb->insert($table, array(
            'user_id' => $user_id,
            'goal_type' => sanitize_text_field($_POST['goal_type']),
            'target_value' => floatval($_POST['target_value']),
            'start_date' => sanitize_text_field($_POST['start_date']),
            'target_date' => sanitize_text_field($_POST['target_date']),
            'notes' => sanitize_textarea_field($_POST['notes']),
        ));

        if ($result) {
            wp_send_json_success(array('message' => 'Goal created successfully'));
        }

        wp_send_json_error(array('message' => 'Failed to create goal'));
    }

    public function get_goals() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'fittrack_goals';

        $goals = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND status = 'active' ORDER BY created_at DESC",
            $user_id
        ));

        wp_send_json_success(array('goals' => $goals));
    }
}
