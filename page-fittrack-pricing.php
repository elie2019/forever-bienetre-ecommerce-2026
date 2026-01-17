<?php
/**
 * Template Name: FitTrack Pricing
 * Description: Page des plans tarifaires FitTrack Pro avec intégration Stripe
 */

// Enqueue FitTrack styles
function fittrack_pricing_assets() {
    if (is_page_template('page-fittrack-pricing.php')) {
        wp_enqueue_style('fittrack-app-css', get_template_directory_uri() . '/assets/css/fittrack-app.css', array(), '1.0.0');
        wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, false);

        wp_localize_script('jquery', 'fittrackData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fittrack_nonce'),
            'stripeKey' => get_option('stripe_publishable_key', 'pk_test_51Q0LznP9sEeKU3t8HbJLdxFSCeqj0FmGPqU9sKRFBvtYZqoRKNuTqBXmgDZKEYc9VJRNN8XjP3aTUZykVvHV0fU500kVr4T3qe')
        ));
    }
}
add_action('wp_enqueue_scripts', 'fittrack_pricing_assets');

get_header();
?>

<div class="fittrack-wrapper">
    <!-- Header FitTrack -->
    <header class="fittrack-header">
        <div class="fittrack-header-icon">📋</div>
        <h1 class="fittrack-header-title">Plans & Tarifs FitTrack Pro</h1>
        <p class="fittrack-header-subtitle">Choisissez le plan qui vous convient le mieux et commencez votre transformation dès aujourd'hui.</p>
    </header>

    <div class="fittrack-container">

        <!-- Pricing Cards -->
        <div class="fittrack-grid fittrack-grid-3" style="margin-bottom: 60px;">

            <!-- Plan Starter -->
            <div class="fittrack-card" style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 20px;">🌱</div>
                <h3 style="font-size: 2rem; color: var(--fittrack-primary); margin-bottom: 10px; font-family: 'Playfair Display', serif;">Starter</h3>
                <p style="color: var(--fittrack-text-light); margin-bottom: 25px;">Parfait pour débuter votre parcours fitness</p>

                <div style="margin: 30px 0;">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--fittrack-accent);">
                        19€
                        <span style="font-size: 1.2rem; font-weight: 400; color: var(--fittrack-text-light);">/mois</span>
                    </div>
                </div>

                <ul style="list-style: none; padding: 0; margin: 30px 0; text-align: left;">
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Suivi nutritionnel de base</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Journal d'entraînement</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Suivi du poids</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Support email 48h</span>
                    </li>
                </ul>

                <button class="fittrack-btn fittrack-btn-outline" style="width: 100%;" onclick="selectPlan('starter', 19)">
                    Choisir Starter
                </button>
            </div>

            <!-- Plan Pro -->
            <div class="fittrack-card" style="text-align: center; border: 3px solid var(--fittrack-accent); position: relative;">
                <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: var(--fittrack-accent); color: var(--fittrack-primary); padding: 6px 20px; border-radius: 20px; font-weight: 700; font-size: 0.8rem;">
                    POPULAIRE
                </div>

                <div style="font-size: 3rem; margin-bottom: 20px; margin-top: 10px;">🚀</div>
                <h3 style="font-size: 2rem; color: var(--fittrack-primary); margin-bottom: 10px; font-family: 'Playfair Display', serif;">Pro</h3>
                <p style="color: var(--fittrack-text-light); margin-bottom: 25px;">Accès complet à toutes les fonctionnalités</p>

                <div style="margin: 30px 0;">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--fittrack-accent);">
                        39€
                        <span style="font-size: 1.2rem; font-weight: 400; color: var(--fittrack-text-light);">/mois</span>
                    </div>
                </div>

                <ul style="list-style: none; padding: 0; margin: 30px 0; text-align: left;">
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span><strong>Tout du plan Starter</strong></span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Base de données 500K aliments</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Programmes d'entraînement personnalisés</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Graphiques avancés</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Support prioritaire 24h</span>
                    </li>
                </ul>

                <button class="fittrack-btn fittrack-btn-accent" style="width: 100%;" onclick="selectPlan('pro', 39)">
                    Choisir Pro
                </button>
            </div>

            <!-- Plan Premium -->
            <div class="fittrack-card" style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 20px;">👑</div>
                <h3 style="font-size: 2rem; color: var(--fittrack-primary); margin-bottom: 10px; font-family: 'Playfair Display', serif;">Premium</h3>
                <p style="color: var(--fittrack-text-light); margin-bottom: 25px;">L'expérience ultime avec coaching personnalisé</p>

                <div style="margin: 30px 0;">
                    <div style="font-size: 3rem; font-weight: 700; color: var(--fittrack-accent);">
                        69€
                        <span style="font-size: 1.2rem; font-weight: 400; color: var(--fittrack-text-light);">/mois</span>
                    </div>
                </div>

                <ul style="list-style: none; padding: 0; margin: 30px 0; text-align: left;">
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span><strong>Tout du plan Pro</strong></span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Coaching personnalisé 1-on-1</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Plans nutrition sur-mesure</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Appels vidéo mensuels</span>
                    </li>
                    <li style="padding: 10px 0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: var(--fittrack-success); font-size: 1.2rem;">✓</span>
                        <span>Support VIP 24/7</span>
                    </li>
                </ul>

                <button class="fittrack-btn fittrack-btn-primary" style="width: 100%;" onclick="selectPlan('premium', 69)">
                    Choisir Premium
                </button>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="fittrack-card">
            <div class="fittrack-card-header">
                <h2 class="fittrack-card-title">
                    <span class="fittrack-card-title-icon">❓</span>
                    Questions Fréquentes
                </h2>
            </div>
            <div class="fittrack-card-body">
                <div class="fittrack-faq">
                    <details style="margin-bottom: 20px; padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <summary style="font-weight: 600; cursor: pointer; font-size: 1.1rem;">Puis-je changer de plan à tout moment ?</summary>
                        <p style="margin-top: 15px; color: var(--fittrack-text-light);">Oui, vous pouvez upgrader ou downgrader votre plan à tout moment. Les modifications prennent effet immédiatement.</p>
                    </details>

                    <details style="margin-bottom: 20px; padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <summary style="font-weight: 600; cursor: pointer; font-size: 1.1rem;">Y a-t-il un engagement minimum ?</summary>
                        <p style="margin-top: 15px; color: var(--fittrack-text-light);">Non, tous nos plans sont sans engagement. Vous pouvez annuler à tout moment sans frais.</p>
                    </details>

                    <details style="margin-bottom: 20px; padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <summary style="font-weight: 600; cursor: pointer; font-size: 1.1rem;">Quels sont les moyens de paiement acceptés ?</summary>
                        <p style="margin-top: 15px; color: var(--fittrack-text-light);">Nous acceptons toutes les cartes bancaires (Visa, Mastercard, American Express) via notre système de paiement sécurisé Stripe.</p>
                    </details>

                    <details style="margin-bottom: 20px; padding: 20px; background: var(--fittrack-bg-light); border-radius: 8px;">
                        <summary style="font-weight: 600; cursor: pointer; font-size: 1.1rem;">Existe-t-il une période d'essai gratuite ?</summary>
                        <p style="margin-top: 15px; color: var(--fittrack-text-light);">Oui ! Tous les plans incluent 14 jours d'essai gratuit. Aucune carte bancaire requise.</p>
                    </details>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="fittrack-card" style="background: linear-gradient(135deg, var(--fittrack-primary) 0%, var(--fittrack-secondary) 100%); color: var(--fittrack-white); text-align: center;">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px; font-family: 'Playfair Display', serif;">Prêt à commencer votre transformation ?</h2>
            <p style="font-size: 1.2rem; margin-bottom: 30px; opacity: 0.9;">Rejoignez des milliers d'utilisateurs qui ont déjà atteint leurs objectifs avec FitTrack Pro.</p>
            <a href="<?php echo home_url('/fittrack-dashboard'); ?>" class="fittrack-btn fittrack-btn-accent" style="font-size: 1.1rem; padding: 18px 40px;">
                Commencer Maintenant →
            </a>
        </div>

    </div>
