<?php

   // API Routes - Élèves
  \Router\Router::get('/api/eleves', function(){
      (new \Controllers\EleveController())->index();
  });

  \Router\Router::get('/api/eleves/[i:id]', function($id){
    (new \Controllers\EleveController())->show($id); 
  });

  \Router\Router::get('/api/eleves/etablissement/[a:etablissement]', function($etablissement){
    (new \Controllers\EleveController())->getByEtablissement($etablissement);
  });

  \Router\Router::get('/api/eleves/section/[a:section]', function($section){
    (new \Controllers\EleveController())->getBySection($section);
  });
?>