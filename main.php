<?php
require_once 'config.php';

// 处理发博请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $content = trim($_POST['content'] ?? '');
    if (strlen($content) > 0) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $content);
        $stmt->execute();
        $stmt->close();
        header('Location: index.php');
        exit;
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// 分页查询
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// 获取所有用户
$users_result = $conn->query("SELECT id, username FROM users ORDER BY created_at DESC");

// 构建查询条件
$conditions = [];
$params = [];
$types = '';

if ($search) {
    $conditions[] = "posts.content LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}
if ($user_id > 0) {
    $conditions[] = "posts.user_id = ?";
    $params[] = $user_id;
    $types .= 'i';
}

$where = '';
if (count($conditions) > 0) {
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        $where
        ORDER BY posts.created_at DESC 
        LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

// 统计总数
$count_sql = "SELECT COUNT(*) AS total FROM posts $where";
$count_stmt = $conn->prepare($count_sql);
if ($types) {
    $types_for_count = substr($types, 0, -2); // 去掉最后的 ii
    $count_params = array_slice($params, 0, -2);
    if ($types_for_count) {
        $count_stmt->bind_param($types_for_count, ...$count_params);
    }
}
$count_stmt->execute();
$count_res = $count_stmt->get_result();
$total_row = $count_res->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>首页 - 博博小站</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="sidebar">
    <h3>📚 快捷学习</h3>
    <div class="sidebar-links">
        <a href="https://www.xuetangx.com/" target="_blank"><span>📖</span>学堂在线</a>
        <a href="https://www.icourse163.org/" target="_blank"><span>🎓</span>中国大学MOOC</a>
        <a href="https://www.imooc.com/" target="_blank"><span>💻</span>慕课网</a>
        <a href="https://www.runoob.com/" target="_blank"><span>📘</span>菜鸟教程</a>
        <a href="https://study.163.com/" target="_blank"><span>📝</span>网易云课堂</a>
        <a href="https://www.zhihu.com/" target="_blank"><span>❓</span>知乎</a>
        <a href="https://www.csdn.net/" target="_blank"><span>�</span>CSDN</a>
        <a href="https://github.com/" target="_blank"><span>🐙</span>GitHub</a>
        <a href="https://www.bilibili.com/" target="_blank"><span>📺</span>B站</a>
    </div>
</div>

<div class="main-layout">
<div class="container">
    <div class="header">
        <h1>🐦 博博小站</h1>
        <div class="current-date" id="current-date"></div>
        <div class="user-info">
            <?php if (isset($_SESSION['username'])): ?>
                欢迎，<strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
                <a href="logout.php" class="logout-btn">退出</a>
            <?php else: ?>
                <a href="login.php">登录</a> | <a href="register.php">注册</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($user_id > 0): ?>
        <div class="filter-info">
            👤 查看 <strong><?php 
                $user_result = $conn->query("SELECT username FROM users WHERE id = $user_id");
                $user_row = $user_result->fetch_assoc();
                echo htmlspecialchars($user_row['username']);
            ?></strong> 的帖子
            <a href="index.php" class="clear-filter">× 清除</a>
        </div>
    <?php endif; ?>

    <div class="search-form">
        <form method="get">
            <input type="text" name="search" placeholder="搜索博文..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn">🔍 搜索</button>
        </form>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="post-form">
            <form method="post">
                <textarea name="content" rows="3" placeholder="说点啥，分享到博博..." required></textarea>
                <button type="submit" class="btn">📨 发布博文</button>
            </form>
        </div>
    <?php else: ?>
        <div class="notice">💡 <a href="login.php">登录</a> 后才能在博博发言哦</div>
    <?php endif; ?>

    <div class="feed">
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-item">
                    <div class="post-header">
                        <div class="post-user">👤 <?php echo htmlspecialchars($post['username']); ?></div>
                        <?php if (isset($_SESSION['username']) && ($_SESSION['username'] === 'admin' || $_SESSION['user_id'] == $post['user_id'])): ?>
                            <div class="post-actions">
                                <a href="edit.php?id=<?php echo $post['id']; ?>" class="edit-btn">编辑</a>
                                <a href="delete.php?id=<?php echo $post['id']; ?>" class="delete-btn" onclick="return confirm('确定删除？')">删除</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                    <div class="post-footer">
                        <span class="post-time">📅 <?php echo $post['created_at']; ?></span>
                        <a href="like.php?id=<?php echo $post['id']; ?>" class="like-btn">❤️ <?php echo $post['likes']; ?> 赞</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty">目前博博还空空如也，快来抢沙发！</div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php 
            $pagination_params = '';
            if ($search) $pagination_params .= '&search=' . urlencode($search);
            if ($user_id > 0) $pagination_params .= '&user_id=' . $user_id;
            ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i . $pagination_params; ?>" class="<?php echo $i == $page ? 'current' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<div class="user-sidebar">
    <h3>👥 用户列表</h3>
    <div class="user-list">
        <a href="index.php" class="user-item <?php echo $user_id == 0 ? 'active' : ''; ?>">全部用户</a>
        <?php while ($user = $users_result->fetch_assoc()): ?>
            <a href="?user_id=<?php echo $user['id']; ?>" class="user-item <?php echo $user_id == $user['id'] ? 'active' : ''; ?>">
                👤 <?php echo htmlspecialchars($user['username']); ?>
            </a>
        <?php endwhile; ?>
    </div>
</div>
</div>

<script>
function updateTime() {
    var now = new Date();
    var year = now.getFullYear();
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var day = String(now.getDate()).padStart(2, '0');
    var hours = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');
    var seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('current-date').textContent = year + '年' + month + '月' + day + '日 ' + hours + ':' + minutes + ':' + seconds;
}
updateTime();
setInterval(updateTime, 1000);
</script>
</body>
</html>