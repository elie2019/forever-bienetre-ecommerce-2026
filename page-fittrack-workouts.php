<?php
/**
 * Template Name: FitTrack Workouts
 * Description: Journal d'entraînement avec bibliothèque d'exercices et timer
 */

// Vérifier l'authentification
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Enqueue FitTrack styles
function fittrack_workouts_assets() {
    if (is_page_template('page-fittrack-workouts.php')) {
        wp_enqueue_style('fittrack-app-css', get_template_directory_uri() . '/assets/css/fittrack-app.css', array(), '1.0.0');

        wp_localize_script('jquery', 'fittrackWorkouts', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fittrack_workouts_nonce'),
            'userId' => get_current_user_id()
        ));
    }
}
add_action('wp_enqueue_scripts', 'fittrack_workouts_assets');

$user_id = get_current_user_id();

// Entraînement du jour (demo)
$today_workout = array(
    array('exercise' => 'Développé Couché', 'sets' => 4, 'reps' => 10, 'weight' => 80, 'rest' => 90, 'completed' => true),
    array('exercise' => 'Développé Incliné', 'sets' => 3, 'reps' => 12, 'weight' => 60, 'rest' => 60, 'completed' => true),
    array('exercise' => 'Écartés Haltères', 'sets' => 3, 'reps' => 15, 'weight' => 20, 'rest' => 60, 'completed' => false),
    array('exercise' => 'Pompes', 'sets' => 3, 'reps' => 20, 'weight' => 0, 'rest' => 45, 'completed' => false)
);

// Historique des entraînements (demo)
$workout_history = array(
    array('date' => 'Aujourd\'hui', 'name' => 'Pectoraux & Triceps', 'duration' => '45 min', 'exercises' => 6, 'calories' => 320, 'status' => 'in_progress'),
    array('date' => 'Hier', 'name' => 'Dos & Biceps', 'duration' => '50 min', 'exercises' => 7, 'calories' => 350, 'status' => 'completed'),
    array('date' => 'Il y a 2 jours', 'name' => 'Jambes', 'duration' => '60 min', 'exercises' => 8, 'calories' => 420, 'status' => 'completed'),
    array('date' => 'Il y a 3 jours', 'name' => 'Épaules & Abdos', 'duration' => '40 min', 'exercises' => 6, 'calories' => 280, 'status' => 'completed')
);

// Bibliothèque d'exercices (demo)
$exercise_library = array(
    'Pectoraux' => array(
        array('name' => 'Développé Couché', 'difficulty' => 'Intermédiaire', 'equipment' => 'Barre', 'muscles' => 'Pectoraux, Triceps'),
        array('name' => 'Développé Incliné', 'difficulty' => 'Intermédiaire', 'equipment' => 'Barre/Haltères', 'muscles' => 'Pectoraux (haut)'),
        array('name' => 'Pompes', 'difficulty' => 'Débutant', 'equipment' => 'Poids du corps', 'muscles' => 'Pectoraux, Triceps'),
        array('name' => 'Écartés Haltères', 'difficulty' => 'Débutant', 'equipment' => 'Haltères', 'muscles' => 'Pectoraux')
    ),
    'Dos' => array(
        array('name' => 'Tractions', 'difficulty' => 'Avancé', 'equipment' => 'Barre fixe', 'muscles' => 'Dorsaux, Biceps'),
        array('name' => 'Rowing Barre', 'difficulty' => 'Intermédiaire', 'equipment' => 'Barre', 'muscles' => 'Dorsaux'),
        array('name' => 'Tirage Vertical', 'difficulty' => 'Débutant', 'equipment' => 'Machine', 'muscles' => 'Dorsaux')
    ),
    'Jambes' => array(
        array('name' => 'Squat', 'difficulty' => 'Intermédiaire', 'equipment' => 'Barre', 'muscles' => 'Quadriceps, Fessiers'),
        array('name' => 'Presse à Cuisses', 'difficulty' => 'Débutant', 'equipment' => 'Machine', 'muscles' => 'Quadriceps, Fessiers'),
        array('name' => 'Soulevé de Terre', 'difficulty' => 'Avancé', 'equipment' => 'Barre', 'muscles' => 'Ischio, Dos, Fessiers')
    ),
    'Épaules' => array(
        array('name' => 'Développé Militaire', 'difficulty' => 'Intermédiaire', 'equipment' => 'Barre', 'muscles' => 'Deltoïdes'),
        array('name' => 'Élévations Latérales', 'difficulty' => 'Débutant', 'equipment' => 'Haltères', 'muscles' => 'Deltoïdes latéraux'),
        array('name' => 'Oiseau', 'difficulty' => 'Débutant', 'equipment' => 'Haltères', 'muscles' => 'Deltoïdes postérieurs')
    )
);

