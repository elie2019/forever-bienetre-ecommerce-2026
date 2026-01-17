<?php
/**
 * Forever BE Premium Theme Functions
 *
 * @package Forever_BE_Premium
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load Centralized Configuration
 * All theme constants and settings are defined in config.php
 */
require_once get_template_directory() . '/inc/config.php';

/**
 * CSS Consolidated Loader (Performance Optimization)
 *
 * Active le chargement du bundle CSS consolidé (60 fichiers -> 1 fichier)
 * Pour activer: définir FOREVER_BE_USE_CONSOLIDATED_CSS à true dans wp-config.php
 *
 * @since 2.0.0 - Refonte Performance 2026
 */
require_once get_template_directory() . '/inc/css-consolidated-loader.php';

/**
 * JavaScript Optimization (Performance Optimization)
 *
 * NOTE: Désactivé car performance-optimizations.php gère déjà cette fonctionnalité
 * Le fichier js-optimization.php contient des optimisations supplémentaires
 * mais créait un conflit de fonction avec performance-optimizations.php
 *
 * @since 2.0.0 - Refonte Performance 2026
 */
// require_once get_template_directory() . '/inc/js-optimization.php';

/**
 * YouTube Lazy Load (Performance Optimization)
 *
 * Remplace les iframes YouTube par des facades (thumbnail + click-to-load)
 * Économie estimée: ~500KB+ par vidéo au chargement initial
 * Activé par défaut, désactiver avec FOREVER_BE_YOUTUBE_LAZY = false
 *
 * @since 2.0.0 - Refonte Performance 2026
 */
require_once get_template_directory() . '/inc/youtube-lazy-load.php';

/**
 * FitTrack Pro - Fitness & Nutrition Platform
 *
 * Complete SaaS fitness tracking platform with:
 * - Nutrition logging and analysis
 * - Workout tracking and programs
 * - Progress monitoring with charts
 * - Stripe subscriptions (Free/Pro/Premium)
 * - AI-powered features (Gemini integration)
 *
 * @since 1.0.0 - FitTrack Pro Launch
 */
if (file_exists(get_template_directory() . '/inc/fittrack/fittrack-init.php')) {
    require_once get_template_directory() . '/inc/fittrack/fittrack-init.php';
}

/**
 * Image Optimization (Performance Optimization)
 *
 * Optimise le chargement des images:
 * - loading="lazy" et decoding="async" automatiques
 * - Support WebP avec génération automatique
 * - Préchargement des images LCP
 * Activé par défaut, désactiver avec FOREVER_BE_OPTIMIZE_IMAGES = false
 *
 * @since 2.0.0 - Refonte Performance 2026
 */
require_once get_template_directory() . '/inc/image-optimization.php';

/**
 * Legacy Constants (for backwards compatibility)
 * These are now defined in inc/config.php
 */
if ( ! defined( 'FOREVER_BE_VERSION' ) ) {
    define( 'FOREVER_BE_VERSION', FOREVER_BE_THEME_VERSION );
}

/**
 * Theme Setup
 */
function forever_be_theme_setup() {

    // Make theme available for translation
    load_theme_textdomain( 'forever-be', FOREVER_BE_THEME_DIR . '/languages' );

    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1200, 630, true ); // 16:9 ratio

    // Add custom image sizes
    add_image_size( 'forever-be-featured', 800, 600, true );
    add_image_size( 'forever-be-product', 600, 600, true );
    add_image_size( 'forever-be-thumbnail', 350, 350, true );
    add_image_size( 'forever-be-blog-card', 800, 500, true );

    // Register navigation menus
    register_nav_menus( array(
        'primary'   => esc_html__( 'Menu Principal', 'forever-be' ),
        'footer_1'  => esc_html__( 'Footer Colonne 1 (Produits)', 'forever-be' ),
        'footer_2'  => esc_html__( 'Footer Colonne 2 (Opportunité)', 'forever-be' ),
        'footer_3'  => esc_html__( 'Footer Colonne 3 (Support)', 'forever-be' ),
    ) );

    // Switch default core markup to output valid HTML5
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Add theme support for selective refresh for widgets
    add_theme_support( 'customize-selective-refresh-widgets' );

    // Add support for custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 240,
        'flex-width'  => true,
        'flex-height' => true,
        'header-text' => array( 'site-title', 'site-description' ),
    ) );

    // Add support for responsive embeds
    add_theme_support( 'responsive-embeds' );

    // Add support for editor styles
    add_theme_support( 'editor-styles' );

    // Content width
    if ( ! isset( $content_width ) ) {
        $content_width = 1280;
    }
}
add_action( 'after_setup_theme', 'forever_be_theme_setup' );

// Email Manager - Système d'Email Centralisé et Robuste
require_once FOREVER_BE_INC_DIR . '/class-email-manager.php';

// Email Queue System (v8.0.0) - For bulk email processing
require_once FOREVER_BE_INC_DIR . '/class-email-queue.php';
require_once FOREVER_BE_INC_DIR . '/email-queue-admin.php';

// WooAffiliation Module
require get_template_directory() . '/inc/wooaffiliation/wooaffiliation.php';

// Modal System (UX/UI Expert Refonte) - DÉSACTIVÉ
// Contient "Diagnostic Bien-Être Gratuit" - remplacé par quiz-diagnostic
// require_once FOREVER_BE_INC_DIR . '/modals-system.php';

// Modal Lazy Loading System (v8.0.0) - Optional performance optimization
require_once FOREVER_BE_INC_DIR . '/modals-lazy-loading.php';

