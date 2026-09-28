<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* تسجيل المساق */
if(isset($_GET['register'])){

    $student_id = (int)$_GET['register'];

    $check = mysqli_query($conn,"
    SELECT *
    FROM course_registration
    WHERE student_id='$student_id'
    ");

    if(mysqli_num_rows($check)==0){

        $student = mysqli_query($conn,"
        SELECT *
        FROM academic_verification
        WHERE student_id='$student_id'
        ");

        $row = mysqli_fetch_assoc($student);

        mysqli_query($conn,"

    INSERT INTO course_registration
(
student_id,
student_name,
course_name,
registration_date
)

VALUES
(
'".$row['student_id']."',
'".$row['student_name']."',
'Field Training',
NOW()
)
        ");

        echo "<script>
        alert('Course Registered Successfully');
        window.location='course_registration.php';
        </script>";
    }
}

$result = mysqli_query($conn,"
SELECT *
FROM academic_verification
WHERE academic_status='Eligible'
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Course Registration</title>

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
/**زر عودة لداشبورد */
.back-btn{
display:inline-block;
margin-bottom:20px;
padding:12px 18px;
background:#dc2626;
color:white;
text-decoration:none;
border-radius:10px;
font-weight:600;
transition:.3s;
}

.back-btn:hover{
background:#b91c1c;
}
/**========================== */

.btn{
background:#dc2626;
color:white;
padding:10px 15px;
border-radius:10px;
text-decoration:none;
}

.btn:hover{
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
<i class="fa-solid fa-book"></i>
Course Registration
</h2>

<table>

<tr>
<th>Student</th>
<th>GPA</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td>
<?php echo $row['student_name']; ?>
</td>

<td>
<?php echo $row['gpa']; ?>
</td>

<td>
<?php echo $row['academic_status']; ?>
</td>

<td>

<a class="btn"
href="course_registration.php?register=<?php echo $row['student_id']; ?>">

Register Course

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>