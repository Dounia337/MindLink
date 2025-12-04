// Handle like button clicks
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const postId = this.dataset.postId;
        const likeCountSpan = this.querySelector('.like-count');
        const svg = this.querySelector('svg');
        
        try {
            const response = await fetch('toggle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `post_id=${postId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                likeCountSpan.textContent = data.like_count;
                
                if (data.liked) {
                    this.classList.add('liked');
                    svg.setAttribute('fill', 'currentColor');
                    // Add animation
                    this.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 200);
                } else {
                    this.classList.remove('liked');
                    svg.setAttribute('fill', 'none');
                }
            }
        } catch (error) {
            console.error('Error toggling like:', error);
        }
    });
});

// Handle comment button clicks
document.querySelectorAll('.comment-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const postId = this.dataset.postId;
        window.location.href = `post_detail.php?id=${postId}`;
    });
});

// Refresh feed with new random order
function refreshFeed() {
    const newSeed = Math.floor(Math.random() * 1000000);
    window.location.href = `feed.php?seed=${newSeed}`;
}