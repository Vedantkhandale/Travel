<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=UTF-8');

include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/story_helpers.php';
session_start();

$savedIdsRaw = trim((string) ($_GET['saved_ids'] ?? ''));
$savedIds = [];
if ($savedIdsRaw !== '') {
    $savedIds = array_filter(array_map('intval', explode(',', $savedIdsRaw)));
}

$posts = fetchHomepageStories($conn, [
    'search' => $_GET['search'] ?? '',
    'sort' => $_GET['sort'] ?? 'newest',
    'filter' => $_GET['filter'] ?? 'all',
    'saved_ids' => $savedIds,
    'limit' => 12,
]);

$isLoggedIn = isset($_SESSION['user_id']);
$currentUserId = $isLoggedIn ? (int) $_SESSION['user_id'] : null;

echo json_encode([
    'html' => renderHomepageStoriesGrid($posts, $isLoggedIn, $currentUserId),
    'count' => count($posts),
]);
