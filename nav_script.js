// Function to update navigation based on login status
function updateNavigation() {
    // Check if user is logged in (by looking for a session cookie)
    const isLoggedIn = document.cookie.includes('PHPSESSID');
    
    // Get all navigation elements
    const navContainer = document.querySelector('nav ul');
    
    if (navContainer) {
        // Clear existing navigation
        navContainer.innerHTML = `
            <li><a href="index.html">Home</a></li>
        `;
        
        if (isLoggedIn) {
            // User is logged in
            navContainer.innerHTML += `
                <li><a href="post.html">New Observation</a></li>
                <li><a href="view_posts.html">View Observations</a></li>
                <li><a href="php/logout.php">Logout</a></li>
            `;
        } else {
            // User is not logged in
            navContainer.innerHTML += `
                <li><a href="register.html">Register</a></li>
                <li><a href="login.html">Login</a></li>
                <li><a href="view_posts.html">View Observations</a></li>
            `;
        }
    }
}

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', updateNavigation);