<?php
session_start();
require_once 'databaseconnect.php';

// Enregistrer la tentative sans JS comme triche
if (isset($_SESSION['utilisateur'])) {
    $logMessage = date('Y-m-d H:i:s') . " - " . 
                  $_SESSION['utilisateur']['id'] . " - " . 
                  $_SESSION['utilisateur']['prenom'] . " " . 
                  $_SESSION['utilisateur']['nom'] . 
                  " - JavaScript désactivé\n";
    
    file_put_contents('cheat_logs.txt', $logMessage, FILE_APPEND);
    $_SESSION['cheating_detected'] = true;
}

header("Location: terminer_quiz.php?cheating=1");