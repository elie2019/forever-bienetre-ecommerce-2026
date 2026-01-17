<?php
/**
 * Template Name: FitTrack Goals
 * Description: Gestionnaire d'objectifs SMART avec suivi et système de récompenses
 */

// Vérifier l'authentification
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Enqueue FitTrack styles
function fittrack_goals_assets() {
    if (is_page_template('page-fittrack-goals.php')) {
        wp_enqueue_style('fittrack-app-css', get_template_directory_uri() . '/assets/css/fittrack-app.css', array(), '1.0.0');

        wp_localize_script('jquery', 'fittrackGoals', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fittrack_goals_nonce'),
            'userId' => get_current_user_id()
        ));
    }
}
add_action('wp_enqueue_scripts', 'fittrack_goals_assets');

$user_id = get_current_user_id();

// Objectifs actifs (demo)
$active_goals = array(
    array(
        'id' => 1,
        'title' => 'Atteindre 68kg',
        'category' => 'Perte de poids',
        'icon' => '⚖️',
        'start_date' => '15 Nov 2025',
        'target_date' => '15 Fév 2026',
        'current' => 72.5,
        'target' => 68,
        'unit' => 'kg',
        'progress' => 61,
        'status' => 'active',
        'reminders' => true
    ),
    array(
        'id' => 2,
        'title' => 'S\'entraîner 5x par semaine',
        'category' => 'Habitudes',
        'icon' => '💪',
        'start_date' => '01 Jan 2026',
        'target_date' => '31 Mars 2026',
        'current' => 4,
        'target' => 5,
        'unit' => 'séances/sem',
        'progress' => 80,
        'status' => 'active',
        'reminders' => true
    ),
    array(
        'id' => 3,
        'title' => 'Consommer 150g protéines/jour',
        'category' => 'Nutrition',
        'icon' => '🥩',
        'start_date' => '10 Jan 2026',
        'target_date' => '10 Avr 2026',
        'current' => 120,
        'target' => 150,
        'unit' => 'g/jour',
        'progress' => 80,
        'status' => 'active',
        'reminders' => false
    )
);

// Objectifs complétés (demo)
$completed_goals = array(
    array(
        'title' => 'Perdre 5kg',
        'category' => 'Perte de poids',
        'icon' => '🎯',
        'completed_date' => '20 Déc 2025',
        'duration' => '5 semaines'
    ),
    array(
        'title' => '30 jours consécutifs',
        'category' => 'Série active',
        'icon' => '🔥',
        'completed_date' => '05 Jan 2026',
        'duration' => '30 jours'
    )
);

// Badges débloqués (demo)
$badges = array(
    array('name' => 'Premier Pas', 'icon' => '👣', 'description' => 'Créer votre premier objectif', 'unlocked' => true),
    array('name' => 'Guerrier', 'icon' => '⚔️', 'description' => '10 entraînements complétés', 'unlocked' => true),
    array('name' => 'Marathonien', 'icon' => '🏃', 'description' => '30 jours de série active', 'unlocked' => true),
    array('name' => 'Champion', 'icon' => '🏆', 'description' => 'Atteindre votre objectif de poids', 'unlocked' => false),
    array('name' => 'Maître', 'icon' => '👑', 'description' => 'Compléter 5 objectifs', 'unlocked' => false),
    array('name' => 'Légende', 'icon' => '⭐', 'description' => '100 entraînements complétés', 'unlocked' => false)
);

get_header();
?>

