<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$groups = mysqli_query($conn,"
SELECT *
FROM groups
ORDER BY id DESC
");

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Training Groups</title>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    font-family:Poppins;
    background:#f1f5f9;
    padding:30px;
}

.container{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
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

.group-title{
    margin-top:30px;
    margin-bottom:15px;
    color:#2563eb;
}

</style>

</head>

<body>

<div class="container">

<a href="supervisor_dashboard.php" class="back-btn">
<i class="fa-solid fa-arrow-left"></i>
Back to Dashboard
</a>

<h2>
    <i class="fa-solid fa-users"></i>
    Training Groups
</h2>

<a href="create_group.php"
style="
background:#16a34a;
color:white;
padding:12px 18px;
border-radius:10px;
text-decoration:none;
display:inline-block;
margin:20px 0;
">
<i class="fa-solid fa-plus"></i>
Create New Group
</a>

<?php while($group = mysqli_fetch_assoc($groups)): ?>

<div style="
background:#fff;
padding:20px;
border-radius:12px;
margin-top:20px;
box-shadow:0 3px 10px rgba(0,0,0,.08);
">

<h3 class="group-title">
<?php echo $group['name']; ?>
</h3>

<table>

<tr>
<th>Student ID</th>
<th>Student Name</th>
</tr>

<?php

$group_id = $group['id'];

$members = mysqli_query($conn,"
SELECT DISTINCT s.*
FROM group_members gm
JOIN students s
ON s.id = gm.user_id
WHERE gm.group_id='$group_id'
");
if(mysqli_num_rows($members)>0){

    while($student = mysqli_fetch_assoc($members)){
?>

<tr>
    <td><?php echo $student['id']; ?></td>
    <td><?php echo $student['full_name']; ?></td>
</tr>

<?php
    }

}else{
?>

<tr>
    <td colspan="2" style="text-align:center;">
        No students in this group.
    </td>
</tr>

<?php } ?>

</table>

<br>

<a href="group_chat.php?group_id=<?php echo $group['id']; ?>"
style="
background:#38bdf8;
color:white;
padding:10px 18px;
border-radius:8px;
text-decoration:none;
">
<i class="fa-solid fa-comments"></i>
Open Chat
</a>

</div>

<?php endwhile; ?>

</div>

</body>
</html>