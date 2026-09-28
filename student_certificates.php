<?php
session_start();
include "db.php";

// حماية الصفحة
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// جلب الشهادات مع التأكد من وجود بيانات
$result = mysqli_query($conn, "
    SELECT *
    FROM certificates
    WHERE student_id = '$student_id'
    ORDER BY id DESC
");

// التحقق من وجود شهادات
$has_certificates = mysqli_num_rows($result) > 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Certificates</title>

<style>
body{
    font-family:Arial;
    background:#f5f7fb;
    margin:0;
    padding:0;
}

.container{
    width:80%;
    margin:30px auto;
}

.card{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,.1);
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:12px;
    border:1px solid #ddd;
}

th{
    background:#0f172a;
    color:white;
}

.back-btn{
    display:inline-block;
    margin-bottom:20px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    padding:10px 18px;
    border-radius:8px;
}

.no-data{
    text-align:center;
    padding:40px;
    color:#666;
    font-size:18px;
}

.view-btn{
    background:#0ea5e9;
    color:white;
    padding:8px 16px;
    border-radius:6px;
    text-decoration:none;
    display:inline-block;
}

.view-btn:hover{
    background:#0284c7;
}
</style>

</head>

<body>

<div class="container">

<a href="student_dashboard.php" class="back-btn">
← Back To Dashboard
</a>

<div class="card">

<h2>My Certificates</h2>

<?php if($has_certificates): ?>
    <table>
    <tr>
        <th>Course</th>
        <th>Provider</th>
        <th>Certificate</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
        <td><?php echo htmlspecialchars($row['provider']); ?></td>
        <td>
            <a href="uploads/<?php echo htmlspecialchars($row['certificate_file']); ?>" 
               target="_blank" 
               class="view-btn">
                View Certificate
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
    </table>
<?php else: ?>
    <div class="no-data">
        <p>No certificates found.</p>
        <p>Your supervisor hasn't added any certificates yet.</p>
    </div>
<?php endif; ?>

</div>

</div>

</body>
</html>