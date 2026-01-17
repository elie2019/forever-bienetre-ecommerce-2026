# FitTrack Pro - Guide de Test en Localhost

**Date:** 17 janvier 2026
**Version:** 1.0.0
**Environnement:** XAMPP Local

---

## 🚀 DÉMARRAGE RAPIDE (5 MINUTES)

### Étape 1: Démarrer les Services

**Double-cliquez sur:**
```
C:\xampp\htdocs\foreverbienetre\wp-content\themes\forever-be-wp-premium\inc\fittrack\scripts\start-local-testing.bat
```

**Ce que ça fait:**
- ✅ Démarre Apache (port 80)
- ✅ Démarre MySQL (port 3306)
- ✅ Vérifie que les services tournent
- ✅ Ouvre le navigateur sur http://localhost/foreverbienetre

### Étape 2: Configurer l'Environnement

**Ouvrez un terminal (CMD) dans le dossier du thème et exécutez:**
```bash
cd C:\xampp\htdocs\foreverbienetre\wp-content\themes\forever-be-wp-premium\inc\fittrack\scripts
C:\xampp\php\php.exe setup-local-env.php
```

**Ce que ça fait:**
- ✅ Vérifie la connexion MySQL
- ✅ Crée les tables FitTrack si manquantes
- ✅ Vérifie les clés Stripe
- ✅ Crée un utilisateur de test (fittrack_test / test123)
- ✅ Crée des aliments de démonstration (6 aliments)
- ✅ Crée des exercices de démonstration (5 exercices)
- ✅ Affiche toutes les URLs de test

### Étape 3: Configurer Stripe (OPTIONNEL pour tester les paiements)

**Si vous voulez tester les abonnements Stripe:**

1. Récupérez vos clés TEST sur: https://dashboard.stripe.com/test/apikeys

2. Ouvrez: `C:\xampp\htdocs\foreverbienetre\wp-config.php`

3. Ajoutez AVANT `/* That's all, stop editing! */`:

```php
// FitTrack Pro - Stripe API Keys (TEST MODE)
define('FITTRACK_STRIPE_PUBLISHABLE_KEY', 'pk_test_VOTRE_CLE_ICI');
define('FITTRACK_STRIPE_SECRET_KEY', 'sk_test_VOTRE_CLE_ICI');
```

4. Sauvegardez et rechargez la page

---

## ✅ CHECKLIST DE TEST COMPLÈTE

### 🎯 PRÉ-REQUIS

- [ ] Apache XAMPP démarré
- [ ] MySQL XAMPP démarré
- [ ] Script setup-local-env.php exécuté sans erreurs
- [ ] Utilisateur de test créé (fittrack_test / test123)

---

## 📋 TESTS FONCTIONNELS

### 1️⃣ TEST: Page d'Accueil WordPress

**URL:** http://localhost/foreverbienetre

**Actions:**
- [ ] La page d'accueil se charge correctement
- [ ] Aucune erreur PHP visible
- [ ] Le thème s'affiche correctement
- [ ] Navigation fonctionne

**Résultat attendu:** ✅ Page normale sans erreurs

---

### 2️⃣ TEST: Page Pricing + Stripe Integration

**URL:** http://localhost/foreverbienetre/fittrack-pricing

**Expert en charge:** expert_checkout + expert_products (router_stripe)

#### Test A: Chargement de la Page

**Actions:**
- [ ] Accéder à l'URL ci-dessus
- [ ] Vérifier que 3 cartes de pricing s'affichent (Free, Pro, Premium)
- [ ] Ouvrir Console navigateur (F12)
- [ ] Vérifier qu'il n'y a PAS d'erreurs JavaScript

**Résultat attendu:**
```
✅ 3 cartes de pricing visibles
✅ Free: €0/mois
✅ Pro: €9.99/mois avec badge "POPULAR"
✅ Premium: €79.99/an
✅ Console propre (pas d'erreurs)
```

**Erreurs possibles:**
```
❌ "Stripe publishable key not configured"
→ Solution: Ajouter les clés dans wp-config.php

❌ "fittrackData is not defined"
→ Solution: Template non chargé, vérifier que les pages existent

❌ "jQuery is not defined"
→ Solution: Connexion internet nécessaire pour CDN
```

#### Test B: Bouton Subscribe (utilisateur non connecté)

**Actions:**
- [ ] Cliquer sur bouton "Subscribe Now" du plan Pro
- [ ] Vérifier l'alert qui s'affiche
- [ ] Vérifier la redirection

