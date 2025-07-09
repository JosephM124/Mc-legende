<?php
namespace Controllers;

class BackupController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function destroy($id)
    {
        try {
            $result = $this->database->prepare("DELETE FROM backups WHERE id = ?", [$id]);
            if ($result > 0) {
                $this->successResponse(null, 'Backup supprimé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteOldBackups()
    {
        try {
            $result = $this->database->prepare("DELETE FROM backups WHERE date_creation < DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $this->successResponse(null, 'Anciens backups supprimés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 