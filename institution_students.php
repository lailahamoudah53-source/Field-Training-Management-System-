<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$institution_name = $_SESSION['name'];
 

/* استقبال الطالب */
if(isset($_GET['accept'])){

    $id = (int)$_GET['accept'];

    mysqli_query($conn,"
    UPDATE training_requests
    SET trainee_status='Accepted'
    WHERE id='$id'
    ");
}
 
/* الطلاب المقبولين من المشرف */
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
<title>Institution Students</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    margin:0;
    background:#f1f5f9;
    font-family:Poppins,sans-serif;
    padding:30px;
}

.container{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

h2{
    margin-bottom:20px;
    color:#0f172a;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#10b981;
    color:white;
}

th,td{
    padding:15px;
    border-bottom:1px solid #eee;
    text-align:left;
}

.accept-btn{
    background:#10b981;
    color:white;
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
}

.accepted{
    color:green;
    font-weight:bold;
}

.back-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 15px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    border-radius:8px;
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
<i class="fa fa-users"></i>
Institution Students
</h2>

<table>

<tr>
    <th>Student Name</th>
    <th>Company</th>
    <th>Supervisor Approval</th>
    <th>Trainee Status</th>
    <th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($students)){ ?>

<tr>

<td>
<?php echo htmlspecialchars($row['full_name']); ?>
</td>
<td>
<?php echo htmlspecialchars($row['company_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['status']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['trainee_status']); ?>
</td>

<td>

<?php if($row['trainee_status'] != 'Accepted'){ ?>

<a class="accept-btn"
href="?accept=<?php echo $row['id']; ?>">
Accept Trainee
</a>

<?php } else { ?>

<span class="accepted">
Accepted
</span>

<?php } ?>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>