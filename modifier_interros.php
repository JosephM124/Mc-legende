<?php
session_start();
require_once 'databaseconnect.php';
require_once 'log_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $nom = $_POST['nom'];
  $categorie = $_POST['categorie'];
  $date_lancement = $_POST['date_lancement'];
  $duree_total = $_POST['duree_total'];
  $duree_par_question = $_POST['duree_par_question'];

  $stmt = $pdo->prepare("UPDATE quiz SET titre = ?, categorie = ?, date_lancement = ?, duree_totale = ?, temps_par_question = ? WHERE id = ?");
  $stmt->execute([$nom, $categorie, $date_lancement, $duree_total, $duree_par_question, $id]);

enregistrer_activite_admin($_SESSION['utilisateur']['id'], "Modification d'une interrogation", "Détails : $nom,  $categorie");

  header("Location: interro_admin.php?modif=ok");
  exit;
}
?>
