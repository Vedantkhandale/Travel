<?php
include '../includes/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$postId = (int) ($_POST['post_id'] ?? 0);

if ($postId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

include '../includes/story_helpers.php';

$liked = toggleLike($conn, $postId, $userId);
$likeCount = getPostLikeCount($conn, $postId);

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'like_count' => $likeCount
]);
?>