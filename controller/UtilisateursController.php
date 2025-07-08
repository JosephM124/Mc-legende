<?php
namespace Controllers;
session_start();


class UtilisateursController extends BaseController
{
    private $utilisateur;

    public function __construct()
    {
        parent::__construct();
        $this->utilisateur = new \Models\Utilisateurs();
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
     * Authentifier un utilisateur
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
            $user = $this->database->select(
                "SELECT * FROM utilisateurs WHERE email = ? OR telephone = ?",
                [$input['identifiant'], $input['identifiant']]
            
            );

            if (empty($user)) {
                $this->errorResponse('Email ou mot de passe incorrect', 401);
            }

            $user = $user[0];

            if (password_verify($input['mot_de_passe'], $user['mot_de_passe'])) {
                // Générer un token de session
                $token = bin2hex(random_bytes(32));
                
                // Mettre à jour le token dans la base de données
                $this->database->prepare(
                    "UPDATE utilisateurs SET reset_token = ?, token_expiration = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE id = ?",
                    [$token, $user['id']]
                );

                unset($user['mot_de_passe']); // Ne pas renvoyer le mot de passe
                $user['token'] = $token;
                
                $_SESSION['utilisateur'] = $user;
                $this->redirect_to($user['role']);
               // $this->successResponse($user, 'Connexion réussie');
            } else {
                $this->errorResponse('Email ou mot de passe incorrect', 401);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'authentification: ' . $e->getMessage());
        }
    }
}
?> 