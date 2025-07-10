<?php
namespace Middleware;
class CorsMiddleware
{
    /**
     * Gérer les requêtes CORS
     */
    public function handle()
    {
        // Configuration par défaut plus sécurisée
        $this->configureForDevelopment();
        
        // Gérer les requêtes preflight OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Configuration CORS pour un domaine spécifique
     */
    public function setAllowedOrigin($origin)
    {
        header("Access-Control-Allow-Origin: $origin");
    }

    /**
     * Configuration CORS pour plusieurs domaines
     */
    public function setAllowedOrigins($origins)
    {
        $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        if (in_array($requestOrigin, $origins)) {
            header("Access-Control-Allow-Origin: $requestOrigin");
        }
    }

    /**
     * Configuration CORS pour les méthodes spécifiques
     */
    public function setAllowedMethods($methods)
    {
        $methodsString = implode(', ', $methods);
        header("Access-Control-Allow-Methods: $methodsString");
    }

    /**
     * Configuration CORS pour les headers spécifiques
     */
    public function setAllowedHeaders($headers)
    {
        $headersString = implode(', ', $headers);
        header("Access-Control-Allow-Headers: $headersString");
    }

    /**
     * Configuration CORS complète
     */
    public function configure($config)
    {
        // Origine
        if (isset($config['origin'])) {
            if (is_array($config['origin'])) {
                $this->setAllowedOrigins($config['origin']);
            } else {
                $this->setAllowedOrigin($config['origin']);
            }
        }

        // Méthodes
        if (isset($config['methods'])) {
            $this->setAllowedMethods($config['methods']);
        }

        // Headers
        if (isset($config['headers'])) {
            $this->setAllowedHeaders($config['headers']);
        }

        // Credentials
        if (isset($config['credentials']) && $config['credentials']) {
            header('Access-Control-Allow-Credentials: true');
        }

        // Max Age
        if (isset($config['maxAge'])) {
            header("Access-Control-Max-Age: {$config['maxAge']}");
        }

        // Headers de sécurité supplémentaires
        if (isset($config['security']) && $config['security']) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: DENY');
            header('X-XSS-Protection: 1; mode=block');
        }
    }

    /**
     * Configuration CORS par défaut pour l'API
     */
    public function configureForApi()
    {
        $this->configure([
            'origin' => ['http://localhost:3000', 'http://localhost:8080', 'https://votre-domaine.com'],
            'methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
            'credentials' => true,
            'maxAge' => 86400, // 24 heures
            'security' => true
        ]);
    }

    /**
     * Configuration CORS pour le développement
     */
    public function configureForDevelopment()
    {
        $this->configure([
            'origin' => ['http://localhost:3000', 'http://localhost:8080', 'http://127.0.0.1:3000', 'http://127.0.0.1:8080'],
            'methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
            'credentials' => true,
            'security' => true
        ]);
    }

    /**
     * Configuration CORS pour la production
     */
    public function configureForProduction()
    {
        $this->configure([
            'origin' => ['https://votre-domaine.com', 'https://www.votre-domaine.com'],
            'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
            'headers' => ['Content-Type', 'Authorization'],
            'credentials' => true,
            'maxAge' => 86400,
            'security' => true
        ]);
    }

    /**
     * Configuration CORS restrictive (recommandée pour la production)
     */
    public function configureRestrictive()
    {
        $this->configure([
            'origin' => ['https://votre-domaine.com'],
            'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
            'headers' => ['Content-Type', 'Authorization'],
            'credentials' => true,
            'maxAge' => 3600, // 1 heure
            'security' => true
        ]);
    }

    /**
     * Configuration CORS pour les applications mobiles
     */
    public function configureForMobile()
    {
        $this->configure([
            'origin' => ['capacitor://localhost', 'ionic://localhost', 'http://localhost'],
            'methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
            'credentials' => true,
            'security' => true
        ]);
    }

    /**
     * Vérifier si l'origine est autorisée
     */
    public function isOriginAllowed($origin, $allowedOrigins)
    {
        return in_array($origin, $allowedOrigins);
    }

    /**
     * Obtenir l'origine de la requête
     */
    public function getRequestOrigin()
    {
        return $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
    }

    /**
     * Middleware pour bloquer les requêtes non autorisées
     */
    public function blockUnauthorizedRequests()
    {
        $requestOrigin = $this->getRequestOrigin();
        $allowedOrigins = ['http://localhost:3000', 'http://localhost:8080', 'https://votre-domaine.com'];
        
        if (!empty($requestOrigin) && !$this->isOriginAllowed($requestOrigin, $allowedOrigins)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Origine non autorisée']);
            exit;
        }
    }
}
?> 