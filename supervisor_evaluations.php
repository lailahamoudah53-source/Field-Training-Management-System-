<?php
session_start();
include "db.php";

/* =========================
   التحقق من تسجيل الدخول
========================= */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$supervisor_id = (int)$_SESSION['user_id'];
 

/* =========================
   جلب الطلاب المرتبطين بالمشرف
========================= */
$students = mysqli_query($conn,"
SELECT DISTINCT 
    users.id AS student_id,
    users.username AS full_name
FROM users
JOIN supervisor_students 
    ON users.id = supervisor_students.student_id
JOIN training_requests 
    ON users.id = training_requests.student_id
WHERE supervisor_students.supervisor_id = '$supervisor_id'
AND training_requests.status = 'Accepted'
");
 

/* =========================
   اختيار طالب
========================= */
$selected_student_id = 0;
$institution_data = null;

if(isset($_POST['student_id'])){
    $selected_student_id = (int)$_POST['student_id'];

    $inst = mysqli_query($conn,"
        SELECT *
        FROM evaluations
        WHERE student_id='$selected_student_id'
        ORDER BY id DESC
        LIMIT 1
    ");

    $institution_data = mysqli_fetch_assoc($inst);
}

/* =========================
   حفظ تقييم المشرف
========================= */
if(isset($_POST['save'])){

    $student_id = (int)$_POST['student_id'];
    $score = (int)$_POST['score'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    /* منع التكرار */
    $check = mysqli_query($conn,"
        SELECT * 
        FROM evaluations
        WHERE student_id='$student_id'
        AND supervisor_id='$supervisor_id'
    ");

    if(mysqli_num_rows($check) > 0){

        echo "<script>alert('⚠ هذا الطالب تم تقييمه مسبقاً');</script>";

    } else {

      mysqli_query($conn,"
INSERT INTO evaluations
(student_id, supervisor_id, score, notes)
VALUES
('$student_id', '$supervisor_id', '$score', '$notes')
");

        echo "<script>
        alert('✅ تم حفظ التقييم بنجاح');
        window.location.href='supervisor_evaluations.php';
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Supervisor Evaluation</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* ====== نفس تصميمك ====== */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:#f4f7fc;
    display:flex;
}

/* SIDEBAR */
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
    margin-bottom:30px;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:12px;
    margin-bottom:10px;
    border-radius:8px;
    transition:0.3s;
}

.sidebar a:hover{
    background:#1e293b;
}

.sidebar a.active{
    background:#2563eb;
}

/* MAIN */
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
    display:flex;
    align-items:center;
    gap:10px;
}

/* CARD */
.card{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    max-width:800px;
}

/* FORM */
label{
    font-weight:600;
    display:block;
    margin-top:10px;
    margin-bottom:6px;
}

select, input, textarea{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:10px;
    margin-bottom:15px;
    font-size:14px;
}

/* BUTTON */
button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:10px;
    background:linear-gradient(to right,#38bdf8,#2563eb);
    color:white;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    transform:translateY(-2px);
}

/* BOX */
.inst-box{
    background:#ecfdf5;
    padding:15px;
    border-left:5px solid #10b981;
    border-radius:10px;
    margin-bottom:20px;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>FTS System</h2>

    <a href="supervisor_dashboard.php">Dashboard</a>
    <a href="supervisor_profile.php">Profile</a>
    <a href="supervisor_attendance.php">Attendance</a>
    <a href="supervisor_evaluations.php" class="active">Evaluation</a>
    <a href="login.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<div class="title">
    <i class="fa fa-star"></i>
    Final Evaluation
</div>

<div class="card">

<!-- اختيار طالب -->
<form method="POST">
    <label>Select Student</label>

    <select name="student_id" onchange="this.form.submit()" required>
        <option value="">-- Select Student --</option>

        <?php while($s=mysqli_fetch_assoc($students)){ ?>
            <option value="<?php echo $s['student_id']; ?>"
            <?php if($selected_student_id==$s['student_id']) echo "selected"; ?>>

                <?php echo $s['full_name']; ?>

            </option>
        <?php } ?>
    </select>
</form>

<!-- عرض تقييم المؤسسة -->
<?php if($institution_data){ ?>

<div class="inst-box">
    <h3>Previous Evaluation</h3>

    <p><b>Score:</b> <?php echo $institution_data['score']; ?></p>
    <p><b>Notes:</b> <?php echo $institution_data['notes']; ?></p>
    <p><b>Date:</b> <?php echo $institution_data['created_at']; ?></p>
</div>

<?php } ?>

<!-- تقييم المشرف -->
<form method="POST">

<input type="hidden" name="student_id" value="<?php echo $selected_student_id; ?>">

<label>Score (Supervisor)</label>
<input type="number" name="score" min="0" max="100" required>

<label>Notes</label>
<textarea name="notes" required></textarea>

<button type="submit" name="save">
Save Evaluation
</button>

</form>

</div>
</div>

</body>
</html>