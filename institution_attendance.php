<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$institution_name = $_SESSION['name'];

$students = mysqli_query($conn,"
SELECT *
FROM training_requests
WHERE company_name='$institution_name'
AND status='Accepted'
AND trainee_status='Accepted'
");

if(isset($_POST['save_attendance'])){

    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    $today = date('Y-m-d');

    $check = mysqli_query($conn,"
    SELECT *
    FROM attendance
    WHERE student_id='$student_id'
    AND attend_date='$today'
    ");

    if(mysqli_num_rows($check) > 0){

        echo "<script>
        alert('Attendance already saved today');
        </script>";

    }else{

       mysqli_query($conn,"
INSERT INTO attendance
(
student_id,
student_name,
company_name,
attend_date,
check_in,
check_out,
status
)
VALUES
(
'$student_id',
'$student_name',
'$institution_name',
'$today',
'$check_in',
'$check_out',
'Pending'
)
");
        echo "<script>alert('Attendance Saved Successfully');</script>";
header("Location: institution_attendance.php");
exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Institution Attendance</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    margin:0;
    padding:30px;
    background:#f1f5f9;
    font-family:Poppins,sans-serif;
}

.container{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.back-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:12px 18px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    border-radius:10px;
}

.back-btn:hover{
    background:#1e293b;
}

h2{
    color:#0f172a;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#10b981;
    color:white;
    padding:15px;
}

td{
    padding:15px;
    border-bottom:1px solid #eee;
}

input[type=time]{
    padding:8px;
    border:1px solid #ddd;
    border-radius:8px;
}

.save-btn{
    background:#10b981;
    color:white;
    border:none;
    padding:10px 15px;
    border-radius:8px;
    cursor:pointer;
}

.save-btn:hover{
    background:#059669;
}

</style>
</head>

<body>

<div class="container">

<a href="institution_dashboard.php" class="back-btn">
<i class="fa fa-arrow-left"></i>
Back to Dashboard
</a>

<h2>
<i class="fa-solid fa-calendar-check"></i>
Student Attendance
</h2>

<table>

<tr>
    <th>Student Name</th>
    <th>Company</th>
    <th>Check In</th>
    <th>Check Out</th>
    <th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($students)){ ?>

<tr>

<form method="POST">

<td>
<?php echo htmlspecialchars($row['full_name']); ?>

<input
type="hidden"
name="student_id"
value="<?php echo $row['student_id']; ?>">

<input
type="hidden"
name="student_name"
value="<?php echo $row['full_name']; ?>">
</td>

<td>
<?php echo htmlspecialchars($row['company_name']); ?>
</td>

<td>
<input
type="time"
name="check_in"
required>
</td>

<td>
<input
type="time"
name="check_out"
required>
</td>

<td>
<button
type="submit"
name="save_attendance"
class="save-btn">

Save

</button>
</td>

</form>

</tr>

<?php } ?>

</table>
<h2 style="margin-top:40px;">
<i class="fa-solid fa-clock"></i>
Saved Attendance
</h2>

<table>

<tr>
    <th>Student</th>
    <th>Date</th>
    <th>Check In</th>
    <th>Check Out</th>
</tr>

<?php

$attendance = mysqli_query($conn,"
SELECT *
FROM attendance
WHERE company_name='$institution_name'
ORDER BY id DESC
");

while($att = mysqli_fetch_assoc($attendance)){

?>

<tr>

<td>
<?php echo htmlspecialchars($att['student_name']); ?>
</td>

<td>
<?php echo $att['attend_date']; ?>
</td>

<td>
<?php echo $att['check_in']; ?>
</td>

<td>
<?php echo $att['check_out']; ?>
</td>

</tr>

<?php } ?>

</table>
</div>

</body>
</html>