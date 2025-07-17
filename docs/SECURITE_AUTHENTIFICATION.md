# 🔐 Sécurité et Authentification - MC-LEGENDE

## Vue d'ensemble

Ce document décrit les améliorations de sécurité et d'authentification implémentées dans le projet MC-LEGENDE.

## 🚀 Nouvelles Fonctionnalités de Sécurité

### 1. Système de Sessions Sécurisées

#### SessionManager (`middleware/SessionManager.php`)
- **Sessions sécurisées** avec régénération automatique d'ID
- **Validation d'intégrité** (IP, User-Agent)
- **Expiration automatique** des sessions
- **Protection CSRF** intégrée
- **Limitation des tentatives** de connexion

#### Fonctionnalités principales :
```php
// Créer une session sécurisée
$sessionManager = new SessionManager();
$sessionManager->createUserSession($user);

// Vérifier l'authentification
if ($sessionManager->isAuthenticated()) {
    $user = $sessionManager->getCurrentUser();
}

// Vérifier les rôles
if ($sessionManager->hasRole('admin')) {
    // Accès admin
}

// Générer un token CSRF
$csrfToken = $sessionManager->generateCSRFToken();
```

### 2. Middleware d'Authentification Amélioré

#### AuthMiddleware (`middleware/AuthMiddleware.php`)
- **Authentification par session** pour les pages web
- **Authentification par token** pour les API
- **Vérification des rôles** et permissions
- **Protection CSRF** automatique
- **Gestion des erreurs** sécurisée

#### Utilisation :
```php
// Authentification simple
$auth = new AuthMiddleware();
$user = $auth->authenticate();

// Authentification avec rôle spécifique
$user = $auth->requireRole('admin');

// Authentification avec CSRF
$user = $auth->secureRoute();
```

### 3. Validation des Entrées

#### ValidationMiddleware (`middleware/ValidationMiddleware.php`)
- **Validation complète** des données d'entrée
- **Nettoyage automatique** des données
- **Règles personnalisables** (email, mot de passe, etc.)
- **Validation de fichiers** uploadés
- **Protection contre les injections**

#### Exemples de validation :
```php
$validator = new ValidationMiddleware();

// Validation simple
$rules = [
    'email' => 'required|email',
    'password' => 'required|password|min:8',
    'age' => 'numeric|min:18'
];

if ($validator->validate($data, $rules)) {
    // Données valides
} else {
    $errors = $validator->getErrors();
}

// Nettoyage des données
$cleanData = $validator->sanitize($data);
```

### 4. Configuration de Sécurité Centralisée

#### SecurityConfig (`config/SecurityConfig.php`)
- **Configuration centralisée** de tous les paramètres de sécurité
- **En-têtes de sécurité** automatiques
- **Gestion des permissions** par rôle
- **Rate limiting** configurable
- **Chiffrement** des données sensibles

#### Configuration des en-têtes de sécurité :
```php
// Appliquer automatiquement
SecurityConfig::applySecurityHeaders();

// En-têtes configurés :
// - X-Frame-Options: DENY
// - X-Content-Type-Options: nosniff
// - X-XSS-Protection: 1; mode=block
// - Content-Security-Policy
// - Strict-Transport-Security
```

## 🔧 Améliorations de la Base de Données

### Script de Mise à Jour (`security_updates.sql`)

#### Nouvelles Tables :
1. **`login_attempts`** - Suivi des tentatives de connexion
2. **`password_reset_tokens`** - Tokens de réinitialisation sécurisés
3. **`active_sessions`** - Sessions actives avec métadonnées

#### Améliorations des Tables Existantes :
- **Index de performance** sur les colonnes fréquemment utilisées
- **Contraintes de sécurité** pour les rôles et statuts
- **Colonnes de sécurité** (last_login, failed_attempts, etc.)

#### Procédures Stockées :
```sql
-- Verrouiller un compte après trop de tentatives
CALL lock_account_after_failed_attempts('user@example.com');

-- Réinitialiser les tentatives de connexion
CALL reset_failed_login_attempts('user@example.com');

-- Nettoyer les sessions expirées
CALL cleanup_expired_sessions();
```

