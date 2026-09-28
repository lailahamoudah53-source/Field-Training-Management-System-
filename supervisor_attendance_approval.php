<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* اعتماد */
if(isset($_GET['approve'])){
    $id = $_GET['approve'];

    mysqli_query($conn,"
    UPDATE attendance
    SET status='Approved'
    WHERE id='$id'
    ");
}

/* رفض */
if(isset($_GET['reject'])){
    $id = $_GET['reject'];

    mysqli_query($conn,"
    UPDATE attendance
    SET status='Rejected'
    WHERE id='$id'
    ");
}

/* جلب البيانات */
$result = mysqli_query($conn,"
SELECT * FROM attendance
ORDER BY attend_date DESC
");
?>

<h2>Attendance Approval</h2>

<table border="1" cellpadding="10">

<tr>
<th>Student</th>
<th>Company</th>
<th>Date</th>
<th>Check In</th>
<th>Check Out</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?= $row['student_name'] ?></td>
<td><?= $row['company_name'] ?></td>
<td><?= $row['attend_date'] ?></td>
<td><?= $row['check_in'] ?></td>
<td><?= $row['check_out'] ?></td>

<td><?= $row['status'] ?></td>

<td>

<?php if($row['status']=='Pending'){ ?>

<a href="?approve=<?= $row['id'] ?>">Approve</a>
|
<a href="?reject=<?= $row['id'] ?>">Reject</a>

<?php } else { ?>

Done

<?php } ?>

</td>

</tr>

<?php } ?>

</table>