<?php
namespace Models;

class Utilisateurs extends Database
{
    private $id;
    private $nom;
    private $email;
    private $mot_de_passe;
    private $role;
    private $date_inscription;
    private $reset_token;
    private $token_expiration;
    private $photo;
    private $telephone;
    private $postnom;
    private $prenom;
    private $sexe;
    private $naissance;
    private $inscription_complete;
    private $statut;

    private $datas;
    protected $table;

    public function __construct()
    {
        parent::__construct(\App\App::getConfigInstance());
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public function all()
    {
        return $this->datas = $this->select("SELECT * FROM {$this->table}");
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function find($id)
    {
        $result = $this->select(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function create($data)
    {
        // Vérifier si l'email existe déjà
        $existingUser = $this->select(
            "SELECT id FROM {$this->table} WHERE email = ?",
            [$data['email']]
        );

        if (!empty($existingUser)) {
            throw new \Exception('Un utilisateur avec cet email existe déjà');
        }

        // Hasher le mot de passe
        $hashedPassword = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);

        $result = $this->prepare(
            "INSERT INTO {$this->table} (nom, postnom, prenom, email, mot_de_passe, role, telephone, sexe, date_inscription, statut) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)",
            [
                $data['nom'] ?? '',
                $data['postnom'] ?? '',
                $data['prenom'] ?? '',
                $data['email'],
                $hashedPassword,
                $data['role'],
                $data['telephone'] ?? '',
                $data['sexe'] ?? '',
                $data['statut'] ?? 'active'
            ]
        );

        return $result > 0 ? $this->lastInsertId() : false;
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser($id, $data)
    {
        // Vérifier si l'utilisateur existe
        $existingUser = $this->select(
            "SELECT id FROM {$this->table} WHERE id = ?",
            [$id]
        );

        if (empty($existingUser)) {
            throw new \Exception('Utilisateur non trouvé');
        }

        $updateFields = [];
        $params = [];

        // Construire la requête de mise à jour dynamiquement
        $allowedFields = ['nom', 'postnom', 'prenom', 'email', 'telephone', 'sexe', 'role', 'statut', 'photo'];
        
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
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        return $this->prepare($sql, $params) > 0;
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($id)
    {
        return $this->prepare(
            "DELETE FROM {$this->table} WHERE id = ?",
            [$id]
        ) > 0;
    }

    /**
     * Authentifier un utilisateur
     */
    public function authenticate($email, $password)
    {
        $user = $this->select(
            "SELECT * FROM {$this->table} WHERE email = ? AND statut = 'active'",
            [$email]
        );

        if (!empty($user) && password_verify($password, $user[0]['mot_de_passe'])) {
            return $user[0];
        }

        return false;
    }

    /**
     * Récupérer les utilisateurs par rôle
     */
    public function getByRole($role)
    {
        return $this->select(
            "SELECT * FROM {$this->table} WHERE role = ? ORDER BY nom, prenom",
            [$role]
        );
    }

    /**
     * Récupérer un utilisateur par email
     */
    public function getByEmail($email)
    {
        $result = $this->select(
            "SELECT * FROM {$this->table} WHERE email = ?",
            [$email]
        );
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Récupérer les utilisateurs actifs
     */
    public function getActive()
    {
        return $this->select(
            "SELECT * FROM {$this->table} WHERE statut = 'active' ORDER BY nom, prenom"
        );
    }

    /**
     * Récupérer les utilisateurs inactifs
     */
    public function getInactive()
    {
        return $this->select(
            "SELECT * FROM {$this->table} WHERE statut = 'inactive' ORDER BY nom, prenom"
        );
    }

    /**
     * Rechercher des utilisateurs
     */
    public function search($term)
    {
        return $this->select(
            "SELECT * FROM {$this->table} 
             WHERE nom LIKE ? OR postnom LIKE ? OR prenom LIKE ? OR email LIKE ? 
             ORDER BY nom, prenom",
            ["%$term%", "%$term%", "%$term%", "%$term%"]
        );
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword($id, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        return $this->prepare(
            "UPDATE {$this->table} SET mot_de_passe = ? WHERE id = ?",
            [$hashedPassword, $id]
        ) > 0;
    }

    /**
     * Générer un token de réinitialisation
     */
    public function generateResetToken($email)
    {
        $user = $this->getByEmail($email);
        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $result = $this->prepare(
            "UPDATE {$this->table} SET reset_token = ?, token_expiration = ? WHERE id = ?",
            [$token, $expiration, $user['id']]
        );

        return $result > 0 ? $token : false;
    }

    /**
     * Vérifier un token de réinitialisation
     */
    public function verifyResetToken($email, $token)
    {
        $user = $this->select(
            "SELECT * FROM {$this->table} WHERE email = ? AND reset_token = ? AND token_expiration > NOW()",
            [$email, $token]
        );

        return !empty($user) ? $user[0] : false;
    }

    /**
     * Réinitialiser le mot de passe avec un token
     */
    public function resetPasswordWithToken($email, $token, $newPassword)
    {
        $user = $this->verifyResetToken($email, $token);
        if (!$user) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $result = $this->prepare(
            "UPDATE {$this->table} SET mot_de_passe = ?, reset_token = NULL, token_expiration = NULL WHERE id = ?",
            [$hashedPassword, $user['id']]
        );

        return $result > 0;
    }

    /**
     * Vérifier l'email d'un utilisateur
     */
    public function verifyEmail($id)
    {
        return $this->prepare(
            "UPDATE {$this->table} SET inscription_complete = 1 WHERE id = ?",
            [$id]
        ) > 0;
    }

    /**
     * Mettre à jour le statut d'un utilisateur
     */
    public function updateStatus($id, $status)
    {
        return $this->prepare(
            "UPDATE {$this->table} SET statut = ? WHERE id = ?",
            [$status, $id]
        ) > 0;
    }

    /**
     * Mettre à jour la photo de profil
     */
    public function updatePhoto($id, $photoPath)
    {
        return $this->prepare(
            "UPDATE {$this->table} SET photo = ? WHERE id = ?",
            [$photoPath, $id]
        ) > 0;
    }

    /**
     * Récupérer les statistiques des utilisateurs
     */
    public function getStats()
    {
        return $this->select(
            "SELECT 
                COUNT(*) as total_utilisateurs,
                COUNT(CASE WHEN statut = 'active' THEN 1 END) as utilisateurs_actifs,
                COUNT(CASE WHEN statut = 'inactive' THEN 1 END) as utilisateurs_inactifs,
                COUNT(CASE WHEN role = 'eleve' THEN 1 END) as eleves,
                COUNT(CASE WHEN role = 'admin_simple' THEN 1 END) as admins_simples,
                COUNT(CASE WHEN role = 'admin_principal' THEN 1 END) as admins_principaux
             FROM {$this->table}"
        );
    }

    /**
     * Récupérer les utilisateurs récents
     */
    public function getRecent($limit = 10)
    {
        return $this->select(
            "SELECT * FROM {$this->table} ORDER BY date_inscription DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Récupérer les utilisateurs par date d'inscription
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->select(
            "SELECT * FROM {$this->table} 
             WHERE date_inscription BETWEEN ? AND ? 
             ORDER BY date_inscription DESC",
            [$startDate, $endDate]
        );
    }

    /**
     * Récupérer les utilisateurs avec leurs activités
     */
    public function getWithActivities($user_id = null)
    {
        if ($user_id) {
            return $this->select(
                "SELECT u.*, 
                       COUNT(DISTINCT r.id) as nombre_resultats,
                       COUNT(DISTINCT a.id) as nombre_activites
                FROM {$this->table} u 
                LEFT JOIN resultats r ON u.id = r.eleve_id 
                LEFT JOIN activites_admin a ON u.id = a.admin_id 
                WHERE u.id = ?
                GROUP BY u.id",
                [$user_id]
            );
        } else {
            return $this->select(
                "SELECT u.*, 
                       COUNT(DISTINCT r.id) as nombre_resultats,
                       COUNT(DISTINCT a.id) as nombre_activites
                FROM {$this->table} u 
                LEFT JOIN resultats r ON u.id = r.eleve_id 
                LEFT JOIN activites_admin a ON u.id = a.admin_id 
                GROUP BY u.id 
                ORDER BY u.nom, u.prenom"
            );
        }
    }

    /**
     * Définir la table à utiliser
     */
    public function setTable(string $table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Récupérer le nom de la table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Vérifier si un email existe
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT id FROM {$this->table} WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->select($sql, $params);
        return !empty($result);
    }

    /**
     * Récupérer les utilisateurs par sexe
     */
    public function getBySexe($sexe)
    {
        return $this->select(
            "SELECT * FROM {$this->table} WHERE sexe = ? ORDER BY nom, prenom",
            [$sexe]
        );
    }

    /**
     * Récupérer les utilisateurs par téléphone
     */
    public function getByTelephone($telephone)
    {
        $result = $this->select(
            "SELECT * FROM {$this->table} WHERE telephone = ?",
            [$telephone]
        );
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Récupérer les utilisateurs avec inscription incomplète
     */
    public function getIncomplete()
    {
        return $this->select(
            "SELECT * FROM {$this->table} WHERE inscription_complete = 0 OR inscription_complete IS NULL"
        );
    }

    /**
     * Marquer l'inscription comme complète
     */
    public function markComplete($id)
    {
        return $this->prepare(
            "UPDATE {$this->table} SET inscription_complete = 1 WHERE id = ?",
            [$id]
        ) > 0;
    }
}
?>