<div class="fittrack-wrapper">
    <!-- Header FitTrack -->
    <header class="fittrack-header">
        <div class="fittrack-header-icon">🎯</div>
        <h1 class="fittrack-header-title">Gestion d'Objectifs</h1>
        <p class="fittrack-header-subtitle">Définissez et atteignez vos objectifs fitness avec notre système SMART.</p>
    </header>

    <div class="fittrack-container-narrow">

        <!-- Actions Rapides -->
        <div class="fittrack-grid fittrack-grid-2" style="margin-bottom: 30px;">
            <button class="fittrack-btn fittrack-btn-accent" style="width: 100%; justify-content: center; padding: 20px;" onclick="createGoal()">
                <span style="font-size: 1.5rem;">➕</span>
                <span>Créer un Objectif</span>
            </button>
            <button class="fittrack-btn fittrack-btn-outline" style="width: 100%; justify-content: center; padding: 20px;" onclick="showTemplates()">
                <span style="font-size: 1.5rem;">📋</span>
                <span>Modèles d'Objectifs</span>
            </button>
        </div>

        <!-- Vue d'Ensemble -->
        <div class="fittrack-grid fittrack-grid-3" style="margin-bottom: 30px;">
            <div class="fittrack-stat-card">
                <div class="fittrack-stat-icon">🎯</div>
                <div class="fittrack-stat-value"><?php echo count($active_goals); ?></div>
                <div class="fittrack-stat-label">Objectifs Actifs</div>
            </div>
            <div class="fittrack-stat-card">
                <div class="fittrack-stat-icon">✅</div>
                <div class="fittrack-stat-value"><?php echo count($completed_goals); ?></div>
                <div class="fittrack-stat-label">Objectifs Atteints</div>
            </div>
            <div class="fittrack-stat-card">
                <div class="fittrack-stat-icon">🏆</div>
                <div class="fittrack-stat-value"><?php echo count(array_filter($badges, fn($b) => $b['unlocked'])); ?></div>
                <div class="fittrack-stat-label">Badges Débloqués</div>
            </div>
        </div>

        <!-- Objectifs Actifs -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">🎯</span>
                    Objectifs Actifs
                </h2>
            </div>
            <div class="fittrack-card-body">
                <?php if (!empty($active_goals)) : ?>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php foreach ($active_goals as $goal) : ?>
                            <div style="padding: 20px; background: var(--fittrack-bg-light); border-radius: 12px; border-left: 4px solid var(--fittrack-accent);">
                                <!-- Header -->
                                <div class="fittrack-flex-between" style="margin-bottom: 15px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <span style="font-size: 2rem;"><?php echo $goal['icon']; ?></span>
                                        <div>
                                            <h3 style="font-size: 1.2rem; color: var(--fittrack-primary); margin-bottom: 3px;">
                                                <?php echo esc_html($goal['title']); ?>
                                            </h3>
                                            <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">
                                                <?php echo $goal['category']; ?> • Début: <?php echo $goal['start_date']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fittrack-badge fittrack-badge-success">Actif</div>
                                </div>

                                <!-- Progress -->
                                <div style="margin-bottom: 15px;">
                                    <div class="fittrack-flex-between" style="margin-bottom: 8px;">
                                        <span style="font-size: 0.9rem; color: var(--fittrack-text-light);">Progression</span>
                                        <span style="font-weight: 600; color: var(--fittrack-accent);">
                                            <?php echo $goal['current']; ?> / <?php echo $goal['target']; ?> <?php echo $goal['unit']; ?>
                                        </span>
                                    </div>
                                    <div class="fittrack-progress" style="height: 12px;">
                                        <div class="fittrack-progress-bar" style="width: <?php echo $goal['progress']; ?>%;"></div>
                                    </div>
                                    <div style="text-align: right; margin-top: 5px; font-size: 0.9rem; color: var(--fittrack-accent); font-weight: 600;">
                                        <?php echo $goal['progress']; ?>% complété
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="fittrack-flex-between">
                                    <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">
                                        <span>🎯 Objectif: <?php echo $goal['target_date']; ?></span>
                                        <?php if ($goal['reminders']) : ?>
                                            <span style="margin-left: 15px;">🔔 Rappels activés</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; gap: 10px;">
                                        <button class="fittrack-btn" style="padding: 8px 16px; font-size: 0.85rem; background: var(--fittrack-white); border: 1px solid var(--fittrack-border); color: var(--fittrack-primary);" onclick="editGoal(<?php echo $goal['id']; ?>)">
                                            ✏️ Modifier
                                        </button>
                                        <button class="fittrack-btn fittrack-btn-success" style="padding: 8px 16px; font-size: 0.85rem;" onclick="updateGoal(<?php echo $goal['id']; ?>)">
                                            📊 Mettre à jour
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="fittrack-empty-state">
                        <div class="fittrack-empty-icon">🎯</div>
                        <div class="fittrack-empty-title">Aucun objectif actif</div>
                        <p class="fittrack-empty-text">Créez votre premier objectif pour commencer votre transformation !</p>
                        <button class="fittrack-btn fittrack-btn-accent" onclick="createGoal()">
                            Créer un Objectif
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Objectifs Complétés -->
        <?php if (!empty($completed_goals)) : ?>
            <div class="fittrack-card" style="margin-bottom: 30px;">
                <div class="fittrack-card-header">
                    <h2 class="fittrack-card-title">
                        <span class="fittrack-card-title-icon">✅</span>
                        Objectifs Complétés
                    </h2>
                </div>
                <div class="fittrack-card-body">
                    <div style="display: grid; gap: 12px;">
                        <?php foreach ($completed_goals as $goal) : ?>
                            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #d4edda; border-radius: 8px; border-left: 4px solid var(--fittrack-success);">
                                <span style="font-size: 2rem;"><?php echo $goal['icon']; ?></span>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 3px;">
                                        <?php echo esc_html($goal['title']); ?>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">
                                        <?php echo $goal['category']; ?> • Complété le <?php echo $goal['completed_date']; ?> • <?php echo $goal['duration']; ?>
                                    </div>
                                </div>
                                <div class="fittrack-badge fittrack-badge-success">✓ Complété</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Système de Badges -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">🏆</span>
                    Badges & Récompenses
                </h2>
            </div>
            <div class="fittrack-card-body">
                <div class="fittrack-grid fittrack-grid-3">
                    <?php foreach ($badges as $badge) : ?>
                        <div style="text-align: center; padding: 20px; background: <?php echo $badge['unlocked'] ? 'linear-gradient(135deg, #fff9e6 0%, #fff 100%)' : 'var(--fittrack-bg-light)'; ?>; border-radius: 12px; border: 2px solid <?php echo $badge['unlocked'] ? 'var(--fittrack-accent)' : 'var(--fittrack-border)'; ?>; <?php echo !$badge['unlocked'] ? 'opacity: 0.5;' : ''; ?>">
                            <div style="font-size: 3rem; margin-bottom: 10px; filter: <?php echo !$badge['unlocked'] ? 'grayscale(1)' : 'none'; ?>;">
                                <?php echo $badge['icon']; ?>
                            </div>
                            <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;">
                                <?php echo esc_html($badge['name']); ?>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">
                                <?php echo esc_html($badge['description']); ?>
                            </div>
                            <?php if ($badge['unlocked']) : ?>
                                <div class="fittrack-badge fittrack-badge-success" style="margin-top: 10px;">Débloqué</div>
                            <?php else : ?>
                                <div class="fittrack-badge" style="margin-top: 10px; background: var(--fittrack-border); color: var(--fittrack-text-light);">Verrouillé</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Guide SMART -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📖</span>
                    Guide: Créer un Objectif SMART
                </h2>
            </div>
            <div class="fittrack-card-body">
                <div style="display: grid; gap: 15px;">
                    <div style="display: flex; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 2rem; flex-shrink: 0;">🎯</div>
                        <div>
                            <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;">
                                <strong>S</strong>pécifique
                            </div>
                            <div style="font-size: 0.9rem; color: var(--fittrack-text-light);">
                                Définissez clairement ce que vous voulez accomplir. Exemple: "Perdre 5kg" plutôt que "Perdre du poids".
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 2rem; flex-shrink: 0;">📊</div>
                        <div>
                            <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;">
                                <strong>M</strong>esurable
                            </div>
                            <div style="font-size: 0.9rem; color: var(--fittrack-text-light);">
                                Utilisez des chiffres précis pour pouvoir suivre votre progression. Exemple: "68kg" au lieu de "mince".
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 2rem; flex-shrink: 0;">✅</div>
                        <div>
                            <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;">
                                <strong>A</strong>tteignable
                            </div>
                            <div style="font-size: 0.9rem; color: var(--fittrack-text-light);">
                                Fixez des objectifs réalistes selon votre situation. Exemple: 0.5-1kg par semaine pour une perte de poids saine.
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 2rem; flex-shrink: 0;">🎯</div>
                        <div>
                            <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;">
                                <strong>R</strong>elevant
                            </div>
                            <div style="font-size: 0.9rem; color: var(--fittrack-text-light);">
                                Assurez-vous que l'objectif correspond à vos aspirations et votre style de vie.
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 2rem; flex-shrink: 0;">⏰</div>
                        <div>
                            <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;">
                                <strong>T</strong>emporel
                            </div>
                            <div style="font-size: 0.9rem; color: var(--fittrack-text-light);">
                                Fixez une date limite précise. Exemple: "Atteindre 68kg d'ici le 15 mars 2026".
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Citation Motivationnelle -->
        <div class="fittrack-card" style="background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); color: var(--fittrack-white); text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 15px;">💭</div>
            <h2 style="font-size: 2rem; margin-bottom: 15px; font-family: 'Playfair Display', serif; font-style: italic;">
                "Un objectif sans plan n'est qu'un souhait."
            </h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 25px;">
                Transformez vos souhaits en réalité avec FitTrack Pro.
            </p>
            <a href="<?php echo home_url('/fittrack-dashboard'); ?>" class="fittrack-btn fittrack-btn-accent">
                Retour au Dashboard →
            </a>
        </div>

    </div>
