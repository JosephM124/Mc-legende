<?php
namespace Controllers;

class ImportController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Importer des élèves depuis un fichier Excel
     */
    public function importEleves()
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

            // Vérifier la taille (max 10MB)
            if ($file['size'] > 10 * 1024 * 1024) {
                $this->errorResponse('Fichier trop volumineux. Maximum 10MB', 422);
            }

            // Créer le modèle Eleve
            $eleveModel = new \Models\Eleve();

            // Importer les données
            $imported = $eleveModel->importFromExcel($file['tmp_name']);

            $this->successResponse(['imported_count' => $imported], 'Import des élèves réussi');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Importer des questions depuis un fichier Excel
     */
    public function importQuestions()
    {
        try {
            if (!isset($_FILES['file'])) {
                $this->errorResponse('Aucun fichier fourni', 422);
            }

            $file = $_FILES['file'];
            $interrogation_id = $_POST['interrogation_id'] ?? null;

            if (!$interrogation_id) {
                $this->errorResponse('ID d\'interrogation requis', 422);
            }

            // Vérifier le type de fichier
            $allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];
            if (!in_array($file['type'], $allowedTypes)) {
                $this->errorResponse('Type de fichier non supporté. Utilisez Excel ou CSV', 422);
            }

            // Vérifier la taille (max 10MB)
            if ($file['size'] > 10 * 1024 * 1024) {
                $this->errorResponse('Fichier trop volumineux. Maximum 10MB', 422);
            }

            // Lire le fichier Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Supprimer l'en-tête
            $headers = array_shift($rows);

            $this->database->beginTransaction();
            $imported = 0;

            foreach ($rows as $row) {
                $data = array_combine($headers, $row);
                
                // Récupérer l'ordre de la dernière question
                $lastOrder = $this->database->select(
                    "SELECT MAX(ordre) as max_ordre FROM questions WHERE interrogation_id = ?",
                    [$interrogation_id]
                );
                
                $ordre = ($lastOrder[0]['max_ordre'] ?? 0) + 1;

                // Créer la question
                $result = $this->database->prepare(
                    "INSERT INTO questions (interrogation_id, question, type, points, options, ordre, temps_estime) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $interrogation_id,
                        $data['question'] ?? '',
                        $data['type'] ?? 'choix_unique',
                        $data['points'] ?? 1,
                        json_encode($data['options'] ?? []),
                        $ordre,
                        $data['temps_estime'] ?? 60
                    ]
                );

                if ($result > 0) {
                    $imported++;
                }
            }

            $this->database->commit();

            $this->successResponse(['imported_count' => $imported], 'Import des questions réussi');
        } catch (\Exception $e) {
            $this->database->rollback();
            $this->errorResponse('Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Importer des résultats depuis un fichier Excel
     */
    public function importResultats()
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

            // Vérifier la taille (max 10MB)
            if ($file['size'] > 10 * 1024 * 1024) {
                $this->errorResponse('Fichier trop volumineux. Maximum 10MB', 422);
            }

            // Lire le fichier Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Supprimer l'en-tête
            $headers = array_shift($rows);

            $this->database->beginTransaction();
            $imported = 0;

            foreach ($rows as $row) {
                $data = array_combine($headers, $row);
                
                // Vérifier si l'élève existe
                $eleve = $this->database->select(
                    "SELECT id FROM eleves WHERE id = ?",
                    [$data['eleve_id'] ?? 0]
                );

                if (empty($eleve)) {
                    continue; // Ignorer cette ligne
                }

                // Vérifier si l'interrogation existe
                $interrogation = $this->database->select(
                    "SELECT id FROM interrogations WHERE id = ?",
                    [$data['interrogation_id'] ?? 0]
                );

                if (empty($interrogation)) {
                    continue; // Ignorer cette ligne
                }

                // Vérifier si un résultat existe déjà
                $existingResult = $this->database->select(
                    "SELECT id FROM resultats WHERE eleve_id = ? AND interrogation_id = ?",
                    [$data['eleve_id'], $data['interrogation_id']]
                );

                if (!empty($existingResult)) {
                    continue; // Ignorer cette ligne
                }

                // Créer le résultat
                $result = $this->database->prepare(
                    "INSERT INTO resultats (eleve_id, interrogation_id, score, temps_utilise, reponses, date_soumission) 
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        $data['eleve_id'],
                        $data['interrogation_id'],
                        $data['score'] ?? 0,
                        $data['temps_utilise'] ?? 0,
                        json_encode($data['reponses'] ?? []),
                        $data['date_soumission'] ?? date('Y-m-d H:i:s')
                    ]
                );

                if ($result > 0) {
                    $imported++;
                }
            }

            $this->database->commit();

            $this->successResponse(['imported_count' => $imported], 'Import des résultats réussi');
        } catch (\Exception $e) {
            $this->database->rollback();
            $this->errorResponse('Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Importer des utilisateurs depuis un fichier Excel
     */
    public function importUtilisateurs()
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

            // Vérifier la taille (max 10MB)
            if ($file['size'] > 10 * 1024 * 1024) {
                $this->errorResponse('Fichier trop volumineux. Maximum 10MB', 422);
            }

            // Lire le fichier Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Supprimer l'en-tête
            $headers = array_shift($rows);

            $this->database->beginTransaction();
            $imported = 0;

            foreach ($rows as $row) {
                $data = array_combine($headers, $row);
                
                // Vérifier si l'email existe déjà
                $existingUser = $this->database->select(
                    "SELECT id FROM utilisateurs WHERE email = ?",
                    [$data['email'] ?? '']
                );

                if (!empty($existingUser)) {
                    continue; // Ignorer cette ligne
                }

                // Hasher le mot de passe
                $hashedPassword = password_hash($data['mot_de_passe'] ?? 'password123', PASSWORD_DEFAULT);

                // Créer l'utilisateur
                $result = $this->database->prepare(
                    "INSERT INTO utilisateurs (nom, postnom, prenom, email, mot_de_passe, role, telephone, sexe, date_inscription, statut) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)",
                    [
                        $data['nom'] ?? '',
                        $data['postnom'] ?? '',
                        $data['prenom'] ?? '',
                        $data['email'] ?? '',
                        $hashedPassword,
                        $data['role'] ?? 'eleve',
                        $data['telephone'] ?? '',
                        $data['sexe'] ?? '',
                        $data['statut'] ?? 'active'
                    ]
                );

                if ($result > 0) {
                    $imported++;
                }
            }

            $this->database->commit();

            $this->successResponse(['imported_count' => $imported], 'Import des utilisateurs réussi');
        } catch (\Exception $e) {
            $this->database->rollback();
            $this->errorResponse('Erreur lors de l\'import: ' . $e->getMessage());
        }
    }
}
?> 