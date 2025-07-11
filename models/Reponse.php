<?php
namespace Models;

class Reponse extends \Models\Database
{
    private $id;
    private $question_id;
    private $eleve_id;
    private $reponse;
    private $score;
    private $temps_reponse;
    private $date_reponse;
    private $commentaire;

    public function __construct()
    {
        parent::__construct(\App\App::getConfigInstance());
    }

    /**
     * Récupérer toutes les réponses
     */
    public function all()
    {
        return $this->select(
            "SELECT r.*, q.question, e.nom as eleve_nom, e.prenom as eleve_prenom
                 FROM reponses r 
                 JOIN questions q ON r.question_id = q.id 
                 JOIN eleves e ON r.eleve_id = e.id 
                 ORDER BY r.date_reponse DESC"
        );
    }

    /**
     * Récupérer une réponse par ID
     */
    public function find($id)
    {
        $result = $this->select(
            "SELECT r.*, q.question, q.type, q.points, e.nom as eleve_nom, e.prenom as eleve_prenom
                 FROM reponses r 
                 JOIN questions q ON r.question_id = q.id 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE r.id = ?",
            [$id]
        );
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Créer une nouvelle réponse
     */
    public function create($data)
    {
        // Vérifier si la question existe
        $question = $this->select(
            "SELECT * FROM questions WHERE id = ?",
            [$data['question_id']]
        );

        if (empty($question)) {
            throw new \Exception('Question non trouvée');
        }

        // Vérifier si l'élève existe
        $eleve = $this->select(
            "SELECT id FROM eleves WHERE id = ?",
            [$data['eleve_id']]
        );

        if (empty($eleve)) {
            throw new \Exception('Élève non trouvé');
        }

        // Vérifier si une réponse existe déjà
        $existingReponse = $this->select(
            "SELECT id FROM reponses WHERE question_id = ? AND eleve_id = ?",
            [$data['question_id'], $data['eleve_id']]
        );

        if (!empty($existingReponse)) {
            throw new \Exception('Une réponse existe déjà pour cette question et cet élève');
        }

        // Calculer le score
        $score = $this->calculateScore($data['question_id'], $data['reponse']);

        $result = $this->prepare(
            "INSERT INTO reponses (question_id, eleve_id, reponse, score, temps_reponse, date_reponse) 
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $data['question_id'],
                $data['eleve_id'],
                json_encode($data['reponse']),
                $score,
                $data['temps_reponse'] ?? 0
            ]
        );

        return $result > 0 ? $this->lastInsertId() : false;
    }

