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

function setupCategoryCards() {
    const cards = Array.from(document.querySelectorAll('.category-card[data-query]'));
    if (cards.length === 0) return;

    const searchInput = document.getElementById('searchInput');

    const applyCategory = (card) => {
        const query = (card.getAttribute('data-query') || '').trim();
        const label = (card.getAttribute('data-label') || 'selected').trim();
        if (!query) return;

        cards.forEach((item) => item.classList.remove('active'));
        card.classList.add('active');

        if (searchInput) {
            searchInput.value = query;
        }

        filterCards(query);
        toastNotification('Filtered: ' + label);

        const target = document.getElementById('postsGrid');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    cards.forEach((card) => {
        if (card.dataset.categoryBound === '1') return;
        card.dataset.categoryBound = '1';

        card.setAttribute('role', 'button');
        card.setAttribute('tabindex', '0');
        if (!card.hasAttribute('aria-label')) {
            const label = (card.getAttribute('data-label') || 'category').trim();
            card.setAttribute('aria-label', 'Filter by ' + label);
        }

        card.addEventListener('click', () => {
            applyCategory(card);
        });

        card.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            event.preventDefault();
            applyCategory(card);
        });
    });
}

function setupFooterLinks() {
    const footer = document.querySelector('.main-footer');
    if (!footer) return;

    footer.querySelectorAll('a[href^="#"]').forEach((link) => {
        if (link.dataset.bound === '1') return;
        link.dataset.bound = '1';

        link.addEventListener('click', (event) => {
            const targetSelector = link.getAttribute('href') || '';
            if (!targetSelector || targetSelector === '#') return;

            const target = document.querySelector(targetSelector);
            if (!target) return;

            event.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
}

function setupNewsletterForm() {
    const form = document.getElementById('newsletterForm');
    const input = document.getElementById('newsletterEmail');
    if (!form || !input || form.dataset.bound === '1') return;
    form.dataset.bound = '1';

    const storageKey = 'travelBlogNewsletterSubscribers';

    const setInvalidState = (invalid) => {
        input.classList.toggle('is-invalid', invalid);
        input.setAttribute('aria-invalid', invalid ? 'true' : 'false');
    };

    input.addEventListener('input', () => setInvalidState(false));

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const email = (input.value || '').trim().toLowerCase();
        const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email);

        if (!emailValid) {
            setInvalidState(true);
            input.focus();
            toastNotification('Please enter a valid email');
            return;
        }

        setInvalidState(false);

        let list = [];
        try {
            const parsed = JSON.parse(localStorage.getItem(storageKey) || '[]');
            if (Array.isArray(parsed)) list = parsed;
        } catch (_) {
            list = [];
        }

        if (!list.includes(email)) {
            list.push(email);
        }

        localStorage.setItem(storageKey, JSON.stringify(list.slice(-200)));
        input.value = '';
        toastNotification('Subscribed successfully');
    });
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

    const readTarget = (element) => {
        const target = parseInt(element.getAttribute('data-target') || '', 10);
        if (!Number.isNaN(target)) return target;
        const fallback = parseInt((element.textContent || '0').replace(',', ''), 10);
        return Number.isNaN(fallback) ? 0 : fallback;
    };

    const targetPosts = readTarget(totalPosts);
    const targetDestinations = readTarget(destinations);
    const targetCommunities = readTarget(communities);
    const targetOnline = readTarget(onlineUsers);

    animateNumber(totalPosts, targetPosts);
    animateNumber(destinations, targetDestinations);
    animateNumber(communities, targetCommunities);

    if (targetOnline <= 0) return;

    const variance = Math.max(2, Math.round(targetOnline * 0.1));
    const currentOnline = parseInt((onlineUsers.textContent || '0').replace(',', ''), 10);
    const baseOnline = Number.isNaN(currentOnline) ? targetOnline : currentOnline;
    let liveOnline = baseOnline + Math.floor(Math.random() * 5) - 2;

    const minOnline = Math.max(1, targetOnline - variance);
    const maxOnline = targetOnline + variance;
    liveOnline = Math.max(minOnline, Math.min(maxOnline, liveOnline));
    animateNumber(onlineUsers, liveOnline);
}

function setupNotifications() {
    const container = document.getElementById('notificationContainer');
    if (!container || container.dataset.bound === '1') return;
    container.dataset.bound = '1';

    const notifications = [
        { user: 'Sarah', type: 'follow', action: 'started following your profile' },
        { user: 'Mike', type: 'like', action: 'liked your Tokyo story' },
        { user: 'Emma', type: 'comment', action: 'commented on your Paris post' },
        { user: 'Alex', type: 'save', action: 'saved your mountain guide' },
        { user: 'Lisa', type: 'share', action: 'shared your Bali itinerary' },
        { user: 'Rahul', type: 'follow', action: 'followed your updates' },
        { user: 'Nina', type: 'like', action: 'liked your sunset photos' }
    ];

    const typeMeta = {
        follow: { label: 'Follow', icon: 'fa-user-plus' },
        like: { label: 'Like', icon: 'fa-heart' },
        comment: { label: 'Comment', icon: 'fa-comment-dots' },
        save: { label: 'Save', icon: 'fa-bookmark' },
        share: { label: 'Share', icon: 'fa-paper-plane' }
    };

    const getRandomTime = () => {
        const minutes = Math.floor(Math.random() * 14) + 1;
        return minutes + (minutes === 1 ? ' min ago' : ' mins ago');
    };

    function showRandomNotification() {
        const randomNotif = notifications[Math.floor(Math.random() * notifications.length)];
        const meta = typeMeta[randomNotif.type] || typeMeta.like;

        if (container.children.length >= 3) {
            const first = container.firstElementChild;
            if (first) first.remove();
        }

        const notification = document.createElement('div');
        notification.className = 'notification notification-' + randomNotif.type;
        notification.innerHTML = `
            <div class="notification-header">
                <div class="notification-avatar">${randomNotif.user.charAt(0)}</div>
                <strong class="notification-user">${randomNotif.user}</strong>
                <span class="notification-badge"><i class="fas ${meta.icon}"></i>${meta.label}</span>
            </div>
            <div class="notification-content">${randomNotif.action}</div>
            <div class="notification-time">${getRandomTime()}</div>
        `;

        container.appendChild(notification);
        requestAnimationFrame(() => notification.classList.add('show'));

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 260);
        }, 5200);
    }

    function scheduleNotification() {
        const delay = Math.random() * 16000 + 16000;
        setTimeout(() => {
            if (document.visibilityState === 'visible') {
                showRandomNotification();
            }
            scheduleNotification();
        }, delay);
    }

    setTimeout(() => showRandomNotification(), 1400);
    setTimeout(scheduleNotification, 2600);
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

