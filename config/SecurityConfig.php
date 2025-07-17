<?php
namespace Config;

class SecurityConfig
{
    // Configuration des sessions
    const SESSION_NAME = 'MCLEGENDE_SESSION';
    const SESSION_TIMEOUT = 3600; // 1 heure
    const SESSION_REGENERATE_INTERVAL = 300; // 5 minutes
    
    // Configuration des mots de passe
    const PASSWORD_MIN_LENGTH = 8;
    const PASSWORD_REQUIRE_UPPERCASE = true;
    const PASSWORD_REQUIRE_LOWERCASE = true;
    const PASSWORD_REQUIRE_NUMBERS = true;
    const PASSWORD_REQUIRE_SPECIAL_CHARS = true;
    const PASSWORD_EXPIRY_DAYS = 90; // 90 jours
    
    // Configuration des tentatives de connexion
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_DURATION = 900; // 15 minutes
    const RESET_ATTEMPTS_AFTER = 900; // 15 minutes
    
    // Configuration des tokens
    const RESET_TOKEN_EXPIRY = 3600; // 1 heure
    const EMAIL_VERIFICATION_EXPIRY = 86400; // 24 heures
    const CSRF_TOKEN_EXPIRY = 3600; // 1 heure
    
    // Configuration des en-têtes de sécurité
    const SECURITY_HEADERS = [
        'X-Frame-Options' => 'DENY',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'none';",
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload'
    ];
    
    // Configuration des types de fichiers autorisés
    const ALLOWED_FILE_TYPES = [
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'spreadsheet' => ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'presentation' => ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation']
    ];
    
    const MAX_FILE_SIZE = 5242880; // 5MB
    
    // Configuration des rôles et permissions
    const ROLES = [
        'admin' => [
            'name' => 'Administrateur Principal',
            'permissions' => ['all']
        ],
        'admin_principal' => [
            'name' => 'Administrateur Principal',
            'permissions' => ['manage_users', 'manage_content', 'view_reports', 'manage_settings']
        ],
        'admin_simple' => [
            'name' => 'Administrateur Simple',
            'permissions' => ['view_reports', 'manage_content']
        ],
        'enseignant' => [
            'name' => 'Enseignant',
            'permissions' => ['create_quiz', 'view_student_results', 'manage_own_content']
        ],
        'eleve' => [
            'name' => 'Élève',
            'permissions' => ['take_quiz', 'view_own_results', 'view_own_profile']
        ]
    ];
    
    // Configuration des routes sécurisées
    const SECURE_ROUTES = [
        '/admin' => ['admin', 'admin_principal'],
        '/admin/home' => ['admin', 'admin_principal'],
        '/admin/users' => ['admin', 'admin_principal'],
        '/admin/settings' => ['admin', 'admin_principal'],
        '/eleve' => ['eleve'],
        '/eleve/home' => ['eleve'],
        '/eleve/quiz' => ['eleve'],
        '/api/admin' => ['admin', 'admin_principal'],
        '/api/eleve' => ['eleve'],
        '/api/enseignant' => ['enseignant']
    ];
    
    // Configuration des routes publiques (pas d'authentification requise)
    const PUBLIC_ROUTES = [
        '/',
        '/connexion',
        '/inscription',
        '/reset-password',
        '/api/auth/login',
        '/api/auth/register',
        '/api/auth/reset-password'
    ];
    
    // Configuration des IPs autorisées (optionnel)
    const ALLOWED_IPS = [
        // '127.0.0.1',
        // '192.168.1.0/24'
    ];
    
    // Configuration des domaines autorisés pour les redirections
    const ALLOWED_DOMAINS = [
        'localhost',
        '127.0.0.1',
        // Ajouter vos domaines de production ici
    ];
    
    // Configuration du rate limiting
    const RATE_LIMIT = [
        'login' => ['requests' => 5, 'window' => 900], // 5 tentatives en 15 minutes
        'api' => ['requests' => 100, 'window' => 3600], // 100 requêtes par heure
        'file_upload' => ['requests' => 10, 'window' => 3600] // 10 uploads par heure
    ];
    
