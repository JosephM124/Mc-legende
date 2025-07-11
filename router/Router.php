<?php
   
   namespace Router;

    class Router{

       public static function get(string $uri, callable $callable){
         \App\App::getRouterInstance()->map('GET',$uri,$callable);
       }

       public  static function post(string $uri, callable $callable){
        \App\App::getRouterInstance()->map('POST', $uri, $callable);
       }

       public static function put(string $uri, callable $callable){
        \App\App::getRouterInstance()->map('PUT', $uri, $callable);
       }

       public static function delete(string $uri, callable $callable){
        \App\App::getRouterInstance()->map('DELETE', $uri, $callable);
       }
    
       public static function origin($path){
           \App\App::getRouterInstance()->setBasePath($path);
       }


       public static function matcher(){
         $match = \App\App::getRouterInstance()->match();
           if($match){
                if(is_callable($match['target']))
                {
                  call_user_func($match['target'],$match['params']);
                }
               
                else
                {
                    list($controllerName,$method) = explode("#",$match['target']);
                    $controller = new $controllerName();
                    call_user_func_array([$controller,$method],$match['params']);
                }       
            }
            else{
                http_response_code(404);
                header("Location: /error/404");
            }
      }
    }
?>