<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$tasks = mysqli_query($conn,"
SELECT * 
FROM training_tasks
WHERE student_id='$user_id'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Tasks</title>

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

/* ================= SIDEBAR (اختياري إذا عندك) ================= */
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
}

.sidebar a:hover{
    background:#1e293b;
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
    margin-top:15px;
}
.back-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    margin-bottom:25px;
    padding:12px 22px;
    background:linear-gradient(135deg,#0f172a,#1e293b);
    color:#fff;
    text-decoration:none;
    border-radius:12px;
    font-size:15px;
    font-weight:600;
    transition:.3s;
    box-shadow:0 5px 15px rgba(15,23,42,.25);
}

.back-btn:hover{
    background:linear-gradient(135deg,#2563eb,#38bdf8);
    transform:translateY(-2px);
    box-shadow:0 8px 18px rgba(37,99,235,.35);
}

.back-btn i{
    font-size:15px;
}
</style>
</head>

<body>

<div class="main">
<a href="student_dashboard.php" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
    Back to Dashboard
</a>
<div class="title">
    <i class="fa-solid fa-list-check"></i>
    My Daily Tasks
</div>

<div class="card">

<table>

<tr>
    <th>Date</th>
    <th>Task</th>
    <th>Company</th>
</tr>

<?php if(mysqli_num_rows($tasks) > 0){ ?>

<?php while($row=mysqli_fetch_assoc($tasks)){ ?>

<tr>
    <td><?= $row['task_date'] ?></td>
    <td><?= $row['task_text'] ?></td>
    <td><?= $row['company_name'] ?></td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
    <td colspan="3" class="empty">
        No tasks found
    </td>
</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>