<?php
/**
 * Template Name: FitTrack Progress
 * Description: Suivi de progression avec mesures corporelles et photos avant/après
 */

// Vérifier l'authentification
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Enqueue FitTrack styles et Chart.js
function fittrack_progress_assets() {
    if (is_page_template('page-fittrack-progress.php')) {
        wp_enqueue_style('fittrack-app-css', get_template_directory_uri() . '/assets/css/fittrack-app.css', array(), '1.0.0');
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true);

        wp_localize_script('jquery', 'fittrackProgress', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fittrack_progress_nonce'),
            'userId' => get_current_user_id()
        ));
    }
}
add_action('wp_enqueue_scripts', 'fittrack_progress_assets');

$user_id = get_current_user_id();

// Données de progression (demo)
$current_stats = array(
    'weight' => 72.5,
    'body_fat' => 15.2,
    'muscle_mass' => 61.5,
    'waist' => 82,
    'chest' => 102,
    'arms' => 38,
    'thighs' => 56
);

$start_stats = array(
    'weight' => 78.0,
    'body_fat' => 22.0,
    'muscle_mass' => 58.0,
    'waist' => 92,
    'chest' => 98,
    'arms' => 36,
    'thighs' => 60
);

// Historique des mesures (demo)
$measurements_history = array(
    array('date' => '17 Jan 2026', 'weight' => 72.5, 'body_fat' => 15.2, 'notes' => ''),
    array('date' => '10 Jan 2026', 'weight' => 72.8, 'body_fat' => 15.5, 'notes' => 'Semaine chargée'),
    array('date' => '03 Jan 2026', 'weight' => 73.2, 'body_fat' => 16.0, 'notes' => 'Après les fêtes'),
    array('date' => '27 Déc 2025', 'weight' => 73.5, 'body_fat' => 16.2, 'notes' => ''),
    array('date' => '20 Déc 2025', 'weight' => 74.0, 'body_fat' => 17.0, 'notes' => 'Bon rythme'),
    array('date' => '13 Déc 2025', 'weight' => 74.5, 'body_fat' => 17.5, 'notes' => ''),
    array('date' => '06 Déc 2025', 'weight' => 75.2, 'body_fat' => 18.2, 'notes' => ''),
    array('date' => '29 Nov 2025', 'weight' => 76.0, 'body_fat' => 19.0, 'notes' => 'Motivation +'),
    array('date' => '22 Nov 2025', 'weight' => 76.8, 'body_fat' => 20.0, 'notes' => ''),
    array('date' => '15 Nov 2025', 'weight' => 77.5, 'body_fat' => 21.0, 'notes' => ''),
    array('date' => '08 Nov 2025', 'weight' => 78.0, 'body_fat' => 22.0, 'notes' => 'Début du parcours')
);

// Photos avant/après (demo - placeholders)
$progress_photos = array(
    array('date' => 'Nov 2025', 'label' => 'Début', 'type' => 'before'),
    array('date' => 'Déc 2025', 'label' => '1 mois', 'type' => 'progress'),
    array('date' => 'Jan 2026', 'label' => '2 mois', 'type' => 'current')
);

get_header();
?>

