<?php 
  namespace Models;

   class Notifications
   {
      private int  $id,$utilisateur_id	,$quiz_id,$type;
      private $titre,$message,$lien	,$lue ,$date_creation,	$role_destinataire;
      private $est_generale,$categorie;
   }
?> 