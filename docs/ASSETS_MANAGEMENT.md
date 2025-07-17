# Gestion des Assets CSS et JS - MC-LEGENDE

## 📋 Vue d'ensemble

Le système de gestion des assets de MC-LEGENDE centralise et simplifie la gestion des fichiers CSS et JavaScript. Il permet de :

- **Centraliser** tous les liens vers les assets
- **Automatiser** la génération des balises HTML
- **Gérer** les CDN et fichiers locaux
- **Optimiser** le chargement des ressources
- **Maintenir** facilement les dépendances

## 🏗️ Architecture

### Fichiers principaux

```
config/
├── AssetsConfig.php     # Configuration des assets
└── Config.php          # Configuration générale

helpers/
└── AssetsHelper.php    # Helper pour l'utilisation

views/
├── eleve.php          # Vue avec ancien système
└── eleve_new.php      # Vue avec nouveau système
```

## 🔧 Configuration

### 1. AssetsConfig.php

```php
// Configuration de base
private static $baseUrl = '';
private static $assetsPath = '/assets';
private static $adminltePath = '/adminlte';

// CSS Libraries (CDN)
private static $cssLibraries = [
    'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'fontawesome' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'animate' => 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
    'fullcalendar' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css'
];

// JS Libraries (CDN)
private static $jsLibraries = [
    'jquery' => 'https://code.jquery.com/jquery-3.6.0.min.js',
    'bootstrap' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'sweetalert2' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
    'fullcalendar' => 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'
];

// Local Assets
private static $localAssets = [
    'css' => [
        'adminlte' => '/adminlte/dist/css/adminlte.min.css',
        'fontawesome' => '/adminlte/plugins/fontawesome-free/css/all.min.css'
    ],
    'js' => [
        'adminlte' => '/adminlte/dist/js/adminlte.min.js',
        'jquery' => '/adminlte/plugins/jquery/jquery.min.js',
        'bootstrap' => '/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js'
    ]
];
```

### 2. Assets prédéfinis par page

```php
public static function getPageAssets($page)
{
    $assets = [
        'default' => [
            'css' => [
                ['name' => 'bootstrap', 'type' => 'cdn'],
                ['name' => 'fontawesome', 'type' => 'cdn'],
                ['name' => 'adminlte', 'type' => 'local']
            ],
            'js' => [
                ['name' => 'jquery', 'type' => 'local'],
                ['name' => 'bootstrap', 'type' => 'local'],
                ['name' => 'adminlte', 'type' => 'local']
            ]
        ],
        'eleve' => [
            'css' => [
                ['name' => 'assets-fontawesome', 'type' => 'local'],
                ['name' => 'assets-adminlte', 'type' => 'local'],
                ['name' => 'animate', 'type' => 'cdn'],
                ['name' => 'fullcalendar', 'type' => 'cdn']
            ],
            'js' => [
                ['name' => 'assets-bootstrap', 'type' => 'local'],
                ['name' => 'assets-adminlte', 'type' => 'local'],
                ['name' => 'fullcalendar', 'type' => 'cdn']
            ]
        ]
    ];
    
    return isset($assets[$page]) ? $assets[$page] : $assets['default'];
}
```

## 🚀 Utilisation

### 1. Dans les contrôleurs

```php
// PageController.php
public function eleve_home(){
   $this->view('eleve', 'eleve'); // Type de page 'eleve'
}

private function view($view, $pageType = 'default')
{
   // Initialiser le helper d'assets
   \Helpers\AssetsHelper::init();
   require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view . '.php';
}
```

### 2. Dans les vues

#### Ancien système (manuel)
```html
<!-- CSS -->
<link href="/assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="/assets/dist/css/adminlte.min.css" rel="stylesheet">
<link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">

<!-- JS -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>
<script src="adminlte/plugins/jquery/jquery.min.js"></script>
<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="adminlte/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
```

#### Nouveau système (automatisé)
```html
<!-- CSS automatique pour la page 'eleve' -->
<?= \Helpers\AssetsHelper::css('eleve') ?>

<!-- JS automatique pour la page 'eleve' -->
<?= \Helpers\AssetsHelper::js('eleve') ?>
```

