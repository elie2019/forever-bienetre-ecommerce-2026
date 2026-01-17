<?php
/**
 * FitTrack Pro - Subscriptions Management
 */

if (!defined('ABSPATH')) exit;

class FitTrack_Subscriptions {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function get_user_subscription($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'fittrack_subscriptions';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id));
    }

    public function create_free_subscription($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'fittrack_subscriptions';

        return $wpdb->insert($table, array(
            'user_id' => $user_id,
            'plan' => 'free',
            'status' => 'active',
        ));
    }

    public function update_subscription($user_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'fittrack_subscriptions';

        // Check if subscription exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE user_id = %d", $user_id));

        if ($exists) {
            return $wpdb->update($table, $data, array('user_id' => $user_id));
        } else {
            $data['user_id'] = $user_id;
            return $wpdb->insert($table, $data);
        }
    }

    public function has_active_subscription($user_id) {
        $subscription = $this->get_user_subscription($user_id);
        return $subscription && $subscription->status === 'active' && in_array($subscription->plan, array('pro', 'premium'));
    }

    public function can_access_feature($user_id, $feature) {
        $subscription = $this->get_user_subscription($user_id);
        if (!$subscription) return false;

        $plan_features = array(
            'free' => array('basic_tracking'),
            'pro' => array('basic_tracking', 'advanced_analytics', 'custom_programs'),
            'premium' => array('basic_tracking', 'advanced_analytics', 'custom_programs', 'ai_assistant', 'pdf_reports'),
        );

        $plan = $subscription->plan;
        return isset($plan_features[$plan]) && in_array($feature, $plan_features[$plan]);
    }
}
