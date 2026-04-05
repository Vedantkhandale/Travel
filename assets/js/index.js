// TravelBlog - Shared scripts for index/profile pages

const SAVED_POSTS_KEY = 'travelBlogSavedPosts';
const explorerState = {
    query: '',
    sort: 'newest',
    filter: 'all'
};

function getCookieValue(name) {
    const raw = document.cookie || '';
    const parts = raw.split(';').map((p) => p.trim());
    for (const part of parts) {
        if (!part) continue;
        const eq = part.indexOf('=');
        if (eq === -1) continue;
        const key = part.slice(0, eq).trim();
        if (key !== name) continue;
        return decodeURIComponent(part.slice(eq + 1));
    }
    return '';
}

function applyStoredTheme() {
    const cookieTheme = getCookieValue('theme');
    const storedTheme = cookieTheme || localStorage.getItem('theme') || '';
    if (storedTheme === 'dark') {
        document.body.classList.add('dark');
    } else if (storedTheme === 'light') {
        document.body.classList.remove('dark');
    }

    const themeBtn = document.getElementById('themeBtn');
    const icon = document.querySelector('#themeBtn i');
    if (icon) {
        const isDark = document.body.classList.contains('dark');
        icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        if (themeBtn) {
            themeBtn.setAttribute('aria-label', isDark ? 'Switch to light theme' : 'Switch to dark theme');
        }
    }
}

// Keep theme toggle global because HTML uses onclick="toggleTheme()".
function toggleTheme() {
    const body = document.body;
    body.classList.toggle('dark');
    const isDark = body.classList.contains('dark');
    const themeBtn = document.getElementById('themeBtn');
    const icon = document.querySelector('#themeBtn i');
    if (icon) {
        icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
    }
    if (themeBtn) {
        themeBtn.setAttribute('aria-label', isDark ? 'Switch to light theme' : 'Switch to dark theme');
    }
    document.cookie = 'theme=' + (isDark ? 'dark' : 'light') + ';path=/';
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

function toastNotification(message) {
    const notif = document.createElement('div');
    notif.className = 'toast-message';
    notif.textContent = message;
    document.body.appendChild(notif);

    setTimeout(() => notif.classList.add('visible'), 20);
    setTimeout(() => {
        notif.classList.remove('visible');
        setTimeout(() => notif.remove(), 300);
    }, 2200);
}

function showTooltip(element, text) {
    if (!element) return;

    const tip = document.createElement('div');
    tip.className = 'tooltip-tip';
    tip.textContent = text;
    document.body.appendChild(tip);

    const rect = element.getBoundingClientRect();
    tip.style.left = rect.left + rect.width / 2 - tip.offsetWidth / 2 + 'px';
    tip.style.top = rect.top - tip.offsetHeight - 8 + window.scrollY + 'px';

    setTimeout(() => tip.classList.add('visible'), 20);
    setTimeout(() => {
        tip.classList.remove('visible');
        setTimeout(() => tip.remove(), 220);
    }, 1200);
}

function setupNavbarAndMenu() {
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('mainNav');
        if (!nav) return;
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });

    const menuToggle = document.getElementById('mobile-menu');
    const navLinks = document.getElementById('navLinks');
    if (!menuToggle || !navLinks) return;

    menuToggle.setAttribute('aria-controls', 'navLinks');
    if (!menuToggle.hasAttribute('aria-expanded')) {
        menuToggle.setAttribute('aria-expanded', 'false');
    }

    let scrim = document.querySelector('.nav-scrim');
    if (!scrim) {
        scrim = document.createElement('div');
        scrim.className = 'nav-scrim';
        document.body.appendChild(scrim);
    }

    const icon = menuToggle.querySelector('i');

    const closeMenu = () => {
        navLinks.classList.remove('active');
        scrim.classList.remove('visible');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.setAttribute('aria-label', 'Open menu');
        if (icon) {
            icon.classList.add('fa-bars');
            icon.classList.remove('fa-times');
        }
    };

    const openMenu = () => {
        navLinks.classList.add('active');
        scrim.classList.add('visible');
        menuToggle.setAttribute('aria-expanded', 'true');
        menuToggle.setAttribute('aria-label', 'Close menu');
        if (icon) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        }
    };

    menuToggle.addEventListener('click', () => {
        if (navLinks.classList.contains('active')) closeMenu();
        else openMenu();
    });

    scrim.addEventListener('click', closeMenu);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMenu();
    });

    navLinks.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) closeMenu();
    });
}

function filterCards(query) {
    explorerState.query = (query || '').toLowerCase();
    applyExplorerState();
}

