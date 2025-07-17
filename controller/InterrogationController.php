<?php
namespace Controllers;

class InterrogationController extends BaseController
{
    private $interrogation;

    public function __construct()
    {
        parent::__construct();
        $this->requireAnyRole(['eleve', 'enseignant', 'admin', 'admin_principal']);
        $this->interrogation = new \Models\Interrogation();
    }

    /**
     * Récupérer toutes les interrogations
     */
    public function index()
    {
        try {
            $interrogations = $this->database->select(
                "SELECT i.*, COUNT(q.id) as nombre_questions 
                 FROM interrogations i 
                 LEFT JOIN questions q ON i.id = q.interrogation_id 
                 GROUP BY i.id 
                 ORDER BY i.date_creation DESC"
            );
            $this->successResponse($interrogations, 'Interrogations récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des interrogations: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer une interrogation par ID
     */
    public function show($id)
    {
        try {
            $interrogation = $this->database->select(
                "SELECT i.*, COUNT(q.id) as nombre_questions 
                 FROM interrogations i 
                 LEFT JOIN questions q ON i.id = q.interrogation_id 
                 WHERE i.id = ? 
                 GROUP BY i.id",
                [$id]
            );
            
            if (empty($interrogation)) {
                $this->errorResponse('Interrogation non trouvée', 404);
            }
            
            // Récupérer les questions de l'interrogation
            $questions = $this->database->select(
                "SELECT * FROM questions WHERE interrogation_id = ?",
                [$id]
            );
            
            $interrogation[0]['questions'] = $questions;
            
            $this->successResponse($interrogation[0], 'Interrogation récupérée avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération de l\'interrogation: ' . $e->getMessage());
        }
    }

    /**
     * Créer une nouvelle interrogation
     */
    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation des données
        $rules = [
            'titre' => 'required',
            'description' => 'required',
            'duree' => 'required',
            'matiere' => 'required'
        ];

        $errors = $this->validateInput($input, $rules);
        if (!empty($errors)) {
            $this->errorResponse($errors, 422);
        }

        try {
            $result = $this->database->prepare(
                "INSERT INTO interrogations (titre, description, duree, matiere, niveau, date_creation, statut, created_by) 
                 VALUES (?, ?, ?, ?, ?, NOW(), 'draft', ?)",
                [
                    $input['titre'],
                    $input['description'],
                    $input['duree'],
                    $input['matiere'],
                    $input['niveau'] ?? 'tous',
                    $_SESSION['utilisateur']['id'] ?? 1
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Interrogation créée avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création de l\'interrogation');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la création de l\'interrogation: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une interrogation
     */
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            // Vérifier si l'interrogation existe
            $existingInterrogation = $this->database->select(
                "SELECT id FROM interrogations WHERE id = ?",
                [$id]
            );

            if (empty($existingInterrogation)) {
                $this->errorResponse('Interrogation non trouvée', 404);
            }

            $updateFields = [];
            $params = [];

            // Construire la requête de mise à jour dynamiquement
            $allowedFields = ['titre', 'description', 'duree', 'matiere', 'niveau', 'statut'];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $input[$field];
                }
            }

            if (empty($updateFields)) {
                $this->errorResponse('Aucune donnée à mettre à jour');
            }

            $params[] = $id;
            $sql = "UPDATE interrogations SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $result = $this->database->prepare($sql, $params);

            if ($result > 0) {
                $this->successResponse(null, 'Interrogation mise à jour avec succès');
            } else {
                $this->errorResponse('Aucune modification effectuée');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une interrogation
     */
    public function destroy($id)
    {
        try {
            $result = $this->database->prepare(
                "DELETE FROM interrogations WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Interrogation supprimée avec succès');
            } else {
                $this->errorResponse('Interrogation non trouvée', 404);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les interrogations par élève
     */
    public function getByEleve($eleve_id)
    {
        try {
            $interrogations = $this->database->select(
                "SELECT i.*, r.score, r.date_soumission 
                 FROM interrogations i 
                 LEFT JOIN resultats r ON i.id = r.interrogation_id AND r.eleve_id = ?
                 WHERE i.statut = 'active'
                 ORDER BY i.date_creation DESC",
                [$eleve_id]
            );
            
            $this->successResponse($interrogations, 'Interrogations de l\'élève récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les interrogations actives
     */
    public function getActive()
    {
        try {
            $interrogations = $this->database->select(
                "SELECT i.*, COUNT(q.id) as nombre_questions 
                 FROM interrogations i 
                 LEFT JOIN questions q ON i.id = q.interrogation_id 
                 WHERE i.statut = 'active' 
                 GROUP BY i.id 
                 ORDER BY i.date_creation DESC"
            );
            
            $this->successResponse($interrogations, 'Interrogations actives récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération: ' . $e->getMessage());
        }
    }

    /**
     * Ajouter des questions à une interrogation
     */
    public function addQuestions($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            if (!isset($input['questions']) || !is_array($input['questions'])) {
                $this->errorResponse('Questions requises', 422);
            }

            $questionsAdded = 0;
            foreach ($input['questions'] as $question) {
                $result = $this->database->prepare(
                    "INSERT INTO questions (interrogation_id, question, type, points, options) 
                     VALUES (?, ?, ?, ?, ?)",
                    [
                        $id,
                        $question['question'],
                        $question['type'],
                        $question['points'] ?? 1,
                        json_encode($question['options'] ?? [])
                    ]
                );
                
                if ($result > 0) {
                    $questionsAdded++;
                }
            }

            $this->successResponse(['questions_added' => $questionsAdded], 'Questions ajoutées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'ajout des questions: ' . $e->getMessage());
        }
    }

    /**
     * Démarrer une interrogation
     */
    public function startInterrogation($id)
    {
        try {
            $result = $this->database->prepare(
                "UPDATE interrogations SET statut = 'active', date_debut = NOW() WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Interrogation démarrée avec succès');
            } else {
                $this->errorResponse('Erreur lors du démarrage de l\'interrogation');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors du démarrage: ' . $e->getMessage());
        }
    }

    /**
     * Arrêter une interrogation
     */
    public function stopInterrogation($id)
    {
        try {
            $result = $this->database->prepare(
                "UPDATE interrogations SET statut = 'finished', date_fin = NOW() WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Interrogation arrêtée avec succès');
            } else {
                $this->errorResponse('Erreur lors de l\'arrêt de l\'interrogation');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'arrêt: ' . $e->getMessage());
        }
    }
}
?> 