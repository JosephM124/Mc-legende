<?php
namespace Controllers;

class PermissionController extends BaseController
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
                "UPDATE permissions SET permissions = ? WHERE user_id = ?",
                [json_encode($input['permissions'] ?? []), $user_id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Permissions mises à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function updateRole($user_id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE utilisateurs SET role = ? WHERE id = ?",
                [$input['role'], $user_id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Rôle mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 