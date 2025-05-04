
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lấy thông tin điểm cần sửa
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM scores WHERE id = $id");
    $score = mysqli_fetch_assoc($result);

    if (!$score) {
        echo "Không tìm thấy điểm!";
        exit();
    }
} else {
    echo "Không có id điểm!";
    exit();
}

if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $math = floatval($_POST['math']);
    $physics = floatval($_POST['physics']);
    $chemistry = floatval($_POST['chemistry']);
    $average = ($math + $physics + $chemistry) / 3;

    // Kiểm tra xem điểm cũ có tồn tại không
$old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM scores WHERE id = $id"));

// Lấy thông tin người sửa
$user_id = $_SESSION['user_id']; // Giả sử ID người dùng đã đăng nhập

// Chuẩn bị câu lệnh SQL để ghi lịch sử sửa điểm
$stmt = $conn->prepare("INSERT INTO score_history (score_id, old_math, new_math, old_physics, new_physics, old_chemistry, new_chemistry, updated_by, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

// Gắn giá trị vào câu lệnh SQL
$stmt->bind_param("iddddddi",
    $id,
    $old['math'], $math,
    $old['physics'], $physics,
    $old['chemistry'], $chemistry,
    $user_id
);

// Thực thi câu lệnh và kiểm tra lỗi
if (!$stmt->execute()) {
    echo "Lỗi ghi lịch sử: " . $stmt->error;
    exit();
}else {
    echo "Lịch sử sửa điểm đã được ghi thành công.";
}


    // Cập nhật điểm mới
    $sql = "UPDATE scores SET math = $math, physics = $physics, chemistry = $chemistry, average = $average WHERE id = $id";
    mysqli_query($conn, $sql);

    header('Location: scores.php');
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Sửa điểm</title>
</head>
<body>
    <h2>Sửa điểm học sinh</h2>

    <form method="POST">
    <input type="hidden" name="id" value="<?php echo $score['id']; ?>">

        <label>Toán:</label><br>
        <input type="number" step="0.01" name="math" value="<?php echo $score['math']; ?>" required><br><br>

        <label>Lý:</label><br>
        <input type="number" step="0.01" name="physics" value="<?php echo $score['physics']; ?>" required><br><br>

        <label>Hóa:</label><br>
        <input type="number" step="0.01" name="chemistry" value="<?php echo $score['chemistry']; ?>" required><br><br>

        <input type="submit" name="update" value="Cập nhật điểm">
    </form>

    <br><a href="scores.php">Quay lại danh sách điểm</a>
</body>
</html>
