<?php
namespace Models;

class Question extends Database
{
    private $id;
    private $interrogation_id;
    private $question;
    private $type;
    private $points;
    private $options;
    private $ordre;
    private $temps_estime;

    public function __construct()
    {
        parent::__construct(\App\App::getConfigInstance());
    }

    /**
     * Récupérer toutes les questions
     */
    public function all()
    {
        return $this->select(
            "SELECT q.*, i.titre as interrogation_titre, i.matiere 
             FROM questions q 
             JOIN interrogations i ON q.interrogation_id = i.id 
             ORDER BY q.interrogation_id, q.ordre"
        );
    }

    /**
     * Récupérer une question par ID
     */
    public function find($id)
    {
        $result = $this->select(
            "SELECT q.*, i.titre as interrogation_titre, i.matiere 
             FROM questions q 
             JOIN interrogations i ON q.interrogation_id = i.id 
             WHERE q.id = ?",
            [$id]
        );
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Créer une nouvelle question
     */
    public function create($data)
    {
        // Récupérer l'ordre de la dernière question
        $lastOrder = $this->select(
            "SELECT MAX(ordre) as max_ordre FROM questions WHERE interrogation_id = ?",
            [$data['interrogation_id']]
        );
        
        $ordre = ($lastOrder[0]['max_ordre'] ?? 0) + 1;

        $result = $this->prepare(
            "INSERT INTO questions (interrogation_id, question, type, points, options, ordre, temps_estime) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['interrogation_id'],
                $data['question'],
                $data['type'],
                $data['points'] ?? 1,
                json_encode($data['options'] ?? []),
                $ordre,
                $data['temps_estime'] ?? 60
            ]
        );

        return $result > 0 ? $this->lastInsertId() : false;
    }

