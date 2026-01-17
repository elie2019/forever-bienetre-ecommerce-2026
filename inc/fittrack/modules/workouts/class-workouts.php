<?php
/**
 * FitTrack Pro - Workouts Module
 */

if (!defined('ABSPATH')) exit;

class FitTrack_Workouts {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_fittrack_log_workout', array($this, 'log_workout'));
        add_action('wp_ajax_fittrack_get_workout_history', array($this, 'get_workout_history'));
    }

    public function log_workout() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $user_id = get_current_user_id();
        $data = array(
            'workout_id' => intval($_POST['workout_id']),
            'date' => sanitize_text_field($_POST['date']),
            'duration' => intval($_POST['duration']),
            'calories_burned' => intval($_POST['calories_burned']),
            'notes' => sanitize_textarea_field($_POST['notes']),
        );

        $db = FitTrack_Database::get_instance();
        $result = $db->insert_workout_log($user_id, $data);

        if ($result) {
            wp_send_json_success(array('message' => 'Workout logged successfully'));
        }

        wp_send_json_error(array('message' => 'Failed to log workout'));
    }

    public function get_workout_history() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'fittrack_workout_logs';

        $logs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY date DESC LIMIT 30",
            $user_id
        ));

        wp_send_json_success(array('logs' => $logs));
    }
}
