<?php 
session_start();
require_once 'databaseconnect.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'eleve') {
    header("Location: connexion.php");
    exit();
}

$eleve_id = $_SESSION['utilisateur']['id'];
$quiz_id = $_SESSION['quiz_id'] ?? null;
$reponses = $_SESSION['reponses'] ?? null;

if (!$quiz_id || !$reponses || !isset($_SESSION['questions'])) {
    echo "Données incomplètes ou session expirée.";
    exit();
}

$questions = $_SESSION['questions'];
$total = count($questions);
$score = 0;

// Correction des réponses
foreach ($questions as $index => $question) {
    $reponse_donnee = $reponses[$index] ?? '';
    if ($reponse_donnee === $question['bonne_reponse']) {
        $score++;
    }
}

// Enregistrement des réponses
$insert_rep = $pdo->prepare("INSERT INTO reponses (utilisateur_id, quiz_id, question_id, reponse_donnee, est_correct) VALUES (?, ?, ?, ?, ?)");
foreach ($questions as $index => $question) {
    $reponse_donnee = $reponses[$index] ?? '';
    $correct = $reponse_donnee === $question['bonne_reponse'] ? 1 : 0;

    $insert_rep->execute([
        $eleve_id,
        $quiz_id,
        $question['id'],
        $reponse_donnee,
        $correct
    ]);
}

// Enregistrement du résultat global
$insert_resultat = $pdo->prepare("INSERT INTO resultats (utilisateur_id, quiz_id, score, total_questions, date_passage) VALUES (?, ?, ?, ?, NOW())");
$insert_resultat->execute([$eleve_id, $quiz_id, $score, $total]);

// Mise à jour de l'état dans interrogation_utilisateur
$maj = $pdo->prepare("UPDATE interrogation_utilisateur SET etat = 'termine', fin = NOW() WHERE utilisateur_id = ? AND quiz_id = ?");
$maj->execute([$eleve_id, $quiz_id]);

// Nettoyage de session
unset($_SESSION['quiz_id']);
unset($_SESSION['questions']);
unset($_SESSION['reponses']);
unset($_SESSION['quiz_duree']);
unset($_SESSION['quiz_start_time']);
unset($_SESSION['interrogation_en_cours']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat - MC-LEGENDE</title>
    <link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <div class="content-wrapper">
        <div class="content p-5 text-center">
            <h1 class="text-success"><i class="fas fa-check-circle"></i> Quiz Terminé !</h1>
            <p>Votre score sera connu dans les heures qui suivent.</p>
            <a href="mes_interro.php" class="btn btn-primary mt-3"><i class="fas fa-arrow-left"></i> Retour aux Interros</a>
        </div>
    </div>
</div>
</body>
</html>
