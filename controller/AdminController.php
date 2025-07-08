<?php 
namespace Controllers;

   class AdminController extends \Controllers\BaseController{
    
    public function enregistrer_activite_admin($admin_id, $action, $details = null) {
        $this->database->prepare(
          "INSERT INTO activites_admin (admin_id, action, details) VALUES (?, ?, ?)",
          [$admin_id, $action, $details]
        );
    }

   }
?>