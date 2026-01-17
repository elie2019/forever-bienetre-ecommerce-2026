<?php
/**
 * Template Name: FitTrack Dashboard
 * Description: Tableau de bord principal FitTrack Pro avec statistiques et activités
 */

// Vérifier l'authentification
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Enqueue FitTrack styles et Chart.js
function fittrack_dashboard_assets() {
    if (is_page_template('page-fittrack-dashboard.php')) {
        wp_enqueue_style('fittrack-app-css', get_template_directory_uri() . '/assets/css/fittrack-app.css', array(), '1.0.0');
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true);

        wp_localize_script('jquery', 'fittrackDashboard', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fittrack_dashboard_nonce'),
            'userId' => get_current_user_id()
        ));
    }
}
add_action('wp_enqueue_scripts', 'fittrack_dashboard_assets');

// Récupérer les données utilisateur (demo data pour l'instant)
$user_id = get_current_user_id();
$user_info = get_userdata($user_id);

// Stats demo
$stats = array(
    'workouts_completed' => get_user_meta($user_id, 'fittrack_workouts_completed', true) ?: 47,
    'calories_tracked' => get_user_meta($user_id, 'fittrack_calories_today', true) ?: 1842,
    'current_weight' => get_user_meta($user_id, 'fittrack_current_weight', true) ?: 72.5,
    'weight_goal' => get_user_meta($user_id, 'fittrack_weight_goal', true) ?: 68.0,
    'active_streak' => get_user_meta($user_id, 'fittrack_active_streak', true) ?: 12,
    'total_calories_burned' => get_user_meta($user_id, 'fittrack_total_calories_burned', true) ?: 3247
);

// Activités récentes (demo data)
$recent_activities = array(
    array('type' => 'workout', 'title' => 'Entraînement Full Body', 'time' => 'Il y a 2 heures', 'icon' => '💪', 'details' => '45 min • 320 cal'),
    array('type' => 'meal', 'title' => 'Déjeuner enregistré', 'time' => 'Il y a 4 heures', 'icon' => '🥗', 'details' => '650 cal • Équilibré'),
    array('type' => 'weight', 'title' => 'Pesée matinale', 'time' => 'Il y a 8 heures', 'icon' => '⚖️', 'details' => '72.5 kg • -0.3 kg'),
    array('type' => 'goal', 'title' => 'Objectif atteint !', 'time' => 'Hier', 'icon' => '🎯', 'details' => '10,000 pas franchis'),
    array('type' => 'workout', 'title' => 'Cardio HIIT', 'time' => 'Hier', 'icon' => '🏃', 'details' => '30 min • 280 cal')
);

// Objectifs en cours (demo data)
$active_goals = array(
    array('title' => 'Perte de poids', 'current' => 72.5, 'target' => 68, 'unit' => 'kg', 'progress' => 60),
    array('title' => 'Entraînements/semaine', 'current' => 4, 'target' => 5, 'unit' => 'séances', 'progress' => 80),
    array('title' => 'Apport protéines', 'current' => 120, 'target' => 150, 'unit' => 'g/jour', 'progress' => 80)
);

get_header();
?>

