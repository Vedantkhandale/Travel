// TravelBlog - Index Page JavaScript
// Smooth Scroll Navbar
window.onscroll = () => {
    const nav = document.getElementById('mainNav');
    if (window.scrollY > 50) nav.classList.add('scrolled');
    else nav.classList.remove('scrolled');
};

// Mobile Menu Toggle logic
const menuToggle = document.getElementById('mobile-menu');
const navLinks = document.getElementById('navLinks');

menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    const icon = menuToggle.querySelector('i');
    icon.classList.toggle('fa-bars');
    icon.classList.toggle('fa-times');
});

function toggleTheme() {
    const body = document.body;
    body.classList.toggle("dark");
    const isDark = body.classList.contains("dark");
    document.querySelector("#themeBtn i").className = isDark ? "fas fa-sun" : "fas fa-moon";
    document.cookie = "theme=" + (isDark ? "dark" : "light") + ";path=/";
}

function toggleLike(el) {
    el.classList.toggle("active");
    const icon = el.querySelector("i");
    if (el.classList.contains("active")) {
        icon.className = "fas fa-heart";
    } else {
        icon.className = "far fa-heart";
    }
}

document.getElementById("searchInput").addEventListener("input", function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll(".card").forEach(card => {
        const title = card.getAttribute("data-title") || "";
        const description = card.getAttribute("data-description") || "";
        if (title.includes(query) || description.includes(query)) {
            card.classList.remove("hidden");
        } else {
            card.classList.add("hidden");
        }
    });
});

// Dynamic Stats Simulation
function updateStats() {
    const onlineUsers = document.getElementById('onlineUsers');
    const totalPosts = document.getElementById('totalPosts');
    const destinations = document.getElementById('destinations');
    const communities = document.getElementById('communities');

    // Simulate real-time changes
    let currentOnline = parseInt(onlineUsers.textContent.replace(',', ''));
    let currentPosts = parseInt(totalPosts.textContent.replace(',', ''));
    let currentDest = parseInt(destinations.textContent);
    let currentComm = parseInt(communities.textContent);

    // Random fluctuations
    currentOnline += Math.floor(Math.random() * 11) - 5; // -5 to +5
    currentPosts += Math.floor(Math.random() * 3) - 1; // -1 to +1
    currentDest += Math.floor(Math.random() * 3) - 1;
    currentComm += Math.floor(Math.random() * 5) - 2;

    // Keep within reasonable bounds
    currentOnline = Math.max(200, Math.min(300, currentOnline));
    currentPosts = Math.max(1200, Math.min(1300, currentPosts));
    currentDest = Math.max(80, Math.min(100, currentDest));
    currentComm = Math.max(140, Math.min(170, currentComm));

    // Update display with animation
    animateNumber(onlineUsers, currentOnline);
    animateNumber(totalPosts, currentPosts);
    animateNumber(destinations, currentDest);
    animateNumber(communities, currentComm);
}

function animateNumber(element, target) {
    const current = parseInt(element.textContent.replace(',', ''));
    const increment = target > current ? 1 : -1;
    const timer = setInterval(() => {
        const newValue = parseInt(element.textContent.replace(',', '')) + increment;
        element.textContent = newValue.toLocaleString();
        if (newValue === target) clearInterval(timer);
    }, 50);
}

// Update stats every 30 seconds
setInterval(updateStats, 30000);

// Floating Notifications
const notifications = [
    { user: "Sarah", action: "shared a new adventure in Bali", time: "2 min ago" },
    { user: "Mike", action: "liked your Tokyo story", time: "5 min ago" },
    { user: "Emma", action: "commented on your Paris post", time: "8 min ago" },
    { user: "Alex", action: "started following you", time: "12 min ago" },
    { user: "Lisa", action: "shared your Iceland guide", time: "15 min ago" }
];

function showRandomNotification() {
    const container = document.getElementById('notificationContainer');
    const randomNotif = notifications[Math.floor(Math.random() * notifications.length)];

    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.innerHTML = `
        <div class="notification-header">
            <div class="notification-avatar">${randomNotif.user.charAt(0)}</div>
            <strong>${randomNotif.user}</strong>
        </div>
        <div class="notification-content">${randomNotif.action}</div>
        <div class="notification-time">${randomNotif.time}</div>
    `;

    container.appendChild(notification);

    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);

    // Hide and remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}

// Show notifications randomly every 45-90 seconds
function scheduleNotification() {
    const delay = Math.random() * 45000 + 45000; // 45-90 seconds
    setTimeout(() => {
        showRandomNotification();
        scheduleNotification();
    }, delay);
}

