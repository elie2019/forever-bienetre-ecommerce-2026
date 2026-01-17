<?php
/**
 * FitTrack Data Synchronization System
 *
 * Ce fichier centralise toute la gestion des données FitTrack pour assurer
 * la cohérence et la synchronisation entre toutes les pages de l'application.
 *
 * @package FitTrack_Pro
 * @version 1.0.0
 */

// Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe principale de synchronisation des données FitTrack
 */
class FitTrack_Data_Sync {

    /**
     * Instance unique de la classe (Singleton)
     */
    private static $instance = null;

    /**
     * ID de l'utilisateur actuel
     */
    private $user_id;

    /**
     * Clé de cache transitoire
     */
    private $cache_key_prefix = 'fittrack_cache_';

    /**
     * Durée du cache (en secondes) - 1 heure
     */
    private $cache_duration = 3600;

    /**
     * Obtenir l'instance unique (Singleton)
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructeur privé (Singleton)
     */
    private function __construct() {
        $this->user_id = get_current_user_id();
        $this->init_hooks();
    }

    /**
     * Initialiser les hooks WordPress
     */
    private function init_hooks() {
        // AJAX handlers - Authentification (no_priv pour utilisateurs non connectés)
        add_action('wp_ajax_nopriv_fittrack_classic_login', array($this, 'ajax_classic_login'));
        add_action('wp_ajax_nopriv_fittrack_classic_register', array($this, 'ajax_classic_register'));
        add_action('wp_ajax_nopriv_fittrack_google_login', array($this, 'ajax_google_login'));
        add_action('wp_ajax_nopriv_fittrack_google_register', array($this, 'ajax_google_register'));

        // AJAX handlers - Application (utilisateurs connectés)
        add_action('wp_ajax_fittrack_update_weight', array($this, 'ajax_update_weight'));
        add_action('wp_ajax_fittrack_log_workout', array($this, 'ajax_log_workout'));
        add_action('wp_ajax_fittrack_log_meal', array($this, 'ajax_log_meal'));
        add_action('wp_ajax_fittrack_update_goal', array($this, 'ajax_update_goal'));
        add_action('wp_ajax_fittrack_update_settings', array($this, 'ajax_update_settings'));
        add_action('wp_ajax_fittrack_get_stats', array($this, 'ajax_get_stats'));

        // Hooks de nettoyage
        add_action('wp_login', array($this, 'clear_user_cache'), 10, 2);
        add_action('wp_logout', array($this, 'clear_user_cache'));
    }

    // =========================
    // MÉTHODES DE DONNÉES - PROFIL
    // =========================

    /**
     * Obtenir le profil utilisateur complet
     */
    public function get_user_profile($user_id = null) {
        $user_id = $user_id ?: $this->user_id;
        $cache_key = $this->cache_key_prefix . 'profile_' . $user_id;

        // Vérifier le cache
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }

        $user_info = get_userdata($user_id);

        $profile = array(
            'user_id' => $user_id,
            'display_name' => $user_info->display_name,
            'email' => $user_info->user_email,
            'birth_date' => get_user_meta($user_id, 'fittrack_birth_date', true) ?: '',
            'gender' => get_user_meta($user_id, 'fittrack_gender', true) ?: 'male',
            'height' => floatval(get_user_meta($user_id, 'fittrack_height', true)) ?: 175,
            'current_weight' => floatval(get_user_meta($user_id, 'fittrack_current_weight', true)) ?: 70,
            'goal_weight' => floatval(get_user_meta($user_id, 'fittrack_goal_weight', true)) ?: 68,
            'bio' => get_user_meta($user_id, 'fittrack_bio', true) ?: '',
            'avatar_url' => get_avatar_url($user_id, array('size' => 200)),
            'plan' => get_user_meta($user_id, 'fittrack_plan', true) ?: 'starter',
            'member_since' => get_user_meta($user_id, 'fittrack_member_since', true) ?: date('Y-m-d')
        );