function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    let searchTimeout;
    searchInput.addEventListener('input', function onSearchInput() {
        clearTimeout(searchTimeout);
        const value = this.value || '';
        searchTimeout = setTimeout(() => filterCards(value), 200);
    });
}

function getSavedPosts() {
    return JSON.parse(localStorage.getItem(SAVED_POSTS_KEY) || '{}');
}

function savePostState(map) {
    localStorage.setItem(SAVED_POSTS_KEY, JSON.stringify(map));
}

function updateSaveButtonState(btn, isSaved) {
    const icon = btn.querySelector('i');
    if (icon) {
        icon.className = isSaved ? 'fas fa-bookmark' : 'far fa-bookmark';
    }
    btn.classList.toggle('saved', isSaved);
    btn.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
}

function setupSaveButtons() {
    const savedMap = getSavedPosts();

    document.querySelectorAll('.save-btn').forEach((btn) => {
        const postId = btn.getAttribute('data-post-id');
        if (!postId) return;

        updateSaveButtonState(btn, !!savedMap[postId]);

        if (btn.dataset.bound === '1') return;
        btn.dataset.bound = '1';

        btn.addEventListener('click', () => {
            const map = getSavedPosts();
            const next = !map[postId];
            if (next) {
                map[postId] = 1;
            } else {
                delete map[postId];
            }
            savePostState(map);
            updateSaveButtonState(btn, next);
            toastNotification(next ? 'Story saved' : 'Story removed from saved');
            applyExplorerState();
        });
    });
}

function applyExplorerState() {
    const cards = Array.from(document.querySelectorAll('#postsGrid .card'));
    const grid = document.getElementById('postsGrid');
    if (!grid || cards.length === 0) return;

    const savedMap = getSavedPosts();
    const query = explorerState.query;

    cards.forEach((card) => {
        const title = (card.getAttribute('data-title') || '').toLowerCase();
        const description = (card.getAttribute('data-description') || '').toLowerCase();
        const author = (card.getAttribute('data-author') || '').toLowerCase();
        const postId = card.getAttribute('data-post-id') || '';
        const hasImage = card.getAttribute('data-has-image') === '1';
        const isSaved = !!savedMap[postId];

        const matchesSearch = !query || title.includes(query) || description.includes(query) || author.includes(query);
        const matchesFilter =
            explorerState.filter === 'all' ||
            (explorerState.filter === 'with-image' && hasImage) ||
            (explorerState.filter === 'saved' && isSaved);

        if (matchesSearch && matchesFilter) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });

    cards.sort((a, b) => {
        if (explorerState.sort === 'title-asc') {
            return (a.getAttribute('data-title') || '').localeCompare(b.getAttribute('data-title') || '');
        }
        if (explorerState.sort === 'title-desc') {
            return (b.getAttribute('data-title') || '').localeCompare(a.getAttribute('data-title') || '');
        }

        const aIndex = parseInt(a.dataset.originalIndex || '0', 10);
        const bIndex = parseInt(b.dataset.originalIndex || '0', 10);
        return aIndex - bIndex;
    });

    cards.forEach((card) => grid.appendChild(card));

    const visibleCount = cards.filter((card) => !card.classList.contains('hidden')).length;
    const resultsCount = document.getElementById('resultsCount');
    if (resultsCount) {
        resultsCount.textContent = visibleCount + (visibleCount === 1 ? ' story' : ' stories');
    }
}

function setupExplorerControls() {
    const sortSelect = document.getElementById('sortPosts');
    const filterSelect = document.getElementById('filterPosts');

    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            explorerState.sort = sortSelect.value;
            applyExplorerState();
        });
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', () => {
            explorerState.filter = filterSelect.value;
            applyExplorerState();
        });
    }
}