**Résultat attendu:**
```
✅ Alert: "Please log in to subscribe"
✅ Redirection vers /wp-login.php?redirect_to=...
```

#### Test C: Bouton Subscribe (utilisateur connecté)

**Prérequis:** Se connecter avec fittrack_test / test123

**Actions:**
- [ ] Aller sur http://localhost/foreverbienetre/wp-login.php
- [ ] Se connecter: fittrack_test / test123
- [ ] Retourner sur /fittrack-pricing
- [ ] Cliquer sur "Subscribe Now" (plan Pro)
- [ ] Observer la console (F12 → Network)

**Résultat attendu (AVEC clés Stripe configurées):**
```
✅ Bouton change: "Processing..."
✅ Requête AJAX vers /wp-admin/admin-ajax.php
✅ Action: fittrack_create_checkout_session
✅ Réponse JSON avec sessionId
✅ Redirection vers Stripe Checkout
```

**Résultat attendu (SANS clés Stripe):**
```
⚠️ Alert: "Payment system not configured. Please contact support."
```

#### Test D: Stripe Checkout Flow (si clés configurées)

**Actions:**
- [ ] Sur la page Stripe Checkout, utiliser carte de test: 4242 4242 4242 4242
- [ ] CVV: 123
- [ ] Date: n'importe quelle date future
- [ ] Email: test@example.com
- [ ] Cliquer "Subscribe"
- [ ] Vérifier redirection après paiement

**Résultat attendu:**
```
✅ Paiement accepté
✅ Redirection vers /fittrack-dashboard?session_id=...
✅ Abonnement créé dans Stripe Dashboard TEST
✅ Entrée créée dans table wp_fittrack_subscriptions
```

**Vérification en base de données:**
```sql
SELECT * FROM wp_fittrack_subscriptions WHERE user_id = [ID de fittrack_test];
```

---

### 3️⃣ TEST: Dashboard

**URL:** http://localhost/foreverbienetre/fittrack-dashboard

**Expert en charge:** expert_files (router_github)

**Prérequis:** Être connecté

#### Test A: Chargement Dashboard

**Actions:**
- [ ] Accéder à l'URL
- [ ] Vérifier que la page se charge
- [ ] Vérifier les 4 cards statistiques
- [ ] Vérifier le graphique Chart.js

**Résultat attendu:**
```
✅ 4 stats cards affichées:
   - Today's Calories
   - Workouts This Week
   - Current Weight
   - Active Goals

✅ Graphique "Your Progress" affiché
✅ Section "Quick Actions" visible
✅ Section "Recent Workouts" visible
✅ Badge plan utilisateur affiché
```

#### Test B: Graphiques Chart.js

**Actions:**
- [ ] Ouvrir Console (F12)
- [ ] Vérifier qu'un graphique se dessine
- [ ] Passer la souris sur le graphique (hover)

**Résultat attendu:**
```
✅ Graphique en courbes visible
✅ 3 datasets: Weight, Calories, Workouts
✅ Tooltips s'affichent au survol
✅ Pas d'erreur "Chart is not defined"
```

#### Test C: Quick Actions

**Actions:**
- [ ] Cliquer sur "Log Meal"
- [ ] Cliquer sur "Log Workout"
- [ ] Cliquer sur "Update Weight"
- [ ] Vérifier les réactions

**Résultat attendu:**
```
✅ Clic fonctionne (pas d'erreur console)
✅ Redirection ou modal s'ouvre (selon implémentation)
```

---

### 4️⃣ TEST: Module Nutrition

**URL:** http://localhost/foreverbienetre/fittrack-nutrition

**Expert en charge:** expert_files (router_github) - Module Nutrition

**Prérequis:** Être connecté

#### Test A: Affichage Page

**Actions:**
- [ ] Accéder à l'URL
- [ ] Vérifier que le template se charge
- [ ] Vérifier la présence d'un formulaire "Add Meal"

**Résultat attendu:**
```
✅ Page chargée
✅ Formulaire visible avec champs:
   - Meal Type (Breakfast, Lunch, Dinner, Snack)
   - Food selection
   - Quantity
   - Bouton "Add to Log"
```

#### Test B: Ajouter un Repas (AJAX)

**Actions:**
- [ ] Sélectionner "Breakfast"
- [ ] Sélectionner un aliment (ex: "Poulet grillé (100g)")
- [ ] Quantité: 1
- [ ] Cliquer "Add to Log"
- [ ] Observer Console (F12 → Network)

