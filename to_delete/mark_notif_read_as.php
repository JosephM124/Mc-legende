<?php
session_start();
require_once 'databaseconnect.php';
require_once 'fonctions.php';

// Vérifie que l'utilisateur est bien un admin simple
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_simple') {
    http_response_code(403);
    exit;
}

$id = $_GET['id'] ?? null;
$redirect = $_GET['redirect'] ?? 'notifications_as.php';
$id_utilisateur = $_SESSION['utilisateur']['id'];

// Sécuriser la mise à jour
$stmt = $pdo->prepare("UPDATE notifications SET lue = 1 WHERE id = ? AND (utilisateur_id = ? OR (est_generale = 1 AND role_destinataire = 'eleve'))");
$stmt->execute([$id, $id_utilisateur]);

header("Location: " . $redirect);
exit;