function setupBackToTop() {
    const backToTop = document.getElementById('backToTop');
    if (!backToTop) return;

    const updateVisibility = () => {
        if (window.scrollY > 320) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    };

    updateVisibility();
    window.addEventListener('scroll', updateVisibility);

    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function animateNumber(element, target) {
    const current = parseInt((element.textContent || '0').replace(',', ''), 10);
    if (Number.isNaN(current) || current === target) return;

    const increment = target > current ? 1 : -1;
    const timer = setInterval(() => {
        const now = parseInt((element.textContent || '0').replace(',', ''), 10);
        const next = now + increment;
        element.textContent = next.toLocaleString();
        if (next === target) clearInterval(timer);
    }, 50);
}

function updateStats() {
    const onlineUsers = document.getElementById('onlineUsers');
    const totalPosts = document.getElementById('totalPosts');
    const destinations = document.getElementById('destinations');
    const communities = document.getElementById('communities');

    if (!onlineUsers || !totalPosts || !destinations || !communities) return;

    let currentOnline = parseInt((onlineUsers.textContent || '0').replace(',', ''), 10);
    let currentPosts = parseInt((totalPosts.textContent || '0').replace(',', ''), 10);
    let currentDest = parseInt(destinations.textContent || '0', 10);
    let currentComm = parseInt(communities.textContent || '0', 10);

    if ([currentOnline, currentPosts, currentDest, currentComm].some(Number.isNaN)) return;

    currentOnline += Math.floor(Math.random() * 11) - 5;
    currentPosts += Math.floor(Math.random() * 3) - 1;
    currentDest += Math.floor(Math.random() * 3) - 1;
    currentComm += Math.floor(Math.random() * 5) - 2;

    currentOnline = Math.max(200, Math.min(300, currentOnline));
    currentPosts = Math.max(1200, Math.min(1300, currentPosts));
    currentDest = Math.max(80, Math.min(100, currentDest));
    currentComm = Math.max(140, Math.min(170, currentComm));

    animateNumber(onlineUsers, currentOnline);
    animateNumber(totalPosts, currentPosts);
    animateNumber(destinations, currentDest);
    animateNumber(communities, currentComm);
}

function setupNotifications() {
    const container = document.getElementById('notificationContainer');
    if (!container) return;

    const notifications = [
        { user: 'Sarah', action: 'shared a new adventure in Bali', time: '2 min ago' },
        { user: 'Mike', action: 'liked your Tokyo story', time: '5 min ago' },
        { user: 'Emma', action: 'commented on your Paris post', time: '8 min ago' },
        { user: 'Alex', action: 'started following you', time: '12 min ago' },
        { user: 'Lisa', action: 'shared your Iceland guide', time: '15 min ago' }
    ];

    function showRandomNotification() {
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
        setTimeout(() => notification.classList.add('show'), 60);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 4500);
    }

    function scheduleNotification() {
        const delay = Math.random() * 45000 + 45000;
        setTimeout(() => {
            showRandomNotification();
            scheduleNotification();
        }, delay);
    }

    setTimeout(scheduleNotification, 9000);
}

function hydrateSavedCardStats() {
    const likes = JSON.parse(localStorage.getItem('travelBlogLikeStats') || '{}');
    const comments = JSON.parse(localStorage.getItem('travelBlogCommentStats') || '{}');

    document.querySelectorAll('.card').forEach((card) => {
        const postId = card.getAttribute('data-post-id');
        if (!postId) return;

        const likeBtn = card.querySelector('.like-btn');
        const commentBtn = card.querySelector('.comment-btn');

        if (likeBtn && likes[postId] !== undefined) {
            const countEl = likeBtn.querySelector('.like-count');
            if (countEl) {
                countEl.textContent = likes[postId];
            }
            if (parseInt(likes[postId], 10) > 0) {
                likeBtn.classList.add('active');
                const icon = likeBtn.querySelector('i');
                if (icon) icon.className = 'fas fa-heart';
            }
        }

        if (commentBtn && comments[postId] !== undefined) {
            const countEl = commentBtn.querySelector('.comment-count');
            if (countEl) {
                countEl.textContent = comments[postId];
            }
        }
    });
}

