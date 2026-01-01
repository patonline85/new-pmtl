<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pháp Môn Tâm Linh 心靈法門</title>
	<link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v18.0"></script>
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
        function displayContent($content) {
            if (empty($content)) return "";

            // 1. TỰ ĐỘNG XỬ LÝ LINK TIKTOK (MỚI)
            // Bạn chỉ cần dán link: https://www.tiktok.com/@user/video/123456789
            // Code sẽ tự tạo ra khung Blockquote chuẩn
            $content = preg_replace_callback(
                '#https://www\.tiktok\.com/@[a-zA-Z0-9\._-]+/video/(\d+)#',
                function($matches) {
                    $full_link = $matches[0];
                    $video_id = $matches[1];
                    return '
                    <blockquote class="tiktok-embed" cite="'.$full_link.'" data-video-id="'.$video_id.'" style="max-width: 605px;min-width: 325px;">
                        <section> <a target="_blank" href="'.$full_link.'">Đang tải video TikTok...</a> </section>
                    </blockquote>';
                },
                $content
            );

            // 2. Tự động xử lý Link Youtube
            $content = preg_replace(
                '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', 
                '<div class="video-responsive"><iframe src="https://www.youtube.com/embed/$1" allowfullscreen></iframe></div>', 
                $content
            );

            // 3. Biến link web thường thành thẻ <a> (Trừ link TikTok/Youtube đã xử lý ở trên)
            // Regex này bỏ qua các link nằm trong thẻ cite="" hoặc src=""
            $content = preg_replace(
                '/(?<!cite="|src="|href="|">)(https?:\/\/[^\s<]+)/', 
                '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', 
                $content
            );

            // 4. Xuống dòng
            return nl2br($content);
        }

        $file = 'data/data.json';
        if (file_exists($file)) {
            $current_data = file_get_contents($file);
            $news = json_decode($current_data, true);

            if (!empty($news) && is_array($news)) {
                foreach ($news as $item) {
                    $title = isset($item['title']) ? $item['title'] : '';
                    $date = isset($item['date']) ? $item['date'] : '';
                    $content = isset($item['content']) ? $item['content'] : '';

                    echo '<div class="news-item">';
                    echo '<span class="date">' . $date . '</span>';
                    echo '<h3 class="title">' . htmlspecialchars($title) . '</h3>';
                    
                    echo '<div class="content-wrapper content-collapsed">';
                    echo displayContent($content);
                    echo '</div>';
                    
                    echo '<button class="btn-readmore" onclick="toggleContent(this)">Xem thêm ▼</button>';
                    echo '</div>';
                }
            } else {
                echo '<p class="empty">Chưa có tin tức nào.</p>';
            }
        } else {
             echo '<p class="empty">Chưa khởi tạo dữ liệu.</p>';
        }
        ?>
    </div>
    
    <footer>
        <a href="admin.php">Đăng nhập quản trị</a>
    </footer>
</div>

<script>
    function toggleContent(btn) {
        var contentDiv = btn.previousElementSibling;
        if (contentDiv.classList.contains('content-collapsed')) {
            contentDiv.classList.remove('content-collapsed');
            btn.innerHTML = "Thu gọn ▲";
        } else {
            contentDiv.classList.add('content-collapsed');
            btn.innerHTML = "Xem thêm ▼";
            contentDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    window.addEventListener('load', function() {
        var contents = document.querySelectorAll('.content-wrapper');
        contents.forEach(function(div) {
            if (div.scrollHeight <= 280) {
                div.classList.remove('content-collapsed'); 
                var btn = div.nextElementSibling;
                if (btn && btn.classList.contains('btn-readmore')) {
                    btn.classList.add('hidden');
                }
            }
        });
    });
</script>

</body>
</html>
