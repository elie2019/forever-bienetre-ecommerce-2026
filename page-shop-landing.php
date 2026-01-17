<?php
/**
 * Template Name: Shop Landing Premium
 * Description: Landing page premium pour la boutique Forever Bien-Etre avec intégration FitTrack Pro
 */

// Enqueue des styles et scripts spécifiques
function shop_landing_enqueue_assets() {
    if (is_page_template('page-shop-landing.php')) {
        // Google Fonts
        wp_enqueue_style(
            'google-fonts-shop',
            'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap',
            array(),
            null
        );

        // Stripe JS
        wp_enqueue_script(
            'stripe-js',
            'https://js.stripe.com/v3/',
            array(),
            null,
            false
        );

        // CSS personnalisé
        wp_enqueue_style(
            'shop-landing-css',
            get_template_directory_uri() . '/assets/css/shop-landing.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/shop-landing.css')
        );

        // JavaScript personnalisé
        wp_enqueue_script(
            'shop-landing-js',
            get_template_directory_uri() . '/assets/js/shop-landing.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/shop-landing.js'),
            true
        );

        // Passer les données PHP à JavaScript
        wp_localize_script('shop-landing-js', 'shopLandingData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('shop_landing_nonce'),
            'siteUrl' => home_url(),
            'themeUrl' => get_template_directory_uri(),
            'stripePublicKey' => get_option('stripe_publishable_key', 'pk_test_...')
        ));
    }
}
add_action('wp_enqueue_scripts', 'shop_landing_enqueue_assets');

// Récupérer les produits dynamiquement
$products_query = new WP_Query(array(
    'post_type' => 'product',
    'posts_per_page' => 6,
    'orderby' => 'date',
    'order' => 'DESC'
));

