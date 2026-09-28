<?php

// بدء الجلسة
session_start();

// ربط قاعدة البيانات
include "db.php";

// رقم الطالب الحالي من الجلسة
$student_id = $_SESSION['user_id'];

// رسالة نجاح
$message = "";

// عند الضغط على زر الإرسال
if(isset($_POST['submit_problem'])){

    // عنوان المشكلة
    $title = $_POST['title'];

    // وصف المشكلة
    $description = $_POST['description'];

    // تاريخ المشكلة
    $problem_date = $_POST['problem_date'];

    // حفظ المشكلة بقاعدة البيانات
    mysqli_query($conn,"
    INSERT INTO problems(
    student_id,
    title,
    description,
    problem_date
    )
    VALUES(
    '$student_id',
    '$title',
    '$description',
    '$problem_date'
    )
    ");

    // رسالة نجاح
    $message = "Problem submitted successfully";
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Problems & Difficulties</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    margin:0;
    font-family:Poppins,sans-serif;
    background:#eef2f7;
}

.container{
    width:70%;
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

label{
    display:block;
    margin-top:15px;
    margin-bottom:8px;
    font-weight:bold;
}

input,
textarea{
    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:8px;
}

textarea{
    height:150px;
}

.btn{
    margin-top:20px;
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
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
}

.back-btn{
    display:inline-block;
    margin-bottom:20px;
    text-decoration:none;
    color:white;
    background:#0b1f4d;
    padding:10px 20px;
    border-radius:8px;
}

</style>

</head>

<body>

<div class="container">

<a href="student_dashboard.php" class="back-btn">
<i class="fa fa-arrow-left"></i>
 Back To Dashboard
</a>

<div class="card">

<h1>Problems & Difficulties</h1>

<?php
if(!empty($message)){
echo "<div class='success'>$message</div>";
}
?>

<form method="POST">

<label>Problem Title</label>

<input
type="text"
name="title"
required
>

<label>Problem Description</label>

<textarea
name="description"
required
></textarea>

<label>Problem Date</label>

<input
type="date"
name="problem_date"
required
>

<button
type="submit"
name="submit_problem"
class="btn"
>
Submit Problem
</button>

</form>

</div>

</div>

</body>
</html>