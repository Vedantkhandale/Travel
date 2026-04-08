<?php

if (!function_exists('formatStoryDate')) {
    function formatStoryDate($value)
    {
        $timestamp = strtotime((string) $value);
        if (!$timestamp) {
            return 'Just landed';
        }

        return date('M d, Y', $timestamp);
    }
}

if (!function_exists('estimateReadTime')) {
    function estimateReadTime($text)
    {
        $wordCount = str_word_count(strip_tags((string) $text));
        return max(1, (int) ceil($wordCount / 45));
    }
}

if (!function_exists('fetchHomepageStories')) {
    function fetchHomepageStories($conn, array $options = [])
    {
        $search = trim((string) ($options['search'] ?? ''));
        $sort = trim((string) ($options['sort'] ?? 'newest'));
        $filter = trim((string) ($options['filter'] ?? 'all'));
        $limit = max(1, min(24, (int) ($options['limit'] ?? 12)));
        $savedIds = array_values(array_filter(array_map('intval', $options['saved_ids'] ?? [])));

        $conditions = [];

        if ($search !== '') {
            $escaped = mysqli_real_escape_string($conn, $search);
            $like = "'%" . $escaped . "%'";
            $conditions[] = "(posts.title LIKE {$like} OR posts.description LIKE {$like} OR users.name LIKE {$like})";
        }

        if ($filter === 'with-image') {
            $conditions[] = "posts.image IS NOT NULL AND posts.image <> ''";
        } elseif ($filter === 'saved') {
            if (empty($savedIds)) {
                return [];
            }

            $conditions[] = 'posts.id IN (' . implode(',', $savedIds) . ')';
        }

        $whereSql = '';
        if (!empty($conditions)) {
            $whereSql = ' WHERE ' . implode(' AND ', $conditions);
        }

        $orderSql = 'posts.created_at DESC';
        if ($sort === 'title-asc') {
            $orderSql = 'posts.title ASC';
        } elseif ($sort === 'title-desc') {
            $orderSql = 'posts.title DESC';
        }

        $sql = "
            SELECT posts.*, users.name AS author_name
            FROM posts
            JOIN users ON posts.user_id = users.id
            {$whereSql}
            ORDER BY {$orderSql}
            LIMIT {$limit}
        ";

        $result = mysqli_query($conn, $sql);
        if (!$result) {
            return [];
        }

        $posts = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $posts[] = $row;
        }

        return $posts;
    }
}

if (!function_exists('renderHomepageStoryCard')) {
    function renderHomepageStoryCard(array $post, $isLoggedIn, $currentUserId = null)
    {
        $storyType = !empty($post['image']) ? 'Photo Story' : 'Travel Note';
        $storyDate = formatStoryDate($post['created_at'] ?? '');
        $readTime = estimateReadTime($post['description'] ?? '');
        $title = htmlspecialchars(substr((string) ($post['title'] ?? ''), 0, 50));
        $description = htmlspecialchars(substr((string) ($post['description'] ?? ''), 0, 100));
        $authorName = htmlspecialchars((string) ($post['author_name'] ?? 'Traveler'));
        $authorUrl = 'profile.php?user_id=' . (int) ($post['user_id'] ?? 0);
        $slug = htmlspecialchars((string) ($post['slug'] ?? ''));
        $image = !empty($post['image'])
            ? 'uploads/' . htmlspecialchars((string) $post['image'])
            : 'images/air.jpg';
        $postId = (int) ($post['id'] ?? 0);
        $canEdit = $isLoggedIn && $currentUserId !== null && (int) $currentUserId === (int) ($post['user_id'] ?? 0);

        ob_start();
        ?>
        <div class="card" id="post-<?php echo $postId; ?>" data-post-id="<?php echo $postId; ?>" data-title="<?php echo htmlspecialchars((string) ($post['title'] ?? '')); ?>" data-description="<?php echo htmlspecialchars((string) ($post['description'] ?? '')); ?>" data-author="<?php echo $authorName; ?>" data-has-image="<?php echo !empty($post['image']) ? '1' : '0'; ?>">
            <div class="card-img">
                <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars((string) ($post['title'] ?? 'Travel story')); ?>" loading="lazy" decoding="async">
                <div class="card-overlay-meta">
                    <span class="story-badge"><?php echo $storyType; ?></span>
                    <span class="story-meta-chip"><i class="far fa-clock"></i><?php echo $readTime; ?> min read</span>
                </div>
            </div>
            <div class="card-body">
                <div class="story-topline">
                    <span class="story-chip"><i class="far fa-calendar"></i><?php echo $storyDate; ?></span>
                </div>
                <h3>
                    <a href="post.php?slug=<?php echo $slug; ?>" class="card-title-link">
                        <?php echo $title; ?>
                    </a>
                </h3>
                <p class="story-excerpt"><?php echo $description; ?>...</p>
                <p class="author"><i class="far fa-user"></i> By <a href="<?php echo $authorUrl; ?>"><?php echo $authorName; ?></a></p>
            </div>
            <div class="card-footer premium-card-footer">
                <div class="stats footer-stats">
                    <button type="button" class="stat-item like-btn" data-post-id="<?php echo $postId; ?>" aria-label="Like post">
                        <i class="far fa-heart"></i>
                        <span class="like-count">0</span>
                    </button>
                    <button type="button" class="stat-item comment-btn" data-post-id="<?php echo $postId; ?>" aria-label="Add comment">
                        <i class="far fa-comment"></i>
                        <span class="comment-count">0</span>
                    </button>
                </div>
                <div class="card-actions footer-actions">
                    <button type="button" class="btn btn-ghost save-btn" data-post-id="<?php echo $postId; ?>" aria-label="Save post">
                        <i class="far fa-bookmark"></i>Save
                    </button>
                    <?php if ($canEdit): ?>
                        <a href="edit-post.php?id=<?php echo $postId; ?>" class="btn btn-secondary"><i class="fas fa-edit"></i>Edit</a>
                    <?php else: ?>
                        <button class="btn btn-secondary disabled" title="Only the author can edit"><i class="fas fa-edit"></i>Edit</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php

        return trim((string) ob_get_clean());
    }
}

if (!function_exists('renderHomepageStoriesGrid')) {
    function renderHomepageStoriesGrid(array $posts, $isLoggedIn, $currentUserId = null)
    {
        ob_start();

        if (empty($posts)) {
            ?>
            <div class="empty-state">
                <span class="section-kicker">Quiet for now</span>
                <h3>No stories yet</h3>
                <p>Be the first one to drop a travel memory worth reading.</p>
                <?php if ($isLoggedIn): ?>
                    <a href="add-post.php" class="btn btn-primary empty-state-cta">
                        Start Writing
                    </a>
                <?php endif; ?>
            </div>
            <?php
        } else {
            foreach ($posts as $post) {
                echo renderHomepageStoryCard($post, $isLoggedIn, $currentUserId);
            }
        }

        return trim((string) ob_get_clean());
    }
}
