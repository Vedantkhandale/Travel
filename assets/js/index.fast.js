const SAVED_POSTS_KEY = 'travelBlogSavedPosts';
const LIKE_STATS_KEY = 'travelBlogLikeStats';
const COMMENT_STATS_KEY = 'travelBlogCommentStats';
const NEWSLETTER_KEY = 'travelBlogNewsletterSubscribers';

const explorerState = {
    query: '',
    sort: 'newest',
    filter: 'all',
    debounceTimer: null,
    pendingRequest: null
};

function getCookieValue(name) {
    const raw = document.cookie || '';
    const parts = raw.split(';').map((part) => part.trim());

    for (const part of parts) {
        if (!part) continue;
        const [key, ...rest] = part.split('=');
        if ((key || '').trim() !== name) continue;
        return decodeURIComponent(rest.join('='));
    }

    return '';
}

function updateThemeButton() {
    const themeBtn = document.getElementById('themeBtn');
    const icon = document.querySelector('#themeBtn i');
    if (!themeBtn || !icon) return;

    const isDark = document.body.classList.contains('dark');
    icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
    themeBtn.setAttribute('aria-label', isDark ? 'Switch to light theme' : 'Switch to dark theme');
}

function applyStoredTheme() {
    const cookieTheme = getCookieValue('theme');
    const storedTheme = cookieTheme || localStorage.getItem('theme') || 'dark';

    if (storedTheme === 'light') {
        document.body.classList.remove('dark');
    } else {
        document.body.classList.add('dark');
    }

    updateThemeButton();
}

function toggleTheme() {
    document.body.classList.toggle('dark');
    const isDark = document.body.classList.contains('dark');
    document.cookie = 'theme=' + (isDark ? 'dark' : 'light') + ';path=/';
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateThemeButton();
}

window.toggleTheme = toggleTheme;

function toastNotification(message) {
    const toast = document.createElement('div');
    toast.className = 'toast-message';
    toast.textContent = message;
    document.body.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add('visible'));

    setTimeout(() => {
        toast.classList.remove('visible');
        setTimeout(() => toast.remove(), 280);
    }, 2200);
}

function getSavedPosts() {
    try {
        return JSON.parse(localStorage.getItem(SAVED_POSTS_KEY) || '{}');
    } catch (_) {
        return {};
    }
}

function savePostState(map) {
    localStorage.setItem(SAVED_POSTS_KEY, JSON.stringify(map));
}

function getLocalMap(key) {
    try {
        return JSON.parse(localStorage.getItem(key) || '{}');
    } catch (_) {
        return {};
    }
}

function setStoriesStatus(message, mode) {
    const status = document.getElementById('storiesStatus');
    if (!status) return;

    status.textContent = message;
    status.dataset.mode = mode || 'idle';
}

function updateResultsCount(count) {
    const countNode = document.getElementById('resultsCount');
    if (!countNode) return;

    countNode.textContent = count + (count === 1 ? ' story' : ' stories');
}

function updateSaveButtonState(btn, isSaved) {
    const icon = btn.querySelector('i');
    if (icon) {
        icon.className = isSaved ? 'fas fa-bookmark' : 'far fa-bookmark';
    }

    btn.classList.toggle('saved', isSaved);
    btn.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
}

function syncStoryCardState() {
    const savedMap = getSavedPosts();
    const likes = getLocalMap(LIKE_STATS_KEY);
    const comments = getLocalMap(COMMENT_STATS_KEY);

    document.querySelectorAll('#postsGrid .save-btn').forEach((btn) => {
        const postId = btn.getAttribute('data-post-id') || '';
        updateSaveButtonState(btn, !!savedMap[postId]);
    });

    document.querySelectorAll('#postsGrid .like-btn').forEach((btn) => {
        const postId = btn.getAttribute('data-post-id') || '';
        const count = parseInt(likes[postId] || '0', 10) || 0;
        const icon = btn.querySelector('i');
        const countEl = btn.querySelector('.like-count');

        if (countEl) countEl.textContent = String(count);
        btn.classList.toggle('active', count > 0);
        if (icon) {
            icon.className = count > 0 ? 'fas fa-heart' : 'far fa-heart';
        }
    });

    document.querySelectorAll('#postsGrid .comment-btn').forEach((btn) => {
        const postId = btn.getAttribute('data-post-id') || '';
        const count = parseInt(comments[postId] || '0', 10) || 0;
        const countEl = btn.querySelector('.comment-count');
        if (countEl) countEl.textContent = String(count);
    });
}

