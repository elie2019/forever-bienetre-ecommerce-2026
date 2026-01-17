<?php
/**
 * FitTrack Pro - Nutrition Module
 */

if (!defined('ABSPATH')) exit;

class FitTrack_Nutrition {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_fittrack_add_meal', array($this, 'add_meal'));
        add_action('wp_ajax_fittrack_get_daily_nutrition', array($this, 'get_daily_nutrition'));
        add_action('wp_ajax_fittrack_search_foods', array($this, 'search_foods'));
    }

    public function add_meal() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $user_id = get_current_user_id();
        $data = array(
            'date' => sanitize_text_field($_POST['date']),
            'meal_type' => sanitize_text_field($_POST['meal_type']),
            'food_name' => sanitize_text_field($_POST['food_name']),
            'quantity' => floatval($_POST['quantity']),
            'unit' => sanitize_text_field($_POST['unit']),
            'calories' => intval($_POST['calories']),
            'protein' => floatval($_POST['protein']),
            'carbs' => floatval($_POST['carbs']),
            'fat' => floatval($_POST['fat']),
        );

        $db = FitTrack_Database::get_instance();
        $result = $db->insert_nutrition_log($user_id, $data);

        if ($result) {
            wp_send_json_success(array('message' => 'Meal added successfully'));
        }

        wp_send_json_error(array('message' => 'Failed to add meal'));
    }

    public function get_daily_nutrition() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $user_id = get_current_user_id();
        $date = sanitize_text_field($_POST['date']) ?: current_time('Y-m-d');

        $db = FitTrack_Database::get_instance();
        $logs = $db->get_nutrition_logs_by_date($user_id, $date);
        $summary = $db->get_nutrition_summary($user_id, $date);

        wp_send_json_success(array(
            'logs' => $logs,
            'summary' => $summary,
        ));
    }

    public function search_foods() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        $query = sanitize_text_field($_POST['query']);

        // Simple food database
        $foods = array(
            array('name' => 'Chicken Breast', 'calories' => 165, 'protein' => 31, 'carbs' => 0, 'fat' => 3.6),
            array('name' => 'Brown Rice', 'calories' => 216, 'protein' => 5, 'carbs' => 45, 'fat' => 1.8),
            array('name' => 'Broccoli', 'calories' => 55, 'protein' => 3.7, 'carbs' => 11, 'fat' => 0.6),
            array('name' => 'Salmon', 'calories' => 208, 'protein' => 20, 'carbs' => 0, 'fat' => 13),
            array('name' => 'Eggs', 'calories' => 155, 'protein' => 13, 'carbs' => 1.1, 'fat' => 11),
            array('name' => 'Oatmeal', 'calories' => 389, 'protein' => 17, 'carbs' => 66, 'fat' => 7),
            array('name' => 'Banana', 'calories' => 105, 'protein' => 1.3, 'carbs' => 27, 'fat' => 0.4),
            array('name' => 'Almonds', 'calories' => 164, 'protein' => 6, 'carbs' => 6, 'fat' => 14),
        );

        $results = array_filter($foods, function($food) use ($query) {
            return stripos($food['name'], $query) !== false;
        });

        wp_send_json_success(array('foods' => array_values($results)));
    }
}
