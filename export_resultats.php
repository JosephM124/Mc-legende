<?php
require_once 'databaseconnect.php';

if (!isset($_GET['quiz_id'])) {
    die("ID d'interrogation manquant.");
}

$interro_id = (int)$_GET['quiz_id'];

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=resultats_interrogation_$interro_id.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "Nom\tPrénom\tScore\tDate de passage\n";

$stmt = $pdo->prepare("
  SELECT u.nom, u.prenom, r.score, r.date_passage 
  FROM resultats r
  JOIN utilisateurs u ON u.id = r.utilisateur_id
  WHERE r.quiz_id = ?
");
$stmt->execute([$interro_id]);

while ($row = $stmt->fetch()) {
    echo "{$row['nom']}\t{$row['prenom']}\t{$row['score']}\t" . date('d/m/Y H:i', strtotime($row['date_passage'])) . "\n";
}
