<?php
session_start();
include "db.php";

/* =========================
   حماية الصفحة (مؤسسة فقط)
========================= */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'institution'){
    header("Location: login.php");
    exit();
}
 
/* =========================
   جلب طلاب المؤسسة
========================= */
 $institution_name = $_SESSION['name'];

$students = mysqli_query($conn,"
SELECT DISTINCT users.id, users.username
FROM users
JOIN training_requests
ON users.id = training_requests.student_id
WHERE training_requests.company_name = '$institution_name'
AND training_requests.status = 'Accepted'
AND training_requests.trainee_status = 'Accepted'
");

$selected = 0;
$result_data = null;

/* =========================
   حساب الأهلية
========================= */
if(isset($_POST['student_id'])){

    $selected = $_POST['student_id'];

    /* تقييم المشرف */
    $sup = mysqli_query($conn,"
        SELECT AVG(score) as sup_score
        FROM evaluations
        WHERE student_id='$selected'
    ");
    $sup_score = mysqli_fetch_assoc($sup)['sup_score'] ?? 0;

    /* المهام */
    $tasks = mysqli_query($conn,"
        SELECT COUNT(*) as task_count
        FROM training_tasks
        WHERE student_id='$selected'
    ");
    $task_count = mysqli_fetch_assoc($tasks)['task_count'] ?? 0;

    /* الحضور */
    $att = mysqli_query($conn,"
        SELECT COUNT(*) as att_count
        FROM attendance
        WHERE student_id='$selected'
    ");
    $att_count = mysqli_fetch_assoc($att)['att_count'] ?? 0;

    /* الحساب النهائي */
    $final_score = (
        ($sup_score * 0.5) +
        ($task_count * 2) +
        ($att_count * 2)
    );

    if($final_score >= 85){
        $result = "مؤهل للتوظيف";
    } elseif($final_score >= 70){
        $result = "مؤهل جزئياً";
    } else {
        $result = "غير مؤهل";
    }

    $result_data = [
        "score" => $final_score,
        "result" => $result
    ];
}

/* =========================
   حفظ النتيجة النهائية
========================= */
if(isset($_POST['save_final'])){

    $student_id = $_POST['student_id'];
    $score = $_POST['final_score'];
    $result = $_POST['result'];

    $institution_id = $_SESSION['user_id'];

mysqli_query($conn,"
   INSERT INTO employability
   (student_id, institution_id, final_score, result)
   VALUES
   ('$student_id','$institution_id','$score','$result')
");

    echo "<script>alert('تم حفظ التقييم النهائي');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Institution Employability</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins, sans-serif;
}

body{
    background:#f4f7fc;
    display:flex;
}

/* Sidebar */
.sidebar{
    width:250px;
    height:100vh;
    background:linear-gradient(180deg,#0f172a,#064e3b);
    padding:25px;
    position:fixed;
}

.sidebar h2{
    color:#34d399;
    text-align:center;
    margin-bottom:30px;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:12px;
    margin-bottom:10px;
    border-radius:8px;
}

.sidebar a:hover{
    background:#1e293b;
}

/* Main */
.main{
    margin-left:250px;
    padding:40px;
    width:100%;
}

.title{
    font-size:26px;
    font-weight:bold;
    margin-bottom:20px;
    color:#0f172a;
}

/* Card */
.card{
    background:white;
    padding:30px;
    border-radius:18px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    max-width:700px;
}


/* Form */
select, button{
    width:100%;
    padding:12px;
    margin-top:10px;
    margin-bottom:15px;
    border-radius:10px;
    border:1px solid #ddd;
    font-size:15px;
}

button{
    background:linear-gradient(to right,#38bdf8,#2563eb);
    color:white;
    font-weight:bold;
    border:none;
    cursor:pointer;
}

button:hover{
    transform:translateY(-2px);
}

.result-box{
    background:#ecfdf5;
    padding:15px;
    border-left:5px solid #10b981;
    margin-top:15px;
    border-radius:10px;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <h2>Institution Panel</h2>

    <a href="institution_dashboard.php">Dashboard</a>
    <a href="institution_students.php">Students</a>
    <a href="institution_evaluation.php">Evaluation</a>
    <a href="login.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<div class="title">
    <i class="fa fa-chart-line"></i>
    Institution Employability Assessment
</div>

<div class="card">

<form method="POST">

<select name="student_id" onchange="this.form.submit()" required>
    <option value="">Select Student</option>

    <?php while($s=mysqli_fetch_assoc($students)){ ?>
        <option value="<?= $s['id'] ?>" <?= ($selected==$s['id'])?'selected':'' ?>>
            <?= $s['username'] ?>
        </option>
    <?php } ?>

</select>

</form>

<?php if($result_data){ ?>

<div class="result-box">
    <h3>Final Score: <?= round($result_data['score']) ?></h3>
    <h3>Result: <?= $result_data['result'] ?></h3>
</div>

<form method="POST">

    <input type="hidden" name="student_id" value="<?= $selected ?>">
    <input type="hidden" name="final_score" value="<?= $result_data['score'] ?>">
    <input type="hidden" name="result" value="<?= $result_data['result'] ?>">

    <button name="save_final">
        Save Final Evaluation
    </button>

</form>

<?php } ?>

</div>
</div>

</body>
</html>