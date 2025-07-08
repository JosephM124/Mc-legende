<?php 


  namespace App;
  use AltoRouter;
    
  class App
  {
    private static $db_instance;
    private static $config_instance;
    private static $mysql_instance;
    private static $router_instance;

    public static function getDbInstance(){
            if(is_null(static::$db_instance)){
                static::$db_instance = new \Models\Database(static::getConfigInstance());
            }
            return static::$db_instance;
    }

    public static function getConfigInstance(){
            if(is_null(static::$config_instance)){
                static::$config_instance = new \Config\Config();
            }
            return static::$config_instance;
    }

    public static function getMysqlDatabaseInstance(){
         if(is_null(static::$mysql_instance)){
            static::$mysql_instance = new \Models\MysqlDatabase(static::getConfigInstance());
         }
          return static::$mysql_instance;
    }
    public static function getRouterInstance(){
        if(is_null(static::$router_instance)){
            static::$router_instance = new AltoRouter();
        }
        return static::$router_instance;
    }
  }
?>