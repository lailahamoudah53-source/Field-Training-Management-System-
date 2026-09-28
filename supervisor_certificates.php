<?php
session_start();
include "db.php";

/* حماية الصفحة */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$message = "";

/* =========================
   إضافة شهادة
========================= */
if(isset($_POST['add_certificate'])){

    $student_id = $_POST['student_id'];
    $course_name = $_POST['course_name'];
    $provider = $_POST['provider'];

    $file_name = "";

    if(!empty($_FILES['certificate_file']['name'])){

        $file_name = time() . "_" . $_FILES['certificate_file']['name'];

        move_uploaded_file(
            $_FILES['certificate_file']['tmp_name'],
            "uploads/" . $file_name
        );
    }

    mysqli_query($conn, "
        INSERT INTO certificates (
            student_id,
            course_name,
            provider,
            certificate_file,
            created_by
        )
        VALUES (
            '$student_id',
            '$course_name',
            '$provider',
            '$file_name',
            '$supervisor_id'
        )
    ");

    $message = "Certificate added successfully ✔";
}

/* =========================
   حذف شهادة (اختياري)
========================= */
if(isset($_GET['delete'])){

    $id = intval($_GET['delete']);

    mysqli_query($conn, "
        DELETE FROM certificates
        WHERE id='$id'
    ");

    header("Location: supervisor_certificates.php");
    exit();
}

/* =========================
   جلب الطلاب
========================= */
$students = mysqli_query($conn, "SELECT id, full_name FROM students");

/* =========================
   جلب الشهادات
========================= */
$certificates = mysqli_query($conn, "
    SELECT c.*, s.full_name
    FROM certificates c
    JOIN students s ON s.id = c.student_id
    ORDER BY c.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Supervisor Certificates</title>

<style>
body{
    font-family:Arial;
    background:#f5f7fb;
}

.container{
    width:80%;
    margin:30px auto;
}
/**لاضافة زر عودة لداشبورد */
.back-btn{
    display:inline-block;
    margin-bottom:15px;
    text-decoration:none;
    background:#0f172a;
    color:white;
    padding:10px 18px;
    border-radius:8px;
    transition:0.3s;
}

.back-btn:hover{
    background:#1e293b;
}
/**================================================= */

.card{
    background:white;
    padding:25px;
    border-radius:10px;
    margin-bottom:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

input, select{
    width:100%;
    padding:10px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:6px;
}

button{
    padding:10px 20px;
    background:#0ea5e9;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th, td{
    border:1px solid #ddd;
    padding:10px;
    text-align:left;
}

th{
    background:#0f172a;
    color:white;
}

.success{
    background:#d1fae5;
    padding:10px;
    margin-bottom:10px;
    border-radius:6px;
    color:#065f46;
}

a.delete{
    color:red;
    text-decoration:none;
}
</style>

</head>

<body>

<div class="container">

<a href="supervisor_dashboard.php" class="back-btn">
    ← Back To Dashboard
</a>

<div class="card"></div>

<h2>Supervisor Certificates Management</h2>

<?php if(!empty($message)) echo "<div class='success'>$message</div>"; ?>

<!-- =========================
     إضافة شهادة
========================= -->
<div class="card">
<h3>Add Certificate</h3>

<form method="POST" enctype="multipart/form-data">

    <label>Student</label>
    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php while($s = mysqli_fetch_assoc($students)) { ?>
            <option value="<?php echo $s['id']; ?>">
                <?php echo $s['full_name']; ?>
            </option>
        <?php } ?>
    </select>

    <label>Course Name</label>
    <input type="text" name="course_name" required>

    <label>Provider</label>
    <input type="text" name="provider" required>

    <label>Certificate File</label>
    <input type="file" name="certificate_file" required>

    <button type="submit" name="add_certificate">
        Add Certificate
    </button>

</form>
</div>

<!-- =========================
     عرض الشهادات
========================= -->
<div class="card">
<h3>All Certificates</h3>

<table>
<tr>
    <th>Student</th>
    <th>Course</th>
    <th>Provider</th>
    <th>File</th>
    <th>Action</th>
</tr>

<?php while($c = mysqli_fetch_assoc($certificates)) { ?>

<tr>
    <td><?php echo $c['full_name']; ?></td>
    <td><?php echo $c['course_name']; ?></td>
    <td><?php echo $c['provider']; ?></td>
    <td>
        <a href="uploads/<?php echo $c['certificate_file']; ?>" target="_blank">
            View
        </a>
    </td>
    <td>
        <a class="delete" href="?delete=<?php echo $c['id']; ?>">
            Delete
        </a>
    </td>
</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>