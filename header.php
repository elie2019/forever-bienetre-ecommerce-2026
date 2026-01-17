<?php
/**
 * The header for our theme - Marketing Optimized v7.0.0
 *
 * @package Forever_BE_Premium
 * @since 1.0.0
 * @version 7.0.0 - 5 Experts Refactoring
 */

// Security: Direct access prevention
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Action: Before header output
 * @since 7.0.0
 */
do_action( 'forever_be_before_header' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <!-- Performance Expert v8.0 - Critical Resource Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    <?php if ( function_exists( 'is_woocommerce' ) ) : ?>
    <link rel="dns-prefetch" href="//www.foreverliving.fr">
    <?php endif; ?>

    <?php
    /**
     * SEO Expert v7.0 - Dynamic meta tags and Schema.org
     * Dr. Akiko TANAKA optimizations
     */
    if ( is_front_page() ) :
    ?>
        <!-- SEO Meta Tags Homepage - SEO Expert v7.0 -->
        <meta name="description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : 'Forever Bien-Être : Distributeur officiel Forever Living Products. 150+ produits naturels à l\'Aloe Vera certifiés IASC. Livraison 48-72h.' ); ?>">
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
        <link rel="canonical" href="<?php echo esc_url( home_url( '/' ) ); ?>">

        <!-- Open Graph (Facebook, LinkedIn) -->
        <meta property="og:type" content="website">
        <meta property="og:title" content="Forever Bien-Être | Produits Naturels Aloe Vera Certifiés IASC">
        <meta property="og:description" content="Découvrez 150+ produits bien-être 100% naturels Forever Living. Nutrition, beauté, perte de poids. Certifié IASC. Livraison rapide.">
        <meta property="og:url" content="<?php echo esc_url( home_url( '/' ) ); ?>">
        <meta property="og:image" content="<?php echo esc_url( get_template_directory_uri() . '/assets/images/og-image-home.jpg' ); ?>">
        <meta property="og:locale" content="fr_FR">
        <meta property="og:site_name" content="Forever Bien-Être">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Forever Bien-Être | Produits Naturels Aloe Vera">
        <meta name="twitter:description" content="150+ produits bien-être 100% naturels Forever Living certifiés IASC. Livraison 48-72h.">
        <meta name="twitter:image" content="<?php echo esc_url( get_template_directory_uri() . '/assets/images/twitter-card-home.jpg' ); ?>">

        <!-- Schema.org Organization -->
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          "name": "Forever Bien-Être",
          "url": "<?php echo esc_url( home_url( '/' ) ); ?>",
          "logo": "<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>",
          "description": "Distributeur officiel Forever Living Products en France. Produits naturels à l'Aloe Vera certifiés IASC.",
          "sameAs": [
            "https://www.facebook.com/foreverbienetre",
            "https://www.instagram.com/foreverbienetre"
          ],
          "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+33-6-00-00-00-00",
            "contactType": "customer service",
            "availableLanguage": ["French"],
            "areaServed": "FR"
          },
          "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "3247"
          }
        }
        </script>

        <!-- Schema.org WebPage -->
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "WebPage",
          "name": "Accueil - Forever Bien-Être",
          "description": "Page d'accueil Forever Bien-Être : découvrez nos produits naturels Aloe Vera et rejoignez notre communauté mondiale.",
          "url": "<?php echo esc_url( home_url( '/' ) ); ?>",
          "inLanguage": "fr-FR",
          "isPartOf": {
            "@type": "WebSite",
            "name": "Forever Bien-Être",
            "url": "<?php echo esc_url( home_url( '/' ) ); ?>"
          }
        }
        </script>

        <!-- Schema.org SiteNavigationElement - SEO Expert v7.0 -->
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "SiteNavigationElement",
          "name": "Menu Principal Forever Bien-Être",
          "url": "<?php echo esc_url( home_url( '/' ) ); ?>",
          "hasPart": [
            {"@type": "SiteNavigationElement", "name": "Accueil", "url": "<?php echo esc_url( home_url( '/' ) ); ?>"},
            {"@type": "SiteNavigationElement", "name": "Boutique", "url": "<?php echo esc_url( get_post_type_archive_link( 'product' ) ?: home_url( '/boutique/' ) ); ?>"},
            {"@type": "SiteNavigationElement", "name": "Blog", "url": "<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>"},
            {"@type": "SiteNavigationElement", "name": "Contact", "url": "<?php echo esc_url( home_url( '/contact/' ) ); ?>"}
          ]
        }
        </script>
    <?php endif; ?>

    <?php
    // SEO Expert: Dynamic meta for single posts/pages
    if ( is_singular() && ! is_front_page() ) :
        global $post;
        $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 25 );
        ?>
        <meta name="description" content="<?php echo esc_attr( $excerpt ); ?>">
        <link rel="canonical" href="<?php echo esc_url( get_permalink() ); ?>">
        <meta property="og:type" content="article">
        <meta property="og:title" content="<?php echo esc_attr( get_the_title() ); ?> | <?php bloginfo( 'name' ); ?>">
        <meta property="og:description" content="<?php echo esc_attr( $excerpt ); ?>">
        <meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
        <?php if ( has_post_thumbnail() ) : ?>
        <meta property="og:image" content="<?php echo esc_url( get_the_post_thumbnail_url( $post, 'large' ) ); ?>">
        <?php endif; ?>
    <?php endif; ?>

    <?php wp_head(); ?>
