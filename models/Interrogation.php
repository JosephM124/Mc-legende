<?php
namespace Models;

class Interrogation extends \Models\Database
{
    private $id;
    private $titre;
    private $description;
    private $duree;
    private $matiere;
    private $niveau;
    private $statut;
    private $date_creation;
    private $date_debut;
    private $date_fin;
    private $created_by;

    public function __construct()
    {
        parent::__construct(\App\App::getConfigInstance());
    }

    /**
     * Récupérer toutes les interrogations
     */
    public function all()
    {
        return $this->select(
            "SELECT i.*, COUNT(q.id) as nombre_questions 
             FROM interrogations i 
             LEFT JOIN questions q ON i.id = q.interrogation_id 
             GROUP BY i.id 
             ORDER BY i.date_creation DESC"
        );
    }

    /**
     * Récupérer une interrogation par ID
     */
    public function find($id)
    {
        $result = $this->select(
            "SELECT * FROM interrogations WHERE id = ?",
            [$id]
        );
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Créer une nouvelle interrogation
     */
    public function create($data)
    {
        $result = $this->prepare(
            "INSERT INTO interrogations (titre, description, duree, matiere, niveau, statut, date_creation, created_by) 
             VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)",
            [
                $data['titre'],
                $data['description'],
                $data['duree'],
                $data['matiere'],
                $data['niveau'] ?? 'tous',
                $data['statut'] ?? 'draft',
                $data['created_by'] ?? 1
            ]
        );

        return $result > 0 ? $this->lastInsertId() : false;
    }

    /**
     * Mettre à jour une interrogation
     */
    public function update($id, $data)
    {
        $updateFields = [];
        $params = [];

        $allowedFields = ['titre', 'description', 'duree', 'matiere', 'niveau', 'statut'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($updateFields)) {
            return false;
        }

        $params[] = $id;
        $sql = "UPDATE interrogations SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        return $this->prepare($sql, $params) > 0;
    }

    /**
     * Supprimer une interrogation
     */
    public function delete($id)
    {
        return $this->prepare(
            "DELETE FROM interrogations WHERE id = ?",
            [$id]
        ) > 0;
    }

    /**
     * Récupérer les interrogations actives
     */
    public function getActive()
    {
        return $this->select(
            "SELECT i.*, COUNT(q.id) as nombre_questions 
             FROM interrogations i 
             LEFT JOIN questions q ON i.id = q.interrogation_id 
             WHERE i.statut = 'active' 
             GROUP BY i.id 
             ORDER BY i.date_creation DESC"
        );
    }

    /**
     * Récupérer les interrogations par matière
     */
    public function getByMatiere($matiere)
    {
        return $this->select(
            "SELECT i.*, COUNT(q.id) as nombre_questions 
             FROM interrogations i 
             LEFT JOIN questions q ON i.id = q.interrogation_id 
             WHERE i.matiere = ? 
             GROUP BY i.id 
             ORDER BY i.date_creation DESC",
            [$matiere]
        );
    }

    /**
     * Récupérer les interrogations par niveau
     */
    public function getByNiveau($niveau)
    {
        return $this->select(
            "SELECT i.*, COUNT(q.id) as nombre_questions 
             FROM interrogations i 
             LEFT JOIN questions q ON i.id = q.interrogation_id 
             WHERE i.niveau = ? OR i.niveau = 'tous' 
             GROUP BY i.id 
             ORDER BY i.date_creation DESC",
            [$niveau]
        );
    }

    /**
     * Démarrer une interrogation
     */
    public function start($id)
    {
        return $this->prepare(
            "UPDATE interrogations SET statut = 'active', date_debut = NOW() WHERE id = ?",
            [$id]
        ) > 0;
    }

    /**
     * Arrêter une interrogation
     */
    public function stop($id)
    {
        return $this->prepare(
            "UPDATE interrogations SET statut = 'finished', date_fin = NOW() WHERE id = ?",
            [$id]
        ) > 0;
    }

    /**
     * Récupérer les interrogations avec leurs statistiques
     */
    public function getWithStats()
    {
        return $this->select(
            "SELECT i.*, 
                    COUNT(q.id) as nombre_questions,
                    COUNT(DISTINCT r.eleve_id) as nombre_participants,
                    AVG(r.score) as score_moyen,
                    MAX(r.score) as score_max,
                    MIN(r.score) as score_min
             FROM interrogations i 
             LEFT JOIN questions q ON i.id = q.interrogation_id 
             LEFT JOIN resultats r ON i.id = r.interrogation_id 
             GROUP BY i.id 
             ORDER BY i.date_creation DESC"
        );
    }

    /**
     * Récupérer les interrogations récentes
     */
    public function getRecent($limit = 10)
    {
        return $this->select(
            "SELECT i.*, COUNT(q.id) as nombre_questions 
             FROM interrogations i 
             LEFT JOIN questions q ON i.id = q.interrogation_id 
             GROUP BY i.id 
             ORDER BY i.date_creation DESC 
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Rechercher des interrogations
     */
    public function search($term)
    {
        return $this->select(
            "SELECT i.*, COUNT(q.id) as nombre_questions 
             FROM interrogations i 
             LEFT JOIN questions q ON i.id = q.interrogation_id 
             WHERE i.titre LIKE ? OR i.description LIKE ? OR i.matiere LIKE ? 
             GROUP BY i.id 
             ORDER BY i.date_creation DESC",
            ["%$term%", "%$term%", "%$term%"]
        );
    }

    /**
     * Récupérer les interrogations par créateur
     */
    public function getByCreator($creator_id)
    {
        return $this->select(
            "SELECT i.*, COUNT(q.id) as nombre_questions 
             FROM interrogations i 
             LEFT JOIN questions q ON i.id = q.interrogation_id 
             WHERE i.created_by = ? 
             GROUP BY i.id 
             ORDER BY i.date_creation DESC",
            [$creator_id]
        );
    }

    /**
     * Vérifier si une interrogation peut être démarrée
     */
    public function canStart($id)
    {
        $interrogation = $this->find($id);
        if (!$interrogation) {
            return false;
        }

        // Vérifier s'il y a des questions
        $questions = $this->select(
            "SELECT COUNT(*) as count FROM questions WHERE interrogation_id = ?",
            [$id]
        );

        return $interrogation['statut'] === 'draft' && $questions[0]['count'] > 0;
    }

    /**
     * Récupérer les interrogations expirées
     */
    public function getExpired()
    {
        return $this->select(
            "SELECT * FROM interrogations 
             WHERE statut = 'active' 
             AND date_debut IS NOT NULL 
             AND DATE_ADD(date_debut, INTERVAL duree MINUTE) < NOW()"
        );
    }

    /**
     * Marquer les interrogations expirées comme terminées
     */
    public function markExpiredAsFinished()
    {
        return $this->prepare(
            "UPDATE interrogations 
             SET statut = 'finished', date_fin = DATE_ADD(date_debut, INTERVAL duree MINUTE) 
             WHERE statut = 'active' 
             AND date_debut IS NOT NULL 
             AND DATE_ADD(date_debut, INTERVAL duree MINUTE) < NOW()"
        );
    }
}
?> 