**Résultat attendu:**
```
✅ Requête AJAX vers admin-ajax.php
✅ Action: fittrack_add_meal
✅ Réponse success: true
✅ Notification "Meal added successfully!"
✅ Entrée dans table wp_fittrack_nutrition_logs
```

**Vérification en BDD:**
```sql
SELECT * FROM wp_fittrack_nutrition_logs
WHERE user_id = [ID]
ORDER BY created_at DESC
LIMIT 5;
```

#### Test C: Affichage Journal Quotidien

**Actions:**
- [ ] Vérifier section "Today's Nutrition"
- [ ] Vérifier calcul des macros totales
- [ ] Vérifier liste des repas

**Résultat attendu:**
```
✅ Total Calories: [calculé]
✅ Protein: [g]
✅ Carbs: [g]
✅ Fat: [g]
✅ Liste des repas ajoutés aujourd'hui
```

#### Test D: Barres de Progression Macros

**Actions:**
- [ ] Vérifier les barres de progression (Protein, Carbs, Fat)
- [ ] Vérifier les pourcentages

**Résultat attendu:**
```
✅ 3 barres de progression affichées
✅ Couleurs différentes (vert, bleu, orange)
✅ Pourcentages corrects
```

---

### 5️⃣ TEST: Module Workouts

**URL:** http://localhost/foreverbienetre/fittrack-workouts

**Expert en charge:** expert_files (router_github) - Module Workouts

#### Test A: Logger un Workout

**Actions:**
- [ ] Accéder à la page
- [ ] Cliquer "Log New Workout"
- [ ] Remplir le formulaire:
   - Workout name: "Morning Run"
   - Duration: 30 minutes
   - Calories: 250
   - Exercises: Running
- [ ] Soumettre
- [ ] Vérifier la notification

**Résultat attendu:**
```
✅ Formulaire se soumet
✅ AJAX call vers admin-ajax.php
✅ Action: fittrack_log_workout
✅ Notification success
✅ Entrée dans wp_fittrack_workout_logs
```

#### Test B: Historique des Workouts

**Actions:**
- [ ] Vérifier section "Recent Workouts"
- [ ] Vérifier que le workout ajouté apparaît

**Résultat attendu:**
```
✅ Liste des workouts récents
✅ Affichage: date, nom, durée, calories
✅ Bouton "View Details" fonctionnel
```

---

### 6️⃣ TEST: Module Progress

**URL:** http://localhost/foreverbienetre/fittrack-progress

**Expert en charge:** expert_files (router_github) - Module Progress

#### Test A: Ajouter une Entrée de Poids

**Actions:**
- [ ] Cliquer "Add Progress Entry"
- [ ] Remplir:
   - Weight: 75 kg
   - Body Fat: 15 %
   - Muscle Mass: 35 kg
   - Date: Aujourd'hui
- [ ] Soumettre

**Résultat attendu:**
```
✅ Formulaire soumis via AJAX
✅ Action: fittrack_add_progress
✅ Entrée dans wp_fittrack_progress_logs
✅ Graphique mis à jour automatiquement
```

#### Test B: Graphique de Progression

**Actions:**
- [ ] Vérifier que le graphique Chart.js se dessine
- [ ] Passer souris sur les points
- [ ] Vérifier les 3 courbes (Weight, Body Fat, Muscle Mass)

**Résultat attendu:**
```
✅ 3 courbes de couleurs différentes
✅ Tooltips au survol
✅ Légende visible
✅ Données correctes affichées
```

---

### 7️⃣ TEST: Module Goals

**URL:** http://localhost/foreverbienetre/fittrack-goals

**Expert en charge:** expert_files (router_github) - Module Goals

#### Test A: Créer un Objectif

**Actions:**
- [ ] Cliquer "Create New Goal"
- [ ] Remplir:
   - Goal Type: Weight Loss
   - Target: 70 kg
   - Current: 75 kg
   - Target Date: Dans 2 mois
- [ ] Soumettre

**Résultat attendu:**
```
✅ Formulaire soumis
✅ AJAX action: fittrack_create_goal
✅ Entrée dans wp_fittrack_goals
✅ Objectif affiché dans la liste
```

#### Test B: Barre de Progression de l'Objectif

