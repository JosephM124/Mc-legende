<?php
session_start();
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: login.php');
    exit;
}

require_once 'databaseconnect.php'; // adapte le chemin à ton projet

date_default_timezone_set('Africa/Kinshasa'); // adapte à ta timezone

$now = date('Y-m-d H:i:s');

$sqlUpdate = "UPDATE quiz 
              SET statut = CASE 
                  WHEN date_lancement > ? THEN 'prévu'
                  WHEN date_lancement <= ? AND DATE_ADD(date_lancement, INTERVAL duree_totale MINUTE) >= ? THEN 'actif'
                  ELSE 'inactif'
              END";

$stmtUpdate = $pdo->prepare($sqlUpdate);
$stmtUpdate->execute([$now, $now, $now]);
?>
