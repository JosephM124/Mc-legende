<?php
session_start();
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_principal') {
    header('Location: login.php');
    exit;
}

include 'databaseconnect.php';
require_once 'log_admin.php';
require_once 'fonctions.php'; // Pour CSRF

// Suppression de question
if (isset($_POST['supprimer_id'])) {
    if (!isset($_POST['csrf_token']) || !verifier_csrf_token($_POST['csrf_token'], 'suppression_question_' . intval($_POST['supprimer_id']))) {
        $message = "Échec lors de la vérification CSRF.";
        header("Location: question_admin.php?success=echec");
        exit();
    } else {
        $id = intval($_POST['supprimer_id']);

        // 1. Récupérer les infos avant la suppression
        $questions = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
        $questions->execute([$id]);
        $question = $questions->fetch();

        $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
        if ($stmt->execute([$id])) {

            // 3. Enregistrer l'action dans l'historique
            enregistrer_activite_admin(
                $_SESSION['utilisateur']['id'],
                "Suppression d'une question",
                "Question : " . htmlspecialchars(strip_tags($question['texte_question'])) . " | Catégorie : " . htmlspecialchars(strip_tags($question['categorie']))
            );
            supprimer_csrf_token($_POST['csrf_token'], 'suppression_question_' . $id);
            header("Location: question_admin.php?success=ok");
            exit();
        } else {
            header("Location: question_admin.php?success=echec");
        }
    }
}



