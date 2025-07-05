<?php 
// Sécurisation avancée de la session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (isset($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}
session_start();
if (!isset($_SESSION['session_regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = true;
}
// Headers HTTP de sécurité (CSP adaptée)
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=()");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data:; connect-src 'self';");

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: login.php');
    exit;
}

require_once 'databaseconnect.php';
require_once 'log_admin.php';


if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['id']) &&
    isset($_POST['csrf_token']) &&
    isset($_SESSION['csrf_token']) &&
    $_POST['csrf_token'] === $_SESSION['csrf_token']
) {
    unset($_SESSION['csrf_token']);
    $id = $_POST['id'];

    // 1. Récupérer les infos avant la suppression
    $notifs = $pdo->prepare("SELECT * FROM notifications WHERE id = ?");
    $notifs->execute([$id]);
    $notif = $notifs->fetch();

    if ($notif) {
        // 2. Supprimer
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->execute([$id]);

        // 3. Enregistrer l'action dans l'historique
        enregistrer_activite_admin(
            $_SESSION['utilisateur']['id'],
            "Suppression d'une notification",
            "Titre : " . $notif['titre'] . " | Message : " . $notif['message']
        );

        header("Location: gestion_notifications.php?success=ok");
        exit;
    } else {
        // Notification inexistante
        header("Location: gestion_notifications.php?success=notfound");
        exit;
    }

} else {
    header("Location: gestion_notifications.php?success=echec");
    exit;
}
