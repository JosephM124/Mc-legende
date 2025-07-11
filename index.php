<?php 
    require (__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    $path = isset($_SERVER['BASE_URI']) ? $_SERVER['BASE_URI'] : '';
    require './routes/routes.php';
    
    \Router\Router::origin($path);
    \Router\Router::matcher();
?> 