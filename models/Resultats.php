<?php

namespace Models;

class Resultats extends Database
{
    private $id;
    private $eleve_id;
    private $interrogation_id;
    private $score;
    private $temps_utilise;
    private $reponses;
    private $date_soumission;
    private $commentaire;

    public function __construct()
    {
        parent::__construct(\App\App::getConfigInstance());
    }

    /**
     * Récupérer tous les résultats
     */
    public function all()
    {
        return $this->select(
            "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                    i.titre as interrogation_titre, i.matiere
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 ORDER BY r.date_soumission DESC"
        );
    }

    /**
     * Récupérer un résultat par ID
     */
    public function find($id)
    {
        $result = $this->select(
            "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                    i.titre as interrogation_titre, i.matiere, i.duree
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 WHERE r.id = ?",
            [$id]
        );
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Créer un nouveau résultat
     */
    public function create($data)
    {
        // Vérifier si un résultat existe déjà pour cet élève et cette interrogation
        $existingResult = $this->select(
            "SELECT id FROM resultats WHERE eleve_id = ? AND interrogation_id = ?",
            [$data['eleve_id'], $data['interrogation_id']]
        );

        if (!empty($existingResult)) {
            throw new \Exception('Un résultat existe déjà pour cet élève et cette interrogation');
        }

        $result = $this->prepare(
            "INSERT INTO resultats (eleve_id, interrogation_id, score, temps_utilise, reponses, date_soumission) 
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $data['eleve_id'],
                $data['interrogation_id'],
                $data['score'],
                $data['temps_utilise'] ?? 0,
                json_encode($data['reponses'] ?? [])
            ]
        );

        return $result > 0 ? $this->lastInsertId() : false;
    }

