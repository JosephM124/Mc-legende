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
// Headers HTTP de sécurité
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data:; connect-src 'self';");


if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header("Location: connexion.php");
    exit();
}

require_once 'databaseconnect.php';
require_once 'log_admin.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['id']) &&
    isset($_POST['csrf_token']) &&
    isset($_SESSION['csrf_delete_admin']) &&
    hash_equals($_SESSION['csrf_delete_admin'], $_POST['csrf_token'])
) {
    unset($_SESSION['csrf_delete_admin']); // usage unique
    $id = (int)$_POST['id'];
    // Vérifier que l'admin existe et n'est pas le dernier admin principal
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ? AND role = 'admin_simple'");
    $stmt->execute([$id]);
    $admin = $stmt->fetch();
    if ($admin) {
        if (!empty($admin['photo']) && file_exists($admin['photo'])) {
            unlink($admin['photo']);
        }
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $success = $stmt->execute([$id]);
        if ($success) {
            enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Suppression d'un admin", "Nom : $admin[nom]  $admin[prenom]");
            header('Location: gestion_admins.php?success=suppression');
            exit;
        } else {
            $_SESSION['erreur'] = "Erreur lors de la suppression.";
            header('Location: gestion_admins.php?success=echec');
            exit;
        }
    } else {
        $_SESSION['erreur'] = "Admin introuvable ou suppression non autorisée.";
        header('Location: gestion_admins.php?success=echec');
        exit;
    }
} else {
    $_SESSION['erreur'] = "Suppression non autorisée (CSRF ou méthode).";
    header('Location: gestion_admins.php?success=echec');
    exit;
}
?>
