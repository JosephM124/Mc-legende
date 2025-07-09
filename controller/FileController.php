<?php
namespace Controllers;

class FileController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Upload d'avatar
     */
    public function uploadAvatar()
    {
        try {
            if (!isset($_FILES['avatar'])) {
                $this->errorResponse('Aucun fichier avatar fourni', 422);
            }

            $file = $_FILES['avatar'];
            $user_id = $_POST['user_id'] ?? null;

            if (!$user_id) {
                $this->errorResponse('ID utilisateur requis', 422);
            }

            // Vérification du type réel du fichier
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
            if (!array_key_exists($realType, $allowedTypes)) {
                $this->errorResponse('Type de fichier non autorisé. Utilisez JPG, PNG ou GIF', 422);
            }
            $extension = $allowedTypes[$realType];

            // Vérifier la taille (max 2MB)
            if ($file['size'] > 2 * 1024 * 1024) {
                $this->errorResponse('Fichier trop volumineux. Maximum 2MB', 422);
            }

            // Créer le dossier s'il n'existe pas
            $uploadDir = 'uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Générer un nom de fichier unique
            $filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;

            // Vérifier l'extension (refus des extensions dangereuses)
            $dangerous = ['php', 'php5', 'phtml', 'phar', 'exe', 'js', 'sh', 'bat'];
            if (in_array($extension, $dangerous)) {
                $this->errorResponse('Extension de fichier interdite', 422);
            }

            // Déplacer le fichier
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Mettre à jour la base de données
                $result = $this->database->prepare(
                    "UPDATE utilisateurs SET photo = ? WHERE id = ?",
                    [$filepath, $user_id]
                );

                if ($result > 0) {
                    $this->successResponse(['filepath' => $filepath], 'Avatar uploadé avec succès');
                } else {
                    $this->errorResponse('Erreur lors de la mise à jour de la base de données');
                }
            } else {
                $this->errorResponse('Erreur lors du déplacement du fichier');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'upload: ' . $e->getMessage());
        }
    }

    /**
     * Upload de document
     */
    public function uploadDocument()
    {
        try {
            if (!isset($_FILES['document'])) {
                $this->errorResponse('Aucun fichier document fourni', 422);
            }

            $file = $_FILES['document'];
            $type = $_POST['type'] ?? 'general';
            $user_id = $_POST['user_id'] ?? null;

            // Vérification du type réel du fichier
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            $allowedTypes = [
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                'text/plain' => 'txt'
            ];
            if (!array_key_exists($realType, $allowedTypes)) {
                $this->errorResponse('Type de fichier non autorisé. Utilisez PDF, DOC, DOCX ou TXT', 422);
            }
            $extension = $allowedTypes[$realType];

            // Vérifier la taille (max 10MB)
            if ($file['size'] > 10 * 1024 * 1024) {
                $this->errorResponse('Fichier trop volumineux. Maximum 10MB', 422);
            }

            // Créer le dossier s'il n'existe pas
            $uploadDir = 'uploads/documents/' . $type . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Générer un nom de fichier unique
            $filename = 'doc_' . $type . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;

            // Vérifier l'extension (refus des extensions dangereuses)
            $dangerous = ['php', 'php5', 'phtml', 'phar', 'exe', 'js', 'sh', 'bat'];
            if (in_array($extension, $dangerous)) {
                $this->errorResponse('Extension de fichier interdite', 422);
            }

            // Déplacer le fichier
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Enregistrer dans la base de données
                $result = $this->database->prepare(
                    "INSERT INTO fichiers (nom, chemin, type, taille, user_id, date_upload) 
                     VALUES (?, ?, ?, ?, ?, NOW())",
                    [$file['name'], $filepath, $file['type'], $file['size'], $user_id]
                );

                if ($result > 0) {
                    $this->successResponse(['filepath' => $filepath, 'id' => $this->database->lastInsertId()], 'Document uploadé avec succès');
                } else {
                    $this->errorResponse('Erreur lors de l\'enregistrement en base de données');
                }
            } else {
                $this->errorResponse('Erreur lors du déplacement du fichier');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'upload: ' . $e->getMessage());
        }
    }

    /**
     * Upload d'image
     */
    public function uploadImage()
    {
        try {
            if (!isset($_FILES['image'])) {
                $this->errorResponse('Aucun fichier image fourni', 422);
            }

            $file = $_FILES['image'];
            $category = $_POST['category'] ?? 'general';
            $user_id = $_POST['user_id'] ?? null;

            // Vérification du type réel du fichier
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];
            if (!array_key_exists($realType, $allowedTypes)) {
                $this->errorResponse('Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WEBP', 422);
            }
            $extension = $allowedTypes[$realType];

            // Vérifier la taille (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                $this->errorResponse('Fichier trop volumineux. Maximum 5MB', 422);
            }

            // Créer le dossier s'il n'existe pas
            $uploadDir = 'uploads/images/' . $category . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Générer un nom de fichier unique
            $filename = 'img_' . $category . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;

            // Vérifier l'extension (refus des extensions dangereuses)
            $dangerous = ['php', 'php5', 'phtml', 'phar', 'exe', 'js', 'sh', 'bat'];
            if (in_array($extension, $dangerous)) {
                $this->errorResponse('Extension de fichier interdite', 422);
            }

            // Déplacer le fichier
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Enregistrer dans la base de données
                $result = $this->database->prepare(
                    "INSERT INTO fichiers (nom, chemin, type, taille, user_id, date_upload) 
                     VALUES (?, ?, ?, ?, ?, NOW())",
                    [$file['name'], $filepath, $file['type'], $file['size'], $user_id]
                );

                if ($result > 0) {
                    $this->successResponse(['filepath' => $filepath, 'id' => $this->database->lastInsertId()], 'Image uploadée avec succès');
                } else {
                    $this->errorResponse('Erreur lors de l\'enregistrement en base de données');
                }
            } else {
                $this->errorResponse('Erreur lors du déplacement du fichier');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'upload: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un fichier
     */
    public function destroy($id)
    {
        try {
            // Récupérer les informations du fichier
            $file = $this->database->select(
                "SELECT * FROM fichiers WHERE id = ?",
                [$id]
            );

            if (empty($file)) {
                $this->errorResponse('Fichier non trouvé', 404);
            }

            $file = $file[0];

            // Supprimer le fichier physique
            if (file_exists($file['chemin'])) {
                unlink($file['chemin']);
            }

            // Supprimer de la base de données
            $result = $this->database->prepare(
                "DELETE FROM fichiers WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Fichier supprimé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer les fichiers d'un utilisateur
     */
    public function deleteByUser($user_id)
    {
        try {
            // Récupérer tous les fichiers de l'utilisateur
            $files = $this->database->select(
                "SELECT * FROM fichiers WHERE user_id = ?",
                [$user_id]
            );

            $deleted = 0;
            foreach ($files as $file) {
                // Supprimer le fichier physique
                if (file_exists($file['chemin'])) {
                    unlink($file['chemin']);
                }
                $deleted++;
            }

            // Supprimer de la base de données
            $result = $this->database->prepare(
                "DELETE FROM fichiers WHERE user_id = ?",
                [$user_id]
            );

            if ($result > 0) {
                $this->successResponse(['deleted_count' => $deleted], 'Fichiers supprimés avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les fichiers d'un utilisateur
     */
    public function getByUser($user_id)
    {
        try {
            $files = $this->database->select(
                "SELECT * FROM fichiers WHERE user_id = ? ORDER BY date_upload DESC",
                [$user_id]
            );

            $this->successResponse($files, 'Fichiers récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les fichiers par type
     */
    public function getByType($type)
    {
        try {
            $files = $this->database->select(
                "SELECT f.*, u.nom, u.prenom 
                 FROM fichiers f 
                 JOIN utilisateurs u ON f.user_id = u.id 
                 WHERE f.type LIKE ? 
                 ORDER BY f.date_upload DESC",
                ["%$type%"]
            );

            $this->successResponse($files, 'Fichiers récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération: ' . $e->getMessage());
        }
    }
}
?> 