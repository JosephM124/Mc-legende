<?php 
 namespace Models;

    class Utilisateurs 
    {
        
        private $id;
        private string $nom;
        private $email;
        private $mot_de_passe;
        private $role;
        private $date_inscription;
        private $reset_token;
        private $token_expiration;
        private $photo;
        private $telephone;
        private string $postnom;
        private string $prenom;
        private $sexe;
        private $naissance;
        private $inscrption_complete;
        private $statut; 

        private $datas;
        protected $table;


        public function all(){
          return  $this->datas = \App\App::getMysqlDatabaseInstance()->select("SELECT * FROM {$this->table}");
        }


        public function setTable(string $table)
        {
            $this->table = $table;
            return $this;
        }
     
    }

?>