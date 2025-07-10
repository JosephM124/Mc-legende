<?php
namespace Controllers;

class PreferenceController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function update($user_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE preferences SET valeur = ? WHERE user_id = ? AND nom = ?",
                [$input['valeur'], $user_id, $input['nom']]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Préférence mise à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function updateTheme($user_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE preferences SET valeur = ? WHERE user_id = ? AND nom = 'theme'",
                [$input['theme'], $user_id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Thème mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 