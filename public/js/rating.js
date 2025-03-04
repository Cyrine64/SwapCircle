document.addEventListener('DOMContentLoaded', function() {
    initializeRating();
});

function initializeRating() {
    const ratingContainers = document.querySelectorAll('.rating');
    
    ratingContainers.forEach(container => {
        const stars = container.querySelectorAll('.star');
        const blogId = container.dataset.blogId;
        const currentRating = parseFloat(container.dataset.rating) || 0;
        
        // Initial state
        updateStars(container, currentRating);
        
        // Add click events to stars
        stars.forEach(star => {
            star.addEventListener('click', async () => {
                const rating = parseInt(star.dataset.value);
                
                try {
                    const response = await fetch(`/blog/${blogId}/rate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `rating=${rating}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Update the display
                        updateStars(container, data.newRating);
                        updateRatingInfo(container, data.newRating, data.ratingCount);
                        
                        // Show success message
                        showNotification('Thank you for your rating!');
                    }
                } catch (error) {
                    console.error('Error rating blog:', error);
                    showNotification('Error submitting rating. Please try again.', 'error');
                }
            });
            
            // Hover effects
            star.addEventListener('mouseenter', () => {
                const rating = parseInt(star.dataset.value);
                highlightStars(container, rating);
            });
        });
        
        // Reset stars on mouse leave
        container.addEventListener('mouseleave', () => {
            const currentRating = parseFloat(container.dataset.rating) || 0;
            updateStars(container, currentRating);
        });
    });
}

function updateStars(container, rating) {
    const stars = container.querySelectorAll('.star');
    stars.forEach((star, index) => {
        const value = index + 1;
        if (value <= rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
    container.dataset.rating = rating;
}

function highlightStars(container, rating) {
    const stars = container.querySelectorAll('.star');
    stars.forEach((star, index) => {
        const value = index + 1;
        if (value <= rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

function updateRatingInfo(container, rating, count) {
    const averageRating = container.querySelector('.average-rating');
    const ratingCount = container.querySelector('.rating-count');
    
    if (averageRating) {
        averageRating.textContent = rating.toFixed(1);
    }
    if (ratingCount) {
        ratingCount.textContent = `(${count} votes)`;
    }
}

function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `rating-notification ${type}`;
    notification.textContent = message;
    
    // Style the notification
    Object.assign(notification.style, {
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        padding: '10px 20px',
        borderRadius: '5px',
        backgroundColor: type === 'success' ? '#4caf50' : '#f44336',
        color: 'white',
        zIndex: '1000',
        opacity: '0',
        transition: 'opacity 0.3s ease'
    });
    
    // Add to document
    document.body.appendChild(notification);
    
    // Fade in
    setTimeout(() => {
        notification.style.opacity = '1';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
