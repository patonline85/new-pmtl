<?php
$message = "";

// Xử lý khi bấm nút Đăng bài
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // MẬT KHẨU ĐƠN GIẢN (Bạn có thể đổi ở đây)

    $env_pass = getenv('ADMIN_PASSWORD');
    $real_pass = $env_pass ? $env_pass : '123456';
    
    if ($pass === $real_pass) {
        $file = 'data.json';
        
        // Lấy dữ liệu cũ
        $current_data = file_get_contents($file);
        $array_data = json_decode($current_data, true);
        if (!$array_data) $array_data = [];

        // Tạo bài viết mới
        $new_post = array(
            'title' => $title,
            'content' => $content,
            'date' => date("d/m/Y") // Lấy ngày hiện tại
        );

        // Đưa bài mới lên đầu danh sách
        array_unshift($array_data, $new_post);

        // Lưu lại vào file JSON
        if(file_put_contents($file, json_encode($array_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $message = "<span style='color:green'>Đăng bài thành công! <a href='index.php'>Xem trang chủ</a></span>";
        } else {
            $message = "<span style='color:red'>Lỗi không ghi được file. Kiểm tra quyền ghi (permission).</span>";
        }
    } else {
        $message = "<span style='color:red'>Sai mật khẩu!</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Tin Tức</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Soạn Thảo Tin Tức</h2>
    <p><?php echo $message; ?></p>
    
    <form method="post" action="">
        <div class="form-group">
            <label>Tiêu đề:</label>
            <input type="text" name="title" required placeholder="Nhập tiêu đề bài viết...">
        </div>
        
        <div class="form-group">
            <label>Nội dung:</label>
            <textarea name="content" rows="10" required placeholder="Nhập nội dung văn bản..."></textarea>
        </div>

        <div class="form-group">
            <label>Mật khẩu đăng bài:</label>
            <input type="password" name="password" required placeholder="Nhập mật khẩu quản trị">
        </div>

        <button type="submit">Đăng Bài</button>
    </form>
    <br>
    <a href="index.php">← Quay lại trang tin</a>
</div>

</body>

</html>