// Simple Capture System (v8.0.0) - Formulaires Email Simplifiés pour Conversion Maximale
require_once FOREVER_BE_INC_DIR . '/simple-capture-system.php';

// Performance Optimizations (defer, preload, lazy loading, etc.)
require_once FOREVER_BE_INC_DIR . '/performance-optimizations.php';

// FAQ System with Pagination (v8.0.0)
require_once FOREVER_BE_INC_DIR . '/faq-system.php';

// Exit Intent Popup (Conversion Expert v8.0.0) - DÉSACTIVÉ
// Le quiz-diagnostic gère déjà la capture de leads
// require_once FOREVER_BE_INC_DIR . '/exit-intent-popup.php';

// Product Promo Lite (v1.0.1) - Système popup pour boutons "Commander"
// Gère l'affichage du popup promo code lors du clic sur les boutons produits
require_once FOREVER_BE_INC_DIR . '/product-promo-lite.php';

// Expert Email System Module (v1.0.0) - Emails personnalises multi-experts
// Shortcodes: [fbo_expert_form] [fbo_product_recommendation ref="815"]
// API: fbo_expert_email()->send_personalized_email($email, $products, $profile)
require_once FOREVER_BE_INC_DIR . '/modules/expert-email-system/class-fbo-expert-email-module.php';

/**
 * Enqueue Scripts and Styles
 */