function setupCardButtons() {
    document.querySelectorAll('.like-btn').forEach((btn) => {
        if (btn.dataset.bound === '1') return;
        btn.dataset.bound = '1';

        btn.addEventListener('click', (event) => {
            event.preventDefault();
            const postId = btn.getAttribute('data-post-id');
            if (!postId) return;

            const icon = btn.querySelector('i');
            const countEl = btn.querySelector('.like-count');
            if (!icon || !countEl) return;

            let count = parseInt(countEl.textContent || '0', 10);
            if (Number.isNaN(count)) count = 0;

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
            const stats = JSON.parse(localStorage.getItem('travelBlogLikeStats') || '{}');
            stats[postId] = count;
            localStorage.setItem('travelBlogLikeStats', JSON.stringify(stats));
            showTooltip(btn, count > 0 ? 'Liked!' : 'Unliked');
        });
    });

    document.querySelectorAll('.comment-btn').forEach((btn) => {
        if (btn.dataset.bound === '1') return;
        btn.dataset.bound = '1';

        btn.addEventListener('click', (event) => {
            event.preventDefault();
            const postId = btn.getAttribute('data-post-id');
            if (!postId) return;

            const comment = prompt('Add a quick comment:', 'Amazing experience!');
            if (!comment || !comment.trim()) return;

            const countEl = btn.querySelector('.comment-count');
            if (!countEl) return;

            let count = parseInt(countEl.textContent || '0', 10);
            if (Number.isNaN(count)) count = 0;
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

function populateActivityFeed() {
    const feed = document.getElementById('activityFeed');
    if (!feed) return;

    const activities = [
        { user: 'Sarah', action: 'shared a new adventure', location: 'Bali', time: '2 minutes ago' },
        { user: 'Mike', action: 'discovered hidden gems in', location: 'Tokyo', time: '5 minutes ago' },
        { user: 'Emma', action: 'posted amazing photos from', location: 'Paris', time: '8 minutes ago' },
        { user: 'Alex', action: 'started exploring', location: 'New York', time: '12 minutes ago' },
        { user: 'Lisa', action: 'shared food adventures in', location: 'Thailand', time: '15 minutes ago' },
        { user: 'David', action: 'captured sunset views in', location: 'Santorini', time: '18 minutes ago' }
    ];

    const selected = activities.sort(() => 0.5 - Math.random()).slice(0, 6);
    feed.innerHTML = '';

    selected.forEach((activity) => {
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

function updateActivityTimes() {
    document.querySelectorAll('.activity-time').forEach((el) => {
        const current = el.textContent || '';
        const minutes = parseInt(current.split(' ')[0], 10);
        if (!Number.isNaN(minutes)) {
            el.textContent = (minutes + 1) + ' minutes ago';
        }
    });
}

function hideLoadingOverlay() {
    const overlay = document.querySelector('.loading-overlay');
    if (!overlay) return;

    overlay.classList.add('hidden');
    setTimeout(() => {
        if (overlay.parentNode) overlay.remove();
    }, 450);
}

function setupLoadingOverlay() {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = `
        <div class="loader">
            <div class="loader-spinner"></div>
            <div class="loader-text">Exploring the world...</div>
        </div>
    `;
    document.body.appendChild(overlay);

    setTimeout(hideLoadingOverlay, 1500);
    window.addEventListener('load', hideLoadingOverlay);
}

function handleScrollAnimations() {
    const elements = document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right, .scale-in');
    elements.forEach((element) => {
        const rect = element.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            element.classList.add('visible');
        }
    });
}

let scrollTimeout;
function throttledScrollHandler() {
    if (scrollTimeout) return;

    scrollTimeout = setTimeout(() => {
        handleScrollAnimations();
        scrollTimeout = null;
    }, 16);
}

function addButtonEffects() {
    document.querySelectorAll('.card, .category-card, .social-btn').forEach((el) => {
        if (!el.classList.contains('hover-lift')) {
            el.classList.add('hover-lift');
        }
    });

    document.querySelectorAll('h1, h2, h3').forEach((heading) => {
        if (!heading.classList.contains('text-glow')) {
            heading.classList.add('text-glow');
        }
    });
}

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
            i += 1;
            setTimeout(typeWriter, 100);
        } else {
            typingElement.style.borderRight = 'none';
        }
    };

    setTimeout(typeWriter, 900);
}

function deletePost(postId) {
    const card = document.getElementById('post-' + postId);
    if (!card) return;

    if (!confirm('Are you sure? This memory will be gone forever!')) return;

    card.style.border = '2px solid #f43f5e';
    card.style.boxShadow = '0 0 20px rgba(244, 63, 94, 0.2)';

    fetch('delete-post.php?id=' + postId)
        .then(() => {
            card.classList.add('fade-out-card');
            setTimeout(() => card.remove(), 500);
        })
        .catch(() => alert('Something went wrong while deleting post.'));
}

window.deletePost = deletePost;

document.addEventListener('DOMContentLoaded', () => {
    applyStoredTheme();

    document.querySelectorAll('#postsGrid .card').forEach((card, index) => {
        card.dataset.originalIndex = String(index);
    });

    setupNavbarAndMenu();
    setupSearch();
    setupExplorerControls();
    setupSaveButtons();
    setupNotifications();
    setupCardButtons();
    hydrateSavedCardStats();
    populateActivityFeed();
    setupBackToTop();

    updateStats();
    setInterval(updateStats, 30000);
    setInterval(updateActivityTimes, 60000);

    setupLoadingOverlay();
    addButtonEffects();
    initTypingAnimation();
    handleScrollAnimations();
    applyExplorerState();
});

window.addEventListener('scroll', throttledScrollHandler);
window.addEventListener('load', handleScrollAnimations);