**Actions:**
- [ ] Vérifier la carte de l'objectif créé
- [ ] Vérifier la barre de progression
- [ ] Vérifier le pourcentage

**Résultat attendu:**
```
✅ Carte objectif affichée
✅ Barre de progression visible
✅ Pourcentage calculé: (75-70)/(75-70) = 0% (début)
✅ Bouton "Update Progress" visible
```

---

### 8️⃣ TEST: Fonctionnalités AJAX Globales

**Expert en charge:** expert_console (router_chromeDevTools)

#### Test A: Tous les Endpoints AJAX

**Actions:**
- [ ] Ouvrir Console (F12)
- [ ] Aller sur chaque page et effectuer une action
- [ ] Vérifier tab "Network" pour chaque requête AJAX

**Endpoints à tester:**

| Action | Endpoint | Expected Response |
|--------|----------|-------------------|
| Add Meal | fittrack_add_meal | {success: true} |
| Log Workout | fittrack_log_workout | {success: true} |
| Add Progress | fittrack_add_progress | {success: true} |
| Create Goal | fittrack_create_goal | {success: true} |
| Get Dashboard Stats | fittrack_get_dashboard_stats | {success: true, data: {...}} |
| Create Checkout | fittrack_create_checkout_session | {success: true, data: {sessionId: "..."}} |

#### Test B: Validation Nonce

**Actions:**
- [ ] Ouvrir Console
- [ ] Essayer de faire une requête AJAX sans nonce valide
- [ ] Vérifier la réponse

**Résultat attendu:**
```
❌ Erreur 403 ou message "Nonce verification failed"
✅ Sécurité fonctionnelle
```

#### Test C: Gestion d'Erreurs

**Actions:**
- [ ] Déconnecter MySQL (arrêter XAMPP MySQL)
- [ ] Essayer d'ajouter un repas
- [ ] Vérifier le message d'erreur

**Résultat attendu:**
```
✅ Message d'erreur clair
✅ Pas de crash de la page
✅ Log dans console avec détails
```

---

### 9️⃣ TEST: Sécurité

**Expert en charge:** expert_auth (router_supabase adapté)

#### Test A: Accès Non Autorisé

**Actions:**
- [ ] Se déconnecter
- [ ] Essayer d'accéder à /fittrack-dashboard
- [ ] Vérifier la redirection

**Résultat attendu:**
```
✅ Redirection vers wp-login.php
OU
✅ Message "Please log in to access this page"
```

#### Test B: Injection SQL

**Actions:**
- [ ] Dans formulaire Nutrition, essayer d'entrer: `'; DROP TABLE wp_fittrack_nutrition_logs; --`
- [ ] Soumettre
- [ ] Vérifier que la table existe toujours

**Résultat attendu:**
```
✅ Entrée sanitized (échappée)
✅ Table toujours présente
✅ Pas d'exécution SQL malveillante
```

#### Test C: XSS (Cross-Site Scripting)

**Actions:**
- [ ] Essayer d'entrer: `<script>alert('XSS')</script>` dans nom de workout
- [ ] Soumettre
- [ ] Recharger la page et voir l'affichage

**Résultat attendu:**
```
✅ Script échappé (affiché comme texte)
✅ Pas d'exécution JavaScript
```

---

### 🔟 TEST: Performance

**Expert en charge:** expert_console (router_chromeDevTools)

#### Test A: Temps de Chargement des Pages

**Actions:**
- [ ] Ouvrir DevTools → Network
- [ ] Charger chaque page FitTrack
- [ ] Noter le temps de chargement (DOMContentLoaded)

**Résultat attendu:**
```
✅ Dashboard: < 2 secondes
✅ Pricing: < 1.5 secondes
✅ Nutrition: < 2 secondes
✅ Workouts: < 2 secondes
```

#### Test B: Requêtes de Base de Données

**Actions:**
- [ ] Installer Query Monitor plugin (optionnel)
- [ ] Charger Dashboard
- [ ] Vérifier nombre de requêtes SQL

**Résultat attendu:**
```
✅ Moins de 30 requêtes par page
✅ Pas de requêtes N+1
```

#### Test C: Taille des Assets

**Actions:**
- [ ] Vérifier taille de fittrack-main.css
- [ ] Vérifier taille de fittrack-main.js
- [ ] Vérifier chargement Chart.js

**Résultat attendu:**
```
✅ CSS: < 50 KB
✅ JS: < 100 KB
✅ Chart.js: Chargé depuis CDN
```

---

## 📊 RAPPORT DE TEST

### Template de Rapport

Copiez-collez ce template et remplissez après vos tests:

```markdown
# FitTrack Pro - Rapport de Test Localhost

