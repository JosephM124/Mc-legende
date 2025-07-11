<?php 
 namespace Models;

  class Activity extends \Models\MysqlDatabase{
    private $id;
    private $user_id;
    private $ation;
    private $details;
    private $activity;
    private $datas ;

    public function __construct(\Config\Config $config)
    {
        parent::__construct($config);
        $this->datas = parent::query('SELECT   activites_admin.*, 
        utilisateurs.nom, utilisateurs.postnom , utilisateurs.prenom
        FROM  activites_admin INNER JOIN utilisateurs ON activites_admin.admin_id = utilisateurs.id
        ');
    }


    public function seeAllActivity(){
      return $this->datas;
    }
  }
?>