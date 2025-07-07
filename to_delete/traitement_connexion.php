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
require_once 'fonctions.php'; // Pour filtrage
include 'databaseconnect.php';
require_once 'log_admin.php';

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer et filtrer les champs
    $identifiant = htmlspecialchars(strip_tags(trim($_POST['identifiant'] ?? ''))); // email OU téléphone
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($identifiant) || empty($mot_de_passe)) {
        header('Location: connexion.php?nr=ok');
     exit();
    }

    // Préparer la requête : chercher par email OU téléphone
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? OR telephone = ?");
    $stmt->execute([$identifiant, $identifiant]);
    
    if ($stmt->rowCount() === 0) {
        header('Location: connexion.php?pi=ok');
     exit();
    }

    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe
    if (!password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        header('Location: connexion.php?mi=ok');
     exit();
    }

    if ($utilisateur['role'] === 'eleve' && $utilisateur['inscription_complete'] == 0) {
    $_SESSION['inscription1']['id'] = $utilisateur['id'];
    header('Location: formulaire2.php?ii=ok');
     exit();
     }


    // Création de la session utilisateur
    $_SESSION['utilisateur'] = [
        'id' => $utilisateur['id'],
        'nom' => htmlspecialchars($utilisateur['nom']),
        'email' => htmlspecialchars($utilisateur['email']),
        'role' => $utilisateur['role']
    ];

    // Redirection selon le rôle
    switch ($utilisateur['role']) {
        case 'eleve':
            header("Location: eleve.php");
            break;

        case 'admin_simple':

            // 3. Enregistrer l'action dans l'historique
        enregistrer_activite_admin(
            $_SESSION['utilisateur']['id'],
            "Connexion d'un admin",
            "Nom : " . $_SESSION['utilisateur']['nom'] . " | Email : " . $_SESSION['utilisateur']['email']
        );

            header("Location: admin_simple.php");
            break;

        case 'admin_principal': 
            
            // 3. Enregistrer l'action dans l'historique
        enregistrer_activite_admin(
            $_SESSION['utilisateur']['id'],
            "Connexion d'un admin",
            "Nom : " . $_SESSION['utilisateur']['nom'] . " | Email : " . $_SESSION['utilisateur']['email']
        );

            header("Location: admin_principal.php");
            break;
        default:
            die("Rôle utilisateur non reconnu.");
    }
    exit();

} else {
    echo "Méthode non autorisée.";
}


