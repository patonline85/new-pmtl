<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin Tức Mới</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Tin Tức Phật Pháp</h1>
        <p>Tổng hợp tin tức mới nhất</p>
    </header>

    <div class="news-list">
        <?php
        $file = 'data/data.json';
        if (file_exists($file)) {
            $current_data = file_get_contents($file);
            $news = json_decode($current_data, true);

            // Kiểm tra nếu có dữ liệu
            if (!empty($news)) {
                foreach ($news as $item) {
                    echo '<div class="news-item">';
                    echo '<span class="date">' . $item['date'] . '</span>';
                    echo '<h3 class="title">' . htmlspecialchars($item['title']) . '</h3>';
                    echo '<div class="content">' . nl2br(htmlspecialchars($item['content'])) . '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="empty">Chưa có tin tức nào.</p>';
            }
        }
        ?>
    </div>
    
    <footer>
        <a href="admin.php" style="font-size: 12px; color: #999; text-decoration: none;">Đăng nhập quản trị</a>
    </footer>
</div>

</body>
</html>