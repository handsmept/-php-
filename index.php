<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>博博小站 - 欢迎</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Microsoft YaHei', sans-serif;
        }
        
        .welcome-container {
            text-align: center;
            animation: fadeIn 1s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .logo {
            font-size: 72px;
            margin-bottom: 20px;
        }
        
        h1 {
            font-size: 48px;
            color: #fff;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .subtitle {
            font-size: 20px;
            color: rgba(255,255,255,0.9);
            margin-bottom: 50px;
        }
        
        .enter-btn {
            display: inline-block;
            padding: 18px 60px;
            font-size: 22px;
            color: #667eea;
            background: #fff;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .enter-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            background: #f0f0f0;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            color: rgba(255,255,255,0.6);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="logo">📝</div>
        <h1>欢迎来到博博小站</h1>
        <p class="subtitle">校园社交博客系统</p>
        <a href="main.php" class="enter-btn">点击进入</a>
    </div>
    <div class="footer">博博小站 · Bobo's Blog</div>
</body>
</html>
