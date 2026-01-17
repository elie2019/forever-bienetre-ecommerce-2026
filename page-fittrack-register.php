<?php
/**
 * Template Name: FitTrack Register
 * Description: Page d'inscription FitTrack Pro avec Google OAuth
 */

// Rediriger si déjà connecté
if (is_user_logged_in()) {
    wp_redirect(home_url('/fittrack-dashboard'));
    exit;
}

// Enqueue FitTrack styles
function fittrack_register_assets() {
    if (is_page_template('page-fittrack-register.php')) {
        wp_enqueue_style('fittrack-app-css', get_template_directory_uri() . '/assets/css/fittrack-app.css', array(), '1.0.0');

        // Google OAuth SDK
        wp_enqueue_script('google-platform', 'https://accounts.google.com/gsi/client', array(), null, false);

        wp_localize_script('jquery', 'fittrackRegister', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fittrack_register_nonce'),
            'redirectUrl' => home_url('/fittrack-dashboard'),
            'googleClientId' => get_option('fittrack_google_client_id', '')
        ));
    }
}
add_action('wp_enqueue_scripts', 'fittrack_register_assets');

get_header();
?>

<div class="fittrack-wrapper" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); padding: 40px 20px;">
    <div style="width: 100%; max-width: 500px;">

        <!-- Logo & Title -->
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="font-size: 4rem; margin-bottom: 15px;">💪</div>
            <h1 style="font-size: 2.5rem; color: white; font-family: 'Playfair Display', serif; margin-bottom: 10px;">FitTrack Pro</h1>
            <p style="color: rgba(255,255,255,0.8); font-size: 1.1rem;">Créez votre compte et commencez votre transformation</p>
        </div>

        <!-- Register Card -->
        <div class="fittrack-card" style="padding: 40px;">

            <!-- Messages d'erreur/succès -->
            <div id="register-messages"></div>

            <!-- Google Sign Up Button -->
            <div style="margin-bottom: 30px;">
                <div id="g_id_onload"
                     data-client_id="<?php echo esc_attr(get_option('fittrack_google_client_id', '')); ?>"
                     data-callback="handleGoogleRegister"
                     data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="rectangular"
                     data-theme="outline"
                     data-text="signup_with"
                     data-size="large"
                     data-logo_alignment="left"
                     data-width="100%">
                </div>
            </div>

            <!-- Divider -->
            <div style="position: relative; text-align: center; margin: 30px 0;">
                <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: var(--fittrack-border);"></div>
                <span style="position: relative; background: white; padding: 0 15px; color: var(--fittrack-text-light); font-size: 0.9rem;">OU</span>
            </div>

            <!-- Classic Registration Form -->
            <form id="fittrack-register-form" onsubmit="handleClassicRegister(event)">

                <div class="fittrack-form-group">
                    <label class="fittrack-label">Nom Complet *</label>
                    <input type="text" id="register-name" class="fittrack-input" placeholder="Votre nom" required>
                </div>

                <div class="fittrack-form-group">
                    <label class="fittrack-label">Email *</label>
                    <input type="email" id="register-email" class="fittrack-input" placeholder="votre@email.com" required>
                </div>

                <div class="fittrack-form-group">
                    <label class="fittrack-label">Mot de Passe *</label>
                    <input type="password" id="register-password" class="fittrack-input" placeholder="Minimum 8 caractères" required minlength="8">
                    <div style="margin-top: 8px; font-size: 0.85rem; color: var(--fittrack-text-light);">
                        Le mot de passe doit contenir au moins 8 caractères
                    </div>
                </div>

                <div class="fittrack-form-group">
                    <label class="fittrack-label">Confirmer le Mot de Passe *</label>
                    <input type="password" id="register-password-confirm" class="fittrack-input" placeholder="Répétez le mot de passe" required minlength="8">
                </div>

                <!-- Terms & Conditions -->
                <div style="margin-bottom: 25px;">
                    <label style="display: flex; align-items: start; gap: 10px; font-size: 0.9rem; cursor: pointer;">
                        <input type="checkbox" id="accept-terms" required style="width: 18px; height: 18px; margin-top: 2px; flex-shrink: 0;">
                        <span style="color: var(--fittrack-text-light);">
                            J'accepte les <a href="<?php echo home_url('/mentions-legales'); ?>" target="_blank" style="color: var(--fittrack-accent); text-decoration: none;">Conditions d'Utilisation</a> et la <a href="<?php echo home_url('/politique-confidentialite'); ?>" target="_blank" style="color: var(--fittrack-accent); text-decoration: none;">Politique de Confidentialité</a>
                        </span>
                    </label>
                </div>

                <button type="submit" class="fittrack-btn fittrack-btn-accent" style="width: 100%; justify-content: center; padding: 16px;" id="register-submit-btn">
                    <span id="register-btn-text">Créer mon compte</span>
                    <span id="register-btn-loader" style="display: none;">
                        <svg style="animation: spin 1s linear infinite; width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path>
                        </svg>
                    </span>
                </button>

            </form>

            <!-- Login Link -->
            <div style="margin-top: 25px; text-align: center; padding-top: 25px; border-top: 1px solid var(--fittrack-border);">
                <span style="color: var(--fittrack-text-light); font-size: 0.9rem;">Vous avez déjà un compte?</span>
                <a href="<?php echo home_url('/fittrack-login'); ?>" style="color: var(--fittrack-accent); font-weight: 600; text-decoration: none; margin-left: 5px;">
                    Se connecter →
                </a>
            </div>

        </div>

        <!-- Benefits -->
        <div class="fittrack-card" style="margin-top: 20px; padding: 30px; background: rgba(255,255,255,0.95);">
            <h3 style="font-size: 1.2rem; color: var(--fittrack-primary); margin-bottom: 20px; text-align: center;">✨ Inclus dans tous les plans</h3>
            <div style="display: grid; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.5rem;">📊</span>
                    <span style="color: var(--fittrack-text);">Tableau de bord personnalisé</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.5rem;">🥗</span>
                    <span style="color: var(--fittrack-text);">Suivi nutritionnel complet</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.5rem;">🏋️</span>
                    <span style="color: var(--fittrack-text);">Journal d'entraînement</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.5rem;">📈</span>
                    <span style="color: var(--fittrack-text);">Graphiques de progression</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.5rem;">🎯</span>
                    <span style="color: var(--fittrack-text);">Système d'objectifs SMART</span>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div style="text-align: center; margin-top: 25px;">
            <a href="<?php echo home_url(); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.9rem; transition: all 0.3s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.8)'">
                ← Retour à l'accueil
            </a>
        </div>

    </div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
