<?php
namespace Controllers;
abstract class BaseController
{
    protected $database;
    protected $response = [];
    protected $sessionManager;
    protected $authMiddleware;

    public function __construct()
    {
        $this->database = \App\App::getMysqlDatabaseInstance();
        $this->sessionManager = new \Middleware\SessionManager();
        $this->authMiddleware = new \Middleware\AuthMiddleware();
    }

    /**
     * Retourne une réponse JSON
     */
    protected function jsonResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Retourne une réponse d'erreur
     */
    protected function errorResponse($message, $status = 400)
    {
        $this->jsonResponse(['error' => $message], $status);
    }

    /**
     * Retourne une réponse de succès
     */
    protected function successResponse($data, $message = 'Success')
    {
        $this->jsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Valide les données d'entrée
     */
    protected function validateInput($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || empty($data[$field])) {
                if (strpos($rule, 'required') !== false) {
                    $errors[$field] = "Le champ $field est requis";
                }
            }
        }
        
        return $errors;
    }

    /**
     * Nettoie les données d'entrée
     */
    protected function sanitizeInput($data)
    {
        $clean = [];
        foreach ($data as $key => $value) {
            $clean[$key] = htmlspecialchars(strip_tags(trim($value)));
        }
        return $clean;
    }

    public function logout(){
       $this->sessionManager->destroySession();
       header("Location: /connexion");
       exit();
    }

    protected function redirect_to($role){
        $user = $this->sessionManager->getCurrentUser();
        
        switch($role){
            case 'eleve':
                header('Location: /eleve/home');
                break;
            case 'admin_simple':
                header('Location: views/admin_principal.php');
                break;
            case 'admin_principal':
                header('Location: /admin/home');
                break;
            default:
                header('Location: /connexion');
                break;
        }
        exit();
    }

    /**
     * Vérifier si l'utilisateur est authentifié
     */
    protected function requireAuth()
    {
        if (!$this->sessionManager->isAuthenticated()) {
            header('Location: /connexion');
            exit();
        }
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    protected function requireRole($role)
    {
        $this->requireAuth();
        
        if (!$this->sessionManager->hasRole($role)) {
            header('Location: /connexion');
            exit();
        }
    }

    /**
     * Vérifier si l'utilisateur a un des rôles autorisés
     */
    protected function requireAnyRole($roles)
    {
        $this->requireAuth();
        
        if (!$this->sessionManager->hasAnyRole($roles)) {
            header('Location: /connexion');
            exit();
        }
    }
}
?> 