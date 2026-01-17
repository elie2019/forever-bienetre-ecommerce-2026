<?php
/**
 * Template Name: FitTrack Settings
 * Description: Paramètres utilisateur, profil et préférences de l'application
 */

// Vérifier l'authentification
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Enqueue FitTrack styles
function fittrack_settings_assets() {
    if (is_page_template('page-fittrack-settings.php')) {
        wp_enqueue_style('fittrack-app-css', get_template_directory_uri() . '/assets/css/fittrack-app.css', array(), '1.0.0');

        wp_localize_script('jquery', 'fittrackSettings', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fittrack_settings_nonce'),
            'userId' => get_current_user_id()
        ));
    }
}
add_action('wp_enqueue_scripts', 'fittrack_settings_assets');

$user_id = get_current_user_id();
$user_info = get_userdata($user_id);

// Récupérer les préférences utilisateur (ou valeurs par défaut)
$user_settings = array(
    'units_weight' => get_user_meta($user_id, 'fittrack_units_weight', true) ?: 'kg',
    'units_height' => get_user_meta($user_id, 'fittrack_units_height', true) ?: 'cm',
    'units_distance' => get_user_meta($user_id, 'fittrack_units_distance', true) ?: 'km',
    'language' => get_user_meta($user_id, 'fittrack_language', true) ?: 'fr',
    'notifications_email' => get_user_meta($user_id, 'fittrack_notif_email', true) !== 'no',
    'notifications_push' => get_user_meta($user_id, 'fittrack_notif_push', true) !== 'no',
    'notifications_workout' => get_user_meta($user_id, 'fittrack_notif_workout', true) !== 'no',
    'notifications_nutrition' => get_user_meta($user_id, 'fittrack_notif_nutrition', true) !== 'no',
    'privacy_profile' => get_user_meta($user_id, 'fittrack_privacy_profile', true) ?: 'private',
    'privacy_progress' => get_user_meta($user_id, 'fittrack_privacy_progress', true) ?: 'private'
);

// Plan actuel
$current_plan = get_user_meta($user_id, 'fittrack_plan', true) ?: 'starter';
$plan_names = array(
    'starter' => array('name' => 'Starter', 'price' => '19€', 'icon' => '🌱'),
    'pro' => array('name' => 'Pro', 'price' => '39€', 'icon' => '🚀'),
    'premium' => array('name' => 'Premium', 'price' => '69€', 'icon' => '👑')
);

get_header();
?>