## 🛡️ Mesures de Sécurité Implémentées

### 1. Protection contre les Attaques

#### Attaques par Force Brute
- **Limitation des tentatives** : 5 tentatives en 15 minutes
- **Verrouillage automatique** des comptes
- **Logging des tentatives** échouées
- **Délai progressif** entre les tentatives

#### Attaques CSRF
- **Tokens CSRF** générés automatiquement
- **Validation obligatoire** sur les formulaires
- **Expiration automatique** des tokens
- **Protection sur toutes les actions** sensibles

#### Attaques XSS
- **Nettoyage automatique** des entrées utilisateur
- **En-têtes de sécurité** appropriés
- **Validation stricte** des types de données
- **Échappement HTML** automatique

### 2. Sécurité des Sessions

#### Configuration Sécurisée
```php
// Sessions sécurisées par défaut
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
```

#### Fonctionnalités
- **Régénération d'ID** toutes les 5 minutes
- **Expiration automatique** après 1 heure d'inactivité
- **Validation d'intégrité** (IP + User-Agent)
- **Nettoyage automatique** des sessions expirées

### 3. Sécurité des Mots de Passe

#### Politique de Complexité
- **Minimum 8 caractères**
- **Au moins une majuscule**
- **Au moins une minuscule**
- **Au moins un chiffre**
- **Au moins un caractère spécial**

#### Hachage Sécurisé
```php
// Hachage avec bcrypt
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Vérification sécurisée
if (password_verify($password, $hashedPassword)) {
    // Mot de passe correct
}
```

### 4. Logging et Monitoring