function setupCardHoverEnhancements() {
    const cards = document.querySelectorAll('#postsGrid .card');

    cards.forEach((card) => {
        if (card.dataset.hoverEnhanceBound === '1') return;
        card.dataset.hoverEnhanceBound = '1';

        const imageWrap = card.querySelector('.card-img');
        if (!imageWrap || imageWrap.dataset.quickLikeBound === '1') return;

        imageWrap.dataset.quickLikeBound = '1';
        imageWrap.addEventListener('dblclick', (event) => {
            event.preventDefault();
            const likeBtn = card.querySelector('.like-btn');
            if (!likeBtn) return;

            likeBtn.click();
            imageWrap.classList.add('quick-liked');
            setTimeout(() => imageWrap.classList.remove('quick-liked'), 420);
        });
    });
}

function formatActivityTime(minutes) {
    const value = Math.max(0, parseInt(minutes, 10) || 0);
    if (value === 0) return 'just now';
    if (value < 60) return value + (value === 1 ? ' min ago' : ' mins ago');

    const hours = Math.floor(value / 60);
    if (hours < 24) return hours + (hours === 1 ? ' hr ago' : ' hrs ago');

    const days = Math.floor(hours / 24);
    return days + (days === 1 ? ' day ago' : ' days ago');
}

function buildActivityData() {
    const cards = Array.from(document.querySelectorAll('#postsGrid .card'));
    const actionPool = [
        { text: 'shared a new route guide', icon: 'fa-map-location-dot' },
        { text: 'posted a photo highlight', icon: 'fa-camera-retro' },
        { text: 'updated a travel checklist', icon: 'fa-list-check' },
        { text: 'recommended a local spot', icon: 'fa-star' },
        { text: 'saved a weekend plan', icon: 'fa-bookmark' },
        { text: 'started a discussion thread', icon: 'fa-comments' }
    ];
    const fallbackLocations = ['Bali', 'Tokyo', 'Paris', 'New York', 'Thailand', 'Santorini', 'Amsterdam', 'Manali'];

    if (cards.length > 0) {
        return cards.slice(0, 10).map((card, index) => {
            const title = (card.getAttribute('data-title') || '').trim();
            const author = (card.getAttribute('data-author') || 'Traveler').trim();
            const action = actionPool[index % actionPool.length];
            const location = fallbackLocations[index % fallbackLocations.length];

            return {
                user: author || 'Traveler',
                action: action.text,
                icon: action.icon,
                title: title || 'New travel story',
                location,
                minutes: 2 + index * 4
            };
        });
    }

    return [
        { user: 'Sarah', action: 'shared a new route guide', icon: 'fa-map-location-dot', title: 'Sunrise trail in Bali', location: 'Bali', minutes: 2 },
        { user: 'Mike', action: 'posted a photo highlight', icon: 'fa-camera-retro', title: 'Tokyo street walk', location: 'Tokyo', minutes: 6 },
        { user: 'Emma', action: 'recommended a local spot', icon: 'fa-star', title: 'Paris café notes', location: 'Paris', minutes: 10 },
        { user: 'Alex', action: 'updated a travel checklist', icon: 'fa-list-check', title: 'Weekend city guide', location: 'New York', minutes: 14 },
        { user: 'Lisa', action: 'saved a weekend plan', icon: 'fa-bookmark', title: 'Island food trail', location: 'Thailand', minutes: 18 },
        { user: 'David', action: 'started a discussion thread', icon: 'fa-comments', title: 'Sunset viewpoint picks', location: 'Santorini', minutes: 22 }
    ];
}

