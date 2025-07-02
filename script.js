// Main JavaScript file for CTO Bird Observation Website

// Function to validate registration form
function validateRegistration() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const email = document.getElementById('email').value;
    
    if (username.length < 3) {
        alert('Username must be at least 3 characters long');
        return false;
    }
    
    if (password.length < 6) {
        alert('Password must be at least 6 characters long');
        return false;
    }
    
    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return false;
        
    }
    
    if (!email.includes('@')) {
        alert('Please enter a valid email address');
        return false;
    }
    
    return true;
}

// Function to validate login form
function validateLogin() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    if (username.trim() === '' || password.trim() === '') {
        alert('Username and password are required');
        return false;
    }
    
    return true;
}

// Function to validate post form
function validatePost() {
    const location = document.getElementById('location').value;
    const observationTime = document.getElementById('observation-time').value;
    const observationDate = document.getElementById('observation-date').value;
    const birdSpecies = document.getElementById('bird-species').value;
    const activity = document.getElementById('activity').value;
    const duration = document.getElementById('duration').value;
    
    if (location === '') {
        alert('Please select a location');
        return false;
    }
    
    if (observationTime === '') {
        alert('Please enter observation time');
        return false;
    }
    
    if (observationDate === '') {
        alert('Please enter observation date');
        return false;
    }
    
    if (birdSpecies === '') {
        alert('Please select a bird species');
        return false;
    }
    
    if (activity === '') {
        alert('Please select an activity');
        return false;
    }
    
    if (duration === '' || duration <= 0) {
        alert('Please enter a valid duration');
        return false;
    }
    
    return true;
}

// Function to prepare edit post form
function editPost(postId) {
    // Redirect to edit page with the post ID
    window.location.href = `edit_post.html?id=${postId}`;
}

// Function to confirm and delete post
function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        // Send request to delete post
        fetch(`php/delete_post.php?id=${postId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove post from DOM
                document.getElementById(`post-${postId}`).remove();
                alert('Post deleted successfully');
            } else {
                alert('Error deleting post');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting post');
        });
    }
}

// Function to search posts
function searchPosts() {
    const searchTerm = document.getElementById('search-input').value;
    
    // Clear previous results
    const postsContainer = document.getElementById('posts-container');
    postsContainer.innerHTML = '<p>Searching...</p>';
    
    // Send search request
    fetch(`php/get_posts.php?search=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            postsContainer.innerHTML = '';
            
            if (data.length === 0) {
                postsContainer.innerHTML = '<p>No posts found</p>';
                return;
            }
            
            // Display search results
            data.forEach(post => {
                const postElement = createPostElement(post);
                postsContainer.appendChild(postElement);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            postsContainer.innerHTML = '<p>Error searching posts</p>';
        });
}

// Function to create post element
function createPostElement(post) {
    const postDiv = document.createElement('div');
    postDiv.className = 'post-card';
    postDiv.id = `post-${post.post_id}`;
    
    // Create post content
    postDiv.innerHTML = `
        <div class="post-header">
            <div>
                <strong>${post.username}</strong> - ${post.location}
            </div>
            <div>
                ${formatDate(post.observation_date)} ${post.observation_time}
            </div>
        </div>
        <div class="post-details">
            <p><strong>Bird Species:</strong> ${post.bird_species}</p>
            <p><strong>Activity:</strong> ${post.activity}</p>
            <p><strong>Duration:</strong> ${post.duration} minutes</p>
            <p><strong>Comments:</strong> ${post.comments}</p>
        </div>
        ${post.image_path ? `<img src="${post.image_path}" alt="Bird observation" class="post-image">` : ''}
        ${post.is_author ? `
            <div class="post-actions">
                <button class="btn" onclick="editPost(${post.post_id})">Edit</button>
                <button class="btn btn-danger" onclick="deletePost(${post.post_id})">Delete</button>
            </div>
        ` : ''}
    `;
    
    return postDiv;
}

// Helper function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

// Add event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Registration form validation
    const registrationForm = document.getElementById('registration-form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            if (!validateRegistration()) {
                e.preventDefault();
            }
        });
    }
    
    // Login form validation
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLogin()) {
                e.preventDefault();
            }
        });
    }
    
    // Post form validation
    const postForm = document.getElementById('post-form');
    if (postForm) {
        postForm.addEventListener('submit', function(e) {
            if (!validatePost()) {
                e.preventDefault();
            }
        });
    }
    
    // Search button
    const searchButton = document.getElementById('search-button');
    if (searchButton) {
        searchButton.addEventListener('click', searchPosts);
    }
    
    // Search input - allow search on Enter key
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchPosts();
            }
        });
    }
});