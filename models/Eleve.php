<?php
namespace Models;

class Eleve extends Database
{
    private $id;
    private $utilisateur_id;
    private $etablissement;
    private $section;
    private $adresse_ecole;
    private $categorie;
    private $pays;
    private $ville_province;

    public function __construct()
    {
        parent::__construct(\App\App::getConfigInstance());
    }

    /**
     * Récupérer tous les élèves
     */
    public function all()
    {
        return $this->select(
            "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             ORDER BY u.nom, u.prenom"
        );
    }

    /**
     * Récupérer un élève par ID
     */
    public function find($id)
    {
        $result = $this->select(
            "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             WHERE e.id = ?",
            [$id]
        );
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Créer un nouvel élève
     */
    public function create($data)
    {
        // Vérifier si l'utilisateur existe
        $utilisateur = $this->select(
            "SELECT id FROM utilisateurs WHERE id = ?",
            [$data['utilisateur_id']]
        );

        if (empty($utilisateur)) {
            throw new \Exception('Utilisateur non trouvé');
        }

        // Vérifier si l'élève existe déjà pour cet utilisateur
        $existingEleve = $this->select(
            "SELECT id FROM eleves WHERE utilisateur_id = ?",
            [$data['utilisateur_id']]
        );

        if (!empty($existingEleve)) {
            throw new \Exception('Un élève existe déjà pour cet utilisateur');
        }

        $result = $this->prepare(
            "INSERT INTO eleves (utilisateur_id, etablissement, section, adresse_ecole, categorie, pays, ville_province) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['utilisateur_id'],
                $data['etablissement'],
                $data['section'],
                $data['adresse_ecole'] ?? '',
                $data['categorie'] ?? '',
                $data['pays'] ?? '',
                $data['ville_province'] ?? ''
            ]
        );

        return $result > 0 ? $this->lastInsertId() : false;
    }

    /**
     * Mettre à jour un élève
     */
    public function updateEleve($id, $data)
    {
        // Vérifier si l'élève existe
        $existingEleve = $this->select(
            "SELECT id FROM eleves WHERE id = ?",
            [$id]
        );

        if (empty($existingEleve)) {
            throw new \Exception('Élève non trouvé');
        }

        $updateFields = [];
        $params = [];

        // Construire la requête de mise à jour dynamiquement
        $allowedFields = ['etablissement', 'section', 'adresse_ecole', 'categorie', 'pays', 'ville_province'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($updateFields)) {
            throw new \Exception('Aucune donnée à mettre à jour');
        }

        $params[] = $id;
        $sql = "UPDATE eleves SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        return $this->prepare($sql, $params) > 0;
    }

    /**
     * Supprimer un élève
     */
    public function deleteEleve($id)
    {
        return $this->prepare(
            "DELETE FROM eleves WHERE id = ?",
            [$id]
        ) > 0;
    }

    /**
     * Récupérer les élèves par établissement
     */
    public function getByEtablissement($etablissement)
    {
        return $this->select(
            "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             WHERE e.etablissement = ? 
             ORDER BY u.nom, u.prenom",
            [$etablissement]
        );
    }

    /**
     * Récupérer les élèves par section
     */
    public function getBySection($section)
    {
        return $this->select(
            "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             WHERE e.section = ? 
             ORDER BY u.nom, u.prenom",
            [$section]
        );
    }

