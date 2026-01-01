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
        // HÀM XỬ LÝ VIDEO THÔNG MINH
        function autoEmbedContent($content) {
            // 1. Xử lý Youtube Link (Dạng watch?v= hoặc youtu.be/)
            $content = preg_replace(
                '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', 
                '<div class="video-responsive"><iframe src="https://www.youtube.com/embed/$1" allowfullscreen></iframe></div>', 
                $content
            );

            // 2. Xử lý Facebook Video Link
            // Tìm link facebook video và chuyển thành iframe
            // Lưu ý: Link phải ở dạng công khai (Public)
            $content = preg_replace_callback(
                '/(https:\/\/www\.facebook\.com\/(?:watch\/\?v=|video\.php\?v=|.+?\/videos\/)(\d+)\/?)/',
                function($matches) {
                    $encodedUrl = urlencode($matches[0]);
                    return '<div class="video-responsive"><iframe src="https://www.facebook.com/plugins/video.php?href='.$encodedUrl.'&show_text=false&t=0" scrolling="no" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe></div>';
                },
                $content
            );

            // 3. Xử lý xuống dòng cho văn bản thường
            $content = nl2br($content);
            
            return $content;
        }

        $file = 'data/data.json';
        if (file_exists($file)) {
            $current_data = file_get_contents($file);
            $news = json_decode($current_data, true);

            if (!empty($news) && is_array($news)) {
                foreach ($news as $item) {
                    echo '<div class="news-item">';
                    echo '<span class="date">' . $item['date'] . '</span>';
                    echo '<h3 class="title">' . htmlspecialchars($item['title']) . '</h3>';
                    
                    // GỌI HÀM XỬ LÝ NỘI DUNG Ở ĐÂY
                    // Lưu ý: Không dùng htmlspecialchars cho content nữa vì ta cần render mã HTML của iframe
                    // Thay vào đó ta sẽ lọc cơ bản để tránh lỗi XSS nếu cần (nhưng đây là admin post nên tạm tin tưởng)
                    echo '<div class="content">' . autoEmbedContent($item['content']) . '</div>';
                    echo '<div class="content">' . autoEmbedContent($clean_content) . '</div>';
                    
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

