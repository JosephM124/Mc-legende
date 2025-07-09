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
        $token = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$token) {
            $this->sendUnauthorizedResponse('Token d\'authentification requis');
        }

        // Nettoyer le token (enlever "Bearer " si présent)
        $token = str_replace('Bearer ', '', $token);

        try {
            // Vérifier dans la table utilisateurs (système actuel)
            $user = $this->database->select(
                "SELECT * FROM utilisateurs WHERE reset_token = ? AND token_expiration > NOW()",
                [$token]
            );

            if (empty($user)) {
                $this->sendUnauthorizedResponse('Token invalide ou expiré');
            }

            return $user[0];
        } catch (\Exception $e) {
            $this->sendUnauthorizedResponse('Erreur lors de la vérification du token: ' . $e->getMessage());
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
     * Vérifier si l'utilisateur peut accéder à une ressource spécifique
     */
    public function requireResourceAccess($resourceType, $resourceId)
    {
        $user = $this->authenticate();
        
        // Les administrateurs ont accès à tout
        if (in_array($user['role'], ['admin', 'admin_principal'])) {
            return $user;
        }

        // Vérifications spécifiques selon le type de ressource
        switch ($resourceType) {
            case 'eleve':
                $eleve = $this->database->select(
                    "SELECT * FROM eleves WHERE id = ? AND utilisateur_id = ?",
                    [$resourceId, $user['id']]
                );
                if (empty($eleve)) {
                    $this->sendForbiddenResponse('Accès refusé à cette ressource élève');
                }
                break;
                
            case 'interrogation':
                // Les enseignants peuvent voir leurs interrogations
                if ($user['role'] === 'enseignant') {
                    $interrogation = $this->database->select(
                        "SELECT * FROM interrogations WHERE id = ? AND created_by = ?",
                        [$resourceId, $user['id']]
                    );
                    if (empty($interrogation)) {
                        $this->sendForbiddenResponse('Accès refusé à cette interrogation');
                    }
                }
                break;
                
            case 'resultat':
                // Les élèves peuvent voir leurs résultats
                if ($user['role'] === 'eleve') {
                    $eleve = $this->database->select(
                        "SELECT id FROM eleves WHERE utilisateur_id = ?",
                        [$user['id']]
                    );
                    if (!empty($eleve)) {
                        $resultat = $this->database->select(
                            "SELECT * FROM resultats WHERE id = ? AND eleve_id = ?",
                            [$resourceId, $eleve[0]['id']]
                        );
                        if (empty($resultat)) {
                            $this->sendForbiddenResponse('Accès refusé à ce résultat');
                        }
                    }
                }
                break;
        }

        return $user;
    }

    /**
     * Vérifier si l'utilisateur est actif
     */
    public function requireActiveUser()
    {
        $user = $this->authenticate();
        
        if ($user['statut'] !== 'active') {
            $this->sendForbiddenResponse('Compte utilisateur inactif');
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
        echo json_encode([
            'error' => $message,
            'code' => 401
        ]);
        exit;
    }

    /**
     * Envoyer une réponse d'erreur 403
     */
    private function sendForbiddenResponse($message)
    {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $message,
            'code' => 403
        ]);
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

    /**
     * Middleware pour les routes d'administration (admin ou admin_principal)
     */
    public function adminOrPrincipal()
    {
        return $this->requireAnyRole(['admin', 'admin_principal']);
    }

    /**
     * Middleware pour les routes d'utilisateurs authentifiés (tous rôles)
     */
    public function authenticatedOnly()
    {
        return $this->requireActiveUser();
    }
}
?> 