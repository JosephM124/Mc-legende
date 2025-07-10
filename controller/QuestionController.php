<?php
namespace Controllers;

class QuestionController extends BaseController
{
    private $question;

    public function __construct()
    {
        parent::__construct();
        $this->question = new \Models\Question();
    }

    /**
     * Récupérer toutes les questions
     */
    public function index()
    {
        try {
            $questions = $this->database->select(
                "SELECT q.*, i.titre as interrogation_titre, i.matiere 
                 FROM questions q 
                 JOIN interrogations i ON q.interrogation_id = i.id 
                 ORDER BY q.interrogation_id, q.ordre"
            );
            $this->successResponse($questions, 'Questions récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des questions: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer une question par ID
     */
    public function show($id)
    {
        try {
            $question = $this->database->select(
                "SELECT q.*, i.titre as interrogation_titre, i.matiere 
                 FROM questions q 
                 JOIN interrogations i ON q.interrogation_id = i.id 
                 WHERE q.id = ?",
                [$id]
            );
            
            if (empty($question)) {
                $this->errorResponse('Question non trouvée', 404);
            }
            
            $this->successResponse($question[0], 'Question récupérée avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération de la question: ' . $e->getMessage());
        }
    }

    /**
     * Créer une nouvelle question
     */
    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation des données
        $rules = [
            'interrogation_id' => 'required',
            'question' => 'required',
            'type' => 'required'
        ];

        $errors = $this->validateInput($input, $rules);
        if (!empty($errors)) {
            $this->errorResponse($errors, 422);
        }

        try {
            // Vérifier si l'interrogation existe
            $interrogation = $this->database->select(
                "SELECT id FROM interrogations WHERE id = ?",
                [$input['interrogation_id']]
            );

            if (empty($interrogation)) {
                $this->errorResponse('Interrogation non trouvée', 404);
            }

            // Récupérer l'ordre de la dernière question
            $lastOrder = $this->database->select(
                "SELECT MAX(ordre) as max_ordre FROM questions WHERE interrogation_id = ?",
                [$input['interrogation_id']]
            );
            
            $ordre = ($lastOrder[0]['max_ordre'] ?? 0) + 1;

            $result = $this->database->prepare(
                "INSERT INTO questions (interrogation_id, question, type, points, options, ordre, temps_estime) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $input['interrogation_id'],
                    $input['question'],
                    $input['type'],
                    $input['points'] ?? 1,
                    json_encode($input['options'] ?? []),
                    $ordre,
                    $input['temps_estime'] ?? 60
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Question créée avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création de la question');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la création de la question: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une question
     */
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            // Vérifier si la question existe
            $existingQuestion = $this->database->select(
                "SELECT id FROM questions WHERE id = ?",
                [$id]
            );

            if (empty($existingQuestion)) {
                $this->errorResponse('Question non trouvée', 404);
            }

            $updateFields = [];
            $params = [];

            // Construire la requête de mise à jour dynamiquement
            $allowedFields = ['question', 'type', 'points', 'options', 'ordre', 'temps_estime'];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    if ($field === 'options') {
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
            $sql = "UPDATE questions SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $result = $this->database->prepare($sql, $params);

            if ($result > 0) {
                $this->successResponse(null, 'Question mise à jour avec succès');
            } else {
                $this->errorResponse('Aucune modification effectuée');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une question
     */
    public function destroy($id)
    {
        try {
            $result = $this->database->prepare(
                "DELETE FROM questions WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Question supprimée avec succès');
            } else {
                $this->errorResponse('Question non trouvée', 404);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les questions par interrogation
     */
    public function getByInterrogation($interrogation_id)
    {
        try {
            $questions = $this->database->select(
                "SELECT * FROM questions WHERE interrogation_id = ? ORDER BY ordre",
                [$interrogation_id]
            );
            
            $this->successResponse($questions, 'Questions de l\'interrogation récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les questions par type
     */
    public function getByType($type)
    {
        try {
            $questions = $this->database->select(
                "SELECT q.*, i.titre as interrogation_titre 
                 FROM questions q 
                 JOIN interrogations i ON q.interrogation_id = i.id 
                 WHERE q.type = ? 
                 ORDER BY q.interrogation_id, q.ordre",
                [$type]
            );
            
            $this->successResponse($questions, 'Questions du type récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour les réponses d'une question
     */
    public function updateReponses($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            if (!isset($input['options'])) {
                $this->errorResponse('Options requises', 422);
            }

            $result = $this->database->prepare(
                "UPDATE questions SET options = ? WHERE id = ?",
                [json_encode($input['options']), $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Réponses mises à jour avec succès');
            } else {
                $this->errorResponse('Question non trouvée', 404);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la mise à jour des réponses: ' . $e->getMessage());
        }
    }

    /**
     * Importer des questions depuis un fichier
     */
    public function importQuestions()
    {
        try {
            if (!isset($_FILES['file'])) {
                $this->errorResponse('Fichier requis', 422);
            }

            $file = $_FILES['file'];
            $interrogation_id = $_POST['interrogation_id'] ?? null;

            if (!$interrogation_id) {
                $this->errorResponse('ID d\'interrogation requis', 422);
            }

            // Vérifier le type de fichier
            $allowedTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            if (!in_array($file['type'], $allowedTypes)) {
                $this->errorResponse('Type de fichier non supporté', 422);
            }

            // Lire le fichier
            $questions = $this->parseQuestionsFile($file['tmp_name']);
            
            $questionsAdded = 0;
            foreach ($questions as $question) {
                $question['interrogation_id'] = $interrogation_id;
                
                $result = $this->database->prepare(
                    "INSERT INTO questions (interrogation_id, question, type, points, options, ordre) 
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        $question['interrogation_id'],
                        $question['question'],
                        $question['type'],
                        $question['points'] ?? 1,
                        json_encode($question['options'] ?? []),
                        $question['ordre'] ?? 1
                    ]
                );
                
                if ($result > 0) {
                    $questionsAdded++;
                }
            }

            $this->successResponse(['questions_added' => $questionsAdded], 'Questions importées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Parser un fichier de questions
     */
    private function parseQuestionsFile($filePath)
    {
        $questions = [];
        
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $headers = fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $question = [];
                foreach ($headers as $index => $header) {
                    $question[$header] = $data[$index] ?? '';
                }
                
                // Traitement des options selon le type
                if (isset($question['options'])) {
                    $options = explode('|', $question['options']);
                    $question['options'] = [
                        'choix' => $options,
                        'correcte' => $question['reponse_correcte'] ?? $options[0] ?? ''
                    ];
                }
                
                $questions[] = $question;
            }
            fclose($handle);
        }
        
        return $questions;
    }

    /**
     * Récupérer les statistiques des questions
     */
    public function getStats()
    {
        try {
            $stats = $this->database->select(
                "SELECT 
                    type,
                    COUNT(*) as nombre,
                    AVG(points) as points_moyens,
                    SUM(points) as points_totaux
                 FROM questions 
                 GROUP BY type"
            );
            
            $this->successResponse($stats, 'Statistiques des questions récupérées avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        }
    }
}
?> 