<div class="fittrack-wrapper">
    <!-- Header FitTrack -->
    <header class="fittrack-header">
        <div class="fittrack-header-icon">📈</div>
        <h1 class="fittrack-header-title">Suivi de Progression</h1>
        <p class="fittrack-header-subtitle">Visualisez votre transformation et mesurez vos progrès.</p>
    </header>

    <div class="fittrack-container-narrow">

        <!-- Vue d'Ensemble de la Progression -->
        <div class="fittrack-card" style="margin-bottom: 30px; background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); color: var(--fittrack-white);">
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 1.2rem; margin-bottom: 10px; opacity: 0.9;">Perte de Poids Totale</div>
                <div style="font-size: 4rem; font-weight: 700; margin-bottom: 5px;">
                    -<?php echo number_format($start_stats['weight'] - $current_stats['weight'], 1); ?> kg
                </div>
                <div style="font-size: 1rem; opacity: 0.8;">en <?php echo count($measurements_history) - 1; ?> semaines</div>

                <div class="fittrack-grid fittrack-grid-3" style="margin-top: 30px; gap: 20px;">
                    <div style="background: rgba(255, 255, 255, 0.15); padding: 20px; border-radius: 12px;">
                        <div style="font-size: 2rem; margin-bottom: 5px;">⚖️</div>
                        <div style="font-size: 1.8rem; font-weight: 700;"><?php echo $current_stats['weight']; ?> kg</div>
                        <div style="font-size: 0.9rem; opacity: 0.8; margin-top: 5px;">Poids Actuel</div>
                    </div>
                    <div style="background: rgba(255, 255, 255, 0.15); padding: 20px; border-radius: 12px;">
                        <div style="font-size: 2rem; margin-bottom: 5px;">📊</div>
                        <div style="font-size: 1.8rem; font-weight: 700;"><?php echo $current_stats['body_fat']; ?>%</div>
                        <div style="font-size: 0.9rem; opacity: 0.8; margin-top: 5px;">Masse Grasse</div>
                    </div>
                    <div style="background: rgba(255, 255, 255, 0.15); padding: 20px; border-radius: 12px;">
                        <div style="font-size: 2rem; margin-bottom: 5px;">💪</div>
                        <div style="font-size: 1.8rem; font-weight: 700;"><?php echo $current_stats['muscle_mass']; ?> kg</div>
                        <div style="font-size: 0.9rem; opacity: 0.8; margin-top: 5px;">Masse Musculaire</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique d'Évolution -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📉</span>
                    Évolution du Poids
                </h2>
                <div class="fittrack-tabs" style="border: none; margin: 0;">
                    <button class="fittrack-tab active" onclick="switchProgressChart('weight')">Poids</button>
                    <button class="fittrack-tab" onclick="switchProgressChart('bodyfat')">% Graisse</button>
                </div>
            </div>
            <div class="fittrack-card-body">
                <canvas id="progressChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Mesures Corporelles -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📏</span>
                    Mesures Corporelles
                </h2>
                <button class="fittrack-btn fittrack-btn-accent" style="padding: 10px 20px;" onclick="addMeasurement()">
                    + Nouvelle Mesure
                </button>
            </div>
            <div class="fittrack-card-body">
                <div class="fittrack-grid fittrack-grid-2" style="gap: 20px;">

                    <!-- Taille -->
                    <div style="padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <div style="font-size: 1rem; color: var(--fittrack-text-light); margin-bottom: 5px;">Tour de Taille</div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--fittrack-primary);">
                                    <?php echo $current_stats['waist']; ?> <span style="font-size: 1.2rem;">cm</span>
                                </div>
                            </div>
                            <div style="font-size: 2.5rem;">📐</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: var(--fittrack-success); font-weight: 600;">-<?php echo $start_stats['waist'] - $current_stats['waist']; ?> cm</span>
                            <span style="font-size: 0.85rem; color: var(--fittrack-text-light);">depuis le début</span>
                        </div>
                    </div>

                    <!-- Poitrine -->
                    <div style="padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <div style="font-size: 1rem; color: var(--fittrack-text-light); margin-bottom: 5px;">Tour de Poitrine</div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--fittrack-primary);">
                                    <?php echo $current_stats['chest']; ?> <span style="font-size: 1.2rem;">cm</span>
                                </div>
                            </div>
                            <div style="font-size: 2.5rem;">💪</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: var(--fittrack-success); font-weight: 600;">+<?php echo $current_stats['chest'] - $start_stats['chest']; ?> cm</span>
                            <span style="font-size: 0.85rem; color: var(--fittrack-text-light);">depuis le début</span>
                        </div>
                    </div>

                    <!-- Bras -->
                    <div style="padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <div style="font-size: 1rem; color: var(--fittrack-text-light); margin-bottom: 5px;">Tour de Bras</div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--fittrack-primary);">
                                    <?php echo $current_stats['arms']; ?> <span style="font-size: 1.2rem;">cm</span>
                                </div>
                            </div>
                            <div style="font-size: 2.5rem;">💪</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: var(--fittrack-success); font-weight: 600;">+<?php echo $current_stats['arms'] - $start_stats['arms']; ?> cm</span>
                            <span style="font-size: 0.85rem; color: var(--fittrack-text-light);">depuis le début</span>
                        </div>
                    </div>

                    <!-- Cuisses -->
                    <div style="padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <div style="font-size: 1rem; color: var(--fittrack-text-light); margin-bottom: 5px;">Tour de Cuisse</div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--fittrack-primary);">
                                    <?php echo $current_stats['thighs']; ?> <span style="font-size: 1.2rem;">cm</span>
                                </div>
                            </div>
                            <div style="font-size: 2.5rem;">🦵</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="color: var(--fittrack-success); font-weight: 600;">-<?php echo $start_stats['thighs'] - $current_stats['thighs']; ?> cm</span>
                            <span style="font-size: 0.85rem; color: var(--fittrack-text-light);">depuis le début</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Historique des Mesures -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📋</span>
                    Historique des Mesures
                </h2>
            </div>
            <div class="fittrack-card-body">
                <div style="overflow-x: auto;">
                    <table class="fittrack-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Poids</th>
                                <th>% Graisse</th>
                                <th>Évolution</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($measurements_history, 0, 7) as $index => $measurement) : ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo $measurement['date']; ?></td>
                                    <td><?php echo $measurement['weight']; ?> kg</td>
                                    <td><?php echo $measurement['body_fat']; ?>%</td>
                                    <td>
                                        <?php if ($index < count($measurements_history) - 1) : ?>
                                            <?php
                                            $diff = $measurement['weight'] - $measurements_history[$index + 1]['weight'];
                                            $color = $diff < 0 ? 'var(--fittrack-success)' : 'var(--fittrack-danger)';
                                            $arrow = $diff < 0 ? '↓' : '↑';
                                            ?>
                                            <span style="color: <?php echo $color; ?>; font-weight: 600;">
                                                <?php echo $arrow; ?> <?php echo abs($diff); ?> kg
                                            </span>
                                        <?php else : ?>
                                            <span style="color: var(--fittrack-text-light);">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: var(--fittrack-text-light); font-size: 0.9rem;">
                                        <?php echo $measurement['notes'] ?: '-'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Photos Avant/Après -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📷</span>
                    Photos Avant/Après
                </h2>
                <button class="fittrack-btn fittrack-btn-accent" style="padding: 10px 20px;" onclick="uploadPhoto()">
                    + Ajouter Photo
                </button>
            </div>
            <div class="fittrack-card-body">
                <div class="fittrack-grid fittrack-grid-3">
                    <?php foreach ($progress_photos as $photo) : ?>
                        <div style="text-align: center;">
                            <div style="aspect-ratio: 3/4; background: var(--fittrack-bg-light); border-radius: 12px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; font-size: 4rem; border: 2px solid var(--fittrack-border);">
                                📷
                            </div>
                            <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;"><?php echo $photo['label']; ?></div>
                            <div style="font-size: 0.85rem; color: var(--fittrack-text-light);"><?php echo $photo['date']; ?></div>
                            <?php if ($photo['type'] === 'current') : ?>
                                <div class="fittrack-badge fittrack-badge-success" style="margin-top: 8px;">Actuelle</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 20px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px; text-align: center;">
                    <div style="font-size: 0.9rem; color: var(--fittrack-text-light); margin-bottom: 10px;">
                        💡 <strong>Astuce:</strong> Prenez vos photos dans les mêmes conditions (éclairage, angle, moment de la journée) pour une comparaison précise.
                    </div>
                </div>
            </div>
        </div>

        <!-- Milestones & Achievements -->
        <div class="fittrack-card">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">🏆</span>
                    Réalisations
                </h2>
            </div>
            <div class="fittrack-card-body">
                <div class="fittrack-grid fittrack-grid-2" style="gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 3rem;">🎯</div>
                        <div>
                            <div style="font-weight: 600; color: var(--fittrack-primary);">Premier Objectif Atteint</div>
                            <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">-5kg atteints en 4 semaines</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 3rem;">💪</div>
                        <div>
                            <div style="font-weight: 600; color: var(--fittrack-primary);">Gain Musculaire</div>
                            <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">+3.5kg de masse musculaire</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 3rem;">📏</div>
                        <div>
                            <div style="font-weight: 600; color: var(--fittrack-primary);">Tour de Taille</div>
                            <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">-10cm en 2 mois</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <div style="font-size: 3rem;">🔥</div>
                        <div>
                            <div style="font-weight: 600; color: var(--fittrack-primary);">Série Active</div>
                            <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">12 jours consécutifs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="fittrack-card" style="background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); color: var(--fittrack-white); text-align: center; margin-top: 30px;">
            <div style="font-size: 3rem; margin-bottom: 15px;">📊</div>
            <h2 style="font-size: 2rem; margin-bottom: 15px; font-family: 'Playfair Display', serif;">Rapports Avancés</h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 25px;">
                Débloquez des analyses approfondies avec graphiques personnalisables et exportation PDF.
            </p>
            <a href="<?php echo home_url('/fittrack-pricing'); ?>" class="fittrack-btn fittrack-btn-accent">
                Upgrader vers Pro →
            </a>
        </div>

    </div>
