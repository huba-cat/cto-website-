<?php
session_start();
require_once 'config.php';

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

try {
    if ($searchTerm) {
        $searchTerm = "%$searchTerm%";
        $stmt = $conn->prepare("SELECT * FROM posts 
                               WHERE location LIKE ? OR 
                                     bird_species LIKE ? OR 
                                     activity LIKE ? OR 
                                     comments LIKE ?
                               ORDER BY created_at DESC");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM posts ORDER BY created_at DESC");
        $stmt->execute();
    }
    
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($posts as $post) {
        echo '<div class="post-card">';
        echo '<div class="post-header">';
        echo '<span class="post-user">' . htmlspecialchars($post['username']) . '</span>';
        echo '<span class="post-date">' . htmlspecialchars($post['observation_date']) . ' ' . htmlspecialchars($post['observation_time']) . '</span>';
        echo '</div>';
        
        if ($post['image_path']) {
            echo '<img src="' . htmlspecialchars($post['image_path']) . '" class="post-image">';
        }
        
        echo '<div class="post-details">';
        echo '<div class="detail-item"><span class="detail-label">Location:</span> ' . htmlspecialchars($post['location']) . '</div>';
        echo '<div class="detail-item"><span class="detail-label">Species:</span> ' . htmlspecialchars($post['bird_species']) . '</div>';
        echo '<div class="detail-item"><span class="detail-label">Activity:</span> ' . htmlspecialchars($post['activity']) . '</div>';
        echo '<div class="detail-item"><span class="detail-label">Duration:</span> ' . htmlspecialchars($post['duration']) . ' minutes</div>';
        echo '</div>';
        
        if ($post['comments']) {
            echo '<div class="post-comments">' . htmlspecialchars($post['comments']) . '</div>';
        }
        
        // Add edit/delete buttons if the post belongs to the current user
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
            echo '<div class="action-buttons">';
            echo '<a href="edit_post.html?id=' . $post['post_id'] . '" class="btn btn-edit">Edit</a>';
            echo '<button onclick="deletePost(' . $post['post_id'] . ')" class="btn btn-delete">Delete</button>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    
} catch (PDOException $e) {
    echo '<div class="error">Error loading posts: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>