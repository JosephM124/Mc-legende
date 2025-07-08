<?php 

// ----------------------
 // ici aussi c est la meme chose avec put donc il faut que tu utilises
 // fetch ou ajax pour envoyer les données au serveur
 // et tu vas utiliser la méthode DELETE pour supprimer les données sur le serveur.
 // ah oui tu peux t utiliser des fonctions ou des fonctions anonymes mais 
 // utilise les anonymes pour les routes
 // et les fonctions pour les controllers
  // c est mieux pour reduire la portée des variables et des fonctions
  
 \Router\Router::delete('/',function(){

 })
 // pareil ici aussi 
 // j ai ajouté des controllers  resolu le probleme de l api/auth , mais il faut trouver cmt
 //le rendre utilisable par exemple avec le formulaire avec le js pour fetch les erreurs surtout
 // ajoutes les .htaccess aussi pour d abord tout rediriger sur index comme point principal 
 // le dossier public viendra apres 
?>