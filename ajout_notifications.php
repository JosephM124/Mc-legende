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

require_once 'databaseconnect.php';
require_once 'log_admin.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: connexion.php');
    exit;
}

function filtrer_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protection CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['erreur'] = "Token CSRF invalide.";
        header('Location: gestion_notifications.php?ajout=echec');
        exit;
    }
    unset($_SESSION['csrf_token']);

    $titre = filtrer_input($_POST['titre'] ?? '');
    $message = filtrer_input($_POST['message'] ?? '');
    $lien = 'notifications.php';
    $type = filtrer_input($_POST['type'] ?? '');
    $categorie = !empty($_POST['categorie']) ? filtrer_input($_POST['categorie']) : null;
    $cible_type = filtrer_input($_POST['cible_type'] ?? '');
    $est_generale = 1;

    if ($titre && $message && $type && $cible_type) {
        if ($cible_type === 'eleve') {
            $utilisateur_id = isset($_POST['utilisateur_id']) ? (int)$_POST['utilisateur_id'] : null;
            if ($utilisateur_id) {
                $stmt = $pdo->prepare("INSERT INTO notifications 
                    (utilisateur_id, titre, message, lien, type, role_destinataire, est_generale, categorie, date_creation, lue)
                    VALUES (?, ?, ?, ?, ?, ?, 1, ?, NOW(), 0)");
                $stmt->execute([
                    $utilisateur_id,
                    $titre,
                    $message,
                    $lien,
                    $type,
                    $cible_type,
                    $categorie
                ]);
            }
        } elseif ($cible_type === 'role') {
            $role_destinataire = filtrer_input($_POST['role_destinataire'] ?? '');
            if (!$role_destinataire) {
                header('Location: gestion_notifications.php?success=echec');
                exit;
            }
            if ($role_destinataire === 'eleve' && $categorie) {
                $query = "SELECT u.id FROM utilisateurs u JOIN eleves e ON u.id = e.utilisateur_id WHERE u.role = 'eleve' AND e.categorie_activite = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$categorie]);
            } else {
                $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE role = ?");
                $stmt->execute([$role_destinataire]);
            }
            $utilisateurs = $stmt->fetchAll();
            $insert = $pdo->prepare("INSERT INTO notifications 
                (utilisateur_id, titre, message, lien, type, role_destinataire, est_generale, categorie, date_creation, lue)
                VALUES (?, ?, ?, ?, ?, ?, 1, ?, NOW(), 0)");
            foreach ($utilisateurs as $u) {
                $insert->execute([
                    $u['id'],
                    $titre,
                    $message,
                    $lien,
                    $type,
                    $role_destinataire,
                    $categorie
                ]);
            }
        }
        enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Ajout d'une notification", "Détail : $titre $message ");
        header('Location: gestion_notifications.php?ajout=ok');
        exit;
    } else {
        enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Ajout d'une notification échoué", "Détail : $titre $message ");
        header('Location: gestion_notifications.php?ajout=echec');
        exit;
    }
} else {
    // Génération du token CSRF pour le formulaire (à mettre dans le form HTML)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
