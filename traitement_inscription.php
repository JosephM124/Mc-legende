<?php
// Sécurité : headers HTTP stricts
header('Content-Security-Policy: default-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; style-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

session_start();
require_once 'databaseconnect.php';

function filtrer($donnee) {
  return htmlspecialchars(trim($donnee), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Vérification CSRF
  if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token_inscription']) || $_POST['csrf_token'] !== $_SESSION['csrf_token_inscription']) {
    unset($_SESSION['csrf_token_inscription']);
    afficherErreur('Erreur de sécurité (CSRF). Veuillez réessayer.');
    exit();
  }
  unset($_SESSION['csrf_token_inscription']);

  // Récupération et filtrage
  $nom       = filtrer($_POST['nom']);
  $postnom   = filtrer($_POST['postnom']);
  $prenom    = filtrer($_POST['prenom']);
  $telephone = preg_replace('/[^0-9]/', '', $_POST['telephone']); // chiffres uniquement
  $email     = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
  $adresse   = filtrer($_POST['adresse']);
  $naissance = filtrer($_POST['naissance']);
  $sexe      = filtrer($_POST['sexe']);
  $mdp       = $_POST['mot_de_passe'];
  $confirmer = $_POST['confirmer'];
  $role      = 'eleve';

  // Validation basique
  if (!$email || $mdp !== $confirmer) {
    header('Location: inscription.php?mpe=ok');
      exit();
  }

  // Vérification unicité email/téléphone
  $verif = $pdo->prepare("SELECT id, inscription_complete FROM utilisateurs WHERE email = ? OR telephone = ?");
  $verif->execute([$email, $telephone]);

  if ($verif->rowCount() > 0) {
    $utilisateur = $verif->fetch();

    if ($utilisateur['inscription_complete'] == 0) {
      $_SESSION['inscription1']['id'] = $utilisateur['id'];
      header('Location: formulaire2.php?ii=ok');
      exit();
    } else {
      header('Location: inscription.php?eu=ok');
      exit();
    }
  }

  // Hachage du mot de passe
  $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

  // Insertion
  $requete = $pdo->prepare("INSERT INTO utilisateurs
    (nom, postnom, prenom, email, mot_de_passe, role, adresse, telephone, date_inscription, sexe, inscription_complete)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 0)");

  $ok = $requete->execute([
    $nom, $postnom, $prenom, $email, $mdp_hash, $role, $adresse, $telephone, $sexe
  ]);

  if ($ok) {
    $_SESSION['inscription1']['id'] = $pdo->lastInsertId();
    header('Location: formulaire2.php');
    exit();
  } else {
    header('Location: inscription.php?e=ok');
      exit();
  }
}

// Fonction pour afficher une erreur joliment
function afficherErreur($message) {
  echo '
  <!DOCTYPE html>
  <html lang="fr">
  <head>
    <meta charset="UTF-8">
    <title>Erreur - MC-LEGENDE</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
  </head>
  <body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="alert alert-danger text-center shadow p-4 rounded" role="alert">
      <h4 class="alert-heading">Oups !</h4>
      <p>' . $message . '</p>
      <hr>
      <a href="inscription.php" class="btn btn-outline-danger">Retour à l’inscription</a>
    </div>
  </body>
  </html>
  ';
}
?>
