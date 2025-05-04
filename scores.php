<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Thêm điểm
if (isset($_POST['add'])) {
    $student_id = intval($_POST['student_id']);
    $math = floatval($_POST['math']);
    $physics = floatval($_POST['physics']);
    $chemistry = floatval($_POST['chemistry']);
    $average = ($math + $physics + $chemistry) / 3;

    $sql = "INSERT INTO scores (student_id, math, physics, chemistry, average) 
            VALUES ($student_id, $math, $physics, $chemistry, $average)";
    mysqli_query($conn, $sql);
    header('Location: scores.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
    <title>Nhập điểm</title>
    <!-- scrip tính đtb  -->
    <script>
    function calcAverage() {
        var math = parseFloat(document.getElementById('math').value) || 0;
        var physics = parseFloat(document.getElementById('physics').value) || 0;
        var chemistry = parseFloat(document.getElementById('chemistry').value) || 0;
        var avg = (math + physics + chemistry) / 3;
        document.getElementById('average').value = avg.toFixed(2);
    }
    </script>
    <!-- script bắt lỗi  -->
    <script>
        document.getElementById("scoreForm").addEventListener("submit", 
        function(event){
            const math = parseFloat(document.getElementById("math").value);
            const physics = parseFloat(document.getElementById("physics").value);
            const chemistry = parseFloat(document.getElementById("chemistry").value);
            function isValidScore(score){
                return !isNaN(score)&&score>=0&&score<=10;
            }
            if(!isValidScore(math)){
                alert ("Điểm toán phải từ 0 đến 10");
                event.preventDefault();
                return;
            }
            if(!isValidScore(physics)){
                alert ("Điểm vật lý phải từ 0 đến 10");
                event.preventDefault();
                return;
            }
            if(!isValidScore(chemistry)){
                alert ("Điểm hóa học phải từ 0 đến 10");
                event.preventDefault();
                return;
            }
        }
        )
    </script>
</head>
<body>
    <h2>Nhập điểm học sinh</h2>

    <form method="POST" id="scoreForm">
        <select name="student_id" required>
            <option value="">-- Chọn học sinh --</option>
            <?php
            $students = mysqli_query($conn, "SELECT * FROM students");
            while ($row = mysqli_fetch_assoc($students)) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select><br><br>

        <label>Toán:</label><br>
        <input type="number" step="0.01" id="math" name="math" onchange="calcAverage()" required><br><br>

        <label>Lý:</label><br>
        <input type="number" step="0.01" id="physics" name="physics" onchange="calcAverage()" required><br><br>

        <label>Hóa:</label><br>
        <input type="number" step="0.01" id="chemistry" name="chemistry" onchange="calcAverage()" required><br><br>

        <label>Trung bình môn:</label><br>
        <input type="text" id="average" readonly><br><br>

        <input type="submit" name="add" value="Lưu điểm">
    </form>
<!-- hien thi ds diem cua hs -->

<?php
include 'db.php';
$scores = mysqli_query($conn, "SELECT scores.*, students.name FROM scores JOIN students ON scores.student_id = students.id");

echo "<h2>Danh sách điểm</h2>";
echo "<table border='1' cellpadding='5'><tr><th>Học sinh</th><th>Toán</th><th>Lý</th><th>Hóa</th><th>TBM</th><th>Hành động</th></tr>";
while ($row = mysqli_fetch_assoc($scores)) {
    echo "<tr>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['math']}</td>";
    echo "<td>{$row['physics']}</td>";
    echo "<td>{$row['chemistry']}</td>";
    echo "<td>{$row['average']}</td>";
    echo "<td><a href='edit_scores.php?id={$row['id']}'>Sửa</a> | <a href='score_history.php?id={$row['id']}'>Xem lịch sử</a></td>";
    echo "</tr>";
}
echo "</table>";
?>
    <br>
        <a  href="dashboard.php">Quay lại Trang chính</a>
</body>
</html>