        // Mettre en cache
        set_transient($cache_key, $profile, $this->cache_duration);

        return $profile;
    }

    /**
     * Mettre à jour le profil utilisateur
     */
    public function update_user_profile($data, $user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $allowed_fields = array(
            'birth_date', 'gender', 'height', 'current_weight',
            'goal_weight', 'bio', 'display_name'
        );

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                if ($key === 'display_name') {
                    wp_update_user(array('ID' => $user_id, 'display_name' => sanitize_text_field($value)));
                } else {
                    update_user_meta($user_id, 'fittrack_' . $key, sanitize_text_field($value));
                }
            }
        }

        // Invalider le cache
        $this->clear_user_cache($user_id);

        return true;
    }

    // =========================
    // MÉTHODES DE DONNÉES - NUTRITION
    // =========================

    /**
     * Obtenir le résumé nutritionnel du jour
     */
    public function get_nutrition_today($user_id = null) {
        $user_id = $user_id ?: $this->user_id;
        $today = date('Y-m-d');

        $nutrition_data = get_user_meta($user_id, 'fittrack_nutrition_' . $today, true);

        if (!$nutrition_data) {
            $nutrition_data = array(
                'date' => $today,
                'calories' => 0,
                'proteins' => 0,
                'carbs' => 0,
                'fats' => 0,
                'meals' => array(
                    'breakfast' => array(),
                    'lunch' => array(),
                    'dinner' => array(),
                    'snacks' => array()
                )
            );
        }

        return $nutrition_data;
    }

    /**
     * Enregistrer un repas
     */
    public function log_meal($meal_type, $food_data, $user_id = null) {
        $user_id = $user_id ?: $this->user_id;
        $today = date('Y-m-d');

        $nutrition_data = $this->get_nutrition_today($user_id);

        // Ajouter l'aliment
        $nutrition_data['meals'][$meal_type][] = array(
            'name' => sanitize_text_field($food_data['name']),
            'calories' => floatval($food_data['calories']),
            'proteins' => floatval($food_data['proteins']),
            'carbs' => floatval($food_data['carbs']),
            'fats' => floatval($food_data['fats']),
            'time' => current_time('H:i'),
            'timestamp' => current_time('timestamp')
        );

        // Mettre à jour les totaux
        $nutrition_data['calories'] += floatval($food_data['calories']);
        $nutrition_data['proteins'] += floatval($food_data['proteins']);
        $nutrition_data['carbs'] += floatval($food_data['carbs']);
        $nutrition_data['fats'] += floatval($food_data['fats']);

        // Sauvegarder
        update_user_meta($user_id, 'fittrack_nutrition_' . $today, $nutrition_data);

        // Mettre à jour les stats globales
        $this->update_global_stats($user_id);

        return $nutrition_data;
    }

    /**
     * Obtenir les objectifs nutritionnels
     */
    public function get_nutrition_goals($user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $goals = get_user_meta($user_id, 'fittrack_nutrition_goals', true);

        if (!$goals) {
            // Calculer des objectifs par défaut basés sur le profil
            $profile = $this->get_user_profile($user_id);
            $goals = $this->calculate_default_nutrition_goals($profile);
            update_user_meta($user_id, 'fittrack_nutrition_goals', $goals);
        }

        return $goals;
    }

    /**
     * Calculer les objectifs nutritionnels par défaut
     */
    private function calculate_default_nutrition_goals($profile) {
        // Formule simple basée sur le poids et l'objectif
        $weight = $profile['current_weight'];
        $goal_weight = $profile['goal_weight'];
        $is_cutting = $weight > $goal_weight;

        $calories = $is_cutting ? ($weight * 30) : ($weight * 35);
        $proteins = $weight * 2; // 2g par kg
        $fats = $weight * 1; // 1g par kg
        $carbs = ($calories - ($proteins * 4) - ($fats * 9)) / 4;

        return array(
            'calories' => round($calories),
            'proteins' => round($proteins),
            'carbs' => round($carbs),
            'fats' => round($fats)
        );
    }

    // =========================
    // MÉTHODES DE DONNÉES - ENTRAÎNEMENTS
    // =========================

    /**
     * Enregistrer un entraînement
     */
    public function log_workout($workout_data, $user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $workout = array(
            'date' => date('Y-m-d'),
            'timestamp' => current_time('timestamp'),
            'name' => sanitize_text_field($workout_data['name']),
            'duration' => intval($workout_data['duration']),
            'calories_burned' => intval($workout_data['calories_burned']),
            'exercises' => $workout_data['exercises'],
            'notes' => sanitize_textarea_field($workout_data['notes'] ?? '')
        );

        // Obtenir l'historique des workouts
        $workouts = get_user_meta($user_id, 'fittrack_workouts_history', true) ?: array();

        // Ajouter le nouveau workout
        array_unshift($workouts, $workout);

        // Garder seulement les 100 derniers
        $workouts = array_slice($workouts, 0, 100);

        // Sauvegarder
        update_user_meta($user_id, 'fittrack_workouts_history', $workouts);

        // Incrémenter le compteur total
        $total_workouts = intval(get_user_meta($user_id, 'fittrack_workouts_completed', true));
        update_user_meta($user_id, 'fittrack_workouts_completed', $total_workouts + 1);

        // Mettre à jour la série active
        $this->update_active_streak($user_id);

        // Mettre à jour les stats globales
        $this->update_global_stats($user_id);

        return $workout;
    }

    /**
     * Obtenir l'historique des entraînements
     */
    public function get_workouts_history($limit = 10, $user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $workouts = get_user_meta($user_id, 'fittrack_workouts_history', true) ?: array();

        return array_slice($workouts, 0, $limit);
    }

    // =========================
    // MÉTHODES DE DONNÉES - PROGRESSION
    // =========================

    /**
     * Enregistrer une mesure de poids
     */
    public function log_weight($weight, $body_fat = null, $notes = '', $user_id = null) {
        $user_id = $user_id ?: $this->user_id;
        $today = date('Y-m-d');

        $measurement = array(
            'date' => $today,
            'timestamp' => current_time('timestamp'),
            'weight' => floatval($weight),
            'body_fat' => $body_fat ? floatval($body_fat) : null,
            'notes' => sanitize_text_field($notes)
        );

        // Obtenir l'historique
        $measurements = get_user_meta($user_id, 'fittrack_weight_history', true) ?: array();

        // Ajouter la nouvelle mesure
        array_unshift($measurements, $measurement);

        // Garder seulement les 365 derniers jours
        $measurements = array_slice($measurements, 0, 365);

        // Sauvegarder
        update_user_meta($user_id, 'fittrack_weight_history', $measurements);

        // Mettre à jour le poids actuel
        update_user_meta($user_id, 'fittrack_current_weight', floatval($weight));

        // Mettre à jour les stats globales
        $this->update_global_stats($user_id);

        return $measurement;
    }

    /**
     * Obtenir l'historique du poids
     */
    public function get_weight_history($days = 30, $user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $measurements = get_user_meta($user_id, 'fittrack_weight_history', true) ?: array();

        return array_slice($measurements, 0, $days);
    }

    /**
     * Enregistrer des mesures corporelles
     */
    public function log_body_measurements($measurements, $user_id = null) {
        $user_id = $user_id ?: $this->user_id;
        $today = date('Y-m-d');

        $data = array(
            'date' => $today,
            'timestamp' => current_time('timestamp'),
            'waist' => floatval($measurements['waist'] ?? 0),
            'chest' => floatval($measurements['chest'] ?? 0),
            'arms' => floatval($measurements['arms'] ?? 0),
            'thighs' => floatval($measurements['thighs'] ?? 0),
            'hips' => floatval($measurements['hips'] ?? 0),
            'neck' => floatval($measurements['neck'] ?? 0)
        );

        // Sauvegarder les mesures actuelles
        update_user_meta($user_id, 'fittrack_body_measurements', $data);

        return $data;
    }

    // =========================
    // MÉTHODES DE DONNÉES - OBJECTIFS
    // =========================

    /**
     * Créer un nouvel objectif
     */
    public function create_goal($goal_data, $user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $goal = array(
            'id' => uniqid('goal_'),
            'title' => sanitize_text_field($goal_data['title']),
            'category' => sanitize_text_field($goal_data['category']),
            'icon' => sanitize_text_field($goal_data['icon'] ?? '🎯'),
            'start_date' => date('Y-m-d'),
            'target_date' => sanitize_text_field($goal_data['target_date']),
            'current' => floatval($goal_data['current']),
            'target' => floatval($goal_data['target']),
            'unit' => sanitize_text_field($goal_data['unit']),
            'status' => 'active',
            'reminders' => boolval($goal_data['reminders'] ?? false),
            'created_at' => current_time('timestamp')
        );

        // Obtenir les objectifs existants
        $goals = get_user_meta($user_id, 'fittrack_goals', true) ?: array();

        // Ajouter le nouvel objectif
        $goals[] = $goal;

        // Sauvegarder
        update_user_meta($user_id, 'fittrack_goals', $goals);

        return $goal;
    }

    /**
     * Obtenir tous les objectifs
     */
    public function get_goals($status = 'active', $user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $goals = get_user_meta($user_id, 'fittrack_goals', true) ?: array();

        if ($status !== 'all') {
            $goals = array_filter($goals, function($goal) use ($status) {
                return $goal['status'] === $status;
            });
        }

        return array_values($goals);
    }

    /**
     * Mettre à jour un objectif
     */
    public function update_goal($goal_id, $updates, $user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $goals = get_user_meta($user_id, 'fittrack_goals', true) ?: array();

        foreach ($goals as &$goal) {
            if ($goal['id'] === $goal_id) {
                $goal = array_merge($goal, $updates);

                // Calculer la progression
                if (isset($goal['current']) && isset($goal['target'])) {
                    $goal['progress'] = min(100, ($goal['current'] / $goal['target']) * 100);
                }

                break;
            }
        }

        update_user_meta($user_id, 'fittrack_goals', $goals);

        return true;
    }

    // =========================
    // MÉTHODES DE DONNÉES - STATISTIQUES GLOBALES
    // =========================

    /**
     * Obtenir les statistiques globales
     */
    public function get_global_stats($user_id = null) {
        $user_id = $user_id ?: $this->user_id;
        $cache_key = $this->cache_key_prefix . 'stats_' . $user_id;

        // Vérifier le cache
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }

        $stats = array(
            'workouts_completed' => intval(get_user_meta($user_id, 'fittrack_workouts_completed', true)),
            'active_streak' => intval(get_user_meta($user_id, 'fittrack_active_streak', true)),
            'total_calories_burned' => intval(get_user_meta($user_id, 'fittrack_total_calories_burned', true)),
            'current_weight' => floatval(get_user_meta($user_id, 'fittrack_current_weight', true)),
            'weight_goal' => floatval(get_user_meta($user_id, 'fittrack_goal_weight', true)),
            'calories_today' => $this->get_nutrition_today($user_id)['calories'],
            'plan' => get_user_meta($user_id, 'fittrack_plan', true) ?: 'starter',
            'member_since' => get_user_meta($user_id, 'fittrack_member_since', true) ?: date('Y-m-d')
        );

        // Mettre en cache
        set_transient($cache_key, $stats, $this->cache_duration);

        return $stats;
    }

    /**
     * Mettre à jour les statistiques globales
     */
    private function update_global_stats($user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        // Invalider le cache
        delete_transient($this->cache_key_prefix . 'stats_' . $user_id);

        // Les stats seront recalculées à la prochaine lecture
        return true;
    }

    /**
     * Mettre à jour la série active
     */
    private function update_active_streak($user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $today = date('Y-m-d');
        $last_workout_date = get_user_meta($user_id, 'fittrack_last_workout_date', true);

        $current_streak = intval(get_user_meta($user_id, 'fittrack_active_streak', true));

        if ($last_workout_date === $today) {
            // Déjà compté aujourd'hui
            return $current_streak;
        } elseif ($last_workout_date === date('Y-m-d', strtotime('-1 day'))) {
            // Série continue
            $current_streak++;
        } else {
            // Série rompue
            $current_streak = 1;
        }

        update_user_meta($user_id, 'fittrack_active_streak', $current_streak);
        update_user_meta($user_id, 'fittrack_last_workout_date', $today);

        return $current_streak;
    }

    // =========================
    // HANDLERS AJAX - AUTHENTIFICATION
    // =========================

    /**
     * AJAX: Connexion classique (email/password)
     */
    public function ajax_classic_login() {
        check_ajax_referer('fittrack_login_nonce', 'nonce');

        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) && $_POST['remember'] === '1';

        // Validation
        if (empty($email) || empty($password)) {
            wp_send_json_error(array(
                'message' => 'Veuillez remplir tous les champs'
            ));
        }

        // Tentative de connexion
        $credentials = array(
            'user_login' => $email,
            'user_password' => $password,
            'remember' => $remember
        );

        $user = wp_signon($credentials, is_ssl());

        if (is_wp_error($user)) {
            wp_send_json_error(array(
                'message' => 'Email ou mot de passe incorrect'
            ));
        }

        // Connexion réussie
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, $remember);

        wp_send_json_success(array(
            'message' => 'Connexion réussie !',
            'redirect_url' => home_url('/fittrack-dashboard')
        ));
    }

    /**
     * AJAX: Inscription classique
     */
    public function ajax_classic_register() {
        check_ajax_referer('fittrack_register_nonce', 'nonce');

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            wp_send_json_error(array(
                'message' => 'Veuillez remplir tous les champs'
            ));
        }

        if (!is_email($email)) {
            wp_send_json_error(array(
                'message' => 'Email invalide'
            ));
        }

        if (strlen($password) < 8) {
            wp_send_json_error(array(
                'message' => 'Le mot de passe doit contenir au moins 8 caractères'
            ));
        }

        // Vérifier si l'email existe déjà
        if (email_exists($email)) {
            wp_send_json_error(array(
                'message' => 'Cet email est déjà utilisé'
            ));
        }

        // Créer l'utilisateur
        $user_id = wp_insert_user(array(
            'user_login' => $email,
            'user_email' => $email,
            'user_pass' => $password,
            'display_name' => $name,
            'role' => 'subscriber'
        ));

        if (is_wp_error($user_id)) {
            wp_send_json_error(array(
                'message' => 'Erreur lors de la création du compte'
            ));
        }

        // Initialiser les métadonnées FitTrack
        update_user_meta($user_id, 'fittrack_signup_date', date('Y-m-d H:i:s'));
        update_user_meta($user_id, 'fittrack_plan', 'free');
        update_user_meta($user_id, 'fittrack_onboarding_completed', '0');

        // Connecter automatiquement l'utilisateur
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);

        wp_send_json_success(array(
            'message' => 'Compte créé avec succès !',
            'redirect_url' => home_url('/fittrack-dashboard')
        ));
    }

    /**
     * AJAX: Connexion Google OAuth
     */
    public function ajax_google_login() {
        check_ajax_referer('fittrack_login_nonce', 'nonce');

        $credential = sanitize_text_field($_POST['credential']);

        if (empty($credential)) {
            wp_send_json_error(array(
                'message' => 'Token Google invalide'
            ));
        }

        // Décoder le JWT Google
        $google_user = $this->verify_google_token($credential);

        if (!$google_user) {
            wp_send_json_error(array(
                'message' => 'Impossible de vérifier le token Google'
            ));
        }

        // Chercher l'utilisateur par email
        $user = get_user_by('email', $google_user['email']);

        if (!$user) {
            wp_send_json_error(array(
                'message' => 'Aucun compte trouvé avec cet email. Veuillez vous inscrire.'
            ));
        }

        // Connecter l'utilisateur
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);

        wp_send_json_success(array(
            'message' => 'Connexion Google réussie !',
            'redirect_url' => home_url('/fittrack-dashboard')
        ));
    }

    /**
     * AJAX: Inscription Google OAuth
     */
    public function ajax_google_register() {
        check_ajax_referer('fittrack_register_nonce', 'nonce');

        $credential = sanitize_text_field($_POST['credential']);

        if (empty($credential)) {
            wp_send_json_error(array(
                'message' => 'Token Google invalide'
            ));
        }

        // Décoder le JWT Google
        $google_user = $this->verify_google_token($credential);

        if (!$google_user) {
            wp_send_json_error(array(
                'message' => 'Impossible de vérifier le token Google'
            ));
        }

        // Vérifier si l'email existe déjà
        if (email_exists($google_user['email'])) {
            wp_send_json_error(array(
                'message' => 'Un compte existe déjà avec cet email. Veuillez vous connecter.'
            ));
        }

        // Créer l'utilisateur
        $user_id = wp_insert_user(array(
            'user_login' => $google_user['email'],
            'user_email' => $google_user['email'],
            'display_name' => $google_user['name'],
            'first_name' => $google_user['given_name'] ?? '',
            'last_name' => $google_user['family_name'] ?? '',
            'role' => 'subscriber'
        ));

        if (is_wp_error($user_id)) {
            wp_send_json_error(array(
                'message' => 'Erreur lors de la création du compte'
            ));
        }

        // Ajouter métadonnées Google
        update_user_meta($user_id, 'fittrack_signup_date', date('Y-m-d H:i:s'));
        update_user_meta($user_id, 'fittrack_signup_method', 'google');
        update_user_meta($user_id, 'fittrack_google_id', $google_user['sub']);
        update_user_meta($user_id, 'fittrack_plan', 'free');
        update_user_meta($user_id, 'fittrack_onboarding_completed', '0');

        // Sauvegarder la photo de profil Google
        if (!empty($google_user['picture'])) {
            update_user_meta($user_id, 'fittrack_profile_picture', $google_user['picture']);
        }

        // Connecter automatiquement l'utilisateur
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);

        wp_send_json_success(array(
            'message' => 'Compte créé avec succès !',
            'redirect_url' => home_url('/fittrack-dashboard')
        ));
    }

    /**
     * Vérifier et décoder le token Google JWT
     */
    private function verify_google_token($credential) {
        // Décoder le JWT sans vérification (pour développement)
        // En production, utiliser Google API Client Library pour vérifier la signature
        $parts = explode('.', $credential);

        if (count($parts) !== 3) {
            return false;
        }

        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        $user_data = json_decode($payload, true);

        if (!$user_data || !isset($user_data['email'])) {
            return false;
        }

        // Vérifier que le token vient bien de Google
        if (!isset($user_data['iss']) || !in_array($user_data['iss'], ['accounts.google.com', 'https://accounts.google.com'])) {
            return false;
        }

        // Vérifier que le token n'est pas expiré
        if (isset($user_data['exp']) && $user_data['exp'] < time()) {
            return false;
        }

        return $user_data;
    }

    // =========================
    // HANDLERS AJAX - APPLICATION
    // =========================

    /**
     * AJAX: Mettre à jour le poids
     */
    public function ajax_update_weight() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        $weight = floatval($_POST['weight']);
        $body_fat = isset($_POST['body_fat']) ? floatval($_POST['body_fat']) : null;
        $notes = sanitize_text_field($_POST['notes'] ?? '');

        $measurement = $this->log_weight($weight, $body_fat, $notes);

        wp_send_json_success(array(
            'measurement' => $measurement,
            'message' => 'Poids enregistré avec succès !'
        ));
    }

    /**
     * AJAX: Enregistrer un entraînement
     */
    public function ajax_log_workout() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        $workout_data = array(
            'name' => sanitize_text_field($_POST['name']),
            'duration' => intval($_POST['duration']),
            'calories_burned' => intval($_POST['calories_burned']),
            'exercises' => json_decode(stripslashes($_POST['exercises']), true),
            'notes' => sanitize_textarea_field($_POST['notes'] ?? '')
        );

        $workout = $this->log_workout($workout_data);

        wp_send_json_success(array(
            'workout' => $workout,
            'message' => 'Entraînement enregistré avec succès !'
        ));
    }

    /**
     * AJAX: Enregistrer un repas
     */
    public function ajax_log_meal() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        $meal_type = sanitize_text_field($_POST['meal_type']);
        $food_data = array(
            'name' => sanitize_text_field($_POST['food_name']),
            'calories' => floatval($_POST['calories']),
            'proteins' => floatval($_POST['proteins']),
            'carbs' => floatval($_POST['carbs']),
            'fats' => floatval($_POST['fats'])
        );

        $nutrition_data = $this->log_meal($meal_type, $food_data);

        wp_send_json_success(array(
            'nutrition' => $nutrition_data,
            'message' => 'Repas enregistré avec succès !'
        ));
    }

    /**
     * AJAX: Mettre à jour un objectif
     */
    public function ajax_update_goal() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        $goal_id = sanitize_text_field($_POST['goal_id']);
        $updates = array(
            'current' => floatval($_POST['current'])
        );

        $this->update_goal($goal_id, $updates);

        wp_send_json_success(array(
            'message' => 'Objectif mis à jour avec succès !'
        ));
    }

    /**
     * AJAX: Mettre à jour les paramètres
     */
    public function ajax_update_settings() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        $settings = json_decode(stripslashes($_POST['settings']), true);

        foreach ($settings as $key => $value) {
            update_user_meta($this->user_id, 'fittrack_' . $key, $value);
        }

        $this->clear_user_cache();

        wp_send_json_success(array(
            'message' => 'Paramètres enregistrés avec succès !'
        ));
    }

    /**
     * AJAX: Obtenir les statistiques
     */
    public function ajax_get_stats() {
        check_ajax_referer('fittrack_nonce', 'nonce');

        $stats = $this->get_global_stats();

        wp_send_json_success($stats);
    }

    // =========================
    // UTILITAIRES
    // =========================

    /**
     * Effacer le cache utilisateur
     */
    public function clear_user_cache($user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        delete_transient($this->cache_key_prefix . 'profile_' . $user_id);
        delete_transient($this->cache_key_prefix . 'stats_' . $user_id);
    }

    /**
     * Exporter toutes les données utilisateur
     */
    public function export_user_data($user_id = null) {
        $user_id = $user_id ?: $this->user_id;

        $data = array(
            'profile' => $this->get_user_profile($user_id),
            'stats' => $this->get_global_stats($user_id),
            'workouts' => $this->get_workouts_history(100, $user_id),
            'weight_history' => $this->get_weight_history(365, $user_id),
            'goals' => $this->get_goals('all', $user_id),
            'export_date' => date('Y-m-d H:i:s')
        );

        return $data;
    }
}

// Initialiser la classe
function fittrack_data_sync() {
    return FitTrack_Data_Sync::get_instance();
}

// Démarrer l'instance
fittrack_data_sync();
