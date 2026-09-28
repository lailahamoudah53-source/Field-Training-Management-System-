<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location:login.php");
    exit();
}

if(isset($_POST['create'])){

    $name=mysqli_real_escape_string($conn,$_POST['group_name']);

    mysqli_query($conn,"
    INSERT INTO groups(name)
    VALUES('$name')
    ");

    $group_id=mysqli_insert_id($conn);

    if(isset($_POST['students'])){

        foreach($_POST['students'] as $student){

            mysqli_query($conn,"
            INSERT INTO group_members(group_id,user_id)
            VALUES('$group_id','$student')
            ");

        }

    }

    echo "<script>

    alert('Group created successfully');

    window.location='training_groups.php';

    </script>";

}
?>

<!DOCTYPE html>

<html>

<head>

<title>Create Group</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{

font-family:Poppins;

background:#f1f5f9;

padding:40px;

}

.card{

background:white;

padding:30px;

border-radius:15px;

max-width:700px;

margin:auto;

}

input{

width:100%;

padding:12px;

margin:15px 0;

}

.student{

padding:10px;

}

button{

background:#38bdf8;

color:white;

border:none;

padding:12px 25px;

border-radius:10px;

cursor:pointer;

}

</style>

</head>

<body>

<div class="card">

<h2>Create Training Group</h2>

<form method="POST">

<input
type="text"
name="group_name"
placeholder="Group Name"
required>

<h3>Select Students</h3>

<?php

$students=mysqli_query($conn,"
SELECT *
FROM users
WHERE role='student'
");

while($row=mysqli_fetch_assoc($students)){

?>

<div class="student">

<label>

<input
type="checkbox"
name="students[]"
value="<?php echo $row['id'];?>">

<?php echo $row['username'];?>

</label>

</div>

<?php } ?>

<br>

<button
type="submit"
name="create">

Create Group

</button>

</form>

</div>

</body>

</html>