    // Configuration du logging
    const LOG_LEVELS = [
        'ERROR' => 1,
        'WARNING' => 2,
        'INFO' => 3,
        'DEBUG' => 4
    ];
    
    const LOG_SECURITY_EVENTS = true;
    const LOG_USER_ACTIONS = true;
    const LOG_API_CALLS = true;
    
    // Configuration de l'encryption
    const ENCRYPTION_KEY = 'your-secret-key-here'; // À changer en production
    const ENCRYPTION_METHOD = 'AES-256-CBC';
    
    // Configuration des cookies
    const COOKIE_SECURE = true;
    const COOKIE_HTTPONLY = true;
    const COOKIE_SAMESITE = 'Strict';
    const COOKIE_PATH = '/';
    const COOKIE_DOMAIN = ''; // Laissez vide pour le domaine actuel
    
    /**
     * Appliquer les en-têtes de sécurité
     */
    public static function applySecurityHeaders()
    {
        foreach (self::SECURITY_HEADERS as $header => $value) {
            header("$header: $value");
        }
    }
    
    /**
     * Vérifier si une IP est autorisée
     */
    public static function isIPAllowed($ip)
    {
        if (empty(self::ALLOWED_IPS)) {
            return true; // Aucune restriction si la liste est vide
        }
        
        foreach (self::ALLOWED_IPS as $allowedIP) {
            if (self::ipInRange($ip, $allowedIP)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Vérifier si une IP est dans une plage donnée
     */
    private static function ipInRange($ip, $range)
    {
        if (strpos($range, '/') !== false) {
            // Notation CIDR
            list($subnet, $mask) = explode('/', $range);
            $ip_binary = ip2long($ip);
            $subnet_binary = ip2long($subnet);
            $mask_binary = ~((1 << (32 - $mask)) - 1);
            
            return ($ip_binary & $mask_binary) == ($subnet_binary & $mask_binary);
        } else {
            // IP unique
            return $ip === $range;
        }
    }
    
    /**
     * Vérifier si un domaine est autorisé
     */
    public static function isDomainAllowed($domain)
    {
        return in_array($domain, self::ALLOWED_DOMAINS);
    }
    
    /**
     * Vérifier si une route nécessite une authentification
     */
    public static function isRouteSecure($route)
    {
        return !in_array($route, self::PUBLIC_ROUTES);
    }
    
    /**
     * Obtenir les rôles autorisés pour une route
     */
    public static function getRouteRoles($route)
    {
        foreach (self::SECURE_ROUTES as $pattern => $roles) {
            if (strpos($route, $pattern) === 0) {
                return $roles;
            }
        }
        
        return []; // Aucun rôle spécifique requis
    }
    
    /**
     * Vérifier si un utilisateur a une permission
     */
    public static function hasPermission($role, $permission)
    {
        if (!isset(self::ROLES[$role])) {
            return false;
        }
        
        $permissions = self::ROLES[$role]['permissions'];
        
        return in_array('all', $permissions) || in_array($permission, $permissions);
    }
    
    /**
     * Obtenir la configuration des cookies
     */
    public static function getCookieConfig()
    {
        return [
            'secure' => self::COOKIE_SECURE,
            'httponly' => self::COOKIE_HTTPONLY,
            'samesite' => self::COOKIE_SAMESITE,
            'path' => self::COOKIE_PATH,
            'domain' => self::COOKIE_DOMAIN
        ];
    }
    
    /**
     * Générer une clé de chiffrement sécurisée
     */
    public static function generateEncryptionKey()
    {
        return base64_encode(random_bytes(32));
    }
    
    /**
     * Chiffrer des données
     */
    public static function encrypt($data)
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, self::ENCRYPTION_METHOD, self::ENCRYPTION_KEY, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Déchiffrer des données
     */
    public static function decrypt($encryptedData)
    {
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, self::ENCRYPTION_METHOD, self::ENCRYPTION_KEY, 0, $iv);
    }
}
?> 