// Start notifications after page load
setTimeout(scheduleNotification, 10000);

function handleCardButtons() {
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            const postId = btn.getAttribute('data-post-id');
            if (!postId) return;

            const icon = btn.querySelector('i');
            const countEl = btn.querySelector('.like-count');
            let count = parseInt(countEl.textContent) || 0;

            if (btn.classList.contains('active')) {
                btn.classList.remove('active');
                icon.className = 'far fa-heart';
                count = Math.max(0, count - 1);
            } else {
                btn.classList.add('active');
                icon.className = 'fas fa-heart';
                count += 1;
            }

            countEl.textContent = count;
            // localStorage state saving optional
            const stats = JSON.parse(localStorage.getItem('travelBlogLikeStats') || '{}');
            stats[postId] = count;
            localStorage.setItem('travelBlogLikeStats', JSON.stringify(stats));
            showTooltip(btn, count > 0 ? 'Liked!' : 'Unliked');
        });
    });

    document.querySelectorAll('.comment-btn').forEach(btn => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            const postId = btn.getAttribute('data-post-id');
            if (!postId) return;

            const comment = prompt('Add a quick comment:', 'Amazing experience! 🌟');
            if (!comment || !comment.trim()) return;

            const countEl = btn.querySelector('.comment-count');
            let count = parseInt(countEl.textContent) || 0;
            count += 1;
            countEl.textContent = count;

            const comments = JSON.parse(localStorage.getItem('travelBlogCommentStats') || '{}');
            comments[postId] = count;
            localStorage.setItem('travelBlogCommentStats', JSON.stringify(comments));
            showTooltip(btn, 'Comment added');
            toastNotification('Comment: ' + comment.trim().slice(0, 60));
        });
    });
}

function hydrateSavedCardStats() {
    const likes = JSON.parse(localStorage.getItem('travelBlogLikeStats') || '{}');
    const comments = JSON.parse(localStorage.getItem('travelBlogCommentStats') || '{}');

    document.querySelectorAll('.card').forEach(card => {
        const postId = card.getAttribute('data-post-id');
        if (!postId) return;

        if (likes[postId] !== undefined) {
            const likeBtn = card.querySelector('.like-btn');
            const countEl = likeBtn.querySelector('.like-count');
            countEl.textContent = likes[postId];
            if (likes[postId] > 0) {
                likeBtn.classList.add('active');
                likeBtn.querySelector('i').className = 'fas fa-heart';
            }
        }

        if (comments[postId] !== undefined) {
            const commentBtn = card.querySelector('.comment-btn');
            const countEl = commentBtn.querySelector('.comment-count');
            countEl.textContent = comments[postId];
        }
    });
}

// Recent Activity Feed
const activities = [
    { user: "Sarah", action: "shared a new adventure", location: "Bali", time: "2 minutes ago" },
    { user: "Mike", action: "discovered hidden gems in", location: "Tokyo", time: "5 minutes ago" },
    { user: "Emma", action: "posted amazing photos from", location: "Paris", time: "8 minutes ago" },
    { user: "Alex", action: "started exploring", location: "New York", time: "12 minutes ago" },
    { user: "Lisa", action: "shared food adventures in", location: "Thailand", time: "15 minutes ago" },
    { user: "David", action: "captured sunset views in", location: "Santorini", time: "18 minutes ago" },
    { user: "Anna", action: "found the best cafes in", location: "Barcelona", time: "22 minutes ago" },
    { user: "John", action: "hiked through mountains in", location: "Switzerland", time: "25 minutes ago" }
];

function populateActivityFeed() {
    const feed = document.getElementById('activityFeed');
    const shuffled = activities.sort(() => 0.5 - Math.random());
    const selected = shuffled.slice(0, 6);

    selected.forEach(activity => {
        const item = document.createElement('div');
        item.className = 'activity-item';
        item.innerHTML = `
            <div class="activity-avatar">${activity.user.charAt(0)}</div>
            <div class="activity-content">
                <div class="activity-text">
                    <strong>${activity.user}</strong> ${activity.action} <strong>${activity.location}</strong>
                </div>
                <div class="activity-time">${activity.time}</div>
            </div>
        `;
        feed.appendChild(item);
    });
}

// Update activity times
function updateActivityTimes() {
    const timeElements = document.querySelectorAll('.activity-time');
    timeElements.forEach(el => {
        const currentTime = el.textContent;
        const minutes = parseInt(currentTime.split(' ')[0]);
        el.textContent = `${minutes + 1} minutes ago`;
    });
}

// Update activity feed every 60 seconds
setInterval(updateActivityTimes, 60000);

