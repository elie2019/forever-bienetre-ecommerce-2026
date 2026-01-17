<?php
/**
 * FitTrack Pro - AI Features (Gemini Integration)
 */

if (!defined('ABSPATH')) exit;

class FitTrack_AI {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_fittrack_ai_nutrition_advice', array($this, 'get_nutrition_advice'));
        add_action('wp_ajax_fittrack_ai_workout_plan', array($this, 'generate_workout_plan'));
    }

    public function get_nutrition_advice() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        // Check if user has premium plan
        $user_id = get_current_user_id();
        if (!FitTrack_Subscriptions::get_instance()->can_access_feature($user_id, 'ai_assistant')) {
            wp_send_json_error(array('message' => 'Upgrade to Premium to access AI features'));
        }

        $question = sanitize_text_field($_POST['question']);

        // Call Gemini API (simplified for demo)
        $advice = "Based on your nutritional data, I recommend focusing on balanced macros and staying hydrated. Consider adding more protein to your breakfast and reducing processed carbs in the evening.";

        wp_send_json_success(array('advice' => $advice));
    }

    public function generate_workout_plan() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $user_id = get_current_user_id();
        if (!FitTrack_Subscriptions::get_instance()->can_access_feature($user_id, 'ai_assistant')) {
            wp_send_json_error(array('message' => 'Upgrade to Premium to access AI features'));
        }

        $goals = sanitize_text_field($_POST['goals']);

        // Generate workout plan using AI (simplified)
        $plan = array(
            'name' => 'Custom AI Workout Plan',
            'duration' => '4 weeks',
            'workouts' => array(
                array('day' => 'Monday', 'focus' => 'Upper Body', 'exercises' => 5),
                array('day' => 'Wednesday', 'focus' => 'Lower Body', 'exercises' => 5),
                array('day' => 'Friday', 'focus' => 'Full Body', 'exercises' => 6),
            ),
        );

        wp_send_json_success(array('plan' => $plan));
    }
}
