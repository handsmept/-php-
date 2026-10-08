<?php
require_once 'config.php';

$error = '';
$success = '';

// 处理注册请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $captcha = trim($_POST['captcha'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = '请填写所有字段';
    } elseif (empty($captcha)) {
        $error = '请填写验证码';
    } elseif ($captcha !== $_SESSION['captcha']) {
        $error = '验证码错误';
    } elseif (strlen($username) < 3) {
        $error = '用户名至少3个字符';
    } elseif (strlen($password) < 6) {
        $error = '密码至少6个字符';
    } elseif ($password !== $confirm_password) {
        $error = '两次密码不一致';
    } else {
        // 检查用户是否已存在
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = '用户名已存在';
        } else {
            // 创建用户
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $success = '注册成功！请登录';
            } else {
                $error = '注册失败，请重试';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>注册 - 博博小站</title>
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
        <a href="https://www.csdn.net/" target="_blank"><span>💾</span>CSDN</a>
        <a href="https://github.com/" target="_blank"><span>🐙</span>GitHub</a>
        <a href="https://www.bilibili.com/" target="_blank"><span>📺</span>B站</a>
    </div>
</div>
<div class="container" style="max-width:400px; margin: 50px auto;">
    <div class="header">
        <h1>🐦 博博小站</h1>
        <p>创建你的账户</p>
    </div>
    
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <p><a href="login.php" class="btn">去登录</a></p>
    <?php else: ?>
        <form method="post" class="auth-form">
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" required minlength="3">
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label>确认密码</label>
                <input type="password" name="confirm_password" required minlength="6">
            </div>
            <div class="form-group">
                <label>验证码</label>
                <div class="captcha-row">
                    <input type="text" name="captcha" required maxlength="4" style="width:80px;">
                    <img src="captcha.php" alt="验证码" class="captcha-img" onclick="this.src='captcha.php?'+Math.random()">
                    <span class="captcha-tip">点击刷新</span>
                </div>
            </div>
            <button type="submit" class="btn">注册</button>
        </form>
    <?php endif; ?>
    
    <p class="auth-link">已有账户？<a href="login.php">立即登录</a></p>
    <p><a href="index.php">返回首页</a></p>
</div>
</body>
</html>