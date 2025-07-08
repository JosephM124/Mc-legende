<?php
require_once(__DIR__ . '/config/Config.php');
//    sprintf('mysql:host=%s;dbname=%s;port=%s;charset=utf8', MYSQL_HOST, MYSQL_NAME, MYSQL_PORT),
        // MYSQL_USER,
        // MYSQL_PASSWORD
try {
    $pdo = new PDO('mysql:host='.\Config\MYSQL_HOST.';dbname='.\Config\MYSQL_NAME.';charset=utf8', \Config\MYSQL_USER, \Config\MYSQL_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $exception) {
    die('Erreur : ' . $exception->getMessage());
}

