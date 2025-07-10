<?php
namespace Controllers;

class LogController extends BaseController
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
            // Pour l'instant, on utilise la table activites_admin au lieu de logs
            $result = $this->database->prepare(
                "INSERT INTO activites_admin (admin_id, action, details, date_activite) VALUES (?, ?, ?, NOW())",
                [
                    $input['user_id'] ?? null,
                    $input['action'] ?? '',
                    $input['description'] ?? ''
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Log créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->database->prepare("DELETE FROM logs WHERE id = ?", [$id]);
            if ($result > 0) {
                $this->successResponse(null, 'Log supprimé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteOldLogs()
    {
        try {
            $result = $this->database->prepare("DELETE FROM logs WHERE date_creation < DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $this->successResponse(null, 'Anciens logs supprimés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 