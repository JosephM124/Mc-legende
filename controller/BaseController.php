<?php
namespace Controllers;
abstract class BaseController
{
    protected $database;
    protected $response = [];

    public function __construct()
    {
        $this->database = \App\App::getMysqlDatabaseInstance();
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
       session_unset();
       session_destroy();
       header("Location: /");
       exit();
    }

    protected function redirect_to($role){
        switch($role){
            case 'eleve':
                header('Location: /eleve/home');
                break;
            case 'admin_simple':
                $admin = (new AdminController())->enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Connexion d'un admin", "Nom : " . $_SESSION['utilisateur']['nom'] . " | Email : " . $_SESSION['utilisateur']['email']);
                header('Location: admin_simple.php');
            break;
            case 'admin_principal':
                header('Location: /admin/home');
                $admin = (new AdminController())->enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Connexion d'un admin", "Nom : " . $_SESSION['utilisateur']['nom'] . " | Email : " . $_SESSION['utilisateur']['email']);
                break;
            default:
           break;
        }
    }
}
?> 