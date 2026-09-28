<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$message="";

/* ==========================
   إرسال كتاب رسمي
========================== */

if(isset($_POST['send'])){

    $student_id = (int)$_POST['student_id'];
    $title = mysqli_real_escape_string($conn,$_POST['title']);
    $letter = mysqli_real_escape_string($conn,$_POST['letter_text']);

    $file_name="";

    if(!empty($_FILES['file']['name'])){

        $file_name=time()."_".$_FILES['file']['name'];

        move_uploaded_file(
            $_FILES['file']['tmp_name'],
            "uploads/".$file_name
        );
    }

    mysqli_query($conn,"
    INSERT INTO official_letters
    (
        student_id,
        supervisor_id,
        title,
        letter_text,
        file_name,
        created_at
    )
    VALUES
    (
        '$student_id',
        '$supervisor_id',
        '$title',
        '$letter',
        '$file_name',
        NOW()
    )
    ");

    $message="Official Letter Sent Successfully.";
}


/* ==========================
   طلاب المشرف فقط
========================== */

$students=mysqli_query($conn,"
SELECT
students.id,
students.full_name
FROM students
JOIN supervisor_students
ON students.id=supervisor_students.student_id
WHERE supervisor_students.supervisor_id='$supervisor_id'
");


/* ==========================
   الكتب السابقة
========================== */

$letters=mysqli_query($conn,"
SELECT
official_letters.*,
students.full_name
FROM official_letters
JOIN students
ON students.id=official_letters.student_id
WHERE official_letters.supervisor_id='$supervisor_id'
ORDER BY official_letters.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Official Letters</title>

<style>

body{
font-family:Arial;
background:#f3f6fa;
}

.container{
width:85%;
margin:auto;
margin-top:30px;
}

.card{
background:white;
padding:25px;
border-radius:12px;
margin-bottom:20px;
box-shadow:0 2px 8px rgba(0,0,0,.1);
}

input,
select,
textarea{

width:100%;
padding:10px;
margin:8px 0;
border:1px solid #ccc;
border-radius:6px;

}

textarea{

height:160px;

}

button{

background:#0ea5e9;
color:white;
padding:12px 20px;
border:none;
border-radius:6px;
cursor:pointer;

}

table{

width:100%;
border-collapse:collapse;

}

table th,
table td{

border:1px solid #ddd;
padding:12px;

}

th{

background:#0f172a;
color:white;

}

.success{

background:#d1fae5;
padding:12px;
border-radius:8px;
color:#065f46;
margin-bottom:15px;

}

.back{

display:inline-block;
padding:10px 18px;
background:#0f172a;
color:white;
text-decoration:none;
border-radius:6px;
margin-bottom:15px;

}

</style>

</head>

<body>

<div class="container">

<a href="supervisor_dashboard.php" class="back">
Back To Dashboard
</a>

<?php
if($message!=""){
echo "<div class='success'>$message</div>";
}
?>

<div class="card">

<h2>Send Official Letter</h2>

<form method="POST" enctype="multipart/form-data">

<label>Student</label>

<select name="student_id" required>

<option value="">Select Student</option>

<?php while($s=mysqli_fetch_assoc($students)){ ?>

<option value="<?php echo $s['id']; ?>">

<?php echo $s['full_name']; ?>

</option>

<?php } ?>

</select>

<label>Letter Title</label>

<input
type="text"
name="title"
required>

<label>Letter Content</label>

<textarea
name="letter_text"
required></textarea>

<label>Attach File (Optional)</label>

<input
type="file"
name="file">

<br><br>

<button
type="submit"
name="send">

Send Letter

</button>

</form>

</div>


<div class="card">

<h2>Sent Letters</h2>

<table>

<tr>

<th>Student</th>
<th>Title</th>
<th>Date</th>
<th>Attachment</th>

</tr>

<?php while($row=mysqli_fetch_assoc($letters)){ ?>

<tr>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['created_at']; ?></td>

<td>

<?php
if($row['file_name']!=""){
?>

<a href="uploads/<?php echo $row['file_name']; ?>" target="_blank">

Open

</a>

<?php
}else{

echo "-";

}
?>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>