</head>

<body <?php body_class( 'antialiased text-text-medium bg-brand-gray font-sans dark:bg-slate-900 dark:text-slate-100 transition-colors duration-300 has-topbar' ); ?>>
<?php wp_body_open(); ?>

<a class="skip-to-main-content" href="#primary" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;"><?php esc_html_e( 'Aller au contenu principal', 'forever-be' ); ?></a>
<style>.skip-to-main-content:focus{position:fixed!important;left:1rem!important;top:1rem!important;width:auto!important;height:auto!important;overflow:visible!important;padding:0.75rem 1.5rem;background:#006241;color:#fff;z-index:99999;border-radius:0.375rem;text-decoration:none;font-weight:600;}.breadcrumb,nav[aria-label*="Ariane"],nav[aria-label*="ariane"],nav.breadcrumb,[class*="breadcrumb"]{display:none!important;}</style>

<!-- Top Bar with Social Proof & Offers -->
<div class="top-bar hidden lg:block" role="banner" aria-label="<?php esc_attr_e( 'Informations promotionnelles', 'forever-be' ); ?>">
	<div class="container mx-auto px-6">
		<div class="top-bar__container">
			<div class="top-bar__left">
				<!-- Social Proof - Marketing Expert v7.0 -->
				<span class="top-bar__item">
					<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
						<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
					</svg>
					<strong>4.9/5</strong> <?php esc_html_e( 'sur 3 247+ avis vérifiés', 'forever-be' ); ?>
				</span>

				<!-- Urgency Element - Marketing Expert v7.0 -->
				<?php if ( get_theme_mod( 'forever_be_top_bar_promo_visible', 1 ) != 0 ) : ?>
					<span class="top-bar__promo-flash">
						<?php echo esc_html( get_theme_mod( 'forever_be_top_bar_promo', '🎁 Code Promo -5% • Offre limitée' ) ); ?>
					</span>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'forever_be_top_bar_delivery_visible', 1 ) != 0 ) : ?>
					<span><?php echo esc_html( get_theme_mod( 'forever_be_top_bar_delivery', '🚚 Livraison GRATUITE dès 50€' ) ); ?></span>
				<?php endif; ?>

				<!-- Trust Badge - IASC Certified -->
				<span class="top-bar__trust-badge">
					<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
						<path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
					</svg>
					<?php esc_html_e( 'Certifié IASC', 'forever-be' ); ?>
				</span>
			</div>
			<div class="top-bar__right">
				<?php
				/**
				 * Filter: Top bar right items
				 * @since 7.0.0
				 * @param array $items Array of top bar right items
				 */
				/**
				 * v8.0.0 - WhatsApp removed for privacy compliance
				 */
				$top_bar_right_items = apply_filters( 'forever_be_top_bar_right_items', array(
					'support'  => get_theme_mod( 'forever_be_top_bar_support_visible', 1 ),
				) );

				if ( ! empty( $top_bar_right_items['support'] ) ) :
					?>
					<span><?php echo esc_html( get_theme_mod( 'forever_be_top_bar_support', '💬 Support 7j/7' ) ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- ========== MOBILE TOP BAR (Scrolling Banner) ========== -->
<div class="mobile-top-bar lg:hidden" id="mobile-top-bar">
	<div class="mobile-top-bar__track">
		<div class="mobile-top-bar__content">
			<span class="mobile-top-bar__item">
				<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
				<strong>4.9/5</strong> sur 3 247+ avis
			</span>
			<span class="mobile-top-bar__item mobile-top-bar__promo">
				🎁 Code Promo -5%
			</span>
			<span class="mobile-top-bar__item">
				🚚 Livraison GRATUITE dès 50€
			</span>
			<span class="mobile-top-bar__item">
				<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
				Certifié IASC
			</span>
			<span class="mobile-top-bar__item">
				💬 Support 7j/7
			</span>
		</div>
		<!-- Duplicate for seamless loop -->
		<div class="mobile-top-bar__content">
			<span class="mobile-top-bar__item">
				<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
				<strong>4.9/5</strong> sur 3 247+ avis
			</span>
			<span class="mobile-top-bar__item mobile-top-bar__promo">
				🎁 Code Promo -5%
			</span>
			<span class="mobile-top-bar__item">
				🚚 Livraison GRATUITE dès 50€
			</span>
			<span class="mobile-top-bar__item">
				<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
				Certifié IASC
			</span>
			<span class="mobile-top-bar__item">
				💬 Support 7j/7
			</span>
		</div>
	</div>
</div>

<!-- ========== MOBILE HEADER (Style FLP) - Premium v8.0 ========== -->
<header class="site-header-mobile lg:hidden fixed left-0 right-0 z-50 shadow-lg" style="height: 60px; top: 28px; background: linear-gradient(135deg, #1e5a3e 0%, #2d8659 50%, #1e5a3e 100%);">
    <div class="flex items-center justify-between h-full px-4">
        <!-- Left: Hamburger Menu -->
        <button id="flp-menu-toggle" class="flp-hamburger p-2" aria-label="<?php esc_attr_e( 'Menu', 'forever-be' ); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <!-- Center: Logo - White for Premium Header -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flp-logo">
            <span style="font-family: 'Times New Roman', serif; font-size: 20px; font-weight: 400; letter-spacing: 2px; color: #ffffff;">Forever</span><sup style="font-size: 11px; color: #fbbf24; font-weight: 600;">BE</sup>
        </a>

        <!-- Right: Icons - White for Premium Header -->
        <div class="flex items-center gap-2">
            <!-- Search Toggle -->
            <button id="flp-search-toggle" class="p-2" aria-label="<?php esc_attr_e( 'Rechercher', 'forever-be' ); ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>

            <!-- Dark Mode Toggle Mobile -->
            <button id="dark-mode-toggle-mobile" class="p-2 dark-mode-btn" aria-label="<?php esc_attr_e( 'Mode Sombre', 'forever-be' ); ?>">
                <!-- Sun Icon (visible in dark mode) -->
                <svg class="dark-mode-icon-sun w-5 h-5 hidden" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <!-- Moon Icon (visible in light mode) -->
                <svg class="dark-mode-icon-moon w-5 h-5" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>

            <!-- Account Link -->
            <a href="<?php echo esc_url( home_url( '/mon-compte/' ) ); ?>" class="p-2" aria-label="<?php esc_attr_e( 'Mon compte', 'forever-be' ); ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
        </div>
    </div>
</header>

<!-- ========== MOBILE SEARCH PANEL ========== -->
<div id="flp-search-panel" class="flp-search-panel">
    <div class="flp-search-header">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="flp-search-form">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="search" name="s" id="flp-search-input" placeholder="<?php esc_attr_e( 'Rechercher un produit...', 'forever-be' ); ?>" autocomplete="off" required>
            <button type="submit" class="flp-search-submit">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#006241" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </form>
        <button id="flp-search-close" class="flp-search-close-btn" aria-label="<?php esc_attr_e( 'Fermer', 'forever-be' ); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="flp-search-suggestions">
        <p class="flp-search-hint">Recherches populaires :</p>
        <div class="flp-search-tags">
            <a href="<?php echo esc_url( home_url( '/?s=aloe+vera' ) ); ?>">Aloe Vera</a>
            <a href="<?php echo esc_url( home_url( '/?s=forever+c9' ) ); ?>">Forever C9</a>
            <a href="<?php echo esc_url( home_url( '/?s=gel' ) ); ?>">Gel</a>
            <a href="<?php echo esc_url( home_url( '/?s=minceur' ) ); ?>">Minceur</a>
            <a href="<?php echo esc_url( home_url( '/?s=soins' ) ); ?>">Soins</a>
        </div>
    </div>
</div>

<!-- ========== MOBILE MENU PANEL (Style FLP) ========== -->
<div id="flp-mobile-menu" class="flp-menu-panel">
    <div class="flp-menu-header">
        <span style="font-family: 'Times New Roman', serif; font-size: 18px; letter-spacing: 2px; color: #333;">Forever</span><sup style="font-size: 10px; color: #006241; font-weight: 600;">BE</sup>
        <button id="flp-menu-close" class="flp-menu-close-btn" aria-label="<?php esc_attr_e( 'Fermer', 'forever-be' ); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <nav class="flp-menu-nav">
        <ul class="flp-menu-list">
            <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a></li>

            <!-- Boutique avec sous-menu -->
            <li class="flp-has-submenu">
                <a href="#" class="flp-submenu-toggle">
                    <span>Boutique</span>
                    <svg class="flp-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
                <ul class="flp-submenu">
                    <li><a href="<?php echo esc_url( home_url( '/produit/' ) ); ?>">Tous les produits</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=boissons' ) ); ?>">Boissons Aloe Vera</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=soins-du-corps' ) ); ?>">Soins du Corps</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=soins-du-visage' ) ); ?>">Soins du Visage</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=nutrition-essentielle' ) ); ?>">Nutrition</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=gestion-du-poids' ) ); ?>">Gestion du Poids</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=sante' ) ); ?>">Santé</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=produits-de-la-ruche' ) ); ?>">Produits de la Ruche</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=hygiene' ) ); ?>">Hygiène</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=programmes-fit' ) ); ?>">Programmes F.I.T.</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=packs' ) ); ?>">Packs & Coffrets</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/produit/?product_category[]=parfums' ) ); ?>">Parfums</a></li>
                </ul>
            </li>

            <!-- Blog avec sous-menu -->
            <li class="flp-has-submenu">
                <a href="#" class="flp-submenu-toggle">
                    <span>Blog</span>
                    <svg class="flp-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
                <ul class="flp-submenu">
                    <li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Tous les articles</a></li>
                    <?php
                    // Récupérer dynamiquement les catégories de blog
                    $blog_categories = get_categories( array(
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                        'hide_empty' => false,
                        'exclude'    => array( 1 ), // Exclure "Non classé"
                        'parent'     => 0, // Seulement les catégories parentes
                        'number'     => 8, // Limiter à 8 catégories
                    ) );

                    if ( ! empty( $blog_categories ) ) :
                        foreach ( $blog_categories as $category ) :
                            ?>
                            <li><a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
                            <?php
                        endforeach;
                    else :
                        // Fallback si aucune catégorie n'existe
                        ?>
                        <li><a href="<?php echo esc_url( home_url( '/category/bien-etre/' ) ); ?>">Bien-être</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/category/nutrition/' ) ); ?>">Nutrition</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/category/beaute/' ) ); ?>">Beauté</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/category/conseils/' ) ); ?>">Conseils</a></li>
                        <?php
                    endif;
                    ?>
                    <li><a href="<?php echo esc_url( home_url( '/temoignages/' ) ); ?>">Témoignages</a></li>
                </ul>
            </li>

            <!-- FitTrack Pro avec sous-menu -->
            <li class="flp-has-submenu">
                <a href="#" class="flp-submenu-toggle">
                    <span>💪 FitTrack Pro</span>
                    <svg class="flp-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
                <ul class="flp-submenu">
                    <li><a href="<?php echo esc_url( home_url( '/fittrack-pricing/' ) ); ?>">📋 Pricing & Plans</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/fittrack-dashboard/' ) ); ?>">📊 Dashboard</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/fittrack-nutrition/' ) ); ?>">🥗 Nutrition Tracker</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/fittrack-workouts/' ) ); ?>">🏋️ Workout Logger</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/fittrack-progress/' ) ); ?>">📈 Progress Tracking</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/fittrack-goals/' ) ); ?>">🎯 Goals Manager</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/fittrack-settings/' ) ); ?>">⚙️ Settings</a></li>
                </ul>
            </li>

            <li><a href="<?php echo esc_url( home_url( '/a-propos/' ) ); ?>">À Propos</a></li>
            <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
            <li><a href="<?php echo esc_url( home_url( '/affiliation/' ) ); ?>" class="flp-menu-highlight">Devenir FBO</a></li>
        </ul>
    </nav>
