<?php
require_once 'config.php';

// 检查权限：管理员或帖子作者
if (!isset($_SESSION['username'])) {
    die('请先登录');
}

// 获取帖子ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die('帖子ID无效');
}

// 获取帖子信息
$stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die('帖子不存在');
}

// 检查权限：管理员或帖子作者
if ($_SESSION['username'] !== 'admin' && $_SESSION['user_id'] != $post['user_id']) {
    die('无权限删除此帖子');
}

// 删除帖子
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: index.php');
exit;
?>