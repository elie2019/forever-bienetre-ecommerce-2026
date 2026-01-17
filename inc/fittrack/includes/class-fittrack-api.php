<?php
/**
 * FitTrack Pro - REST API
 */

if (!defined('ABSPATH')) exit;

class FitTrack_API {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function register_routes() {
        register_rest_route('fittrack/v1', '/progress', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_progress'),
            'permission_callback' => array($this, 'check_permission'),
        ));

        register_rest_route('fittrack/v1', '/progress', array(
            'methods' => 'POST',
            'callback' => array($this, 'add_progress'),
            'permission_callback' => array($this, 'check_permission'),
        ));

        register_rest_route('fittrack/v1', '/nutrition', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_nutrition'),
            'permission_callback' => array($this, 'check_permission'),
        ));

        register_rest_route('fittrack/v1', '/nutrition', array(
            'methods' => 'POST',
            'callback' => array($this, 'add_nutrition'),
            'permission_callback' => array($this, 'check_permission'),
        ));
    }

    public function check_permission() {
        return is_user_logged_in();
    }

    public function get_progress($request) {
        $user_id = get_current_user_id();
        $db = FitTrack_Database::get_instance();
        $logs = $db->get_progress_logs($user_id, 30);

        return rest_ensure_response($logs);
    }

    public function add_progress($request) {
        $user_id = get_current_user_id();
        $data = $request->get_json_params();
        $db = FitTrack_Database::get_instance();

        $result = $db->insert_progress($user_id, $data);

        if ($result) {
            return rest_ensure_response(array('success' => true));
        }

        return new WP_Error('insert_failed', 'Failed to insert progress', array('status' => 500));
    }

    public function get_nutrition($request) {
        $user_id = get_current_user_id();
        $date = $request->get_param('date') ?: current_time('Y-m-d');
        $db = FitTrack_Database::get_instance();

        $logs = $db->get_nutrition_logs_by_date($user_id, $date);
        $summary = $db->get_nutrition_summary($user_id, $date);

        return rest_ensure_response(array(
            'logs' => $logs,
            'summary' => $summary,
        ));
    }

    public function add_nutrition($request) {
        $user_id = get_current_user_id();
        $data = $request->get_json_params();
        $db = FitTrack_Database::get_instance();

        $result = $db->insert_nutrition_log($user_id, $data);

        if ($result) {
            return rest_ensure_response(array('success' => true));
        }

        return new WP_Error('insert_failed', 'Failed to insert nutrition log', array('status' => 500));
    }
}