</div>
<div id="flp-menu-overlay" class="flp-menu-overlay"></div>

<!-- ========== DESKTOP HEADER - Premium v8.0 ========== -->
<header class="site-header site-header--with-topbar hidden lg:block fixed left-0 right-0 z-50 transition-all duration-300" style="height: var(--header-height-desktop); background: linear-gradient(135deg, #1e5a3e 0%, #2d8659 50%, #1e5a3e 100%); box-shadow: 0 4px 20px rgba(30, 90, 62, 0.3);">
    <div class="container h-full">
        <div class="flex items-center justify-between h-full">

            <!-- Logo -->
            <div class="site-branding">
                <?php
                if ( has_custom_logo() ) :
                    the_custom_logo();
                else :
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-block" aria-label="<?php bloginfo( 'name' ); ?>">
                        <svg width="240" height="60" viewBox="0 0 240 60" aria-label="Forever logo">
                            <title><?php bloginfo( 'name' ); ?></title>
                            <text x="120" y="40" text-anchor="middle" style="font-family: 'Dancing Script', cursive; font-size: 36px; font-weight: 700; letter-spacing: 1px;" fill="#ffffff">
                                <tspan>Forever</tspan>
                                <tspan baseline-shift="super" font-size="0.6em" dy="-0.4em" dx="-0.3em" fill="#fbbf24">BE</tspan>
                            </text>
                        </svg>
                    </a>
                    <?php
                endif;
                ?>
            </div>

            <!-- Desktop Navigation -->
            <nav class="main-navigation flex items-center gap-10" aria-label="<?php esc_attr_e( 'Menu principal', 'forever-be' ); ?>">
                <?php
                /**
                 * Action: Before primary navigation
                 * @since 7.0.0
                 */
                do_action( 'forever_be_before_primary_nav' );

                if ( has_nav_menu( 'primary' ) ) {
                    // WordPress Expert: Check if walker class exists
                    $walker = class_exists( 'Forever_BE_Walker_Nav_Menu' )
                        ? new Forever_BE_Walker_Nav_Menu()
                        : null;

                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'flex items-center gap-10 list-none m-0 p-0',
                        'fallback_cb'    => false,
                        'walker'         => $walker,
                    ) );
                } else {
                    // Fallback if no menu assigned
                    ?>
                    <ul class="flex items-center gap-10 list-none m-0 p-0">
                        <li>
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-[15px] font-medium tracking-[0.3px] py-2 text-text-dark hover:text-accent-blue transition-colors duration-300">
                                <?php esc_html_e( 'Accueil', 'forever-be' ); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>" class="text-[15px] font-medium tracking-[0.3px] py-2 text-brand-gold hover:text-accent-blue transition-colors duration-300">
                                <?php esc_html_e( '⚠️ Assignez un menu "Principal"', 'forever-be' ); ?>
                            </a>
                        </li>
                    </ul>
                    <?php
                }
                ?>

                <!-- Dark Mode Toggle - Premium White -->
                <button id="dark-mode-toggle" class="hover:text-brand-gold transition-colors ml-6" style="color: #ffffff;" aria-label="<?php esc_attr_e( 'Mode Sombre', 'forever-be' ); ?>">
                    <!-- Sun Icon (for dark mode) -->
                    <svg id="icon-sun" class="w-6 h-6 hidden" fill="none" stroke="#fbbf24" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <!-- Moon Icon (for light mode) -->
                    <svg id="icon-moon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <!-- Search Trigger - Premium White -->
                <button id="search-trigger" class="hover:text-brand-gold transition-colors ml-4" style="color: #ffffff;" aria-label="<?php esc_attr_e( 'Rechercher', 'forever-be' ); ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>

                <!-- FitTrack Pro Access - Premium Feature -->
                <div class="relative ml-4 fittrack-menu-desktop">
                    <button id="fittrack-menu-toggle" class="flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-300 text-white font-medium" style="backdrop-filter: blur(10px);">
                        <span>💪</span>
                        <span class="hidden xl:inline"><?php esc_html_e( 'FitTrack Pro', 'forever-be' ); ?></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="fittrack-submenu" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-2xl opacity-0 invisible transform translate-y-2 transition-all duration-300" style="top: 100%;">
                        <div class="py-2">
                            <a href="<?php echo esc_url( home_url( '/fittrack-pricing/' ) ); ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors">
                                📋 Pricing & Plans
                            </a>
                            <a href="<?php echo esc_url( home_url( '/fittrack-dashboard/' ) ); ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors">
                                📊 Dashboard
                            </a>
                            <a href="<?php echo esc_url( home_url( '/fittrack-nutrition/' ) ); ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors">
                                🥗 Nutrition Tracker
                            </a>
                            <a href="<?php echo esc_url( home_url( '/fittrack-workouts/' ) ); ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors">
                                🏋️ Workout Logger
                            </a>
                            <a href="<?php echo esc_url( home_url( '/fittrack-progress/' ) ); ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors">
                                📈 Progress Tracking
                            </a>
                            <a href="<?php echo esc_url( home_url( '/fittrack-goals/' ) ); ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors">
                                🎯 Goals Manager
                            </a>
                            <a href="<?php echo esc_url( home_url( '/fittrack-settings/' ) ); ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors">
                                ⚙️ Settings
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CTA Button - Marketing Expert v7.0: Benefit-oriented -->
                <?php
                $affiliation_page = get_page_by_path( 'affiliation' );
                if ( $affiliation_page ) :
                    ?>
                    <a href="<?php echo esc_url( get_permalink( $affiliation_page->ID ) ); ?>" class="btn btn-gold btn-sm ml-4 header-cta-pulse">
                        <span class="header-cta-icon" aria-hidden="true">💰</span>
                        <?php esc_html_e( 'Gagner des revenus', 'forever-be' ); ?>
                    </a>
                    <?php
                endif;
                ?>
            </nav>

            <!-- Search Overlay - Security Expert v7.0 -->
            <div id="search-overlay" class="fixed inset-0 bg-black/50 z-[100] opacity-0 invisible transition-all duration-300 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="search-dialog-title">
                <span id="search-dialog-title" class="sr-only"><?php esc_html_e( 'Recherche sur le site', 'forever-be' ); ?></span>
                <div class="container mx-auto px-4 pt-24">
                    <div class="bg-white rounded-xl shadow-2xl max-w-3xl mx-auto overflow-hidden transform -translate-y-full transition-transform duration-300" id="search-container">
                        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="p-4 border-b border-gray-100 flex items-center gap-4">
                            <?php wp_nonce_field( 'forever_be_search_nonce', '_search_nonce' ); ?>
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="search" id="search-input" name="s"
                                   class="w-full text-lg border-none focus:ring-0 placeholder-gray-400 text-gray-800"
                                   placeholder="<?php esc_attr_e( 'Rechercher un produit, un article...', 'forever-be' ); ?>"
                                   autocomplete="off"
                                   maxlength="100"
                                   pattern="[^<>]*">
                            <button type="button" id="search-close" class="text-gray-400 hover:text-gray-600 transition-colors" aria-label="<?php esc_attr_e( 'Fermer la recherche', 'forever-be' ); ?>">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </form>
                        <div id="search-results" class="max-h-[60vh] overflow-y-auto bg-gray-50" aria-live="polite">
                            <!-- Results will be injected here via AJAX with sanitization -->
                            <div class="p-8 text-center text-gray-500">
                                <p><?php esc_html_e( 'Commencez à taper pour rechercher...', 'forever-be' ); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>