function setupNavbarAndMenu() {
    const nav = document.getElementById('mainNav');
    const menuToggle = document.getElementById('mobile-menu');
    const navLinks = document.getElementById('navLinks');
    if (!nav || !menuToggle || !navLinks) return;

    let scrim = document.querySelector('.nav-scrim');
    if (!scrim) {
        scrim = document.createElement('div');
        scrim.className = 'nav-scrim';
        document.body.appendChild(scrim);
    }

    const icon = menuToggle.querySelector('i');

    const setMenuState = (isOpen) => {
        navLinks.classList.toggle('active', isOpen);
        scrim.classList.toggle('visible', isOpen);
        document.body.classList.toggle('menu-open', isOpen);
        menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        menuToggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');

        if (icon) {
            icon.classList.toggle('fa-bars', !isOpen);
            icon.classList.toggle('fa-times', isOpen);
        }
    };

    const closeMenu = () => {
        setMenuState(false);
    };

    const openMenu = () => {
        setMenuState(true);
    };

    const syncScrolledState = () => {
        nav.classList.toggle('scrolled', window.scrollY > 20);
    };

    syncScrolledState();
    window.addEventListener('scroll', syncScrolledState, { passive: true });

    menuToggle.addEventListener('click', () => {
        if (navLinks.classList.contains('active')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    scrim.addEventListener('click', closeMenu);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    navLinks.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            closeMenu();
        }
    });
}

function scheduleStoryRefresh() {
    clearTimeout(explorerState.debounceTimer);
    explorerState.debounceTimer = setTimeout(() => refreshStories(), 180);
}

function buildStoryRequestData() {
    const savedIds = Object.keys(getSavedPosts());

    return {
        search: explorerState.query,
        sort: explorerState.sort,
        filter: explorerState.filter,
        saved_ids: savedIds.join(',')
    };
}

function applyStoryResponse(response, showToast) {
    const grid = document.getElementById('postsGrid');
    const wrap = document.getElementById('storiesGridWrap');
    if (!grid || !wrap || !response || typeof response.html !== 'string') return;

    grid.innerHTML = response.html;
    grid.setAttribute('aria-busy', 'false');
    wrap.classList.remove('is-loading');

    syncStoryCardState();

    const count = Number(response.count) || 0;
    updateResultsCount(count);
    setStoriesStatus(count > 0 ? 'Stories updated instantly' : 'No stories found', count > 0 ? 'success' : 'empty');

    if (showToast) {
        toastNotification(count > 0 ? 'Stories updated' : 'No stories found');
    }
}

function handleStoryRequestError(statusText) {
    const grid = document.getElementById('postsGrid');
    const wrap = document.getElementById('storiesGridWrap');
    if (grid) {
        grid.setAttribute('aria-busy', 'false');
    }
    if (wrap) {
        wrap.classList.remove('is-loading');
    }

    if (statusText === 'abort') return;

    setStoriesStatus('Refresh failed', 'error');
    toastNotification('Stories refresh failed');
}

function refreshStories(options) {
    const settings = options || {};
    const grid = document.getElementById('postsGrid');
    const wrap = document.getElementById('storiesGridWrap');
    if (!grid || !wrap) return;

    if (explorerState.pendingRequest && typeof explorerState.pendingRequest.abort === 'function') {
        explorerState.pendingRequest.abort();
    }

    wrap.classList.add('is-loading');
    grid.setAttribute('aria-busy', 'true');
    setStoriesStatus('Refreshing stories...', 'loading');

    const requestData = buildStoryRequestData();

    if (window.jQuery && typeof window.jQuery.ajax === 'function') {
        explorerState.pendingRequest = window.jQuery.ajax({
            url: 'ajax/fetch-stories.php',
            method: 'GET',
            dataType: 'json',
            cache: false,
            data: requestData
        })
            .done((response) => applyStoryResponse(response, !!settings.toast))
            .fail((xhr, statusText) => handleStoryRequestError(statusText))
            .always(() => {
                explorerState.pendingRequest = null;
            });

        return;
    }

    const params = new URLSearchParams(requestData);
    const controller = new AbortController();
    explorerState.pendingRequest = controller;

    fetch('ajax/fetch-stories.php?' + params.toString(), {
        method: 'GET',
        signal: controller.signal
    })
        .then((res) => res.json())
        .then((response) => applyStoryResponse(response, !!settings.toast))
        .catch((error) => {
            if (error && error.name === 'AbortError') {
                handleStoryRequestError('abort');
                return;
            }
            handleStoryRequestError('error');
        })
        .finally(() => {
            explorerState.pendingRequest = null;
        });
}

