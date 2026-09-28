<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$institution_name = $_SESSION['name'];

/* حفظ التقييم */

if(isset($_POST['save_evaluation'])){

    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];

    $attendance_score = $_POST['attendance_score'];
    $skills_score = $_POST['skills_score'];
    $behavior_score = $_POST['behavior_score'];

    $comments = $_POST['comments'];

    $overall_score =
    ($attendance_score +
    $skills_score +
    $behavior_score) / 3;

    $check = mysqli_query($conn,"
    SELECT *
    FROM student_evaluations
    WHERE student_id='$student_id'
    ");

    if(mysqli_num_rows($check)>0){

        echo "<script>
        alert('Evaluation already exists');
        </script>";

    }else{

        mysqli_query($conn,"
        INSERT INTO student_evaluations
        (
            student_id,
            student_name,
            company_name,
            attendance_score,
            skills_score,
            behavior_score,
            overall_score,
            comments
        )
        VALUES
        (
            '$student_id',
            '$student_name',
            '$institution_name',
            '$attendance_score',
            '$skills_score',
            '$behavior_score',
            '$overall_score',
            '$comments'
        )
        ");

        mysqli_query($conn,"
        INSERT INTO notifications
        (user_name,message)
        VALUES
        (
            '$student_name',
            'Your training evaluation has been added'
        )
        ");

        echo "<script>
        alert('Evaluation Saved Successfully');
        </script>";
    }
}

/* الطلاب المقبولين */

$students = mysqli_query($conn,"
SELECT *
FROM training_requests
WHERE company_name='$institution_name'
AND status='Accepted'
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Evaluation</title>

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

.card{
    background:#fff;
    border:1px solid #eee;
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
}

h2{
    color:#0f172a;
}

input,
textarea{
    width:100%;
    padding:10px;
    margin-top:5px;
    margin-bottom:15px;
    border:1px solid #ddd;
    border-radius:8px;
}

.save-btn{
    background:#10b981;
    color:white;
    border:none;
    padding:10px 20px;
    border-radius:8px;
    cursor:pointer;
}

.save-btn:hover{
    background:#059669;
}

label{
    font-weight:bold;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th{
    background:#10b981;
    color:white;
    padding:12px;
}

td{
    padding:12px;
    border-bottom:1px solid #ddd;
}

.done{
    color:green;
    font-weight:bold;
    font-size:16px;
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
<i class="fa fa-star"></i>
Student Evaluation
</h2>

<?php while($row=mysqli_fetch_assoc($students)){ ?>

<div class="card">

<h3>
<?php echo $row['full_name']; ?>
</h3>

<p>
Company:
<?php echo $row['company_name']; ?>
</p>

<?php

$eval_check = mysqli_query($conn,"
SELECT *
FROM student_evaluations
WHERE student_id='".$row['student_id']."'
");

?>

<?php if(mysqli_num_rows($eval_check)==0){ ?>

<form method="POST">

<input
type="hidden"
name="student_id"
value="<?php echo $row['student_id']; ?>">

<input
type="hidden"
name="student_name"
value="<?php echo $row['full_name']; ?>">

<label>Attendance Score</label>

<input
type="number"
name="attendance_score"
min="0"
max="100"
required>

<label>Skills Score</label>

<input
type="number"
name="skills_score"
min="0"
max="100"
required>

<label>Behavior Score</label>

<input
type="number"
name="behavior_score"
min="0"
max="100"
required>

<label>Comments</label>

<textarea
name="comments"
rows="4"
required></textarea>

<button
type="submit"
name="save_evaluation"
class="save-btn">

Save Evaluation

</button>

</form>

<?php } else { ?>

<p class="done">
<i class="fa fa-check-circle"></i>
Evaluation Already Added
</p>

<?php } ?>

</div>

<?php } ?>

<h2 style="margin-top:40px;">
<i class="fa fa-list"></i>
Saved Evaluations
</h2>

<table>

<tr>
    <th>Student</th>
    <th>Attendance</th>
    <th>Skills</th>
    <th>Behavior</th>
    <th>Overall</th>
</tr>

<?php

$evaluations = mysqli_query($conn,"
SELECT *
FROM student_evaluations
WHERE company_name='$institution_name'
ORDER BY id DESC
");

while($e=mysqli_fetch_assoc($evaluations)){

?>

<tr>

<td>
<?php echo $e['student_name']; ?>
</td>

<td>
<?php echo $e['attendance_score']; ?>
</td>

<td>
<?php echo $e['skills_score']; ?>
</td>

<td>
<?php echo $e['behavior_score']; ?>
</td>

<td>
<?php echo $e['overall_score']; ?>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>