window.onload = () => {
    populateActivityFeed();
    if (document.body.classList.contains("dark")) {
        document.querySelector("#themeBtn i").className = "fas fa-sun";
    }
    hydrateSavedCardStats();
    handleCardButtons();
}

function deletePost(postId) {
    const card = document.getElementById('post-' + postId);
    if (confirm("Are you sure? This memory will be gone forever! 🏝️")) {
        // Red glow effect before vanishing
        card.style.border = "2px solid #f43f5e";
        card.style.boxShadow = "0 0 20px rgba(244, 63, 94, 0.2)";

        fetch('delete-post.php?id=' + postId)
            .then(res => {
                card.classList.add('fade-out-card');
                setTimeout(() => card.remove(), 500);
            })
            .catch(err => alert("Kucch galat hua!"));
    }
}

// --- Loading Animation ---
function hideLoadingOverlay() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) {
        overlay.classList.add('hidden');
        setTimeout(() => overlay.remove(), 500);
    }
}

// Show loading overlay on page load
document.addEventListener('DOMContentLoaded', () => {
    // Create loading overlay
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = `
        <div class="loader">
            <div class="loader-spinner"></div>
            <div class="loader-text">Exploring the world...</div>
        </div>
    `;
    document.body.appendChild(overlay);

    // Hide loading after 2 seconds or when page is fully loaded
    setTimeout(hideLoadingOverlay, 2000);
    window.addEventListener('load', hideLoadingOverlay);
});

// --- Scroll Animations ---
function handleScrollAnimations() {
    const elements = document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right, .scale-in');

    elements.forEach(element => {
        const rect = element.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight - 100;

        if (isVisible) {
            element.classList.add('visible');
        }
    });
}

// Throttle scroll events for better performance
let scrollTimeout;
function throttledScrollHandler() {
    if (!scrollTimeout) {
        scrollTimeout = setTimeout(() => {
            handleScrollAnimations();
            scrollTimeout = null;
        }, 16); // ~60fps
    }
}

window.addEventListener('scroll', throttledScrollHandler);
window.addEventListener('load', handleScrollAnimations);

// --- Enhanced Button Interactions ---
function addButtonEffects() {
    // Add pulse effect to primary buttons
    const primaryButtons = document.querySelectorAll('.btn-primary, .btn-cta');
    primaryButtons.forEach(btn => {
        if (!btn.classList.contains('btn-pulse')) {
            btn.classList.add('btn-pulse');
        }
    });

    // Add hover lift effect to cards and other elements
    const liftElements = document.querySelectorAll('.card, .category-card, .social-btn');
    liftElements.forEach(el => {
        if (!el.classList.contains('hover-lift')) {
            el.classList.add('hover-lift');
        }
    });

    // Add text glow effect to headings
    const headings = document.querySelectorAll('h1, h2, h3');
    headings.forEach(heading => {
        if (!heading.classList.contains('text-glow')) {
            heading.classList.add('text-glow');
        }
    });
}

// --- Typing Animation for Hero ---
function initTypingAnimation() {
    const typingElement = document.querySelector('.typing-text');
    if (!typingElement) return;

    const text = typingElement.textContent;
    typingElement.textContent = '';
    typingElement.style.borderRight = '2px solid var(--primary)';

    let i = 0;
    const typeWriter = () => {
        if (i < text.length) {
            typingElement.textContent += text.charAt(i);
            i++;
            setTimeout(typeWriter, 100);
        } else {
            typingElement.style.borderRight = 'none';
        }
    };

    setTimeout(typeWriter, 1000);
}

// --- Enhanced Search with Debouncing ---
let searchTimeout;
document.getElementById("searchInput").addEventListener("input", function() {
    clearTimeout(searchTimeout);
    const query = this.value.toLowerCase();

    searchTimeout = setTimeout(() => {
        document.querySelectorAll(".card").forEach(card => {
            const title = card.getAttribute("data-title") || "";
            const description = card.getAttribute("data-description") || "";
            if (title.includes(query) || description.includes(query)) {
                card.classList.remove("hidden");
                // Add fade-in animation for search results
                card.style.animation = 'none';
                setTimeout(() => card.style.animation = 'fadeIn 0.5s ease', 10);
            } else {
                card.classList.add("hidden");
            }
        });
    }, 300); // Debounce for 300ms
});

// --- Initialize Everything ---
document.addEventListener('DOMContentLoaded', () => {
    addButtonEffects();
    initTypingAnimation();
    handleScrollAnimations();
});
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        let count = this.querySelector('.like-count');
        count.innerText = parseInt(count.innerText) + 1;
        this.classList.toggle('active');
        this.style.color = "#ef4444"; // Red heart on click
    });
});