function forever_be_enqueue_scripts() {

    // Error Handler (v8.0.0) - Load first to catch all errors
    wp_enqueue_script(
        'forever-be-error-handler',
        FOREVER_BE_THEME_URI . '/assets/js/error-handler.js',
        array(), // No dependencies - must load first
        '8.0.0',
        false // Load in header to catch early errors
    );

    // Google Fonts - Optimized Strategy (Performance Expert v9.0)
    // Preconnect + async loading for better LCP/FCP
    add_action('wp_head', 'forever_be_optimized_fonts', 1);

    // Note: Old enqueue below is kept as fallback but fonts loaded via optimized function
    // wp_enqueue_style(
    //     'forever-be-fonts',
    //     'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&family=Dancing+Script:wght@700&display=swap',
    //     array(),
    //     null
    // );

    // Main stylesheet
    wp_enqueue_style(
        'forever-be-style',
        get_stylesheet_uri(),
        array(),
        FOREVER_BE_VERSION
    );

    // Premium Styles (Homepage Overhaul)
    wp_enqueue_style(
        'forever-be-premium-style',
        FOREVER_BE_THEME_URI . '/assets/css/premium-styles.css',
        array( 'forever-be-style' ),
        FOREVER_BE_VERSION
    );

    // Header Inline CSS externalisé (359 lignes)
    wp_enqueue_style(
        'forever-be-header-inline',
        FOREVER_BE_THEME_URI . '/assets/css/pages/header-inline.css',
        array( 'forever-be-premium-style' ),
        '9.0.0'
    );

    // Micro-Interactions CSS (P8 - Refonte 2026)
    // Animations subtiles pour hover, focus, transitions
    wp_enqueue_style(
        'forever-be-micro-interactions',
        FOREVER_BE_THEME_URI . '/assets/css/micro-interactions.css',
        array( 'forever-be-premium-style' ),
        FOREVER_BE_VERSION
    );

    // Desktop UX Expert Refonte (Desktop Only - 20 Years Experience)
    wp_enqueue_style(
        'forever-be-desktop-ux-refonte',
        FOREVER_BE_THEME_URI . '/assets/css/desktop-ux-refonte.css',
        array( 'forever-be-premium-style' ),
        '8.1.0'
    );

    // Footer & Newsletter Mobile Premium CSS (Mobile Only < 768px)
    // Design E-commerce Premium - Score 10/10
    // Loaded AFTER tailwind.css to override Tailwind classes
    wp_enqueue_style(
        'forever-be-footer-mobile-premium',
        FOREVER_BE_THEME_URI . '/assets/css/footer-mobile-premium.css',
        array( 'forever-be-premium-style', 'forever-be-tailwind' ),
        '1.5.2'
    );

    // Homepage Mobile Premium CSS (Mobile Only < 768px)
    // Compactage, alignements, espaces réduits sans supprimer le carrousel
    if ( is_front_page() ) {
        wp_enqueue_style(
            'forever-be-homepage-mobile-premium',
            FOREVER_BE_THEME_URI . '/assets/css/homepage-mobile-premium.css',
            array( 'forever-be-premium-style' ),
            '1.0.0'
        );
    }

    // Shop Premium Styles
    if ( is_post_type_archive( 'product' ) || is_tax( array( 'product_category', 'health_category' ) ) ) {
        wp_enqueue_style(
            'forever-be-shop-premium',
            FOREVER_BE_THEME_URI . '/assets/css/shop-premium.css',
            array( 'forever-be-premium-style' ),
            FOREVER_BE_VERSION
        );

        // Shop Mobile Refonte CSS (Mobile Only < 768px)
        // Expert E-commerce Mobile UX - Simplification acces produits
        wp_enqueue_style(
            'forever-be-shop-mobile-refonte',
            FOREVER_BE_THEME_URI . '/assets/css/shop-mobile-refonte.css',
            array( 'forever-be-shop-premium' ),
            '2.0.4'
        );

        wp_enqueue_script(
            'forever-be-shop-premium-js',
            FOREVER_BE_THEME_URI . '/assets/js/shop-premium.js',
            array( 'jquery' ), // jQuery might not be strictly needed but good for compatibility
            FOREVER_BE_VERSION,
            true
        );

        wp_localize_script( 'forever-be-shop-premium-js', 'forever_be_vars', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );

        // Shop AJAX Filters (P5 - Refonte 2026)
        wp_enqueue_script(
            'forever-be-shop-ajax-filters',
            FOREVER_BE_THEME_URI . '/assets/js/shop-ajax-filters.js',
            array(),
            FOREVER_BE_VERSION,
            true
        );
    }

    // Contact Page Styles v7.0.0 (Expert WordPress Refonte)
    if ( is_page_template( 'page-contact.php' ) || is_page( 'contact' ) ) {
        wp_enqueue_style(
            'forever-be-contact-page',
            FOREVER_BE_THEME_URI . '/assets/css/contact-page.css',
            array( 'forever-be-premium-style' ),
            '7.0.0'
        );
    }

    // Affiliation Page Styles & Scripts v7.0.0 (5 Experts Refonte)
    if ( is_page_template( 'page-affiliation.php' ) || is_page( 'affiliation' ) ) {
        // Performance Expert v7.0: CSS with preload hint
        wp_enqueue_style(
            'forever-be-affiliation-page',
            FOREVER_BE_THEME_URI . '/assets/css/page-affiliation.css',
            array( 'forever-be-premium-style' ),
            '7.0.0'
        );

        // Performance Expert v9.0: CSS inline externalisé (800 lignes -> fichier externe)
        wp_enqueue_style(
            'forever-be-affiliation-inline',
            FOREVER_BE_THEME_URI . '/assets/css/pages/page-affiliation-inline.css',
            array( 'forever-be-affiliation-page' ),
            '9.0.0'
        );

        // Performance Expert v7.0: Script loaded in footer with defer strategy
        wp_enqueue_script(
            'forever-be-affiliation-js',
            FOREVER_BE_THEME_URI . '/assets/js/page-affiliation.js',
            array(),
            '7.0.0',
            array(
                'in_footer' => true,
                'strategy'  => 'defer',
            )
        );

        // WordPress Expert v7.0: Localize script for AJAX with security nonce
        wp_localize_script( 'forever-be-affiliation-js', 'forever_be_affiliation', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'forever_be_fbo_lead_nonce' ),
            'messages' => array(
                'success'          => __( 'Merci ! Votre demande a bien été envoyée. Nous vous contacterons dans les 24h.', 'forever-be' ),
                'error'            => __( 'Une erreur est survenue. Veuillez réessayer ou nous contacter directement.', 'forever-be' ),
                'required'         => __( 'Ce champ est requis.', 'forever-be' ),
                'invalid_email'    => __( 'Veuillez entrer une adresse email valide.', 'forever-be' ),
                'invalid_phone'    => __( 'Veuillez entrer un numéro de téléphone valide.', 'forever-be' ),
                'consent_required' => __( 'Vous devez accepter les conditions pour continuer.', 'forever-be' ),
            ),
        ) );
    }

    // FAQ Page Script with Pagination (v8.0.0)
    if ( is_page_template( 'page-faq.php' ) || is_page( 'faq' ) ) {
        wp_enqueue_script(
            'forever-be-faq-pagination',
            FOREVER_BE_THEME_URI . '/assets/js/faq-pagination.js',
            array(),
            '8.0.0',
            array(
                'in_footer' => true,
                'strategy'  => 'defer',
            )
        );

        // Localize script for AJAX
        wp_localize_script( 'forever-be-faq-pagination', 'foreverBEFAQ', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'forever_be_faq_nonce' ),
        ) );
    }

    // TailwindCSS (will be replaced with compiled version later)
    wp_enqueue_style(
        'forever-be-tailwind',
        FOREVER_BE_THEME_URI . '/assets/css/tailwind.css',
        array(),
        FOREVER_BE_VERSION
    );

    // Component Styles - Centralized inline styles replacement
    wp_enqueue_style(
        'forever-be-components',
        FOREVER_BE_THEME_URI . '/assets/css/components.css',
        array( 'forever-be-tailwind' ),
        FOREVER_BE_VERSION
    );

    // Mobile-First Refonte CSS - Expert Sarah CHEN (Breakpoints unifiés, spacing system)
    wp_enqueue_style(
        'forever-be-mobile-first',
        FOREVER_BE_THEME_URI . '/assets/css/mobile-first-refonte.css',
        array( 'forever-be-premium-style' ),
        '1.0.2'
    );

    // Responsive Fixes - Professional UX/UI Refonte v8.0.0
    // Expert-level responsive corrections for all breakpoints (mobile, tablet, desktop)
    wp_enqueue_style(
        'forever-be-responsive-fixes',
        FOREVER_BE_THEME_URI . '/assets/css/responsive-fixes.css',
        array( 'forever-be-mobile-first' ),
        '8.0.0'
    );

    // Header Mobile Styles - Extracted from inline (428 lines optimized)
    wp_enqueue_style(
        'forever-be-header-mobile',
        FOREVER_BE_THEME_URI . '/assets/css/header-mobile.css',
        array( 'forever-be-responsive-fixes' ),
        '8.0.1'
    );

    // Footer Premium Styles - Extracted from inline (118 lines optimized)
    wp_enqueue_style(
        'forever-be-footer-premium',
        FOREVER_BE_THEME_URI . '/assets/css/footer-premium.css',
        array( 'forever-be-responsive-fixes' ),
        '8.0.1'
    );

    // Home Premium Styles - Extracted from inline (901 lines optimized)
    if ( is_front_page() || is_home() ) {
        wp_enqueue_style(
            'forever-be-home-premium',
            FOREVER_BE_THEME_URI . '/assets/css/home-premium.css',
            array( 'forever-be-responsive-fixes' ),
            '8.0.1'
        );

        // Hero Video Carousel CSS - Professional Marketing Quality v8.2.0
        // Effets Ken Burns, Parallax, transitions cinématiques
        wp_enqueue_style(
            'forever-be-hero-video-carousel',
            FOREVER_BE_THEME_URI . '/assets/css/hero-video-carousel.css',
            array( 'forever-be-home-premium' ),
            '8.2.0'
        );

        // Hero Video Carousel JavaScript - Auto-play, navigation, lazy loading
        wp_enqueue_script(
            'forever-be-hero-video-carousel-js',
            FOREVER_BE_THEME_URI . '/assets/js/hero-video-carousel.js',
            array(), // Vanilla JS - no dependencies
            '8.2.0',
            array(
                'in_footer' => true,
                'strategy'  => 'defer',
            )
        );
    }

    // Grid System - Unified responsive grids and cards (v8.0.0)
    // Standardized grid layouts for all components (product, blog, benefits, testimonials)
    wp_enqueue_style(
        'forever-be-grid-system',
        FOREVER_BE_THEME_URI . '/assets/css/grid-system.css',
        array( 'forever-be-responsive-fixes' ),
        '8.0.0'
    );

    // Contrast Fixes - Accessibility (v9.1.0)
    // Système intelligent: Fond clair=Texte noir, Fond sombre=Texte blanc
    wp_enqueue_style(
        'forever-be-contrast-fixes',
        FOREVER_BE_THEME_URI . '/assets/css/contrast-fixes.css',
        array( 'forever-be-grid-system' ),
        '9.1.0'
    );

    // Color System Master v10.1.2 - Elena MARTINEZ Expert Accessibility
    // Solution DÉFINITIVE pour le mariage couleur background/texte sur TOUTES les pages
    // WCAG AAA compliance (ratio 7:1) sur fonds sombres et clairs
    // v10.1: Ajout Footer, Newsletter, Pages légales (CGV, Mentions, Confidentialité, FAQ, À Propos, Témoignages)
    // v10.1.1: Fix page À Propos - Hero section avec fond vert (titre/sous-titre en blanc)
    // v10.1.2: Section Témoignages À Propos (titre/sous-titre blanc, cards blanches) + Suppression stats footer
    wp_enqueue_style(
        'forever-be-color-system-master',
        FOREVER_BE_THEME_URI . '/assets/css/color-system-master.css',
        array( 'forever-be-contrast-fixes' ),
        '10.1.2'
    );

    // Unified Popups CSS - Compact Style v8.4.0 (Applied to all popups/forms)
    wp_enqueue_style(
        'forever-be-unified-popups',
        FOREVER_BE_THEME_URI . '/assets/css/unified-popups.css',
        array( 'forever-be-mobile-first' ),
        '8.4.0'
    );

    // Modals System CSS/JS (UX/UI Expert Refonte)
    wp_enqueue_style(
        'forever-be-modals',
        FOREVER_BE_THEME_URI . '/assets/css/modals.css',
        array( 'forever-be-unified-popups' ),
        FOREVER_BE_VERSION
    );

    wp_enqueue_script(
        'forever-be-modals-js',
        FOREVER_BE_THEME_URI . '/assets/js/modals.js',
        array( 'forever-be-main' ),
        FOREVER_BE_VERSION,
        true
    );

    // Premium Navigation CSS/JS (UX/UI Expert - Navigation Enhancement)
    wp_enqueue_style(
        'forever-be-navigation-premium',
        FOREVER_BE_THEME_URI . '/assets/css/navigation-premium.css',
        array( 'forever-be-premium-style' ),
        FOREVER_BE_VERSION
    );

    // Submenu Premium Fix CSS - Expert Frontend UX WorldClass v8.2.0
    // Fix: Visibilite des sous-menus Boutique/Blog sur desktop/tablette
    // Fix: Couleur texte sous-menus (force couleur sombre sur fond blanc)
    // Design e-commerce premium (Zara, Apple, Sephora inspired)
    wp_enqueue_style(
        'forever-be-submenu-premium-fix',
        FOREVER_BE_THEME_URI . '/assets/css/submenu-premium-fix.css',
        array( 'forever-be-navigation-premium' ),
        '8.2.0'
    );

    wp_enqueue_script(
        'forever-be-navigation-premium-js',
        FOREVER_BE_THEME_URI . '/assets/js/navigation-premium.js',
        array(),
        FOREVER_BE_VERSION,
        array(
            'in_footer' => true,
            'strategy'  => 'defer',
        )
    );

    // Main JavaScript
    wp_enqueue_script(
        'forever-be-main',
        FOREVER_BE_THEME_URI . '/assets/js/main.js',
        array(),
        FOREVER_BE_VERSION,
        array(
            'in_footer' => true,
            'strategy'  => 'defer',
        )
    );

    // Localize script for AJAX
    wp_localize_script( 'forever-be-main', 'foreverBeTheme', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'forever_be_nonce' ),
        'themeUrl' => FOREVER_BE_THEME_URI,
    ) );

    // Dark Mode Script (vanilla JS - no jQuery dependency)
    wp_enqueue_script(
        'forever-be-dark-mode',
        FOREVER_BE_THEME_URI . '/assets/js/dark-mode.js',
        array(),
        FOREVER_BE_VERSION,
        array(
            'in_footer' => true,
            'strategy'  => 'defer',
        )
    );

    // Dark Mode Styles - MODULAR ARCHITECTURE v2.0
    // Système modulaire: 8 fichiers organisés par logique métier
    // Fichier principal: dark-mode/dark-mode-modular.css (charge tous les modules)
    $dark_mode_enabled = isset( $_COOKIE['forever_be_dark_mode'] ) && $_COOKIE['forever_be_dark_mode'] === 'true';
    $dark_mode_css_url = FOREVER_BE_THEME_URI . '/assets/css/dark-mode/dark-mode-modular.css';

    if ( $dark_mode_enabled ) {
        // Charger immédiatement si dark mode déjà actif
        wp_enqueue_style(
            'forever-be-dark-mode-style',
            $dark_mode_css_url,
            array( 'forever-be-style' ),
            FOREVER_BE_VERSION
        );
    } else {
        // Sinon, enregistrer pour chargement dynamique via JS
        wp_register_style(
            'forever-be-dark-mode-style',
            $dark_mode_css_url,
            array( 'forever-be-style' ),
            FOREVER_BE_VERSION
        );
    }

    // Passer l'URL du CSS au script dark-mode pour chargement lazy
    wp_localize_script( 'forever-be-dark-mode', 'darkModeVars', array(
        'cssUrl'  => $dark_mode_css_url,
        'version' => FOREVER_BE_VERSION,
        'enabled' => $dark_mode_enabled,
    ) );

    // Dark Mode Visibility Fix CSS - Global corrections for all pages v1.0.0
    // Fixes text visibility issues in dark mode across all templates
    wp_enqueue_style(
        'forever-be-dark-mode-visibility-fix',
        FOREVER_BE_THEME_URI . '/assets/css/dark-mode-visibility-fix.css',
        array( 'forever-be-style' ),
        FOREVER_BE_VERSION
    );

    // Text Contrast Mobile Fixes CSS - Load on all pages for mobile accessibility
    wp_enqueue_style(
        'forever-be-text-contrast-mobile',
        FOREVER_BE_THEME_URI . '/assets/css/text-contrast-mobile-fix.css',
        array( 'forever-be-style' ),
        FOREVER_BE_VERSION
    );

    // COLOR HARMONY CSS - Expert UX Refonte v2.0
    // Harmonisation couleurs texte et backgrounds cartes
    // WCAG AA Compliant - Lisibilité optimale
    // Chargé EN DERNIER pour override tous les styles inline
    wp_enqueue_style(
        'forever-be-color-harmony',
        FOREVER_BE_THEME_URI . '/assets/css/color-harmony.css',
        array( 'forever-be-color-system-master', 'forever-be-text-contrast-mobile' ),
        '2.0.0'
    );

    // PREMIUM REFONTE 2024 - Expert UX Front-end 20 ans
    // CSS unifié pour score 5/5 : Design System complet
    // Cards, Buttons, Forms, Layouts, Pages
    // Chargé EN DERNIER pour établir le style final
    wp_enqueue_style(
        'forever-be-premium-refonte-2024',
        FOREVER_BE_THEME_URI . '/assets/css/premium-refonte-2024.css',
        array( 'forever-be-color-harmony' ),
        '2.0.0'
    );

    // TEMPLATE OVERRIDES - Corrections styles inline
    // Force l'application du design system sur tous les templates
    // Corrige page-contact, single-product, page-affiliation, etc.
    wp_enqueue_style(
        'forever-be-template-overrides',
        FOREVER_BE_THEME_URI . '/assets/css/template-overrides.css',
        array( 'forever-be-premium-refonte-2024' ),
        '2.0.0'
    );

    // HEADER REFONTE - Expert UX Front-end 20 ans
    // Remplace les styles inline du header.php
    // Top bar, Navigation, Mobile menu, Search panel
    wp_enqueue_style(
        'forever-be-header-refonte',
        FOREVER_BE_THEME_URI . '/assets/css/header-refonte.css',
        array( 'forever-be-template-overrides' ),
        '2.0.0'
    );

    // FOOTER REFONTE - Expert UX Front-end 20 ans
    // Footer epure, newsletter, scroll-to-top
    // Dark mode support inclus
    wp_enqueue_style(
        'forever-be-footer-refonte',
        FOREVER_BE_THEME_URI . '/assets/css/footer-refonte.css',
        array( 'forever-be-header-refonte' ),
        '2.0.0'
    );

    // PAGES REFONTE - Phase 3 Expert UX Front-end
    // Override styles inline: page-contact, single-product, etc.
    // Epuration complete des templates PHP
    wp_enqueue_style(
        'forever-be-pages-refonte',
        FOREVER_BE_THEME_URI . '/assets/css/pages-refonte.css',
        array( 'forever-be-footer-refonte' ),
        '2.0.0'
    );

    // ================================================================
    // FINAL FIX: Submenu Text Visibility (MUST BE LOADED LAST!)
    // ================================================================
    // Fixes intermittent white text on white background issue in dropdowns
    // This file uses ultra-high specificity selectors with !important
    // It MUST be the last CSS file to override all other conflicting styles
    // Dependency on pages-refonte ensures it loads after all other theme CSS
    wp_enqueue_style(
        'forever-be-submenu-text-fix-final',
        FOREVER_BE_THEME_URI . '/assets/css/submenu-text-fix-final.css',
        array( 'forever-be-pages-refonte' ),
        '1.0.2'
    );

    // Instant Search Script (vanilla JS - no jQuery dependency)
    wp_enqueue_script(
        'forever-be-search',
        FOREVER_BE_THEME_URI . '/assets/js/search.js',
        array( 'forever-be-main' ),
        FOREVER_BE_VERSION,
        array(
            'in_footer' => true,
            'strategy'  => 'defer',
        )
    );

    // Comment reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'forever_be_enqueue_scripts' );

