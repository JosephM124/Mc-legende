<?php
namespace Middleware;

class BaseMiddleware
{
    protected $authMiddleware;
    protected $validationMiddleware;
    protected $corsMiddleware;

    public function __construct()
    {
        $this->authMiddleware = new AuthMiddleware();
        $this->validationMiddleware = new ValidationMiddleware();
        $this->corsMiddleware = new CorsMiddleware();
    }

    /**
     * Appliquer CORS
     */
    public function applyCors($environment = 'development')
    {
        switch ($environment) {
            case 'production':
                $this->corsMiddleware->configureForProduction();
                break;
            case 'mobile':
                $this->corsMiddleware->configureForMobile();
                break;
            case 'restrictive':
                $this->corsMiddleware->configureRestrictive();
                break;
            default:
                $this->corsMiddleware->configureForDevelopment();
                break;
        }
    }

    /**
     * Middleware pour les routes publiques
     */
    public function publicRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->publicRoute();
    }

    /**
     * Middleware pour les routes authentifiées
     */
    public function authenticatedRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->authenticatedOnly();
    }

    /**
     * Middleware pour les routes d'administration
     */
    public function adminRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminOnly();
    }

    /**
     * Middleware pour les routes d'administration principale
     */
    public function adminPrincipalRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminPrincipalOnly();
    }

    /**
     * Middleware pour les routes d'élèves
     */
    public function eleveRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->eleveOnly();
    }

    /**
     * Middleware pour les routes d'enseignants
     */
    public function enseignantRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->enseignantOnly();
    }

    /**
     * Middleware pour les routes d'administration (admin ou admin_principal)
     */
    public function adminOrPrincipalRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminOrPrincipal();
    }

    /**
     * Middleware pour les routes avec validation
     */
    public function validateRoute($validationMethod, $data)
    {
        $this->applyCors();
        $user = $this->authMiddleware->authenticatedOnly();
        
        $errors = $this->validationMiddleware->$validationMethod($data);
        if (!empty($errors)) {
            $this->validationMiddleware->sendValidationError($errors);
        }
        
        return $user;
    }

    /**
     * Middleware pour les routes avec accès aux ressources
     */
    public function resourceRoute($resourceType, $resourceId)
    {
        $this->applyCors();
        return $this->authMiddleware->requireResourceAccess($resourceType, $resourceId);
    }

    /**
     * Middleware pour les routes avec propriété
     */
    public function ownershipRoute($resourceUserId)
    {
        $this->applyCors();
        return $this->authMiddleware->requireOwnership($resourceUserId);
    }

    /**
     * Middleware pour les routes avec rôle spécifique
     */
    public function roleRoute($role)
    {
        $this->applyCors();
        return $this->authMiddleware->requireRole($role);
    }

    /**
     * Middleware pour les routes avec rôles multiples
     */
    public function anyRoleRoute($roles)
    {
        $this->applyCors();
        return $this->authMiddleware->requireAnyRole($roles);
    }

    /**
     * Middleware pour les routes d'API avec validation
     */
    public function apiRoute($validationMethod = null, $data = null)
    {
        $this->applyCors('production');
        $user = $this->authMiddleware->authenticatedOnly();
        
        if ($validationMethod && $data) {
            $errors = $this->validationMiddleware->$validationMethod($data);
            if (!empty($errors)) {
                $this->validationMiddleware->sendValidationError($errors);
            }
        }
        
        return $user;
    }

    /**
     * Middleware pour les routes d'upload de fichiers
     */
    public function fileUploadRoute($allowedTypes = [], $maxSize = 5242880)
    {
        $this->applyCors();
        $user = $this->authMiddleware->authenticatedOnly();
        
        if (isset($_FILES['file'])) {
            $errors = $this->validationMiddleware->validateFileUpload($_FILES['file'], $allowedTypes, $maxSize);
            if (!empty($errors)) {
                $this->validationMiddleware->sendValidationError($errors);
            }
        }
        
        return $user;
    }

    /**
     * Middleware pour les routes de recherche
     */
    public function searchRoute($searchData)
    {
        $this->applyCors();
        $user = $this->authMiddleware->authenticatedOnly();
        
        $errors = $this->validationMiddleware->validateSearch($searchData);
        if (!empty($errors)) {
            $this->validationMiddleware->sendValidationError($errors);
        }
        
        return $user;
    }

    /**
     * Middleware pour les routes de statistiques (admin seulement)
     */
    public function statsRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminOrPrincipal();
    }

    /**
     * Middleware pour les routes d'export (admin seulement)
     */
    public function exportRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminOrPrincipal();
    }

    /**
     * Middleware pour les routes d'import (admin seulement)
     */
    public function importRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminOrPrincipal();
    }

    /**
     * Middleware pour les routes de configuration (admin principal seulement)
     */
    public function configRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminPrincipalOnly();
    }

    /**
     * Middleware pour les routes de logs (admin principal seulement)
     */
    public function logsRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminPrincipalOnly();
    }

    /**
     * Middleware pour les routes de backup (admin principal seulement)
     */
    public function backupRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminPrincipalOnly();
    }

    /**
     * Middleware pour les routes de permissions (admin principal seulement)
     */
    public function permissionsRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminPrincipalOnly();
    }

    /**
     * Middleware pour les routes de cache (admin principal seulement)
     */
    public function cacheRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminPrincipalOnly();
    }

    /**
     * Middleware pour les routes de tokens (admin principal seulement)
     */
    public function tokensRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminPrincipalOnly();
    }

    /**
     * Middleware pour les routes de sessions (admin principal seulement)
     */
    public function sessionsRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminPrincipalOnly();
    }

    /**
     * Middleware pour les routes de validation (publiques)
     */
    public function validationRoute($validationMethod, $data)
    {
        $this->applyCors();
        
        $errors = $this->validationMiddleware->$validationMethod($data);
        if (!empty($errors)) {
            $this->validationMiddleware->sendValidationError($errors);
        }
        
        return true;
    }

    /**
     * Middleware pour les routes de notifications
     */
    public function notificationsRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->authenticatedOnly();
    }

    /**
     * Middleware pour les routes de messages
     */
    public function messagesRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->authenticatedOnly();
    }

    /**
     * Middleware pour les routes de rapports (admin seulement)
     */
    public function reportsRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->adminOrPrincipal();
    }

    /**
     * Middleware pour les routes de profils
     */
    public function profileRoute($userId = null)
    {
        $this->applyCors();
        if ($userId) {
            return $this->authMiddleware->requireOwnership($userId);
        }
        return $this->authMiddleware->authenticatedOnly();
    }

    /**
     * Middleware pour les routes de paramètres
     */
    public function settingsRoute()
    {
        $this->applyCors();
        return $this->authMiddleware->authenticatedOnly();
    }

    /**
     * Middleware pour les routes de préférences
     */
    public function preferencesRoute($userId = null)
    {
        $this->applyCors();
        if ($userId) {
            return $this->authMiddleware->requireOwnership($userId);
        }
        return $this->authMiddleware->authenticatedOnly();
    }
}
?> 