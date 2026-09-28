<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* --------------------------------------
   الموافقة على الطلب
-------------------------------------- */
if(isset($_GET['approve'])){

    $id = (int)$_GET['approve']; // حماية: تحويل إلى رقم صحيح

    $check = mysqli_query($conn, "SELECT * FROM training_requests WHERE id='$id'");
    $row = mysqli_fetch_assoc($check);

    // التحقق من وجود الطلب
    if(!$row){
        echo "<script>
        alert('الطلب غير موجود');
        window.location='registration_requests.php';
        </script>";
        exit();
    }
        //============لاضافة المقبولين للمجموعة===========
$groupName = trim($row['company_name']);

$checkGroup = mysqli_query($conn, "SELECT id FROM groups WHERE name='$groupName'");

if(mysqli_num_rows($checkGroup) > 0){
    $group = mysqli_fetch_assoc($checkGroup);
    $group_id = $group['id'];
} else {
    mysqli_query($conn, "INSERT INTO groups (name) VALUES ('$groupName')");
    $group_id = mysqli_insert_id($conn);
}

$checkMember = mysqli_query($conn, "
SELECT * FROM group_members 
WHERE group_id='$group_id' AND user_id='$row[student_id]'
");

if(mysqli_num_rows($checkMember) == 0){
    mysqli_query($conn,"
    INSERT INTO group_members (group_id, user_id, role)
    VALUES ('$group_id', '$row[student_id]', 'student')
    ");
}

$student_id = $row['student_id'];

$group_id = 1; // رقم المجموعة

mysqli_query($conn,"
INSERT INTO group_members(group_id,user_id,role)
VALUES('$group_id','$student_id','student')
");
//==========================================================


    // شروط القبول
    if($row['completed_hours'] >= 10 && !empty($row['company_name']) && !empty($row['company_email'])){

        // تحديث حالة الطلب
        mysqli_query($conn, "UPDATE training_requests SET status='Approved' WHERE id='$id'");
        //لارسال كتاب للمؤسسة 



        // جلب user_id الخاص بالطالب (هذا هو الخطأ الذي كان موجوداً)
        $user_id = $row['student_id']; // تأكد أن هذا العمود موجود في جدول training_requests
        
        // إذا لم يكن هناك user_id في جدول training_requests، استخدم id الطالب مباشرة
        if(empty($user_id)){
            $user_id = $row['student_id'] ?? $id; // جرب student_id أو id
        }

        $groupName = trim($row['company_name']);

        // التحقق من وجود المجموعة مسبقاً
        $checkGroup = mysqli_query($conn, "SELECT id FROM groups WHERE name='$groupName'");
        
        if(mysqli_num_rows($checkGroup) > 0){
            // المجموعة موجودة، استخدم ID الموجود
            $group = mysqli_fetch_assoc($checkGroup);
            $group_id = $group['id'];
        } else {
            // إنشاء مجموعة جديدة
            mysqli_query($conn, "INSERT INTO groups (name) VALUES ('$groupName')");
            $group_id = mysqli_insert_id($conn);
        }

        // التحقق من أن الطالب ليس عضواً بالفعل في المجموعة
        $checkMember = mysqli_query($conn, "SELECT * FROM group_members WHERE group_id='$group_id' AND user_id='$user_id'");
        
        if(mysqli_num_rows($checkMember) == 0){
            // إضافة الطالب للمجموعة
            mysqli_query($conn, "INSERT INTO group_members (group_id, user_id, role) VALUES ('$group_id', '$user_id', 'student')");
        }

    } else {
        echo "<script>
        alert('لا يمكن اعتماد الطلب: المؤسسة أو الطالب غير مستوفي للشروط');
        window.location='registration_requests.php';
        </script>";
        exit();
    }

    header("Location: registration_requests.php");
    exit();
}

/* --------------------------------------
   رفض الطلب
-------------------------------------- */
if(isset($_GET['reject'])){
    $id = (int)$_GET['reject']; // حماية: تحويل إلى رقم صحيح

    mysqli_query($conn, "UPDATE training_requests SET status='Rejected' WHERE id='$id'");

    header("Location: registration_requests.php");
    exit();
}

/* --------------------------------------
   جلب جميع الطلبات
-------------------------------------- */
$requests = mysqli_query($conn, "SELECT * FROM training_requests ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Training Requests</title>
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

.sidebar ul li a:hover{
    background:#1e293b;
}

.main{
    margin-left:250px;
    padding:40px;
    width:100%;
}

.card{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#38bdf8;
    color:white;
    padding:12px;
}

td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:center;
}

.approve{
    background:#16a34a;
    color:white;
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
    margin:0 5px;
}

.reject{
    background:#dc2626;
    color:white;
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
    margin:0 5px;
}

.approve:hover{
    background:#15803d;
}

.reject:hover{
    background:#b91c1c;
}

.pending{
    color:orange;
    font-weight:bold;
}

.approved{
    color:green;
    font-weight:bold;
}

.rejected{
    color:red;
    font-weight:bold;
}

/* إخفاء أزرار الموافقة/الرفض إذا كان الطلب معالج بالفعل */
.action-disabled{
    pointer-events:none;
    opacity:0.5;
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>FTS System</h2>
    <ul>
        <li><a href="supervisor_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li><a href="supervisor_profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
        <li><a href="registration_requests.php"><i class="fa-solid fa-clipboard-list"></i> Training Requests</a></li>
    </ul>
</div>

<div class="main">
    <div class="card">
        <h2 style="margin-bottom:20px;">Training Requests</h2>

        <table>
            <tr>
                <th>Name</th>
                <th>University ID</th>
                <th>Company</th>
                <th>Eligibility</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($requests)){ ?>
            <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['university_id']); ?></td>
                <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                <td>
                    <?php
                    if($row['completed_hours'] >= 10 && !empty($row['company_name']) && !empty($row['company_email'])){
                        echo "<span style='color:green;font-weight:bold'>مستوفي للشروط</span>";
                    } else {
                        echo "<span style='color:red;font-weight:bold'>لم يستوفِ الشروط</span>";
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if($row['status'] == "Pending"){
                        echo "<span class='pending'>Pending</span>";
                    } elseif($row['status'] == "Approved"){
                        echo "<span class='approved'>Approved</span>";
                    } else {
                        echo "<span class='rejected'>Rejected</span>";
                    }
                    ?>
                </td>
                <td>
                    <?php if($row['status'] == "Pending"): ?>
                        <a class="approve" href="?approve=<?php echo $row['id']; ?>">Approve</a>
                        <a class="reject" href="?reject=<?php echo $row['id']; ?>">Reject</a>
                    <?php else: ?>
                        <span style="color:#999;">تم المعالجة</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>