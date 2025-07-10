<?php
namespace Controllers;

class ReponseController extends BaseController
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
                "INSERT INTO reponses (eleve_id, question_id, reponse, est_correcte, temps_reponse, date_reponse) 
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    $input['eleve_id'] ?? null,
                    $input['question_id'] ?? null,
                    $input['reponse'] ?? '',
                    $input['est_correcte'] ?? false,
                    $input['temps_reponse'] ?? 0
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Réponse créée avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création de la réponse');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function validateReponse()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            // Récupérer la question pour validation
            $question = $this->database->select(
                "SELECT * FROM questions WHERE id = ?",
                [$input['question_id'] ?? 0]
            );

            if (empty($question)) {
                $this->errorResponse('Question non trouvée', 404);
            }

            $question = $question[0];
            $options = json_decode($question['options'], true);
            $reponse_eleve = $input['reponse'] ?? '';
            $est_correcte = false;

            // Validation selon le type de question
            switch ($question['type']) {
                case 'choix_unique':
                    $est_correcte = ($reponse_eleve === $options['correcte'] ?? '');
                    break;
                case 'choix_multiple':
                    $reponses_correctes = $options['correctes'] ?? [];
                    $est_correcte = (is_array($reponse_eleve) && 
                                   count(array_diff($reponse_eleve, $reponses_correctes)) === 0 &&
                                   count(array_diff($reponses_correctes, $reponse_eleve)) === 0);
                    break;
                case 'vrai_faux':
                    $est_correcte = ($reponse_eleve === ($options['correcte'] ?? false));
                    break;
                case 'texte_libre':
                    // Pour le texte libre, on peut faire une validation basique
                    $est_correcte = !empty(trim($reponse_eleve));
                    break;
            }

            // Enregistrer la réponse
            $result = $this->database->prepare(
                "INSERT INTO reponses (eleve_id, question_id, reponse, est_correcte, temps_reponse, date_reponse) 
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    $input['eleve_id'] ?? null,
                    $input['question_id'] ?? null,
                    is_array($reponse_eleve) ? json_encode($reponse_eleve) : $reponse_eleve,
                    $est_correcte,
                    $input['temps_reponse'] ?? 0
                ]
            );

            if ($result > 0) {
                $this->successResponse([
                    'est_correcte' => $est_correcte,
                    'points' => $est_correcte ? $question['points'] : 0,
                    'reponse_id' => $this->database->lastInsertId()
                ], 'Réponse validée avec succès');
            } else {
                $this->errorResponse('Erreur lors de l\'enregistrement de la réponse');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function getByEleve($eleve_id)
    {
        try {
            $reponses = $this->database->select(
                "SELECT r.*, q.question, q.points, q.type 
                 FROM reponses r 
                 JOIN questions q ON r.question_id = q.id 
                 WHERE r.eleve_id = ? 
                 ORDER BY r.date_reponse DESC",
                [$eleve_id]
            );

            $this->successResponse($reponses, 'Réponses récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function getByQuestion($question_id)
    {
        try {
            $reponses = $this->database->select(
                "SELECT r.*, e.nom, e.prenom 
                 FROM reponses r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE r.question_id = ? 
                 ORDER BY r.date_reponse DESC",
                [$question_id]
            );

            $this->successResponse($reponses, 'Réponses récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    public function getStatsByQuestion($question_id)
    {
        try {
            $stats = $this->database->select(
                "SELECT 
                    COUNT(*) as total_reponses,
                    SUM(CASE WHEN est_correcte = 1 THEN 1 ELSE 0 END) as reponses_correctes,
                    AVG(CASE WHEN est_correcte = 1 THEN 1 ELSE 0 END) * 100 as pourcentage_correct,
                    AVG(temps_reponse) as temps_moyen
                 FROM reponses 
                 WHERE question_id = ?",
                [$question_id]
            );

            if (!empty($stats)) {
                $this->successResponse($stats[0], 'Statistiques récupérées avec succès');
            } else {
                $this->errorResponse('Aucune réponse trouvée pour cette question');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }
}
?> 