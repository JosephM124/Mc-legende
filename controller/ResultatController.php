<?php
namespace Controllers;

class ResultatController extends BaseController
{
    private $resultat;

    public function __construct()
    {
        parent::__construct();
        $this->resultat = new \Models\Resultat();
    }

    /**
     * Récupérer tous les résultats
     */
    public function index()
    {
        try {
            $resultats = $this->database->select(
                "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                        i.titre as interrogation_titre, i.matiere
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 ORDER BY r.date_soumission DESC"
            );
            $this->successResponse($resultats, 'Résultats récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des résultats: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer un résultat par ID
     */
    public function show($id)
    {
        try {
            $resultat = $this->database->select(
                "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                        i.titre as interrogation_titre, i.matiere, i.duree
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 WHERE r.id = ?",
                [$id]
            );
            
            if (empty($resultat)) {
                $this->errorResponse('Résultat non trouvé', 404);
            }
            
            $this->successResponse($resultat[0], 'Résultat récupéré avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération du résultat: ' . $e->getMessage());
        }
    }

    /**
     * Créer un nouveau résultat
     */
    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation des données
        $rules = [
            'eleve_id' => 'required',
            'interrogation_id' => 'required',
            'score' => 'required'
        ];

        $errors = $this->validateInput($input, $rules);
        if (!empty($errors)) {
            $this->errorResponse($errors, 422);
        }

        try {
            // Vérifier si un résultat existe déjà pour cet élève et cette interrogation
            $existingResult = $this->database->select(
                "SELECT id FROM resultats WHERE eleve_id = ? AND interrogation_id = ?",
                [$input['eleve_id'], $input['interrogation_id']]
            );

            if (!empty($existingResult)) {
                $this->errorResponse('Un résultat existe déjà pour cet élève et cette interrogation', 409);
            }

            $result = $this->database->prepare(
                "INSERT INTO resultats (eleve_id, interrogation_id, score, temps_utilise, reponses, date_soumission) 
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    $input['eleve_id'],
                    $input['interrogation_id'],
                    $input['score'],
                    $input['temps_utilise'] ?? 0,
                    json_encode($input['reponses'] ?? [])
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Résultat créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création du résultat');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la création du résultat: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour un résultat
     */
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            // Vérifier si le résultat existe
            $existingResult = $this->database->select(
                "SELECT id FROM resultats WHERE id = ?",
                [$id]
            );

            if (empty($existingResult)) {
                $this->errorResponse('Résultat non trouvé', 404);
            }

            $updateFields = [];
            $params = [];

            // Construire la requête de mise à jour dynamiquement
            $allowedFields = ['score', 'temps_utilise', 'reponses', 'commentaire'];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    if ($field === 'reponses') {
                        $updateFields[] = "$field = ?";
                        $params[] = json_encode($input[$field]);
                    } else {
                        $updateFields[] = "$field = ?";
                        $params[] = $input[$field];
                    }
                }
            }

            if (empty($updateFields)) {
                $this->errorResponse('Aucune donnée à mettre à jour');
            }

            $params[] = $id;
            $sql = "UPDATE resultats SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $result = $this->database->prepare($sql, $params);

            if ($result > 0) {
                $this->successResponse(null, 'Résultat mis à jour avec succès');
            } else {
                $this->errorResponse('Aucune modification effectuée');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un résultat
     */
    public function destroy($id)
    {
        try {
            $result = $this->database->prepare(
                "DELETE FROM resultats WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Résultat supprimé avec succès');
            } else {
                $this->errorResponse('Résultat non trouvé', 404);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les résultats par élève
     */
    public function getByEleve($eleve_id)
    {
        try {
            $resultats = $this->database->select(
                "SELECT r.*, i.titre as interrogation_titre, i.matiere, i.duree
                 FROM resultats r 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 WHERE r.eleve_id = ? 
                 ORDER BY r.date_soumission DESC",
                [$eleve_id]
            );
            
            $this->successResponse($resultats, 'Résultats de l\'élève récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les résultats par interrogation
     */
    public function getByInterrogation($interrogation_id)
    {
        try {
            $resultats = $this->database->select(
                "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, e.etablissement
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 WHERE r.interrogation_id = ? 
                 ORDER BY r.score DESC, r.temps_utilise ASC",
                [$interrogation_id]
            );
            
            $this->successResponse($resultats, 'Résultats de l\'interrogation récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération: ' . $e->getMessage());
        }
    }

    /**
     * Soumettre un résultat (pour les élèves)
     */
    public function submitResult()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation des données
        $rules = [
            'interrogation_id' => 'required',
            'reponses' => 'required'
        ];

        $errors = $this->validateInput($input, $rules);
        if (!empty($errors)) {
            $this->errorResponse($errors, 422);
        }

        try {
            // Récupérer l'ID de l'élève connecté
            $eleve_id = $_SESSION['utilisateur']['eleve_id'] ?? null;
            if (!$eleve_id) {
                $this->errorResponse('Utilisateur non connecté', 401);
            }

            // Vérifier si l'interrogation est active
            $interrogation = $this->database->select(
                "SELECT * FROM interrogations WHERE id = ? AND statut = 'active'",
                [$input['interrogation_id']]
            );

            if (empty($interrogation)) {
                $this->errorResponse('Interrogation non disponible', 404);
            }

            // Calculer le score
            $score = $this->calculateScore($input['interrogation_id'], $input['reponses']);

            // Vérifier si un résultat existe déjà
            $existingResult = $this->database->select(
                "SELECT id FROM resultats WHERE eleve_id = ? AND interrogation_id = ?",
                [$eleve_id, $input['interrogation_id']]
            );

            if (!empty($existingResult)) {
                $this->errorResponse('Vous avez déjà soumis cette interrogation', 409);
            }

            // Créer le résultat
            $result = $this->database->prepare(
                "INSERT INTO resultats (eleve_id, interrogation_id, score, temps_utilise, reponses, date_soumission) 
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    $eleve_id,
                    $input['interrogation_id'],
                    $score,
                    $input['temps_utilise'] ?? 0,
                    json_encode($input['reponses'])
                ]
            );

            if ($result > 0) {
                $this->successResponse([
                    'id' => $this->database->lastInsertId(),
                    'score' => $score
                ], 'Résultat soumis avec succès');
            } else {
                $this->errorResponse('Erreur lors de la soumission du résultat');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la soumission: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour le score d'un résultat
     */
    public function updateScore($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            if (!isset($input['score'])) {
                $this->errorResponse('Score requis', 422);
            }

            $result = $this->database->prepare(
                "UPDATE resultats SET score = ? WHERE id = ?",
                [$input['score'], $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Score mis à jour avec succès');
            } else {
                $this->errorResponse('Résultat non trouvé', 404);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la mise à jour du score: ' . $e->getMessage());
        }
    }

    /**
     * Calculer le score d'une interrogation
     */
    private function calculateScore($interrogation_id, $reponses)
    {
        try {
            // Récupérer les questions de l'interrogation
            $questions = $this->database->select(
                "SELECT id, type, points, options FROM questions WHERE interrogation_id = ?",
                [$interrogation_id]
            );

            $totalScore = 0;
            $maxScore = 0;

            foreach ($questions as $question) {
                $maxScore += $question['points'];
                
                if (isset($reponses[$question['id']])) {
                    $reponse = $reponses[$question['id']];
                    $options = json_decode($question['options'], true);
                    
                    // Logique de calcul du score selon le type de question
                    switch ($question['type']) {
                        case 'choix_unique':
                            if ($reponse === $options['correcte']) {
                                $totalScore += $question['points'];
                            }
                            break;
                        case 'choix_multiple':
                            $correctAnswers = $options['correctes'] ?? [];
                            $userAnswers = is_array($reponse) ? $reponse : [$reponse];
                            
                            if (count(array_diff($correctAnswers, $userAnswers)) === 0 && 
                                count(array_diff($userAnswers, $correctAnswers)) === 0) {
                                $totalScore += $question['points'];
                            }
                            break;
                        case 'texte':
                            // Pour les questions texte, on peut implémenter une logique de validation
                            // Pour l'instant, on donne 0 point
                            break;
                    }
                }
            }

            return $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
?> 