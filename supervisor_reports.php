<?php
// 1. بدء الجلسة والاتصال بقاعدة البيانات (يجب أن يكون في الأعلى دائماً)
session_start();
include "db.php";

// 2. التحقق من تسجيل الدخول
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// 3. معالجة الموافقة على التقرير
if(isset($_GET['approve'])){
    $id = (int)$_GET['approve']; // حماية: تحويل إلى رقم صحيح
    mysqli_query($conn, "UPDATE daily_reports SET status='Approved' WHERE id='$id'");
    header("Location: supervisor_reports.php");
    exit();
}

// 4. معالجة رفض التقرير
if(isset($_GET['reject'])){
    $id = (int)$_GET['reject']; // حماية: تحويل إلى رقم صحيح
    mysqli_query($conn, "UPDATE daily_reports SET status='Rejected' WHERE id='$id'");
    header("Location: supervisor_reports.php");
    exit();
}

// 5. جلب جميع التقارير
$reports = mysqli_query($conn, "SELECT * FROM daily_reports ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daily Reports</title>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
/* --------------------------------------
   التنسيقات العامة (نفس تنسيق المشروع)
-------------------------------------- */
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

/* --------------------------------------
   الشريط الجانبي
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
    display:flex;
    align-items:center;
    gap:12px;
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
}

.page-title{
    margin-bottom:30px;
    color:#0f172a;
    font-size:28px;
    display:flex;
    align-items:center;
    gap:12px;
}

.page-title i{
    color:#38bdf8;
}

/* --------------------------------------
   بطاقة التقرير
-------------------------------------- */
.card{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    margin-bottom:20px;
    border-left:5px solid #38bdf8;
    transition:.3s;
}

.card:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 25px rgba(0,0,0,.12);
}

.card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
    padding-bottom:15px;
    border-bottom:1px solid #eee;
}

.student-info{
    font-size:18px;
    color:#0f172a;
    font-weight:bold;
    display:flex;
    align-items:center;
    gap:10px;
}

.student-info i{
    color:#38bdf8;
}

.report-date{
    color:#64748b;
    font-size:14px;
    display:flex;
    align-items:center;
    gap:6px;
}

.report-details{
    margin-bottom:20px;
}

.detail-row{
    margin-bottom:15px;
}

.detail-label{
    font-weight:600;
    color:#0f172a;
    margin-bottom:5px;
    display:flex;
    align-items:center;
    gap:8px;
}

.detail-label i{
    color:#38bdf8;
    font-size:14px;
}

.detail-content{
    color:#334155;
    line-height:1.7;
    font-size:15px;
    padding:15px;
    background:#f8fafc;
    border-radius:10px;
    white-space: pre-wrap; /* يحافظ على الأسطر الجديدة */
}

/* --------------------------------------
   الحالة والأزرار
-------------------------------------- */
.card-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:20px;
    padding-top:15px;
    border-top:1px solid #eee;
}

.status-badge{
    padding:6px 15px;
    border-radius:20px;
    font-size:14px;
    font-weight:600;
}

.status-pending{
    background:#fff7ed;
    color:#ea580c;
    border:1px solid #ffedd5;
}

.status-approved{
    background:#f0fdf4;
    color:#16a34a;
    border:1px solid #dcfce7;
}

.status-rejected{
    background:#fef2f2;
    color:#dc2626;
    border:1px solid #fee2e2;
}

.action-buttons{
    display:flex;
    gap:10px;
}

.btn{
    padding:8px 16px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
    font-weight:500;
    transition:.3s;
    display:flex;
    align-items:center;
    gap:6px;
}

.btn-approve{
    background:#16a34a;
    color:white;
}
.btn-approve:hover{ background:#15803d; }

.btn-reject{
    background:#dc2626;
    color:white;
}
.btn-reject:hover{ background:#b91c1c; }

.btn-disabled{
    background:#e2e8f0;
    color:#94a3b8;
    cursor:not-allowed;
    pointer-events:none;
}

/* --------------------------------------
   حالة عدم وجود تقارير
-------------------------------------- */
.empty-state{
    text-align:center;
    padding:60px 20px;
    background:white;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.empty-state i{
    font-size:60px;
    color:#cbd5e1;
    margin-bottom:20px;
}

.empty-state p{
    color:#64748b;
    font-size:16px;
}

/* --------------------------------------
   تصميم متجاوب
-------------------------------------- */
@media(max-width:768px){
    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }
    .main{
        margin-left:0;
        padding:20px;
    }
    .card-header, .card-footer{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }
    .action-buttons{
        width:100%;
    }
    .btn{
        flex:1;
        justify-content:center;
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
            <a href="supervisor_reports.php" class="active">
                <i class="fa-solid fa-file-lines"></i> Daily Reports
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

    <h1 class="page-title">
        <i class="fa-solid fa-file-lines"></i>
        Daily Reports
    </h1>

    <?php if(mysqli_num_rows($reports) > 0): ?>

        <?php while($row = mysqli_fetch_assoc($reports)){ ?>
            <div class="card">
                <!-- رأس البطاقة -->
                <div class="card-header">
                    <div class="student-info">
                        <i class="fa-solid fa-user-graduate"></i>
                        Student ID: <?php echo htmlspecialchars($row['student_id']); ?> 
                        (<?php echo htmlspecialchars($row['company_name']); ?>)
                    </div>
                    <div class="report-date">
                        <i class="fa-regular fa-calendar"></i>
                        <?php echo date('d M Y', strtotime($row['report_date'])); ?>
                    </div>
                </div>

                <!-- تفاصيل التقرير -->
                <div class="report-details">
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fa-solid fa-briefcase"></i> Work Done
                        </div>
                        <div class="detail-content">
                            <?php echo nl2br(htmlspecialchars($row['work_done'])); ?>
                        </div>
                    </div>

                    <?php if(!empty($row['notes'])): ?>
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fa-solid fa-circle-exclamation"></i> Notes
                        </div>
                        <div class="detail-content">
                            <?php echo nl2br(htmlspecialchars($row['notes'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ذيل البطاقة (الحالة والأزرار) -->
                <div class="card-footer">
                    <?php
                    $status_class = '';
                    $status_text = '';
                    if($row['status'] == 'Pending'){
                        $status_class = 'status-pending';
                        $status_text = 'Pending';
                    } elseif($row['status'] == 'Approved'){
                        $status_class = 'status-approved';
                        $status_text = 'Approved';
                    } else {
                        $status_class = 'status-rejected';
                        $status_text = 'Rejected';
                    }
                    ?>
                    <span class="status-badge <?php echo $status_class; ?>">
                        <?php echo $status_text; ?>
                    </span>

                    <div class="action-buttons">
                        <?php if($row['status'] == 'Pending'): ?>
                            <a href="?approve=<?php echo $row['id']; ?>" class="btn btn-approve">
                                <i class="fa-solid fa-check"></i> Approve
                            </a>
                            <a href="?reject=<?php echo $row['id']; ?>" class="btn btn-reject">
                                <i class="fa-solid fa-xmark"></i> Reject
                            </a>
                        <?php else: ?>
                            <span class="btn btn-disabled">
                                <i class="fa-solid fa-lock"></i> Processed
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php } ?>

    <?php else: ?>
        <!-- رسالة عند عدم وجود تقارير -->
        <div class="empty-state">
            <i class="fa-regular fa-folder-open"></i>
            <p>No daily reports submitted yet.</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>