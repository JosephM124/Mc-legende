<?php
require_once 'databaseconnect.php';

// Active les interrogations dont l'heure de lancement est passée
$pdo->prepare("
    UPDATE quiz 
    SET statut = 'actif'
    WHERE statut != 'actif' 
      AND NOW() >= date_lancement 
      AND NOW() < DATE_ADD(date_lancement, INTERVAL duree_totale MINUTE)
")->execute();

// Désactive celles dont la durée est écoulée
$pdo->prepare("
    UPDATE quiz 
    SET statut = 'inactif'
    WHERE statut != 'inactif' 
      AND NOW() >= DATE_ADD(date_lancement, INTERVAL duree_totale MINUTE)
")->execute();

?>