#### Événements Loggés
- **Tentatives de connexion** (réussies/échouées)
- **Actions sensibles** (modification de profil, etc.)
- **Erreurs de sécurité** (tentatives d'accès non autorisées)
- **Activité des sessions** (création, destruction)

#### Format des Logs
```sql
INSERT INTO logs (user_id, action, description, ip_address, date_creation)
VALUES (?, ?, ?, ?, NOW());
```

## 📋 Guide d'Implémentation

### 1. Installation

#### Étape 1 : Mise à jour de la base de données
```bash
mysql -u root -p mc-legende < security_updates.sql
```

#### Étape 2 : Vérification des fichiers
- ✅ `middleware/SessionManager.php`
- ✅ `middleware/AuthMiddleware.php`
- ✅ `middleware/ValidationMiddleware.php`
- ✅ `config/SecurityConfig.php`

#### Étape 3 : Mise à jour du composer.json
```json
{
    "autoload": {
        "psr-4": {
            "Middleware\\": "middleware/",
            "Config\\": "config/"
        }
    }
}
```

### 2. Utilisation dans les Contrôleurs

#### Exemple de Contrôleur Sécurisé
```php
class SecureController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireRole('admin'); // Vérification automatique
    }

    public function sensitiveAction()
    {
        // Vérifier le token CSRF
        $this->authMiddleware->requireCSRFToken();
        
        // Valider les données
        $validator = new ValidationMiddleware();
        $rules = [
            'data' => 'required|max:1000'
        ];
        
        if (!$validator->validate($_POST, $rules)) {
            $this->errorResponse($validator->getErrors());
        }
        
        // Traitement sécurisé...
    }
}
```

### 3. Utilisation dans les Vues

#### Formulaire avec CSRF
```php
<?php $csrfToken = $this->sessionManager->generateCSRFToken(); ?>

<form method="POST" action="/secure-action">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    <!-- Autres champs... -->
</form>
```

#### Vérification d'authentification
```php
<?php if ($this->sessionManager->isAuthenticated()): ?>
    <p>Bienvenue, <?= htmlspecialchars($this->sessionManager->getCurrentUser()['nom']) ?></p>
<?php else: ?>
    <p><a href="/connexion">Se connecter</a></p>
<?php endif; ?>
```

## 🔍 Tests de Sécurité

### 1. Tests Automatisés Recommandés

#### Test d'Authentification
```php
public function testAuthentication()
{
    // Test connexion réussie
    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'SecurePass123!'
    ]);
    $this->assertAuthenticated();
    
    // Test limitation des tentatives
    for ($i = 0; $i < 6; $i++) {
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong'
        ]);
    }
    $this->assertResponseStatus(429); // Too Many Requests
}
```

#### Test CSRF
```php
public function testCSRFProtection()
{
    $response = $this->post('/secure-action', [
        'data' => 'test'
        // Pas de token CSRF
    ]);
    $this->assertResponseStatus(403); // Forbidden
}
```

### 2. Outils de Test Recommandés

- **OWASP ZAP** - Test de vulnérabilités web
- **Burp Suite** - Test de sécurité des applications
- **Nikto** - Scanner de vulnérabilités
- **SQLMap** - Test d'injection SQL

## 🚨 Gestion des Incidents

### 1. Détection d'Incidents

#### Logs de Sécurité
```sql
-- Tentatives de connexion suspectes
SELECT * FROM logs 
WHERE action = 'login_failed' 
AND date_creation > DATE_SUB(NOW(), INTERVAL 1 HOUR)
AND ip_address = '192.168.1.100';

-- Sessions multiples pour un utilisateur
SELECT user_id, COUNT(*) as session_count 
FROM active_sessions 
GROUP BY user_id 
HAVING session_count > 3;
```

### 2. Réponse aux Incidents

#### Procédures Automatiques
1. **Verrouillage automatique** après 5 tentatives échouées
2. **Nettoyage automatique** des sessions expirées
3. **Logging automatique** de tous les événements de sécurité

#### Actions Manuelles
1. **Révision des logs** de sécurité
2. **Réinitialisation** des mots de passe si nécessaire
3. **Blocage d'IP** si suspectée
4. **Notification** des administrateurs

## 📈 Monitoring et Métriques

### 1. Métriques de Sécurité

#### Dashboard de Sécurité
- **Tentatives de connexion** par heure/jour
- **Comptes verrouillés** actuellement
- **Sessions actives** par utilisateur
- **Erreurs de sécurité** détectées

#### Alertes Automatiques
- **Trop de tentatives** de connexion depuis une IP
- **Sessions multiples** pour un utilisateur
- **Actions sensibles** en dehors des heures normales
- **Erreurs de validation** répétées

### 2. Rapports de Sécurité

#### Rapports Quotidiens
- Résumé des événements de sécurité
- Tentatives d'accès non autorisées
- Comptes verrouillés
- Sessions expirées

#### Rapports Mensuels
- Analyse des tendances de sécurité
- Recommandations d'amélioration
- Audit des permissions utilisateur
- Révision des politiques de sécurité

## 🔄 Maintenance et Mises à Jour

### 1. Tâches de Maintenance

#### Tâches Quotidiennes
```sql
-- Nettoyage automatique des sessions expirées
CALL cleanup_expired_sessions();

-- Suppression des logs anciens (optionnel)
DELETE FROM logs WHERE date_creation < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

#### Tâches Mensuelles
- **Révision des permissions** utilisateur
- **Audit des sessions** actives
- **Mise à jour** des clés de chiffrement
- **Test de sécurité** complet

### 2. Mises à Jour de Sécurité

#### Procédure de Mise à Jour
1. **Sauvegarde** complète de la base de données
2. **Test** des nouvelles fonctionnalités en environnement de développement
3. **Déploiement** en production pendant les heures creuses
4. **Vérification** du bon fonctionnement
5. **Monitoring** renforcé pendant 24h

## 📚 Ressources et Références

### 1. Standards de Sécurité
- **OWASP Top 10** - Vulnérabilités web les plus critiques
- **NIST Cybersecurity Framework** - Cadre de cybersécurité
- **ISO 27001** - Gestion de la sécurité de l'information

### 2. Outils Recommandés
- **Composer** - Gestion des dépendances PHP
- **PHP Security Checker** - Vérification des vulnérabilités
- **PHP CS Fixer** - Standards de code sécurisé

### 3. Documentation Technique
- **PHP Security Guide** - Guide officiel PHP
- **OWASP PHP Security Cheat Sheet** - Bonnes pratiques
- **PHP Session Security** - Sécurité des sessions

---

*Documentation mise à jour le : 2025-01-03*
*Version : 2.0 - Système de sécurité avancé* 