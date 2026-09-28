<?php
session_start();
include "db.php";
$institution_name = $_SESSION['name'];
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn,"
SELECT *
FROM training_requests
WHERE status='Accepted'
AND trainee_status='Accepted'
AND company_name='$institution_name'
");
if(isset($_POST['save'])){

    $id = $_POST['request_id'];

    $department = $_POST['department'];

    $supervisor = $_POST['supervisor_name'];

 mysqli_query($conn,"
UPDATE training_requests
SET
trainee_department='$department',
supervisor_name='$supervisor'
WHERE id='$id'
AND (supervisor_name IS NULL OR supervisor_name = '')
");
mysqli_query($conn,"
INSERT INTO notifications (user_type, user_name, message)
VALUES (
'student',
(SELECT full_name FROM training_requests WHERE id='$id'),
'تم تعيين مشرفك: $supervisor - القسم: $department'
)
");
    echo "<script>alert('Saved Successfully');</script>";
}
mysqli_query($conn,"
INSERT INTO notifications (user_type, user_name, message)
VALUES (
'institution',
'$institution_name',
'تم تعيين مشرف للطالب بنجاح'
)
");
 

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Assign Supervisor</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
font-family:Poppins,sans-serif;
background:#f1f5f9;
padding:30px;
}

.card{
background:white;
padding:25px;
border-radius:15px;
margin-bottom:20px;
box-shadow:0 3px 10px rgba(0,0,0,.08);
}
/**===زر عودة === */
.back-btn{
    display:inline-block;
    margin-bottom:15px;
    padding:10px 15px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    border-radius:8px;
}

.back-btn:hover{
    background:#1e293b;
}
/**========================== */
input{
width:100%;
padding:10px;
margin:5px 0;
border:1px solid #ddd;
border-radius:8px;
}

button{
background:#10b981;
color:white;
border:none;
padding:10px 15px;
border-radius:8px;
cursor:pointer;
}

</style>

</head>

<body>
<a href="institution_dashboard.php" class="back-btn">
⬅ Back to Dashboard
</a>
<h2>Assign Department & Supervisor</h2>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<div class="card">

<h3>
<?php echo $row['full_name']; ?>
</h3>

<p>
Institution:
<?php echo $row['company_name']; ?>
</p>
<form method="POST">

<input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">

<input type="text" name="department" placeholder="Training Department" required>

<input type="text" name="supervisor_name" placeholder="Supervisor Name" required>

<?php if(empty($row['supervisor_name'])) { ?>

<button name="save">
Save
</button>

<?php } else { ?>

<p>Supervisor Assigned</p>

<?php } ?>

</form>

</div>

<?php } ?>

</body>
</html>