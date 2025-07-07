<?php
// Sécurisation avancée de la session et headers HTTP
session_start();
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data:; connect-src 'self';");
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header("Location: connexion.php");
    exit();
}

require_once 'databaseconnect.php';
require_once 'log_admin.php';
require_once 'fonctions.php'; // Pour la gestion CSRF

// Vérification du token CSRF (usage unique)
if (!isset($_GET['csrf_token']) || !verifier_csrf_token($_GET['csrf_token'], 'suppression_eleve')) {
    header("Location: admin_eleve.php?erreur=csrf");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_eleve.php?erreur=ID invalide");
    exit();
}

$id_eleve = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id_eleve]);
$eleve = $stmt->fetch();

if (!$eleve) {
    header("Location: admin_eleve.php?erreur=Élève introuvable");
    exit();
}

try {
    // Suppression dans la table eleves (clé étrangère)
    $stmt1 = $pdo->prepare("DELETE FROM eleves WHERE utilisateur_id = ?");
    $stmt1->execute([$id_eleve]);
    // Suppression dans la table utilisateurs
    $stmt2 = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt2->execute([$id_eleve]);
} catch (PDOException $e) {
    // Log de l'erreur
    error_log('Erreur suppression élève: ' . $e->getMessage());
    header("Location: admin_eleve.php?erreur=suppression_sql");
    exit();
}

// Journalisation sécurisée (anti-XSS)
$nom = htmlspecialchars(strip_tags($eleve['nom']));
$postnom = htmlspecialchars(strip_tags($eleve['postnom']));
$prenom = htmlspecialchars(strip_tags($eleve['prenom']));
enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Suppression d'un élève", "Nom : $nom $postnom $prenom");

// Invalidation du token CSRF après usage
supprimer_csrf_token($_GET['csrf_token'], 'suppression_eleve');

header("Location: admin_eleve.php?suppression=ok");
exit();
?>
