
document.addEventListener("DOMContentLoaded", function(){

    // accordion fix
    document.querySelectorAll(".acc-item button").forEach(btn => {
        btn.addEventListener("click", () => {
            let content = btn.nextElementSibling;
            content.style.display =
                content.style.display === "block" ? "none" : "block";
        });
    });

    // search fix
    let search = document.getElementById("searchInput");
    if(search){
        search.addEventListener("keyup", function() {
            let value = this.value.toLowerCase();
            document.querySelectorAll(".card").forEach(card => {
                card.style.display = card.innerText.toLowerCase().includes(value)
                    ? "block"
                    : "none";
            });
        });
    }

});

// index .php code 

const LOCALSTORAGE_KEY = 'travelBlogStats';

const state = {
    likes: {},
    comments: {}
};

function saveStats() {
    localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(state));
}

function loadStats() {
    try {
        const raw = localStorage.getItem(LOCALSTORAGE_KEY);
        if (raw) {
            const parsed = JSON.parse(raw);
            state.likes = parsed.likes || {};
            state.comments = parsed.comments || {};
        }
    } catch (e) {
        console.warn('Unable to load post stats', e);
    }
}

function applyPostStats() {
    document.querySelectorAll('.card').forEach(card => {
        const postId = card.getAttribute('data-post-id');
        if (!postId) return;

        const likeBtn = card.querySelector('.like-btn');
        const commentCount = card.querySelector('.comment-count');
        const likeCount = card.querySelector('.like-count');

        const likes = parseInt(state.likes[postId] || 0);
        const comments = parseInt(state.comments[postId] || 0);

        likeCount.textContent = likes;
        commentCount.textContent = comments;

        if (likes > 0 && likeBtn) {
            likeBtn.classList.add('active');
            likeBtn.querySelector('i').className = 'fas fa-heart';
        }
    });
}

function bindPostActions() {
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const postId = btn.getAttribute('data-post-id');
            if (!postId) return;

            const countEl = btn.querySelector('.like-count');
            let count = parseInt(countEl.textContent) || 0;

            if (btn.classList.contains('active')) {
                btn.classList.remove('active');
                btn.querySelector('i').className = 'far fa-heart';
                count = Math.max(0, count - 1);
            } else {
                btn.classList.add('active');
                btn.querySelector('i').className = 'fas fa-heart';
                count += 1;
            }

            state.likes[postId] = count;
            countEl.textContent = count;
            saveStats();
            showTooltip(btn, count > 0 ? 'Liked!' : 'Like removed');
        });
    });

    document.querySelectorAll('.comment-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const postId = btn.getAttribute('data-post-id');
            if (!postId) return;

            const comment = prompt('Add a quick comment (max 90 chars):', 'Amazing experience! 🌟');
            if (!comment || !comment.trim()) return;

            const countEl = btn.querySelector('.comment-count');
            let count = parseInt(countEl.textContent) || 0;
            count += 1;
            btn.querySelector('.comment-count').textContent = count;

            state.comments[postId] = count;
            saveStats();
            toastNotification('Comment added: ' + comment.trim().slice(0, 60));
            showTooltip(btn, 'Comment saved');
        });
    });
}

function showTooltip(element, text) {
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
        setTimeout(() => tip.remove(), 300);
    }, 1200);
}

window.onscroll = () => {
    const nav = document.getElementById('mainNav');
    if (window.scrollY > 50) nav.classList.add('scrolled');
    else nav.classList.remove('scrolled');
};

function toggleTheme() {
    const body = document.body;
    body.classList.toggle('dark');
    const isDark = body.classList.contains('dark');
    document.querySelector('#themeBtn i').className = isDark ? 'fas fa-sun' : 'fas fa-moon';
    document.cookie = 'theme=' + (isDark ? 'dark' : 'light') + ';path=/';
}

function toastNotification(message) {
    const notif = document.createElement('div');
    notif.className = 'toast-message';
    notif.textContent = message;
    document.body.appendChild(notif);

    setTimeout(() => {
        notif.classList.add('visible');
    }, 20);

    setTimeout(() => {
        notif.classList.remove('visible');
        setTimeout(() => notif.remove(), 400);
    }, 2600);
}

window.onload = () => {
    if (document.body.classList.contains('dark')) {
        document.querySelector('#themeBtn i').className = 'fas fa-sun';
    }
    loadStats();
    applyPostStats();
    bindPostActions();
};