get_header();
?>

<div class="fittrack-wrapper">
    <!-- Header FitTrack -->
    <header class="fittrack-header">
        <div class="fittrack-header-icon">🏋️</div>
        <h1 class="fittrack-header-title">Journal d'Entraînement</h1>
        <p class="fittrack-header-subtitle">Planifiez et suivez vos séances d'entraînement avec précision.</p>
    </header>

    <div class="fittrack-container-narrow">

        <!-- Actions Rapides -->
        <div class="fittrack-grid fittrack-grid-3" style="margin-bottom: 30px;">
            <button class="fittrack-btn fittrack-btn-accent" style="width: 100%; justify-content: center; padding: 20px;" onclick="startNewWorkout()">
                <span style="font-size: 1.5rem;">➕</span>
                <span>Nouveau Workout</span>
            </button>
            <button class="fittrack-btn fittrack-btn-primary" style="width: 100%; justify-content: center; padding: 20px;" onclick="toggleTimer()">
                <span style="font-size: 1.5rem;">⏱️</span>
                <span id="timerButtonText">Démarrer Timer</span>
            </button>
            <button class="fittrack-btn fittrack-btn-outline" style="width: 100%; justify-content: center; padding: 20px;" onclick="showPrograms()">
                <span style="font-size: 1.5rem;">📚</span>
                <span>Programmes</span>
            </button>
        </div>

        <!-- Timer/Chronomètre -->
        <div class="fittrack-card" id="timerCard" style="margin-bottom: 30px; display: none; background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); color: var(--fittrack-white);">
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 4rem; font-weight: 700; font-family: 'Courier New', monospace; margin-bottom: 20px;" id="timerDisplay">
                    00:00
                </div>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button class="fittrack-btn fittrack-btn-accent" onclick="resetTimer()">Réinitialiser</button>
                    <button class="fittrack-btn" style="background: rgba(255,255,255,0.2); color: white;" onclick="toggleTimer()">Pause</button>
                </div>
            </div>
        </div>

        <!-- Entraînement du Jour -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">💪</span>
                    Entraînement du Jour - Pectoraux & Triceps
                </h2>
                <div class="fittrack-badge fittrack-badge-warning">En cours</div>
            </div>
            <div class="fittrack-card-body">
                <div style="margin-bottom: 20px; padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px;">
                    <div class="fittrack-flex-between" style="margin-bottom: 10px;">
                        <span style="font-size: 0.9rem; color: var(--fittrack-text-light);">Progression</span>
                        <span style="font-size: 0.9rem; font-weight: 600; color: var(--fittrack-accent);">2 / 4 exercices</span>
                    </div>
                    <div class="fittrack-progress" style="height: 12px;">
                        <div class="fittrack-progress-bar" style="width: 50%;"></div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($today_workout as $index => $exercise) : ?>
                        <div style="padding: 15px; background: <?php echo $exercise['completed'] ? '#d4edda' : 'var(--fittrack-bg-light)'; ?>; border-radius: 8px; border-left: 4px solid <?php echo $exercise['completed'] ? 'var(--fittrack-success)' : 'var(--fittrack-accent)'; ?>;">
                            <div class="fittrack-flex-between" style="margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if ($exercise['completed']) : ?>
                                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                                    <?php else : ?>
                                        <span style="color: var(--fittrack-text-light); font-size: 1.2rem;">○</span>
                                    <?php endif; ?>
                                    <span style="font-weight: 600; color: var(--fittrack-primary); font-size: 1.1rem;"><?php echo esc_html($exercise['exercise']); ?></span>
                                </div>
                                <?php if (!$exercise['completed']) : ?>
                                    <button class="fittrack-btn fittrack-btn-success" style="padding: 8px 16px; font-size: 0.85rem;" onclick="completeExercise(<?php echo $index; ?>)">
                                        Terminer
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; font-size: 0.85rem;">
                                <div>
                                    <span style="color: var(--fittrack-text-light);">Séries:</span>
                                    <span style="font-weight: 600; margin-left: 5px;"><?php echo $exercise['sets']; ?></span>
                                </div>
                                <div>
                                    <span style="color: var(--fittrack-text-light);">Reps:</span>
                                    <span style="font-weight: 600; margin-left: 5px;"><?php echo $exercise['reps']; ?></span>
                                </div>
                                <div>
                                    <span style="color: var(--fittrack-text-light);">Poids:</span>
                                    <span style="font-weight: 600; margin-left: 5px;"><?php echo $exercise['weight'] ? $exercise['weight'] . ' kg' : 'PDC'; ?></span>
                                </div>
                                <div>
                                    <span style="color: var(--fittrack-text-light);">Repos:</span>
                                    <span style="font-weight: 600; margin-left: 5px;"><?php echo $exercise['rest']; ?>s</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="fittrack-btn fittrack-btn-accent" style="width: 100%; margin-top: 20px; justify-content: center;" onclick="finishWorkout()">
                    🎉 Terminer l'Entraînement
                </button>
            </div>
        </div>

        <!-- Historique des Entraînements -->
        <div class="fittrack-card" style="margin-bottom: 30px;">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📊</span>
                    Historique
                </h2>
                <a href="#" style="color: var(--fittrack-accent); font-size: 0.9rem; text-decoration: none;">Tout voir →</a>
            </div>
            <div class="fittrack-card-body">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($workout_history as $workout) : ?>
                        <div style="padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px; transition: var(--fittrack-transition); cursor: pointer;" onmouseover="this.style.background='#e8e6e1'" onmouseout="this.style.background='var(--fittrack-bg-light)'">
                            <div class="fittrack-flex-between" style="margin-bottom: 8px;">
                                <div>
                                    <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 3px;"><?php echo esc_html($workout['name']); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--fittrack-text-light);"><?php echo esc_html($workout['date']); ?></div>
                                </div>
                                <?php if ($workout['status'] === 'completed') : ?>
                                    <div class="fittrack-badge fittrack-badge-success">Terminé</div>
                                <?php else : ?>
                                    <div class="fittrack-badge fittrack-badge-warning">En cours</div>
                                <?php endif; ?>
                            </div>
                            <div style="display: flex; gap: 20px; font-size: 0.85rem; color: var(--fittrack-text-light);">
                                <span>⏱️ <?php echo $workout['duration']; ?></span>
                                <span>💪 <?php echo $workout['exercises']; ?> exercices</span>
                                <span>🔥 <?php echo $workout['calories']; ?> cal</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Bibliothèque d'Exercices -->
        <div class="fittrack-card">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">📚</span>
                    Bibliothèque d'Exercices
                </h2>
            </div>
            <div class="fittrack-card-body">
                <div style="margin-bottom: 20px;">
                    <input type="text" class="fittrack-input" placeholder="🔍 Rechercher un exercice..." id="exerciseSearch" onkeyup="filterExercises()">
                </div>

                <div class="fittrack-tabs" style="margin-bottom: 20px;">
                    <?php $first = true; foreach (array_keys($exercise_library) as $category) : ?>
                        <button class="fittrack-tab <?php echo $first ? 'active' : ''; ?>" onclick="showCategory('<?php echo strtolower($category); ?>')">
                            <?php echo $category; ?>
                        </button>
                        <?php $first = false; ?>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($exercise_library as $category => $exercises) : ?>
                    <div class="exercise-category" id="category-<?php echo strtolower($category); ?>" style="<?php echo $category !== 'Pectoraux' ? 'display: none;' : ''; ?>">
                        <div style="display: grid; gap: 12px;">
                            <?php foreach ($exercises as $exercise) : ?>
                                <div class="exercise-item" style="padding: 15px; background: var(--fittrack-bg-light); border-radius: 8px; cursor: pointer; transition: var(--fittrack-transition);" onmouseover="this.style.background='#e8e6e1'" onmouseout="this.style.background='var(--fittrack-bg-light)'">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                        <div>
                                            <div style="font-weight: 600; color: var(--fittrack-primary); margin-bottom: 5px;"><?php echo esc_html($exercise['name']); ?></div>
                                            <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">
                                                <span>🎯 <?php echo $exercise['muscles']; ?></span>
                                            </div>
                                        </div>
                                        <div class="fittrack-badge <?php
                                            echo $exercise['difficulty'] === 'Débutant' ? 'fittrack-badge-success' :
                                                ($exercise['difficulty'] === 'Intermédiaire' ? 'fittrack-badge-warning' : 'fittrack-badge-danger');
                                        ?>">
                                            <?php echo $exercise['difficulty']; ?>
                                        </div>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--fittrack-text-light);">
                                        <span>🏋️ <?php echo $exercise['equipment']; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="fittrack-card" style="background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); color: var(--fittrack-white); text-align: center; margin-top: 30px;">
            <div style="font-size: 3rem; margin-bottom: 15px;">🎬</div>
            <h2 style="font-size: 2rem; margin-bottom: 15px; font-family: 'Playfair Display', serif;">Vidéos d'Instructions HD</h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 25px;">
                Accédez à plus de 1,000 exercices avec vidéos HD et instructions détaillées en passant au plan Pro.
            </p>
            <a href="<?php echo home_url('/fittrack-pricing'); ?>" class="fittrack-btn fittrack-btn-accent">
                Upgrader vers Pro →
            </a>
        </div>

    </div>
