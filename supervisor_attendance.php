<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* --------------------------------------
   تسجيل حضور جديد
-------------------------------------- */
if(isset($_POST['save'])){
    $student_id = (int)$_POST['student_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    mysqli_query($conn, "INSERT INTO attendance 
        (student_id, company_name, attend_date, status, notes)
        VALUES 
        ('$student_id', 'Training', '$date', '$status', '$notes')");

    header("Location: supervisor_attendance.php");
    exit();
}

/* --------------------------------------
   جلب البيانات
-------------------------------------- */
$data = mysqli_query($conn, "SELECT * FROM attendance ORDER BY id DESC");
$result = mysqli_query($conn,"
SELECT *
FROM attendance
ORDER BY attend_date DESC
");

// إحصائيات سريعة
$present_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM attendance WHERE status='Present'"))['c'];
$absent_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM attendance WHERE status='Absent'"))['c'];
$late_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM attendance WHERE status='Late'"))['c'];
$total_count = $present_count + $absent_count + $late_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Monitoring</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
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

.sidebar ul{ list-style:none; }
.sidebar ul li{ margin-bottom:15px; }

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

.page-title i{ color:#38bdf8; }

/* --------------------------------------
   بطاقات الإحصائيات
-------------------------------------- */
.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
    margin-bottom:35px;
}

.stat-card{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    display:flex;
    align-items:center;
    gap:20px;
    transition:.3s;
}

.stat-card:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 25px rgba(0,0,0,.12);
}

.stat-icon{
    width:55px;
    height:55px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
}

.icon-blue{ background:#eff6ff; color:#2563eb; }
.icon-green{ background:#f0fdf4; color:#16a34a; }
.icon-red{ background:#fef2f2; color:#dc2626; }
.icon-orange{ background:#fff7ed; color:#ea580c; }

.stat-info h3{
    font-size:26px;
    color:#0f172a;
    margin-bottom:3px;
}

.stat-info p{
    color:#64748b;
    font-size:13px;
}

/* --------------------------------------
   نموذج إضافة الحضور
-------------------------------------- */
.form-card{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    margin-bottom:30px;
    border-left:5px solid #38bdf8;
}

.form-card h3{
    color:#0f172a;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
}

.form-card h3 i{ color:#38bdf8; }

.form-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
    margin-bottom:20px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group label{
    margin-bottom:6px;
    font-weight:500;
    color:#0f172a;
    font-size:14px;
}

.form-group input,
.form-group select,
.form-group textarea{
    padding:12px;
    border:1px solid #ddd;
    border-radius:10px;
    font-size:14px;
    outline:none;
    transition:.3s;
    font-family:inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus{
    border-color:#38bdf8;
    box-shadow:0 0 0 3px rgba(56,189,248,.15);
}

.full-width{
    grid-column:1 / -1;
}

.save-btn{
    padding:12px 25px;
    border:none;
    border-radius:10px;
    background:linear-gradient(to right,#38bdf8,#2563eb);
    color:white;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
    display:flex;
    align-items:center;
    gap:8px;
}

.save-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 5px 15px rgba(56,189,248,.4);
}

/* --------------------------------------
   قائمة سجلات الحضور
-------------------------------------- */
.records-section{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.records-section h3{
    color:#0f172a;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
}

.records-section h3 i{ color:#38bdf8; }

.record-card{
    background:#f8fafc;
    padding:20px;
    border-radius:12px;
    margin-bottom:15px;
    border-left:4px solid #38bdf8;
    transition:.3s;
}

.record-card:hover{
    transform:translateX(5px);
    box-shadow:0 3px 10px rgba(0,0,0,.05);
}

.record-card.status-present{ border-left-color:#16a34a; }
.record-card.status-absent{ border-left-color:#dc2626; }
.record-card.status-late{ border-left-color:#ea580c; }

.record-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:12px;
    flex-wrap:wrap;
    gap:10px;
}

.student-info{
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:600;
    color:#0f172a;
}

.student-info i{ color:#38bdf8; }

.status-badge{
    padding:5px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.badge-present{
    background:#f0fdf4;
    color:#16a34a;
    border:1px solid #dcfce7;
}

.badge-absent{
    background:#fef2f2;
    color:#dc2626;
    border:1px solid #fee2e2;
}

.badge-late{
    background:#fff7ed;
    color:#ea580c;
    border:1px solid #ffedd5;
}

.record-details{
    display:flex;
    gap:20px;
    color:#64748b;
    font-size:14px;
    flex-wrap:wrap;
}

.record-details span{
    display:flex;
    align-items:center;
    gap:6px;
}

.record-notes{
    margin-top:10px;
    padding:10px 15px;
    background:white;
    border-radius:8px;
    color:#334155;
    font-size:14px;
    border-left:3px solid #cbd5e1;
}

/* --------------------------------------
   حالة عدم وجود سجلات
-------------------------------------- */
.empty-state{
    text-align:center;
    padding:60px 20px;
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
    .sidebar{ width:100%; height:auto; position:relative; }
    .main{ margin-left:0; padding:20px; }
    .form-grid{ grid-template-columns:1fr; }
    .record-header{ flex-direction:column; align-items:flex-start; }
}
</style>
</head>

<body>

<!-- الشريط الجانبي -->
<div class="sidebar">
    <h2>FTS System</h2>
    <ul>
        <li><a href="supervisor_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li><a href="supervisor_profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
        <li><a href="registration_requests.php"><i class="fa-solid fa-clipboard-list"></i> Training Requests</a></li>
        <li><a href="official_letters.php"><i class="fa-solid fa-envelope-open-text"></i> Official Letters</a></li>
        <li><a href="supervisor_attendance.php" class="active"><i class="fa-solid fa-calendar-check"></i> Attendance</a></li>
        <li><a href="supervisor_reports.php"><i class="fa-solid fa-file-lines"></i> Reports</a></li>
        <li><a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>

<!-- المحتوى الرئيسي -->
<div class="main">

    <!-- عنوان الصفحة -->
    <h1 class="page-title">
        <i class="fa-solid fa-calendar-check"></i>
        Attendance Monitoring
    </h1>

    <!-- بطاقات الإحصائيات -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_count; ?></h3>
                <p>Total Records</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-green">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $present_count; ?></h3>
                <p>Present</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-red">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $absent_count; ?></h3>
                <p>Absent</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-orange">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $late_count; ?></h3>
                <p>Late</p>
            </div>
        </div>
    </div>

    <!-- نموذج إضافة الحضور -->
    <div class="form-card">
        <h3><i class="fa-solid fa-plus-circle"></i> Add New Attendance Record</h3>
        
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="number" name="student_id" placeholder="e.g. 12345" required>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="Present">✅ Present</option>
                        <option value="Absent">❌ Absent</option>
                        <option value="Late">⏰ Late</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Notes (Optional)</label>
                    <textarea name="notes" rows="2" placeholder="Add any notes here..."></textarea>
                </div>
            </div>

            <button type="submit" name="save" class="save-btn">
                <i class="fa-solid fa-save"></i> Save Record
            </button>
        </form>
    </div>

    <!-- سجلات الحضور -->
    <div class="records-section">
        <h3><i class="fa-solid fa-list"></i> Attendance Records</h3>

        <?php if(mysqli_num_rows($data) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($data)){ 
                $status = $row['status'] ?? 'Present';
                $status_class = 'status-present';
                $badge_class = 'badge-present';
                $icon = 'fa-circle-check';
                
                if($status == 'Absent'){
                    $status_class = 'status-absent';
                    $badge_class = 'badge-absent';
                    $icon = 'fa-circle-xmark';
                } elseif($status == 'Late'){
                    $status_class = 'status-late';
                    $badge_class = 'badge-late';
                    $icon = 'fa-clock';
                }
            ?>
                <div class="record-card <?php echo $status_class; ?>">
                    <div class="record-header">
                        <div class="student-info">
                            <i class="fa-solid fa-user-graduate"></i>
                            Student ID: <?php echo htmlspecialchars($row['student_id']); ?>
                        </div>
                        <span class="status-badge <?php echo $badge_class; ?>">
                            <i class="fa-solid <?php echo $icon; ?>"></i>
                            <?php echo $status; ?>
                        </span>
                    </div>

                    <div class="record-details">
                        <span>
                            <i class="fa-regular fa-calendar"></i>
                            <?php 
                            if(!empty($row['attend_date']) && strtotime($row['attend_date'])){
                                echo date('d M Y', strtotime($row['attend_date']));
                            } else {
                                echo htmlspecialchars($row['attend_date'] ?? 'N/A');
                            }
                            ?>
                        </span>
                        <span>
                            <i class="fa-solid fa-building"></i>
                            <?php echo htmlspecialchars($row['company_name'] ?? 'Training'); ?>
                        </span>
                    </div>

                    <?php if(!empty($row['notes'])): ?>
                        <div class="record-notes">
                            <i class="fa-solid fa-note-sticky" style="color:#38bdf8;"></i>
                            <?php echo htmlspecialchars($row['notes']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php } ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-regular fa-calendar-xmark"></i>
                <p>No attendance records yet. Add the first one above!</p>
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>