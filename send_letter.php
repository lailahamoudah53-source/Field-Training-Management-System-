<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = (int)$_GET['id'];

$result = mysqli_query($conn,"
SELECT *
FROM training_requests
WHERE id='$id'
");

if(mysqli_num_rows($result)==0){
    die("Training request not found.");
}

$row = mysqli_fetch_assoc($result);
$student = $row['full_name'];
$company = $row['company_name'];

$letter = "
Dear Sir / Madam,

The Field Training Department at the University kindly requests your cooperation in accepting the student:

$student

to complete the Field Training course at your institution ($company).

The student has fulfilled all academic requirements and has been officially approved for training.

Thank you for your cooperation.

Field Training Department
University
";

/* منع تكرار إنشاء نفس الكتاب */
$check = mysqli_query($conn,"
SELECT id
FROM official_letters
WHERE request_id='$id'
");

if(mysqli_num_rows($check)==0){

    mysqli_query($conn,"
    INSERT INTO official_letters
    (request_id,student_name,company_name,letter_text,created_at)
    VALUES
    (
    '$id',
    '$student',
    '$company',
    '".mysqli_real_escape_string($conn,$letter)."',
    NOW()
    )
    ");

}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Official Letter</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    background:#f4f7fc;
    font-family:Poppins,sans-serif;
    padding:40px;
}

.card{

    max-width:900px;
    margin:auto;
    background:white;
    padding:50px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.1);

}

h2{

    text-align:center;
    color:#0f172a;
    margin-bottom:40px;

}

p{

    line-height:2;
    font-size:18px;
    color:#334155;

}

.btn{

    display:inline-block;
    margin-top:40px;
    padding:12px 25px;
    background:#38bdf8;
    color:white;
    text-decoration:none;
    border-radius:10px;
    margin-right:10px;

}
.download-btn {

    display:inline-block;
    margin-top:40px;
    padding:12px 25px;
    background:#38bdf8;
    color:white;
    text-decoration:none;
    border-radius:10px;
    margin-right:10px;

}

</style>

</head>

<body>

<div class="card">

<h2>Official Letter</h2>

<p><strong>Date:</strong> <?php echo date("d M Y"); ?></p>

<br>

<p>

To:<br>

<strong><?php echo $row['company_name']; ?></strong>

</p>

<br>

<p>

Subject: Field Training Student

</p>

<br>

<p>

<?php echo nl2br($letter); ?>

</p>

<br><br>

<p>

Sincerely,

<br>

Field Training Department

<br>

University

</p>

<a href="#" class="btn" onclick="window.print()">

<i class="fa-solid fa-print"></i>

Print Letter

</a>


<a href="official_letters.php" class="btn">

Back

</a>

</div>

</body>
</html>