<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to edit observations'); window.location.href='../login.html';</script>";
    exit;
}

// Include database connection
require_once 'config.php';

// Process GET request to retrieve post data
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $postId = $_GET['id'];
    $userId = $_SESSION['user_id'];
    
    try {
        // Get post data
        $stmt = $conn->prepare("SELECT * FROM posts WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        
        if ($stmt->rowCount() == 1) {
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            // Return post data as JSON
            header('Content-Type: application/json');
            echo json_encode($post);
            exit;
        } else {
            // Post not found or not owned by user
            echo json_encode(['error' => 'Post not found or you do not have permission to edit it']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// Process POST request to update post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id'])) {
    $postId = $_POST['post_id'];
    $userId = $_SESSION['user_id'];
    
    // Get form data
    $location = $_POST['location'];
    $observationTime = $_POST['observation_time'];
    $observationDate = $_POST['observation_date'];
    $birdSpecies = $_POST['bird_species'];
    $activity = $_POST['activity'];
    $duration = $_POST['duration'];
    $comments = $_POST['comments'];
    
    try {
        // Check if post exists and belongs to user
        $stmt = $conn->prepare("SELECT * FROM posts WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        
        if ($stmt->rowCount() != 1) {
            echo "<script>alert('Post not found or you do not have permission to edit it'); window.location.href='../view_posts.html';</script>";
            exit;
        }
        
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        $imagePath = $post['image_path'];
        
        // Check if new image was uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $_FILES['image'];
            
            // Validate image type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($image['type'], $allowedTypes)) {
                echo "<script>alert('Only JPG and PNG images are allowed'); window.location.href='../edit_post.html?id=$postId';</script>";
                exit;
            }
            
            // Validate image size (max 1.2MB)
            $maxSize = 1.2 * 1024 * 1024; // 1.2MB in bytes
            if ($image['size'] > $maxSize) {
                echo "<script>alert('Image size must be less than 1.2MB'); window.location.href='../edit_post.html?id=$postId';</script>";
                exit;
            }
            
            // Create uploads directory if it doesn't exist
            $uploadDir = '../uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Delete old image if exists
            if ($imagePath && file_exists('../' . $imagePath)) {
                unlink('../' . $imagePath);
            }
            
            // Generate unique filename
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $targetPath = $uploadDir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                $imagePath = 'uploads/' . $filename;
            } else {
                echo "<script>alert('Failed to upload image'); window.location.href='../edit_post.html?id=$postId';</script>";
                exit;
            }
        }
        
        // Update post in database
        $stmt = $conn->prepare("UPDATE posts SET location = ?, observation_time = ?, observation_date = ?, bird_species = ?, activity = ?, duration = ?, comments = ?, image_path = ? WHERE post_id = ? AND user_id = ?");
        $result = $stmt->execute([$location, $observationTime, $observationDate, $birdSpecies, $activity, $duration, $comments, $imagePath, $postId, $userId]);
        
        if ($result) {
            // Update successful, redirect to view posts
            echo "<script>alert('Observation updated successfully!'); window.location.href='../view_posts.html';</script>";
            exit;
        } else {
            // Update failed
            echo "<script>alert('Failed to update observation. Please try again.'); window.location.href='../edit_post.html?id=$postId';</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // If not a valid request, redirect to view posts
    header("Location: ../view_posts.html");
    exit;
}
?>