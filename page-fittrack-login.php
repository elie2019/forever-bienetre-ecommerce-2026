<?php
/**
 * Template Name: FitTrack Login
 * Description: Page de connexion FitTrack Pro avec Google OAuth
 */

// Rediriger si déjà connecté
if (is_user_logged_in()) {
    wp_redirect(home_url('/fittrack-dashboard'));
    exit;
}

// Enqueue FitTrack styles
function fittrack_login_assets() {
    if (is_page_template('page-fittrack-login.php')) {
        wp_enqueue_style('fittrack-app-css', get_template_directory_uri() . '/assets/css/fittrack-app.css', array(), '1.0.0');

        // Google OAuth SDK
        wp_enqueue_script('google-platform', 'https://accounts.google.com/gsi/client', array(), null, false);

        wp_localize_script('jquery', 'fittrackLogin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fittrack_login_nonce'),
            'redirectUrl' => home_url('/fittrack-dashboard'),
            'googleClientId' => get_option('fittrack_google_client_id', '')
        ));
    }
}
add_action('wp_enqueue_scripts', 'fittrack_login_assets');

get_header();
?>

<div class="fittrack-wrapper" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%);">
    <div style="width: 100%; max-width: 450px; padding: 20px;">

        <!-- Logo & Title -->
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="font-size: 4rem; margin-bottom: 15px;">💪</div>
            <h1 style="font-size: 2.5rem; color: white; font-family: 'Playfair Display', serif; margin-bottom: 10px;">FitTrack Pro</h1>
            <p style="color: rgba(255,255,255,0.8); font-size: 1.1rem;">Connexion à votre compte</p>
        </div>

        <!-- Login Card -->
        <div class="fittrack-card" style="padding: 40px;">

            <!-- Messages d'erreur/succès -->
            <div id="login-messages"></div>

            <!-- Google Sign In Button -->
            <div style="margin-bottom: 30px;">
                <div id="g_id_onload"
                     data-client_id="<?php echo esc_attr(get_option('fittrack_google_client_id', '')); ?>"
                     data-callback="handleGoogleLogin"
                     data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="rectangular"
                     data-theme="outline"
                     data-text="signin_with"
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

            <!-- Classic Login Form -->
            <form id="fittrack-login-form" onsubmit="handleClassicLogin(event)">

                <div class="fittrack-form-group">
                    <label class="fittrack-label">Email</label>
                    <input type="email" id="login-email" class="fittrack-input" placeholder="votre@email.com" required>
                </div>

                <div class="fittrack-form-group">
                    <label class="fittrack-label">Mot de Passe</label>
                    <input type="password" id="login-password" class="fittrack-input" placeholder="••••••••" required>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; cursor: pointer;">
                        <input type="checkbox" id="remember-me" style="width: 18px; height: 18px;">
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="<?php echo wp_lostpassword_url(); ?>" style="color: var(--fittrack-accent); text-decoration: none; font-size: 0.9rem;">
                        Mot de passe oublié?
                    </a>
                </div>

                <button type="submit" class="fittrack-btn fittrack-btn-accent" style="width: 100%; justify-content: center; padding: 16px;" id="login-submit-btn">
                    <span id="login-btn-text">Se Connecter</span>
                    <span id="login-btn-loader" style="display: none;">
                        <svg style="animation: spin 1s linear infinite; width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path>
                        </svg>
                    </span>
                </button>

            </form>

            <!-- Register Link -->
            <div style="margin-top: 25px; text-align: center; padding-top: 25px; border-top: 1px solid var(--fittrack-border);">
                <span style="color: var(--fittrack-text-light); font-size: 0.9rem;">Pas encore de compte?</span>
                <a href="<?php echo home_url('/fittrack-register'); ?>" style="color: var(--fittrack-accent); font-weight: 600; text-decoration: none; margin-left: 5px;">
                    Créer un compte →
                </a>
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
function handleGoogleLogin(response) {
    console.log('Google login response:', response);

    // Afficher loader
    showMessage('info', 'Connexion en cours...');

    // Envoyer le token à WordPress
    fetch(fittrackLogin.ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'fittrack_google_login',
            nonce: fittrackLogin.nonce,
            credential: response.credential
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showMessage('success', 'Connexion réussie! Redirection...');
            setTimeout(() => {
                window.location.href = fittrackLogin.redirectUrl;
            }, 1000);
        } else {
            showMessage('error', data.data.message || 'Erreur de connexion Google');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', 'Une erreur est survenue lors de la connexion Google');
    });
}

// Classic Login Handler
function handleClassicLogin(event) {
    event.preventDefault();

    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const remember = document.getElementById('remember-me').checked;

    // UI Loading state
    const btn = document.getElementById('login-submit-btn');
    const btnText = document.getElementById('login-btn-text');
    const btnLoader = document.getElementById('login-btn-loader');

    btn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'block';

    // AJAX Login
    fetch(fittrackLogin.ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'fittrack_classic_login',
            nonce: fittrackLogin.nonce,
            email: email,
            password: password,
            remember: remember ? '1' : '0'
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showMessage('success', 'Connexion réussie! Redirection...');
            setTimeout(() => {
                window.location.href = fittrackLogin.redirectUrl;
            }, 1000);
        } else {
            showMessage('error', data.data.message || 'Email ou mot de passe incorrect');
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
    const container = document.getElementById('login-messages');
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

    // Auto-hide success messages
    if (type === 'success') {
        setTimeout(() => {
            container.innerHTML = '';
        }, 3000);
    }
}
</script>

<?php get_footer(); ?>
