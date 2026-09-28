<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$requests = mysqli_query($conn,"
SELECT *
FROM training_requests
WHERE status='Approved'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Training Registration Requests</title>

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
    background:#7f1d1d;
    color:white;
    text-decoration:none;
    border-radius:10px;
}

.back-btn:hover{
    background:#991b1b;
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

.status{
    background:#dc2626;
    color:white;
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="container">

<a href="admissions_dashboard.php" class="back-btn">
<i class="fa fa-arrow-left"></i>
Back to Dashboard
</a>

<h2>
<i class="fa-solid fa-file-circle-check"></i>
Training Registration Requests
</h2>

<table>

<tr>
    <th>Student Name</th>
    <th>Company</th>
    <th>Status</th>
</tr>

<?php while($row=mysqli_fetch_assoc($requests)){ ?>

<tr>

<td>
<?php echo htmlspecialchars($row['full_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['company_name']); ?>
</td>

<td>
<span class="status">
<?php echo $row['status']; ?>
</span>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>