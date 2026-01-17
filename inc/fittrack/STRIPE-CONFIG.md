# FitTrack Pro - Stripe Configuration

## Configuration des clés Stripe

Pour configurer les clés Stripe, ajoutez ces constantes dans votre fichier `wp-config.php` :

```php
// FitTrack Pro - Stripe API Keys (TEST MODE)
define('FITTRACK_STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_PUBLISHABLE_KEY');
define('FITTRACK_STRIPE_SECRET_KEY', 'sk_test_YOUR_SECRET_KEY');
```

### Obtenir vos clés Stripe

1. Connectez-vous à [Stripe Dashboard](https://dashboard.stripe.com/)
2. Allez dans **Developers** → **API keys**
3. Copiez vos clés de TEST (pour le développement)
4. Ajoutez-les dans `wp-config.php` comme indiqué ci-dessus

### Mode Production

Pour passer en mode production, remplacez les clés de test par vos clés de production :

```php
// Production keys (commencent par pk_live_ et sk_live_)
define('FITTRACK_STRIPE_PUBLISHABLE_KEY', 'pk_live_YOUR_PUBLISHABLE_KEY');
define('FITTRACK_STRIPE_SECRET_KEY', 'sk_live_YOUR_SECRET_KEY');
```

### Alternative: Options WordPress

Si vous ne souhaitez pas modifier `wp-config.php`, vous pouvez configurer les clés via les options WordPress :

```php
update_option('fittrack_stripe_publishable_key', 'pk_test_YOUR_KEY');
update_option('fittrack_stripe_secret_key', 'sk_test_YOUR_KEY');
```

## Plans configurés

- **Free**: €0/mois - Fonctionnalités de base
- **Pro**: €9.99/mois - Toutes les fonctionnalités
- **Premium**: €79.99/an - Pro + IA + Rapports PDF

## Webhooks Stripe

Configurez le webhook Stripe pour recevoir les événements :

1. Dashboard Stripe → **Developers** → **Webhooks**
2. Ajoutez l'endpoint : `https://votre-site.com/wp-admin/admin-ajax.php?action=fittrack_stripe_webhook`
3. Sélectionnez les événements :
   - `checkout.session.completed`
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
4. Copiez le secret de signature et ajoutez-le :

```php
update_option('fittrack_stripe_webhook_secret', 'whsec_YOUR_WEBHOOK_SECRET');
```

## Liens utiles

- [Dashboard Stripe TEST](https://dashboard.stripe.com/test)
- [Documentation Stripe](https://stripe.com/docs)
- [Tester les paiements](https://stripe.com/docs/testing)