<div class="fittrack-wrapper">
    <!-- Header FitTrack -->
    <header class="fittrack-header">
        <div class="fittrack-header-icon">📊</div>
        <h1 class="fittrack-header-title">Tableau de Bord</h1>
        <p class="fittrack-header-subtitle">Bienvenue <?php echo esc_html($user_info->display_name); ?> ! Voici votre progression aujourd'hui.</p>
    </header>

    <div class="fittrack-container">

        <!-- Stats Grid -->
        <div class="fittrack-grid fittrack-grid-4" style="margin-bottom: 40px;">

            <!-- Stat: Entraînements -->
            <div class="fittrack-stat-card">
                <div class="fittrack-stat-icon">💪</div>
                <div class="fittrack-stat-value"><?php echo $stats['workouts_completed']; ?></div>
                <div class="fittrack-stat-label">Entraînements</div>
            </div>

            <!-- Stat: Calories -->
            <div class="fittrack-stat-card">
                <div class="fittrack-stat-icon">🔥</div>
                <div class="fittrack-stat-value"><?php echo number_format($stats['calories_tracked'], 0, ',', ' '); ?></div>
                <div class="fittrack-stat-label">Calories Aujourd'hui</div>
            </div>

            <!-- Stat: Poids -->
            <div class="fittrack-stat-card">
                <div class="fittrack-stat-icon">⚖️</div>
                <div class="fittrack-stat-value"><?php echo $stats['current_weight']; ?> <span style="font-size: 1.2rem;">kg</span></div>
                <div class="fittrack-stat-label">Poids Actuel</div>
            </div>

            <!-- Stat: Streak -->
            <div class="fittrack-stat-card">
                <div class="fittrack-stat-icon">🔥</div>
                <div class="fittrack-stat-value"><?php echo $stats['active_streak']; ?> <span style="font-size: 1.2rem;">j</span></div>
                <div class="fittrack-stat-label">Série Active</div>
            </div>

        </div>

        <div class="fittrack-grid fittrack-grid-2">

            <!-- Section: Actions Rapides -->
            <div class="fittrack-card">
                <div class="fittrack-card-header">
                    <h2 class="fittrack-card-title">
                        <span class="fittrack-card-title-icon">⚡</span>
                        Actions Rapides
                    </h2>
                </div>
                <div class="fittrack-card-body">
                    <div style="display: grid; gap: 15px;">
                        <a href="<?php echo home_url('/fittrack-workouts'); ?>" class="fittrack-btn fittrack-btn-primary" style="width: 100%; justify-content: center;">
                            <span>💪</span>
                            <span>Enregistrer un Entraînement</span>
                        </a>
                        <a href="<?php echo home_url('/fittrack-nutrition'); ?>" class="fittrack-btn fittrack-btn-accent" style="width: 100%; justify-content: center;">
                            <span>🥗</span>
                            <span>Ajouter un Repas</span>
                        </a>
                        <a href="<?php echo home_url('/fittrack-progress'); ?>" class="fittrack-btn fittrack-btn-outline" style="width: 100%; justify-content: center;">
                            <span>⚖️</span>
                            <span>Mettre à Jour le Poids</span>
                        </a>
                        <a href="<?php echo home_url('/fittrack-goals'); ?>" class="fittrack-btn fittrack-btn-outline" style="width: 100%; justify-content: center;">
                            <span>🎯</span>
                            <span>Créer un Objectif</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Section: Activités Récentes -->
            <div class="fittrack-card">
                <div class="fittrack-card-header">
                    <h2 class="fittrack-card-title">
                        <span class="fittrack-card-title-icon">📋</span>
                        Activités Récentes
                    </h2>
                    <a href="#" style="color: var(--fittrack-accent); font-size: 0.9rem; text-decoration: none;">Tout voir →</a>
                </div>
                <div class="fittrack-card-body">
                    <?php if (!empty($recent_activities)) : ?>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php foreach ($recent_activities as $activity) : ?>
                                <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px; transition: var(--fittrack-transition);" onmouseover="this.style.background='#e8e6e1'" onmouseout="this.style.background='var(--fittrack-bg-light)'">
                                    <div style="font-size: 2rem; flex-shrink: 0;"><?php echo $activity['icon']; ?></div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 3px;"><?php echo esc_html($activity['title']); ?></div>
                                        <div style="font-size: 0.85rem; color: var(--fittrack-text-light);"><?php echo esc_html($activity['details']); ?></div>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--fittrack-text-light); white-space: nowrap;"><?php echo esc_html($activity['time']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="fittrack-empty-state" style="padding: 30px 20px;">
                            <div class="fittrack-empty-icon">📋</div>
                            <div class="fittrack-empty-title">Aucune activité récente</div>
                            <p class="fittrack-empty-text">Commencez à enregistrer vos entraînements et repas !</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Section: Progression Hebdomadaire -->
        <div class="fittrack-card" style="margin-top: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📈</span>
                    Progression Hebdomadaire
                </h2>
                <div class="fittrack-tabs" style="border: none; margin: 0;">
                    <button class="fittrack-tab active" onclick="switchChart('weight')">Poids</button>
                    <button class="fittrack-tab" onclick="switchChart('calories')">Calories</button>
                    <button class="fittrack-tab" onclick="switchChart('workouts')">Entraînements</button>
                </div>
            </div>
            <div class="fittrack-card-body">
                <canvas id="progressChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Section: Objectifs en Cours -->
        <div class="fittrack-card" style="margin-top: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">🎯</span>
                    Objectifs en Cours
                </h2>
                <a href="<?php echo home_url('/fittrack-goals'); ?>" style="color: var(--fittrack-accent); font-size: 0.9rem; text-decoration: none;">Gérer →</a>
            </div>
            <div class="fittrack-card-body">
                <div style="display: grid; gap: 25px;">
                    <?php foreach ($active_goals as $goal) : ?>
                        <div>
                            <div class="fittrack-flex-between" style="margin-bottom: 10px;">
                                <span style="font-weight: 600; color: var(--fittrack-primary);"><?php echo esc_html($goal['title']); ?></span>
                                <span style="font-size: 0.9rem; color: var(--fittrack-text-light);">
                                    <?php echo $goal['current']; ?> / <?php echo $goal['target']; ?> <?php echo $goal['unit']; ?>
                                </span>
                            </div>
                            <div class="fittrack-progress">
                                <div class="fittrack-progress-bar" style="width: <?php echo $goal['progress']; ?>%;"></div>
                            </div>
                            <div style="text-align: right; margin-top: 5px; font-size: 0.85rem; color: var(--fittrack-accent); font-weight: 600;">
                                <?php echo $goal['progress']; ?>%
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Section: Message Motivationnel -->
        <div class="fittrack-card" style="background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); color: var(--fittrack-white); text-align: center; margin-top: 30px;">
            <div style="font-size: 3rem; margin-bottom: 15px;">🌟</div>
            <h2 style="font-size: 2rem; margin-bottom: 15px; font-family: 'Playfair Display', serif;">Excellente Progression !</h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 25px;">
                Vous êtes sur la bonne voie ! Continuez comme ça et vous atteindrez vos objectifs en un rien de temps.
            </p>
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="<?php echo home_url('/fittrack-pricing'); ?>" class="fittrack-btn fittrack-btn-accent">
                    Upgrader mon Plan →
                </a>
                <a href="<?php echo home_url('/fittrack-nutrition'); ?>" class="fittrack-btn" style="background: rgba(255,255,255,0.2); color: white;">
                    Découvrir la Nutrition
                </a>
            </div>
        </div>

    </div>
