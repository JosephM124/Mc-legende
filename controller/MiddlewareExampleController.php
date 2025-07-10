<?php
namespace Controllers;

class MiddlewareExampleController extends BaseController
{
    private $middleware;

    public function __construct()
    {
        parent::__construct();
        $this->middleware = new \Middleware\BaseMiddleware();
    }

    /**
     * Exemple de route publique
     */
    public function publicEndpoint()
    {
        $this->middleware->publicRoute();
        
        $this->successResponse([
            'message' => 'Endpoint public accessible',
            'timestamp' => date('Y-m-d H:i:s')
        ], 'Succès');
    }

    /**
     * Exemple de route authentifiée
     */
    public function authenticatedEndpoint()
    {
        $user = $this->middleware->authenticatedRoute();
        
        $this->successResponse([
            'message' => 'Endpoint authentifié accessible',
            'user' => [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'email' => $user['email']
            ]
        ], 'Succès');
    }

    /**
     * Exemple de route admin avec validation
     */
    public function createUserWithMiddleware()
    {
        $user = $this->middleware->adminRoute();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation avec middleware
        $this->middleware->validateRoute('validateUtilisateur', $input);

        // Créer l'utilisateur
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
                $this->errorResponse('Erreur lors de la création');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Exemple de route avec accès aux ressources
     */
    public function getEleveResource($eleveId)
    {
        $user = $this->middleware->resourceRoute('eleve', $eleveId);
        
        try {
            $eleve = $this->database->select(
                "SELECT e.*, u.nom, u.prenom, u.email 
                 FROM eleves e 
                 JOIN utilisateurs u ON e.utilisateur_id = u.id 
                 WHERE e.id = ?",
                [$eleveId]
            );

            if (empty($eleve)) {
                $this->errorResponse('Élève non trouvé', 404);
            }

            $this->successResponse($eleve[0], 'Élève récupéré avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Exemple de route d'upload de fichier
     */
    public function uploadFile()
    {
        $user = $this->middleware->fileUploadRoute(
            ['image/jpeg', 'image/png', 'image/gif'],
            5 * 1024 * 1024 // 5MB
        );

        // Traitement du fichier uploadé
        if (isset($_FILES['file'])) {
            $this->successResponse([
                'filename' => $_FILES['file']['name'],
                'size' => $_FILES['file']['size']
            ], 'Fichier uploadé avec succès');
        } else {
            $this->errorResponse('Aucun fichier fourni');
        }
    }

    /**
     * Exemple de route de recherche
     */
    public function searchUsers()
    {
        $user = $this->middleware->searchRoute($_GET);

        try {
            $searchTerm = $_GET['q'] ?? '';
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 10);

            $offset = ($page - 1) * $limit;

            $users = $this->database->select(
                "SELECT * FROM utilisateurs 
                 WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? 
                 LIMIT ? OFFSET ?",
                ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", $limit, $offset]
            );

            $this->successResponse($users, 'Recherche effectuée avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Exemple de route de statistiques
     */
    public function getStats()
    {
        $user = $this->middleware->statsRoute();

        try {
            $stats = $this->database->select(
                "SELECT 
                    (SELECT COUNT(*) FROM utilisateurs) as total_users,
                    (SELECT COUNT(*) FROM eleves) as total_eleves,
                    (SELECT COUNT(*) FROM interrogations) as total_interrogations,
                    (SELECT COUNT(*) FROM resultats) as total_resultats"
            );

            $this->successResponse($stats[0], 'Statistiques récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Exemple de route d'export
     */
    public function exportData()
    {
        $user = $this->middleware->exportRoute();

        // Logique d'export
        $this->successResponse(['export_url' => 'downloads/export.xlsx'], 'Export généré avec succès');
    }

    /**
     * Exemple de route de configuration
     */
    public function updateConfig()
    {
        $user = $this->middleware->configRoute();

        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Mise à jour de la configuration
        $this->successResponse(null, 'Configuration mise à jour avec succès');
    }

    /**
     * Exemple de route de profil
     */
    public function updateProfile($userId)
    {
        $user = $this->middleware->profileRoute($userId);

        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation avec middleware
        $this->middleware->validateRoute('validateUtilisateur', $input);

        // Mise à jour du profil
        $this->successResponse(null, 'Profil mis à jour avec succès');
    }

    /**
     * Exemple de route de validation d'email
     */
    public function validateEmail()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation publique
        $this->middleware->validationRoute('validateEmail', $input);

        $this->successResponse(null, 'Email valide');
    }

    /**
     * Exemple de route de notifications
     */
    public function sendNotification()
    {
        $user = $this->middleware->notificationsRoute();

        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation avec middleware
        $this->middleware->validateRoute('validateNotification', $input);

        // Envoi de notification
        $this->successResponse(null, 'Notification envoyée avec succès');
    }

    /**
     * Exemple de route avec rôles multiples
     */
    public function adminOrTeacherRoute()
    {
        $user = $this->middleware->anyRoleRoute(['admin', 'admin_principal', 'enseignant']);

        $this->successResponse([
            'message' => 'Route accessible aux admins et enseignants',
            'user_role' => $user['role']
        ], 'Succès');
    }

    /**
     * Exemple de route avec propriété
     */
    public function userOwnResource($resourceUserId)
    {
        $user = $this->middleware->ownershipRoute($resourceUserId);

        $this->successResponse([
            'message' => 'Accès autorisé à la ressource',
            'resource_user_id' => $resourceUserId,
            'current_user_id' => $user['id']
        ], 'Succès');
    }
}
?> 