function setupSearchAndFilters() {
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortPosts');
    const filterSelect = document.getElementById('filterPosts');

    if (searchInput) {
        explorerState.query = (searchInput.value || '').trim();
    }

    if (sortSelect) {
        explorerState.sort = sortSelect.value || 'newest';
    }

    if (filterSelect) {
        explorerState.filter = filterSelect.value || 'all';
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            explorerState.query = (searchInput.value || '').trim();
            clearActiveCategories();
            scheduleStoryRefresh();
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            explorerState.sort = sortSelect.value || 'newest';
            refreshStories();
        });
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', () => {
            explorerState.filter = filterSelect.value || 'all';
            refreshStories();
        });
    }
}

function clearActiveCategories() {
    document.querySelectorAll('.category-card.active').forEach((card) => {
        card.classList.remove('active');
    });
}

function setupCategoryCards() {
    const cards = Array.from(document.querySelectorAll('.category-card[data-query]'));
    const searchInput = document.getElementById('searchInput');
    const storiesSection = document.getElementById('stories');
    if (cards.length === 0) return;

    const applyCategory = (card) => {
        const query = (card.getAttribute('data-query') || '').trim();
        const label = (card.getAttribute('data-label') || 'Selected').trim();

        cards.forEach((item) => item.classList.remove('active'));
        card.classList.add('active');

        explorerState.query = query;
        explorerState.filter = 'all';

        const filterSelect = document.getElementById('filterPosts');
        if (filterSelect) {
            filterSelect.value = 'all';
        }

        if (searchInput) {
            searchInput.value = query;
        }

        refreshStories({ toast: false });
        setStoriesStatus('Showing ' + label, 'success');

        if (storiesSection) {
            storiesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    cards.forEach((card) => {
        card.setAttribute('role', 'button');
        card.setAttribute('tabindex', '0');

        card.addEventListener('click', () => applyCategory(card));
        card.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            event.preventDefault();
            applyCategory(card);
        });
    });
}

function setupFooterLinks() {
    document.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const selector = link.getAttribute('href') || '';
            if (!selector || selector === '#') return;

            const target = document.querySelector(selector);
            if (!target) return;

            event.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
}

function setupNewsletterForm() {
    const form = document.getElementById('newsletterForm');
    const input = document.getElementById('newsletterEmail');
    if (!form || !input) return;

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
            toastNotification('Enter a valid email');
            return;
        }

        const list = Object.keys(Object.assign({}, getLocalMap(NEWSLETTER_KEY), { [email]: true }));
        const payload = {};
        list.slice(-200).forEach((item) => {
            payload[item] = true;
        });

        localStorage.setItem(NEWSLETTER_KEY, JSON.stringify(payload));
        input.value = '';
        setInvalidState(false);
        toastNotification('Subscribed successfully');
    });
}