<!-- FLP Mobile Menu & Search Script -->
<script>
(function() {
    'use strict';

    function initFLPMenu() {
        var toggle = document.getElementById('flp-menu-toggle');
        var menu = document.getElementById('flp-mobile-menu');
        var close = document.getElementById('flp-menu-close');
        var overlay = document.getElementById('flp-menu-overlay');

        if (!toggle || !menu) {
            console.error('FLP Menu: Elements not found');
            return;
        }

        console.log('FLP Menu: Initialized');

        function openMenu() {
            menu.classList.add('is-open');
            overlay.classList.add('is-open');
            document.body.classList.add('flp-menu-open');
        }

        function closeMenu() {
            menu.classList.remove('is-open');
            overlay.classList.remove('is-open');
            document.body.classList.remove('flp-menu-open');
        }

        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            openMenu();
        });

        if (close) {
            close.addEventListener('click', function(e) {
                e.preventDefault();
                closeMenu();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeMenu);
        }

        // Close menu on regular link click (not submenu toggles)
        var links = menu.querySelectorAll('a:not(.flp-submenu-toggle)');
        for (var i = 0; i < links.length; i++) {
            links[i].addEventListener('click', closeMenu);
        }

        // Submenu accordion
        var submenuToggles = menu.querySelectorAll('.flp-submenu-toggle');
        for (var j = 0; j < submenuToggles.length; j++) {
            submenuToggles[j].addEventListener('click', function(e) {
                e.preventDefault();
                var parent = this.parentElement;

                // Close other open submenus
                var openItems = menu.querySelectorAll('.flp-has-submenu.is-open');
                for (var k = 0; k < openItems.length; k++) {
                    if (openItems[k] !== parent) {
                        openItems[k].classList.remove('is-open');
                    }
                }

                // Toggle current submenu
                parent.classList.toggle('is-open');
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && menu.classList.contains('is-open')) {
                closeMenu();
            }
        });
    }

    function initFLPSearch() {
        var searchToggle = document.getElementById('flp-search-toggle');
        var searchPanel = document.getElementById('flp-search-panel');
        var searchClose = document.getElementById('flp-search-close');
        var searchInput = document.getElementById('flp-search-input');

        if (!searchToggle || !searchPanel) {
            console.error('FLP Search: Elements not found');
            return;
        }

        console.log('FLP Search: Initialized');

        function openSearch() {
            searchPanel.classList.add('is-open');
            document.body.classList.add('flp-menu-open');
            setTimeout(function() {
                searchInput.focus();
            }, 300);
        }

        function closeSearch() {
            searchPanel.classList.remove('is-open');
            document.body.classList.remove('flp-menu-open');
        }

        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            openSearch();
        });

        if (searchClose) {
            searchClose.addEventListener('click', function(e) {
                e.preventDefault();
                closeSearch();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchPanel.classList.contains('is-open')) {
                closeSearch();
            }
        });
    }

    // ========== FITTRACK PRO DESKTOP MENU ==========
    function initFitTrackMenu() {
        var toggle = document.getElementById('fittrack-menu-toggle');
        var submenu = document.getElementById('fittrack-submenu');

        if (!toggle || !submenu) return;

        // Toggle submenu on click
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var isOpen = submenu.classList.contains('is-open');

            if (isOpen) {
                submenu.classList.remove('is-open');
                submenu.style.opacity = '0';
                submenu.style.visibility = 'hidden';
                submenu.style.transform = 'translateY(8px)';
            } else {
                submenu.classList.add('is-open');
                submenu.style.opacity = '1';
                submenu.style.visibility = 'visible';
                submenu.style.transform = 'translateY(0)';
            }
        });

        // Close submenu when clicking outside
        document.addEventListener('click', function(e) {
            if (!toggle.contains(e.target) && !submenu.contains(e.target)) {
                submenu.classList.remove('is-open');
                submenu.style.opacity = '0';
                submenu.style.visibility = 'hidden';
                submenu.style.transform = 'translateY(8px)';
            }
        });

        // Close submenu on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && submenu.classList.contains('is-open')) {
                submenu.classList.remove('is-open');
                submenu.style.opacity = '0';
                submenu.style.visibility = 'hidden';
                submenu.style.transform = 'translateY(8px)';
            }
        });
    }

    // ========== DARK MODE ==========
    // Dark mode is handled by /assets/js/dark-mode.js (unified system v9.0)
    // Do NOT add dark mode handlers here to avoid conflicts

    // Initialize menu, search, and FitTrack menu
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initFLPMenu();
            initFLPSearch();
            initFitTrackMenu();
        });
    } else {
        initFLPMenu();
        initFLPSearch();
        initFitTrackMenu();
    }
})();
</script>

<!-- Premium Header CSS v8.1.0 - Expert Frontend UX Fix -->

<!-- Dark Mode CSS -->

<?php
/**
 * Action: After header output
 * Allows plugins/child themes to add content after header
 * @since 7.0.0
 */
do_action( 'forever_be_after_header' );
?>