    /**
     * Récupérer les élèves par utilisateur ID
     */
    public function getByUtilisateurId($utilisateur_id)
    {
        $result = $this->select(
            "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             WHERE e.utilisateur_id = ?",
            [$utilisateur_id]
        );
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Récupérer les élèves par pays
     */
    public function getByPays($pays)
    {
        return $this->select(
            "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             WHERE e.pays = ? 
             ORDER BY u.nom, u.prenom",
            [$pays]
        );
    }

    /**
     * Récupérer les élèves par ville/province
     */
    public function getByVille($ville_province)
    {
        return $this->select(
            "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             WHERE e.ville_province = ? 
             ORDER BY u.nom, u.prenom",
            [$ville_province]
        );
    }

    /**
     * Rechercher des élèves
     */
    public function search($term)
    {
        return $this->select(
            "SELECT e.*, u.nom, u.prenom, u.email, u.telephone, u.sexe 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             WHERE u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? 
                OR e.etablissement LIKE ? OR e.section LIKE ? 
             ORDER BY u.nom, u.prenom",
            ["%$term%", "%$term%", "%$term%", "%$term%", "%$term%"]
        );
    }

    /**
     * Récupérer les statistiques des élèves
     */
    public function getStats()
    {
        return $this->select(
            "SELECT 
                COUNT(*) as total_eleves,
                COUNT(DISTINCT etablissement) as nombre_etablissements,
                COUNT(DISTINCT section) as nombre_sections,
                COUNT(DISTINCT pays) as nombre_pays,
                COUNT(DISTINCT ville_province) as nombre_villes
             FROM eleves"
        );
    }

    /**
     * Récupérer les statistiques par établissement
     */
    public function getStatsByEtablissement()
    {
        return $this->select(
            "SELECT 
                etablissement,
                COUNT(*) as nombre_eleves,
                COUNT(DISTINCT section) as nombre_sections
             FROM eleves 
             GROUP BY etablissement 
             ORDER BY nombre_eleves DESC"
        );
    }

    /**
     * Récupérer les statistiques par section
     */
    public function getStatsBySection()
    {
        return $this->select(
            "SELECT 
                section,
                COUNT(*) as nombre_eleves,
                COUNT(DISTINCT etablissement) as nombre_etablissements
             FROM eleves 
             GROUP BY section 
             ORDER BY nombre_eleves DESC"
        );
    }

    /**
     * Récupérer les élèves avec leurs résultats
     */
    public function getWithResults($eleve_id = null)
    {
        if ($eleve_id) {
            return $this->select(
                "SELECT e.*, u.nom, u.prenom, u.email,
                       COUNT(r.id) as nombre_resultats,
                       AVG(r.score) as score_moyen,
                       MAX(r.score) as score_max,
                       MIN(r.score) as score_min
                FROM eleves e 
                JOIN utilisateurs u ON e.utilisateur_id = u.id 
                LEFT JOIN resultats r ON e.id = r.eleve_id 
                WHERE e.id = ?
                GROUP BY e.id",
                [$eleve_id]
            );
        } else {
            return $this->select(
                "SELECT e.*, u.nom, u.prenom, u.email,
                       COUNT(r.id) as nombre_resultats,
                       AVG(r.score) as score_moyen,
                       MAX(r.score) as score_max,
                       MIN(r.score) as score_min
                FROM eleves e 
                JOIN utilisateurs u ON e.utilisateur_id = u.id 
                LEFT JOIN resultats r ON e.id = r.eleve_id 
                GROUP BY e.id 
                ORDER BY u.nom, u.prenom"
            );
        }
    }

    /**
     * Récupérer les élèves récents
     */
    public function getRecent($limit = 10)
    {
        return $this->select(
            "SELECT e.*, u.nom, u.prenom, u.email, u.date_inscription 
             FROM eleves e 
             JOIN utilisateurs u ON e.utilisateur_id = u.id 
             ORDER BY u.date_inscription DESC 
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Mettre à jour l'établissement d'un élève
     */
    public function updateEtablissement($id, $etablissement)
    {
        return $this->prepare(
            "UPDATE eleves SET etablissement = ? WHERE id = ?",
            [$etablissement, $id]
        ) > 0;
    }

    /**
     * Mettre à jour la section d'un élève
     */
    public function updateSection($id, $section)
    {
        return $this->prepare(
            "UPDATE eleves SET section = ? WHERE id = ?",
            [$section, $id]
        ) > 0;
    }

    /**
     * Importer des élèves depuis un fichier Excel
     */
    public function importFromExcel($filename)
    {
        try {
            // Utiliser PhpSpreadsheet pour lire le fichier Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filename);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Supprimer l'en-tête
            $headers = array_shift($rows);

            $this->beginTransaction();
            $imported = 0;

            foreach ($rows as $row) {
                $data = array_combine($headers, $row);
                
                // Créer d'abord l'utilisateur
                $utilisateurData = [
                    'nom' => $data['nom'] ?? '',
                    'postnom' => $data['postnom'] ?? '',
                    'prenom' => $data['prenom'] ?? '',
                    'email' => $data['email'] ?? '',
                    'mot_de_passe' => password_hash($data['mot_de_passe'] ?? 'password123', PASSWORD_DEFAULT),
                    'role' => 'eleve',
                    'telephone' => $data['telephone'] ?? '',
                    'sexe' => $data['sexe'] ?? ''
                ];

                $utilisateurId = $this->createUtilisateur($utilisateurData);

                if ($utilisateurId) {
                    // Créer l'élève
                    $eleveData = [
                        'utilisateur_id' => $utilisateurId,
                        'etablissement' => $data['etablissement'] ?? '',
                        'section' => $data['section'] ?? '',
                        'adresse_ecole' => $data['adresse_ecole'] ?? '',
                        'categorie' => $data['categorie'] ?? '',
                        'pays' => $data['pays'] ?? '',
                        'ville_province' => $data['ville_province'] ?? ''
                    ];

                    if ($this->create($eleveData)) {
                        $imported++;
                    }
                }
            }

            $this->commit();
            return $imported;
        } catch (\Exception $e) {
            $this->rollback();
            throw new \Exception("Import error: " . $e->getMessage());
        }
    }

    /**
     * Créer un utilisateur (méthode privée pour l'import)
     */
    private function createUtilisateur($data)
    {
        $result = $this->prepare(
            "INSERT INTO utilisateurs (nom, postnom, prenom, email, mot_de_passe, role, telephone, sexe, date_inscription) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                $data['nom'],
                $data['postnom'],
                $data['prenom'],
                $data['email'],
                $data['mot_de_passe'],
                $data['role'],
                $data['telephone'],
                $data['sexe']
            ]
        );

        return $result > 0 ? $this->lastInsertId() : false;
    }
}
?>