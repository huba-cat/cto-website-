<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

echo json_encode([
    'authenticated' => isset($_SESSION['user_id']),
    'username' => $_SESSION['username'] ?? null
]);
?>