<?php
namespace Controllers;

class SessionController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function startSession()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            // Pour l'instant, on utilise le système existant avec reset_token
            $token = bin2hex(random_bytes(32));
            $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            $result = $this->database->prepare(
                "UPDATE utilisateurs SET reset_token = ?, token_expiration = ? WHERE id = ?",
                [$token, $expiration, $input['user_id'] ?? null]
            );

            if ($result > 0) {
                $this->successResponse(['token' => $token], 'Session démarrée avec succès');
            } else {
                $this->errorResponse('Erreur lors du démarrage de la session');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function validateSession()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $user = $this->database->select(
                "SELECT * FROM utilisateurs WHERE reset_token = ? AND token_expiration > NOW()",
                [$input['token'] ?? '']
            );

            if (!empty($user)) {
                $this->successResponse($user[0], 'Session valide');
            } else {
                $this->errorResponse('Session invalide ou expirée', 401);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
            $result = $this->database->prepare(
                "UPDATE utilisateurs SET token_expiration = ? WHERE id = ?",
                [$expiration, $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Session mise à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function extendSession($id)
    {
        try {
            $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
            $result = $this->database->prepare(
                "UPDATE utilisateurs SET token_expiration = ? WHERE id = ?",
                [$expiration, $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Session prolongée avec succès');
            } else {
                $this->errorResponse('Erreur lors de la prolongation');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->database->prepare(
                "UPDATE utilisateurs SET reset_token = NULL, token_expiration = NULL WHERE id = ?", 
                [$id]
            );
            if ($result > 0) {
                $this->successResponse(null, 'Session supprimée avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteByUser($user_id)
    {
        try {
            $result = $this->database->prepare(
                "UPDATE utilisateurs SET reset_token = NULL, token_expiration = NULL WHERE id = ?", 
                [$user_id]
            );
            if ($result > 0) {
                $this->successResponse(null, 'Sessions supprimées avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 