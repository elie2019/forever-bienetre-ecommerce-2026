<?php
/**
 * FitTrack Pro - Database
 *
 * Create and manage custom database tables
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
 * Database Class
 */
class FitTrack_Database {

    /**
     * Single instance
     */
    private static $instance = null;

    /**
     * Database version
     */
    private $db_version = '1.0.0';

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
        $this->maybe_create_tables();
    }

    /**
     * Maybe create tables
     */
    private function maybe_create_tables() {
        $installed_version = get_option('fittrack_db_version', '0');

        if (version_compare($installed_version, $this->db_version, '<')) {
            $this->create_tables();
            update_option('fittrack_db_version', $this->db_version);
        }
    }

    /**
     * Create tables
     */
    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Table: progress_logs
        $table_progress = $wpdb->prefix . 'fittrack_progress_logs';
        $sql_progress = "CREATE TABLE IF NOT EXISTS $table_progress (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            date date NOT NULL,
            weight decimal(5,2) DEFAULT NULL,
            body_fat decimal(5,2) DEFAULT NULL,
            muscle_mass decimal(5,2) DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY date (date)
        ) $charset_collate;";

        // Table: workout_logs
        $table_workouts = $wpdb->prefix . 'fittrack_workout_logs';
        $sql_workouts = "CREATE TABLE IF NOT EXISTS $table_workouts (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            workout_id bigint(20) NOT NULL,
            date datetime NOT NULL,
            duration int(11) DEFAULT NULL COMMENT 'Duration in minutes',
            calories_burned int(11) DEFAULT NULL,
            notes text DEFAULT NULL,
            status varchar(20) DEFAULT 'completed',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY workout_id (workout_id),
            KEY date (date)
        ) $charset_collate;";

        // Table: exercise_logs
        $table_exercises = $wpdb->prefix . 'fittrack_exercise_logs';
        $sql_exercises = "CREATE TABLE IF NOT EXISTS $table_exercises (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            workout_log_id bigint(20) NOT NULL,
            exercise_id bigint(20) NOT NULL,
            sets int(11) DEFAULT NULL,
            reps int(11) DEFAULT NULL,
            weight decimal(5,2) DEFAULT NULL,
            duration int(11) DEFAULT NULL COMMENT 'Duration in seconds',
            rest_time int(11) DEFAULT NULL COMMENT 'Rest in seconds',
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY workout_log_id (workout_log_id),
            KEY exercise_id (exercise_id)
        ) $charset_collate;";

        // Table: nutrition_logs
        $table_nutrition = $wpdb->prefix . 'fittrack_nutrition_logs';
        $sql_nutrition = "CREATE TABLE IF NOT EXISTS $table_nutrition (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            date date NOT NULL,
            meal_type varchar(20) NOT NULL COMMENT 'breakfast, lunch, dinner, snack',
            food_id bigint(20) DEFAULT NULL,
            food_name varchar(255) NOT NULL,
            quantity decimal(10,2) NOT NULL DEFAULT 1,
            unit varchar(50) NOT NULL DEFAULT 'serving',
            calories int(11) DEFAULT NULL,
            protein decimal(5,2) DEFAULT NULL,
            carbs decimal(5,2) DEFAULT NULL,
            fat decimal(5,2) DEFAULT NULL,
            fiber decimal(5,2) DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY date (date),
            KEY meal_type (meal_type)
        ) $charset_collate;";

        // Table: user_subscriptions
        $table_subscriptions = $wpdb->prefix . 'fittrack_subscriptions';
        $sql_subscriptions = "CREATE TABLE IF NOT EXISTS $table_subscriptions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            stripe_customer_id varchar(255) DEFAULT NULL,
            stripe_subscription_id varchar(255) DEFAULT NULL,
            plan varchar(50) NOT NULL DEFAULT 'free',
            status varchar(50) NOT NULL DEFAULT 'active',
            current_period_start datetime DEFAULT NULL,
            current_period_end datetime DEFAULT NULL,
            cancel_at datetime DEFAULT NULL,
            canceled_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id),
            KEY stripe_customer_id (stripe_customer_id),
            KEY stripe_subscription_id (stripe_subscription_id),
            KEY plan (plan),
            KEY status (status)
        ) $charset_collate;";

        // Table: user_goals
        $table_goals = $wpdb->prefix . 'fittrack_goals';
        $sql_goals = "CREATE TABLE IF NOT EXISTS $table_goals (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            goal_type varchar(50) NOT NULL COMMENT 'weight, calories, workout_frequency, etc',
            target_value decimal(10,2) NOT NULL,
            current_value decimal(10,2) DEFAULT NULL,
            start_date date NOT NULL,
            target_date date NOT NULL,
            status varchar(20) DEFAULT 'active',
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY goal_type (goal_type),
            KEY status (status)
        ) $charset_collate;";

        // Execute table creation
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql_progress);
        dbDelta($sql_workouts);
        dbDelta($sql_exercises);
        dbDelta($sql_nutrition);
        dbDelta($sql_subscriptions);
        dbDelta($sql_goals);
    }

    /**
     * Insert progress log
     */
    public function insert_progress($user_id, $data) {
        global $wpdb;

        $table = $wpdb->prefix . 'fittrack_progress_logs';

        return $wpdb->insert($table, array(
            'user_id' => $user_id,
            'date' => $data['date'],
            'weight' => isset($data['weight']) ? $data['weight'] : null,
            'body_fat' => isset($data['body_fat']) ? $data['body_fat'] : null,
            'muscle_mass' => isset($data['muscle_mass']) ? $data['muscle_mass'] : null,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
        ));
    }

    /**
     * Get progress logs
     */
    public function get_progress_logs($user_id, $limit = 30, $offset = 0) {
        global $wpdb;

        $table = $wpdb->prefix . 'fittrack_progress_logs';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d ORDER BY date DESC LIMIT %d OFFSET %d",
            $user_id,
            $limit,
            $offset
        ));
    }

    /**
     * Insert workout log
     */
    public function insert_workout_log($user_id, $data) {
        global $wpdb;

        $table = $wpdb->prefix . 'fittrack_workout_logs';

        return $wpdb->insert($table, array(
            'user_id' => $user_id,
            'workout_id' => $data['workout_id'],
            'date' => $data['date'],
            'duration' => isset($data['duration']) ? $data['duration'] : null,
            'calories_burned' => isset($data['calories_burned']) ? $data['calories_burned'] : null,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'status' => isset($data['status']) ? $data['status'] : 'completed',
        ));
    }

    /**
     * Insert nutrition log
     */
    public function insert_nutrition_log($user_id, $data) {
        global $wpdb;

        $table = $wpdb->prefix . 'fittrack_nutrition_logs';

        return $wpdb->insert($table, array(
            'user_id' => $user_id,
            'date' => $data['date'],
            'meal_type' => $data['meal_type'],
            'food_id' => isset($data['food_id']) ? $data['food_id'] : null,
            'food_name' => $data['food_name'],
            'quantity' => $data['quantity'],
            'unit' => isset($data['unit']) ? $data['unit'] : 'serving',
            'calories' => isset($data['calories']) ? $data['calories'] : null,
            'protein' => isset($data['protein']) ? $data['protein'] : null,
            'carbs' => isset($data['carbs']) ? $data['carbs'] : null,
            'fat' => isset($data['fat']) ? $data['fat'] : null,
            'fiber' => isset($data['fiber']) ? $data['fiber'] : null,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
        ));
    }

    /**
     * Get nutrition logs by date
     */
    public function get_nutrition_logs_by_date($user_id, $date) {
        global $wpdb;

        $table = $wpdb->prefix . 'fittrack_nutrition_logs';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND date = %s ORDER BY created_at ASC",
            $user_id,
            $date
        ));
    }

    /**
     * Get nutrition summary
     */
    public function get_nutrition_summary($user_id, $date) {
        global $wpdb;

        $table = $wpdb->prefix . 'fittrack_nutrition_logs';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT
                SUM(calories) as total_calories,
                SUM(protein) as total_protein,
                SUM(carbs) as total_carbs,
                SUM(fat) as total_fat,
                SUM(fiber) as total_fiber
            FROM $table
            WHERE user_id = %d AND date = %s",
            $user_id,
            $date
        ));
    }
}
