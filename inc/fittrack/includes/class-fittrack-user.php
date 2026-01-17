<?php
/**
 * FitTrack Pro - User Management
 */

if (!defined('ABSPATH')) exit;

class FitTrack_User {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function get_user_profile($user_id) {
        return array(
            'id' => $user_id,
            'name' => get_user_meta($user_id, 'first_name', true) . ' ' . get_user_meta($user_id, 'last_name', true),
            'email' => get_userdata($user_id)->user_email,
            'avatar' => get_avatar_url($user_id),
            'subscription' => FitTrack_Subscriptions::get_instance()->get_user_subscription($user_id),
        );
    }

    public function update_user_profile($user_id, $data) {
        if (isset($data['first_name'])) {
            update_user_meta($user_id, 'first_name', sanitize_text_field($data['first_name']));
        }
        if (isset($data['last_name'])) {
            update_user_meta($user_id, 'last_name', sanitize_text_field($data['last_name']));
        }
        return true;
    }
}