    /**
     * Mettre à jour une réponse
     */
    public function updateReponse($id, $data)
    {
        // Vérifier si la réponse existe
        $existingReponse = $this->select(
            "SELECT id FROM reponses WHERE id = ?",
            [$id]
        );

        if (empty($existingReponse)) {
            throw new \Exception('Réponse non trouvée');
        }

        $updateFields = [];
        $params = [];

        // Construire la requête de mise à jour dynamiquement
        $allowedFields = ['reponse', 'score', 'temps_reponse', 'commentaire'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if ($field === 'reponse') {
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
        $sql = "UPDATE reponses SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        return $this->prepare($sql, $params) > 0;
    }

    /**
     * Supprimer une réponse
     */
    public function deleteReponse($id)
    {
        return $this->prepare(
            "DELETE FROM reponses WHERE id = ?",
            [$id]
        ) > 0;
    }

    /**
     * Récupérer les réponses par question
     */
    public function getByQuestion($question_id)
    {
        return $this->select(
            "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom
                 FROM reponses r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE r.question_id = ? 
                 ORDER BY r.date_reponse DESC",
            [$question_id]
        );
    }

    /**
     * Récupérer les réponses par élève
     */
    public function getByEleve($eleve_id)
    {
        return $this->select(
            "SELECT r.*, q.question, q.type, q.points
                 FROM reponses r 
                 JOIN questions q ON r.question_id = q.id 
                 WHERE r.eleve_id = ? 
                 ORDER BY r.date_reponse DESC",
            [$eleve_id]
        );
    }

    /**
     * Récupérer les réponses par interrogation
     */
    public function getByInterrogation($interrogation_id)
    {
        return $this->select(
            "SELECT r.*, q.question, q.type, q.points, e.nom as eleve_nom, e.prenom as eleve_prenom
                 FROM reponses r 
                 JOIN questions q ON r.question_id = q.id 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE q.interrogation_id = ? 
                 ORDER BY r.date_reponse DESC",
            [$interrogation_id]
        );
    }

    /**
     * Récupérer les réponses correctes
     */
    public function getCorrect()
    {
        return $this->select(
            "SELECT r.*, q.question, e.nom as eleve_nom, e.prenom as eleve_prenom
                 FROM reponses r 
                 JOIN questions q ON r.question_id = q.id 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE r.score > 0 
                 ORDER BY r.score DESC"
        );
    }

    /**
     * Récupérer les réponses incorrectes
     */
    public function getIncorrect()
    {
        return $this->select(
            "SELECT r.*, q.question, e.nom as eleve_nom, e.prenom as eleve_prenom
                 FROM reponses r 
                 JOIN questions q ON r.question_id = q.id 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE r.score = 0 
                 ORDER BY r.date_reponse DESC"
        );
    }

    /**
     * Récupérer les réponses récentes
     */
    public function getRecent($limit = 20)
    {
        return $this->select(
            "SELECT r.*, q.question, e.nom as eleve_nom, e.prenom as eleve_prenom
                 FROM reponses r 
                 JOIN questions q ON r.question_id = q.id 
                 JOIN eleves e ON r.eleve_id = e.id 
                 ORDER BY r.date_reponse DESC 
                 LIMIT ?",
            [$limit]
        );
    }

    /**
     * Récupérer les statistiques des réponses
     */
    public function getStats()
    {
        return $this->select(
            "SELECT 
                COUNT(*) as total_reponses,
                AVG(score) as score_moyen,
                COUNT(CASE WHEN score > 0 THEN 1 END) as bonnes_reponses,
                COUNT(CASE WHEN score = 0 THEN 1 END) as mauvaises_reponses,
                AVG(temps_reponse) as temps_moyen
             FROM reponses"
        );
    }

    /**
     * Récupérer les statistiques par question
     */
    public function getStatsByQuestion($question_id)
    {
        return $this->select(
            "SELECT 
                COUNT(*) as nombre_reponses,
                AVG(score) as score_moyen,
                COUNT(CASE WHEN score > 0 THEN 1 END) as bonnes_reponses,
                COUNT(CASE WHEN score = 0 THEN 1 END) as mauvaises_reponses,
                AVG(temps_reponse) as temps_moyen
             FROM reponses 
             WHERE question_id = ?",
            [$question_id]
        );
    }

    /**
     * Récupérer les statistiques par élève
     */
    public function getStatsByEleve($eleve_id)
    {
        return $this->select(
            "SELECT 
                COUNT(*) as nombre_reponses,
                AVG(score) as score_moyen,
                COUNT(CASE WHEN score > 0 THEN 1 END) as bonnes_reponses,
                COUNT(CASE WHEN score = 0 THEN 1 END) as mauvaises_reponses,
                AVG(temps_reponse) as temps_moyen
             FROM reponses 
             WHERE eleve_id = ?",
            [$eleve_id]
        );
    }

    /**
     * Rechercher des réponses
     */
    public function search($term)
    {
        return $this->select(
            "SELECT r.*, q.question, e.nom as eleve_nom, e.prenom as eleve_prenom
                 FROM reponses r 
                 JOIN questions q ON r.question_id = q.id 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE e.nom LIKE ? OR e.prenom LIKE ? OR q.question LIKE ? 
                 ORDER BY r.date_reponse DESC",
            ["%$term%", "%$term%", "%$term%"]
        );
    }

    /**
     * Calculer le score d'une réponse
     */
    private function calculateScore($question_id, $reponse)
    {
        try {
            $question = $this->select(
                "SELECT type, points, options FROM questions WHERE id = ?",
                [$question_id]
            );

            if (empty($question)) {
                return 0;
            }

            $question = $question[0];
            $options = json_decode($question['options'], true);

            switch ($question['type']) {
                case 'choix_unique':
                    if ($reponse === $options['correcte']) {
                        return $question['points'];
                    }
                    break;

                case 'choix_multiple':
                    $correctAnswers = $options['correctes'] ?? [];
                    $userAnswers = is_array($reponse) ? $reponse : [$reponse];
                    
                    if (count(array_diff($correctAnswers, $userAnswers)) === 0 && 
                        count(array_diff($userAnswers, $correctAnswers)) === 0) {
                        return $question['points'];
                    }
                    break;

                case 'texte':
                    // Pour les questions texte, on peut implémenter une logique de validation
                    // Pour l'instant, on donne 0 point
                    return 0;

                case 'vrai_faux':
                    if ($reponse === $options['correcte']) {
                        return $question['points'];
                    }
                    break;
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Valider une réponse
     */
    public function validate($question_id, $reponse)
    {
        $score = $this->calculateScore($question_id, $reponse);
        $isCorrect = $score > 0;

        return [
            'score' => $score,
            'is_correct' => $isCorrect,
            'feedback' => $this->getFeedback($question_id, $reponse)
        ];
    }

    /**
     * Générer un feedback pour une réponse
     */
    private function getFeedback($question_id, $reponse)
    {
        try {
            $question = $this->select(
                "SELECT type, options FROM questions WHERE id = ?",
                [$question_id]
            );

            if (empty($question)) {
                return 'Question non trouvée';
            }

            $question = $question[0];
            $options = json_decode($question['options'], true);

            switch ($question['type']) {
                case 'choix_unique':
                    if ($reponse === $options['correcte']) {
                        return 'Correct !';
                    } else {
                        return 'Incorrect. La bonne réponse était : ' . $options['correcte'];
                    }

                case 'choix_multiple':
                    $correctAnswers = $options['correctes'] ?? [];
                    $userAnswers = is_array($reponse) ? $reponse : [$reponse];
                    
                    if (count(array_diff($correctAnswers, $userAnswers)) === 0 && 
                        count(array_diff($userAnswers, $correctAnswers)) === 0) {
                        return 'Correct !';
                    } else {
                        return 'Incorrect. Les bonnes réponses étaient : ' . implode(', ', $correctAnswers);
                    }

                case 'vrai_faux':
                    if ($reponse === $options['correcte']) {
                        return 'Correct !';
                    } else {
                        return 'Incorrect. La bonne réponse était : ' . $options['correcte'];
                    }

                default:
                    return 'Réponse enregistrée';
            }
        } catch (\Exception $e) {
            return 'Erreur lors de la génération du feedback';
        }
    }
}
?> 