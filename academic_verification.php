<?php
session_start();
include "db.php";
/* Verify Student */

if(isset($_POST['verify'])){

    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $gpa = $_POST['gpa'];
    $hours = $_POST['completed_hours'];

    if($gpa >= 2.0 && $hours >= 90){

        $status = "Eligible";

    }else{

        $status = "Not Eligible";

    }

    $check = mysqli_query($conn,"
    SELECT *
    FROM academic_verification
    WHERE student_id='$student_id'
    ");

    if(mysqli_num_rows($check)==0){

    $sql = "
    INSERT INTO academic_verification
    (
        student_id,
        student_name,
        gpa,
        completed_hours,
        academic_status,
        verification_date
    )
    VALUES
    (
        '$student_id',
        '$student_name',
        '$gpa',
        '$hours',
        '$status',
        NOW()
    )";

   if(!mysqli_query($conn,$sql)){
    die(mysqli_error($conn));
}

}else{

    $sql = "
    UPDATE academic_verification
    SET
        gpa='$gpa',
        completed_hours='$hours',
        academic_status='$status',
        verification_date=NOW()
    WHERE student_id='$student_id'";

 if(!mysqli_query($conn,$sql)){
    die(mysqli_error($conn));
}
}
header("Location: academic_verification.php");
exit();
}   // هذا يغلق if(isset($_POST['verify']))

$result = mysqli_query($conn,"
SELECT *
FROM training_requests
WHERE status='Accepted'
AND trainee_status='Accepted'
AND student_id NOT IN (
    SELECT student_id
    FROM academic_verification
)
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Academic Verification</title>

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
background:#7f1d1d;
color:white;
text-decoration:none;
border-radius:10px;
}

.back-btn:hover{
background:#991b1b;
}

h2{
color:#7f1d1d;
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#dc2626;
color:white;
padding:15px;
}

td{
padding:15px;
border-bottom:1px solid #eee;
}

tr:hover{
background:#fef2f2;
}

.status{
background:#16a34a;
color:white;
padding:6px 12px;
border-radius:20px;
font-size:13px;
}
input[type=number]{

width:90px;
padding:8px;
border:1px solid #ddd;
border-radius:8px;

}

button{

background:#16a34a;
color:white;
border:none;
padding:10px 15px;
border-radius:8px;
cursor:pointer;

}

button:hover{

background:#15803d;

}

</style>

</head>

<body>

<div class="container">

<a href="admissions_dashboard.php" class="back-btn">
<i class="fa fa-arrow-left"></i>
Back to Dashboard
</a>

<h2>
<i class="fa-solid fa-user-check"></i>
Academic Verification
</h2>

<table>

<tr>
<th>Student</th>
<th>Completed Hours</th>
<th>Company</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<form method="POST">

<td>

<?php echo $row['full_name']; ?>

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

<input
type="number"
name="completed_hours"
value="<?php echo $row['completed_hours']; ?>"
required>

</td>
<td>

<?php echo $row['company_name']; ?>

</td>



<td>

<input
type="number"
step="0.01"
min="0"
max="4"
name="gpa"
required>

</td>

<td>

<button
type="submit"
name="verify">
Verify
</button>

</td>

</form>

</tr>

<?php } ?>

</table>
<hr style="margin:40px 0;">

<h2>
<i class="fa-solid fa-check-circle"></i>
Verified Students
</h2>

<table>

<tr>
    <th>Student</th>
    <th>GPA</th>
    <th>Completed Hours</th>
    <th>Status</th>
</tr>

<?php

$verified = mysqli_query($conn,"
SELECT *
FROM academic_verification
ORDER BY verification_date DESC
");

while($v = mysqli_fetch_assoc($verified)){
?>

<tr>

<td><?php echo $v['student_name']; ?></td>

<td><?php echo $v['gpa']; ?></td>

<td><?php echo $v['completed_hours']; ?></td>

<td><?php echo $v['academic_status']; ?></td>

</tr>

<?php } ?>

</table>
</div>

</body>
</html>