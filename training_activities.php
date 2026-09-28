<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

/* ===============================
رفع التوقيع
=============================== */
if(isset($_POST['upload_signature'])){

    $file = $_FILES['signature']['name'];
    $tmp = $_FILES['signature']['tmp_name'];

    if($file){
        move_uploaded_file($tmp,"uploads/".$file);

        mysqli_query($conn,"
        INSERT INTO training_activities(student_id, signature_file)
        VALUES('$student_id','$file')
        ");
    }
}

/* ===============================
رفع الصور
=============================== */
if(isset($_POST['upload_image'])){

    $file = $_FILES['activity_image']['name'];
    $tmp = $_FILES['activity_image']['tmp_name'];

    if($file){
        move_uploaded_file($tmp,"uploads/".$file);

        mysqli_query($conn,"
        INSERT INTO training_activities(student_id, activity_image)
        VALUES('$student_id','$file')
        ");
    }
}

/* ===============================
المهام والمهارات
=============================== */
if(isset($_POST['submit_text'])){

    $tasks = $_POST['tasks'];
    $skills = $_POST['skills'];

    mysqli_query($conn,"
    INSERT INTO training_activities(student_id, tasks_text, skills_text)
    VALUES('$student_id','$tasks','$skills')
    ");
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Training Activities</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* ===============================
GENERAL (نفس الداشبورد)
=============================== */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    display:flex;
    background:#f1f5f9;
}

/* ===============================
SIDEBAR
=============================== */

.sidebar{
    width:260px;
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
    padding:14px;
    margin:10px 0;
    text-decoration:none;
    border-radius:10px;
    transition:0.3s;
}

.sidebar a:hover{
    background:#1e293b;
}

/* ===============================
MAIN
=============================== */

.main{
    margin-left:260px;
    width:100%;
    padding:30px;
}

/* ===============================
CARDS (نفس الداشبورد)
=============================== */

.card{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    margin-bottom:20px;
}

h1{
    color:#0f172a;
    margin-bottom:20px;
}

h3{
    color:#2563eb;
    margin-bottom:15px;
}

/* ===============================
INPUTS
=============================== */

input, textarea{
    width:100%;
    padding:14px;
    margin:10px 0;
    border:1px solid #ddd;
    border-radius:10px;
    outline:none;
}

/* ===============================
BUTTON
=============================== */

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:10px;
    background:linear-gradient(to right,#38bdf8,#2563eb);
    color:white;
    cursor:pointer;
}

/* ===============================
DISPLAY
=============================== */

.item{
    background:#f8fafc;
    padding:15px;
    border-left:5px solid #38bdf8;
    margin-top:10px;
    border-radius:10px;
}

img{
    width:120px;
    border-radius:10px;
    margin-top:10px;
}

</style>

</head>

<body>

<!-- ===============================
SIDEBAR
=============================== -->

<div class="sidebar">

<h2>FTS System</h2>

<a href="student_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>

<a href="profile.php"><i class="fa fa-user"></i> Profile</a>

<a href="training_registration.php"><i class="fa fa-building"></i> Training</a>

<a href="daily_reports.php"><i class="fa fa-file"></i> Reports</a>

<a href="training_activities.php"><i class="fa fa-image"></i> Activities</a>

<a href="login.php"><i class="fa fa-right-from-bracket"></i> Logout</a>

</div>

<!-- ===============================
MAIN
=============================== -->

<div class="main">

<h1>Training Activities</h1>

<!-- ===================== توقيع ===================== -->
<div class="card">

<h3>Signature Sheet</h3>

<form method="POST" enctype="multipart/form-data">

<input type="file" name="signature" required>

<button name="upload_signature">Upload</button>

</form>

</div>

<!-- ===================== صور ===================== -->
<div class="card">

<h3>Activity Images</h3>

<form method="POST" enctype="multipart/form-data">

<input type="file" name="activity_image" required>

<button name="upload_image">Upload</button>

</form>

</div>

<!-- ===================== مهام ===================== -->
<div class="card">

<h3>Tasks & Skills</h3>

<form method="POST">

<textarea name="tasks" placeholder="Completed Tasks"></textarea>

<textarea name="skills" placeholder="Learned Skills"></textarea>

<button name="submit_text">Submit</button>

</form>

</div>

<!-- ===================== عرض ===================== -->
<div class="card">

<h3>My Records</h3>

<?php

$q = mysqli_query($conn,"
SELECT * FROM training_activities
WHERE student_id='$student_id'
ORDER BY id DESC
");

while($r = mysqli_fetch_assoc($q)){

?>

<div class="item">

<p><b>Tasks:</b> <?php echo $r['tasks_text']; ?></p>

<p><b>Skills:</b> <?php echo $r['skills_text']; ?></p>

<?php if($r['signature_file']){ ?>
<p><b>Signature:</b><br>
<img src="uploads/<?php echo $r['signature_file']; ?>">
</p>
<?php } ?>

<?php if($r['activity_image']){ ?>
<p><b>Image:</b><br>
<img src="uploads/<?php echo $r['activity_image']; ?>">
</p>
<?php } ?>

</div>

<?php } ?>

</div>

</div>

</body>
</html>