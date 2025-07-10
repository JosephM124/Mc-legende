<?php
namespace Controllers;

class ParametreController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function update($param)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE parametres SET valeur = ? WHERE nom = ?",
                [$input['valeur'], $param]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Paramètre mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function updateUserParams($user_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE parametres_utilisateur SET valeur = ? WHERE user_id = ? AND nom = ?",
                [$input['valeur'], $user_id, $input['nom']]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Paramètres utilisateur mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 