<div class="fittrack-wrapper">
    <!-- Header FitTrack -->
    <header class="fittrack-header">
        <div class="fittrack-header-icon">⚙️</div>
        <h1 class="fittrack-header-title">Paramètres</h1>
        <p class="fittrack-header-subtitle">Personnalisez votre expérience FitTrack Pro.</p>
    </header>

    <div class="fittrack-container-narrow">

        <!-- Tabs Navigation -->
        <div class="fittrack-tabs">
            <button class="fittrack-tab active" onclick="showTab('profile')">👤 Profil</button>
            <button class="fittrack-tab" onclick="showTab('preferences')">🎨 Préférences</button>
            <button class="fittrack-tab" onclick="showTab('notifications')">🔔 Notifications</button>
            <button class="fittrack-tab" onclick="showTab('privacy')">🔐 Confidentialité</button>
            <button class="fittrack-tab" onclick="showTab('subscription')">💳 Abonnement</button>
        </div>

        <!-- Tab: Profil -->
        <div class="tab-content" id="tab-profile">
            <div class="fittrack-card">
                <div class="fittrack-card-header">
                    <h2 class="fittrack-card-title">
                        <span class="fittrack-card-title-icon">👤</span>
                        Informations du Profil
                    </h2>
                </div>
                <div class="fittrack-card-body">
                    <form id="profileForm" onsubmit="saveProfile(event)">

                        <!-- Photo de Profil -->
                        <div style="text-align: center; margin-bottom: 30px;">
                            <div style="width: 120px; height: 120px; margin: 0 auto 15px; border-radius: 50%; background: linear-gradient(135deg, var(--fittrack-primary), var(--fittrack-secondary)); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white;">
                                <?php echo strtoupper(substr($user_info->display_name, 0, 1)); ?>
                            </div>
                            <button type="button" class="fittrack-btn fittrack-btn-outline" style="padding: 8px 20px;">
                                📷 Changer la Photo
                            </button>
                        </div>

                        <div class="fittrack-grid fittrack-grid-2">
                            <!-- Nom Complet -->
                            <div class="fittrack-form-group">
                                <label class="fittrack-label">Nom Complet</label>
                                <input type="text" class="fittrack-input" value="<?php echo esc_attr($user_info->display_name); ?>" placeholder="Votre nom">
                            </div>

                            <!-- Email -->
                            <div class="fittrack-form-group">
                                <label class="fittrack-label">Email</label>
                                <input type="email" class="fittrack-input" value="<?php echo esc_attr($user_info->user_email); ?>" placeholder="votre@email.com">
                            </div>

                            <!-- Date de Naissance -->
                            <div class="fittrack-form-group">
                                <label class="fittrack-label">Date de Naissance</label>
                                <input type="date" class="fittrack-input" value="1990-01-15">
                            </div>

                            <!-- Genre -->
                            <div class="fittrack-form-group">
                                <label class="fittrack-label">Genre</label>
                                <select class="fittrack-select">
                                    <option value="male">Homme</option>
                                    <option value="female">Femme</option>
                                    <option value="other">Autre</option>
                                    <option value="prefer-not">Préfère ne pas dire</option>
                                </select>
                            </div>

                            <!-- Taille -->
                            <div class="fittrack-form-group">
                                <label class="fittrack-label">Taille (cm)</label>
                                <input type="number" class="fittrack-input" value="175" placeholder="175">
                            </div>

                            <!-- Poids Actuel -->
                            <div class="fittrack-form-group">
                                <label class="fittrack-label">Poids Actuel (kg)</label>
                                <input type="number" class="fittrack-input" value="72.5" step="0.1" placeholder="72.5">
                            </div>
                        </div>

                        <!-- Bio -->
                        <div class="fittrack-form-group">
                            <label class="fittrack-label">Bio</label>
                            <textarea class="fittrack-textarea" placeholder="Parlez-nous de vos objectifs fitness...">En quête d'une transformation complète - perte de poids et gain musculaire.</textarea>
                        </div>

                        <button type="submit" class="fittrack-btn fittrack-btn-accent" style="width: 100%; justify-content: center;">
                            💾 Enregistrer les Modifications
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tab: Préférences -->
        <div class="tab-content" id="tab-preferences" style="display: none;">
            <div class="fittrack-card">
                <div class="fittrack-card-header">
                    <h2 class="fittrack-card-title">
                        <span class="fittrack-card-title-icon">🎨</span>
                        Préférences de l'Application
                    </h2>
                </div>
                <div class="fittrack-card-body">
                    <form id="preferencesForm" onsubmit="savePreferences(event)">

                        <!-- Unités de Mesure -->
                        <div style="margin-bottom: 30px;">
                            <h3 style="font-size: 1.1rem; color: var(--fittrack-primary); margin-bottom: 15px;">🔢 Unités de Mesure</h3>

                            <div class="fittrack-grid fittrack-grid-3">
                                <div class="fittrack-form-group">
                                    <label class="fittrack-label">Poids</label>
                                    <select class="fittrack-select">
                                        <option value="kg" <?php echo $user_settings['units_weight'] === 'kg' ? 'selected' : ''; ?>>Kilogrammes (kg)</option>
                                        <option value="lbs" <?php echo $user_settings['units_weight'] === 'lbs' ? 'selected' : ''; ?>>Livres (lbs)</option>
                                    </select>
                                </div>

                                <div class="fittrack-form-group">
                                    <label class="fittrack-label">Taille</label>
                                    <select class="fittrack-select">
                                        <option value="cm" <?php echo $user_settings['units_height'] === 'cm' ? 'selected' : ''; ?>>Centimètres (cm)</option>
                                        <option value="ft" <?php echo $user_settings['units_height'] === 'ft' ? 'selected' : ''; ?>>Pieds & Pouces (ft/in)</option>
                                    </select>
                                </div>

                                <div class="fittrack-form-group">
                                    <label class="fittrack-label">Distance</label>
                                    <select class="fittrack-select">
                                        <option value="km" <?php echo $user_settings['units_distance'] === 'km' ? 'selected' : ''; ?>>Kilomètres (km)</option>
                                        <option value="mi" <?php echo $user_settings['units_distance'] === 'mi' ? 'selected' : ''; ?>>Miles (mi)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Langue -->
                        <div style="margin-bottom: 30px;">
                            <h3 style="font-size: 1.1rem; color: var(--fittrack-primary); margin-bottom: 15px;">🌍 Langue</h3>
                            <div class="fittrack-form-group">
                                <label class="fittrack-label">Langue de l'Interface</label>
                                <select class="fittrack-select">
                                    <option value="fr" <?php echo $user_settings['language'] === 'fr' ? 'selected' : ''; ?>>🇫🇷 Français</option>
                                    <option value="en" <?php echo $user_settings['language'] === 'en' ? 'selected' : ''; ?>>🇬🇧 English</option>
                                    <option value="es" <?php echo $user_settings['language'] === 'es' ? 'selected' : ''; ?>>🇪🇸 Español</option>
                                    <option value="de" <?php echo $user_settings['language'] === 'de' ? 'selected' : ''; ?>>🇩🇪 Deutsch</option>
                                </select>
                            </div>
                        </div>

                        <!-- Format de Date -->
                        <div style="margin-bottom: 30px;">
                            <h3 style="font-size: 1.1rem; color: var(--fittrack-primary); margin-bottom: 15px;">📅 Format de Date</h3>
                            <div class="fittrack-grid fittrack-grid-2">
                                <div class="fittrack-form-group">
                                    <label class="fittrack-label">Format de Date</label>
                                    <select class="fittrack-select">
                                        <option value="dd/mm/yyyy">JJ/MM/AAAA</option>
                                        <option value="mm/dd/yyyy">MM/JJ/AAAA</option>
                                        <option value="yyyy-mm-dd">AAAA-MM-JJ</option>
                                    </select>
                                </div>

                                <div class="fittrack-form-group">
                                    <label class="fittrack-label">Format d'Heure</label>
                                    <select class="fittrack-select">
                                        <option value="24h">24 heures</option>
                                        <option value="12h">12 heures (AM/PM)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="fittrack-btn fittrack-btn-accent" style="width: 100%; justify-content: center;">
                            💾 Enregistrer les Préférences
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tab: Notifications -->
        <div class="tab-content" id="tab-notifications" style="display: none;">
            <div class="fittrack-card">
                <div class="fittrack-card-header">
                    <h2 class="fittrack-card-title">
                        <span class="fittrack-card-title-icon">🔔</span>
                        Paramètres de Notifications
                    </h2>
                </div>
                <div class="fittrack-card-body">
                    <form id="notificationsForm" onsubmit="saveNotifications(event)">

                        <!-- Notifications Générales -->
                        <div style="margin-bottom: 30px;">
                            <h3 style="font-size: 1.1rem; color: var(--fittrack-primary); margin-bottom: 15px;">📧 Canaux de Notification</h3>

                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                                    <div>
                                        <div style="font-weight: 600; color: var(--fittrack-primary);">Notifications Email</div>
                                        <div style="font-size: 0.85rem; color: var(--fittrack-text-light); margin-top: 3px;">Recevoir des mises à jour par email</div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" <?php echo $user_settings['notifications_email'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                                    <div>
                                        <div style="font-weight: 600; color: var(--fittrack-primary);">Notifications Push</div>
                                        <div style="font-size: 0.85rem; color: var(--fittrack-text-light); margin-top: 3px;">Recevoir des notifications sur mobile</div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" <?php echo $user_settings['notifications_push'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Types de Notifications -->
                        <div style="margin-bottom: 30px;">
                            <h3 style="font-size: 1.1rem; color: var(--fittrack-primary); margin-bottom: 15px;">📬 Types de Notifications</h3>

                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                                    <div>
                                        <div style="font-weight: 600; color: var(--fittrack-primary);">Rappels d'Entraînement</div>
                                        <div style="font-size: 0.85rem; color: var(--fittrack-text-light); margin-top: 3px;">Ne manquez jamais une séance</div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" <?php echo $user_settings['notifications_workout'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                                    <div>
                                        <div style="font-weight: 600; color: var(--fittrack-primary);">Rappels Nutrition</div>
                                        <div style="font-size: 0.85rem; color: var(--fittrack-text-light); margin-top: 3px;">Enregistrer vos repas quotidiens</div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" <?php echo $user_settings['notifications_nutrition'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                                    <div>
                                        <div style="font-weight: 600; color: var(--fittrack-primary);">Objectifs & Milestones</div>
                                        <div style="font-size: 0.85rem; color: var(--fittrack-text-light); margin-top: 3px;">Célébrer vos réussites</div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                                    <div>
                                        <div style="font-weight: 600; color: var(--fittrack-primary);">Rapports Hebdomadaires</div>
                                        <div style="font-size: 0.85rem; color: var(--fittrack-text-light); margin-top: 3px;">Résumé de votre semaine</div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="fittrack-btn fittrack-btn-accent" style="width: 100%; justify-content: center;">
                            💾 Enregistrer les Notifications
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tab: Confidentialité -->
        <div class="tab-content" id="tab-privacy" style="display: none;">
            <div class="fittrack-card">
                <div class="fittrack-card-header">
                    <h2 class="fittrack-card-title">
                        <span class="fittrack-card-title-icon">🔐</span>
                        Confidentialité & Sécurité
                    </h2>
                </div>
                <div class="fittrack-card-body">

                    <!-- Visibilité du Profil -->
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 1.1rem; color: var(--fittrack-primary); margin-bottom: 15px;">👁️ Visibilité du Profil</h3>

                        <div class="fittrack-form-group">
                            <label class="fittrack-label">Qui peut voir mon profil?</label>
                            <select class="fittrack-select">
                                <option value="private" <?php echo $user_settings['privacy_profile'] === 'private' ? 'selected' : ''; ?>>🔒 Privé (uniquement moi)</option>
                                <option value="friends" <?php echo $user_settings['privacy_profile'] === 'friends' ? 'selected' : ''; ?>>👥 Amis uniquement</option>
                                <option value="public" <?php echo $user_settings['privacy_profile'] === 'public' ? 'selected' : ''; ?>>🌍 Public</option>
                            </select>
                        </div>

                        <div class="fittrack-form-group">
                            <label class="fittrack-label">Qui peut voir mes progrès?</label>
                            <select class="fittrack-select">
                                <option value="private" <?php echo $user_settings['privacy_progress'] === 'private' ? 'selected' : ''; ?>>🔒 Privé (uniquement moi)</option>
                                <option value="friends" <?php echo $user_settings['privacy_progress'] === 'friends' ? 'selected' : ''; ?>>👥 Amis uniquement</option>
                                <option value="coach" <?php echo $user_settings['privacy_progress'] === 'coach' ? 'selected' : ''; ?>>🏋️ Mon coach uniquement</option>
                                <option value="public" <?php echo $user_settings['privacy_progress'] === 'public' ? 'selected' : ''; ?>>🌍 Public</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sécurité -->
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 1.1rem; color: var(--fittrack-primary); margin-bottom: 15px;">🔑 Sécurité du Compte</h3>

                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <button type="button" class="fittrack-btn fittrack-btn-outline" style="width: 100%; justify-content: space-between;">
                                <span>🔒 Changer le Mot de Passe</span>
                                <span>→</span>
                            </button>
                            <button type="button" class="fittrack-btn fittrack-btn-outline" style="width: 100%; justify-content: space-between;">
                                <span>📱 Authentification à Deux Facteurs</span>
                                <span class="fittrack-badge fittrack-badge-warning">Recommandé</span>
                            </button>
                            <button type="button" class="fittrack-btn fittrack-btn-outline" style="width: 100%; justify-content: space-between;">
                                <span>📋 Gérer les Sessions Actives</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>

                    <!-- Export & Suppression -->
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 1.1rem; color: var(--fittrack-primary); margin-bottom: 15px;">📊 Vos Données</h3>

                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <button type="button" class="fittrack-btn fittrack-btn-primary" style="width: 100%; justify-content: center;" onclick="exportData()">
                                📥 Télécharger Mes Données
                            </button>
                            <button type="button" class="fittrack-btn fittrack-btn-danger" style="width: 100%; justify-content: center;" onclick="deleteAccount()">
                                🗑️ Supprimer Mon Compte
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Tab: Abonnement -->
        <div class="tab-content" id="tab-subscription" style="display: none;">
            <div class="fittrack-card">
                <div class="fittrack-card-header">
                    <h2 class="fittrack-card-title">
                        <span class="fittrack-card-title-icon">💳</span>
                        Mon Abonnement
                    </h2>
                </div>
                <div class="fittrack-card-body">

                    <!-- Plan Actuel -->
                    <div style="padding: 25px; background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); color: var(--fittrack-white); border-radius: 12px; margin-bottom: 30px; text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 10px;"><?php echo $plan_names[$current_plan]['icon']; ?></div>
                        <div style="font-size: 1.5rem; font-weight: 700; margin-bottom: 5px;">Plan <?php echo $plan_names[$current_plan]['name']; ?></div>
                        <div style="font-size: 2rem; font-weight: 700; margin-bottom: 15px; opacity: 0.9;"><?php echo $plan_names[$current_plan]['price']; ?><span style="font-size: 1.2rem;">/mois</span></div>
                        <div style="font-size: 0.9rem; opacity: 0.8;">Prochaine facturation: 17 Février 2026</div>
                    </div>

                    <!-- Informations d'Abonnement -->
                    <div style="display: grid; gap: 12px; margin-bottom: 30px;">
                        <div class="fittrack-flex-between" style="padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                            <span style="color: var(--fittrack-text-light);">Statut</span>
                            <span class="fittrack-badge fittrack-badge-success">Actif</span>
                        </div>
                        <div class="fittrack-flex-between" style="padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                            <span style="color: var(--fittrack-text-light);">Date d'Inscription</span>
                            <span style="font-weight: 600;">15 Novembre 2025</span>
                        </div>
                        <div class="fittrack-flex-between" style="padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                            <span style="color: var(--fittrack-text-light);">Méthode de Paiement</span>
                            <span style="font-weight: 600;">💳 •••• 4242</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 30px;">
                        <a href="<?php echo home_url('/fittrack-pricing'); ?>" class="fittrack-btn fittrack-btn-accent" style="width: 100%; justify-content: center;">
                            ⬆️ Upgrader mon Plan
                        </a>
                        <button type="button" class="fittrack-btn fittrack-btn-outline" style="width: 100%; justify-content: center;" onclick="updatePayment()">
                            💳 Modifier le Moyen de Paiement
                        </button>
                        <button type="button" class="fittrack-btn fittrack-btn-outline" style="width: 100%; justify-content: center;" onclick="viewInvoices()">
                            📄 Voir mes Factures
                        </button>
                        <button type="button" class="fittrack-btn fittrack-btn-danger" style="width: 100%; justify-content: center;" onclick="cancelSubscription()">
                            ❌ Annuler l'Abonnement
                        </button>
                    </div>

                    <!-- Support -->
                    <div style="padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; margin-bottom: 10px;">💬</div>
                        <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;">Besoin d'aide?</div>
                        <div style="font-size: 0.9rem; color: var(--fittrack-text-light); margin-bottom: 15px;">Notre équipe de support est là pour vous aider</div>
                        <button type="button" class="fittrack-btn fittrack-btn-primary" onclick="contactSupport()">
                            Contacter le Support
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* Toggle Switch */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--fittrack-border);
    transition: 0.4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: var(--fittrack-accent);
}

