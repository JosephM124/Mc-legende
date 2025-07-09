<?php
namespace Controllers;

class ValidationController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validateEmail()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $email = $input['email'] ?? '';
            
            // Validation basique
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errorResponse('Format d\'email invalide', 422);
            }

            // Vérifier si l'email existe déjà
            $existingUser = $this->database->select(
                "SELECT id FROM utilisateurs WHERE email = ?",
                [$email]
            );

            if (!empty($existingUser)) {
                $this->errorResponse('Cet email est déjà utilisé', 409);
            }

            $this->successResponse(null, 'Email valide');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function validatePhone()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $phone = $input['phone'] ?? '';
            
            // Validation basique du téléphone
            if (!preg_match('/^[0-9+\-\s\(\)]{8,15}$/', $phone)) {
                $this->errorResponse('Format de téléphone invalide', 422);
            }

            // Vérifier si le téléphone existe déjà
            $existingUser = $this->database->select(
                "SELECT id FROM utilisateurs WHERE telephone = ?",
                [$phone]
            );

            if (!empty($existingUser)) {
                $this->errorResponse('Ce numéro de téléphone est déjà utilisé', 409);
            }

            $this->successResponse(null, 'Téléphone valide');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 