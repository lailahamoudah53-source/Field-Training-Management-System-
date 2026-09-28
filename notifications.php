<?php
session_start();
include "db.php";

$user_name = $_SESSION['name'];

$result = mysqli_query($conn,"
SELECT *
FROM notifications
WHERE user_name='$user_name'
ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<style>

.back-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:10px 15px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    border-radius:8px;
}

.back-btn:hover{
    background:#1e293b;
}

.card{
    background:#fff;
    padding:15px;
    margin:10px 0;
    border-radius:10px;
}

</style>
</head>

<body>

<a href="javascript:history.back()" class="back-btn">⬅ Back</a>

<h2>Notifications</h2>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<div class="card">
    <p><?php echo $row['message']; ?></p>
    <small><?php echo $row['created_at']; ?></small>
</div>

<?php } ?>

</body>
</html>