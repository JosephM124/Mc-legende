# Documentation des Middlewares

## Vue d'ensemble

Les middlewares de MC-LEGENDE fournissent une couche de sécurité et de validation pour toutes les routes de l'application. Ils sont organisés en trois catégories principales :

1. **AuthMiddleware** - Authentification et autorisation
2. **ValidationMiddleware** - Validation des données
3. **CorsMiddleware** - Gestion des requêtes CORS
4. **BaseMiddleware** - Intégration de tous les middlewares

## 🔐 AuthMiddleware

### Fonctionnalités principales

- **Authentification par token** : Vérification des tokens JWT ou de session
- **Gestion des rôles** : Contrôle d'accès basé sur les rôles utilisateur
- **Vérification de propriété** : Accès aux ressources selon la propriété
- **Gestion des sessions** : Support des sessions PHP et tokens

### Méthodes disponibles

```php
// Authentification de base
$user = $authMiddleware->authenticate();

// Vérification de rôle spécifique
$user = $authMiddleware->requireRole('admin');

// Vérification de rôles multiples
$user = $authMiddleware->requireAnyRole(['admin', 'admin_principal']);

// Vérification de propriété
$user = $authMiddleware->requireOwnership($resourceUserId);

// Vérification d'accès aux ressources
$user = $authMiddleware->requireResourceAccess('eleve', $eleveId);

// Vérification d'utilisateur actif
$user = $authMiddleware->requireActiveUser();
```

### Middlewares prédéfinis

```php
// Routes publiques
$authMiddleware->publicRoute();

// Routes d'administration
$authMiddleware->adminOnly();
$authMiddleware->adminPrincipalOnly();
$authMiddleware->adminOrPrincipal();

// Routes d'utilisateurs
$authMiddleware->eleveOnly();
$authMiddleware->enseignantOnly();
$authMiddleware->authenticatedOnly();
```

## ✅ ValidationMiddleware

### Fonctionnalités principales

- **Validation des entités** : Utilisateurs, élèves, interrogations, etc.
- **Validation des fichiers** : Uploads avec vérification de type et taille
- **Validation des formulaires** : Connexion, récupération de mot de passe, etc.
- **Messages d'erreur personnalisés** : Retours d'erreur détaillés

### Méthodes de validation

```php
// Validation des utilisateurs
$errors = $validationMiddleware->validateUtilisateur($data);

// Validation des élèves
$errors = $validationMiddleware->validateEleve($data);

// Validation des interrogations
$errors = $validationMiddleware->validateInterrogation($data);

// Validation des questions
$errors = $validationMiddleware->validateQuestion($data);

// Validation des résultats
$errors = $validationMiddleware->validateResultat($data);

// Validation des fichiers
$errors = $validationMiddleware->validateFileUpload($file, $allowedTypes, $maxSize);

// Validation des notifications
$errors = $validationMiddleware->validateNotification($data);

// Validation de recherche
$errors = $validationMiddleware->validateSearch($data);
```

### Envoi d'erreurs de validation

```php
if (!empty($errors)) {
    $validationMiddleware->sendValidationError($errors);
}
```

## 🌐 CorsMiddleware

### Fonctionnalités principales

- **Gestion CORS** : Configuration des origines autorisées
- **Sécurité renforcée** : Headers de sécurité supplémentaires
- **Configurations prédéfinies** : Développement, production, mobile
- **Blocage des requêtes non autorisées**

### Configurations disponibles

```php
// Configuration pour le développement
$corsMiddleware->configureForDevelopment();

// Configuration pour la production
$corsMiddleware->configureForProduction();

// Configuration restrictive
$corsMiddleware->configureRestrictive();

// Configuration pour mobile
$corsMiddleware->configureForMobile();

// Configuration personnalisée
$corsMiddleware->configure([
    'origin' => ['https://votre-domaine.com'],
    'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
    'headers' => ['Content-Type', 'Authorization'],
    'credentials' => true,
    'security' => true
]);
```

## 🔧 BaseMiddleware

### Intégration complète

Le `BaseMiddleware` combine tous les middlewares en une interface unifiée :

