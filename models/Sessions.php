
<?php 

   class Sessions extends \Models\Database 
   {
    private $id;
    private $user_id_tokken;
    private $ip_address;
    private $user_agent;
    private \DateTime $date_debut;
    private \DateTime $date_fin;
    private $active;
    private $created_at;
    private $updated_at;
    }
?>