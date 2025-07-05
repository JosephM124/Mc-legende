<?php
require_once 'databaseconnect.php';

function enregistrer_activite_admin($admin_id, $action, $details = null) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO activites_admin (admin_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$admin_id, $action, $details]);
}
