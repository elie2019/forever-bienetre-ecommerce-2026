<?php
/**
 * FitTrack Pro - Custom Post Types
 *
 * Register all custom post types for FitTrack Pro
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
 * Custom Post Types Class
 */
class FitTrack_CPT {

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
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
    }

    /**
     * Register custom post types
     */
    public function register_post_types() {
        // Workouts CPT
        $this->register_workouts();

        // Exercises CPT
        $this->register_exercises();

        // Meals CPT
        $this->register_meals();

        // Foods CPT
        $this->register_foods();

        // Goals CPT
        $this->register_goals();
    }

    /**
     * Register Workouts CPT
     */
    private function register_workouts() {
        $labels = array(
            'name'               => __('Workouts', 'forever-be-premium'),
            'singular_name'      => __('Workout', 'forever-be-premium'),
            'menu_name'          => __('Workouts', 'forever-be-premium'),
            'add_new'            => __('Add New', 'forever-be-premium'),
            'add_new_item'       => __('Add New Workout', 'forever-be-premium'),
            'edit_item'          => __('Edit Workout', 'forever-be-premium'),
            'new_item'           => __('New Workout', 'forever-be-premium'),
            'view_item'          => __('View Workout', 'forever-be-premium'),
            'search_items'       => __('Search Workouts', 'forever-be-premium'),
            'not_found'          => __('No workouts found', 'forever-be-premium'),
            'not_found_in_trash' => __('No workouts found in trash', 'forever-be-premium'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'fittrack-pro',
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'fittrack/workouts'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-heart',
            'supports'            => array('title', 'editor', 'author', 'thumbnail', 'custom-fields'),
        );

        register_post_type('fittrack_workout', $args);
    }

    /**
     * Register Exercises CPT
     */
    private function register_exercises() {
        $labels = array(
            'name'               => __('Exercises', 'forever-be-premium'),
            'singular_name'      => __('Exercise', 'forever-be-premium'),
            'menu_name'          => __('Exercises', 'forever-be-premium'),
            'add_new'            => __('Add New', 'forever-be-premium'),
            'add_new_item'       => __('Add New Exercise', 'forever-be-premium'),
            'edit_item'          => __('Edit Exercise', 'forever-be-premium'),
            'new_item'           => __('New Exercise', 'forever-be-premium'),
            'view_item'          => __('View Exercise', 'forever-be-premium'),
            'search_items'       => __('Search Exercises', 'forever-be-premium'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'fittrack-pro',
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'fittrack/exercises'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'supports'            => array('title', 'editor', 'thumbnail', 'custom-fields'),
        );

        register_post_type('fittrack_exercise', $args);
    }

    /**
     * Register Meals CPT
     */
    private function register_meals() {
        $labels = array(
            'name'               => __('Meals', 'forever-be-premium'),
            'singular_name'      => __('Meal', 'forever-be-premium'),
            'menu_name'          => __('Meals', 'forever-be-premium'),
            'add_new'            => __('Add New', 'forever-be-premium'),
            'add_new_item'       => __('Add New Meal', 'forever-be-premium'),
            'edit_item'          => __('Edit Meal', 'forever-be-premium'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'fittrack-pro',
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'fittrack/meals'),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array('title', 'author', 'custom-fields'),
        );

        register_post_type('fittrack_meal', $args);
    }

    /**
     * Register Foods CPT
     */
    private function register_foods() {
        $labels = array(
            'name'               => __('Foods', 'forever-be-premium'),
            'singular_name'      => __('Food', 'forever-be-premium'),
            'menu_name'          => __('Foods Database', 'forever-be-premium'),
            'add_new'            => __('Add New', 'forever-be-premium'),
            'add_new_item'       => __('Add New Food', 'forever-be-premium'),
            'edit_item'          => __('Edit Food', 'forever-be-premium'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'fittrack-pro',
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'fittrack/foods'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'supports'            => array('title', 'custom-fields'),
        );

        register_post_type('fittrack_food', $args);
    }

    /**
     * Register Goals CPT
     */
    private function register_goals() {
        $labels = array(
            'name'               => __('Goals', 'forever-be-premium'),
            'singular_name'      => __('Goal', 'forever-be-premium'),
            'menu_name'          => __('Goals', 'forever-be-premium'),
            'add_new'            => __('Add New', 'forever-be-premium'),
            'add_new_item'       => __('Add New Goal', 'forever-be-premium'),
            'edit_item'          => __('Edit Goal', 'forever-be-premium'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => 'fittrack-pro',
            'show_in_rest'        => true,
            'query_var'           => true,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array('title', 'author', 'custom-fields'),
        );

        register_post_type('fittrack_goal', $args);
    }

    /**
     * Register taxonomies
     */
    public function register_taxonomies() {
        // Exercise Category
        $this->register_exercise_category();

        // Workout Type
        $this->register_workout_type();

        // Food Category
        $this->register_food_category();
    }

    /**
     * Register Exercise Category taxonomy
     */
    private function register_exercise_category() {
        $labels = array(
            'name'              => __('Exercise Categories', 'forever-be-premium'),
            'singular_name'     => __('Exercise Category', 'forever-be-premium'),
            'search_items'      => __('Search Categories', 'forever-be-premium'),
            'all_items'         => __('All Categories', 'forever-be-premium'),
            'edit_item'         => __('Edit Category', 'forever-be-premium'),
            'update_item'       => __('Update Category', 'forever-be-premium'),
            'add_new_item'      => __('Add New Category', 'forever-be-premium'),
            'new_item_name'     => __('New Category Name', 'forever-be-premium'),
            'menu_name'         => __('Categories', 'forever-be-premium'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'exercise-category'),
        );

        register_taxonomy('exercise_category', array('fittrack_exercise'), $args);
    }

    /**
     * Register Workout Type taxonomy
     */
    private function register_workout_type() {
        $labels = array(
            'name'              => __('Workout Types', 'forever-be-premium'),
            'singular_name'     => __('Workout Type', 'forever-be-premium'),
            'search_items'      => __('Search Types', 'forever-be-premium'),
            'all_items'         => __('All Types', 'forever-be-premium'),
            'edit_item'         => __('Edit Type', 'forever-be-premium'),
            'update_item'       => __('Update Type', 'forever-be-premium'),
            'add_new_item'      => __('Add New Type', 'forever-be-premium'),
            'new_item_name'     => __('New Type Name', 'forever-be-premium'),
            'menu_name'         => __('Types', 'forever-be-premium'),
        );

        $args = array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'workout-type'),
        );

        register_taxonomy('workout_type', array('fittrack_workout'), $args);
    }

    /**
     * Register Food Category taxonomy
     */
    private function register_food_category() {
        $labels = array(
            'name'              => __('Food Categories', 'forever-be-premium'),
            'singular_name'     => __('Food Category', 'forever-be-premium'),
            'search_items'      => __('Search Categories', 'forever-be-premium'),
            'all_items'         => __('All Categories', 'forever-be-premium'),
            'edit_item'         => __('Edit Category', 'forever-be-premium'),
            'update_item'       => __('Update Category', 'forever-be-premium'),
            'add_new_item'      => __('Add New Category', 'forever-be-premium'),
            'new_item_name'     => __('New Category Name', 'forever-be-premium'),
            'menu_name'         => __('Categories', 'forever-be-premium'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'food-category'),
        );

        register_taxonomy('food_category', array('fittrack_food'), $args);
    }
}
