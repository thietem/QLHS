<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Thêm học sinh
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $class_id = intval($_POST['class_id']);

    $sql = "INSERT INTO students (name, class_id) VALUES ('$name', $class_id)";
    mysqli_query($conn, $sql);
    header('Location: students.php');
    exit();
}

// Xóa học sinh
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM students WHERE id = $id");
    header('Location: students.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Quản lý Học sinh</title>
</head>
<body>
    <h2>Quản lý Học sinh</h2>

    <!-- Form thêm học sinh -->
    <form method="POST">
        <input type="text" name="name" placeholder="Tên học sinh" required>
        <select name="class_id" required>
            <option value="">-- Chọn lớp --</option>
            <?php
            $classes = mysqli_query($conn, "SELECT * FROM classes");
            while ($row = mysqli_fetch_assoc($classes)) {
                echo "<option value='{$row['id']}'>{$row['name']} ({$row['year']})</option>";
            }
            ?>
        </select>
        <input type="submit" name="add" value="Thêm Học sinh">
    </form>

    <br>

    <!-- Form tìm kiếm / lọc -->
    <form method="GET">
        <input type="text" name="search_name" placeholder="Tìm tên học sinh" value="<?= $_GET['search_name'] ?? '' ?>">
        
        <select name="class_id">
            <option value="">-- Lọc theo lớp --</option>
            <?php
            $classes = mysqli_query($conn, "SELECT * FROM classes");
            while ($row = mysqli_fetch_assoc($classes)) {
                $selected = ($_GET['class_id'] ?? '') == $row['id'] ? "selected" : "";
                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
            }
            ?>
        </select>

        <select name="year">
            <option value="">-- Lọc theo năm học --</option>
            <?php
            $years = mysqli_query($conn, "SELECT DISTINCT year FROM classes ORDER BY year DESC");
            while ($row = mysqli_fetch_assoc($years)) {
                $selected = ($_GET['year'] ?? '') == $row['year'] ? "selected" : "";
                echo "<option value='{$row['year']}' $selected>{$row['year']}</option>";
            }
            ?>
        </select>

        <input type="submit" value="Tìm kiếm / Lọc">
        <a href="students.php">Đặt lại</a>
    </form>

    <h3>Danh sách học sinh</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Tên học sinh</th>
            <th>Lớp</th>
            <th>Năm học</th>
            <th>Hành động</th>
        </tr>

        <?php
        // Xử lý tìm kiếm và lọc
        $where = [];
        $params = [];
        $types = "";

        if (!empty($_GET['search_name'])) {
            $where[] = "students.name LIKE ?";
            $params[] = "%" . $_GET['search_name'] . "%";
            $types .= "s";
        }

        if (!empty($_GET['class_id'])) {
            $where[] = "students.class_id = ?";
            $params[] = $_GET['class_id'];
            $types .= "i";
        }

        if (!empty($_GET['year'])) {
            $where[] = "classes.year = ?";
            $params[] = $_GET['year'];
            $types .= "s";
        }

        $sql = "SELECT students.*, classes.name as class_name, classes.year 
                FROM students 
                JOIN classes ON students.class_id = classes.id";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $students = $stmt->get_result();

        while ($row = mysqli_fetch_assoc($students)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['class_name']}</td>";
            echo "<td>{$row['year']}</td>";
            echo "<td>";
            if ($_SESSION['role'] === 'admin') {
                echo "<a href='students.php?delete={$row['id']}' onclick=\"return confirm('Bạn có chắc muốn xóa học sinh này?')\">Xóa</a>";
            } else {
                echo "-";
            }
            echo "</td></tr>";
        }
        ?>
    </table>

    <br><a href="dashboard.php">← Quay lại Trang chính</a>
</body>
</html>
