<?php

abstract class BaseController
{
    protected $database;
    protected $response = [];

    public function __construct()
    {
        $this->database = App::getMysqlDatabaseInstance();
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
}
?> 