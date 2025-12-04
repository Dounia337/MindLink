<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

try {
    // Check if user already liked the post
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $existing_like = $stmt->fetch();
    
    if ($existing_like) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
        $liked = false;
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$post_id, $user_id]);
        $liked = true;
    }
    
    // Get updated like count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $result = $stmt->fetch();
    $like_count = $result['count'];
    
    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'like_count' => $like_count
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>