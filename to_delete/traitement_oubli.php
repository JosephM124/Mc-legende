<?php
// Sécurité : headers HTTP stricts
header('Content-Security-Policy: default-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; style-src \'self\' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');

session_start();
include 'databaseconnect.php';
require './vendor/autoload.php'; // Assurez-vous que le chemin est correct
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';
// require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token_oubli']) || $_POST['csrf_token'] !== $_SESSION['csrf_token_oubli']) {
        unset($_SESSION['csrf_token_oubli']);
        $_SESSION['erreur'] = "Erreur de sécurité (CSRF). Veuillez réessayer.";
        header("Location: mot_de_passe_oublie.php");
        exit();
    }
    unset($_SESSION['csrf_token_oubli']);
    $email = trim($_POST['email']);

    // Vérifier si l'e-mail existe
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() === 0) {
        $_SESSION['erreur'] = "Aucun compte n'est associé à cette adresse e-mail.";
        header("Location: mot_de_passe_oublie.php");
        exit();
    }

    // Générer un token et une date d'expiration
    $token = bin2hex(random_bytes(32));
    $expiration = date("Y-m-d H:i:s", time() + 1800); // 30 minutes

    // Sauvegarder le token dans la base de données
    $update = $pdo->prepare("UPDATE utilisateurs SET reset_token = ?, token_expiration = ? WHERE email = ?");
    $update->execute([$token, $expiration, $email]);

    $reset_link = "http://localhost/mc-legende/reset_password.php?token=$token";

    // Envoi du mail
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mukadijeff4@gmail.com'; // 👉 remplace par ton adresse Gmail
        $mail->Password = 'supssfvwfbtlavfs'; // 👉 mot de passe d'application Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Expéditeur et destinataire
        $mail->setFrom('mukadijeff4@gmail.com', 'MC-LEGENDE');
        $mail->addAddress($email);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'Réinitialisation de votre mot de passe';
        $mail->Body = "
            <h3>Bonjour,</h3>
            <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
            <p>Veuillez cliquer sur le lien suivant :</p>
            <p><a href='$reset_link'>$reset_link</a></p>
            <p>Ce lien est valable pendant 30 minutes.</p>
        ";

        $mail->send();

        $_SESSION['success'] = "Un lien de réinitialisation vous a été envoyé par e-mail.";
        header("Location: mot_de_passe_oublie.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['erreur'] = "Erreur lors de l'envoi de l'e-mail : " . $mail->ErrorInfo;
        header("Location: mot_de_passe_oublie.php");
        exit();
    }
}
?>
