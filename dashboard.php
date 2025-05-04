<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
    <title>Trang chính</title>
</head>
<body>
    <h2>Xin chào <?php echo $_SESSION['username']; ?>!</h2>
    <div class="list-ql">
        <p><a href="grades.php">Quản lý Khối lớp</a></p>
        <p><a href="classes.php">Quản lý Lớp</a></p>
        <p><a href="students.php">Quản lý Học sinh</a></p>
        <p><a href="scores.php">Nhập điểm</a></p>
        <p><a href="logout.php">Đăng xuất</a></p>
    </div>
</body>
</html>
