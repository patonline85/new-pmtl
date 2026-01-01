<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pháp Môn Tâm Linh 心靈法門</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <script async src="https://www.tiktok.com/embed.js"></script>
</head>
<body>

<div class="container">
    <header class="main-header">
        <img src="logo.png" alt="Logo" class="logo">
        <div class="header-content">
            <h1>Pháp Môn Tâm Linh 心靈法門</h1>
            <p>Trang tin tức mới nhất</p>
        </div>
    </header>

    <div class="news-list">
        <?php
        // HÀM TỰ ĐỘNG BIẾN LINK YOUTUBE THÀNH VIDEO
        // (Chỉ giữ lại Youtube vì nó ổn định, FB/TikTok dùng mã nhúng sẽ tốt hơn)
        function formatContent($content) {
            if (empty($content)) return "";

            // 1. Tự động nhận diện Link Youtube -> Video
            $content = preg_replace(
                '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', 
                '<div class="video-responsive"><iframe src="https://www.youtube.com/embed/$1" allowfullscreen></iframe></div>', 
                $content
            );

            // 2. Chuyển xuống dòng thành thẻ <br> (nhưng không làm hỏng mã HTML nhúng)
            // Logic: Chỉ xuống dòng ở những chỗ không phải là thẻ HTML
            $content = nl2br($content);
            
            return $content;
        }

        $file = 'data/data.json';
        if (file_exists($file)) {
            $current_data = file_get_contents($file);
            $news = json_decode($current_data, true);

            if (!empty($news) && is_array($news)) {
                foreach ($news as $item) {
                    // Sửa lỗi Undefined array key: Kiểm tra xem có tồn tại title/content không
                    $title = isset($item['title']) ? $item['title'] : '(Không tiêu đề)';
                    $date = isset($item['date']) ? $item['date'] : '';
                    $raw_content = isset($item['content']) ? $item['content'] : '';

                    echo '<div class="news-item">';
                    echo '<span class="date">' . $date . '</span>';
                    echo '<h3 class="title">' . htmlspecialchars($title) . '</h3>';
                    
                    // HIỂN THỊ NỘI DUNG (Cho phép mã nhúng hoạt động)
                    echo '<div class="content">' . formatContent($raw_content) . '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="empty">Chưa có tin tức nào.</p>';
            }
        } else {
             echo '<p class="empty">Không tìm thấy tập tin dữ liệu.</p>';
        }
        ?>
    </div>
    
    <footer>
        <a href="admin.php">Đăng nhập quản trị</a>
    </footer>
</div>

</body>
</html>
