<?php
    require dirname(__DIR__) . '/app/App.php';

    class Router{
       public static function get(string $uri, callable $callable){
         App::getRouterInstance()->map('GET',$uri,$callable);
       }
       public  static function post(string $uri, callable $callable){
         App::getRouterInstance()->map('POST', $uri, $callable);
       }
       public static function put(string $uri, callable $callable){
         App::getRouterInstance()->map('PUT', $uri, $callable);
       }
       public static function delete(string $uri, callable $callable){
         App::getRouterInstance()->map('DELETE', $uri, $callable);
       }

       public static function matcher(){
         $match = App::getRouterInstance()->match();
         if($match && is_callable($match['target'])){
           call_user_func_array($match['target'], $match['params']);
         } else {
           header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
           echo "404 Not Found";
         }
       }
    }
?>