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

if (!isset($_SESSION['utilisateur']) && $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header("Location: connexion.php");
    exit();
}

require_once 'databaseconnect.php';
require_once 'log_admin.php';
require_once 'fonctions.php'; // Pour la gestion CSRF


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id']) && !empty($_POST['csrf_token'])) {
  $id = $_POST['id'];
  $csrf_token = $_POST['csrf_token'];
  // Vérification stricte du token CSRF
  if (!verifier_csrf_token($csrf_token, 'suppression_interro_' . $id)) {
    header("Location: interro_admin.php?erreur=csrf");
    exit();
  }

  $stmts = $pdo->prepare("SELECT * FROM quiz WHERE id = ?");
  $stmts->execute([$id]);
  $quiz = $stmts->fetch();

  // Supprimer d'abord les questions liées à ce quiz
  $pdo->prepare("DELETE FROM quiz_questions WHERE quiz_id = ?")->execute([$id]);

  $stmt = $pdo->prepare("DELETE FROM quiz WHERE id = ?");
  $stmt->execute([$id]);

  // Journalisation sécurisée (anti-XSS)
  $titre = htmlspecialchars(strip_tags($quiz['titre']));
  $categorie = htmlspecialchars(strip_tags($quiz['categorie']));
  enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Suppression d'une interrogation", "Détail : $titre $categorie");

  // Invalidation du token CSRF après usage
  supprimer_csrf_token($csrf_token, 'suppression_interro_' . $id);

  header("Location: interro_admin.php?success=suppression");
  exit;
}
?>
