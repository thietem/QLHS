<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$id = intval($_GET['id']);
$history_query = mysqli_query($conn, "
    SELECT sh.*, u.username AS updater_name 
    FROM score_history sh 
    JOIN users u ON sh.updated_by = u.id 
    WHERE sh.score_id = $id
");


?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Lịch sử sửa điểm</title>
</head>
<body>
    <h2>Lịch sử sửa điểm</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Điểm Toán cũ</th>
            <th>Điểm Toán mới</th>
            <th>Điểm Lý cũ</th>
            <th>Điểm Lý mới</th>
            <th>Điểm Hóa cũ</th>
            <th>Điểm Hóa mới</th>
            <th>Người sửa</th>
            <th>Thời gian sửa</th>
        </tr>
        <?php 
         if (mysqli_num_rows($history_query) > 0) {
        while ($row = mysqli_fetch_assoc($history_query)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['old_math']; ?></td>
                <td><?php echo $row['new_math']; ?></td>
                <td><?php echo $row['old_physics']; ?></td>
                <td><?php echo $row['new_physics']; ?></td>
                <td><?php echo $row['old_chemistry']; ?></td>
                <td><?php echo $row['new_chemistry']; ?></td>
                <td><?php echo htmlspecialchars($row['updater_name']); ?></td>
                <td><?php echo $row['updated_at']; ?></td>
            </tr>
        <?php endwhile; } else {
            echo "<tr><td colspan='9'>Không có lịch sử sửa điểm.</td></tr>";
        }
        ?>
    </table>

    <br><a href="scores.php">Quay lại danh sách điểm</a>
</body>
</html>
