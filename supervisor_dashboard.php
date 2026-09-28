<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];

$supervisor_result = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
$supervisor = mysqli_fetch_assoc($supervisor_result);
$profile_image = !empty($supervisor['profile_image']) ? "uploads/" . $supervisor['profile_image'] : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";

$students_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM students");
$students = mysqli_fetch_assoc($students_result);

$reports_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM daily_reports");
$reports = mysqli_fetch_assoc($reports_result);

$requests_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM training_requests");
$requests = mysqli_fetch_assoc($requests_result);

$pending_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM training_requests WHERE status='Pending'");
$pending = mysqli_fetch_assoc($pending_result);

$activities = mysqli_query($conn, "SELECT * FROM training_requests ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Supervisor Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    display:flex;
    background:#f1f5f9;
    min-height:100vh;
    overflow-x:hidden;
}

/* --------------------------------------
   الشريط الجانبي المتحرك
-------------------------------------- */
.sidebar{
    width:260px;
    height:100vh;
    background:#0f172a;
    color:white;
    padding:30px 20px;
    position:fixed;
    top:0;
    left:0;
    z-index:1000;
    /* إخفاء الشريط خارج الشاشة */
    transform:translateX(-260px);
    transition:transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow:5px 0 25px rgba(0,0,0,0.2);
    overflow-y:auto;
}

/* منطقة التحفيز (Trigger) - شريط رفيع على اليسار */
.sidebar-trigger{
    position:fixed;
    top:0;
    left:0;
    width:15px;
    height:100vh;
    z-index:999;
    cursor:pointer;
}

/* عند المرور على الشريط أو منطقة التحفيز، يظهر الشريط */
.sidebar-trigger:hover ~ .sidebar,
.sidebar:hover{
    transform:translateX(0);
}

.sidebar h2{
    text-align:center;
    margin-bottom:40px;
    color:#38bdf8;
    font-size:22px;
}

.sidebar ul{
    list-style:none;
}

.sidebar ul li{
    margin:18px 0;
}

.sidebar ul li a{
    color:white;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px;
    border-radius:12px;
    transition:0.3s;
    font-size:15px;
}

.sidebar ul li a:hover,
.sidebar ul li a.active{
    background:#1e293b;
    transform:translateX(5px);
}

.sidebar ul li a i{
    font-size:18px;
    width:24px;
    text-align:center;
}

/* --------------------------------------
   المحتوى الرئيسي
-------------------------------------- */
.main-content{
    width:100%;
    padding:30px;
    margin-left:0;
    transition:margin-left 0.4s ease;
}

/* --------------------------------------
   شريط التنقل العلوي (Topbar)
-------------------------------------- */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    background:white;
    padding:20px 25px;
    border-radius:15px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.topbar h1{
    color:#0f172a;
    font-size:24px;
}

.profile{
    display:flex;
    align-items:center;
    gap:12px;
}

.profile span{
    color:#475569;
    font-weight:500;
}

.profile img{
    width:45px;
    height:45px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #38bdf8;
}

/* --------------------------------------
   تلميح للمستخدم (Hover Hint)
-------------------------------------- */
.hover-hint{
    position:fixed;
    top:50%;
    left:0;
    transform:translateY(-50%);
    background:#38bdf8;
    color:white;
    padding:10px 8px 10px 12px;
    border-radius:0 10px 10px 0;
    font-size:12px;
    z-index:998;
    box-shadow:2px 0 10px rgba(0,0,0,0.1);
    transition:all 0.3s ease;
    cursor:pointer;
    writing-mode:vertical-rl;
    text-orientation:mixed;
    letter-spacing:2px;
    font-weight:600;
}

.hover-hint i{
    writing-mode:horizontal-tb;
    margin-bottom:5px;
    display:block;
    animation:bounce 1.5s infinite;
}

/* إخفاء التلميح عند ظهور الشريط */
.sidebar-trigger:hover ~ .hover-hint,
.sidebar:hover ~ .hover-hint{
    opacity:0;
    left:-50px;
}

@keyframes bounce{
    0%, 100%{ transform:translateX(0); }
    50%{ transform:translateX(-5px); }
}

/* --------------------------------------
   بطاقات الإحصائيات
-------------------------------------- */
.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:35px;
}

.stat-card{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    transition:0.3s;
    display:flex;
    align-items:center;
    gap:20px;
}

.stat-card:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 30px rgba(0,0,0,0.12);
}

.stat-icon{
    width:60px;
    height:60px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
}

