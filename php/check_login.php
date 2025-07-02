<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'username' => null
];

if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
    $response['username'] = $_SESSION['username'];
}

echo json_encode($response);
?>