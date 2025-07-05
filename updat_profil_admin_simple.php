<?php
session_start();

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_simple') {
    header("Location: connexion.php");
    exit;
}
require_once 'databaseconnect.php';

$id = $_SESSION['utilisateur']['id'];

$nom = $_POST['nom'];
$postnom = $_POST['postnom'];
$prenom = $_POST['prenom'];
$adresse = $_POST['adresse'];
$email = $_POST['email'];
$telephone = $_POST['telephone'];
$date_naissance = $_POST['date_naissance'];
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
        header("Location: profil_admin_simple.php?erreur=confirmation");
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

header("Location: profil_admin_simple.php?success=ok");
exit;
?>
