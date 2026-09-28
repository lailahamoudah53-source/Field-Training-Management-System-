<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$institution_name = $_SESSION['name'];

/* عدد الطلاب */
$students = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM training_requests
WHERE company_name='$institution_name'
AND status='Approved'
"));

/* الحضور */
$attendance = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM attendance
WHERE company_name='$institution_name'
"));

/* طلبات */
$requests = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM training_requests
WHERE company_name='$institution_name'
"));
 
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Institution Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* ===== BASE ===== */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    display:flex;
    background:#f8fafc;
}

/* ===== SIDEBAR (RIGHT) ===== */
.sidebar{
    width:260px;
    height:100vh;
    background:linear-gradient(180deg,#0f172a,#064e3b);
    color:white;
    position:fixed;
    left:0;   
    top:0;
    padding:25px;
}
.sidebar h2{
    text-align:center;
    margin-bottom:30px;
    color:#34d399;
}

.sidebar ul{
    list-style:none;
}

.sidebar ul li{
    margin:15px 0;
}

.sidebar ul li a{
    color:white;
    text-decoration:none;
    display:flex;
    gap:10px;
    padding:12px;
    border-radius:10px;
    transition:0.3s;
}

.sidebar ul li a:hover{
    background:rgba(255,255,255,0.1);
    transform:translateX(-5px);
}

/* ===== MAIN ===== */
.main{
    margin-left:260px;  
    width:100%;
    padding:30px;
}

/* HEADER */
.header{
    background:white;
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
}

.header h1{
    color:#0f172a;
}

/* CARDS */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

.card{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card i{
    font-size:30px;
    color:#10b981;
    margin-bottom:10px;
}

.card h3{
    font-size:28px;
    color:#0f172a;
}

.card p{
    color:#64748b;
}

/* BUTTONS */
.btn{
    display:inline-block;
    margin-top:10px;
    padding:10px 15px;
    background:#10b981;
    color:white;
    border-radius:8px;
    text-decoration:none;
}

.btn:hover{
    background:#059669;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Institution Panel</h2>
    <ul>
        <li><a href="#"><i class="fa fa-home"></i> Dashboard</a></li>
  
<li>
    <a href="institution_students.php">
        <i class="fa fa-users"></i>
        Students
    </a>
</li>
   <li><a href="institution_attendance.php"><i class="fa fa-calendar"></i> Attendance</a></li>
   <li>
    <a href="institution_tasks.php">
        <i class="fa fa-tasks"></i>
        Tasks Completed
    </a>
</li>
<li>
<a href="institution_evaluation.php">
<i class="fa fa-star"></i>
Student Evaluation
</a>
</li>

<li>
<a href="institution_employability.php">
<i class="fa fa-star"></i>
employability
</a>
</li>
<li>
    <a href="institution_official_letters.php" class="active">
        <i class="fa-solid fa-envelope-open-text"></i>
        Official Letters
    </a>
</li>
     
<li>
<a href="institution_assign_supervisor.php">
<i class="fa fa-user-tie"></i>
Assign Supervisor
</a>
</li>
<li>
<a href="notifications.php">
<i class="fa fa-bell"></i> Notifications
</a>
</li>


       
        <li><a href="login.php"><i class="fa fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h1>Institution Dashboard</h1>
        <span>Welcome <?= $_SESSION['name']; ?></span>
    </div>

    <!-- CARDS -->
    <div class="grid">

        

        <div class="card">
            <i class="fa fa-calendar-check"></i>
            <h3><?= $attendance['total']; ?></h3>
            <p>Attendance Records</p>
        </div>

        <div class="card">
            <i class="fa fa-file"></i>
            <h3><?= $requests['total']; ?></h3>
            <p>Total Requests</p>
        </div>

        <div class="card">
            <i class="fa fa-building"></i>
            <h3>Institution</h3>
            <p>Manage training operations</p>
        </div>

    </div>

</div>

</body>
</html>