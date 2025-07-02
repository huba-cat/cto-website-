document.addEventListener('DOMContentLoaded', function() {
    fetch('php/check_login.php')
        .then(response => response.json())
        .then(data => {
            const nav = document.querySelector('nav ul');
            if (nav) {
                if (data.loggedIn) {
                    // Add logout button
                    const logoutItem = document.createElement('li');
                    logoutItem.innerHTML = `<a href="#" id="logout-btn">Logout (${data.username})</a>`;
                    nav.appendChild(logoutItem);
                    
                    // Add event listener for logout
                    document.getElementById('logout-btn').addEventListener('click', function(e) {
                        e.preventDefault();
                        logout();
                    });
                    
                    // Show post link
                    document.querySelector('a[href="post.html"]').style.display = 'block';
                } else {
                    // Hide post link if not logged in
                    document.querySelector('a[href="post.html"]').style.display = 'none';
                }
            }
        });
});

function logout() {
    fetch('php/logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'index.html';
            }
        });
}