function populateActivityFeed(shouldShuffle) {
    const feed = document.getElementById('activityFeed');
    if (!feed) return;

    const activities = buildActivityData();
    const source = shouldShuffle ? [...activities].sort(() => Math.random() - 0.5) : activities;
    const selected = source.slice(0, 6).sort((a, b) => a.minutes - b.minutes);

    feed.innerHTML = '';

    selected.forEach((activity, index) => {
        const item = document.createElement('div');
        item.className = 'activity-item';
        item.style.animationDelay = String(index * 40) + 'ms';

        const avatar = document.createElement('div');
        avatar.className = 'activity-avatar';
        avatar.textContent = (activity.user || 'T').charAt(0).toUpperCase();

        const content = document.createElement('div');
        content.className = 'activity-content';

        const text = document.createElement('p');
        text.className = 'activity-text';
        text.innerHTML = `<strong>${activity.user}</strong> ${activity.action}`;

        const title = document.createElement('p');
        title.className = 'activity-title';
        title.innerHTML = `<i class="fas fa-book-open"></i>${(activity.title || '').slice(0, 44)}`;

        const meta = document.createElement('div');
        meta.className = 'activity-meta';

        const location = document.createElement('span');
        location.className = 'activity-location';
        location.innerHTML = `<i class="fas fa-location-dot"></i>${activity.location}`;

        const time = document.createElement('span');
        time.className = 'activity-time';
        time.setAttribute('data-minutes', String(activity.minutes));
        time.textContent = formatActivityTime(activity.minutes);

        const actionIcon = document.createElement('span');
        actionIcon.className = 'activity-action-icon';
        actionIcon.innerHTML = `<i class="fas ${activity.icon}"></i>`;

        meta.appendChild(location);
        meta.appendChild(time);
        content.appendChild(text);
        content.appendChild(title);
        content.appendChild(meta);
        item.appendChild(avatar);
        item.appendChild(content);
        item.appendChild(actionIcon);
        feed.appendChild(item);
    });
}

function setupActivityRefresh() {
    const refreshBtn = document.getElementById('refreshActivity');
    if (!refreshBtn || refreshBtn.dataset.bound === '1') return;
    refreshBtn.dataset.bound = '1';

    refreshBtn.addEventListener('click', () => {
        refreshBtn.classList.add('is-spinning');
        populateActivityFeed(true);
        toastNotification('Activity refreshed');
        setTimeout(() => refreshBtn.classList.remove('is-spinning'), 420);
    });
}

function updateActivityTimes() {
    document.querySelectorAll('.activity-time[data-minutes]').forEach((el) => {
        let minutes = parseInt(el.getAttribute('data-minutes') || '0', 10);
        if (Number.isNaN(minutes)) minutes = 0;
        minutes += 1;
        el.setAttribute('data-minutes', String(minutes));
        el.textContent = formatActivityTime(minutes);
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
    setupCategoryCards();
    setupFooterLinks();
    setupNewsletterForm();
    setupSaveButtons();
    setupNotifications();
    setupCardButtons();
    setupCardHoverEnhancements();
    hydrateSavedCardStats();
    populateActivityFeed(true);
    setupActivityRefresh();
    setupBackToTop();

    updateStats();
    setInterval(updateStats, 30000);
    setInterval(updateActivityTimes, 60000);
    setInterval(() => populateActivityFeed(true), 90000);

    setupLoadingOverlay();
    addButtonEffects();
    initTypingAnimation();
    handleScrollAnimations();
    applyExplorerState();
});

window.addEventListener('scroll', throttledScrollHandler);
window.addEventListener('load', handleScrollAnimations);