/**
 * Unify CSS versions to prevent loading order issues
 * All theme CSS files will have the same version number
 * This ensures they all reload together when updated
 *
 * @since 8.0.4
 */
function forever_be_unify_css_versions( $src, $handle ) {
    // Only modify theme CSS files
    if ( strpos( $handle, 'forever-be' ) !== false && strpos( $src, '.css' ) !== false ) {
        // Remove existing version
        $src = remove_query_arg( 'ver', $src );
        // Add unified version
        $src = add_query_arg( 'ver', FOREVER_BE_CSS_VERSION, $src );
    }
    return $src;
}
add_filter( 'style_loader_src', 'forever_be_unify_css_versions', 10, 2 );

/**
 * Register Widget Areas
 */
function forever_be_widgets_init() {

    // Footer Widget Area 1
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 1', 'forever-be' ),
        'id'            => 'footer-1',
        'description'   => esc_html__( 'Première colonne du footer', 'forever-be' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );

    // Footer Widget Area 2
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 2', 'forever-be' ),
        'id'            => 'footer-2',
        'description'   => esc_html__( 'Deuxième colonne du footer', 'forever-be' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );

    // Footer Widget Area 3
    register_sidebar( array(
        'name'          => esc_html__( 'Footer 3', 'forever-be' ),
        'id'            => 'footer-3',
        'description'   => esc_html__( 'Troisième colonne du footer', 'forever-be' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 'forever_be_widgets_init' );

/**
 * Include Required Files
 */

// Custom Post Types
if ( ! function_exists( 'forever_be_register_product_post_type' ) ) {
    require_once FOREVER_BE_INC_DIR . '/post-types.php';
}

// Taxonomies
if ( ! function_exists( 'forever_be_register_product_category_taxonomy' ) ) {
    require_once FOREVER_BE_INC_DIR . '/taxonomies.php';
}

// Theme Options & Customizer
require_once FOREVER_BE_INC_DIR . '/customizer.php';

// Custom Functions
require_once FOREVER_BE_INC_DIR . '/template-functions.php';

// Template Tags
require_once FOREVER_BE_INC_DIR . '/template-tags.php';

// Contact System (Enterprise-Grade Form & CRM)
if ( ! class_exists( 'Forever_BE_Contact_System' ) ) {
    require_once FOREVER_BE_INC_DIR . '/contact-system.php';
}

// Comment and Rating Handler
if ( ! function_exists( 'forever_be_handle_custom_comment' ) ) {
    require_once FOREVER_BE_INC_DIR . '/comment-handler.php';
}

// AJAX Handlers - Load if not already loaded by plugin
if ( ! function_exists( 'forever_be_filter_products' ) ) {
    require_once FOREVER_BE_INC_DIR . '/ajax-handlers.php';
} else {
    // Plugin loaded base handlers, load modal handlers separately
    require_once FOREVER_BE_INC_DIR . '/ajax-handlers-modals.php';
}

// AJAX Modules (v8.0.0) - Modular architecture (coexists with legacy)
if ( file_exists( FOREVER_BE_INC_DIR . '/ajax/ajax-handlers-products.php' ) ) {
    // Commented out to avoid duplicate function definitions
    // Will be activated after full migration from ajax-handlers.php
    // require_once FOREVER_BE_INC_DIR . '/ajax/ajax-handlers-products.php';
}

// AJAX Shop Filters (P5 - Refonte 2026) - Filtrage AJAX sans rechargement page
if ( file_exists( FOREVER_BE_INC_DIR . '/ajax/ajax-shop-filters.php' ) ) {
    require_once FOREVER_BE_INC_DIR . '/ajax/ajax-shop-filters.php';
}

// Custom Walker for Navigation
require_once FOREVER_BE_INC_DIR . '/class-walker-nav-menu.php';

// Product Categories Configuration (E-commerce Expert Structure)
if ( ! function_exists( 'forever_be_get_product_categories_structure' ) ) {
    require_once FOREVER_BE_INC_DIR . '/product-categories-config.php';
}

// Admin Dashboard Widget (CRM Overview)
if ( class_exists( 'Forever_BE_Admin_Dashboard' ) ) {
    // Plugin is active, do nothing (or use plugin's version if needed)
} else {
    require_once get_template_directory() . '/inc/admin-dashboard-widget.php';
    require_once get_template_directory() . '/inc/price-automation.php';
}

// Admin Category Images Manager (v9.0.0) - Manage hero images for product categories
require_once get_template_directory() . '/inc/admin-category-images.php';

/**
 * Security: Remove WordPress version info and other meta
 * Note: Additional security headers are in performance-optimizations.php
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * CRITICAL FIX: Force dropdown menu text visibility
 * Uses both inline CSS AND JavaScript to ensure colors are applied
 *
 * @since 9.2.0
 */
function forever_be_dropdown_menu_fix() {
    ?>
    <style id="dropdown-menu-text-fix">
    /* FORCE Blog menu dropdown text colors - Light Mode */
    /* Target BOTH the links AND their child spans */
    .mega-menu-blog a,
    .mega-menu-blog a.menu-link,
    .mega-menu-blog a.mega-menu-header,
    .mega-menu-type-blog a,
    .mega-menu-type-blog a.menu-link,
    .mega-menu-type-blog a.mega-menu-header,
    .mega-menu-blog a span,
    .mega-menu-blog a .menu-item-text,
    .mega-menu-type-blog a span,
    .mega-menu-type-blog a .menu-item-text,
    .site-header .mega-menu-blog a,
    .site-header .mega-menu-blog a span,
    .site-header .mega-menu-type-blog a,
    .site-header .mega-menu-type-blog a span {
        color: #374151 !important;
        text-decoration: none !important;
    }
    /* Blog menu headers - green */
    .mega-menu-blog a.mega-menu-header,
    .mega-menu-blog a.mega-menu-header span,
    .mega-menu-blog a.mega-menu-header .menu-item-text,
    .mega-menu-type-blog a.mega-menu-header,
    .mega-menu-type-blog a.mega-menu-header span,
    .mega-menu-type-blog a.mega-menu-header .menu-item-text {
        color: #1e5a3e !important;
        font-weight: 600 !important;
    }
    /* FORCE Blog menu dropdown text colors - Dark Mode */
    html.dark .mega-menu-blog a,
    html.dark .mega-menu-blog a span,
    html.dark .mega-menu-blog a .menu-item-text,
    html.dark .mega-menu-type-blog a,
    html.dark .mega-menu-type-blog a span,
    html.dark .mega-menu-type-blog a .menu-item-text,
    html.dark .site-header .mega-menu-blog a,
    html.dark .site-header .mega-menu-blog a span,
    html.dark .site-header .mega-menu-type-blog a,
    html.dark .site-header .mega-menu-type-blog a span {
        color: #94a3b8 !important;
    }
    html.dark .mega-menu-blog a.mega-menu-header,
    html.dark .mega-menu-blog a.mega-menu-header span,
    html.dark .mega-menu-blog a.mega-menu-header .menu-item-text,
    html.dark .mega-menu-type-blog a.mega-menu-header,
    html.dark .mega-menu-type-blog a.mega-menu-header span,
    html.dark .mega-menu-type-blog a.mega-menu-header .menu-item-text {
        color: #fbbf24 !important;
    }
    </style>
    <script>
    // Force dropdown menu colors via JavaScript
    (function() {
        function fixBlogMenuColors() {
            var isDark = document.documentElement.classList.contains('dark');
            var color = isDark ? '#94a3b8' : '#374151';
            var headerColor = isDark ? '#fbbf24' : '#1e5a3e';

            document.querySelectorAll('.mega-menu-blog a, .mega-menu-type-blog a').forEach(function(link) {
                var targetColor = link.classList.contains('mega-menu-header') ? headerColor : color;
                // Set color on the link itself
                link.style.setProperty('color', targetColor, 'important');
                link.style.setProperty('text-decoration', 'none', 'important');
                // CRITICAL: Also set color on ALL child spans (including .menu-item-text)
                link.querySelectorAll('span, .menu-item-text').forEach(function(span) {
                    span.style.setProperty('color', targetColor, 'important');
                });
            });
        }

        // Run on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fixBlogMenuColors);
        } else {
            fixBlogMenuColors();
        }

        // Run again after a delay (for dynamically loaded content)
        setTimeout(fixBlogMenuColors, 1000);
        setTimeout(fixBlogMenuColors, 3000);

        // Run when hovering over Blog menu
        document.addEventListener('mouseover', function(e) {
            if (e.target.closest('.mega-menu-blog, .mega-menu-type-blog')) {
                fixBlogMenuColors();
            }
        });

        // Run when dark mode changes
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    fixBlogMenuColors();
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    })();
    </script>
    <?php
}
add_action( 'wp_head', 'forever_be_dropdown_menu_fix', 9999 );

// Note: defer_scripts and preload_fonts moved to performance-optimizations.php

/**
 * Clear featured products transient when product is saved/deleted
 * WordPress Dev Expert: Ensure cache invalidation
 *
 * @since 7.0.0
 */
function forever_be_clear_featured_products_cache( $post_id ) {
    if ( get_post_type( $post_id ) === 'product' ) {
        delete_transient( 'forever_be_featured_products_home' );
    }
}
add_action( 'save_post', 'forever_be_clear_featured_products_cache' );
add_action( 'delete_post', 'forever_be_clear_featured_products_cache' );
add_action( 'trash_post', 'forever_be_clear_featured_products_cache' );

/**
 * Custom Excerpt Length
 */
function forever_be_excerpt_length( $length ) {
    return 25;
}
add_filter( 'excerpt_length', 'forever_be_excerpt_length', 999 );

/**
 * Custom Excerpt More
 */
function forever_be_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'forever_be_excerpt_more' );

/**
 * Helper function to check admin script permissions
 * Security: All admin scripts require manage_options capability + nonce verification
 *
 * @since 1.0.1
 * @since 1.0.2 Added nonce verification for CSRF protection
 *
 * @param string $action The action name for nonce verification.
 */
function forever_be_check_admin_script_permission( $action = '' ) {
    // Check capability
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die(
            esc_html__( 'Vous n\'avez pas les permissions nécessaires pour exécuter cette action.', 'forever-be' ),
            esc_html__( 'Accès non autorisé', 'forever-be' ),
            array( 'response' => 403 )
        );
    }

    // Verify nonce if action provided
    if ( ! empty( $action ) ) {
        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), $action ) ) {
            wp_die(
                esc_html__( 'Vérification de sécurité échouée. Veuillez réessayer.', 'forever-be' ),
                esc_html__( 'Erreur de sécurité', 'forever-be' ),
                array( 'response' => 403 )
            );
        }
    }
}

