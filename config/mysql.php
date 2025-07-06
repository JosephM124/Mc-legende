<?php

const MYSQL_HOST = 'localhost:3306';
// const MYSQL_PORT = 3306;
const MYSQL_NAME = 'mc-legende';
const MYSQL_USER = 'root';
const MYSQL_PASSWORD = '';


class Config{
    private $hostname = 'localhost:3306';
    private $dbname = 'mc-legende';
    private $username = 'root';
    private $password = '';
    
  
    public function gethost(){
        return $this->hostname;
    }
    public function getdb(){
        return $this->dbname;
    }
    public function getuser(){
        return $this->username;
    }

    public function getpassword(){
        return $this->password;
    
    }
}

?>
