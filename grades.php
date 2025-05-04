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
// Thêm khối mới
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $sql = "INSERT INTO grades (name) VALUES ('$name')";
    mysqli_query($conn, $sql);
    header('Location: grades.php');
    exit();
}

// Xóa khối
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM grades WHERE id = $id");
    header('Location: grades.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
    <title>Quản lý Khối</title>
</head>
<body>
    <h2>Quản lý Khối lớp</h2>

    <form method="POST">
        <input type="text" name="name" placeholder="Tên khối" required>
        <input type="submit" name="add" value="Thêm Khối">
    </form>

    <h3>Danh sách khối</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Tên Khối</th>
            <th>Hành động</th>
        </tr>
        <?php
        $grades = mysqli_query($conn, "SELECT * FROM grades");
        while ($row = mysqli_fetch_assoc($grades)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td><a href='grades.php?delete={$row['id']}' onclick=\"return confirm('Bạn có chắc chắn muốn xóa?')\">Xóa</a></td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br><a href="dashboard.php">Quay lại Trang chính</a>
</body>
</html>