    /**
     * Mettre à jour un résultat
     */
    public function updateResultat($id, $data)
    {
        // Vérifier si le résultat existe
        $existingResult = $this->select(
            "SELECT id FROM resultats WHERE id = ?",
            [$id]
        );

        if (empty($existingResult)) {
            throw new \Exception('Résultat non trouvé');
        }

        $updateFields = [];
        $params = [];

        // Construire la requête de mise à jour dynamiquement
        $allowedFields = ['score', 'temps_utilise', 'reponses', 'commentaire'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if ($field === 'reponses') {
                    $updateFields[] = "$field = ?";
                    $params[] = json_encode($data[$field]);
                } else {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
        }

        if (empty($updateFields)) {
            throw new \Exception('Aucune donnée à mettre à jour');
        }

        $params[] = $id;
        $sql = "UPDATE resultats SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        return $this->prepare($sql, $params) > 0;
    }

    /**
     * Supprimer un résultat
     */
    public function deleteResultat($id)
    {
        return $this->prepare(
            "DELETE FROM resultats WHERE id = ?",
            [$id]
        ) > 0;
    }

    /**
     * Récupérer les résultats par élève
     */
    public function getByEleve($eleve_id)
    {
        return $this->select(
            "SELECT r.*, i.titre as interrogation_titre, i.matiere, i.duree
                 FROM resultats r 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 WHERE r.eleve_id = ? 
                 ORDER BY r.date_soumission DESC",
            [$eleve_id]
        );
    }

    /**
     * Récupérer les résultats par interrogation
     */
    public function getByInterrogation($interrogation_id)
    {
        return $this->select(
            "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, e.etablissement
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE r.interrogation_id = ? 
                 ORDER BY r.score DESC, r.temps_utilise ASC",
            [$interrogation_id]
        );
    }

    /**
     * Récupérer les résultats par score
     */
    public function getByScoreRange($minScore, $maxScore)
    {
        return $this->select(
            "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                    i.titre as interrogation_titre, i.matiere
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 WHERE r.score BETWEEN ? AND ? 
                 ORDER BY r.score DESC",
            [$minScore, $maxScore]
        );
    }

    /**
     * Récupérer les meilleurs résultats
     */
    public function getTopResults($limit = 10)
    {
        return $this->select(
            "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                    i.titre as interrogation_titre, i.matiere
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 ORDER BY r.score DESC, r.temps_utilise ASC 
                 LIMIT ?",
            [$limit]
        );
    }

    /**
     * Récupérer les résultats récents
     */
    public function getRecent($limit = 20)
    {
        return $this->select(
            "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                    i.titre as interrogation_titre, i.matiere
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 ORDER BY r.date_soumission DESC 
                 LIMIT ?",
            [$limit]
        );
    }

    /**
     * Récupérer les statistiques des résultats
     */
    public function getStats()
    {
        return $this->select(
            "SELECT 
                COUNT(*) as total_resultats,
                AVG(score) as score_moyen,
                MAX(score) as score_max,
                MIN(score) as score_min,
                AVG(temps_utilise) as temps_moyen,
                COUNT(DISTINCT eleve_id) as nombre_eleves,
                COUNT(DISTINCT interrogation_id) as nombre_interrogations
             FROM resultats"
        );
    }

    /**
     * Récupérer les statistiques par interrogation
     */
    public function getStatsByInterrogation($interrogation_id)
    {
        return $this->select(
            "SELECT 
                COUNT(*) as nombre_participants,
                AVG(score) as score_moyen,
                MAX(score) as score_max,
                MIN(score) as score_min,
                AVG(temps_utilise) as temps_moyen,
                COUNT(CASE WHEN score >= 80 THEN 1 END) as excellents,
                COUNT(CASE WHEN score >= 60 AND score < 80 THEN 1 END) as bons,
                COUNT(CASE WHEN score >= 40 AND score < 60 THEN 1 END) as moyens,
                COUNT(CASE WHEN score < 40 THEN 1 END) as faibles
             FROM resultats 
             WHERE interrogation_id = ?",
            [$interrogation_id]
        );
    }

    /**
     * Récupérer les statistiques par élève
     */
    public function getStatsByEleve($eleve_id)
    {
        return $this->select(
            "SELECT 
                COUNT(*) as nombre_interrogations,
                AVG(score) as score_moyen,
                MAX(score) as score_max,
                MIN(score) as score_min,
                AVG(temps_utilise) as temps_moyen,
                SUM(score) as score_total
             FROM resultats 
             WHERE eleve_id = ?",
            [$eleve_id]
        );
    }

    /**
     * Rechercher des résultats
     */
    public function search($term)
    {
        return $this->select(
            "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                    i.titre as interrogation_titre, i.matiere
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 WHERE e.nom LIKE ? OR e.prenom LIKE ? OR i.titre LIKE ? OR i.matiere LIKE ? 
                 ORDER BY r.date_soumission DESC",
            ["%$term%", "%$term%", "%$term%", "%$term%"]
        );
    }

    /**
     * Récupérer les résultats par date
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->select(
            "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                    i.titre as interrogation_titre, i.matiere
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 WHERE r.date_soumission BETWEEN ? AND ? 
                 ORDER BY r.date_soumission DESC",
            [$startDate, $endDate]
        );
    }

    /**
     * Mettre à jour le score d'un résultat
     */
    public function updateScore($id, $score)
    {
        return $this->prepare(
            "UPDATE resultats SET score = ? WHERE id = ?",
            [$score, $id]
        ) > 0;
    }

    /**
     * Récupérer les résultats avec classement
     */
    public function getWithRanking($interrogation_id = null)
    {
        if ($interrogation_id) {
            return $this->select(
                "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom,
                       RANK() OVER (ORDER BY r.score DESC, r.temps_utilise ASC) as classement
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE r.interrogation_id = ? 
                 ORDER BY r.score DESC, r.temps_utilise ASC",
                [$interrogation_id]
            );
        } else {
            return $this->select(
                "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom,
                       i.titre as interrogation_titre,
                       RANK() OVER (PARTITION BY r.interrogation_id ORDER BY r.score DESC, r.temps_utilise ASC) as classement
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 ORDER BY r.interrogation_id, r.score DESC"
            );
        }
    }
}
?>