<?php
namespace Controllers;

class UtilisateursController extends BaseController
{
    private $utilisateur;
    protected $sessionManager;
    protected $authMiddleware;

    public function __construct()
    {
        parent::__construct();
        $this->utilisateur = new \Models\Utilisateurs();
        $this->sessionManager = new \Middleware\SessionManager();
        $this->authMiddleware = new \Middleware\AuthMiddleware();
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public function index()
    {
        try {
            $utilisateurs = $this->utilisateur->setTable('utilisateurs')->all();
            $this->successResponse($utilisateurs, 'Utilisateurs récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function show($id)
    {
        try {
            $utilisateur = $this->database->select(
                "SELECT * FROM utilisateurs WHERE id = ?",
                [$id]
            );
            
            if (empty($utilisateur)) {
                $this->errorResponse('Utilisateur non trouvé', 404);
            }
            
            $this->successResponse($utilisateur[0], 'Utilisateur récupéré avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération de l\'utilisateur: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les utilisateurs par rôle
     */
    public function getByRole($role)
    {
        try {
            $utilisateurs = $this->database->select(
                "SELECT * FROM utilisateurs WHERE role = ? ORDER BY nom, prenom",
                [$role]
            );
            
            $this->successResponse($utilisateurs, 'Utilisateurs récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer un utilisateur par email
     */
    public function getByEmail($email)
    {
        try {
            $utilisateur = $this->database->select(
                "SELECT * FROM utilisateurs WHERE email = ?",
                [$email]
            );
            
            if (empty($utilisateur)) {
                $this->errorResponse('Utilisateur non trouvé', 404);
            }
            
            $this->successResponse($utilisateur[0], 'Utilisateur récupéré avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération de l\'utilisateur: ' . $e->getMessage());
        }
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation des données
        $rules = [
            'nom' => 'required',
            'email' => 'required',
            'mot_de_passe' => 'required',
            'role' => 'required'
        ];

        $errors = $this->validateInput($input, $rules);
        if (!empty($errors)) {
            $this->errorResponse($errors, 422);
        }

        try {
            // Vérifier si l'email existe déjà
            $existingUser = $this->database->select(
                "SELECT id FROM utilisateurs WHERE email = ?",
                [$input['email']]
            );

            if (!empty($existingUser)) {
                $this->errorResponse('Un utilisateur avec cet email existe déjà', 409);
            }

            // Hasher le mot de passe
            $hashedPassword = password_hash($input['mot_de_passe'], PASSWORD_DEFAULT);

            $result = $this->database->prepare(
                "INSERT INTO utilisateurs (nom, postnom, prenom, email, mot_de_passe, role, telephone, sexe, date_inscription) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [
                    $input['nom'] ?? '',
                    $input['postnom'] ?? '',
                    $input['prenom'] ?? '',
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
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            // Vérifier si l'utilisateur existe
            $existingUser = $this->database->select(
                "SELECT id FROM utilisateurs WHERE id = ?",
                [$id]
            );

            if (empty($existingUser)) {
                $this->errorResponse('Utilisateur non trouvé', 404);
            }

            $updateFields = [];
            $params = [];

            // Construire la requête de mise à jour dynamiquement
            $allowedFields = ['nom', 'postnom', 'prenom', 'email', 'telephone', 'sexe', 'role', 'statut'];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $input[$field];
                }
            }

            if (empty($updateFields)) {
                $this->errorResponse('Aucune donnée à mettre à jour');
            }

            $params[] = $id;
            $sql = "UPDATE utilisateurs SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $result = $this->database->prepare($sql, $params);

            if ($result > 0) {
                $this->successResponse(null, 'Utilisateur mis à jour avec succès');
            } else {
                $this->errorResponse('Aucune modification effectuée');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            if (empty($input['nouveau_mot_de_passe'])) {
                $this->errorResponse('Nouveau mot de passe requis', 422);
            }

            $hashedPassword = password_hash($input['nouveau_mot_de_passe'], PASSWORD_DEFAULT);

            $result = $this->database->prepare(
                "UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?",
                [$hashedPassword, $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Mot de passe mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour du mot de passe');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE utilisateurs SET nom = ?, postnom = ?, prenom = ?, telephone = ?, sexe = ? WHERE id = ?",
                [
                    $input['nom'] ?? '',
                    $input['postnom'] ?? '',
                    $input['prenom'] ?? '',
                    $input['telephone'] ?? '',
                    $input['sexe'] ?? '',
                    $id
                ]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Profil mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour du profil');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour le statut
     */
    public function updateStatus($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE utilisateurs SET statut = ? WHERE id = ?",
                [$input['statut'] ?? 'active', $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Statut mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour du statut');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy($id)
    {
        try {
            $result = $this->database->prepare(
                "DELETE FROM utilisateurs WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Utilisateur supprimé avec succès');
            } else {
                $this->errorResponse('Utilisateur non trouvé', 404);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Suppression douce d'un utilisateur
     */
    public function softDelete($id)
    {
        try {
            $result = $this->database->prepare(
                "UPDATE utilisateurs SET statut = 'deleted', date_suppression = NOW() WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Utilisateur supprimé avec succès');
            } else {
                $this->errorResponse('Utilisateur non trouvé', 404);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Authentifier un utilisateur (version sécurisée)
     */
    public function login($datas = [])
    {
        $input = $this->sanitizeInput($datas);
        $rules = [
            'identifiant' => 'required',
            'mot_de_passe' => 'required'
        ];

        $errors = $this->validateInput($input, $rules);
      
        if (!empty($errors)) {
            $this->errorResponse($errors, 422);
        }
        
        try {
            // Vérifier les tentatives de connexion
            if (!$this->sessionManager->checkLoginAttempts($input['identifiant'])) {
                $this->errorResponse('Trop de tentatives de connexion. Réessayez dans 15 minutes.', 429);
            }

            $user = $this->database->select(
                "SELECT * FROM utilisateurs WHERE email = ? OR telephone = ?",
                [$input['identifiant'], $input['identifiant']]
            );

            if (empty($user)) {
                $this->sessionManager->logFailedLogin($input['identifiant']);
                $this->errorResponse('Email ou mot de passe incorrect', 401);
            }

            $user = $user[0];

            // Vérifier si le compte est actif
            if ($user['statut'] !== 'active') {
                $this->errorResponse('Compte inactif. Contactez l\'administrateur.', 403);
            }

            if (password_verify($input['mot_de_passe'], $user['mot_de_passe'])) {
                // Créer une session sécurisée
                $this->sessionManager->createUserSession($user);
                
                // Enregistrer la connexion réussie
                $this->logSuccessfulLogin($user['id']);
                
                // Rediriger selon le rôle
                //var_dump($user['role']);
                //exit;
                $this->redirect_to($user['role']);
            } else {
                $this->sessionManager->logFailedLogin($input['identifiant']);
                $this->errorResponse('Email ou mot de passe incorrect', 401);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'authentification: ' . $e->getMessage());
        }
    }

    /**
     * Enregistrer une connexion réussie
     */
    private function logSuccessfulLogin($userId)
    {
        try {
            $this->database->prepare(
                "INSERT INTO logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)",
                [
                    $userId,
                    'login_success',
                    'Connexion réussie',
                    $this->getClientIP()
                ]
            );
        } catch (\Exception $e) {
            error_log("Erreur lors de l'enregistrement de la connexion: " . $e->getMessage());
        }
    }

    /**
     * Récupérer l'adresse IP du client
     */
    private function getClientIP()
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, 
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            if (empty($input['email'])) {
                $this->errorResponse('Email requis', 422);
            }

            $user = $this->database->select(
                "SELECT * FROM utilisateurs WHERE email = ?",
                [$input['email']]
            );

            if (empty($user)) {
                $this->errorResponse('Aucun utilisateur trouvé avec cet email', 404);
            }

            // Générer un token de réinitialisation
            $resetToken = bin2hex(random_bytes(32));
            
            $this->database->prepare(
                "UPDATE utilisateurs SET reset_token = ?, token_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?",
                [$resetToken, $user[0]['id']]
            );

            // Ici, vous pourriez envoyer un email avec le lien de réinitialisation
            // Pour l'instant, on renvoie juste le token
            $this->successResponse(['reset_token' => $resetToken], 'Email de réinitialisation envoyé');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier l'email
     */
    public function verifyEmail()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            if (empty($input['email'])) {
                $this->errorResponse('Email requis', 422);
            }

            $user = $this->database->select(
                "SELECT * FROM utilisateurs WHERE email = ?",
                [$input['email']]
            );

            if (!empty($user)) {
                $this->errorResponse('Cet email est déjà utilisé', 409);
            }

            $this->successResponse(null, 'Email disponible');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Déconnexion (version sécurisée)
     */
    public function logout()
    {
        try {
            // Détruire la session sécurisée
            $this->sessionManager->destroySession();
            
            // Rediriger vers la page de connexion
            header('Location: /connexion');
            exit();
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la déconnexion: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier l'état de l'authentification
     */
    public function checkAuth()
    {
        if ($this->sessionManager->isAuthenticated()) {
            $user = $this->sessionManager->getCurrentUser();
            $this->successResponse($user, 'Utilisateur authentifié');
        } else {
            $this->errorResponse('Non authentifié', 401);
        }
    }
}
?> 