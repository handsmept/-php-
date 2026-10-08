<?php
session_start();

// 生成验证码
$code = '';
for ($i = 0; $i < 4; $i++) {
    $code .= rand(0, 9);
}

// 存入session
$_SESSION['captcha'] = $code;

// 创建图片
$width = 100;
$height = 35;
$image = imagecreatetruecolor($width, $height);

// 背景色（浅灰）
$bg = imagecolorallocate($image, 220, 220, 220);
imagefill($image, 0, 0, $bg);

// 文字色（深灰）
$text_color = imagecolorallocate($image, 50, 50, 50);

// 绘制验证码文字
for ($i = 0; $i < 4; $i++) {
    $x = 15 + $i * 20;
    $y = rand(8, 15);
    imagestring($image, 5, $x, $y, $code[$i], $text_color);
}

// 添加干扰线
for ($i = 0; $i < 3; $i++) {
    $line_color = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
}

// 输出图片
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>