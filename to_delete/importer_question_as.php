<?php
session_start();
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_simple')  {
    header('Location: connexion.php');
    exit;
}

include 'databaseconnect.php';
require_once 'log_admin.php';
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Import Excel
if (isset($_POST['import_excel'])) {
    if ($_FILES['excel_file']['error'] === 0) {
        $filePath = $_FILES['excel_file']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $ligne_ignorees = 0;
            $ligne_importees = 0;

            foreach ($rows as $index => $row) {
                if ($index == 0) continue; // sauter l’en-tête

                // Nettoyage des données
                $row = array_map('trim', $row);

                // Vérification : on ignore si au moins une des 7 premières cases est vide
                if (count($row) < 7 || in_array(null, array_slice($row, 0, 7)) || in_array('', array_slice($row, 0, 7))) {
                    $ligne_ignorees++;
                    continue;
                }

                $stmt = $pdo->prepare("
                    INSERT INTO questions 
                    (texte_question, option_1, option_2, option_3, option_4, bonne_reponse, categorie)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6]
                ]);

                $ligne_importees++;
            }

            enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Import de questions", "Import réussi. $ligne_importees ligne(s) importée(s), $ligne_ignorees ignorée(s)");

            header("Location: question_admin_simple.php?import=ok&importees=$ligne_importees&ignorees=$ligne_ignorees");
            exit;

        } catch (Exception $e) {
            // Si une erreur plus grave survient
            enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Erreur d'import", "Détail : " . $e->getMessage());
            header("Location: question_admin_simple.php?import=echec");
            exit;
        }

    } else {
        header("Location: question_admin_simple.php?import=echec");
        exit;
    }
}