// Fallback products si aucun produit WordPress
$fallback_products = array(
    array(
        'id' => 'product_1',
        'name' => 'Forever Aloe Vera Gel',
        'category' => 'Bien-être',
        'description' => 'Gel d\'Aloe Vera pur à 99,7% - Boisson naturelle pour votre bien-être quotidien',
        'price' => 29.90,
        'image' => get_template_directory_uri() . '/assets/images/catalogue/aloe-vera.jpg',
        'badge' => 'Bestseller'
    ),
    array(
        'id' => 'product_2',
        'name' => 'Marine Collagen',
        'category' => 'Beauté',
        'description' => 'Collagène marin hydrolysé - Pour une peau jeune et éclatante',
        'price' => 39.90,
        'image' => get_template_directory_uri() . '/assets/images/catalogue/collagene.jpg',
        'badge' => 'Nouveau'
    ),
    array(
        'id' => 'product_3',
        'name' => 'ARGI+',
        'category' => 'Performance',
        'description' => 'L-Arginine et vitamines - Boost votre énergie et performance',
        'price' => 54.90,
        'image' => get_template_directory_uri() . '/assets/images/catalogue/argi.jpg',
        'badge' => 'Pro'
    ),
    array(
        'id' => 'product_4',
        'name' => 'Aloe Scrub',
        'category' => 'Beauté',
        'description' => 'Gommage corporel à l\'Aloe Vera - Exfoliation douce et naturelle',
        'price' => 18.90,
        'image' => get_template_directory_uri() . '/assets/images/catalogue/scrub.jpg',
        'badge' => ''
    ),
    array(
        'id' => 'product_5',
        'name' => 'Active Pro-B',
        'category' => 'Santé',
        'description' => 'Probiotiques avancés - 8 milliards de bonnes bactéries par dose',
        'price' => 32.90,
        'image' => get_template_directory_uri() . '/assets/images/catalogue/probiotic.jpg',
        'badge' => ''
    ),
    array(
        'id' => 'product_6',
        'name' => 'Vital 5',
        'category' => 'Pack',
        'description' => 'Pack complet de 5 produits essentiels pour votre bien-être',
        'price' => 149.90,
        'image' => get_template_directory_uri() . '/assets/images/catalogue/vital5.jpg',
        'badge' => 'Pack'
    )
);

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

    <!-- Header -->
    <header class="header" id="header">
        <div class="header-container">
            <a href="<?php echo home_url(); ?>" class="logo">Forever <span>Bien-Etre</span></a>

            <nav class="nav-menu">
                <ul class="nav-links">
                    <li><a href="#hero">Accueil</a></li>
                    <li><a href="#products">Boutique</a></li>
                    <li class="fittrack-dropdown">
                        <button class="fittrack-toggle" id="fittrackToggle">
                            <span>💪 FitTrack Pro</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="fittrack-submenu">
                            <a href="#fittrack-pricing">
                                <span>📋</span>
                                <span>Pricing & Plans</span>
                            </a>
                            <a href="#fittrack-dashboard">
                                <span>📊</span>
                                <span>Dashboard</span>
                            </a>
                            <a href="#fittrack-nutrition">
                                <span>🥗</span>
                                <span>Nutrition Tracker</span>
                            </a>
                            <a href="#fittrack-workouts">
                                <span>🏋️</span>
                                <span>Workout Logger</span>
                            </a>
                            <a href="#fittrack-progress">
                                <span>📈</span>
                                <span>Progress Tracking</span>
                            </a>
                            <a href="#fittrack-goals">
                                <span>🎯</span>
                                <span>Goals Manager</span>
                            </a>
                            <a href="#fittrack-settings">
                                <span>⚙️</span>
                                <span>Settings</span>
                            </a>
                        </div>
                    </li>
                    <li><a href="#features">Nos Engagements</a></li>
                    <li><a href="#newsletter">Contact</a></li>
                </ul>

                <button class="cart-btn" id="cartBtn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Panier
                    <span class="cart-count" id="cartCount">0</span>
                </button>

                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <div class="mobile-menu-title">Menu</div>
            <button class="mobile-menu-close" id="mobileMenuClose">&times;</button>
        </div>
        <ul class="mobile-nav-links">
            <li><a href="#hero">Accueil</a></li>
            <li><a href="#products">Boutique</a></li>
            <li class="fittrack-dropdown">
                <button class="fittrack-toggle" id="fittrackToggleMobile">
                    <span>💪 FitTrack Pro</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="fittrack-submenu">
                    <a href="#fittrack-pricing"><span>📋</span><span>Pricing & Plans</span></a>
                    <a href="#fittrack-dashboard"><span>📊</span><span>Dashboard</span></a>
                    <a href="#fittrack-nutrition"><span>🥗</span><span>Nutrition Tracker</span></a>
                    <a href="#fittrack-workouts"><span>🏋️</span><span>Workout Logger</span></a>
                    <a href="#fittrack-progress"><span>📈</span><span>Progress Tracking</span></a>
                    <a href="#fittrack-goals"><span>🎯</span><span>Goals Manager</span></a>
                    <a href="#fittrack-settings"><span>⚙️</span><span>Settings</span></a>
                </div>
            </li>
            <li><a href="#features">Nos Engagements</a></li>
            <li><a href="#newsletter">Contact</a></li>
        </ul>
    </div>

    <!-- Cart Overlay -->
    <div class="cart-overlay" id="cartOverlay"></div>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>Votre Panier</h3>
            <button class="cart-close" id="cartClose">&times;</button>
        </div>

        <div class="cart-items" id="cartItems">
            <!-- Cart items will be inserted here by JavaScript -->
        </div>

        <div class="cart-footer">
            <div class="cart-subtotal">
                <span>Sous-total:</span>
                <span id="cartSubtotal">0,00 €</span>
            </div>
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotal">0,00 €</span>
            </div>
            <button class="checkout-btn" id="checkoutBtn">Passer au paiement</button>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-bg"></div>
        <div class="hero-pattern"></div>
        <div class="hero-content">
            <div class="hero-subtitle">Collection Premium</div>
            <h1 class="hero-title">Excellence & Bien-Être</h1>
            <p class="hero-description">Découvrez notre sélection de produits Forever Living haut de gamme pour votre santé et votre beauté</p>
            <a href="#products" class="hero-cta">Découvrir</a>
        </div>
        <div class="scroll-indicator">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- Carousel Section -->
    <section class="carousel-section">
        <div class="carousel-container">
            <div class="carousel-slides" id="carouselSlides">
                <div class="carousel-slide active">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/videos/slide1.jpg" alt="Forever Aloe Vera">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-content">
                        <h2>Aloe Vera Pure à 99,7%</h2>
                        <p>La puissance naturelle de l'Aloe Vera pour votre bien-être quotidien</p>
                        <a href="#products" class="carousel-btn">Découvrir</a>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/videos/slide2.jpg" alt="Collagène Marin">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-content">
                        <h2>Collagène Marin Premium</h2>
                        <p>Pour une peau jeune, ferme et éclatante de beauté</p>
                        <a href="#products" class="carousel-btn">Découvrir</a>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/videos/slide3.jpg" alt="Nutrition Sportive">
                    <div class="carousel-overlay"></div>
                    <div class="carousel-content">
                        <h2>Performance & Vitalité</h2>
                        <p>Boostez votre énergie avec nos compléments professionnels</p>
                        <a href="#products" class="carousel-btn">Découvrir</a>
                    </div>
                </div>
            </div>
            <div class="carousel-arrow prev" id="carouselPrev">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </div>
            <div class="carousel-arrow next" id="carouselNext">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
            <div class="carousel-nav" id="carouselNav">
                <div class="carousel-dot active" data-slide="0"></div>
                <div class="carousel-dot" data-slide="1"></div>
                <div class="carousel-dot" data-slide="2"></div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section" id="products">
        <div class="section-header">
            <span class="section-tag">Notre Collection</span>
            <h2 class="section-title">Produits Premium</h2>
        </div>

        <div class="products-grid">
            <?php
            // Utiliser les produits WordPress si disponibles, sinon fallback
            $products_to_display = $fallback_products;

            if ($products_query->have_posts()) {
                $products_to_display = array();
                while ($products_query->have_posts()) {
                    $products_query->the_post();
                    $products_to_display[] = array(
                        'id' => 'product_' . get_the_ID(),
                        'name' => get_the_title(),
                        'category' => 'Produit',
                        'description' => get_the_excerpt(),
                        'price' => get_post_meta(get_the_ID(), '_price', true) ?: 29.90,
                        'image' => get_the_post_thumbnail_url(get_the_ID(), 'large') ?: get_template_directory_uri() . '/assets/images/catalogue/default.jpg',
                        'badge' => get_post_meta(get_the_ID(), '_badge', true) ?: ''
                    );
                }
                wp_reset_postdata();
            }

            foreach ($products_to_display as $product) :
            ?>
            <div class="product-card" data-product-id="<?php echo esc_attr($product['id']); ?>">
                <div class="product-image-wrapper">
                    <img src="<?php echo esc_url($product['image']); ?>" alt="<?php echo esc_attr($product['name']); ?>" class="product-image">
                    <?php if (!empty($product['badge'])) : ?>
                    <div class="product-badge"><?php echo esc_html($product['badge']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-category"><?php echo esc_html($product['category']); ?></div>
                    <h3 class="product-name"><?php echo esc_html($product['name']); ?></h3>
                    <p class="product-description"><?php echo esc_html($product['description']); ?></p>
                    <div class="product-footer">
                        <div class="product-price"><?php echo number_format($product['price'], 2, ',', ' '); ?> € <span>TTC</span></div>
                        <button class="btn-add-cart" data-id="<?php echo esc_attr($product['id']); ?>" data-name="<?php echo esc_attr($product['name']); ?>" data-price="<?php echo esc_attr($product['price']); ?>" data-image="<?php echo esc_url($product['image']); ?>">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Ajouter
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="section-header">
            <span class="section-tag">Pourquoi Nous Choisir</span>
            <h2 class="section-title">Nos Engagements</h2>
        </div>

        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Qualité Garantie</h3>
                <p class="feature-text">Produits certifiés et testés pour votre sécurité</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Livraison Rapide</h3>
                <p class="feature-text">Expédition sous 24-48h partout en France</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Paiement Sécurisé</h3>
                <p class="feature-text">Transactions protégées par Stripe</p>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Support 7j/7</h3>
                <p class="feature-text">Notre équipe à votre écoute tous les jours</p>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section" id="newsletter">
        <div class="newsletter-content">
            <h2 class="newsletter-title">Restez Informé</h2>
            <p class="newsletter-text">Inscrivez-vous pour recevoir nos offres exclusives et nos conseils bien-être.</p>
            <form class="newsletter-form" id="newsletterForm">
                <input type="email" class="newsletter-input" placeholder="Votre adresse email" required>
                <button type="submit" class="newsletter-btn">S'inscrire</button>
            </form>
        </div>
    </section>

    <?php
    // Include FitTrack sections
    get_template_part('template-parts/fittrack-sections');
    ?>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-logo">Forever Bien-Etre</div>
            <nav class="footer-links">
                <a href="<?php echo home_url('/mentions-legales'); ?>">Mentions Légales</a>
                <a href="<?php echo home_url('/politique-confidentialite'); ?>">Politique de Confidentialité</a>
                <a href="<?php echo home_url('/cgv'); ?>">CGV</a>
                <a href="<?php echo home_url('/contact'); ?>">Contact</a>
            </nav>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Forever Bien-Etre. Tous droits réservés. Paiements sécurisés par Stripe.</p>
        </div>
    </footer>

    <!-- Success Modal -->
    <div class="modal-overlay" id="successModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <div class="modal-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="modal-title">Paiement Réussi !</h3>
            <p class="modal-text">Votre commande a été confirmée. Vous recevrez un email de confirmation dans quelques instants.</p>
            <button class="modal-btn" onclick="closeModal()">Continuer</button>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="toastMessage">Produit ajouté au panier !</span>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