### 3. Utilisation avancée

#### Balises personnalisées
```php
// CSS personnalisé
<?= \Helpers\AssetsHelper::cssTag('custom-style', 'local') ?>
<?= \Helpers\AssetsHelper::cssTag('bootstrap', 'cdn') ?>

// JS personnalisé
<?= \Helpers\AssetsHelper::jsTag('custom-script', 'local') ?>
<?= \Helpers\AssetsHelper::jsTag('sweetalert2', 'cdn') ?>
```

#### Images et fichiers
```php
// Images
<?= \Helpers\AssetsHelper::img('logo.png', 'Logo', ['class' => 'img-fluid']) ?>
<?= \Helpers\AssetsHelper::imgSrc('avatar.jpg') ?>

// Fichiers uploadés
<?= \Helpers\AssetsHelper::file('documents/rapport.pdf') ?>

// Audio/Video
<?= \Helpers\AssetsHelper::audio('notification.mp3', ['autoplay' => '']) ?>
<?= \Helpers\AssetsHelper::video('tutorial.mp4', ['controls' => '']) ?>
```

#### Meta tags
```php
// Meta tags de base
<?= \Helpers\AssetsHelper::metaTags('Titre de la page', 'Description', 'mots,clés') ?>

// Meta tags sociaux
<?= \Helpers\AssetsHelper::socialMeta('Titre', 'Description', 'image.jpg', 'https://url.com') ?>

// Favicon
<?= \Helpers\AssetsHelper::favicon() ?>
```

## 📁 Structure des dossiers

```
mc-legende/
├── assets/                    # Assets locaux
│   ├── css/
│   ├── js/
│   ├── img/
│   └── plugins/
├── adminlte/                  # AdminLTE framework
│   ├── dist/
│   │   ├── css/
│   │   └── js/
│   └── plugins/
├── images/                    # Images du projet
├── uploads/                   # Fichiers uploadés
├── config/
│   ├── AssetsConfig.php      # Configuration assets
│   └── Config.php           # Configuration générale
├── helpers/
│   └── AssetsHelper.php     # Helper assets
└── views/
    ├── eleve.php            # Vue avec ancien système
    └── eleve_new.php        # Vue avec nouveau système
```

## 🔄 Migration depuis l'ancien système

### Étape 1 : Mettre à jour composer.json
```json
{
    "autoload": {
        "psr-4": {
            "Helpers\\": "helpers/"
        }
    }
}
```

### Étape 2 : Régénérer l'autoloader
```bash
composer dump-autoload
```

### Étape 3 : Mettre à jour les contrôleurs
```php
// Avant
public function eleve_home(){
   $this->view('eleve');
}

// Après
public function eleve_home(){
   $this->view('eleve', 'eleve');
}
```

### Étape 4 : Mettre à jour les vues
```php
// Avant (dans eleve.php)
<link href="/assets/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="/assets/dist/css/adminlte.min.css" rel="stylesheet">
<!-- ... 10+ lignes de CSS ... -->

<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>
<!-- ... 10+ lignes de JS ... -->

// Après (dans eleve_new.php)
<?= \Helpers\AssetsHelper::css('eleve') ?>
<?= \Helpers\AssetsHelper::js('eleve') ?>
```

## 🎯 Avantages du nouveau système

### ✅ Avantages
- **Centralisation** : Tous les assets dans un seul endroit
- **Maintenance** : Facile d'ajouter/supprimer des dépendances
- **Performance** : Optimisation automatique du chargement
- **Flexibilité** : Support CDN et local
- **Sécurité** : Validation des URLs et chemins
- **Réutilisabilité** : Configurations par type de page

### 📊 Comparaison

| Aspect | Ancien système | Nouveau système |
|--------|----------------|-----------------|
| **Lignes de code** | 15+ lignes par vue | 2 lignes par vue |
| **Maintenance** | Manuelle dans chaque fichier | Centralisée |
| **Erreurs** | Fréquentes (typos, chemins) | Minimisées |
| **Performance** | Non optimisée | Optimisée |
| **Flexibilité** | Limitée | Élevée |