</div>

<script>
function selectPlan(plan, price) {
    // Vérifier si l'utilisateur est connecté
    <?php if (!is_user_logged_in()) : ?>
        alert('Vous devez être connecté pour souscrire à un plan. Redirection vers la page de connexion...');
        window.location.href = '<?php echo wp_login_url(get_permalink()); ?>';
        return;
    <?php endif; ?>

    // Préparer les données pour Stripe
    const planData = {
        plan: plan,
        price: price,
        currency: 'eur',
        user_id: <?php echo is_user_logged_in() ? get_current_user_id() : 0; ?>
    };

    // Afficher loader
    const button = event.target;
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Chargement...';

    // Appel AJAX pour créer la session Stripe
    fetch(fittrackData.ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'create_fittrack_subscription',
            nonce: fittrackData.nonce,
            plan: plan,
            price: price
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.sessionId) {
            // Rediriger vers Stripe Checkout
            const stripe = Stripe(fittrackData.stripeKey);
            stripe.redirectToCheckout({ sessionId: data.data.sessionId });
        } else {
            alert('Erreur lors de la création de la session de paiement. Veuillez réessayer.');
            button.disabled = false;
            button.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue. Veuillez réessayer.');
        button.disabled = false;
        button.textContent = originalText;
    });
}
</script>

<?php get_footer(); ?>
