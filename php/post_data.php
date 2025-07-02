<?php
// Start session
session_start();
echo "Logged in as: " . $_SESSION['username'];


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to submit observations'); window.location.href='../login.html';</script>";
    exit;
}

// Include database connection
require_once 'config.php';

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $location = $_POST['location'];
    $observationTime = $_POST['observation_time'];
    $observationDate = $_POST['observation_date'];
    $birdSpecies = $_POST['bird_species'];
    $activity = $_POST['activity'];
    $duration = $_POST['duration'];
    $comments = $_POST['comments'];
    
    // Initialize image path variable
    $imagePath = null;
    
    // Check if image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        
        // Validate image type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($image['type'], $allowedTypes)) {
            echo "<script>alert('Only JPG and PNG images are allowed'); window.location.href='../post.html';</script>";
            exit;
        }
        
        // Validate image size (max 1.2MB)
        $maxSize = 1.2 * 1024 * 1024; // 1.2 MB in bytes
        $minSize = 10 * 1024;         // 10 KB in bytes
        
        if ($image['size'] > $maxSize) {
            echo "<script>alert('Image size must be less than 1.2MB'); window.location.href='../post.html';</script>";
            exit;
        }
        
        if ($image['size'] < $minSize) {
            echo "<script>alert('Image size must be at least 10KB'); window.location.href='../post.html';</script>";
            exit;
        }
        
        
        // Create uploads directory if it doesn't exist
        $uploadDir = '../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($image['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/' . $filename;
        } else {
            echo "<script>alert('Failed to upload image'); window.location.href='../post.html';</script>";
            exit;
        }
    }
    
    try {
        // Insert post into database
        $stmt = $conn->prepare("INSERT INTO posts (user_id, username, location, observation_time, observation_date, bird_species, activity, duration, comments, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$userId, $username, $location, $observationTime, $observationDate, $birdSpecies, $activity, $duration, $comments, $imagePath]);
        
        if ($result) {
            // Post successful, redirect to view posts
            echo "<script>alert('Observation posted successfully!'); window.location.href='../view_posts.html';</script>";
            exit;
        } else {
            // Post failed
            echo "<script>alert('Failed to post observation. Please try again.'); window.location.href='../post.html';</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // If not a POST request, redirect to post form
    header("Location: ../post.html");
    exit; 
}
?>