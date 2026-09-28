<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admissions Dashboard</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins,sans-serif;
}

body{
    display:flex;
    background:#f1f5f9;
}

/* ================= Sidebar ================= */

.sidebar{
    width:250px;
    height:100vh;
    background:#7f1d1d;
    padding:25px;
    position:fixed;
}

.sidebar h2{
    color:white;
    text-align:center;
    margin-bottom:35px;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:12px;
    margin-bottom:10px;
    border-radius:10px;
    transition:.3s;
}

.sidebar a:hover{
    background:#991b1b;
}

/* ================= Main ================= */

.main{
    margin-left:250px;
    padding:40px;
    width:100%;
}

.card{
    background:white;
    padding:35px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
    border-top:5px solid #dc2626;
}

.card h2{
    color:#7f1d1d;
    margin-bottom:15px;
}

.card p{
    color:#475569;
    font-size:17px;
}

.welcome-icon{
    color:#dc2626;
    margin-right:10px;
}

</style>

</head>

<body>

<div class="sidebar">

<h2>FTS System</h2>

<a href="admissions_dashboard.php">
<i class="fa-solid fa-house"></i>
Dashboard
</a>

<a href="admission_requests.php">
<i class="fa-solid fa-file-circle-check"></i>
Training Registration Requests
</a>

<a href="academic_verification.php">
<i class="fa-solid fa-user-check"></i>
Academic Verification
</a>

<a href="course_registration.php">
<i class="fa-solid fa-book"></i>
Course Registration
</a>

<a href="academic_records.php">
<i class="fa-solid fa-database"></i>
Academic Records
</a>

<a href="enable_grades.php">
<i class="fa-solid fa-star"></i>
Enable Grades
</a>

<a href="login.php">
<i class="fa-solid fa-right-from-bracket"></i>
Logout
</a>

</div>

<div class="main">

<div class="card">

<h2>
<i class="fa-solid fa-building-columns welcome-icon"></i>
Welcome
<?php echo $_SESSION['name']; ?>
</h2>

<p>
Admissions and Registration Department Dashboard
</p>

</div>

</div>

</body>
</html>