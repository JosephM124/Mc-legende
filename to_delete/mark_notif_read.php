<?php
session_start();
require_once 'databaseconnect.php';
require_once 'fonctions.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

$id = $_GET['id'] ?? null;
$redirect = $_GET['redirect'] ?? 'notifications.php';
$id_utilisateur = $_SESSION['utilisateur']['id'];

// Sécuriser la mise à jour
$stmt = $pdo->prepare("UPDATE notifications SET lue = 1 WHERE id = ? AND (utilisateur_id = ? OR (est_generale = 1 AND role_destinataire = 'eleve'))");
$stmt->execute([$id, $id_utilisateur]);

header("Location: " . $redirect);
exit;
