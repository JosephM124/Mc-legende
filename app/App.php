<?php 

  require dirname(__DIR__) . '/vendor/autoload.php';
  require dirname(__DIR__) . '/models/MysqlDatabase.php';
  require dirname(__DIR__) . '/config/mysql.php';
  
    
  class App
  {
    private static $db_instance;
    private static $config_instance;
    private static $mysql_instance;
    private static $router_instance;

    public static function getDbInstance(){
            if(is_null(static::$db_instance)){
                static::$db_instance = new Database(static::getConfigInstance());
            }
            return static::$db_instance;
    }

    public static function getConfigInstance(){
            if(is_null(static::$config_instance)){
                static::$config_instance = new Config();
            }
            return static::$config_instance;
    }

    public static function getMysqlDatabaseInstance(){
         if(is_null(static::$mysql_instance)){
            static::$mysql_instance = new MysqlDatabase(static::getConfigInstance());
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