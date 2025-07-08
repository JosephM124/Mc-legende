<?php
// harry's ajoute juste les routes en fonction des fichiers 
// ne les supprimes pas encore et laisse moi recuperer les vues de son projet 
// pour la logique on garde sa logique aussi on ne supprim rien on va se basser sur ca pour faire 
// nos controllers , nos middlewares et les models
// je n ai pas encore fini ca et ca me soule donc si tu as le temps 
// rajoutes justes les attribut en fonction de chaque model 
// et n oublie de prendre chaque class avec le namespace 


  \Router\Router::get('/',function(){
    (new \Controllers\PageController())->index();
  });

 \Router\Router::get('/connexion',function(){
    (new \Controllers\PageController())->connexion();
  });

 \Router\Router::get('/inscription',function(){
    (new \Controllers\PageController())->inscription();
  });

  \Router\Router::get('/logout',function(){
     (new \Controllers\UtilisateursController())->logout();
  });

 \Router\Router::get('/eleve/home',function(){
    (new \Controllers\PageController())->eleve_home();
  });

 \Router\Router::get('/eleve/interro',function(){
        (new \Controllers\PageController())->eleve_interro();
  });

 \Router\Router::get('/eleve/resultats',function(){
   (new \Controllers\PageController())->eleve_resultat();
  });

  \Router\Router::get('/eleve/profil',function(){
      (new \Controllers\PageController())->eleve_profil();
  });
  
  \Router\Router::get('/admin/home', function(){
    (new \Controllers\PageController())->admin_home();
  });

  \Router\Router::get('/admin/profil',function(){
        echo "chope le fichier profil_admin.php et met ca dans la vue c est tout ";
  })
?>