input:checked + .slider:before {
    transform: translateX(26px);
}
</style>

<script>
// Tab Navigation
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });

    // Show selected tab
    document.getElementById('tab-' + tabName).style.display = 'block';

    // Update active tab button
    document.querySelectorAll('.fittrack-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Form Handlers
function saveProfile(event) {
    event.preventDefault();
    alert('✅ Profil enregistré avec succès !');
}

function savePreferences(event) {
    event.preventDefault();
    alert('✅ Préférences enregistrées avec succès !');
}

function saveNotifications(event) {
    event.preventDefault();
    alert('✅ Paramètres de notifications enregistrés !');
}

// Actions
function exportData() {
    alert('📥 Export de Données\n\nVotre fichier contenant toutes vos données FitTrack (workouts, nutrition, progrès, objectifs) sera téléchargé au format JSON.\n\nLancement du téléchargement...');
}

function deleteAccount() {
    if (confirm('⚠️ ATTENTION\n\nÊtes-vous sûr de vouloir supprimer votre compte?\n\nCette action est IRRÉVERSIBLE et supprimera:\n• Toutes vos données\n• Votre historique\n• Vos photos\n• Votre abonnement')) {
        alert('Compte supprimé. Nous sommes désolés de vous voir partir. 😢');
    }
}

function updatePayment() {
    alert('💳 Modifier le Moyen de Paiement\n\nRedirection vers le portail sécurisé Stripe...');
}

function viewInvoices() {
    alert('📄 Factures\n\nAffichage de vos 12 dernières factures...');
}

function cancelSubscription() {
    if (confirm('❌ Annuler l\'Abonnement\n\nÊtes-vous sûr de vouloir annuler votre abonnement?\n\nVous conserverez l\'accès jusqu\'à la fin de votre période de facturation actuelle (17 Fév 2026).')) {
        alert('Abonnement annulé. Nous espérons vous revoir bientôt ! 👋');
    }
}

function contactSupport() {
    alert('💬 Support FitTrack\n\nEmail: support@fittrack.com\nTéléphone: +33 1 23 45 67 89\n\nOuverture d\'un ticket de support...');
}
</script>

<?php get_footer(); ?>
