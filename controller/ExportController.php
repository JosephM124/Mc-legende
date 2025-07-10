<?php
namespace Controllers;

class ExportController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exporter les élèves en Excel
     */
    public function exportEleves()
    {
        try {
            $eleves = $this->database->select(
                "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe, u.date_inscription 
                 FROM eleves e 
                 JOIN utilisateurs u ON e.utilisateur_id = u.id 
                 ORDER BY u.nom, u.prenom"
            );

            if (empty($eleves)) {
                $this->errorResponse('Aucun élève à exporter');
            }

            // Créer le fichier Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // En-têtes
            $headers = ['ID', 'Nom', 'Prénom', 'Email', 'Téléphone', 'Sexe', 'Établissement', 'Section', 'Pays', 'Ville', 'Date d\'inscription'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            // Données
            $row = 2;
            foreach ($eleves as $eleve) {
                $sheet->setCellValue('A' . $row, $eleve['id']);
                $sheet->setCellValue('B' . $row, $eleve['nom']);
                $sheet->setCellValue('C' . $row, $eleve['prenom']);
                $sheet->setCellValue('D' . $row, $eleve['email']);
                $sheet->setCellValue('E' . $row, $eleve['telephone']);
                $sheet->setCellValue('F' . $row, $eleve['sexe']);
                $sheet->setCellValue('G' . $row, $eleve['etablissement']);
                $sheet->setCellValue('H' . $row, $eleve['section']);
                $sheet->setCellValue('I' . $row, $eleve['pays']);
                $sheet->setCellValue('J' . $row, $eleve['ville_province']);
                $sheet->setCellValue('K' . $row, $eleve['date_inscription']);
                $row++;
            }

            // Auto-dimensionner les colonnes
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Créer le fichier
            $filename = 'eleves_' . date('Y-m-d_H-i-s') . '.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filepath = 'uploads/exports/' . $filename;
            
            if (!is_dir('uploads/exports')) {
                mkdir('uploads/exports', 0777, true);
            }
            
            $writer->save($filepath);

            $this->successResponse(['filename' => $filename, 'filepath' => $filepath], 'Export des élèves réussi');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Exporter les résultats en Excel
     */
    public function exportResultats()
    {
        try {
            $resultats = $this->database->select(
                "SELECT r.*, e.nom as eleve_nom, e.prenom as eleve_prenom, e.etablissement,
                        i.titre as interrogation_titre, i.matiere
                 FROM resultats r 
                 JOIN eleves e ON r.eleve_id = e.id 
                 JOIN interrogations i ON r.interrogation_id = i.id 
                 ORDER BY r.date_soumission DESC"
            );

            if (empty($resultats)) {
                $this->errorResponse('Aucun résultat à exporter');
            }

            // Créer le fichier Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // En-têtes
            $headers = ['ID', 'Élève', 'Interrogation', 'Matière', 'Score', 'Temps utilisé', 'Date de soumission', 'Établissement'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            // Données
            $row = 2;
            foreach ($resultats as $resultat) {
                $sheet->setCellValue('A' . $row, $resultat['id']);
                $sheet->setCellValue('B' . $row, $resultat['eleve_nom'] . ' ' . $resultat['eleve_prenom']);
                $sheet->setCellValue('C' . $row, $resultat['interrogation_titre']);
                $sheet->setCellValue('D' . $row, $resultat['matiere']);
                $sheet->setCellValue('E' . $row, $resultat['score']);
                $sheet->setCellValue('F' . $row, $resultat['temps_utilise']);
                $sheet->setCellValue('G' . $row, $resultat['date_soumission']);
                $sheet->setCellValue('H' . $row, $resultat['etablissement']);
                $row++;
            }

            // Auto-dimensionner les colonnes
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Créer le fichier
            $filename = 'resultats_' . date('Y-m-d_H-i-s') . '.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filepath = 'uploads/exports/' . $filename;
            
            if (!is_dir('uploads/exports')) {
                mkdir('uploads/exports', 0777, true);
            }
            
            $writer->save($filepath);

            $this->successResponse(['filename' => $filename, 'filepath' => $filepath], 'Export des résultats réussi');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Exporter les activités en Excel
     */
    public function exportActivites()
    {
        try {
            $activites = $this->database->select(
                "SELECT a.*, u.nom, u.prenom, u.email 
                 FROM activites_admin a 
                 JOIN utilisateurs u ON a.admin_id = u.id 
                 ORDER BY a.date_activite DESC"
            );

            if (empty($activites)) {
                $this->errorResponse('Aucune activité à exporter');
            }

            // Créer le fichier Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // En-têtes
            $headers = ['ID', 'Admin', 'Action', 'Description', 'Date d\'activité'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            // Données
            $row = 2;
            foreach ($activites as $activite) {
                $sheet->setCellValue('A' . $row, $activite['id']);
                $sheet->setCellValue('B' . $row, $activite['nom'] . ' ' . $activite['prenom']);
                $sheet->setCellValue('C' . $row, $activite['action']);
                $sheet->setCellValue('D' . $row, $activite['details']);
                $sheet->setCellValue('E' . $row, $activite['date_activite']);
                $row++;
            }

            // Auto-dimensionner les colonnes
            foreach (range('A', 'E') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Créer le fichier
            $filename = 'activites_' . date('Y-m-d_H-i-s') . '.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filepath = 'uploads/exports/' . $filename;
            
            if (!is_dir('uploads/exports')) {
                mkdir('uploads/exports', 0777, true);
            }
            
            $writer->save($filepath);

            $this->successResponse(['filename' => $filename, 'filepath' => $filepath], 'Export des activités réussi');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Exporter les interrogations en Excel
     */
    public function exportInterrogations()
    {
        try {
            $interrogations = $this->database->select(
                "SELECT i.*, u.nom, u.prenom, COUNT(q.id) as nombre_questions 
                 FROM interrogations i 
                 LEFT JOIN questions q ON i.id = q.interrogation_id 
                 LEFT JOIN utilisateurs u ON i.created_by = u.id 
                 GROUP BY i.id 
                 ORDER BY i.date_creation DESC"
            );

            if (empty($interrogations)) {
                $this->errorResponse('Aucune interrogation à exporter');
            }

            // Créer le fichier Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // En-têtes
            $headers = ['ID', 'Titre', 'Description', 'Matière', 'Niveau', 'Durée', 'Statut', 'Créateur', 'Nombre de questions', 'Date de création'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            // Données
            $row = 2;
            foreach ($interrogations as $interrogation) {
                $sheet->setCellValue('A' . $row, $interrogation['id']);
                $sheet->setCellValue('B' . $row, $interrogation['titre']);
                $sheet->setCellValue('C' . $row, $interrogation['description']);
                $sheet->setCellValue('D' . $row, $interrogation['matiere']);
                $sheet->setCellValue('E' . $row, $interrogation['niveau']);
                $sheet->setCellValue('F' . $row, $interrogation['duree']);
                $sheet->setCellValue('G' . $row, $interrogation['statut']);
                $sheet->setCellValue('H' . $row, $interrogation['nom'] . ' ' . $interrogation['prenom']);
                $sheet->setCellValue('I' . $row, $interrogation['nombre_questions']);
                $sheet->setCellValue('J' . $row, $interrogation['date_creation']);
                $row++;
            }

            // Auto-dimensionner les colonnes
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Créer le fichier
            $filename = 'interrogations_' . date('Y-m-d_H-i-s') . '.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filepath = 'uploads/exports/' . $filename;
            
            if (!is_dir('uploads/exports')) {
                mkdir('uploads/exports', 0777, true);
            }
            
            $writer->save($filepath);

            $this->successResponse(['filename' => $filename, 'filepath' => $filepath], 'Export des interrogations réussi');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Exporter les statistiques en PDF
     */
    public function exportStatsPDF()
    {
        try {
            // Récupérer les statistiques
            $stats = $this->database->select(
                "SELECT 
                    (SELECT COUNT(*) FROM utilisateurs) as total_utilisateurs,
                    (SELECT COUNT(*) FROM eleves) as total_eleves,
                    (SELECT COUNT(*) FROM interrogations) as total_interrogations,
                    (SELECT COUNT(*) FROM resultats) as total_resultats,
                    (SELECT AVG(score) FROM resultats) as score_moyen"
            );

            if (empty($stats)) {
                $this->errorResponse('Aucune statistique à exporter');
            }

            $stat = $stats[0];

            // Créer le contenu HTML
            $html = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .stats { margin: 20px 0; }
                    .stat-item { margin: 10px 0; padding: 10px; background-color: #f5f5f5; }
                    .stat-label { font-weight: bold; }
                    .stat-value { color: #007bff; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Rapport Statistiques MC-LEGENDE</h1>
                    <p>Généré le ' . date('d/m/Y à H:i') . '</p>
                </div>
                
                <div class="stats">
                    <div class="stat-item">
                        <span class="stat-label">Total Utilisateurs:</span>
                        <span class="stat-value">' . $stat['total_utilisateurs'] . '</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Élèves:</span>
                        <span class="stat-value">' . $stat['total_eleves'] . '</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Interrogations:</span>
                        <span class="stat-value">' . $stat['total_interrogations'] . '</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Résultats:</span>
                        <span class="stat-value">' . $stat['total_resultats'] . '</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Score Moyen:</span>
                        <span class="stat-value">' . round($stat['score_moyen'], 2) . '%</span>
                    </div>
                </div>
            </body>
            </html>';

            // Créer le fichier PDF
            $filename = 'statistiques_' . date('Y-m-d_H-i-s') . '.pdf';
            $filepath = 'uploads/exports/' . $filename;
            
            if (!is_dir('uploads/exports')) {
                mkdir('uploads/exports', 0777, true);
            }

            // Utiliser Dompdf pour créer le PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream($filepath, ['Attachment' => false]);

            $this->successResponse(['filename' => $filename, 'filepath' => $filepath], 'Export des statistiques réussi');
        } catch (\Exception $e) {
            $this->errorResponse('Erreur lors de l\'export: ' . $e->getMessage());
        }
    }
}
?> 