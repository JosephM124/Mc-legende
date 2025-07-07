<?php
// Sécurité : headers HTTP stricts
header('Content-Security-Policy: default-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; style-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

session_start();
include 'databaseconnect.php';

// Vérification du formulaire et CSRF
if (!isset($_POST['token'], $_POST['mot_de_passe'], $_POST['mot_de_passe2'], $_POST['csrf_token']) || !isset($_SESSION['reset_csrf']) || $_POST['csrf_token'] !== $_SESSION['reset_csrf']) {
    unset($_SESSION['reset_csrf']);
    $_SESSION['erreur'] = "Erreur de sécurité (CSRF) ou formulaire incomplet.";
    header("Location: mot_de_passe_oublie.php");
    exit();
}
unset($_SESSION['reset_csrf']);

$token = $_POST['token'];
$mot_de_passe = $_POST['mot_de_passe'];
$mot_de_passe2 = $_POST['mot_de_passe2'];

if (strlen($mot_de_passe) < 6) {
    $_SESSION['erreur'] = "Le mot de passe doit contenir au moins 6 caractères.";
    header("Location: reset_password.php?token=" . urlencode($token));
    exit();
}

if ($mot_de_passe !== $mot_de_passe2) {
    $_SESSION['erreur'] = "Les mots de passe ne correspondent pas.";
    header("Location: reset_password.php?token=" . urlencode($token));
    exit();
}

// Rechercher le token
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE reset_token = ? AND token_expiration > NOW()");
$stmt->execute([$token]);

if ($stmt->rowCount() === 0) {
    $_SESSION['erreur'] = "Token invalide ou expiré.";
    header("Location: mot_de_passe_oublie.php");
    exit();
}

$user = $stmt->fetch();
$id = $user['id'];
$hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

// Mettre à jour le mot de passe
$update = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ?, reset_token = NULL, token_expiration = NULL WHERE id = ?");
$update->execute([$hash, $id]);

$_SESSION['success'] = "Votre mot de passe a été réinitialisé. Vous pouvez maintenant vous connecter.";
header("Location: connexion.php");
exit();
