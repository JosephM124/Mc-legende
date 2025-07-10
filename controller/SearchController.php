<?php
namespace Controllers;

class SearchController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Rechercher des utilisateurs
     */
    public function searchUtilisateurs()
    {
        try {
            $term = $_GET['q'] ?? '';
            $role = $_GET['role'] ?? '';
            $statut = $_GET['statut'] ?? '';

            if (empty($term)) {
                $this->errorResponse('Terme de recherche requis', 422);
            }

            $sql = "SELECT * FROM utilisateurs WHERE 1=1";
            $params = [];

            // Recherche par nom, prénom, email
            $sql .= " AND (nom LIKE ? OR postnom LIKE ? OR prenom LIKE ? OR email LIKE ?)";
            $params = array_merge($params, ["%$term%", "%$term%", "%$term%", "%$term%"]);

            // Filtre par rôle
            if (!empty($role)) {
                $sql .= " AND role = ?";
                $params[] = $role;
            }

            // Filtre par statut
            if (!empty($statut)) {
                $sql .= " AND statut = ?";
                $params[] = $statut;
            }

            $sql .= " ORDER BY nom, prenom";

            $utilisateurs = $this->database->select($sql, $params);
            
            $this->successResponse($utilisateurs, 'Recherche d\'utilisateurs terminée');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Rechercher des élèves
     */
    public function searchEleves()
    {
        try {
            $term = $_GET['q'] ?? '';
            $etablissement = $_GET['etablissement'] ?? '';
            $section = $_GET['section'] ?? '';

            if (empty($term)) {
                $this->errorResponse('Terme de recherche requis', 422);
            }

            $sql = "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
                    FROM eleves e 
                    JOIN utilisateurs u ON e.utilisateur_id = u.id 
                    WHERE 1=1";
            $params = [];

            // Recherche par nom, prénom, email, établissement, section
            $sql .= " AND (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? OR e.etablissement LIKE ? OR e.section LIKE ?)";
            $params = array_merge($params, ["%$term%", "%$term%", "%$term%", "%$term%", "%$term%"]);

            // Filtre par établissement
            if (!empty($etablissement)) {
                $sql .= " AND e.etablissement = ?";
                $params[] = $etablissement;
            }

            // Filtre par section
            if (!empty($section)) {
                $sql .= " AND e.section = ?";
                $params[] = $section;
            }

            $sql .= " ORDER BY u.nom, u.prenom";

            $eleves = $this->database->select($sql, $params);
            
            $this->successResponse($eleves, 'Recherche d\'élèves terminée');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Rechercher des interrogations
     */
    public function searchInterrogations()
    {
        try {
            $term = $_GET['q'] ?? '';
            $matiere = $_GET['matiere'] ?? '';
            $statut = $_GET['statut'] ?? '';

            if (empty($term)) {
                $this->errorResponse('Terme de recherche requis', 422);
            }

            $sql = "SELECT i.*, COUNT(q.id) as nombre_questions 
                    FROM interrogations i 
                    LEFT JOIN questions q ON i.id = q.interrogation_id 
                    WHERE 1=1";
            $params = [];

            // Recherche par titre, description, matière
            $sql .= " AND (i.titre LIKE ? OR i.description LIKE ? OR i.matiere LIKE ?)";
            $params = array_merge($params, ["%$term%", "%$term%", "%$term%"]);

            // Filtre par matière
            if (!empty($matiere)) {
                $sql .= " AND i.matiere = ?";
                $params[] = $matiere;
            }

            // Filtre par statut
            if (!empty($statut)) {
                $sql .= " AND i.statut = ?";
                $params[] = $statut;
            }

            $sql .= " GROUP BY i.id ORDER BY i.date_creation DESC";

            $interrogations = $this->database->select($sql, $params);
            
            $this->successResponse($interrogations, 'Recherche d\'interrogations terminée');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Rechercher des questions
     */
    public function searchQuestions()
    {
        try {
            $term = $_GET['q'] ?? '';
            $type = $_GET['type'] ?? '';
            $interrogation_id = $_GET['interrogation_id'] ?? '';

            if (empty($term)) {
                $this->errorResponse('Terme de recherche requis', 422);
            }

            $sql = "SELECT q.*, i.titre as interrogation_titre, i.matiere 
                    FROM questions q 
                    JOIN interrogations i ON q.interrogation_id = i.id 
                    WHERE 1=1";
            $params = [];

            // Recherche par question
            $sql .= " AND q.question LIKE ?";
            $params[] = "%$term%";

            // Filtre par type
            if (!empty($type)) {
                $sql .= " AND q.type = ?";
                $params[] = $type;
            }

            // Filtre par interrogation
            if (!empty($interrogation_id)) {
                $sql .= " AND q.interrogation_id = ?";
                $params[] = $interrogation_id;
            }

            $sql .= " ORDER BY q.interrogation_id, q.ordre";

            $questions = $this->database->select($sql, $params);
            
            $this->successResponse($questions, 'Recherche de questions terminée');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Rechercher des résultats
     */
    public function searchResultats()
    {
        try {
            $term = $_GET['q'] ?? '';
            $min_score = $_GET['min_score'] ?? '';
            $max_score = $_GET['max_score'] ?? '';

            if (empty($term)) {
                $this->errorResponse('Terme de recherche requis', 422);
            }

            $sql = "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                           i.titre as interrogation_titre, i.matiere 
                    FROM resultats r 
                    JOIN eleves e ON r.eleve_id = e.id 
                    JOIN interrogations i ON r.interrogation_id = i.id 
                    WHERE 1=1";
            $params = [];

            // Recherche par nom d'élève, titre d'interrogation, matière
            $sql .= " AND (e.nom LIKE ? OR e.prenom LIKE ? OR i.titre LIKE ? OR i.matiere LIKE ?)";
            $params = array_merge($params, ["%$term%", "%$term%", "%$term%", "%$term%"]);

            // Filtre par score minimum
            if (!empty($min_score)) {
                $sql .= " AND r.score >= ?";
                $params[] = $min_score;
            }

            // Filtre par score maximum
            if (!empty($max_score)) {
                $sql .= " AND r.score <= ?";
                $params[] = $max_score;
            }

            $sql .= " ORDER BY r.date_soumission DESC";

            $resultats = $this->database->select($sql, $params);
            
            $this->successResponse($resultats, 'Recherche de résultats terminée');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la recherche: ' . $e->getMessage());
        }
    }

    /**
     * Recherche globale
     */
    public function globalSearch()
    {
        try {
            $term = $_GET['q'] ?? '';

            if (empty($term)) {
                $this->errorResponse('Terme de recherche requis', 422);
            }

            $results = [];

            // Recherche dans les utilisateurs
            $utilisateurs = $this->database->select(
                "SELECT 'utilisateur' as type, id, nom, prenom, email, role 
                 FROM utilisateurs 
                 WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? 
                 LIMIT 5",
                ["%$term%", "%$term%", "%$term%"]
            );
            $results['utilisateurs'] = $utilisateurs;

            // Recherche dans les élèves
            $eleves = $this->database->select(
                "SELECT 'eleve' as type, e.id, u.nom, u.prenom, u.email, e.etablissement, e.section 
                 FROM eleves e 
                 JOIN utilisateurs u ON e.utilisateur_id = u.id 
                 WHERE u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? OR e.etablissement LIKE ? 
                 LIMIT 5",
                ["%$term%", "%$term%", "%$term%", "%$term%"]
            );
            $results['eleves'] = $eleves;

            // Recherche dans les interrogations
            $interrogations = $this->database->select(
                "SELECT 'interrogation' as type, id, titre, matiere, statut 
                 FROM interrogations 
                 WHERE titre LIKE ? OR description LIKE ? OR matiere LIKE ? 
                 LIMIT 5",
                ["%$term%", "%$term%", "%$term%"]
            );
            $results['interrogations'] = $interrogations;

            // Recherche dans les questions
            $questions = $this->database->select(
                "SELECT 'question' as type, q.id, q.question, i.titre as interrogation_titre 
                 FROM questions q 
                 JOIN interrogations i ON q.interrogation_id = i.id 
                 WHERE q.question LIKE ? OR i.titre LIKE ? 
                 LIMIT 5",
                ["%$term%", "%$term%"]
            );
            $results['questions'] = $questions;

            $this->successResponse($results, 'Recherche globale terminée');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la recherche globale: ' . $e->getMessage());
        }
    }
}
?> 