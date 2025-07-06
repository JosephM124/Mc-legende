<?php 
   require './Utilisateurs.php';
   class Eleve extends Utilisateurs
   {
      private $id;
      private $utilsateur_id;
      private $etablissement;
      private $section;
      private $adresse_ecole;
      private $categorie;
      private $pays;
      private $ville_province;

   }

   $eleve = new Eleve();

   var_dump($eleve->setTable('eleves')->all());
?>