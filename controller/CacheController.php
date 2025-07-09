<?php
namespace Controllers;

class CacheController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function clear()
    {
        try {
            // Logique de nettoyage du cache
            $this->successResponse(null, 'Cache vidé avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function delete($key)
    {
        try {
            // Logique de suppression d'une clé de cache
            $this->successResponse(null, 'Clé de cache supprimée avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 