</div>

<script>
// Chart.js - Graphique de progression
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('progressChart');

    // Données demo pour les 7 derniers jours
    const chartData = {
        weight: {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            data: [73.2, 73.0, 72.8, 72.9, 72.7, 72.6, 72.5],
            label: 'Poids (kg)',
            color: 'rgba(201, 169, 98, 1)',
            bgColor: 'rgba(201, 169, 98, 0.1)'
        },
        calories: {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            data: [1820, 1950, 1780, 1900, 1850, 2100, 1842],
            label: 'Calories consommées',
            color: 'rgba(255, 99, 132, 1)',
            bgColor: 'rgba(255, 99, 132, 0.1)'
        },
        workouts: {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            data: [1, 0, 1, 1, 0, 1, 1],
            label: 'Entraînements',
            color: 'rgba(75, 192, 192, 1)',
            bgColor: 'rgba(75, 192, 192, 0.1)'
        }
    };

    let currentChart = 'weight';
    let chart;

    function createChart(type) {
        const data = chartData[type];

        if (chart) {
            chart.destroy();
        }

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: data.label,
                    data: data.data,
                    borderColor: data.color,
                    backgroundColor: data.bgColor,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: data.color,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26, 26, 46, 0.9)',
                        titleColor: '#c9a962',
                        bodyColor: '#fff',
                        padding: 12,
                        borderColor: '#c9a962',
                        borderWidth: 1,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + (type === 'weight' ? ' kg' : type === 'calories' ? ' cal' : ' séance(s)');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: type !== 'weight',
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#666'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666'
                        }
                    }
                }
            }
        });
    }

    // Initialiser avec le graphique "poids"
    createChart('weight');

    // Fonction pour changer de graphique
    window.switchChart = function(type) {
        currentChart = type;
        createChart(type);

        // Update active tab
        document.querySelectorAll('.fittrack-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.target.classList.add('active');
    };
});
</script>

<?php get_footer(); ?>