</div>

<script>
// Actions placeholders
function createGoal() {
    alert('Créer un Objectif SMART\n\nFormulaire permettra de :\n• Choisir une catégorie\n• Définir l\'objectif (nombre cible)\n• Fixer une date limite\n• Configurer des rappels\n• Ajouter des notes motivationnelles');
}

function editGoal(goalId) {
    alert('Modifier l\'objectif #' + goalId + '\n\nPermettra de :\n• Ajuster la cible\n• Modifier la date limite\n• Activer/désactiver les rappels\n• Archiver l\'objectif');
}

function updateGoal(goalId) {
    alert('Mettre à jour la progression de l\'objectif #' + goalId + '\n\nPermettra de :\n• Enregistrer les nouvelles valeurs\n• Ajouter une note\n• Voir l\'historique des mises à jour');
}

function showTemplates() {
    alert('Modèles d\'Objectifs Populaires :\n\n🏋️ Perte de poids (5kg en 10 semaines)\n💪 Gain musculaire (+3kg en 12 semaines)\n🏃 Endurance (Courir 5km sans pause)\n💧 Hydratation (2L d\'eau par jour)\n😴 Sommeil (8h par nuit)\n🥗 Nutrition (5 portions fruits/légumes/jour)');
}
</script>

<?php get_footer(); ?>
