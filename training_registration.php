<?php
// ===============================
// بدء الجلسة
// ===============================
session_start();

// ===============================
// ربط قاعدة البيانات
// ===============================
include "db.php";

// ===============================
// التحقق من تسجيل الدخول
// ===============================
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// ===============================
// جلب الطالب
// ===============================
$student_id = $_SESSION['user_id'];
$user_result = mysqli_query($conn , "SELECT * FROM students WHERE id=$student_id");
$user = mysqli_fetch_assoc($user_result);

// ===============================
// البحث
// ===============================
$search = "";
$companies = [];

// ===============================
// البحث عن مؤسسة
// ===============================
if(isset($_POST['search_btn'])){

    $search = mysqli_real_escape_string($conn,$_POST['search']);

  $result = mysqli_query($conn,"
SELECT
id,
username AS name
FROM users
WHERE role='institution'
AND username LIKE '%$search%'
");

    while($row = mysqli_fetch_assoc($result)){
        $companies[] = $row;
    }
}
// ===============================
// إرسال الطلب
// ===============================
if(isset($_POST['submit_training'])){

    mysqli_query($conn , "
        INSERT INTO training_requests(
            student_id,
            full_name,
            university_id,
            gender,
            phone,
            address,
            completed_hours,
            company_name,
            company_address,
            training_department,
            company_phone,
            company_email,
            status
        )
        VALUES(
            '$student_id',
            '{$_POST['full_name']}',
            '{$_POST['university_id']}',
            '{$_POST['gender']}',
            '{$_POST['phone']}',
            '{$_POST['address']}',
            '{$_POST['completed_hours']}',
            '{$_POST['company_name']}',
            '{$_POST['company_address']}',
            '{$_POST['training_department']}',
            '{$_POST['company_phone']}',
            '{$_POST['company_email']}',
            'Pending'
        )
    ");
}
// ===============================
//===========زر لخانةالبحث ===============================
if(isset($_POST['apply_institution'])){

$company_id = $_POST['company_id'];

$company_name = $_POST['company_name'];

$company_address = $_POST['company_address'];

$company_phone = $_POST['company_phone'];

$company_email = $_POST['company_email'];

$department = $_POST['training_department'];

mysqli_query($conn,"
INSERT INTO training_requests
(
student_id,
full_name,
company_id,
company_name,
company_address,
company_phone,
company_email,
training_department,
status
)

VALUES
(
'$student_id',
'{$user['full_name']}',
'$company_id',
'$company_name',
'$company_address',
'$company_phone',
'$company_email',
'$department',
'Pending'
)
");

echo "<script>alert('Request Sent Successfully');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Training Registration</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* ===============================
   التصميم العام
================================*/
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
   Sidebar
================================*/
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
    padding:12px;
    margin:10px 0;
    text-decoration:none;
    border-radius:10px;
}

.sidebar a:hover{
    background:#1e293b;
}

/* ===============================
   المحتوى
================================*/
.main{
    margin-left:260px;
    padding:30px;
    width:100%;
}

/* ===============================
   كرت التصميم
================================*/
.card{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    margin-bottom:20px;
}

/* ===============================
   العناوين
================================*/
h2{
    margin-bottom:15px;
    color:#0f172a;
}

h3{
    margin:15px 0;
    color:#2563eb;
}

/* ===============================
   inputs
================================*/
input{
    width:100%;
    padding:12px;
    margin:6px 0;
    border:1px solid #ddd;
    border-radius:10px;
}

/* ===============================
   زر
================================*/
button{
    padding:12px 18px;
    border:none;
    background:linear-gradient(to right,#38bdf8,#2563eb);
    color:white;
    border-radius:10px;
    cursor:pointer;
}

/* ===============================
   نتيجة البحث
================================*/
.company-box{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px;
    border:1px solid #eee;
    border-radius:10px;
    margin:8px 0;
}

.apply-btn{
    background:#22c55e;
}

/* ===============================
   الحالة
================================*/
.status{
    padding:10px;
    background:#fef3c7;
    color:#92400e;
    border-radius:10px;
    display:inline-block;
}

</style>

</head>

<body>

<!-- ===============================
Sidebar
================================-->
<div class="sidebar">

<h2>FTS System</h2>

<a href="student_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
<a href="profile.php"><i class="fa fa-user"></i> Profile</a>
<a href="training_registration.php"><i class="fa fa-building"></i> Training</a>
<a href="login.php"><i class="fa fa-right-from-bracket"></i> Logout</a>

</div>

<!-- ===============================
Main
================================-->
<div class="main">

<h2>Training Registration</h2>

<!-- ===============================
Search
================================-->
<div class="card">

<h3>Search Company</h3>

<form method="POST">
<input type="text" name="search" placeholder="Search institution">
<button name="search_btn">Search</button>
</form>

</div>




<!-- ===============================
Results
================================-->
<div class="card">

<h3>Results</h3>

<?php

if(count($companies) == 0){
    echo "<p>No institutions found.</p>";
}

foreach($companies as $c){

?>

<div class="company-box">

<div>

<h4><?= $c['name']; ?></h4>
<p>Institution Account</p>

</div>

<form method="POST">

<input type="hidden" name="company_id"
value="<?= $c['id']; ?>">

<input type="hidden" name="company_name"
value="<?= $c['name']; ?>">

<input type="hidden" name="company_address" value="">
<input type="hidden" name="company_phone" value="">
<input type="hidden" name="company_email" value="">
<input type="hidden" name="training_department" value="">

<button type="submit"
name="apply_institution"
class="apply-btn">

Apply Now

</button>

</form>

</div>

<?php } ?>

</div>
<!-- ===============================
Manual form
================================-->
<div class="card">

<h3>Manual Entry</h3>

<form method="POST">

<input name="full_name" value="<?php echo $user['full_name']; ?>" placeholder="Full Name">

<input name="university_id" placeholder="University ID">

<input name="gender" placeholder="Gender">

<input name="phone" value="<?php echo $user['phone']; ?>" placeholder="Phone">

<input name="address" placeholder="Address">

<input name="completed_hours" placeholder="Hours">

<input name="company_name" placeholder="Company Name">

<input name="company_address" placeholder="Address">

<input name="training_department" placeholder="Department">

<input name="company_phone" placeholder="Phone">

<input name="company_email" placeholder="Email">

<button name="submit_training">Submit</button>

</form>

</div>

<!-- ===============================
Status
================================-->
<div class="card">

<h3>My Requests</h3>

<?php

$r = mysqli_query($conn , "SELECT * FROM training_requests WHERE student_id=$student_id");

while($row = mysqli_fetch_assoc($r)){

echo "
<p>
<b>".$row['company_name']."</b>
<span class='status'>".$row['status']."</span>
</p><hr>
";

}

?>

</div>

</div>

</body>
</html>