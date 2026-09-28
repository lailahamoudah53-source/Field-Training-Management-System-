<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}


$company = $_SESSION['name'];
 

$result = mysqli_query($conn,"
SELECT *
FROM official_letters
WHERE company_name='$company'
ORDER BY created_at DESC
");

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Official Letters</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins,sans-serif;
}

body{
background:#f4f7fc;
padding:40px;
}

.title{
font-size:30px;
margin-bottom:30px;
color:#0f172a;
}

.card{

background:#fff;
padding:25px;
margin-bottom:20px;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
border-left:6px solid #38bdf8;

}

.card h3{

color:#0f172a;
margin-bottom:15px;

}

.card p{

line-height:1.9;
color:#334155;

}

.date{

margin-top:15px;
color:#64748b;
font-size:14px;

}

.btn{

display:inline-block;
margin-top:20px;
padding:10px 18px;
background:#38bdf8;
color:white;
text-decoration:none;
border-radius:8px;

}

.empty{

background:white;
padding:50px;
text-align:center;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);

}
.back-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    margin-bottom:25px;
    padding:12px 22px;
    background:linear-gradient(135deg,#0f172a,#1e293b);
    color:#fff;
    text-decoration:none;
    border-radius:12px;
    font-size:15px;
    font-weight:600;
    transition:.3s;
    box-shadow:0 5px 15px rgba(15,23,42,.25);
}

.back-btn:hover{
    background:linear-gradient(135deg,#2563eb,#38bdf8);
    transform:translateY(-2px);
    box-shadow:0 8px 18px rgba(37,99,235,.35);
}

.back-btn i{
    font-size:15px;
}
</style>

</head>

<body>
    <a href="institution_dashboard.php" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
    Back to Dashboard
</a>

<h2 class="title">

<i class="fa-solid fa-envelope-open-text"></i>
Official Letters
</h2>

<?php if(mysqli_num_rows($result)>0){ ?>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<div class="card">

<h3>

<i class="fa-solid fa-building"></i>

<?php echo $row['company_name']; ?>

</h3>

<p>

<?php echo nl2br($row['letter_text']); ?>

</p>

<div class="date">

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</div>

<a href="#" class="btn" onclick="window.print()">

<i class="fa-solid fa-print"></i>

Print

</a>

</div>

<?php } ?>

<?php } else{ ?>

<div class="empty">

<h3>No Official Letters</h3>

<p>No official letters have been sent to your institution yet.</p>

</div>

<?php } ?>

</body>
</html>