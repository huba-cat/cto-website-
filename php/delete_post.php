<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to delete observations']);
    exit;
}

// Include database connection
require_once 'config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if post ID is provided
if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $userId = $_SESSION['user_id'];
    
    try {
        // First, get the post to check ownership and get image path
        $stmt = $conn->prepare("SELECT * FROM posts WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        
        if ($stmt->rowCount() != 1) {
            echo json_encode(['success' => false, 'message' => 'Post not found or you do not have permission to delete it']);
            exit;
        }
        
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        $imagePath = $post['image_path'];
        
        // Delete post from database
        $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ? AND user_id = ?");
        $result = $stmt->execute([$postId, $userId]);
        
        if ($result) {
            // Delete associated image if exists
            if ($imagePath && file_exists('../' . $imagePath)) {
                unlink('../' . $imagePath);
            }
            
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Post ID is required']);
}
?>