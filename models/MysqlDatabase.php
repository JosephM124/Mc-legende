<?php 
   require './Database.php';
  class MysqlDatabase extends Database{
 
  
    public function __construct(Config $config)
    {
        parent::__construct($config);
         
    }

    public function select(string $request, $params = []){
        if(empty($params)){
          return  parent::query($request);
          
        }
        else{
           return parent::prepare($request, $params);
        }
    }



  }

?>