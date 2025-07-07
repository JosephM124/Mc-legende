# Contrôleurs et Middlewares

Ce document explique l'architecture des contrôleurs et middlewares créés pour votre application.

## Structure des dossiers

```
├── controller/
│   ├── BaseController.php          # Contrôleur de base avec méthodes communes
│   ├── UtilisateursController.php  # Gestion des utilisateurs
│   ├── EleveController.php         # Gestion des élèves
│   └── ExampleController.php       # Exemples d'utilisation
├── middleware/
│   ├── AuthMiddleware.php          # Authentification et autorisation
│   ├── ValidationMiddleware.php    # Validation des données
│   └── CorsMiddleware.php          # Gestion CORS
└── README_Controllers_Middlewares.md
```

## Contrôleurs

### BaseController.php
Contrôleur abstrait qui fournit les fonctionnalités communes à tous les contrôleurs :

- **Méthodes de réponse** : `jsonResponse()`, `errorResponse()`, `successResponse()`
- **Validation** : `validateInput()`, `sanitizeInput()`
- **Connexion base de données** : Accès à l'instance MySQL

### UtilisateursController.php
Gestion complète des utilisateurs avec les opérations CRUD :

- `index()` - Récupérer tous les utilisateurs
- `show($id)` - Récupérer un utilisateur par ID
- `store()` - Créer un nouvel utilisateur
- `update($id)` - Mettre à jour un utilisateur
- `destroy($id)` - Supprimer un utilisateur
- `login()` - Authentification utilisateur

### EleveController.php
Gestion spécifique des élèves avec relations utilisateur :

- `index()` - Récupérer tous les élèves avec données utilisateur
- `show($id)` - Récupérer un élève par ID
- `store()` - Créer un nouvel élève
- `update($id)` - Mettre à jour un élève
- `destroy($id)` - Supprimer un élève
- `getByEtablissement($etablissement)` - Filtrer par établissement
- `getBySection($section)` - Filtrer par section

## Middlewares

### AuthMiddleware.php
Gestion de l'authentification et des autorisations :

#### Méthodes d'authentification :
- `authenticate()` - Vérifier le token d'authentification
- `requireRole($role)` - Vérifier un rôle spécifique
- `requireAnyRole($roles)` - Vérifier un des rôles autorisés
- `requireOwnership($resourceUserId)` - Vérifier la propriété d'une ressource

#### Méthodes spécialisées :
- `publicRoute()` - Route publique (pas d'authentification)
- `adminOnly()` - Administrateurs seulement
- `adminPrincipalOnly()` - Administrateurs principaux seulement
- `eleveOnly()` - Élèves seulement
- `enseignantOnly()` - Enseignants seulement

### ValidationMiddleware.php
Validation des données d'entrée :

#### Méthodes de validation :
- `validateUtilisateur($data)` - Validation des données utilisateur
- `validateEleve($data)` - Validation des données élève
- `validateLogin($data)` - Validation des données de connexion
- `validatePasswordReset($data)` - Validation de récupération de mot de passe
- `validatePasswordChange($data)` - Validation de changement de mot de passe
- `validateSearch($data)` - Validation des paramètres de recherche

### CorsMiddleware.php
Gestion des requêtes Cross-Origin Resource Sharing (CORS) :

#### Méthodes de configuration :
- `handle()` - Configuration CORS de base
- `configure($config)` - Configuration personnalisée
- `configureForApi()` - Configuration pour API
- `configureForDevelopment()` - Configuration pour développement
- `configureForProduction()` - Configuration pour production

## Utilisation

### Exemple d'utilisation d'un contrôleur avec middleware

```php
<?php
require_once 'controller/UtilisateursController.php';
require_once 'middleware/AuthMiddleware.php';
require_once 'middleware/ValidationMiddleware.php';

// Initialiser les middlewares
$authMiddleware = new AuthMiddleware();
$validationMiddleware = new ValidationMiddleware();

// Initialiser le contrôleur
$controller = new UtilisateursController();

// Exemple : Créer un utilisateur avec validation et authentification
try {
    // Vérifier que l'utilisateur est un administrateur
    $currentUser = $authMiddleware->requireAnyRole(['admin', 'admin_principal']);
    
    // Récupérer et valider les données
    $input = json_decode(file_get_contents('php://input'), true);
    $errors = $validationMiddleware->validateUtilisateur($input);
    
    if (!empty($errors)) {
        $validationMiddleware->sendValidationError($errors);
    }
    
    // Créer l'utilisateur
    $controller->store();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
```

### Exemple de route avec différents niveaux d'accès

```php
<?php
// Route publique
$controller->publicEndpoint();

// Route protégée (authentification requise)
$controller->protectedEndpoint();

// Route administrateur seulement
$controller->adminEndpoint();

// Route élève seulement
$controller->eleveEndpoint();
?>
```

## Configuration CORS

### Pour le développement
```php
$corsMiddleware = new CorsMiddleware();
$corsMiddleware->configureForDevelopment();
```

### Pour la production
```php
$corsMiddleware = new CorsMiddleware();
$corsMiddleware->configureForProduction();
```

### Configuration personnalisée
```php
$corsMiddleware = new CorsMiddleware();
$corsMiddleware->configure([
    'origin' => ['https://votre-domaine.com'],
    'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
    'headers' => ['Content-Type', 'Authorization'],
    'credentials' => true
]);
```

## Sécurité

### Authentification
- Utilisation de tokens JWT-like stockés en base de données
- Expiration automatique des tokens (24h par défaut)
- Vérification des tokens à chaque requête protégée

### Validation
- Sanitisation automatique des données d'entrée
- Validation des formats (email, téléphone, dates)
- Vérification des rôles et permissions

### Autorisation
- Vérification des rôles utilisateur
- Contrôle d'accès aux ressources
- Protection contre l'accès non autorisé

## Bonnes pratiques

1. **Toujours utiliser les middlewares** pour l'authentification et la validation
2. **Sanitiser les données** avant traitement
3. **Valider les entrées** selon les règles métier
4. **Gérer les erreurs** avec des codes HTTP appropriés
5. **Utiliser les réponses JSON** pour l'API
6. **Configurer CORS** selon l'environnement

## Intégration avec le routeur

Pour intégrer ces contrôleurs avec votre système de routage existant, vous pouvez créer des routes qui utilisent les contrôleurs et middlewares appropriés selon les besoins de chaque endpoint. 