<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];

/* الطلاب المرتبطين بالمشرف */
$students = mysqli_query($conn,"
SELECT users.id, users.username
FROM users
JOIN supervisor_students
ON users.id = supervisor_students.student_id
WHERE supervisor_students.supervisor_id='$supervisor_id'
");

/* اختيار طالب */
$selected = 0;
$tasks = null;

if(isset($_POST['student_id'])){
    $selected = $_POST['student_id'];

    $tasks = mysqli_query($conn,"
        SELECT * 
        FROM training_tasks
        WHERE student_id='$selected'
        ORDER BY id DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Tasks</title>

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

/* ================= SELECT ================= */
select{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
    outline:none;
}

select:focus{
    border-color:#38bdf8;
    box-shadow:0 0 5px rgba(56,189,248,0.3);
}

/* ================= TABLE ================= */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
    overflow:hidden;
    border-radius:12px;
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

/* empty message */
.empty{
    text-align:center;
    color:#777;
    margin-top:15px;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>FTS System</h2>

    <a href="supervisor_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="supervisor_profile.php"><i class="fa fa-user"></i> Profile</a>
    <a href="supervisor_attendance.php"><i class="fa fa-calendar"></i> Attendance</a>
    <a href="supervisor_evaluations.php"><i class="fa fa-star"></i> Evaluation</a>
    <a href="login.php"><i class="fa fa-right-from-bracket"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<div class="title">
    <i class="fa fa-tasks"></i>
    Student Tasks (Supervisor)
</div>

<div class="card">

<!-- SELECT STUDENT -->
<form method="POST">

<select name="student_id" onchange="this.form.submit()">
    <option value="">Select Student</option>

    <?php while($s=mysqli_fetch_assoc($students)){ ?>
        <option value="<?= $s['id'] ?>"
        <?php if($selected==$s['id']) echo "selected"; ?>>
            <?= $s['username'] ?>
        </option>
    <?php } ?>

</select>

</form>

<!-- TASKS TABLE -->
<?php if($tasks && mysqli_num_rows($tasks) > 0){ ?>

<table>

<tr>
    <th>Date</th>
    <th>Task</th>
</tr>

<?php while($t=mysqli_fetch_assoc($tasks)){ ?>

<tr>
    <td><?= $t['task_date'] ?></td>
    <td><?= $t['task_text'] ?></td>
</tr>

<?php } ?>

</table>

<?php } elseif($selected){ ?>

<div class="empty">
    No tasks found for this student
</div>

<?php } ?>

</div>

</div>

</body>
</html>