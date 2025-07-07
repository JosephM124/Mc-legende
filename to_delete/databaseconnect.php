<?php
require_once(__DIR__ . '/config/mysql.php');
//    sprintf('mysql:host=%s;dbname=%s;port=%s;charset=utf8', MYSQL_HOST, MYSQL_NAME, MYSQL_PORT),
        // MYSQL_USER,
        // MYSQL_PASSWORD
try {
    $pdo = new PDO('mysql:host='.MYSQL_HOST.';dbname='.MYSQL_NAME.';charset=utf8', MYSQL_USER, MYSQL_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $exception) {
    die('Erreur : ' . $exception->getMessage());
}