function setupBackToTop() {
    const backToTop = document.getElementById('backToTop');
    if (!backToTop) return;

    const syncVisibility = () => {
        backToTop.classList.toggle('visible', window.scrollY > 320);
    };

    syncVisibility();
    window.addEventListener('scroll', syncVisibility, { passive: true });
    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function toggleLikeButton(button) {
    const postId = button.getAttribute('data-post-id') || '';
    const icon = button.querySelector('i');
    const countEl = button.querySelector('.like-count');
    if (!postId || !icon || !countEl) return;

    let count = parseInt(countEl.textContent || '0', 10) || 0;
    const nextActive = !button.classList.contains('active');

    button.classList.toggle('active', nextActive);
    icon.className = nextActive ? 'fas fa-heart' : 'far fa-heart';
    count = nextActive ? count + 1 : Math.max(0, count - 1);
    countEl.textContent = String(count);

    const stats = getLocalMap(LIKE_STATS_KEY);
    stats[postId] = count;
    localStorage.setItem(LIKE_STATS_KEY, JSON.stringify(stats));

    toastNotification(nextActive ? 'Story liked' : 'Like removed');
}

function addComment(button) {
    const postId = button.getAttribute('data-post-id') || '';
    const countEl = button.querySelector('.comment-count');
    if (!postId || !countEl) return;

    const comment = window.prompt('Add a quick comment:', 'Amazing story!');
    if (!comment || !comment.trim()) return;

    let count = parseInt(countEl.textContent || '0', 10) || 0;
    count += 1;
    countEl.textContent = String(count);

    const comments = getLocalMap(COMMENT_STATS_KEY);
    comments[postId] = count;
    localStorage.setItem(COMMENT_STATS_KEY, JSON.stringify(comments));

    toastNotification('Comment added');
}

function setupStoryInteractions() {
    if (window.jQuery) {
        window.jQuery(document).on('click', '#postsGrid .save-btn', function onSaveClick() {
            const postId = this.getAttribute('data-post-id') || '';
            if (!postId) return;

            const savedMap = getSavedPosts();
            const nextSaved = !savedMap[postId];

            if (nextSaved) {
                savedMap[postId] = 1;
            } else {
                delete savedMap[postId];
            }

            savePostState(savedMap);
            updateSaveButtonState(this, nextSaved);
            toastNotification(nextSaved ? 'Story saved' : 'Saved story removed');

            if (explorerState.filter === 'saved') {
                refreshStories();
            }
        });

        window.jQuery(document).on('click', '#postsGrid .like-btn', function onLikeClick() {
            toggleLikeButton(this);
        });

        window.jQuery(document).on('click', '#postsGrid .comment-btn', function onCommentClick() {
            addComment(this);
        });

        window.jQuery(document).on('dblclick', '#postsGrid .card-img', function onCardImageDoubleClick(event) {
            event.preventDefault();
            const card = this.closest('.card');
            if (!card) return;

            const likeBtn = card.querySelector('.like-btn');
            if (!likeBtn) return;

            toggleLikeButton(likeBtn);
            this.classList.add('quick-liked');
            setTimeout(() => this.classList.remove('quick-liked'), 420);
        });

        return;
    }

    document.addEventListener('click', (event) => {
        const saveBtn = event.target.closest('#postsGrid .save-btn');
        if (saveBtn) {
            const postId = saveBtn.getAttribute('data-post-id') || '';
            if (!postId) return;

            const savedMap = getSavedPosts();
            const nextSaved = !savedMap[postId];

            if (nextSaved) {
                savedMap[postId] = 1;
            } else {
                delete savedMap[postId];
            }

            savePostState(savedMap);
            updateSaveButtonState(saveBtn, nextSaved);
            toastNotification(nextSaved ? 'Story saved' : 'Saved story removed');

            if (explorerState.filter === 'saved') {
                refreshStories();
            }
            return;
        }

        const likeBtn = event.target.closest('#postsGrid .like-btn');
        if (likeBtn) {
            toggleLikeButton(likeBtn);
            return;
        }

        const commentBtn = event.target.closest('#postsGrid .comment-btn');
        if (commentBtn) {
            addComment(commentBtn);
        }
    });

    document.addEventListener('dblclick', (event) => {
        const imageWrap = event.target.closest('#postsGrid .card-img');
        if (!imageWrap) return;

        const card = imageWrap.closest('.card');
        const likeBtn = card ? card.querySelector('.like-btn') : null;
        if (!likeBtn) return;

        toggleLikeButton(likeBtn);
        imageWrap.classList.add('quick-liked');
        setTimeout(() => imageWrap.classList.remove('quick-liked'), 420);
    });
}

function setupRevealAnimations() {
    const elements = document.querySelectorAll('.fade-in');
    if (!('IntersectionObserver' in window)) {
        elements.forEach((element) => element.classList.add('visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.15 });

    elements.forEach((element) => observer.observe(element));
}

document.addEventListener('DOMContentLoaded', () => {
    applyStoredTheme();
    setupNavbarAndMenu();
    setupSearchAndFilters();
    setupCategoryCards();
    setupFooterLinks();
    setupNewsletterForm();
    setupBackToTop();
    setupStoryInteractions();
    setupRevealAnimations();
    syncStoryCardState();
    updateResultsCount(document.querySelectorAll('#postsGrid .card').length);
    setStoriesStatus('Fast mode on', 'success');
});
