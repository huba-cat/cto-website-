// js/posts.js

// Function to create a post element
function createPostElement(post) {
    const postElement = document.createElement('div');
    postElement.className = 'post-card';
    
    // Format the date and time
    const observationDateTime = new Date(`${post.observation_date}T${post.observation_time}`);
    const formattedDate = observationDateTime.toLocaleDateString();
    const formattedTime = observationDateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    // Create post HTML
    postElement.innerHTML = `
        <div class="post-header">
            <span class="post-user">${post.username}</span>
            <span class="post-date">${formattedDate} at ${formattedTime}</span>
        </div>
        
        <div class="post-details">
            <div class="detail-item">
                <div class="detail-label"><i class="fas fa-map-marker-alt"></i> Location</div>
                <div class="detail-value">${post.location}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><i class="fas fa-dove"></i> Bird Species</div>
                <div class="detail-value">${post.bird_species}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><i class="fas fa-running"></i> Activity</div>
                <div class="detail-value">${post.activity}</div>
            </div>
            
            <div class="detail-item">
                <div class="detail-label"><i class="fas fa-clock"></i> Duration</div>
                <div class="detail-value">${post.duration}</div>
            </div>
        </div>
        
        ${post.image_path ? `
        <div class="post-image-container">
            <img src="${post.image_path}" alt="Bird observation" class="post-image">
        </div>
        ` : ''}
        
        ${post.comments ? `
        <div class="post-comments">
            <div class="detail-label"><i class="fas fa-comment"></i> Notes</div>
            <div class="detail-value">${post.comments}</div>
        </div>
        ` : ''}
    `;
    
    return postElement;
}

// Load posts when the page loads
document.addEventListener('DOMContentLoaded', function() {
    loadPosts();
    
    // Add search functionality
    document.getElementById('search-button').addEventListener('click', function() {
        loadPosts(document.getElementById('search-input').value);
    });
    
    document.getElementById('search-input').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            loadPosts(this.value);
        }
    });
});

function loadPosts(searchTerm = '') {
    const postsContainer = document.getElementById('posts-container');
    postsContainer.innerHTML = '<div class="loading"><p><i class="fas fa-spinner fa-spin"></i> Loading observations...</p></div>';
    
    let url = 'php/get_posts.php';
    if (searchTerm) {
        url += `?search=${encodeURIComponent(searchTerm)}`;
    }
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data); // Debug log
            
            postsContainer.innerHTML = '';
            
            if (data.length === 0 || (data.message && data.message === 'No posts found')) {
                postsContainer.innerHTML = '<div class="no-posts"><p><i class="fas fa-binoculars"></i> No observations found</p></div>';
                return;
            }
            
            if (Array.isArray(data)) {
                data.forEach(post => {
                    const postElement = createPostElement(post);
                    postsContainer.appendChild(postElement);
                });
            } else {
                postsContainer.innerHTML = '<div class="no-posts"><p><i class="fas fa-exclamation-triangle"></i> Unexpected data format</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            postsContainer.innerHTML = `<div class="no-posts"><p><i class="fas fa-exclamation-triangle"></i> Error loading observations: ${error.message}</p></div>`;
        });
}