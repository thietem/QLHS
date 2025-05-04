<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// Chỉ cho admin mới được vào
if ($_SESSION['role'] !== 'admin') {
    echo "Bạn không có quyền truy cập trang này.";
    exit();
}
// Thêm lớp mới
if (isset($_POST['add'])) {
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $year = intval($_POST['year']);
    $grade_id = intval($_POST['grade_id']);

    $sql = "INSERT INTO classes (code, name, year, grade_id) VALUES ('$code', '$name', $year, $grade_id)";
    mysqli_query($conn, $sql);
    header('Location: classes.php');
    exit();
}

// Xóa lớp
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM classes WHERE id = $id");
    header('Location: classes.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Quản lý Lớp</title>
</head>
<body>
    <h2>Quản lý Lớp học</h2>
    <?php if ($_SESSION['role'] === 'admin'): ?>
    <form method="POST">
        <input type="text" name="code" placeholder="Mã lớp" required>
        <input type="text" name="name" placeholder="Tên lớp" required>
        <input type="text" name="year" placeholder="Năm học" required>
        <select name="grade_id" required>
            <option value="">-- Chọn khối --</option>
            <?php
            $grades = mysqli_query($conn, "SELECT * FROM grades");
            while ($row = mysqli_fetch_assoc($grades)) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select>
        <input type="submit" name="add" value="Thêm Lớp">
    </form>
    <?php endif; ?>
    <h3>Danh sách lớp</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Mã lớp</th>
            <th>Tên lớp</th>
            <th>Năm học</th>
            <th>Khối</th>
            <th>Hành động</th>
        </tr>
        <?php
        $classes = mysqli_query($conn, "SELECT classes.*, grades.name as grade_name FROM classes JOIN grades ON classes.grade_id = grades.id");
        while ($row = mysqli_fetch_assoc($classes)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['code']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['year']}</td>";
            echo "<td>{$row['grade_name']}</td>";
            if ($_SESSION['role'] === 'admin') {
                echo "<td><a href='classes.php?delete={$row['id']}' onclick=\"return confirm('Bạn có chắc chắn muốn xóa?')\">Xóa</a></td>";
            }
            echo "</tr>";
        }
        ?>
    </table>

    <br><a href="dashboard.php">Quay lại Trang chính</a>
</body>
</html>
