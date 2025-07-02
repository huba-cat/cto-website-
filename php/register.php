<?php
// Start session
session_start();

// Include database connection
require_once 'config.php';

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    
    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "All fields are required";
        exit;
    }
    
    if ($password !== $confirmPassword) {
        echo "Passwords do not match";
        exit;
    }
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            // Username already taken
            echo "<script>alert('Username already taken. Please choose another.'); window.location.href='../register.html';</script>";
            exit;
        }
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $result = $stmt->execute([$username, $email, $hashedPassword]);
        
        if ($result) {
            // Registration successful, redirect to login page
            echo "<script>alert('Registration successful! Please log in.'); window.location.href='../login.html';</script>";
            exit;
        } else {
            // Registration failed
            echo "<script>alert('Registration failed. Please try again.'); window.location.href='../register.html';</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // If not a POST request, redirect to registration page
    header("Location: ../register.html");
    exit;
}
?>