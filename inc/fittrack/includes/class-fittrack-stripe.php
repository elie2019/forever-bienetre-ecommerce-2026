<?php
/**
 * FitTrack Pro - Stripe Integration
 *
 * Handle Stripe payments and subscriptions
 *
 * @package Forever_BE_Premium
 * @subpackage FitTrack_Pro
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Stripe Integration Class
 */
class FitTrack_Stripe {

    /**
     * Single instance
     */
    private static $instance = null;

    /**
     * Stripe API keys
     */
    private $publishable_key;
    private $secret_key;

    /**
     * Plans configuration
     */
    private $plans = array();

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Load Stripe PHP library
        $this->load_stripe_library();

        // Set API keys from WordPress options (secure)
        // Configure these in WordPress admin or wp-config.php
        $this->publishable_key = defined('FITTRACK_STRIPE_PUBLISHABLE_KEY')
            ? FITTRACK_STRIPE_PUBLISHABLE_KEY
            : get_option('fittrack_stripe_publishable_key', '');

        $this->secret_key = defined('FITTRACK_STRIPE_SECRET_KEY')
            ? FITTRACK_STRIPE_SECRET_KEY
            : get_option('fittrack_stripe_secret_key', '');

        // Configure plans
        $this->configure_plans();

        // Initialize Stripe
        if (class_exists('Stripe\Stripe')) {
            \Stripe\Stripe::setApiKey($this->secret_key);
        }

