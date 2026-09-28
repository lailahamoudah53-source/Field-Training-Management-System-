<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* تفعيل رصد الدرجات */
if(isset($_GET['enable'])){

    $student_id = (int)$_GET['enable'];

    $student = mysqli_query($conn,"
    SELECT *
    FROM course_registration
    WHERE student_id='$student_id'
    ");

    $row = mysqli_fetch_assoc($student);

    $check = mysqli_query($conn,"
    SELECT *
    FROM grade_permissions
    WHERE student_id='$student_id'
    ");

    if(mysqli_num_rows($check)==0){

        mysqli_query($conn,"
        INSERT INTO grade_permissions
        (
            student_id,
            student_name,
            enabled
        )
        VALUES
        (
            '".$row['student_id']."',
            '".$row['student_name']."',
            'Yes'
        )
        ");

    }else{

        mysqli_query($conn,"
        UPDATE grade_permissions
        SET enabled='Yes'
        WHERE student_id='$student_id'
        ");
    }

    echo "<script>
    alert('Grades Enabled Successfully');
    window.location='enable_grades.php';
    </script>";
}

$result = mysqli_query($conn,"
SELECT *
FROM course_registration
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Enable Grades</title>

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
background:#dc2626;
color:white;
text-decoration:none;
border-radius:10px;
font-weight:600;
}

.back-btn:hover{
background:#b91c1c;
}

h2{
color:#7f1d1d;
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#dc2626;
color:white;
padding:15px;
}

td{
padding:15px;
border-bottom:1px solid #eee;
}

tr:hover{
background:#fef2f2;
}

.enable-btn{
background:#dc2626;
color:white;
padding:10px 15px;
border-radius:10px;
text-decoration:none;
font-size:14px;
}

.enable-btn:hover{
background:#991b1b;
}

</style>

</head>

<body>

<div class="container">

<a href="admissions_dashboard.php" class="back-btn">
<i class="fa-solid fa-arrow-left"></i>
Back to Dashboard
</a>

<h2>
<i class="fa-solid fa-star"></i>
Enable For Supervisors
</h2>

<table>

<tr>
<th>Student Name</th>
<th>Course</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td>
<?php echo $row['student_name']; ?>
</td>

<td>
<?php echo $row['course_name']; ?>
</td>

<td>

<a
class="enable-btn"
href="?enable=<?php echo $row['student_id']; ?>">
Enable Grades
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>