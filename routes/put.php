<?php
    // ----------------------
     // Fichier: routes/put.php
     //  Description: Ce fichier gère les routes PUT du projet
     // Auteur: Black
     // Dev-supervisor: Harry's
     //Consignes : en gros pour le put tu vas utiliser soit ajax , soit fetch pour envoyer les données au serveur
     // et tu vas utiliser la méthode PUT pour mettre à jour les données sur le serveur.
     // Tu vas aussi utiliser la méthode json_encode pour envoyer les données au format JSON.
        // Tu vas aussi utiliser la méthode json_decode pour récupérer les données au format JSON.
        // et pour recuperer les contenus envoyer tu fais :json_decode(file_get_contents('php://input'), true);
        // ca renvoie un tableau associatif donc tu dois directement le stocker dans une variable
        // ex: $putDatas = json_decode(file_get_contents('php://input'), true);
    // ici aussi les methodes sont statiques donc tu vas utiliser Router::put() pour les appeler
    // j ai crée une methode pour faire macther toutes les routes tu peux l'utiliser dans le fichier index.php
    // ou ailleurs mais le mieux ca serait de l'utiliser dans le fichier index.php
    // pour tout faire matcher et cree aussi le htaccess pour rediriger toutes les requetes vers index.php
   //----------------------

   Router::put('/',function(){
    
   })
?>