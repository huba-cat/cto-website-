<?php
require_once 'php/config.php';

try {
    // Database connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Search functionality
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

    if ($searchTerm) {
        $stmt = $conn->prepare("
            SELECT * FROM posts 
            WHERE bird_species LIKE :search 
               OR location LIKE :search 
               OR activity LIKE :search 
               OR comments LIKE :search
            ORDER BY observation_date DESC, observation_time DESC
        ");
        $searchParam = "%$searchTerm%";
        $stmt->bindParam(':search', $searchParam);
    } else {
        $stmt = $conn->prepare("SELECT * FROM posts ORDER BY observation_date DESC, observation_time DESC");
    }

    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Observations - Centrala Trust for Ornithology</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 10px;
            text-align: center;
        }
        img {
            max-width: 120px;
            height: auto;
        }
        .search-container {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 300px;
        }
        button {
            padding: 8px 15px;
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<header>
    <h1>Centrala Trust for Ornithology</h1>
</header>

<nav>
    <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="register.html">Register</a></li>
        <li><a href="login.html">Login</a></li>
        <li><a href="post.html">New Observation</a></li>
        <li><a href="view_posts.php">View Observations</a></li>
    </ul>
</nav>

<div class="container">
    <h2>Bird Observations</h2>

    <form method="get" class="search-container">
        <input type="text" name="search" placeholder="Search by bird species, location, activity..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($posts): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Bird Species</th>
                <th>Location</th>
                <th>Activity</th>
                <th>Observation Date</th>
                <th>Observation Time</th>
                <th>Comments</th>
                <th>Image</th>
            </tr>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['id']); ?></td>
                    <td><?php echo htmlspecialchars($post['bird_species']); ?></td>
                    <td><?php echo htmlspecialchars($post['location']); ?></td>
                    <td><?php echo htmlspecialchars($post['activity']); ?></td>
                    <td><?php echo htmlspecialchars($post['observation_date']); ?></td>
                    <td><?php echo htmlspecialchars($post['observation_time']); ?></td>
                    <td><?php echo htmlspecialchars($post['comments'] ?: 'N/A'); ?></td>
                    <td>
                        <?php if (!empty($post['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Bird Image">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No observations found.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2025 Centrala Trust for Ornithology. All rights reserved.</p>
</footer>

</body>
</html>
