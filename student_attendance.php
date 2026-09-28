<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
$student_id = $_SESSION['user_id'];
$result = mysqli_query($conn,"
SELECT *
FROM attendance
WHERE student_id='$student_id'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Attendance</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    font-family:Poppins,sans-serif;
    background:#f1f5f9;
    padding:30px;
}

.container{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.back-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 15px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    border-radius:8px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#10b981;
    color:white;
    padding:12px;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:center;
}

</style>
</head>

<body>

<div class="container">

<a href="student_dashboard.php" class="back-btn">
Back to Dashboard
</a>

<h2>
<i class="fa fa-calendar-check"></i>
My Attendance
</h2>

<table>

<tr>
<th>Date</th>
<th>Check In</th>
<th>Check Out</th>
<th>Company</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td>
<?php echo $row['attend_date']; ?>
</td>

<td>
<?php echo $row['check_in']; ?>
</td>

<td>
<?php echo $row['check_out']; ?>
</td>

<td>
<?php echo $row['company_name']; ?>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>