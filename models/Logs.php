<?php 
namespace Models;



    class Logs extends \Models\MysqlDatabase
    {
	   private int $id ;			
	   private $user_id ;	
       private string $action;
	   private string $description;
	   private string  $ip_address;
	   private \DateTime $date_creation;
	   private array $datas;

    }

?>