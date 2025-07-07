<?php
// Sécurisation avancée de la session et headers HTTP
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
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data:; connect-src 'self';");
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
require_once 'fonctions.php'; // Pour CSRF et filtrage

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: login.php');
    exit;
}

require_once 'databaseconnect.php';
require_once 'log_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !verifier_csrf_token($_POST['csrf_token'], 'ajout_interro')) {
        header('Location: interro_admin.php?erreur=csrf');
        exit;
    }
    $nom = htmlspecialchars(strip_tags($_POST['nom']));
    $categorie = htmlspecialchars(strip_tags($_POST['categorie']));
    $date_lancement = $_POST['date_lancement'];
    $duree_total = (int)$_POST['duree_total'];
    $duree_par_question = (int)$_POST['duree_par_question'];

    $stmt = $pdo->prepare("INSERT INTO quiz (titre, categorie, date_lancement, duree_totale, temps_par_question) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $categorie, $date_lancement, $duree_total, $duree_par_question]);
    // Journalisation sécurisée
    enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Ajout d'une interrogation", "Nom: $nom, Catégorie: $categorie");

    // Invalidation du token CSRF après usage
    supprimer_csrf_token($_POST['csrf_token'], 'ajout_interro');

    header("Location: interro_admin.php?ajout=ok");
    exit;
}
?>
