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

// 获取帖子内容
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die('帖子不存在');
}

// 检查权限：管理员或帖子作者
if ($_SESSION['username'] !== 'admin' && $_SESSION['user_id'] != $post['user_id']) {
    die('无权限编辑此帖子');
}

$error = '';

// 处理编辑请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    if (strlen($content) > 0) {
        $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ?");
        $stmt->bind_param("si", $content, $id);
        $stmt->execute();
        header('Location: index.php');
        exit;
    } else {
        $error = '内容不能为空';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>编辑帖子 - 博博小站</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:400px; margin: 50px auto;">
    <div class="header">
        <h1>🐦 博博小站</h1>
        <p>编辑帖子</p>
    </div>
    
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="post" class="auth-form">
        <div class="form-group">
            <label>帖子内容</label>
            <textarea name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <button type="submit" class="btn">保存</button>
    </form>
    
    <p><a href="index.php">返回首页</a></p>
</div>
</body>
</html>