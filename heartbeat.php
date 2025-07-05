<?php
session_start();
require_once 'databaseconnect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'eleve') {
    echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
    exit();
}

// Vérifier si le temps est écoulé
if (isset($_SESSION['quiz_start_time']) && isset($_SESSION['quiz_duree'])) {
    $temps_restant = ($_SESSION['quiz_start_time'] + $_SESSION['quiz_duree']) - time();
    if ($temps_restant <= 0) {
        echo json_encode(['status' => 'timeout']);
        exit();
    }
}

// Enregistrer l'activité dans la base de données
if (isset($_SESSION['quiz_id'])) {
    $req = $pdo->prepare("UPDATE quiz_sessions SET last_activity = NOW() WHERE quiz_id = ? AND user_id = ?");
    $req->execute([$_SESSION['quiz_id'], $_SESSION['utilisateur']['id']]);
}

echo json_encode(['status' => 'success']);