**Date:** [DATE]
**Testeur:** [NOM]
**Environnement:** XAMPP / Windows

## Résumé

- [ ] Tous les tests passés ✅
- [ ] Bugs mineurs trouvés ⚠️
- [ ] Bugs majeurs trouvés ❌

## Détails des Tests

### Page Pricing
- Chargement: ✅ / ⚠️ / ❌
- Boutons Subscribe: ✅ / ⚠️ / ❌
- Stripe Integration: ✅ / ⚠️ / ❌ / N/A

### Dashboard
- Chargement: ✅ / ⚠️ / ❌
- Graphiques: ✅ / ⚠️ / ❌
- Quick Actions: ✅ / ⚠️ / ❌

### Module Nutrition
- Add Meal: ✅ / ⚠️ / ❌
- Daily Summary: ✅ / ⚠️ / ❌
- Macros Calculation: ✅ / ⚠️ / ❌

### Module Workouts
- Log Workout: ✅ / ⚠️ / ❌
- History: ✅ / ⚠️ / ❌

### Module Progress
- Add Progress: ✅ / ⚠️ / ❌
- Charts: ✅ / ⚠️ / ❌

### Module Goals
- Create Goal: ✅ / ⚠️ / ❌
- Progress Bar: ✅ / ⚠️ / ❌

### AJAX & Security
- AJAX Endpoints: ✅ / ⚠️ / ❌
- Nonce Validation: ✅ / ⚠️ / ❌
- SQL Injection Protection: ✅ / ⚠️ / ❌
- XSS Protection: ✅ / ⚠️ / ❌

## Bugs Trouvés

### Bug #1
- **Sévérité:** Mineur / Majeur / Critique
- **Description:** [DESCRIPTION]
- **Steps to Reproduce:** [STEPS]
- **Expected:** [EXPECTED]
- **Actual:** [ACTUAL]
- **Screenshot:** [LINK]

## Performance

- **Dashboard Load Time:** [X] secondes
- **Nombre de requêtes SQL:** [X]
- **Taille CSS:** [X] KB
- **Taille JS:** [X] KB

## Recommandations

1. [RECOMMANDATION 1]
2. [RECOMMANDATION 2]

## Conclusion

✅ Prêt pour déploiement en production
⚠️ Corrections mineures nécessaires avant déploiement
❌ Corrections majeures requises
```

---

## 🔧 TROUBLESHOOTING

### Problème: "Cannot connect to database"

**Solution:**
```bash
# Vérifier que MySQL tourne
tasklist | findstr mysqld

# Redémarrer MySQL
C:\xampp\mysql\bin\mysqld.exe --defaults-file="C:\xampp\mysql\bin\my.ini"
```

### Problème: "Page not found" (404)

**Solution:**
1. Vérifier que les pages existent en BDD:
```sql
SELECT * FROM wp_posts WHERE post_name LIKE 'fittrack%';
```

2. Flush permalinks:
```bash
C:\xampp\php\php.exe inc/fittrack/scripts/flush-permalinks.php
```

### Problème: "fittrackData is not defined"

**Solution:**
- Vérifier que le template FitTrack se charge (pas le template WordPress par défaut)
- Vérifier `inc/fittrack/fittrack-init.php` est chargé dans `functions.php`

### Problème: Charts ne s'affichent pas

**Solution:**
- Vérifier connexion internet (Chart.js chargé depuis CDN)
- Ouvrir Console → vérifier erreurs
- Vérifier que les données sont présentes en BDD

---

## 📝 CHECKLIST FINALE AVANT DÉPLOIEMENT

- [ ] Tous les tests passés ✅
- [ ] Aucun bug critique
- [ ] Performance acceptable (< 3s par page)
- [ ] Sécurité validée (XSS, SQL injection)
- [ ] Stripe fonctionne en mode TEST
- [ ] Tous les graphiques s'affichent
- [ ] AJAX fonctionne pour toutes les actions
- [ ] Données de démonstration créées
- [ ] Documentation à jour
- [ ] Rapport de test généré

---

**Temps estimé pour test complet:** 2-3 heures

**Expert en charge:** Tous les experts MCP (router_github, router_stripe, router_chromeDevTools, router_database)

**Bon tests! 🚀**
