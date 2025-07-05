<?php
function redirectToUrl(string $url): never
{ header ("Location: {$url}");
exit();
}

function is_active($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}


// Fonction utilitaire (à ajouter dans fonctions.php)
function time_to_seconds($time) {
    $parts = explode(':', $time);
    return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
}

function getUnreadNotificationsCount($pdo, $user_id) {
    $req = $pdo->prepare("SELECT COUNT(*) FROM notifications 
                         WHERE utilisateur_id = ? AND lue = 0");
    $req->execute([$user_id]);
    return $req->fetchColumn();
}

function getRecentNotifications($pdo, $user_id, $limit = 5) {
    $req = $pdo->prepare("SELECT n.*, 
                         DATE_FORMAT(n.date_creation, '%d/%m/%Y %H:%i') as date_fr,
                         q.titre as quiz_titre
                         FROM notifications n
                         LEFT JOIN quiz q ON n.quiz_id = q.id
                         WHERE n.utilisateur_id = :user_id 
                         ORDER BY n.date_creation DESC 
                         LIMIT :limit");
    
    // Utilisation de bindValue pour les paramètres
    $req->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $req->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}function addNotification($pdo, $user_id, $type, $titre, $message, $lien = null, $quiz_id = null) {
    $req = $pdo->prepare("INSERT INTO notifications 
                         (utilisateur_id, quiz_id, type, titre, message, lien)
                         VALUES (?, ?, ?, ?, ?, ?)");
    $req->execute([$user_id, $quiz_id, $type, $titre, $message, $lien]);
    return $pdo->lastInsertId();
}
function filtrer_var($var) {
    return htmlspecialchars(trim($var));
}
function filtrer($valeur) {
    return htmlspecialchars(trim($valeur));
}

function addNotificationPourCategorie($pdo, $categorie, $type, $titre, $message, $url_action, $quiz_id = null) {
    $eleves = $pdo->prepare("SELECT id FROM utilisateurs WHERE role = 'eleve' AND categorie = ?");
    $eleves->execute([$categorie]);
    $liste_eleves = $eleves->fetchAll();

    $req = $pdo->prepare("
        INSERT INTO notifications (utilisateur_id, type, titre, message, lien, quiz_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($liste_eleves as $eleve) {
        $req->execute([
            $eleve['id'],
            $type,
            $titre,
            $message,
            $url_action,
            $quiz_id
        ]);
    }
}

// --- CSRF protection utils ---
function generer_csrf_token($action) {
    if (!isset($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_tokens'][$action][$token] = time();
    return $token;
}

function verifier_csrf_token($token, $action) {
    if (!isset($_SESSION['csrf_tokens'][$action][$token])) {
        return false;
    }
    // Optionnel : expiration (ex: 10 min)
    if ($_SESSION['csrf_tokens'][$action][$token] < (time() - 600)) {
        unset($_SESSION['csrf_tokens'][$action][$token]);
        return false;
    }
    return true;
}

function supprimer_csrf_token($token, $action) {
    if (isset($_SESSION['csrf_tokens'][$action][$token])) {
        unset($_SESSION['csrf_tokens'][$action][$token]);
    }
}

?>

