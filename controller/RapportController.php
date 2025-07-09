<?php
namespace Controllers;

class RapportController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "INSERT INTO rapports (titre, contenu, type, user_id, date_creation) VALUES (?, ?, ?, ?, NOW())",
                [
                    $input['titre'] ?? '',
                    $input['contenu'] ?? '',
                    $input['type'] ?? 'general',
                    $input['user_id'] ?? null
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Rapport créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function generateRapport()
    {
        try {
            $this->successResponse(null, 'Rapport généré avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->database->prepare("DELETE FROM rapports WHERE id = ?", [$id]);
            if ($result > 0) {
                $this->successResponse(null, 'Rapport supprimé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteOldRapports()
    {
        try {
            $result = $this->database->prepare("DELETE FROM rapports WHERE date_creation < DATE_SUB(NOW(), INTERVAL 90 DAY)");
            $this->successResponse(null, 'Anciens rapports supprimés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 