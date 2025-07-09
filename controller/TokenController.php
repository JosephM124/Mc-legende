<?php
namespace Controllers;

class TokenController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function destroy($id)
    {
        try {
            $result = $this->database->prepare("DELETE FROM tokens WHERE id = ?", [$id]);
            if ($result > 0) {
                $this->successResponse(null, 'Token supprimé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteExpired()
    {
        try {
            $result = $this->database->prepare("DELETE FROM tokens WHERE date_expiration < NOW()");
            $this->successResponse(null, 'Tokens expirés supprimés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 