.icon-blue{ background:#eff6ff; color:#2563eb; }
.icon-orange{ background:#fff7ed; color:#ea580c; }
.icon-green{ background:#f0fdf4; color:#16a34a; }
.icon-purple{ background:#faf5ff; color:#9333ea; }

.stat-info h3{
    font-size:28px;
    color:#0f172a;
    margin-bottom:5px;
}

.stat-info p{
    color:#64748b;
    font-size:14px;
}

/* --------------------------------------
   البطاقات
-------------------------------------- */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:35px;
}

.card{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card i{
    font-size:35px;
    color:#38bdf8;
    margin-bottom:15px;
}

.card h3{
    margin-bottom:10px;
    color:#0f172a;
}

.card p{
    color:gray;
}

.card button{
    margin-top:15px;
    padding:10px 15px;
    border:none;
    border-radius:10px;
    background:#38bdf8;
    color:white;
    cursor:pointer;
    transition:0.3s;
}

.card button:hover{
    background:#0ea5e9;
}

/* --------------------------------------
   الجدول
-------------------------------------- */
.table-container{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.table-container h2{
    margin-bottom:20px;
    color:#0f172a;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    padding:15px;
    text-align:left;
    border-bottom:1px solid #eee;
}

table th{
    background:#38bdf8;
    color:white;
}

.status{
    padding:6px 12px;
    border-radius:8px;
    color:white;
    font-size:13px;
}

.pending{ background:orange; }
.approved{ background:green; }
.rejected{ background:red; }

.empty-state{
    text-align:center;
    padding:40px;
    color:#64748b;
}

/* --------------------------------------
   تصميم متجاوب
-------------------------------------- */
@media(max-width:768px){
    .sidebar{ width:80%; transform:translateX(-100%); }
    .sidebar-trigger:hover ~ .sidebar,
    .sidebar:hover{ transform:translateX(0); }
    .stats-grid{ grid-template-columns:1fr; }
    .topbar{ flex-direction:column; gap:15px; text-align:center; }
}
</style>
</head>

<body>

<!-- ✅ منطقة التحفيز (Trigger) - عند المرور عليها يظهر الشريط -->
<div class="sidebar-trigger"></div>

<!-- ✅ تلميح للمستخدم -->
<div class="hover-hint">
    <i class="fa-solid fa-bars"></i>
    MENU
</div>

<!-- Sidebar -->
<div class="sidebar">
    <h2>FTS System</h2>
    <ul>
        <li>
            <a href="supervisor_dashboard.php" class="active">
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
                <i class="fa-solid fa-clipboard-check"></i> Registration Requests
            </a>
        </li>
        <li>
            <a href="official_letters.php">
                <i class="fa-solid fa-envelope-open-text"></i> Official Letters
            </a>
        </li>
        <li>
            <a href="supervisor_attendance.php">
                <i class="fa-solid fa-calendar-check"></i> Attendance
            </a>
        </li>
        <li>
            <a href="supervisor_reports.php">
                <i class="fa-solid fa-file-lines"></i> Reports
            </a>
        </li>
        <li>
            <a href="supervisor_problems.php">
                <i class="fa-solid fa-circle-exclamation"></i> Student Problems
            </a>
        </li>
        <li>
            <a href="supervisor_evaluations.php">
                <i class="fa-solid fa-star"></i> Final Evaluation
            </a>
        </li>
        <li>
    <a href="student_grades.php">
        <i class="fa-solid fa-graduation-cap"></i> Student Grades
    </a>
</li>
 
<li>
    <a href="supervisor_tasks.php">
        <i class="fa fa-users"></i>
       tasks
    </a>
</li>

<li>
    <a href="training_groups.php">
        <i class="fa-solid fa-users"></i> Training Groups
    </a>
</li>

<li>
    <a href="student_files_review.php">
        <i class="fa-solid fa-folder-open"></i> Student Files Review
    </a>
</li>
<li>
    <a href="supervisor_certificates.php">
        <i class="fa-solid fa-certificate"></i>
        Certificates
    </a>
</li>
<li>
<a href="supervisor_attendance.php">
<i class="fa fa-calendar-check"></i>
Attendance Monitoring
</a>
</li>
 
<li>
<a href="notifications.php">
<i class="fa fa-bell"></i> Notifications
</a>
</li>
        <li>
            <a href="login.php">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">

    <!-- Topbar -->
    <div class="topbar">
        <h1>Supervisor Dashboard</h1>
        <div class="profile">
            <span>Welcome <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <img src="<?php echo $profile_image; ?>" alt="Profile">
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-blue">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $students['total']; ?></h3>
                <p>Total Students</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-orange">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $pending['total']; ?></h3>
                <p>Pending Requests</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-green">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $reports['total']; ?></h3>
                <p>Daily Reports</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-purple">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $requests['total']; ?></h3>
                <p>Total Requests</p>
            </div>
        </div>
    </div>

    <!-- Cards -->
    <div class="cards">
        <div class="card">
            <i class="fa-solid fa-users"></i>
            <h3>Students</h3>
            <p>Monitor and manage student registrations.</p>
        </div>
        <div class="card">
            <i class="fa-solid fa-clipboard-check"></i>
            <h3>Registration Requests</h3>
            <p>Review training registration requests.</p>
            <a href="registration_requests.php" style="text-decoration:none;">
                <button>Open</button>
            </a>
        </div>
        <div class="card">
            <i class="fa-solid fa-file-lines"></i>
            <h3>Daily Reports</h3>
            <p>Review student daily reports.</p>
            <a href="supervisor_reports.php" style="text-decoration:none;">
                <button>Open</button>
            </a>
        </div>
        <div class="card">
    <i class="fa-solid fa-graduation-cap"></i>
    <h3>Student Grades</h3>
    <p>Enter and monitor student grades.</p>

    <a href="student_grades.php"
       style="text-decoration:none;">
        <button>Open</button>
    </a>
</div>
<div class="card">
    <i class="fa-solid fa-certificate"></i>
    <h3>Certificates</h3>
    <p>Manage and issue student certificates.</p>

    <a href="supervisor_certificates.php"
       style="text-decoration:none;">
        <button>Open</button>
    </a>
</div>
    </div>

    <!-- Recent Activities -->
    <div class="table-container">
        <h2>Recent Training Requests</h2>
        
        <?php if(mysqli_num_rows($activities) > 0): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Student Name</th>
                <th>Company</th>
                <th>Status</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($activities)): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                <td>
                    <?php
                    $status_class = 'pending';
                    if($row['status'] == 'Approved') $status_class = 'approved';
                    if($row['status'] == 'Rejected') $status_class = 'rejected';
                    ?>
                    <span class="status <?php echo $status_class; ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-inbox" style="font-size:40px;margin-bottom:10px;"></i>
                <p>No recent activities</p>
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>