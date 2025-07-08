<?php 

  // ----------------------
   // ici avec les formualires html , fetch ou ajax
   // tu vas utiliser la méthode POST pour envoyer les données au serveur.

  \Router\Router ::post('/login',function(){
      (new \Controllers\PageController())->login();
   });
   
    \Router\Router::post('/register',function(){
        (new \Controllers\PageController())->register();
    });
?>