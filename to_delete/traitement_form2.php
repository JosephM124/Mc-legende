<?php
// Sécurité : headers HTTP stricts
header('Content-Security-Policy: default-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; style-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

session_start();
require_once 'databaseconnect.php'; // ta connexion PDO

// Sécurité : vérification de la session de l'étape 1
if (!isset($_SESSION['inscription1'])) {
  header('Location: inscription.php');
  exit();
}

// Vérification CSRF
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token_form2']) || $_POST['csrf_token'] !== $_SESSION['csrf_token_form2']) {
  unset($_SESSION['csrf_token_form2']);
  header('Location: formulaire2.php?csrf=fail');
  exit();
}
unset($_SESSION['csrf_token_form2']);

// Fonction de filtrage
function filtrer($donnee) {
  return htmlspecialchars(trim($donnee), ENT_QUOTES, 'UTF-8');
}

// Récupération de l'ID utilisateur
$id_utilisateur = $_SESSION['inscription1']['id'];

// Filtrage des données du formulaire 2
$etablissement  = filtrer($_POST['etablissement'] ?? '');
$section        = filtrer($_POST['section'] ?? '');
$adresse_ecole  = filtrer($_POST['adresse_ecole'] ?? '');
$categorie      = filtrer($_POST['categorie'] ?? '');
$pays      = filtrer($_POST['pays'] ?? '');
$ville_province      = filtrer($_POST['province'] ?? '');

// Vérification simple des champs
if (empty($etablissement) || empty($pays) || empty($adresse_ecole) || empty($categorie) || empty($ville_province)) {
  header('Location: formulaire2.php?nr=ok');
  exit();
}

// Enregistrement dans la table eleves
$insert = $pdo->prepare("INSERT INTO eleves (utilisateur_id, etablissement, section, adresse_ecole, categorie_activite,pays,ville_province)
                         VALUES (?, ?, ?, ?, ?,?,?)");
$ok = $insert->execute([
  $id_utilisateur, $etablissement, $section, $adresse_ecole, $categorie,$pays,$ville_province
]);

if ($ok) {
  // Mise à jour : l'inscription est terminée
  $update = $pdo->prepare("UPDATE utilisateurs SET inscription_complete = 1 WHERE id = ?");
  $update->execute([$id_utilisateur]);

  /* Création de la session élève connectée
  $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
  $stmt->execute([$id_utilisateur]);
  $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

  
  $_SESSION['utilisateur'] = [
    'id'    => $utilisateur['id'],
    'nom'   => $utilisateur['nom'],
    'postnom'   => $utilisateur['postnom'],
    'prenom'   => $utilisateur['prenom'],
    'email' => $utilisateur['email'],
    'role'  => $utilisateur['role']
  ];
*/
  // Nettoyer la session temporaire
  unset($_SESSION['inscription1']['id']);

  // Redirection vers l'espace personnel de l'élève
  header('Location: connexion.php?success=ok');
  exit();
} else {
  header('Location: formulaire2.php?e=ok');
  exit();
}
?>
