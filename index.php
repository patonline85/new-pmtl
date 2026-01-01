<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pháp Môn Tâm Linh 心靈法門</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
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
        // Lưu ý đường dẫn file data
        $file = 'data/data.json';
        if (file_exists($file)) {
            $current_data = file_get_contents($file);
            $news = json_decode($current_data, true);

            if (!empty($news) && is_array($news)) {
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

