<?php
/**
 * FitTrack Pro - Authentication
 */

if (!defined('ABSPATH')) exit;

class FitTrack_Auth {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_fittrack_login', array($this, 'ajax_login'));
        add_action('wp_ajax_nopriv_fittrack_login', array($this, 'ajax_login'));
        add_action('wp_ajax_fittrack_register', array($this, 'ajax_register'));
        add_action('wp_ajax_nopriv_fittrack_register', array($this, 'ajax_register'));
    }

    public function ajax_login() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        $username = sanitize_text_field($_POST['username']);
        $password = $_POST['password'];

        $user = wp_signon(array(
            'user_login' => $username,
            'user_password' => $password,
            'remember' => true,
        ), false);

        if (is_wp_error($user)) {
            wp_send_json_error(array('message' => $user->get_error_message()));
        }

        wp_send_json_success(array(
            'redirect' => home_url('/fittrack-dashboard'),
        ));
    }

    public function ajax_register() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $username = sanitize_user($_POST['username']);

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        }

        // Create free subscription
        FitTrack_Subscriptions::get_instance()->create_free_subscription($user_id);

        // Auto login
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        wp_send_json_success(array(
            'redirect' => home_url('/fittrack-dashboard'),
        ));
    }
}