</div>

<script>
// Chart.js - Graphique de progression
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('progressChart');

    // Données pour les graphiques
    const weightData = {
        labels: <?php echo json_encode(array_reverse(array_column(array_slice($measurements_history, 0, 11), 'date'))); ?>,
        data: <?php echo json_encode(array_reverse(array_column(array_slice($measurements_history, 0, 11), 'weight'))); ?>
    };

    const bodyfatData = {
        labels: <?php echo json_encode(array_reverse(array_column(array_slice($measurements_history, 0, 11), 'date'))); ?>,
        data: <?php echo json_encode(array_reverse(array_column(array_slice($measurements_history, 0, 11), 'body_fat'))); ?>
    };

    let chart;

    function createChart(type) {
        const data = type === 'weight' ? weightData : bodyfatData;
        const label = type === 'weight' ? 'Poids (kg)' : 'Masse Grasse (%)';
        const color = type === 'weight' ? 'rgba(201, 169, 98, 1)' : 'rgba(255, 99, 132, 1)';
        const bgColor = type === 'weight' ? 'rgba(201, 169, 98, 0.1)' : 'rgba(255, 99, 132, 0.1)';

        if (chart) {
            chart.destroy();
        }

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: label,
                    data: data.data,
                    borderColor: color,
                    backgroundColor: bgColor,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: color,
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
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
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

    createChart('weight');

    window.switchProgressChart = function(type) {
        createChart(type);

        // Update active tab
        document.querySelectorAll('.fittrack-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.target.classList.add('active');
    };
});

// Actions placeholders
function addMeasurement() {
    alert('Ajouter une nouvelle mesure :\n\n• Poids\n• % de graisse corporelle\n• Tour de taille\n• Tour de poitrine\n• Tour de bras\n• Tour de cuisse\n• Notes');
}

function uploadPhoto() {
    alert('Fonctionnalité d\'upload de photos à venir !\n\nPermettra de :\n• Prendre/uploader des photos\n• Organiser en galerie privée\n• Comparer avant/après\n• Partager avec votre coach (Plan Premium)');
}
</script>

<?php get_footer(); ?>
