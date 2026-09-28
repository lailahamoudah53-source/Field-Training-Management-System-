<?php
session_start();
include "db.php";

$files = mysqli_query($conn, "
    SELECT f.*, s.full_name
    FROM student_uploads f
    JOIN students s ON s.id = f.student_id
    ORDER BY f.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Review Student Files</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
body{
    font-family:Poppins;
    background:#f1f5f9;
    padding:30px;
}

.box{
    background:white;
    padding:25px;
    border-radius:15px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:15px;
    border-bottom:1px solid #eee;
}

th{
    background:#38bdf8;
    color:white;
}
/**لاضافة زر عودة لداشبورد  */
.back-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:12px 18px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    border-radius:10px;
    transition:0.3s;
}

.back-btn:hover{
    background:#1e293b;
}
/**=============================== */
.btn{
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
    color:white;
    font-size:13px;
}

.approve{background:green;}
.reject{background:red;}

</style>
</head>

<body>

<div class="box">
<a href="supervisor_dashboard.php" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
    Back to Dashboard
</a>
<h2><i class="fa-solid fa-folder"></i> Student Files Review</h2>

<table>
<tr>
    <th>Student</th>
    <th>File</th>
    <th>Type</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($files)): ?>
<tr>
    <td><?php echo $row['full_name']; ?></td>

    <td>
        <a href="<?php echo $row['file_path']; ?>" target="_blank">Open</a>
    </td>

    <td><?php echo $row['file_type']; ?></td>

    <td><?php echo $row['status']; ?></td>

    <td>
        <a class="btn approve" href="review_file.php?id=<?php echo $row['id']; ?>&status=Approved">Approve</a>
        <a class="btn reject" href="review_file.php?id=<?php echo $row['id']; ?>&status=Rejected">Reject</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</div>

</body>