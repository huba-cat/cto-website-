<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit;
}

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $location = $_POST['location'];
    $observationTime = $_POST['observation_time'];
    $observationDate = $_POST['observation_date'];
    $birdSpecies = $_POST['bird_species'];
    $activity = $_POST['activity'];
    $duration = $_POST['duration'];
    $comments = $_POST['comments'];
    $imagePath = null;
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        
        if (!in_array($image['type'], $allowedTypes)) {
            $_SESSION['error'] = 'Only JPG and PNG images are allowed';
            header("Location: ../post.html");
            exit;
        }
        
        $maxSize = 1.2 * 1024 * 1024;
        if ($image['size'] > $maxSize) {
            $_SESSION['error'] = 'Image size must be less than 1.2MB';
            header("Location: ../post.html");
            exit;
        }
        
        $uploadDir = '../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($image['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/' . $filename;
        }
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, username, location, observation_time, observation_date, bird_species, activity, duration, comments, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $username, $location, $observationTime, $observationDate, $birdSpecies, $activity, $duration, $comments, $imagePath]);
        
        $_SESSION['success'] = 'Observation posted successfully!';
        header("Location: ../view_posts.html");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to post observation. Please try again.';
        header("Location: ../post.html");
        exit;
    }
} else {
    header("Location: ../post.html");
    exit;
}
?>