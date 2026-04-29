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
$commentText = trim($_POST['comment'] ?? '');

if ($postId <= 0 || empty($commentText)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

include '../includes/story_helpers.php';

$success = addComment($conn, $postId, $userId, $commentText);
$commentCount = getPostCommentCount($conn, $postId);

echo json_encode([
    'success' => $success,
    'comment_count' => $commentCount
]);
?>