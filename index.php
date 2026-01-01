<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pháp Môn Tâm Linh 心靈法門</title>
	<link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <script async src="https://www.tiktok.com/embed.js"></script>
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v18.0"></script>
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

            // 1. [MỚI] SỬA LỖI TIKTOK & FACEBOOK EMBED
            // Loại bỏ dấu xuống dòng (\n) nằm bên trong thẻ mở HTML để tránh bị nl2br làm gãy
            // Ví dụ: <blockquote \n class="..."> sẽ thành <blockquote class="...">
            $content = preg_replace_callback(
                '/<(blockquote|iframe|script|div)([^>]*)>/s',
                function ($matches) {
                    // Xóa hết xuống dòng trong thẻ mở
                    return '<' . $matches[1] . str_replace(["\n", "\r"], " ", $matches[2]) . '>';
                },
                $content
            );

            // 2. Tự động chuyển link Youtube trần thành Video Player
            $content = preg_replace(
                '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', 
                '<div class="video-responsive"><iframe src="https://www.youtube.com/embed/$1" allowfullscreen></iframe></div>', 
                $content
            );

            // 3. Biến link web thành thẻ <a> bấm được (trừ link trong src/href)
            $content = preg_replace(
                '/(?<!src="|href="|">)(https?:\/\/[^\s<]+)/', 
                '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', 
                $content
            );

            // 4. Xuống dòng văn bản
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
                    
                    // Nội dung
                    echo '<div class="content-wrapper content-collapsed">';
                    echo displayContent($content);
                    echo '</div>';
                    
                    // Nút Xem Thêm
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
        // Tìm khung nội dung phía trước
        var contentDiv = btn.previousElementSibling;
        
        if (contentDiv.classList.contains('content-collapsed')) {
            // MỞ RA:
            contentDiv.classList.remove('content-collapsed');
            // Gán chiều cao tối đa lớn để video dọc hiển thị hết
            contentDiv.style.maxHeight = "none"; 
            btn.innerHTML = "Thu gọn ▲";
        } else {
            // THU LẠI:
            contentDiv.classList.add('content-collapsed');
            // Xóa style inline để quay về giới hạn 280px của CSS
            contentDiv.style.maxHeight = null; 
            btn.innerHTML = "Xem thêm ▼";
            
            // Cuộn nhẹ về vị trí nút để người đọc không bị lạc
            // Sử dụng 'nearest' để đỡ bị giật màn hình
            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // Logic kiểm tra độ dài bài viết khi tải trang
    window.addEventListener('load', function() {
        // Chờ thêm 1 giây để TikTok/Youtube tải xong mới tính toán chiều cao
        setTimeout(function() {
            var contents = document.querySelectorAll('.content-wrapper');
            contents.forEach(function(div) {
                // Nếu bài viết thực sự ngắn (dưới 280px)
                if (div.scrollHeight <= 280) {
                    div.classList.remove('content-collapsed'); 
                    var btn = div.nextElementSibling;
                    if (btn && btn.classList.contains('btn-readmore')) {
                        btn.style.display = 'none'; // Ẩn nút đi
                    }
                }
            });
        }, 1000); // Delay 1000ms
    });
</script>

</body>
</html>

