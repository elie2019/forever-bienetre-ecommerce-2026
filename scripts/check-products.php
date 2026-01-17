<?php
/**
 * Script pour vérifier les produits dans la base de données
 */

// Charger WordPress
require_once('/mnt/c/xampp/htdocs/foreverbienetre/wp-load.php');

// Requête pour récupérer tous les produits
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'post_status' => 'publish'
);

$products = new WP_Query($args);

if ($products->have_posts()) {
    echo "=== PRODUITS TROUVÉS DANS LA BASE DE DONNÉES ===\n\n";

    while ($products->have_posts()) {
        $products->the_post();
        $product_id = get_the_ID();

        echo "ID: " . $product_id . "\n";
        echo "Titre: " . get_the_title() . "\n";
        echo "Slug: " . get_post_field('post_name', $product_id) . "\n";
        echo "Prix: " . get_post_meta($product_id, '_price', true) . " €\n";
        echo "Image: " . get_the_post_thumbnail_url($product_id, 'large') . "\n";
        echo "---\n\n";
    }

    wp_reset_postdata();

    echo "Total: " . $products->found_posts . " produits\n";
} else {
    echo "AUCUN PRODUIT TROUVÉ\n";
    echo "Le Custom Post Type 'product' n'existe peut-être pas encore.\n";
}
?>
