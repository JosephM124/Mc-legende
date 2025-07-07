<?php
require_once dirname(__DIR__, 2) . '/controller/UtilisateursController.php';
require_once dirname(__DIR__, 2) . '/middleware/CorsMiddleware.php';

// Configuration CORS
$cors = new CorsMiddleware();
$cors->configureForDevelopment();

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new UtilisateursController();
    $controller->login();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
}
?> 