/**
 * Hook for Page Creation Script
 * Usage: admin.php?create_pages=run&_wpnonce=XXX
 */
function forever_be_load_page_creator() {
    if ( isset( $_GET['create_pages'] ) && $_GET['create_pages'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_create_pages' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/create-pages.php';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_page_creator' );

/**
 * Hook for Product Migration Script
 * Usage: admin.php?migrate_products=run&_wpnonce=XXX
 */
function forever_be_load_product_migrator() {
    if ( isset( $_GET['migrate_products'] ) && $_GET['migrate_products'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_migrate_products' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/migrate-products.php';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_product_migrator' );

/**
 * Hook for Blog Posts Migration Script
 * Usage: admin.php?migrate_blog_posts=run&_wpnonce=XXX
 */
function forever_be_load_blog_migrator() {
    if ( isset( $_GET['migrate_blog_posts'] ) && $_GET['migrate_blog_posts'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_migrate_blog' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/migrate-blog-posts.php';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_blog_migrator' );

/**
 * Hook for Products Cleanup Script
 * Usage: admin.php?cleanup_products=run&_wpnonce=XXX
 */
function forever_be_load_products_cleanup() {
    if ( isset( $_GET['cleanup_products'] ) && $_GET['cleanup_products'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_cleanup_products' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/cleanup-products.php';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_products_cleanup' );

/**
 * Hook for Force Create Taxonomies Script
 * Usage: admin.php?force_create_taxonomies=run&_wpnonce=XXX
 */
function forever_be_load_force_taxonomies() {
    if ( isset( $_GET['force_create_taxonomies'] ) && $_GET['force_create_taxonomies'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_force_taxonomies' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/force-create-taxonomies.php';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_force_taxonomies' );

/**
 * Hook for Diagnose and Fix Script
 * Usage: admin.php?diagnose_and_fix=run&_wpnonce=XXX
 */
function forever_be_load_diagnose_fix() {
    if ( isset( $_GET['diagnose_and_fix'] ) && $_GET['diagnose_and_fix'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_diagnose_fix' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/diagnose-and-fix.php';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_diagnose_fix' );

/**
 * Hook for Professional Products Import Script
 * Usage: admin.php?import_products=run&_wpnonce=XXX
 */
function forever_be_load_products_import() {
    if ( isset( $_GET['import_products'] ) && $_GET['import_products'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_import_products' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/import-products.php';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_products_import' );

/**
 * Hook for Health Categories Sync Script
 * Usage: admin.php?sync_health_categories=run&_wpnonce=XXX
 */
function forever_be_load_health_categories_sync() {
    if ( isset( $_GET['sync_health_categories'] ) && $_GET['sync_health_categories'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_sync_health_categories' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/sync-health-categories.php';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_health_categories_sync' );

/**
 * Hook for Products Categories Update Script
 * Usage: admin.php?update_products_categories=run&_wpnonce=XXX
 */
function forever_be_load_products_categories_update() {
    if ( isset( $_GET['update_products_categories'] ) && $_GET['update_products_categories'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_update_products_categories' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/update-products-categories.php';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_products_categories_update' );

/**
 * Hook for Menu Creation Script
 * Usage: admin.php?create_menus=run&_wpnonce=XXX
 */
function forever_be_load_menu_creator() {
    if ( isset( $_GET['create_menus'] ) && $_GET['create_menus'] === 'run' ) {
        forever_be_check_admin_script_permission( 'forever_be_create_menus' );
        require_once FOREVER_BE_THEME_DIR . '/scripts/create-menus.php';
        // Run the function immediately since we are inside the script
        forever_be_create_default_menus();
        echo 'Menus created successfully!';
        exit;
    }
}
add_action( 'admin_init', 'forever_be_load_menu_creator' );

/**
 * ============================================================================
 * OPTIMIZED GOOGLE FONTS LOADING
 * Performance Expert v9.0 - Sophie LEGRAND
 * ============================================================================
 */
function forever_be_optimized_fonts() {
    ?>
    <!-- Optimized Fonts Loading Strategy -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"></noscript>
    <?php
}

/**
 * ============================================================================
 * FOREVER BE CONTENT MANAGER
 * Système de gestion de contenu intégré - Version 8.0.0
 * ============================================================================
 */
require_once get_template_directory() . '/inc/content-manager/class-content-manager.php';
require_once get_template_directory() . '/inc/content-manager/helpers.php';

/**
 * ============================================================================
 * QUIZ DIAGNOSTIC PERSONNALISÉ - Fonctionnalité Différenciante N°1
 * Version 1.0.0 - Refonte Premium 2025
 * ============================================================================
 */
require_once get_template_directory() . '/inc/quiz-diagnostic.php';

/**
 * ============================================================================
 * SYSTÈME D'EMAILS PERSONNALISÉS - Intégration Quiz + Experts IA
 * Version 1.0.0 - Refonte Premium 2025
 *
 * Ce système génère des emails personnalisés avec:
 * - Base de données complète des produits FLP (97 produits)
 * - 7 experts virtuels spécialisés (20 ans d'expérience chacun)
 * - Génération d'emails persuasifs basés sur le profil quiz
 * ============================================================================
 */
require_once get_template_directory() . '/inc/quiz-email-integration.php';