    /**
     * Mettre à jour une question
     */
    public function update($id, $data)
    {
        $updateFields = [];
        $params = [];

        $allowedFields = ['question', 'type', 'points', 'options', 'ordre', 'temps_estime'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if ($field === 'options') {
                    $updateFields[] = "$field = ?";
                    $params[] = json_encode($data[$field]);
                } else {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
        }

        if (empty($updateFields)) {
            return false;
        }

        $params[] = $id;
        $sql = "UPDATE questions SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        return $this->prepare($sql, $params) > 0;
    }

    /**
     * Supprimer une question
     */
    // public function delete($id)
    // {
    //     return $this->prepare(
    //         "DELETE FROM questions WHERE id = ?",
    //         [$id]
    //     ) > 0;
    // }

    /**
     * Récupérer les questions par interrogation
     */
    public function getByInterrogation($interrogation_id)
    {
        return $this->select(
            "SELECT * FROM questions WHERE interrogation_id = ? ORDER BY ordre",
            [$interrogation_id]
        );
    }

    /**
     * Récupérer les questions par type
     */
    public function getByType($type)
    {
        return $this->select(
            "SELECT q.*, i.titre as interrogation_titre 
             FROM questions q 
             JOIN interrogations i ON q.interrogation_id = i.id 
             WHERE q.type = ? 
             ORDER BY q.interrogation_id, q.ordre",
            [$type]
        );
    }

    /**
     * Récupérer les questions par matière
     */
    public function getByMatiere($matiere)
    {
        return $this->select(
            "SELECT q.*, i.titre as interrogation_titre 
             FROM questions q 
             JOIN interrogations i ON q.interrogation_id = i.id 
             WHERE i.matiere = ? 
             ORDER BY q.interrogation_id, q.ordre",
            [$matiere]
        );
    }

    /**
     * Récupérer les questions avec leurs statistiques de réponses
     */
    public function getWithStats($interrogation_id = null)
    {
        $sql = "SELECT q.*, i.titre as interrogation_titre, i.matiere,
                       COUNT(r.id) as nombre_reponses,
                       AVG(r.score) as score_moyen,
                       SUM(CASE WHEN r.score = q.points THEN 1 ELSE 0 END) as bonnes_reponses
                FROM questions q 
                JOIN interrogations i ON q.interrogation_id = i.id 
                LEFT JOIN reponses r ON q.id = r.question_id 
                GROUP BY q.id 
                ORDER BY q.interrogation_id, q.ordre";

        if ($interrogation_id) {
            $sql = str_replace("GROUP BY q.id", "WHERE q.interrogation_id = ? GROUP BY q.id", $sql);
            return $this->select($sql, [$interrogation_id]);
        }

        return $this->select($sql);
    }

    /**
     * Récupérer les questions difficiles (taux de réussite < 50%)
     */
    public function getDifficult()
    {
        return $this->select(
            "SELECT q.*, i.titre as interrogation_titre,
                   COUNT(r.id) as nombre_reponses,
                   AVG(r.score) as score_moyen,
                   (AVG(r.score) / q.points) * 100 as taux_reussite
                FROM questions q 
                JOIN interrogations i ON q.interrogation_id = i.id 
                LEFT JOIN reponses r ON q.id = r.question_id 
                GROUP BY q.id 
                HAVING taux_reussite < 50 
                ORDER BY taux_reussite ASC"
        );
    }

    /**
     * Récupérer les questions faciles (taux de réussite > 80%)
     */
    public function getEasy()
    {
        return $this->select(
            "SELECT q.*, i.titre as interrogation_titre,
                   COUNT(r.id) as nombre_reponses,
                   AVG(r.score) as score_moyen,
                   (AVG(r.score) / q.points) * 100 as taux_reussite
                FROM questions q 
                JOIN interrogations i ON q.interrogation_id = i.id 
                LEFT JOIN reponses r ON q.id = r.question_id 
                GROUP BY q.id 
                HAVING taux_reussite > 80 
                ORDER BY taux_reussite DESC"
        );
    }

    /**
     * Rechercher des questions
     */
    public function search($term)
    {
        return $this->select(
            "SELECT q.*, i.titre as interrogation_titre, i.matiere 
             FROM questions q 
             JOIN interrogations i ON q.interrogation_id = i.id 
             WHERE q.question LIKE ? OR i.titre LIKE ? OR i.matiere LIKE ? 
             ORDER BY q.interrogation_id, q.ordre",
            ["%$term%", "%$term%", "%$term%"]
        );
    }

    /**
     * Récupérer les statistiques des types de questions
     */
    public function getTypeStats()
    {
        return $this->select(
            "SELECT type,
                   COUNT(*) as nombre,
                   AVG(points) as points_moyens,
                   SUM(points) as points_totaux
                FROM questions 
                GROUP BY type"
        );
    }

    /**
     * Récupérer les questions par points
     */
    public function getByPoints($min_points, $max_points = null)
    {
        if ($max_points) {
            return $this->select(
                "SELECT q.*, i.titre as interrogation_titre 
                 FROM questions q 
                 JOIN interrogations i ON q.interrogation_id = i.id 
                 WHERE q.points BETWEEN ? AND ? 
                 ORDER BY q.points DESC",
                [$min_points, $max_points]
            );
        } else {
            return $this->select(
                "SELECT q.*, i.titre as interrogation_titre 
                 FROM questions q 
                 JOIN interrogations i ON q.interrogation_id = i.id 
                 WHERE q.points >= ? 
                 ORDER BY q.points DESC",
                [$min_points]
            );
        }
    }

    /**
     * Récupérer les questions récentes
     */
    public function getRecent($limit = 20)
    {
        return $this->select(
            "SELECT q.*, i.titre as interrogation_titre, i.matiere 
             FROM questions q 
             JOIN interrogations i ON q.interrogation_id = i.id 
             ORDER BY q.id DESC 
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Valider une réponse à une question
     */
    public function validateAnswer($question_id, $answer)
    {
        $question = $this->find($question_id);
        if (!$question) {
            return false;
        }

        $options = json_decode($question['options'], true);
        
        switch ($question['type']) {
            case 'choix_unique':
                return $answer === ($options['correcte'] ?? '');
                
            case 'choix_multiple':
                $correctAnswers = $options['correctes'] ?? [];
                $userAnswers = is_array($answer) ? $answer : [$answer];
                
                return count(array_diff($correctAnswers, $userAnswers)) === 0 && 
                       count(array_diff($userAnswers, $correctAnswers)) === 0;
                
            case 'texte':
                // Pour les questions texte, on peut implémenter une logique de validation
                // Pour l'instant, on retourne false
                return false;
                
            default:
                return false;
        }
    }

    /**
     * Calculer le score pour une réponse
     */
    public function calculateScore($question_id, $answer)
    {
        $question = $this->find($question_id);
        if (!$question) {
            return 0;
        }

        if ($this->validateAnswer($question_id, $answer)) {
            return $question['points'];
        }

        return 0;
    }

    /**
     * Récupérer les questions populaires (les plus utilisées)
     */
    public function getPopular($limit = 10)
    {
        return $this->select(
            "SELECT q.*, i.titre as interrogation_titre,
                   COUNT(r.id) as nombre_utilisations
                FROM questions q 
                JOIN interrogations i ON q.interrogation_id = i.id 
                LEFT JOIN reponses r ON q.id = r.question_id 
                GROUP BY q.id 
                ORDER BY nombre_utilisations DESC 
                LIMIT ?",
            [$limit]
        );
    }
}
?> 