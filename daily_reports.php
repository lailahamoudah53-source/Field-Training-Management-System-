<?php

// ===============================
// بدء الجلسة
// ===============================
session_start();

// ===============================
// ربط قاعدة البيانات
// ===============================
include "db.php";

// ===============================
// التحقق من تسجيل الدخول
// ===============================
if(!isset($_SESSION['user_id'])){

    // تحويل المستخدم لصفحة الدخول
    header("Location: login.php");

    exit();
}

// ===============================
// جلب ID الطالب
// ===============================
$student_id = $_SESSION['user_id'];

// ===============================
// جلب بيانات الطالب
// ===============================
$user_result = mysqli_query($conn , "

    SELECT * FROM students

    WHERE id=$student_id

");

// ===============================
// تحويل البيانات لمصفوفة
// ===============================
$user = mysqli_fetch_assoc($user_result);

// ===============================
// عند إرسال التقرير
// ===============================
if(isset($_POST['submit_report'])){

    // تاريخ التقرير
    $report_date = $_POST['report_date'];

    // المهام المنجزة
    $completed_tasks = $_POST['completed_tasks'];

    // المهارات المكتسبة
    $learned_skills = $_POST['learned_skills'];

    // عدد ساعات العمل
    $work_hours = $_POST['work_hours'];

    // الملاحظات
    $notes = $_POST['notes'];

    // ===============================
    // إدخال التقرير داخل قاعدة البيانات
    // ===============================
    mysqli_query($conn , "

        INSERT INTO daily_reports(

            student_id,
            report_date,
            completed_tasks,
            learned_skills,
            work_hours,
            notes

        )

        VALUES(

            '$student_id',
            '$report_date',
            '$completed_tasks',
            '$learned_skills',
            '$work_hours',
            '$notes'

        )

    ");

    // ===============================
    // رسالة نجاح
    // ===============================
    $success = "Daily report submitted successfully!";
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Daily Reports</title>

    <!-- ===============================
    Google Font
    =============================== -->

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- ===============================
    Font Awesome
    =============================== -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* ===============================
تنسيق عام
=============================== */

*{

    margin:0;

    padding:0;

    box-sizing:border-box;

    font-family:'Poppins', sans-serif;
}

/* ===============================
الخلفية
=============================== */

body{

    display:flex;

    background:#f1f5f9;
}

/* ===============================
Sidebar
=============================== */

.sidebar{

    width:260px;

    height:100vh;

    background:#0f172a;

    padding:25px;

    position:fixed;

    overflow-y:auto;
}

/* ===============================
عنوان النظام
=============================== */

.sidebar h2{

    color:#38bdf8;

    text-align:center;

    margin-bottom:30px;
}

/* ===============================
روابط القائمة
=============================== */

.sidebar a{

    display:block;

    color:white;

    text-decoration:none;

    padding:14px;

    border-radius:12px;

    margin:10px 0;

    transition:0.3s;
}

/* ===============================
تأثير الهوفر
=============================== */

.sidebar a:hover{

    background:#1e293b;
}

/* ===============================
الرابط النشط
=============================== */

.active{

    background:#1e293b;
}

/* ===============================
Main Content
=============================== */

.main{

    margin-left:260px;

    width:100%;

    padding:35px;
}

/* ===============================
Card
=============================== */

.card{

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);

    margin-bottom:25px;
}

/* ===============================
العناوين
=============================== */

h1{

    color:#0f172a;

    margin-bottom:20px;
}

h3{

    color:#2563eb;

    margin-bottom:15px;
}

/* ===============================
حقول الإدخال
=============================== */

input,
textarea{

    width:100%;

    padding:14px;

    border:1px solid #ddd;

    border-radius:12px;

    margin:10px 0;

    outline:none;

    transition:0.3s;
}

/* ===============================
تأثير الضغط على الحقول
=============================== */

input:focus,
textarea:focus{

    border-color:#38bdf8;

    box-shadow:0 0 8px rgba(56,189,248,0.2);
}

/* ===============================
زر الإرسال
=============================== */

button{

    width:100%;

    padding:14px;

    border:none;

    border-radius:12px;

    background:linear-gradient(to right,#38bdf8,#2563eb);

    color:white;

    font-size:16px;

    cursor:pointer;

    transition:0.3s;
}

/* ===============================
تأثير الزر
=============================== */

button:hover{

    transform:translateY(-2px);
}

/* ===============================
رسالة النجاح
=============================== */

.success{

    background:#dcfce7;

    color:#166534;

    padding:15px;

    border-radius:12px;

    margin-bottom:20px;
}

/* ===============================
كرت التقرير
=============================== */

.report{

    background:#f8fafc;

    border-left:5px solid #38bdf8;

    padding:18px;

    border-radius:12px;

    margin-bottom:15px;
}

/* ===============================
صورة الطالب
=============================== */

.profile-image{

    width:90px;

    height:90px;

    border-radius:50%;

    object-fit:cover;

    border:3px solid #38bdf8;
}

/* ===============================
حرف البروفايل
=============================== */

.profile-letter{

    width:90px;

    height:90px;

    border-radius:50%;

    background:#38bdf8;

    display:flex;

    align-items:center;

    justify-content:center;

    color:white;

    font-size:35px;

    font-weight:bold;

    margin:auto;
}

</style>

</head>

<body>

<!-- ===============================
Sidebar
=============================== -->

<div class="sidebar">

    <!-- عنوان النظام -->

    <h2>FTS System</h2>

    <!-- ===============================
    بيانات الطالب
    =============================== -->

    <div style="text-align:center; margin-bottom:30px;">

        <?php

        // إذا يوجد صورة
        if(!empty($user['profile_image'])){

        ?>

            <img

            src="uploads/<?php echo $user['profile_image']; ?>"

            class="profile-image">

        <?php

        }

        // إذا لا يوجد صورة
        else{

            // أول حرف من الاسم
            $letter = strtoupper(substr($user['full_name'],0,1));

            echo "

            <div class='profile-letter'>

                $letter

            </div>

            ";
        }

        ?>

        <!-- اسم الطالب -->

        <h3 style="color:white; margin-top:12px;">

            <?php echo $user['full_name']; ?>

        </h3>

    </div>

    <!-- Dashboard -->

    <a href="student_dashboard.php">

        <i class="fa fa-home"></i>

        Dashboard

    </a>

    <!-- Profile -->

    <a href="profile.php">

        <i class="fa fa-user"></i>

        Profile

    </a>

    <!-- Training -->

    <a href="training_registration.php">

        <i class="fa fa-building"></i>

        Training

    </a>

    <!-- Daily Reports -->

    <a href="daily_reports.php" class="active">

        <i class="fa fa-file"></i>

        Daily Reports

    </a>

    <!-- Logout -->

    <a href="login.php">

        <i class="fa fa-right-from-bracket"></i>

        Logout

    </a>

</div>

<!-- ===============================
Main Content
=============================== -->

<div class="main">

    <!-- عنوان الصفحة -->

    <h1>Daily Follow-up Reports</h1>

    <!-- ===============================
    فورم إضافة تقرير
    =============================== -->

    <div class="card">

        <h3>Add Daily Report</h3>

        <!-- رسالة النجاح -->

        <?php

        if(isset($success)){

            echo "<div class='success'>$success</div>";
        }

        ?>

        <!-- الفورم -->

        <form method="POST">

            <!-- التاريخ -->

            <input

            type="date"

            name="report_date"

            required>

            <!-- المهام -->

            <textarea

            name="completed_tasks"

            placeholder="Completed Tasks"

            required></textarea>

            <!-- المهارات -->

            <textarea

            name="learned_skills"

            placeholder="Learned Skills"

            required></textarea>

            <!-- الساعات -->

            <input

            type="number"

            name="work_hours"

            placeholder="Work Hours"

            required>

            <!-- الملاحظات -->

            <textarea

            name="notes"

            placeholder="Additional Notes"></textarea>

            <!-- زر الإرسال -->

            <button

            type="submit"

            name="submit_report">

                Submit Report

            </button>

        </form>

    </div>

    <!-- ===============================
    عرض التقارير السابقة
    =============================== -->

    <div class="card">

        <h3>Previous Reports</h3>

        <?php

        // ===============================
        // جلب التقارير السابقة
        // ===============================
        $reports = mysqli_query($conn , "

            SELECT * FROM daily_reports

            WHERE student_id=$student_id

            ORDER BY id DESC

        ");

        // ===============================
        // عرض التقارير
        // ===============================
        while($row = mysqli_fetch_assoc($reports)){

        ?>

        <div class="report">

            <p>

                <b>Date:</b>

                <?php echo $row['report_date']; ?>

            </p>

            <br>

            <p>

                <b>Tasks:</b>

                <?php echo $row['completed_tasks']; ?>

            </p>

            <br>

            <p>

                <b>Skills:</b>

                <?php echo $row['learned_skills']; ?>

            </p>

            <br>

            <p>

                <b>Hours:</b>

                <?php echo $row['work_hours']; ?>

            </p>

            <br>

            <p>

                <b>Notes:</b>

                <?php echo $row['notes']; ?>

            </p>

        </div>

        <?php } ?>

    </div>

</div>

</body>

</html>