<?php
// Sécurisation avancée de la session et headers HTTP
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
if (isset($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['session_regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = true;
}
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; img-src 'self' data:; connect-src 'self';");
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');

require_once 'databaseconnect.php';
require_once 'fonctions.php'; // Pour CSRF

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header("Location: connexion.php");
    exit;
}

$id = $_SESSION['utilisateur']['id'];

// Vérification CSRF
if (!isset($_POST['csrf_token']) || !verifier_csrf_token($_POST['csrf_token'], 'modif_profil_admin')) {
    header('Location: profil_admin.php?erreur=csrf');
    exit;
}

// Filtrage/échappement des entrées utilisateur (anti-XSS)
$nom = htmlspecialchars(strip_tags($_POST['nom']));
$postnom = htmlspecialchars(strip_tags($_POST['postnom']));
$prenom = htmlspecialchars(strip_tags($_POST['prenom']));
$adresse = htmlspecialchars(strip_tags($_POST['adresse']));
$email = htmlspecialchars(strip_tags($_POST['email']));
$telephone = htmlspecialchars(strip_tags($_POST['telephone']));
$date_naissance = htmlspecialchars(strip_tags($_POST['date_naissance']));
$mot_de_passe = $_POST['mot_de_passe'] ?? '';
$confirmation = $_POST['confirmation_mot_de_passe'] ?? '';

$photo_path = null;

// ✅ Gérer l'upload de la photo
if (!empty($_FILES['photo']['name'])) {
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $nom_unique = uniqid() . '.' . $ext;
    $upload_dir = 'uploads/avatars/';
    $upload_path = $upload_dir . $nom_unique;

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
        $photo_path = $upload_path;
    }
}

// ✅ Préparer la mise à jour
$params = [
    $nom, $postnom, $prenom, $adresse, $email, $telephone, $date_naissance
];
$query = "UPDATE utilisateurs SET nom = ?, postnom = ?, prenom = ?, adresse = ?, email = ?, telephone = ?, naissance = ?";

// ✅ Ajout du chemin de photo s’il y en a une
if ($photo_path) {
    $query .= ", photo = ?";
    $params[] = $photo_path;
}

// ✅ Vérifier si mot de passe rempli
if (!empty($mot_de_passe) || !empty($confirmation)) {
    if ($mot_de_passe !== $confirmation) {
        header("Location: profil_admin.php?erreur=confirmation");
        exit;
    }
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    $query .= ", mot_de_passe = ?";
    $params[] = $mot_de_passe_hash;
}

$query .= " WHERE id = ?";
$params[] = $id;

$stmt = $pdo->prepare($query);
$stmt->execute($params);

// ✅ Mettre à jour la session si l'email ou photo change
$_SESSION['utilisateur']['email'] = $email;
if ($photo_path) {
    $_SESSION['utilisateur']['photo'] = $photo_path;
}

// Après modification, suppression du token de session pour usage unique
supprimer_csrf_token($_POST['csrf_token'], 'modif_profil_admin');
unset($_SESSION['csrf_token_profil_admin']);

header("Location: profil_admin.php?success=ok");
exit;
?>
