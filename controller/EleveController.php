<?php
namespace Controllers;


class EleveController extends BaseController
{
    private $eleve;

    public function __construct()
    {
        parent::__construct();
        $this->requireRole('eleve');
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
     * Mettre à jour l'établissement d'un élève
     */
    public function updateEtablissement($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            if (empty($input['etablissement'])) {
                $this->errorResponse('Établissement requis', 422);
            }

            $result = $this->database->prepare(
                "UPDATE eleves SET etablissement = ? WHERE id = ?",
                [$input['etablissement'], $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Établissement mis à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour la section d'un élève
     */
    public function updateSection($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $input = $this->sanitizeInput($input);

        try {
            if (empty($input['section'])) {
                $this->errorResponse('Section requise', 422);
            }

            $result = $this->database->prepare(
                "UPDATE eleves SET section = ? WHERE id = ?",
                [$input['section'], $id]
            );

            if ($result > 0) {
                $this->successResponse(null, 'Section mise à jour avec succès');
            } else {
                $this->errorResponse('Erreur lors de la mise à jour');
            }
        } catch (\Exception $e) {
            $this->errorResponse('Erreur: ' . $e->getMessage());
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
     * Suppression douce d'un élève
     */
    public function softDelete($id)
    {
        try {
            $result = $this->database->prepare(
                "UPDATE eleves SET statut = 'deleted', date_suppression = NOW() WHERE id = ?",
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

    /**
     * Récupérer un élève par ID utilisateur
     */
    public function getByUtilisateurId($utilisateur_id)
    {
        try {
            $eleve = $this->database->select(
                "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
                 FROM eleves e 
                 JOIN utilisateurs u ON e.utilisateur_id = u.id 
                 WHERE e.utilisateur_id = ?",
                [$utilisateur_id]
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
     * Importer des élèves depuis Excel
     */
    public function importFromExcel()
    {
        try {
            if (!isset($_FILES['file'])) {
                $this->errorResponse('Aucun fichier fourni', 422);
            }

            $file = $_FILES['file'];

            // Vérifier le type de fichier
            $allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];
            if (!in_array($file['type'], $allowedTypes)) {
                $this->errorResponse('Type de fichier non supporté. Utilisez Excel ou CSV', 422);
            }

            // Utiliser la méthode du modèle Eleve
            $imported = $this->eleve->importFromExcel($file['tmp_name']);

            $this->successResponse(['imported_count' => $imported], 'Import des élèves réussi');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'import: ' . $e->getMessage());
        }
    }
}
?> 