<?php
namespace Controllers;

class NotificationController extends BaseController
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
                "INSERT INTO notifications (user_id, titre, message, type, statut, date_creation) 
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    $input['user_id'] ?? null,
                    $input['titre'] ?? '',
                    $input['message'] ?? '',
                    $input['type'] ?? 'info',
                    $input['statut'] ?? 'unread'
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Notification créée avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création de la notification');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function sendNotification()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            // Logique d'envoi de notification
            $this->successResponse(null, 'Notification envoyée avec succès');
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
                "UPDATE notifications SET statut = ? WHERE id = ?",
                [$input['statut'] ?? 'read', $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Notification mise à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function markAsRead($id)
    {
        try {
            $result = $this->database->prepare(
                "UPDATE notifications SET statut = 'read' WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Notification marquée comme lue');
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
                "DELETE FROM notifications WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Notification supprimée avec succès');
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
                "DELETE FROM notifications WHERE user_id = ?",
                [$user_id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Notifications supprimées avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 