<?php
namespace Middleware;
class CorsMiddleware
{
    /**
     * Gérer les requêtes CORS
     */
    public function handle()
    {
        // Autoriser les requêtes depuis n'importe quelle origine (à modifier selon vos besoins)
        header('Access-Control-Allow-Origin: *');
        
        // Autoriser les méthodes HTTP
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        
        // Autoriser les headers
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        // Autoriser les credentials
        header('Access-Control-Allow-Credentials: true');
        
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
            'maxAge' => 86400 // 24 heures
        ]);
    }

    /**
     * Configuration CORS pour le développement
     */
    public function configureForDevelopment()
    {
        $this->configure([
            'origin' => '*',
            'methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
            'credentials' => false
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
            'maxAge' => 86400
        ]);
    }
}
?> 