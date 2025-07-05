<?php
session_start();
require_once 'databaseconnect.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'eleve') {
    http_response_code(403);
    exit;
}

$eleve_id = $_SESSION['utilisateur']['id'];

// 1. Trouver l'interrogation en cours (sécurité renforcée)
$stmt = $pdo->prepare("SELECT quiz_id FROM interrogation_utilisateur WHERE utilisateur_id = ? AND etat = 'en_cours' ORDER BY debut DESC LIMIT 1");
$stmt->execute([$eleve_id]);
$quiz_id = $stmt->fetchColumn();

if ($quiz_id) {
    // 2. Marquer résultat = triche
    $stmt = $pdo->prepare("INSERT INTO resultats (utilisateur_id, quiz_id, score, statut, date_passage)
                           VALUES (?, ?, 0, 0, NOW())");
    $stmt->execute([$eleve_id, $quiz_id]);

    // 3. Marquer l'interrogation comme trichée
    $stmt = $pdo->prepare("UPDATE interrogation_utilisateur SET etat = 'triche', fin = NOW() WHERE utilisateur_id = ? AND quiz_id = ?");
    $stmt->execute([$eleve_id, $quiz_id]);
}

// 4. Nettoyage session
unset($_SESSION['quiz_id'], $_SESSION['quiz_start_time'], $_SESSION['quiz_duree'], $_SESSION['questions'], $_SESSION['reponses'], $_SESSION['interrogation_en_cours']);

// 5. Si c'est un appel AJAX (ex: sendBeacon), ne rien afficher
if (!empty($_SERVER['HTTP_SEC_FETCH_MODE']) && $_SERVER['HTTP_SEC_FETCH_MODE'] === 'no-cors') {
    http_response_code(204); // No Content
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Triche détectée</title>
    <link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">
    <div class="content-wrapper">
        <div class="container p-5 text-center">
            <h2 class="text-danger">🚫 Triche détectée</h2>
            <p>Vous avez quitté, caché ou fermé la page durant l’interrogation.<br>Le système a mis fin à votre session.</p>
            <a href="eleve.php" class="btn btn-primary">Retour au tableau de bord</a>
        </div>
    </div>
</div>
</body>
</html>
