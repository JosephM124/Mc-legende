<?php
namespace Controllers;

class ProfilController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function update($id)
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
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function updateAvatar($id)
    {
        try {
            $this->successResponse(null, 'Avatar mis à jour avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 