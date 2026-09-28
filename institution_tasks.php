<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$institution_name = $_SESSION['name'];

/*=============================
جلب طلاب المؤسسة
==============================*/

$students = mysqli_query($conn,"
SELECT student_id, full_name
FROM training_requests
WHERE company_name='$institution_name'
AND trainee_status='Accepted'
ORDER BY full_name
");

/*=============================
إضافة مهمة
==============================*/

if(isset($_POST['save_task'])){

    $student_id=(int)$_POST['student_id'];

    $task=mysqli_real_escape_string(
        $conn,
        $_POST['task']
    );

    $date=$_POST['task_date'];

    mysqli_query($conn,"
    INSERT INTO training_tasks
    (
        student_id,
        company_name,
        task_date,
        task_text
    )
    VALUES
    (
        '$student_id',
        '$institution_name',
        '$date',
        '$task'
    )
    ");

    echo "<script>
    alert('Task Saved Successfully');
    location='institution_tasks.php';
    </script>";

}

/*=============================
تعديل مهمة
==============================*/

if(isset($_POST['update_task'])){

    $id=(int)$_POST['id'];

    $task=mysqli_real_escape_string(
        $conn,
        $_POST['task']
    );

    $date=$_POST['task_date'];

    mysqli_query($conn,"
    UPDATE training_tasks
    SET
    task_text='$task',
    task_date='$date'
    WHERE id='$id'
    ");

}

/*=============================
حذف مهمة
==============================*/

if(isset($_GET['delete'])){

    $id=(int)$_GET['delete'];

    mysqli_query($conn,"
    DELETE FROM training_tasks
    WHERE id='$id'
    ");

}

/*=============================
الطالب المختار
==============================*/

$selected=0;

if(isset($_GET['student'])){

    $selected=(int)$_GET['student'];

}

/*=============================
جلب المهام
==============================*/

$tasks=[];

if($selected!=0){

$tasks=mysqli_query($conn,"
SELECT *
FROM training_tasks
WHERE student_id='$selected'
ORDER BY task_date DESC
");

}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Tasks Completed</title>

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
background:#f1f5f9;
padding:30px;
}

.container{
max-width:1100px;
margin:auto;
background:white;
padding:30px;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.back-btn{
display:inline-block;
background:#0f172a;
color:white;
padding:10px 18px;
border-radius:8px;
text-decoration:none;
margin-bottom:20px;
}

.back-btn:hover{
background:#1e293b;
}

h2{
margin-bottom:25px;
color:#0f172a;
}

label{
display:block;
margin-top:15px;
margin-bottom:8px;
font-weight:bold;
}

select,
input[type=date],
textarea{
width:100%;
padding:12px;
border:1px solid #ddd;
border-radius:10px;
font-size:15px;
}

textarea{
height:120px;
resize:none;
}

.save-btn{

background:#10b981;
color:white;
border:none;
padding:12px 25px;
border-radius:8px;
cursor:pointer;
margin-top:15px;

}

.save-btn:hover{

background:#059669;

}

table{

width:100%;
margin-top:20px;
border-collapse:collapse;

}

th{

background:#10b981;
color:white;
padding:15px;

}

td{

padding:15px;
border-bottom:1px solid #eee;
vertical-align:top;

}

.edit-btn{

background:#f59e0b;
color:white;
border:none;
padding:8px 14px;
border-radius:8px;
cursor:pointer;

}

.delete-btn{

background:#ef4444;
color:white;
padding:8px 14px;
text-decoration:none;
border-radius:8px;
margin-left:8px;

}

.edit-btn:hover{

opacity:.9;

}

.delete-btn:hover{

opacity:.9;

}

hr{

margin:25px 0;

}

</style>

</head>

<body>

<div class="container">

<a href="institution_dashboard.php" class="back-btn">

<i class="fa fa-arrow-left"></i>

Back

</a>

<h2>

<i class="fa fa-list-check"></i>

Tasks Completed

</h2>

<form method="GET">

<label>Select Student</label>

<select name="student" onchange="this.form.submit()" required>

<option value="">Choose Student</option>

<?php

mysqli_data_seek($students,0);

while($s=mysqli_fetch_assoc($students)){

?>

<option

value="<?php echo $s['student_id']; ?>"

<?php

if($selected==$s['student_id'])

echo "selected";

?>

>

<?php echo htmlspecialchars($s['full_name']); ?>

</option>

<?php } ?>

</select>

</form>

<?php if($selected!=0){ ?>

<hr>

<h3>Add New Task</h3>

<form method="POST">

<input
type="hidden"
name="student_id"
value="<?php echo $selected; ?>">

<label>Date</label>

<input
type="date"
name="task_date"
required
value="<?php echo date('Y-m-d'); ?>">

<label>Task Description</label>

<textarea
name="task"
required
placeholder="Write completed task..."></textarea>

<button
type="submit"
name="save_task"
class="save-btn">

Save Task

</button>

</form>

<h3 style="margin-top:35px;">

Previous Tasks

</h3>

<table>

<tr>

<th>Date</th>

<th>Task</th>

<th width="250">

Actions

</th>

</tr>

<?php

if(mysqli_num_rows($tasks)>0){

while($row=mysqli_fetch_assoc($tasks)){

?>

<tr>

<form method="POST">

<td>

<input
type="hidden"
name="id"
value="<?php echo $row['id']; ?>">

<input
type="date"
name="task_date"
value="<?php echo $row['task_date']; ?>">

</td>

<td>

<textarea
name="task"
style="height:80px;">

<?php echo htmlspecialchars($row['task_text']); ?>

</textarea>

</td>

<td>

<button
name="update_task"
class="edit-btn">

Update

</button>

<a
class="delete-btn"
href="?student=<?php echo $selected; ?>&delete=<?php echo $row['id']; ?>"
onclick="return confirm('Delete Task?')">

Delete

</a>

</td>

</form>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="3" style="text-align:center;">

No Tasks Found

</td>

</tr>

<?php } ?>

</table>

<?php } ?>

</div>

</body>
</html>