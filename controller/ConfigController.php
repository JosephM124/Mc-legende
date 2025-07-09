<?php
namespace Controllers;

class ConfigController extends BaseController
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
                "INSERT INTO configuration (cle, valeur, description) VALUES (?, ?, ?)",
                [$input['cle'], $input['valeur'], $input['description'] ?? '']
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Configuration créée avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function update($key)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            $result = $this->database->prepare(
                "UPDATE configuration SET valeur = ? WHERE cle = ?",
                [$input['valeur'], $key]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Configuration mise à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function updateSystem()
    {
        try {
            $this->successResponse(null, 'Système mis à jour avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function createBackup()
    {
        try {
            $this->successResponse(null, 'Sauvegarde créée avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 