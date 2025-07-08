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
             
            }catch(\PDOException $e){
                echo "Connection failed: " . $e->getMessage();
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

        protected function query(string $request){
           if(strpos($request,'SELECT')  !== false){
              return  $this->pdo->query($request)->fetchAll(\PDO::FETCH_ASSOC);
                
           }else{
                $this->pdo->query($request);
           }
        }

        public function prepare(string $request, array $params){
            $stmt = $this->pdo->prepare($request);

            if(strpos($request,'SELECT')  !== false){
                $stmt->execute($params);
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }else{
                $stmt->execute($params);
                return $stmt->rowCount();
            }
        }
    }
?>