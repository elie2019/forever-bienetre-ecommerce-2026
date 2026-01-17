<?php
/**
 * FitTrack Pro - Initialization
 *
 * Main initialization file for FitTrack Pro fitness and nutrition tracking platform
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
 * FitTrack Pro Main Class
 */
class FitTrack_Pro {

    /**
     * Single instance
     */
    private static $instance = null;

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
        $this->define_constants();
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Define constants
     */
    private function define_constants() {
        define('FITTRACK_VERSION', '1.0.0');
        define('FITTRACK_PATH', get_template_directory() . '/inc/fittrack/');
        define('FITTRACK_URL', get_template_directory_uri() . '/inc/fittrack/');
        define('FITTRACK_ASSETS_URL', get_template_directory_uri() . '/assets/fittrack/');
    }

    /**
     * Load dependencies
     */
    private function load_dependencies() {
        // Data Synchronization System (NEW - Priority Load)
        if (file_exists(FITTRACK_PATH . 'fittrack-data-sync.php')) {
            require_once FITTRACK_PATH . 'fittrack-data-sync.php';
        }

        // Core functionality
        if (file_exists(FITTRACK_PATH . 'includes/class-fittrack-cpt.php')) {
            require_once FITTRACK_PATH . 'includes/class-fittrack-cpt.php';
        }
        if (file_exists(FITTRACK_PATH . 'includes/class-fittrack-database.php')) {
            require_once FITTRACK_PATH . 'includes/class-fittrack-database.php';
        }
        if (file_exists(FITTRACK_PATH . 'includes/class-fittrack-auth.php')) {
            require_once FITTRACK_PATH . 'includes/class-fittrack-auth.php';
        }
        if (file_exists(FITTRACK_PATH . 'includes/class-fittrack-user.php')) {
            require_once FITTRACK_PATH . 'includes/class-fittrack-user.php';
        }

        // Stripe integration
        if (file_exists(FITTRACK_PATH . 'includes/class-fittrack-stripe.php')) {
            require_once FITTRACK_PATH . 'includes/class-fittrack-stripe.php';
        }
        if (file_exists(FITTRACK_PATH . 'includes/class-fittrack-subscriptions.php')) {
            require_once FITTRACK_PATH . 'includes/class-fittrack-subscriptions.php';
        }

        // Modules
        if (file_exists(FITTRACK_PATH . 'modules/nutrition/class-nutrition.php')) {
            require_once FITTRACK_PATH . 'modules/nutrition/class-nutrition.php';
        }
        if (file_exists(FITTRACK_PATH . 'modules/workouts/class-workouts.php')) {
            require_once FITTRACK_PATH . 'modules/workouts/class-workouts.php';
        }
        if (file_exists(FITTRACK_PATH . 'modules/progress/class-progress.php')) {
            require_once FITTRACK_PATH . 'modules/progress/class-progress.php';
        }
        if (file_exists(FITTRACK_PATH . 'modules/goals/class-goals.php')) {
            require_once FITTRACK_PATH . 'modules/goals/class-goals.php';
        }

        // AI Features
        if (file_exists(FITTRACK_PATH . 'includes/class-fittrack-ai.php')) {
            require_once FITTRACK_PATH . 'includes/class-fittrack-ai.php';
        }

        // REST API
        if (file_exists(FITTRACK_PATH . 'includes/class-fittrack-api.php')) {
            require_once FITTRACK_PATH . 'includes/class-fittrack-api.php';
        }

        // Admin
        if (is_admin() && file_exists(FITTRACK_PATH . 'admin/class-fittrack-admin.php')) {
            require_once FITTRACK_PATH . 'admin/class-fittrack-admin.php';
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // Template redirect
        add_filter('template_include', array($this, 'template_include'));
    }

    /**
     * Init
     */
    public function init() {
        // Initialize custom post types
        FitTrack_CPT::get_instance();

        // Initialize database tables
        FitTrack_Database::get_instance();

        // Initialize Stripe
        FitTrack_Stripe::get_instance();

        // Initialize modules
        FitTrack_Nutrition::get_instance();
        FitTrack_Workouts::get_instance();
        FitTrack_Progress::get_instance();
        FitTrack_Goals::get_instance();

        // Initialize AI
        FitTrack_AI::get_instance();

        // Flush rewrite rules if needed
        if (get_option('fittrack_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('fittrack_flush_rewrite_rules');
        }
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        // Check if we're on a FitTrack page
        if (!$this->is_fittrack_page()) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'fittrack-main',
            FITTRACK_ASSETS_URL . 'css/fittrack-main.css',
            array(),
            FITTRACK_VERSION
        );

        // JavaScript
        wp_enqueue_script(
            'fittrack-main',
            FITTRACK_ASSETS_URL . 'js/fittrack-main.js',
            array('jquery'),
            FITTRACK_VERSION,
            true
        );

        // Chart.js for graphs
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            array(),
            '4.4.0',
            true
        );

        // Localize script
        wp_localize_script('fittrack-main', 'fittrackData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('fittrack/v1/'),
            'nonce' => wp_create_nonce('fittrack_nonce'),
            'userId' => get_current_user_id(),
            'isLoggedIn' => is_user_logged_in(),
            'userPlan' => $this->get_user_plan(),
            'strings' => array(
                'loading' => __('Loading...', 'forever-be-premium'),
                'error' => __('An error occurred', 'forever-be-premium'),
                'success' => __('Success!', 'forever-be-premium'),
            ),
        ));
    }

    /**
     * Admin enqueue scripts
     */
    public function admin_enqueue_scripts($hook) {
        // Only on FitTrack admin pages
        if (strpos($hook, 'fittrack') === false) {
            return;
        }

        wp_enqueue_style(
            'fittrack-admin',
            FITTRACK_ASSETS_URL . 'css/fittrack-admin.css',
            array(),
            FITTRACK_VERSION
        );

        wp_enqueue_script(
            'fittrack-admin',
            FITTRACK_ASSETS_URL . 'js/fittrack-admin.js',
            array('jquery'),
            FITTRACK_VERSION,
            true
        );
    }

    /**
     * Register REST routes
     */
    public function register_rest_routes() {
        FitTrack_API::get_instance()->register_routes();
    }

    /**
     * Template include
     */
    public function template_include($template) {
        // Check for FitTrack pages
        $page_slug = get_query_var('pagename');

        $fittrack_pages = array(
            'fittrack-login',
            'fittrack-register',
            'fittrack-dashboard',
            'fittrack-nutrition',
            'fittrack-workouts',
            'fittrack-exercises',
            'fittrack-progress',
            'fittrack-goals',
            'fittrack-settings',
            'fittrack-pricing',
        );

        if (in_array($page_slug, $fittrack_pages)) {
            $custom_template = FITTRACK_PATH . 'templates/' . $page_slug . '.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        return $template;
    }

    /**
     * Check if current page is FitTrack page
     */
    private function is_fittrack_page() {
        global $post;

        if (!$post) {
            return false;
        }

        $fittrack_pages = array(
            'fittrack-login',
            'fittrack-register',
            'fittrack-dashboard',
            'fittrack-nutrition',
            'fittrack-workouts',
            'fittrack-exercises',
            'fittrack-progress',
            'fittrack-goals',
            'fittrack-settings',
            'fittrack-pricing',
        );

        return in_array($post->post_name, $fittrack_pages);
    }

    /**
     * Get user plan
     */
    private function get_user_plan() {
        if (!is_user_logged_in()) {
            return 'free';
        }

        $user_id = get_current_user_id();
        $subscription = get_user_meta($user_id, 'fittrack_subscription', true);

        return $subscription ? $subscription['plan'] : 'free';
    }
}

/**
 * Initialize FitTrack Pro
 */
function fittrack_pro_init() {
    return FitTrack_Pro::get_instance();
}

// Initialize
fittrack_pro_init();
