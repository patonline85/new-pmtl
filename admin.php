<?php
session_start();

// Đường dẫn file dữ liệu (khớp với cấu trúc trên Docker)
$file = 'data/data.json';
$message = "";

// 1. LẤY MẬT KHẨU TỪ BIẾN MÔI TRƯỜNG (DOCKER)
// Nếu không thiết lập trong Portainer thì mặc định là '123456'
$env_pass = getenv('ADMIN_PASSWORD');
$real_pass = $env_pass ? $env_pass : '123456';

// 2. Xử lý Đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// 3. Xử lý Đăng nhập
if (isset($_POST['login'])) {
    // Chỉ lấy password khi form đã được gửi
    $pass = isset($_POST['password']) ? $_POST['password'] : '';

    if ($pass === $real_pass) { 
        $_SESSION['loggedin'] = true;
    } else {
        $message = "<span style='color:red'>Sai mật khẩu!</span>";
    }
}

// 4. Xử lý Đăng bài (Chỉ khi đã đăng nhập)
if (isset($_POST['post_news']) && isset($_SESSION['loggedin'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // Đọc dữ liệu cũ
    if (file_exists($file)) {
        $current_data = file_get_contents($file);
        $array_data = json_decode($current_data, true);
    } else {
        $array_data = [];
    }
    
    if (!is_array($array_data)) $array_data = [];

    // Tạo bài mới
    $new_post = array(
        'title' => $title,
        'content' => $content,
        'date' => date("d/m/Y H:i")
    );
    
    // Đưa lên đầu danh sách
    array_unshift($array_data, $new_post);
    
    // Lưu file
    if(file_put_contents($file, json_encode($array_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        $message = "<span style='color:green'>Đăng bài thành công!</span>";
    } else {
        $message = "<span style='color:red'>Lỗi ghi file. Kiểm tra quyền thư mục data.</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Tin Tức</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <?php if (!isset($_SESSION['loggedin'])): ?>
        <h2>Đăng Nhập Admin</h2>
        <p><?php echo $message; ?></p>
        <form method="post">
            <div class="form-group">
                <label>Mật khẩu:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login">Đăng Nhập</button>
        </form>
        <br><a href="index.php">← Về trang chủ</a>

    <?php else: ?>
        <header style="display:flex; justify-content:space-between; align-items:center;">
            <h2>Viết Bài Mới</h2>
            <a href="?logout=true" style="color:red; text-decoration:none;">[Đăng xuất]</a>
        </header>
        
        <p><?php echo $message; ?></p>
        
        <form method="post">
            <div class="form-group">
                <label>Tiêu đề:</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>Nội dung:</label>
                <textarea name="content" rows="10" required></textarea>
            </div>
            
            <button type="submit" name="post_news">Gửi Bài</button>
        </form>
        <br>
        <a href="index.php" target="_blank">→ Xem trang chủ</a>
    <?php endif; ?>
</div>

</body>
</html>
