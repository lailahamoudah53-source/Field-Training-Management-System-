<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}


$institution = $_SESSION['name'];

$feedbacks = mysqli_query($conn,"
SELECT * 
FROM training_feedback
WHERE company_name='$institution'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Feedback</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* ================= GENERAL ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:#f4f7fc;
    display:flex;
}

/* ================= SIDEBAR ================= */
.sidebar{
    width:250px;
    height:100vh;
    background:#0f172a;
    padding:25px;
    position:fixed;
}

.sidebar h2{
    color:#38bdf8;
    text-align:center;
    margin-bottom:30px;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:12px;
    margin-bottom:10px;
    border-radius:8px;
    transition:0.3s;
}

.sidebar a:hover{
    background:#1e293b;
}

.sidebar a.active{
    background:#2563eb;
}

/* ================= MAIN ================= */
.main{
    margin-left:250px;
    padding:40px;
    width:100%;
}

/* ================= TITLE ================= */
.title{
    font-size:26px;
    font-weight:bold;
    margin-bottom:20px;
    color:#0f172a;
    display:flex;
    align-items:center;
    gap:10px;
}

/* ================= CARD ================= */
.card{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* ================= TABLE ================= */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
    border-radius:12px;
    overflow:hidden;
}

th{
    background:#38bdf8;
    color:white;
    padding:15px;
    text-align:left;
}

td{
    padding:15px;
    border-bottom:1px solid #eee;
}

tr:hover{
    background:#f8fafc;
}

/* empty */
.empty{
    text-align:center;
    color:#777;
    padding:20px;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>FTS System</h2>

    <a href="institution_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="institution_students.php"><i class="fa fa-users"></i> Students</a>
    <a href="institution_attendance.php"><i class="fa fa-calendar"></i> Attendance</a>
    <a href="institution_feedback.php" class="active"><i class="fa fa-comment"></i> Feedback</a>
    <a href="login.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<div class="title">
    <i class="fa-solid fa-comments"></i>
    Student Feedback
</div>

<div class="card">

<table>

<tr>
    <th>Student</th>
    <th>Feedback</th>
    <th>Date</th>
</tr>

<?php if(mysqli_num_rows($feedbacks) > 0){ ?>

<?php while($row=mysqli_fetch_assoc($feedbacks)){ ?>

<tr>
    <td><?= $row['student_name'] ?></td>
    <td><?= $row['feedback_text'] ?></td>
    <td><?= $row['created_at'] ?></td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="3" class="empty">
        No feedback available
    </td>
</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>