// Google OAuth Callback
function handleGoogleRegister(response) {
    console.log('Google register response:', response);

    // Afficher loader
    showMessage('info', 'Création du compte en cours...');

    // Envoyer le token à WordPress
    fetch(fittrackRegister.ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'fittrack_google_register',
            nonce: fittrackRegister.nonce,
            credential: response.credential
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showMessage('success', 'Compte créé avec succès! Redirection...');
            setTimeout(() => {
                window.location.href = fittrackRegister.redirectUrl;
            }, 1500);
        } else {
            showMessage('error', data.data.message || 'Erreur lors de l\'inscription avec Google');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'Une erreur est survenue lors de l\'inscription');
    });
}

// Classic Registration Handler
function handleClassicRegister(event) {
    event.preventDefault();

    const name = document.getElementById('register-name').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;
    const passwordConfirm = document.getElementById('register-password-confirm').value;
    const acceptTerms = document.getElementById('accept-terms').checked;

    // Validation
    if (password !== passwordConfirm) {
        showMessage('error', 'Les mots de passe ne correspondent pas');
        return;
    }

    if (password.length < 8) {
        showMessage('error', 'Le mot de passe doit contenir au moins 8 caractères');
        return;
    }

    if (!acceptTerms) {
        showMessage('error', 'Vous devez accepter les conditions d\'utilisation');
        return;
    }

    // UI Loading state
    const btn = document.getElementById('register-submit-btn');
    const btnText = document.getElementById('register-btn-text');
    const btnLoader = document.getElementById('register-btn-loader');

    btn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'block';

    // AJAX Register
    fetch(fittrackRegister.ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'fittrack_classic_register',
            nonce: fittrackRegister.nonce,
            name: name,
            email: email,
            password: password
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showMessage('success', 'Compte créé avec succès! Redirection...');
            setTimeout(() => {
                window.location.href = fittrackRegister.redirectUrl;
            }, 1500);
        } else {
            showMessage('error', data.data.message || 'Erreur lors de la création du compte');
            btn.disabled = false;
            btnText.style.display = 'block';
            btnLoader.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'Une erreur est survenue');
        btn.disabled = false;
        btnText.style.display = 'block';
        btnLoader.style.display = 'none';
    });
}

// Show Message
function showMessage(type, message) {
    const container = document.getElementById('register-messages');
    const colors = {
        success: { bg: '#d4edda', border: '#28a745', text: '#155724' },
        error: { bg: '#f8d7da', border: '#dc3545', text: '#721c24' },
        info: { bg: '#d1ecf1', border: '#17a2b8', text: '#0c5460' }
    };

    const color = colors[type] || colors.info;

    container.innerHTML = `
        <div style="padding: 15px; background: ${color.bg}; border-left: 4px solid ${color.border}; color: ${color.text}; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
            ${message}
        </div>
    `;

    // Scroll to message
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Auto-hide success messages
    if (type === 'success') {
        setTimeout(() => {
            container.innerHTML = '';
        }, 4000);
    }
}
</script>

<?php get_footer(); ?>
