<?php 
session_start();
require_once 'databaseconnect.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'eleve') {
    header("Location: connexion.php");
    exit();
}

$eleve_id = $_SESSION['utilisateur']['id'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: mes_interro.php?invalid=ok");
    exit();
}

$quiz_id = (int) $_GET['id'];

// Vérifier si déjà fait
$stmt = $pdo->prepare("SELECT COUNT(*) FROM resultats WHERE utilisateur_id = ? AND quiz_id = ?");
$stmt->execute([$eleve_id, $quiz_id]);
if ($stmt->fetchColumn() > 0) {
    header("Location: mes_interro.php?deja=ok");
    exit();
}

// Vérifier que le quiz est actif et autorisé pour la catégorie
$categorie_stmt = $pdo->prepare("SELECT categorie_activite FROM eleves WHERE utilisateur_id = ?");
$categorie_stmt->execute([$eleve_id]);
$categorie = $categorie_stmt->fetchColumn();

$quiz_stmt = $pdo->prepare("SELECT * FROM quiz WHERE id = ? AND statut = 'actif' AND categorie = ?");
$quiz_stmt->execute([$quiz_id, $categorie]);
$quiz = $quiz_stmt->fetch();

if (!$quiz) {
    echo "<div style='padding:20px;'>Aucune interrogation disponible ou non autorisée.</div>";
    exit();
}

// Insérer un enregistrement dans interrogation_utilisateur si pas encore fait
$verif_stmt = $pdo->prepare("SELECT COUNT(*) FROM interrogation_utilisateur WHERE utilisateur_id = ? AND quiz_id = ?");
$verif_stmt->execute([$eleve_id, $quiz_id]);
if ($verif_stmt->fetchColumn() == 0) {
    $insert = $pdo->prepare("INSERT INTO interrogation_utilisateur (utilisateur_id, quiz_id, etat, debut) VALUES (?, ?, 'en_cours', NOW())");
    $insert->execute([$eleve_id, $quiz_id]);
}

// Générer aléatoirement les 10 questions uniquement en session
if (!isset($_SESSION['quiz_id']) || $_SESSION['quiz_id'] !== $quiz_id) {
    $qstmt = $pdo->prepare("SELECT * FROM questions WHERE categorie = ? ORDER BY RAND() LIMIT 10");
    $qstmt->execute([$categorie]);
    $questions = $qstmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$questions || count($questions) === 0) {
        echo "Aucune question trouvée pour ce quiz.";
        exit();
    }

    // Initialisation des sessions
    $_SESSION['questions'] = $questions;
    $_SESSION['quiz_start_time'] = time();
    $_SESSION['quiz_duree'] = $quiz['temps_par_question'] * 60; // en secondes
    $_SESSION['quiz_id'] = $quiz_id;
    $_SESSION['interrogation_en_cours'] = true;
    $_SESSION['reponses'] = [];
}

header("Location: faire_quiz.php?page=1");
exit();
