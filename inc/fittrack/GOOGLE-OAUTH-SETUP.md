# Configuration Google OAuth pour FitTrack Pro

## Vue d'ensemble

FitTrack Pro utilise Google Sign-In pour permettre aux utilisateurs de s'inscrire et se connecter facilement avec leur compte Google.

## Étapes de Configuration

### 1. Créer un Projet Google Cloud

1. Allez sur [Google Cloud Console](https://console.cloud.google.com/)
2. Créez un nouveau projet ou sélectionnez un projet existant
3. Nommez votre projet (ex: "FitTrack Pro - Forever Bien-Être")

### 2. Activer l'API Google Sign-In

1. Dans le menu de navigation, allez dans **APIs & Services** > **Library**
2. Recherchez "Google+ API" ou "Google Identity Services"
3. Cliquez sur **Enable**

### 3. Configurer l'écran de consentement OAuth

1. Allez dans **APIs & Services** > **OAuth consent screen**
2. Sélectionnez **External** (pour permettre à tous les utilisateurs de s'inscrire)
3. Remplissez les informations requises:
   - **App name**: FitTrack Pro
   - **User support email**: votre email
   - **Developer contact**: votre email
4. Ajoutez les scopes suivants:
   - `email`
   - `profile`
   - `openid`
5. Sauvegardez et continuez

### 4. Créer les Identifiants OAuth 2.0

1. Allez dans **APIs & Services** > **Credentials**
2. Cliquez sur **Create Credentials** > **OAuth 2.0 Client ID**
3. Sélectionnez **Web application**
4. Configurez:
   - **Name**: FitTrack Pro Web Client
   - **Authorized JavaScript origins**:
     - `http://localhost` (pour développement local)
     - `https://foreverbienetre.com` (pour production)
   - **Authorized redirect URIs**:
     - `http://localhost/foreverbienetre/fittrack-login`
     - `http://localhost/foreverbienetre/fittrack-register`
     - `https://foreverbienetre.com/fittrack-login`
     - `https://foreverbienetre.com/fittrack-register`
5. Cliquez sur **Create**
6. **Copiez le Client ID** qui s'affiche (format: `xxx.apps.googleusercontent.com`)

### 5. Configurer WordPress

#### Option A: Via l'Interface Admin (Recommandé)

1. Connectez-vous à WordPress Admin
2. Allez dans **Réglages** > **FitTrack**
3. Collez votre **Google Client ID** dans le champ correspondant
4. Sauvegardez

#### Option B: Via wp-config.php

Ajoutez cette ligne dans `wp-config.php`:

```php
define('FITTRACK_GOOGLE_CLIENT_ID', 'votre-client-id.apps.googleusercontent.com');
```

#### Option C: Via la Base de Données

Exécutez cette requête SQL:

```sql
UPDATE wp_options
SET option_value = 'votre-client-id.apps.googleusercontent.com'
WHERE option_name = 'fittrack_google_client_id';
```

## Vérification

### Test en Local

1. Ouvrez `http://localhost/foreverbienetre/fittrack-login`
2. Vous devriez voir le bouton "Se connecter avec Google"
3. Cliquez dessus pour tester le flux d'authentification

### Problèmes Courants

#### Le bouton Google ne s'affiche pas

- **Cause**: Client ID manquant ou invalide
- **Solution**: Vérifiez que le Client ID est bien configuré dans WordPress

#### Erreur "redirect_uri_mismatch"

- **Cause**: L'URL de redirection n'est pas autorisée dans Google Console
- **Solution**: Ajoutez l'URL exacte dans **Authorized redirect URIs**

#### Erreur "invalid_client"

- **Cause**: Client ID incorrect
- **Solution**: Vérifiez que vous avez copié le bon Client ID depuis Google Console

## Sécurité en Production

### 1. Utiliser HTTPS

En production, assurez-vous que votre site utilise HTTPS:

```php
// Dans wp-config.php
define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);
```

### 2. Vérification du Token côté Serveur

Le code actuel vérifie basiquement le token JWT. Pour la production, utilisez la bibliothèque officielle Google:

```bash
composer require google/apiclient:"^2.0"
```

Puis modifiez `verify_google_token()` dans `fittrack-data-sync.php`:

```php
private function verify_google_token($credential) {
    require_once get_template_directory() . '/vendor/autoload.php';

    $client = new Google_Client(['client_id' => get_option('fittrack_google_client_id')]);

    try {
        $payload = $client->verifyIdToken($credential);
        if ($payload) {
            return $payload;
        }
        return false;
    } catch (Exception $e) {
        error_log('Google token verification failed: ' . $e->getMessage());
        return false;
    }
}
```

### 3. Limiter les Domaines Autorisés

Dans Google Cloud Console, limitez les **Authorized JavaScript origins** aux domaines de production uniquement.

## Fichiers Concernés

- **page-fittrack-login.php** - Page de connexion avec bouton Google
- **page-fittrack-register.php** - Page d'inscription avec bouton Google
- **inc/fittrack/fittrack-data-sync.php** - Handlers AJAX pour l'authentification Google
- **inc/fittrack/fittrack-init.php** - Liste des pages FitTrack

## Support

Pour plus d'informations:
- [Documentation Google Identity](https://developers.google.com/identity/gsi/web)
- [Guide de démarrage rapide](https://developers.google.com/identity/gsi/web/guides/overview)

---

**Note**: Ce système d'authentification est fonctionnel mais utilise une vérification basique du token JWT pour faciliter le développement. Pour la production, implémentez la vérification complète avec la bibliothèque Google API Client.