```php
$middleware = new \Middleware\BaseMiddleware();

// Routes avec CORS automatique
$user = $middleware->authenticatedRoute();
$user = $middleware->adminRoute();
$user = $middleware->eleveRoute();

// Routes avec validation
$user = $middleware->validateRoute('validateUtilisateur', $data);

// Routes avec accès aux ressources
$user = $middleware->resourceRoute('eleve', $eleveId);

// Routes spécialisées
$user = $middleware->fileUploadRoute($allowedTypes, $maxSize);
$user = $middleware->searchRoute($searchData);
$user = $middleware->statsRoute();
$user = $middleware->exportRoute();
```

## 📝 Exemples d'utilisation

### Dans un contrôleur

```php
class MonController extends BaseController
{
    private $middleware;

    public function __construct()
    {
        parent::__construct();
        $this->middleware = new \Middleware\BaseMiddleware();
    }

    public function createUser()
    {
        // Authentification admin + validation
        $user = $this->middleware->adminRoute();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation automatique
        $this->middleware->validateRoute('validateUtilisateur', $input);

        // Logique métier...
        $this->successResponse($result, 'Utilisateur créé');
    }

    public function uploadAvatar()
    {
        // Authentification + validation fichier
        $user = $this->middleware->fileUploadRoute(
            ['image/jpeg', 'image/png'],
            2 * 1024 * 1024 // 2MB
        );

        // Traitement du fichier...
        $this->successResponse($result, 'Avatar uploadé');
    }
}
```

### Routes avec accès aux ressources

```php
public function getEleveResultats($eleveId)
{
    // Vérification que l'utilisateur peut accéder à cet élève
    $user = $this->middleware->resourceRoute('eleve', $eleveId);
    
    // Récupération des résultats...
    $this->successResponse($resultats, 'Résultats récupérés');
}
```

### Routes de recherche

```php
public function searchEleves()
{
    // Authentification + validation recherche
    $user = $this->middleware->searchRoute($_GET);
    
    // Recherche...
    $this->successResponse($eleves, 'Recherche effectuée');
}
```

## 🔒 Sécurité

### Bonnes pratiques

1. **Toujours utiliser les middlewares** pour les routes sensibles
2. **Valider toutes les entrées** utilisateur
3. **Configurer CORS** selon l'environnement
4. **Vérifier les permissions** avant l'accès aux ressources
5. **Utiliser des tokens sécurisés** pour l'authentification

### Configuration de sécurité

```php
// Production - Configuration restrictive
$corsMiddleware->configureRestrictive();

// Développement - Configuration permissive
$corsMiddleware->configureForDevelopment();

// Headers de sécurité automatiques
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
```

## 🚀 Intégration avec les routes

### Utilisation dans les routes

```php
// routes/get.php
\Router\Router::get('/api/eleves/[i:id]', function($id){
    $controller = new \Controllers\EleveController();
    $controller->show($id); // Middleware intégré dans le contrôleur
});

// routes/post.php
\Router\Router::post('/api/utilisateurs', function(){
    $controller = new \Controllers\UtilisateursController();
    $controller->store(); // Validation automatique
});
```

### Middleware global

Pour appliquer un middleware à toutes les routes :

```php
// Dans index.php ou bootstrap
$corsMiddleware = new \Middleware\CorsMiddleware();
$corsMiddleware->handle(); // Applique CORS à toutes les requêtes
```

## 📋 Checklist de sécurité

- [ ] Toutes les routes sensibles utilisent l'authentification
- [ ] Toutes les entrées utilisateur sont validées
- [ ] CORS est configuré selon l'environnement
- [ ] Les permissions sont vérifiées avant l'accès aux ressources
- [ ] Les tokens d'authentification sont sécurisés
- [ ] Les erreurs ne révèlent pas d'informations sensibles
- [ ] Les uploads de fichiers sont validés
- [ ] Les sessions sont gérées de manière sécurisée

## 🔧 Dépannage

### Problèmes courants

1. **Erreur CORS** : Vérifier la configuration des origines autorisées
2. **Token invalide** : Vérifier l'expiration et la validité du token
3. **Validation échoue** : Vérifier les règles de validation
4. **Accès refusé** : Vérifier les permissions et rôles

### Debug

```php
// Activer le debug des middlewares
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log des middlewares
error_log("Middleware: " . json_encode($data));
``` 