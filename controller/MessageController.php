<?php
namespace Controllers;

class MessageController extends BaseController
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
                "INSERT INTO messages (expediteur_id, destinataire_id, sujet, contenu, statut, date_envoi) 
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    $input['expediteur_id'] ?? null,
                    $input['destinataire_id'] ?? null,
                    $input['sujet'] ?? '',
                    $input['contenu'] ?? '',
                    $input['statut'] ?? 'unread'
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Message créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création du message');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function sendMessage()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            // Logique d'envoi de message
            $this->successResponse(null, 'Message envoyé avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE messages SET statut = ? WHERE id = ?",
                [$input['statut'] ?? 'read', $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Message mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function updateStatus($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE messages SET statut = ? WHERE id = ?",
                [$input['statut'], $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Statut mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->database->prepare(
                "DELETE FROM messages WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Message supprimé avec succès');
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
                "DELETE FROM messages WHERE expediteur_id = ? OR destinataire_id = ?",
                [$user_id, $user_id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Messages supprimés avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 