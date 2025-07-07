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


require_once 'databaseconnect.php'; // connexion PDO $pdo
require_once 'log_admin.php';

// Vérification du rôle admin principal
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    $_SESSION['erreur'] = "Accès refusé.";
    header("Location: connexion.php");
    exit();
}

function filtrer_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Protection CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['erreur'] = "Token CSRF invalide.";
        header("Location: gestion_admins.php");
        exit();
    }
    unset($_SESSION['csrf_token']); // usage unique

    $nom = filtrer_input($_POST['nom'] ?? '');
    $prenom = filtrer_input($_POST['prenom'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $telephone = filtrer_input($_POST['telephone'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $statut = filtrer_input($_POST['statut'] ?? '');
    $inscription_complete = filtrer_input($_POST['inscription_complete'] ?? '');
    $role = 'admin_simple';

    // Validation de base
    if (!$nom || !$prenom || !$email || !$telephone || !$mot_de_passe || !$statut || !$inscription_complete) {
        $_SESSION['erreur'] = "Tous les champs requis doivent être remplis.";
        header("Location: gestion_admins.php");
        exit();
    }

    // Vérification du format du numéro de téléphone
    if (!preg_match('/^\+?\d{9,15}$/', $telephone)) {
        $_SESSION['erreur'] = "Le numéro de téléphone est invalide. Il doit contenir entre 9 et 15 chiffres.";
        header("Location: gestion_admins.php");
        exit();
    }

    // Vérifier si email existe déjà
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['erreur'] = "Cet email est déjà utilisé.";
        header("Location: gestion_admins.php?email=echec");
        exit();
    }

    // Vérifier si le téléphone existe déjà
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE telephone = ?");
    $stmt->execute([$telephone]);
    if ($stmt->fetch()) {
        $_SESSION['erreur'] = "Ce numéro de téléphone est déjà utilisé.";
        header("Location: gestion_admins.php?num=echec");
        exit();
    }

    // Hash du mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Gestion de la photo
    $photo_nom = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
        finfo_close($finfo);
        $allowed_mimes = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!in_array($ext, $allowed) || !in_array($mime, $allowed_mimes) || $_FILES['photo']['size'] > 2*1024*1024) {
            $_SESSION['erreur'] = "Fichier photo invalide (format ou taille).";
            header("Location: gestion_admins.php");
            exit();
        }
        $photo_nom = 'uploads/avatars/' . uniqid('admin_', true) . '.' . $ext;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_nom)) {
            $_SESSION['erreur'] = "Échec du téléchargement de la photo.";
            header("Location: gestion_admins.php");
            exit();
        }
    }

    // Insertion dans la base
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, photo, statut, inscription_complete, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $resultat = $stmt->execute([
        $nom,
        $prenom,
        $email,
        $telephone,
        $mot_de_passe_hash,
        $photo_nom,
        $statut,
        $inscription_complete,
        $role
    ]);

    if ($resultat) {

        enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Ajout d'un administrateur", "Détail : $nom $prenom ");

        $_SESSION['success'] = "L'administrateur a bien été ajouté.";

        header("Location: gestion_admins.php?success=ok");
    exit();

    } else {
        enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Ajout d'un administrateur échoué", "Détail : $nom $prenom ");

        $_SESSION['erreur'] = "Une erreur est survenue lors de l'ajout.";

        header("Location: gestion_admins.php?success=echec");

    exit();
    }

    
} else {
    // Génération du token CSRF pour le formulaire (à mettre dans le form HTML)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['erreur'] = "Méthode non autorisée.";
    header("Location: gestion_admins.php?success=echec");
    exit();
}
