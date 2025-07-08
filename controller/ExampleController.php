<?php

namespace Controllers;

class ExampleController extends BaseController
{
    private $authMiddleware;
    private $validationMiddleware;

    public function __construct()
    {
        parent::__construct();
        $this->authMiddleware = new AuthMiddleware();
        $this->validationMiddleware = new ValidationMiddleware();
    }

    /**
     * Exemple de route publique (pas d'authentification requise)
     */
    public function publicEndpoint()
    {
        $this->authMiddleware->publicRoute();
        
        $data = [
            'message' => 'Ceci est un endpoint public',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $this->successResponse($data, 'Endpoint public accessible');
    }

    /**
     * Exemple de route protégée (authentification requise)
     */
    public function protectedEndpoint()
    {
        // Vérifier l'authentification
        $user = $this->authMiddleware->authenticate();
        
        $data = [
            'message' => 'Ceci est un endpoint protégé',
            'user' => [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role']
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $this->successResponse($data, 'Endpoint protégé accessible');
    }

    /**
     * Exemple de route pour administrateurs seulement
     */
    public function adminEndpoint()
    {
        // Vérifier que l'utilisateur est un administrateur
        $user = $this->authMiddleware->adminOnly();
        
        $data = [
            'message' => 'Ceci est un endpoint administrateur',
            'user' => [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'role' => $user['role']
            ],
            'admin_data' => [
                'total_users' => $this->getTotalUsers(),
                'total_eleves' => $this->getTotalEleves(),
                'system_status' => 'OK'
            ]
        ];
        
        $this->successResponse($data, 'Endpoint administrateur accessible');
    }

    /**
     * Exemple de route pour élèves seulement
     */
    public function eleveEndpoint()
    {
        // Vérifier que l'utilisateur est un élève
        $user = $this->authMiddleware->eleveOnly();
        
        $data = [
            'message' => 'Ceci est un endpoint élève',
            'user' => [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'role' => $user['role']
            ],
            'eleve_data' => $this->getEleveData($user['id'])
        ];
        
        $this->successResponse($data, 'Endpoint élève accessible');
    }

    /**
     * Exemple de route avec validation des données
     */
    public function createUserWithValidation()
    {
        // Vérifier l'authentification
        $user = $this->authMiddleware->requireAnyRole(['admin', 'admin_principal']);
        
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Valider les données
        $errors = $this->validationMiddleware->validateUtilisateur($input);
        
        if (!empty($errors)) {
            $this->validationMiddleware->sendValidationError($errors);
        }

        // Si la validation passe, créer l'utilisateur
        try {
            $hashedPassword = password_hash($input['mot_de_passe'], PASSWORD_DEFAULT);
            
            $result = $this->database->prepare(
                "INSERT INTO utilisateurs (nom, postnom, prenom, email, mot_de_passe, role, telephone, sexe) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $input['nom'],
                    $input['postnom'],
                    $input['prenom'],
                    $input['email'],
                    $hashedPassword,
                    $input['role'],
                    $input['telephone'] ?? '',
                    $input['sexe'] ?? ''
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Utilisateur créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création de l\'utilisateur');
            }
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Exemple de route avec vérification de propriété
     */
    public function userProfile($userId)
    {
        // Vérifier que l'utilisateur peut accéder à ce profil
        $currentUser = $this->authMiddleware->requireOwnership($userId);
        
        try {
            $user = $this->database->select(
                "SELECT id, nom, postnom, prenom, email, role, telephone, sexe, date_inscription 
                 FROM utilisateurs WHERE id = ?",
                [$userId]
            );

            if (empty($user)) {
                $this->errorResponse('Utilisateur non trouvé', 404);
            }

            $this->successResponse($user[0], 'Profil récupéré avec succès');
        } catch (Exception $e) {
            $this->errorResponse('Erreur lors de la récupération du profil: ' . $e->getMessage());
        }
    }

    /**
     * Méthodes utilitaires privées
     */
    private function getTotalUsers()
    {
        $result = $this->database->select("SELECT COUNT(*) as total FROM utilisateurs");
        return $result[0]['total'] ?? 0;
    }

    private function getTotalEleves()
    {
        $result = $this->database->select("SELECT COUNT(*) as total FROM eleves");
        return $result[0]['total'] ?? 0;
    }

    private function getEleveData($userId)
    {
        $result = $this->database->select(
            "SELECT e.*, u.nom, u.prenom 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             WHERE e.utilisateur_id = ?",
            [$userId]
        );
        
        return $result[0] ?? null;
    }
}
?> 