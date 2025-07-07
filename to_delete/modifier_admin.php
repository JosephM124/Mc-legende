<?php
session_start();
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: connexion.php');
    exit;
}

require_once 'databaseconnect.php';
require_once 'fonctions.php';
require_once 'log_admin.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $nom = filtrer($_POST['nom']);
    $prenom = filtrer($_POST['prenom']);
    $email = filtrer_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $telephone = filtrer($_POST['telephone']);
    $statut = filtrer($_POST['statut']);

    // Upload photo si fournie
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'uploads/avatars/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }


    $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ?, statut = ?";
    $params = [$nom, $prenom, $email, $telephone, $statut];

    if ($photo) {
        $sql .= ", photo = ?";
        $params[] = $photo;
    }

    $sql .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Modification d'un admin_simple", "Nom : $nom  $prenom");


    header('Location: gestion_admins.php?success=modif');
    exit;
}
?>
