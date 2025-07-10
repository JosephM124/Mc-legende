<?php
namespace Models;

class Database
{
    protected $pdo;
    protected $hostname;
    protected $dbname;
    protected $username;
    protected $password;

    public function __construct(\Config\Config $config) {
        $this->hostname = $config->gethost();
        $this->dbname = $config->getdb();
        $this->username = $config->getuser();
        $this->password = $config->getpassword();

        try{
            $this->pdo = new \PDO('mysql:host=' . $this->hostname . ';dbname=' . $this->dbname, $this->username, $this->password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch(\PDOException $e){
            throw new \Exception("Connection failed: " . $e->getMessage());
        }
    }

    public function setdbname($db)
    {
        $this->dbname = $db;
    }

    public function setusername($user)
    {
        $this->username = $user;
    }

    public function setpassword($pass)
    {
        $this->password = $pass; 
    }

    public function getdbname(){
        return $this->dbname;
    }

    public function getusername(){
        return $this->username;
    }

    /**
     * Exécuter une requête SQL simple
     */
    protected function query(string $request){
        // AVERTISSEMENT : N'utilisez jamais cette méthode avec des entrées utilisateur !
        if (preg_match('/[?]/', $request)) {
            throw new \Exception("N'utilisez pas query() avec des variables utilisateur. Utilisez prepare().");
        }
        try {
            if(strpos($request,'SELECT') !== false){
                return $this->pdo->query($request)->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return $this->pdo->query($request);
            }
        } catch(\PDOException $e) {
            throw new \Exception("Query error: " . $e->getMessage());
        }
    }

    /**
     * Exécuter une requête préparée
     */
    public function prepare(string $request, array $params = []){
        try {
            $stmt = $this->pdo->prepare($request);
            $stmt->execute($params);

            if(strpos($request,'SELECT') !== false){
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                return $stmt->rowCount();
            }
        } catch(\PDOException $e) {
            throw new \Exception("Prepare error: " . $e->getMessage());
        }
    }

    /**
     * Méthode select pour compatibilité
     */
    public function select(string $request, array $params = []){
        return $this->prepare($request, $params);
    }

    /**
     * Méthode insert pour compatibilité
     */
    public function insert(string $request, array $params = []){
        return $this->prepare($request, $params);
    }

    /**
     * Méthode update pour compatibilité
     */
    public function update(string $request, array $params = []){
        return $this->prepare($request, $params);
    }

    /**
     * Méthode delete pour compatibilité
     */
    public function delete(string $request, array $params = []){
        return $this->prepare($request, $params);
    }

    /**
     * Récupérer l'ID de la dernière insertion
     */
    public function lastInsertId(){
        return $this->pdo->lastInsertId();
    }

    /**
     * Commencer une transaction
     */
    public function beginTransaction(){
        return $this->pdo->beginTransaction();
    }

    /**
     * Valider une transaction
     */
    public function commit(){
        return $this->pdo->commit();
    }

    /**
     * Annuler une transaction
     */
    public function rollback(){
        return $this->pdo->rollback();
    }

    /**
     * Vérifier si une transaction est active
     */
    public function inTransaction(){
        return $this->pdo->inTransaction();
    }

    /**
     * Échapper une chaîne de caractères
     */
    public function quote($string){
        return $this->pdo->quote($string);
    }

    /**
     * Récupérer les informations sur les colonnes d'une table
     */
    public function getTableColumns($table){
        // Validation du nom de table (lettres, chiffres, _ uniquement)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new \Exception("Nom de table invalide");
        }
        try {
            $stmt = $this->pdo->prepare("DESCRIBE $table");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            throw new \Exception("Table columns error: " . $e->getMessage());
        }
    }

    /**
     * Vérifier si une table existe
     */
    public function tableExists($table){
        try {
            $stmt = $this->pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            return $stmt->rowCount() > 0;
        } catch(\PDOException $e) {
            return false;
        }
    }

    /**
     * Récupérer le nombre de lignes affectées
     */
    public function rowCount(){
        return $this->pdo->query("SELECT ROW_COUNT()")->fetchColumn();
    }
}
?>