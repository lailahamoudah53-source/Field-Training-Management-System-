<?php

// بدء الجلسة
session_start();

// ربط قاعدة البيانات
include "db.php";

// رقم الطالب الحالي
$student_id = $_SESSION['user_id'];

$message = "";

// التحقق إذا كان الطالب قدم التعهد سابقاً
$check = mysqli_query($conn,"
SELECT *
FROM pledges
WHERE student_id='$student_id'
");

$pledge = mysqli_fetch_assoc($check);

// عند الضغط على زر الموافقة
if(isset($_POST['submit_pledge'])){

    mysqli_query($conn,"
    INSERT INTO pledges(
    student_id,
    agree
    )
    VALUES(
    '$student_id',
    'Yes'
    )
    ");

    $message = "Pledge submitted successfully";

    header("Refresh:1");
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Training Pledge</title>

<style>

body{
    background:#eef2f7;
    font-family:Poppins,sans-serif;
    margin:0;
}

.container{
    width:75%;
    margin:40px auto;
}

.card{
    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 2px 10px rgba(0,0,0,.1);
}

h1{
    color:#0b1f4d;
}

.pledge-box{
    background:#f8f9fa;
    padding:20px;
    border-radius:10px;
    line-height:2;
    margin:20px 0;
}

.btn{
    background:#25a9e0;
    color:white;
    border:none;
    padding:12px 25px;
    border-radius:8px;
    cursor:pointer;
}

.success{
    background:#d4edda;
    color:#155724;
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
}

.back-btn{
    text-decoration:none;
    background:#0b1f4d;
    color:white;
    padding:10px 20px;
    border-radius:8px;
}

</style>

</head>

<body>

<div class="container">

<a href="student_dashboard.php" class="back-btn">
Back To Dashboard
</a>

<div class="card">

<h1>Electronic Training Pledge</h1>

<?php
if(!empty($message)){
echo "<div class='success'>$message</div>";
}
?>

<?php if(!$pledge){ ?>

<form method="POST">

<div class="pledge-box">

I pledge to comply with all field training regulations,
submit reports honestly,
respect the training institution,
and complete all required tasks during the training period.

</div>

<label>

<input
type="checkbox"
required
>

I Agree To The Training Pledge

</label>

<br><br>

<button
type="submit"
name="submit_pledge"
class="btn"
>

Submit Pledge

</button>

</form>

<?php } else { ?>

<div class="success">

You have already submitted the training pledge.

</div>

<?php } ?>

</div>

</div>

</body>

</html>