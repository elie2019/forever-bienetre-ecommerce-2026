<?php
/**
 * FitTrack Pro - Progress Module
 */

if (!defined('ABSPATH')) exit;

class FitTrack_Progress {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_fittrack_add_progress', array($this, 'add_progress'));
        add_action('wp_ajax_fittrack_get_progress_data', array($this, 'get_progress_data'));
    }

    public function add_progress() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $user_id = get_current_user_id();
        $data = array(
            'date' => sanitize_text_field($_POST['date']),
            'weight' => floatval($_POST['weight']),
            'body_fat' => isset($_POST['body_fat']) ? floatval($_POST['body_fat']) : null,
            'muscle_mass' => isset($_POST['muscle_mass']) ? floatval($_POST['muscle_mass']) : null,
            'notes' => sanitize_textarea_field($_POST['notes']),
        );

        $db = FitTrack_Database::get_instance();
        $result = $db->insert_progress($user_id, $data);

        if ($result) {
            wp_send_json_success(array('message' => 'Progress logged successfully'));
        }

        wp_send_json_error(array('message' => 'Failed to log progress'));
    }

    public function get_progress_data() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $user_id = get_current_user_id();
        $db = FitTrack_Database::get_instance();
        $logs = $db->get_progress_logs($user_id, 90);

        wp_send_json_success(array('logs' => $logs));
    }
}
