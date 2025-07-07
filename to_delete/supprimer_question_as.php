<?php
session_start();
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin_simple') {
    header('Location: connexion.php');
    exit;
}

include 'databaseconnect.php';
require_once 'log_admin.php';

// Suppression de question
if (isset($_POST['supprimer_id'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "Échec lors de la vérification CSRF.";
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
            "Question : " . $question['texte_question'] . " | Catégorie : " . $question['categorie']
        );

            
            header("Location: question_admin_simple.php?success=ok");
            exit();
        } else {
            header("Location: question_admin_simple.php?success=echec");
        }
    }
}