        // Hooks
        $this->init_hooks();
    }

    /**
     * Load Stripe library
     */
    private function load_stripe_library() {
        // Check if Stripe library is already loaded
        if (!class_exists('Stripe\Stripe')) {
            // Try to use Composer autoload if available
            $composer_autoload = get_template_directory() . '/vendor/autoload.php';
            if (file_exists($composer_autoload)) {
                require_once $composer_autoload;
            }
        }
    }

    /**
     * Configure plans
     */
    private function configure_plans() {
        $this->plans = array(
            'free' => array(
                'name' => 'Free',
                'price' => 0,
                'currency' => 'eur',
                'interval' => 'month',
                'features' => array(
                    'Basic workout tracking',
                    'Basic nutrition logging',
                    'Limited exercise library',
                ),
                'stripe_price_id' => null, // No Stripe for free plan
            ),
            'pro' => array(
                'name' => 'Pro',
                'price' => 999, // 9.99 EUR in cents
                'currency' => 'eur',
                'interval' => 'month',
                'features' => array(
                    'Unlimited workout tracking',
                    'Advanced nutrition analysis',
                    'Full exercise library with videos',
                    'Custom workout programs',
                    'Progress charts and analytics',
                ),
                'stripe_price_id' => null, // Will be created
                'stripe_product_id' => null,
            ),
            'premium' => array(
                'name' => 'Premium',
                'price' => 7999, // 79.99 EUR in cents
                'currency' => 'eur',
                'interval' => 'year',
                'features' => array(
                    'All Pro features',
                    'AI Nutritional Assistant',
                    'AI Workout Plan Generator',
                    'Weekly PDF Reports',
                    'Priority support',
                    'Export all data',
                ),
                'stripe_price_id' => null, // Will be created
                'stripe_product_id' => null,
            ),
        );
    }

    /**
     * Init hooks
     */
    private function init_hooks() {
        // AJAX handlers
        add_action('wp_ajax_fittrack_create_checkout_session', array($this, 'create_checkout_session'));
        add_action('wp_ajax_fittrack_create_portal_session', array($this, 'create_portal_session'));
        add_action('wp_ajax_fittrack_cancel_subscription', array($this, 'cancel_subscription'));

        // Webhook handler
        add_action('wp_ajax_nopriv_fittrack_stripe_webhook', array($this, 'handle_webhook'));
        add_action('wp_ajax_fittrack_stripe_webhook', array($this, 'handle_webhook'));

        // Enqueue Stripe.js on pricing page
        add_action('wp_enqueue_scripts', array($this, 'enqueue_stripe_js'));
    }

    /**
     * Enqueue Stripe.js
     */
    public function enqueue_stripe_js() {
        if (is_page('fittrack-pricing')) {
            wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, true);
        }
    }

    /**
     * Get plans
     */
    public function get_plans() {
        return $this->plans;
    }

    /**
     * Get plan
     */
    public function get_plan($plan_slug) {
        return isset($this->plans[$plan_slug]) ? $this->plans[$plan_slug] : null;
    }

    /**
     * Get publishable key
     */
    public function get_publishable_key() {
        return $this->publishable_key;
    }

    /**
     * Create checkout session (AJAX)
     */
    public function create_checkout_session() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'User not logged in'));
        }

        $plan = sanitize_text_field($_POST['plan']);
        $user_id = get_current_user_id();

        try {
            // Get or create Stripe customer
            $customer_id = $this->get_or_create_customer($user_id);

            // Create price if not exists
            $price_id = $this->get_or_create_price($plan);

            if (!$price_id) {
                wp_send_json_error(array('message' => 'Invalid plan'));
            }

            // Create checkout session
            $session = \Stripe\Checkout\Session::create(array(
                'customer' => $customer_id,
                'payment_method_types' => array('card'),
                'line_items' => array(
                    array(
                        'price' => $price_id,
                        'quantity' => 1,
                    ),
                ),
                'mode' => 'subscription',
                'success_url' => home_url('/fittrack-dashboard?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => home_url('/fittrack-pricing'),
                'metadata' => array(
                    'user_id' => $user_id,
                    'plan' => $plan,
                ),
            ));

            wp_send_json_success(array(
                'sessionId' => $session->id,
            ));

        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * Create portal session (AJAX)
     */
    public function create_portal_session() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'User not logged in'));
        }

        $user_id = get_current_user_id();

        try {
            // Get Stripe customer ID
            $customer_id = get_user_meta($user_id, 'fittrack_stripe_customer_id', true);

            if (!$customer_id) {
                wp_send_json_error(array('message' => 'No subscription found'));
            }

            // Create portal session
            $session = \Stripe\BillingPortal\Session::create(array(
                'customer' => $customer_id,
                'return_url' => home_url('/fittrack-settings/billing'),
            ));

            wp_send_json_success(array(
                'url' => $session->url,
            ));

        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage(),
            ));
        }
    }

    /**
     * Get or create Stripe customer
     */
    private function get_or_create_customer($user_id) {
        $customer_id = get_user_meta($user_id, 'fittrack_stripe_customer_id', true);

        if ($customer_id) {
            return $customer_id;
        }

        // Create new customer
        $user = get_user_by('id', $user_id);

        $customer = \Stripe\Customer::create(array(
            'email' => $user->user_email,
            'name' => $user->display_name,
            'metadata' => array(
                'user_id' => $user_id,
            ),
        ));

        // Save customer ID
        update_user_meta($user_id, 'fittrack_stripe_customer_id', $customer->id);

        return $customer->id;
    }

    /**
     * Get or create price
     */
    private function get_or_create_price($plan_slug) {
        if (!isset($this->plans[$plan_slug])) {
            return null;
        }

        $plan = $this->plans[$plan_slug];

        // Check if price ID exists in options
        $price_id = get_option('fittrack_stripe_price_' . $plan_slug);

        if ($price_id) {
            return $price_id;
        }

        // Create product and price
        try {
            // Create product
            $product = \Stripe\Product::create(array(
                'name' => 'FitTrack Pro - ' . $plan['name'],
                'description' => 'FitTrack Pro ' . $plan['name'] . ' Plan',
            ));

            // Create price
            $price = \Stripe\Price::create(array(
                'product' => $product->id,
                'unit_amount' => $plan['price'],
                'currency' => $plan['currency'],
                'recurring' => array(
                    'interval' => $plan['interval'],
                ),
            ));

            // Save IDs
            update_option('fittrack_stripe_product_' . $plan_slug, $product->id);
            update_option('fittrack_stripe_price_' . $plan_slug, $price->id);

            return $price->id;

        } catch (Exception $e) {
            error_log('FitTrack Stripe Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle webhook
     */
    public function handle_webhook() {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $endpoint_secret = get_option('fittrack_stripe_webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }

        // Handle event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handle_checkout_completed($event->data->object);
                break;

            case 'customer.subscription.updated':
            case 'customer.subscription.created':
                $this->handle_subscription_updated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handle_subscription_deleted($event->data->object);
                break;
        }

        http_response_code(200);
        echo json_encode(array('status' => 'success'));
        exit;
    }

    /**
     * Handle checkout completed
     */
    private function handle_checkout_completed($session) {
        $user_id = $session->metadata->user_id;
        $plan = $session->metadata->plan;

        // Update user subscription
        FitTrack_Subscriptions::get_instance()->update_subscription($user_id, array(
            'plan' => $plan,
            'stripe_customer_id' => $session->customer,
            'stripe_subscription_id' => $session->subscription,
            'status' => 'active',
        ));
    }

    /**
     * Handle subscription updated
     */
    private function handle_subscription_updated($subscription) {
        global $wpdb;

        $table = $wpdb->prefix . 'fittrack_subscriptions';

        $wpdb->update(
            $table,
            array(
                'status' => $subscription->status,
                'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
            ),
            array('stripe_subscription_id' => $subscription->id)
        );
    }

    /**
     * Handle subscription deleted
     */
    private function handle_subscription_deleted($subscription) {
        global $wpdb;

        $table = $wpdb->prefix . 'fittrack_subscriptions';

        $wpdb->update(
            $table,
            array(
                'status' => 'canceled',
                'canceled_at' => current_time('mysql'),
                'plan' => 'free',
            ),
            array('stripe_subscription_id' => $subscription->id)
        );
    }

    /**
     * Cancel subscription (AJAX)
     */
    public function cancel_subscription() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'User not logged in'));
        }

        $user_id = get_current_user_id();

        try {
            $subscription = FitTrack_Subscriptions::get_instance()->get_user_subscription($user_id);

            if ($subscription && $subscription->stripe_subscription_id) {
                // Cancel at period end
                \Stripe\Subscription::update($subscription->stripe_subscription_id, array(
                    'cancel_at_period_end' => true,
                ));

                wp_send_json_success(array(
                    'message' => 'Subscription will be canceled at the end of the billing period',
                ));
            } else {
                wp_send_json_error(array('message' => 'No active subscription found'));
            }

        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => $e->getMessage(),
            ));
        }
    }
}
