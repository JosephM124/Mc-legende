<?php
namespace Middleware;

class AuthMiddleware
{
    private $database;

    public function __construct()
    {
        $this->database = \App\App::getMysqlDatabaseInstance();
    }

    /**
     * Vérifier si l'utilisateur est authentifié
     */
    public function authenticate()
    {
        $headers = getallheaders();
        $token = null;

        // Récupérer le token depuis les headers
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
            }
        }

        // Si pas de token dans les headers, vérifier dans les paramètres GET
        if (!$token && isset($_GET['token'])) {
            $token = $_GET['token'];
        }

        if (!$token) {
            $this->sendUnauthorizedResponse('Token d\'authentification requis');
        }

        try {
            $user = $this->database->select(
                "SELECT * FROM utilisateurs WHERE reset_token = ? AND token_expiration > NOW()",
                [$token]
            );

            if (empty($user)) {
                $this->sendUnauthorizedResponse('Token invalide ou expiré');
            }

            return $user[0];
        } catch (\Exception $e) {
            $this->sendUnauthorizedResponse('Erreur lors de la vérification du token');
        }
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public function requireRole($requiredRole)
    {
        $user = $this->authenticate();
        
        if ($user['role'] !== $requiredRole) {
            $this->sendForbiddenResponse('Accès refusé: rôle insuffisant');
        }

        return $user;
    }

    /**
     * Vérifier si l'utilisateur a un des rôles autorisés
     */
    public function requireAnyRole($allowedRoles)
    {
        $user = $this->authenticate();
        
        if (!in_array($user['role'], $allowedRoles)) {
            $this->sendForbiddenResponse('Accès refusé: rôle insuffisant');
        }

        return $user;
    }

    /**
     * Vérifier si l'utilisateur est propriétaire de la ressource
     */
    public function requireOwnership($resourceUserId)
    {
        $user = $this->authenticate();
        
        // Les administrateurs peuvent accéder à toutes les ressources
        if (in_array($user['role'], ['admin', 'admin_principal'])) {
            return $user;
        }

        // Vérifier si l'utilisateur est propriétaire de la ressource
        if ($user['id'] != $resourceUserId) {
            $this->sendForbiddenResponse('Accès refusé: vous n\'êtes pas propriétaire de cette ressource');
        }

        return $user;
    }

    /**
     * Envoyer une réponse d'erreur 401
     */
    private function sendUnauthorizedResponse($message)
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }

    /**
     * Envoyer une réponse d'erreur 403
     */
    private function sendForbiddenResponse($message)
    {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }

    /**
     * Middleware pour les routes publiques (pas d'authentification requise)
     */
    public function publicRoute()
    {
        // Pour les routes publiques, on ne fait rien
        return true;
    }

    /**
     * Middleware pour les routes d'administration
     */
    public function adminOnly()
    {
        return $this->requireRole('admin');
    }

    /**
     * Middleware pour les routes d'administration principale
     */
    public function adminPrincipalOnly()
    {
        return $this->requireRole('admin_principal');
    }

    /**
     * Middleware pour les routes d'élèves
     */
    public function eleveOnly()
    {
        return $this->requireRole('eleve');
    }

    /**
     * Middleware pour les routes d'enseignants
     */
    public function enseignantOnly()
    {
        return $this->requireRole('enseignant');
    }
}
?> 