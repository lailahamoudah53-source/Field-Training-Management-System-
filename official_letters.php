<?php
session_start();
include "db.php";
$supervisor_id = $_SESSION['user_id'];
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}


/* عرض الكتب */
$result = mysqli_query($conn,"
SELECT
training_requests.id,
training_requests.full_name,
training_requests.company_name
FROM training_requests

JOIN supervisor_students
ON training_requests.student_id = supervisor_students.student_id

WHERE supervisor_students.supervisor_id='$supervisor_id'
AND training_requests.status='Accepted'
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
    display:flex;
}

/* --------------------------------------
   الشريط الجانبي (نفس تنسيق باقي الصفحات)
-------------------------------------- */
.sidebar{
    width:250px;
    height:100vh;
    background:#0f172a;
    padding:25px;
    position:fixed;
}

.sidebar h2{
    color:#38bdf8;
    text-align:center;
    margin-bottom:35px;
}

.sidebar ul{
    list-style:none;
}

.sidebar ul li{
    margin-bottom:15px;
}

.sidebar ul li a{
    display:block;
    padding:12px;
    color:white;
    text-decoration:none;
    border-radius:10px;
    transition:.3s;
}

.sidebar ul li a:hover,
.sidebar ul li a.active{
    background:#1e293b;
}

/* --------------------------------------
   المحتوى الرئيسي
-------------------------------------- */
.main{
    margin-left:250px;
    padding:40px;
    width:100%;
    max-width:1000px;
}

.page-title{
    display:flex;
    align-items:center;
    gap:12px;
    font-size:32px;
    color:#0f172a;
    margin-bottom:35px;
    font-weight:700;
}

.page-title i{
    color:#38bdf8;
    font-size:34px;
}

/*======================
        Card
=======================*/

.card{
    background:#fff;
    border-radius:18px;
    padding:25px;
    margin-bottom:25px;
    border-left:6px solid #38bdf8;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
    transition:.3s;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 15px 35px rgba(0,0,0,.12);
}

.card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding-bottom:15px;
    border-bottom:1px solid #ececec;
    margin-bottom:20px;
}

.company-name{
    display:flex;
    align-items:center;
    gap:12px;
    font-size:24px;
    font-weight:700;
    color:#0f172a;
}

.company-name i{
    color:#38bdf8;
    font-size:26px;
}

.date{
    font-size:14px;
    color:#64748b;
}

/*======================
     Letter Details
=======================*/

.letter-text{
    background:#f8fafc;
    border-radius:15px;
    padding:20px;
}

.letter-text p{
    margin-bottom:15px;
    font-size:17px;
    color:#334155;
    line-height:1.8;
}

.letter-text strong{
    color:#0f172a;
}

/*======================
      Button
=======================*/

.download-btn{
    display:inline-block;
    margin-top:18px;
    padding:12px 28px;
    background:#38bdf8;
    color:#fff;
    text-decoration:none;
    border-radius:10px;
    font-size:15px;
    font-weight:600;
    transition:.3s;
}

.download-btn:hover{
    background:#0ea5e9;
    transform:scale(1.04);
}

/*======================
     Empty State
=======================*/

.empty-state{
    background:#fff;
    border-radius:18px;
    text-align:center;
    padding:70px 20px;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.empty-state i{
    font-size:70px;
    color:#cbd5e1;
    margin-bottom:20px;
}

.empty-state p{
    color:#64748b;
    font-size:18px;
}

/*======================
      Responsive
=======================*/

@media(max-width:768px){

.main{
    margin-left:0;
    padding:20px;
}

.card-header{
    flex-direction:column;
    align-items:flex-start;
    gap:12px;
}

.company-name{
    font-size:20px;
}

.download-btn{
    width:100%;
    text-align:center;
}

}
</style>
</head>

<body>

<!-- الشريط الجانبي -->
<div class="sidebar">
    <h2>FTS System</h2>
    <ul>
        <li>
            <a href="supervisor_dashboard.php">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="supervisor_profile.php">
                <i class="fa-solid fa-user"></i> Profile
            </a>
        </li>
        <li>
            <a href="registration_requests.php">
                <i class="fa-solid fa-clipboard-list"></i> Training Requests
            </a>
        </li>
        <li>
            <a href="official_letters.php" class="active">
                <i class="fa-solid fa-envelope-open-text"></i> Official Letters
            </a>
        </li>
        <li>
            <a href="login.php">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- المحتوى الرئيسي -->
<div class="main">

    <!-- عنوان الصفحة -->
    <h1 class="page-title">
        <i class="fa-solid fa-envelope-open-text"></i>
        Official Letters
    </h1>
 
    <!-- عرض الخطابات -->
    <?php if(mysqli_num_rows($result) > 0): ?>

     <?php while($row = mysqli_fetch_assoc($result)){ ?>

<div class="card">

    <div class="card-header">

        <div class="company-name">
            <i class="fa-solid fa-building"></i>
            <?php echo $row['company_name']; ?>
        </div>

        <div class="date">
            <i class="fa-solid fa-calendar-days"></i>
            <?php echo date("d M Y"); ?>
        </div>

    </div>

    <div class="letter-text">

        <p>
            <strong><i class="fa-solid fa-user"></i> Student:</strong>
            <?php echo $row['full_name']; ?>
        </p>

        <p>
            <strong><i class="fa-solid fa-building"></i> Institution:</strong>
            <?php echo $row['company_name']; ?>
        </p>

        <a href="send_letter.php?id=<?php echo $row['id']; ?>" class="download-btn">
            <i class="fa-solid fa-file-lines"></i>
            send Official Letter
        </a>

    </div>

</div>

 

 
        <?php } ?>

    <?php else: ?>
        <!-- رسالة عند عدم وجود خطابات -->
        <div class="empty-state">
            <i class="fa-regular fa-envelope-open"></i>
            <p>No official letters available</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>