</div>

<script>
// Timer/Chronomètre
let timerInterval;
let timerSeconds = 0;
let timerRunning = false;

function toggleTimer() {
    const timerCard = document.getElementById('timerCard');
    const timerButton = document.getElementById('timerButtonText');

    if (!timerRunning) {
        timerCard.style.display = 'block';
        timerRunning = true;
        timerButton.textContent = 'Arrêter Timer';

        timerInterval = setInterval(() => {
            timerSeconds++;
            updateTimerDisplay();
        }, 1000);
    } else {
        clearInterval(timerInterval);
        timerRunning = false;
        timerButton.textContent = 'Reprendre';
    }
}

function resetTimer() {
    clearInterval(timerInterval);
    timerSeconds = 0;
    timerRunning = false;
    updateTimerDisplay();
    document.getElementById('timerButtonText').textContent = 'Démarrer Timer';
}

function updateTimerDisplay() {
    const minutes = Math.floor(timerSeconds / 60);
    const seconds = timerSeconds % 60;
    document.getElementById('timerDisplay').textContent =
        String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
}

// Filtrer exercices
function filterExercises() {
    const searchValue = document.getElementById('exerciseSearch').value.toLowerCase();
    const exerciseItems = document.querySelectorAll('.exercise-item');

    exerciseItems.forEach(item => {
        const exerciseName = item.querySelector('div > div > div').textContent.toLowerCase();
        if (exerciseName.includes(searchValue)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Changer de catégorie
function showCategory(category) {
    // Hide all categories
    document.querySelectorAll('.exercise-category').forEach(cat => {
        cat.style.display = 'none';
    });

    // Show selected category
    document.getElementById('category-' + category).style.display = 'block';

    // Update active tab
    document.querySelectorAll('.fittrack-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');
}

// Actions placeholders
function completeExercise(index) {
    alert('Exercice marqué comme terminé !');
    event.target.textContent = '✓';
    event.target.disabled = true;
}

function finishWorkout() {
    if (confirm('Êtes-vous sûr de vouloir terminer cet entraînement ?')) {
        alert('Félicitations ! Entraînement terminé.\n\n• Durée: 45 min\n• Calories brûlées: 320\n• Exercices: 4/4');
        window.location.href = '<?php echo home_url('/fittrack-dashboard'); ?>';
    }
}

function startNewWorkout() {
    alert('Fonctionnalité à venir !\n\nPermettra de :\n• Créer un workout personnalisé\n• Utiliser un programme pré-défini\n• Sélectionner des exercices de la bibliothèque');
}

function showPrograms() {
    alert('Programmes d\'entraînement :\n\n• Push Pull Legs (6j/sem)\n• Upper Lower (4j/sem)\n• Full Body (3j/sem)\n• HIIT Cardio\n• Force athlétique\n\nDisponible dans le plan Pro !');
}
</script>

<?php get_footer(); ?>
