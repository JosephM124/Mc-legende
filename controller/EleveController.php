<?php
namespace Controllers;


class EleveController extends BaseController
{
    private $eleve;

    public function __construct()
    {
        parent::__construct();
        $this->eleve = new \Models\Eleve();
    }

    /**
     * Récupérer tous les élèves
     */
    public function index()
    {
        try {
            $eleves = $this->database->select(
                "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
                 FROM eleves e 
                 JOIN utilisateurs u ON e.utilisateur_id = u.id"
            );
            $this->successResponse($eleves, 'Élèves récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des élèves: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer un élève par ID
     */
    public function show($id)
    {
        try {
            $eleve = $this->database->select(
                "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
                 FROM eleves e 
                 JOIN utilisateurs u ON e.utilisateur_id = u.id 
                 WHERE e.id = ?",
                [$id]
            );
            
            if (empty($eleve)) {
                $this->errorResponse('Élève non trouvé', 404);
            }
            
            $this->successResponse($eleve[0], 'Élève récupéré avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération de l\'élève: ' . $e->getMessage());
        }
    }

    /**
     * Créer un nouvel élève
     */
    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        // Validation des données
        $rules = [
            'utilisateur_id' => 'required',
            'etablissement' => 'required',
            'section' => 'required'
        ];

        $errors = $this->validateInput($input, $rules);
        if (!empty($errors)) {
            $this->errorResponse($errors, 422);
        }

        try {
            // Vérifier si l'utilisateur existe
            $utilisateur = $this->database->select(
                "SELECT id FROM utilisateurs WHERE id = ?",
                [$input['utilisateur_id']]
            );

            if (empty($utilisateur)) {
                $this->errorResponse('Utilisateur non trouvé', 404);
            }

            // Vérifier si l'élève existe déjà pour cet utilisateur
            $existingEleve = $this->database->select(
                "SELECT id FROM eleves WHERE utilisateur_id = ?",
                [$input['utilisateur_id']]
            );

            if (!empty($existingEleve)) {
                $this->errorResponse('Un élève existe déjà pour cet utilisateur', 409);
            }

            $result = $this->database->prepare(
                "INSERT INTO eleves (utilisateur_id, etablissement, section, adresse_ecole, categorie, pays, ville_province) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $input['utilisateur_id'],
                    $input['etablissement'],
                    $input['section'],
                    $input['adresse_ecole'] ?? '',
                    $input['categorie'] ?? '',
                    $input['pays'] ?? '',
                    $input['ville_province'] ?? ''
                ]
            );

            if ($result > 0) {
                $this->successResponse(['id' => $this->database->lastInsertId()], 'Élève créé avec succès');
            } else {
                $this->errorResponse('Erreur lors de la création de l\'élève');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la création de l\'élève: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour un élève
     */
    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            // Vérifier si l'élève existe
            $existingEleve = $this->database->select(
                "SELECT id FROM eleves WHERE id = ?",
                [$id]
            );

            if (empty($existingEleve)) {
                $this->errorResponse('Élève non trouvé', 404);
            }

            $updateFields = [];
            $params = [];

            // Construire la requête de mise à jour dynamiquement
            $allowedFields = ['etablissement', 'section', 'adresse_ecole', 'categorie', 'pays', 'ville_province'];
            
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
            $sql = "UPDATE eleves SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $result = $this->database->prepare($sql, $params);

            if ($result > 0) {
                $this->successResponse(null, 'Élève mis à jour avec succès');
            } else {
                $this->errorResponse('Aucune modification effectuée');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un élève
     */
    public function destroy($id)
    {
        try {
            $result = $this->database->prepare(
                "DELETE FROM eleves WHERE id = ?",
                [$id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Élève supprimé avec succès');
            } else {
                $this->errorResponse('Élève non trouvé', 404);
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les élèves par établissement
     */
    public function getByEtablissement($etablissement)
    {
        try {
            $eleves = $this->database->select(
                "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
                 FROM eleves e 
                 JOIN utilisateurs u ON e.utilisateur_id = u.id 
                 WHERE e.etablissement = ?",
                [$etablissement]
            );
            
            $this->successResponse($eleves, 'Élèves récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des élèves: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les élèves par section
     */
    public function getBySection($section)
    {
        try {
            $eleves = $this->database->select(
                "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
                 FROM eleves e 
                 JOIN utilisateurs u ON e.utilisateur_id = u.id 
                 WHERE e.section = ?",
                [$section]
            );
            
            $this->successResponse($eleves, 'Élèves récupérés avec succès');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de la récupération des élèves: ' . $e->getMessage());
        }
    }
}
?> 