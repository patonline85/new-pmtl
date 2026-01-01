<?php
session_start();

// CẤU HÌNH
$file = 'data/data.json';
$message = "";

// 1. LẤY MẬT KHẨU TỪ DOCKER
$env_pass = getenv('ADMIN_PASSWORD');
$real_pass = $env_pass ? $env_pass : '123456';

// 2. XỬ LÝ ĐĂNG XUẤT
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// 3. XỬ LÝ ĐĂNG NHẬP
if (isset($_POST['login'])) {
    $pass = isset($_POST['password']) ? $_POST['password'] : '';
    if ($pass === $real_pass) { 
        $_SESSION['loggedin'] = true;
    } else {
        $message = "<span class='msg-error'>Sai mật khẩu!</span>";
    }
}

// HÀM ĐỌC DỮ LIỆU
function getData($file) {
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }
    return [];
}

// HÀM LƯU DỮ LIỆU
function saveData($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// 4. XỬ LÝ GỬI BÀI (THÊM MỚI HOẶC CẬP NHẬT)
if (isset($_POST['save_post']) && isset($_SESSION['loggedin'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $edit_id = $_POST['edit_id']; // Lấy ID bài đang sửa (nếu có)

    $current_data = getData($file);

    if ($edit_id !== "") {
        // --- TRƯỜNG HỢP SỬA BÀI ---
        $index = (int)$edit_id;
        if (isset($current_data[$index])) {
            $current_data[$index]['title'] = $title;
            $current_data[$index]['content'] = $content;
            // Giữ nguyên ngày cũ hoặc cập nhật ngày mới tùy bạn (ở đây mình giữ ngày cũ)
            $message = "<span class='msg-success'>Đã cập nhật bài viết!</span>";
        }
    } else {
        // --- TRƯỜNG HỢP THÊM MỚI ---
        $new_post = array(
            'title' => $title,
            'content' => $content,
            'date' => date("d/m/Y H:i")
        );
        array_unshift($current_data, $new_post); // Đưa lên đầu
        $message = "<span class='msg-success'>Đăng bài mới thành công!</span>";
    }

    if(saveData($file, $current_data)) {
        // Reset form sau khi lưu
        $title = ""; $content = ""; $edit_id = "";
    } else {
        $message = "<span class='msg-error'>Lỗi ghi file data.json!</span>";
    }
}

// 5. XỬ LÝ XÓA BÀI
if (isset($_GET['delete']) && isset($_SESSION['loggedin'])) {
    $delete_id = (int)$_GET['delete'];
    $current_data = getData($file);
    
    if (isset($current_data[$delete_id])) {
        array_splice($current_data, $delete_id, 1); // Cắt bỏ bài viết khỏi mảng
        saveData($file, $current_data);
        $message = "<span class='msg-success'>Đã xóa bài viết!</span>";
    }
    // Xóa xong quay lại trang admin sạch sẽ
    header("Location: admin.php"); 
    exit;
}

// 6. CHUẨN BỊ DỮ LIỆU ĐỂ HIỂN THỊ HOẶC SỬA
$editing_post = null;
$edit_mode = false;
$all_posts = getData($file);

// Nếu bấm nút Sửa, lấy dữ liệu đổ vào Form
if (isset($_GET['edit']) && isset($_SESSION['loggedin'])) {
    $edit_index = (int)$_GET['edit'];
    if (isset($all_posts[$edit_index])) {
        $editing_post = $all_posts[$edit_index];
        $edit_mode = true;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị - Pháp Môn Tâm Linh</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    
    <?php if (!isset($_SESSION['loggedin'])): ?>
        <h2 style="text-align:center; color:#8B4513;">Đăng Nhập Admin</h2>
        <p style="text-align:center"><?php echo $message; ?></p>
        <form method="post" style="max-width:400px; margin:0 auto;">
            <div class="form-group">
                <label>Mật khẩu:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login" style="width:100%">Đăng Nhập</button>
        </form>
        <div style="text-align:center; margin-top:20px;">
            <a href="index.php">← Về trang chủ</a>
        </div>

    <?php else: ?>
        <header class="admin-header">
            <h2><?php echo $edit_mode ? "Đang Sửa Bài" : "Viết Bài Mới"; ?></h2>
            <div>
                <a href="admin.php" class="btn-secondary">Viết bài mới</a>
                <a href="index.php" target="_blank" class="btn-secondary">Xem trang</a>
                <a href="?logout=true" class="btn-logout">[Thoát]</a>
            </div>
        </header>
        
        <p><?php echo $message; ?></p>
        
        <form method="post" action="admin.php">
            <input type="hidden" name="edit_id" value="<?php echo $edit_mode ? $_GET['edit'] : ''; ?>">
            
            <div class="form-group">
                <label>Tiêu đề:</label>
                <input type="text" name="title" required 
                       value="<?php echo $edit_mode ? htmlspecialchars($editing_post['title']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Nội dung:</label>
                <textarea name="content" rows="10" required><?php echo $edit_mode ? htmlspecialchars($editing_post['content']) : ''; ?></textarea>
            </div>
            
            <button type="submit" name="save_post">
                <?php echo $edit_mode ? "Lưu Thay Đổi" : "Đăng Bài Ngay"; ?>
            </button>
            <?php if($edit_mode): ?>
                <a href="admin.php" style="margin-left:10px; color:#666;">Hủy bỏ</a>
            <?php endif; ?>
        </form>

        <hr style="margin: 40px 0; border: 0; border-top: 1px solid #E6D5B8;">

        <h3 style="color:#8B4513;">Danh sách bài đã đăng</h3>
        <div class="admin-list">
            <?php if (!empty($all_posts)): ?>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#FFF8E1; color:#5D4037;">
                            <th style="padding:10px; text-align:left;">STT</th>
                            <th style="padding:10px; text-align:left;">Tiêu đề</th>
                            <th style="padding:10px; text-align:left;">Ngày</th>
                            <th style="padding:10px; text-align:right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_posts as $index => $post): ?>
                            <tr style="border-bottom:1px solid #eee;">
                                <td style="padding:10px; width:50px; color:#999;"><?php echo $index + 1; ?></td>
                                <td style="padding:10px; font-weight:bold; color:#3E2723;">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </td>
                                <td style="padding:10px; font-size:13px; color:#A67B5B;">
                                    <?php echo $post['date']; ?>
                                </td>
                                <td style="padding:10px; text-align:right;">
                                    <a href="admin.php?edit=<?php echo $index; ?>" class="action-btn edit-btn">Sửa</a>
                                    <a href="admin.php?delete=<?php echo $index; ?>" 
                                       class="action-btn del-btn"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa bài này không?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Chưa có bài viết nào.</p>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>

</body>
</html>
