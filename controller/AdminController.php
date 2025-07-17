<?php 
namespace Controllers;

class AdminController extends \Controllers\BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAnyRole(['admin', 'admin_principal']);
    }
    
    public function enregistrer_activite_admin($admin_id, $action, $details = null) {
        $this->database->prepare(
          "INSERT INTO activites_admin (admin_id, action, details, date_activite) VALUES (?, ?, ?, NOW())",
          [$admin_id, $action, $details]
        );
    }

    public function getActivites()
    {
        try {
            $activites = $this->database->select(
                "SELECT a.*, u.nom, u.prenom, u.email 
                 FROM activites_admin a 
                 JOIN utilisateurs u ON a.admin_id = u.id 
                 ORDER BY a.date_activite DESC 
                 LIMIT 100"
            );

            $this->successResponse($activites, 'Activités récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function getActivitesByAdmin($admin_id)
    {
        try {
            $activites = $this->database->select(
                "SELECT a.*, u.nom, u.prenom, u.email 
                 FROM activites_admin a 
                 JOIN utilisateurs u ON a.admin_id = u.id 
                 WHERE a.admin_id = ? 
                 ORDER BY a.date_activite DESC",
                [$admin_id]
            );

            $this->successResponse($activites, 'Activités récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function getStatistiques()
    {
        try {
            $stats = $this->database->select(
                "SELECT 
                    (SELECT COUNT(*) FROM utilisateurs WHERE role = 'admin') as total_admins,
                    (SELECT COUNT(*) FROM utilisateurs WHERE role = 'eleve') as total_eleves,
                    (SELECT COUNT(*) FROM interrogations) as total_interrogations,
                    (SELECT COUNT(*) FROM resultats) as total_resultats,
                    (SELECT AVG(score) FROM resultats) as score_moyen,
                    (SELECT COUNT(*) FROM activites_admin WHERE date_activite >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as activites_semaine"
            );

            if (!empty($stats)) {
                $this->successResponse($stats[0], 'Statistiques récupérées avec succès');
            } else {
                $this->errorResponse('Erreur lors de la récupération des statistiques');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Retourne toutes les statistiques du dashboard admin principal
     */
    public function getDashboardStats()
    {
        try {
            $stats = [];
            // Stats globales
            $stats['total_eleves'] = $this->database->select("SELECT COUNT(*) as total FROM eleves")[0]['total'];
            $stats['total_interros'] = $this->database->select("SELECT COUNT(*) as total FROM quiz WHERE statut = 'actif'")[0]['total'];
            $stats['total_notifications'] = $this->database->select("SELECT COUNT(*) as total FROM notifications WHERE lue = 0")[0]['total'];
            $stats['total_admins'] = $this->database->select("SELECT COUNT(*) as total FROM utilisateurs WHERE role = 'admin_simple'")[0]['total'];
            // Sections
            $stats['sections'] = $this->database->select("SELECT section, COUNT(*) as total FROM eleves GROUP BY section");
            // Interros par jour (7 derniers jours)
            $stats['interro_stats'] = $this->database->select("SELECT DATE(date_lancement) as jour, COUNT(*) as total FROM quiz GROUP BY jour ORDER BY jour DESC LIMIT 7");
            // Catégories d'activité
            $stats['cat_activite'] = $this->database->select("SELECT categorie_activite, COUNT(*) as total FROM eleves GROUP BY categorie_activite");
            // Distribution des scores
            $stats['score_stats'] = $this->database->select("SELECT FLOOR(score) as score, COUNT(*) as total FROM resultats WHERE score IS NOT NULL GROUP BY FLOOR(score) ORDER BY score");
            $this->successResponse($stats, 'Statistiques dashboard récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteActivite($id)
    {
        try {
            $result = $this->database->prepare(
                "DELETE FROM activites_admin WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Activité supprimée avec succès');
            } else {
                $this->errorResponse('Erreur lors de la suppression');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function deleteOldActivites()
    {
        try {
            $result = $this->database->prepare(
                "DELETE FROM activites_admin WHERE date_activite < DATE_SUB(NOW(), INTERVAL 90 DAY)"
            );
            
            $this->successResponse(null, 'Anciennes activités supprimées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?>