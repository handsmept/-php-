<?php
// 生成密码哈希值
$password = '123456';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "密码: $password\n";
echo "哈希值: $hash\n";
?>