## 🛠️ Fonctions disponibles

### AssetsHelper

```php
// Initialisation
\Helpers\AssetsHelper::init($baseUrl);

// CSS
\Helpers\AssetsHelper::css($pageType);
\Helpers\AssetsHelper::cssTag($name, $type, $attributes);
\Helpers\AssetsHelper::cssTags($assets);

// JS
\Helpers\AssetsHelper::js($pageType);
\Helpers\AssetsHelper::jsTag($name, $type, $attributes);
\Helpers\AssetsHelper::jsTags($assets);

// Images et fichiers
\Helpers\AssetsHelper::img($src, $alt, $attributes);
\Helpers\AssetsHelper::imgSrc($src);
\Helpers\AssetsHelper::file($path);

// Média
\Helpers\AssetsHelper::audio($src, $attributes);
\Helpers\AssetsHelper::video($src, $attributes);

// Meta tags
\Helpers\AssetsHelper::metaTags($title, $description, $keywords, $author);
\Helpers\AssetsHelper::socialMeta($title, $description, $image, $url);
\Helpers\AssetsHelper::favicon($type);
```

### AssetsConfig

```php
// Configuration
\Config\AssetsConfig::setBaseUrl($url);
\Config\AssetsConfig::getBaseUrl();

// Génération d'URLs
\Config\AssetsConfig::css($name, $type);
\Config\AssetsConfig::js($name, $type);

// Génération de balises
\Config\AssetsConfig::cssTag($name, $type, $attributes);
\Config\AssetsConfig::jsTag($name, $type, $attributes);

// Assets prédéfinis
\Config\AssetsConfig::getPageAssets($page);
```

## 🔧 Configuration avancée

### Ajouter une nouvelle bibliothèque

```php
// Dans AssetsConfig.php
private static $cssLibraries = [
    'nouvelle-lib' => 'https://cdn.example.com/nouvelle-lib.css'
];

private static $jsLibraries = [
    'nouvelle-lib' => 'https://cdn.example.com/nouvelle-lib.js'
];
```

### Créer un nouveau type de page

```php
// Dans AssetsConfig.php
'nouvelle-page' => [
    'css' => [
        ['name' => 'bootstrap', 'type' => 'cdn'],
        ['name' => 'nouvelle-lib', 'type' => 'cdn']
    ],
    'js' => [
        ['name' => 'jquery', 'type' => 'local'],
        ['name' => 'nouvelle-lib', 'type' => 'cdn']
    ]
]
```

### Utiliser dans une vue

```php
// Dans le contrôleur
$this->view('nouvelle-vue', 'nouvelle-page');

// Dans la vue
<?= \Helpers\AssetsHelper::css('nouvelle-page') ?>
<?= \Helpers\AssetsHelper::js('nouvelle-page') ?>
```

## 🚨 Dépannage

### Problème : Assets non chargés
```php
// Vérifier l'initialisation
\Helpers\AssetsHelper::init();

// Vérifier le type de page
echo \Helpers\AssetsHelper::css('eleve');
```

### Problème : Chemins incorrects
```php
// Vérifier la configuration
echo \Config\AssetsConfig::getBaseUrl();

// Forcer une URL de base
\Config\AssetsConfig::setBaseUrl('http://localhost/mc-legende');
```

### Problème : Autoloader
```bash
# Régénérer l'autoloader
composer dump-autoload
```

## 📈 Performance

### Optimisations automatiques
- **Minification** : Utilisation des versions minifiées
- **CDN** : Chargement depuis des CDN rapides
- **Ordre** : Chargement dans l'ordre optimal
- **Cache** : Headers de cache appropriés

### Monitoring
```php
// Activer le debug
\Config\AssetsConfig::setDebug(true);

// Voir les assets chargés
echo \Helpers\AssetsHelper::getLoadedAssets();
```

---

**Note** : Ce système remplace progressivement l'ancien système de gestion des assets. Les vues existantes continuent de fonctionner pendant la migration. 