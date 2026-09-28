<?php
session_start();

// تغيير اللغة
if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'];
}

// اللغة الافتراضية
if(!isset($_SESSION['lang'])){
    $_SESSION['lang'] = "en";
}

include "db.php";

// التحقق من تسجيل الدخول
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// جلب بيانات الطالب الحالي
$id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM students WHERE id=$id");
$user = mysqli_fetch_assoc($result);

// تحديد الصورة
$profile_image = !empty($user['profile_image']) ? "uploads/" . $user['profile_image'] : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
            transform:translateX(-260px);
            transition:transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow:5px 0 25px rgba(0,0,0,0.2);
            overflow-y:auto;
        }

        /* منطقة التحفيز */
        .sidebar-trigger{
            position:fixed;
            top:0;
            left:0;
            width:15px;
            height:100vh;
            z-index:999;
            cursor:pointer;
        }

        /* ظهور الشريط عند التمرير */
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
           تلميح القائمة
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
           المحتوى الرئيسي
        -------------------------------------- */
        .main-content{
            width:100%;
            padding:30px;
            margin-left:0;
            transition:margin-left 0.4s ease;
        }

        /* --------------------------------------
           شريط التنقل العلوي
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
            box-shadow:0 10px 30px rgba(0,0,0,0.12);
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
            margin-bottom:15px;
        }

        .card button{
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

        .completed{
            background:green;
        }

        .pending{
            background:orange;
        }

        /* --------------------------------------
           تصميم متجاوب
        -------------------------------------- */
        @media(max-width:768px){
            .sidebar{
                width:80%;
                transform:translateX(-100%);
            }
            .sidebar-trigger:hover ~ .sidebar,
            .sidebar:hover{
                transform:translateX(0);
            }
            .cards{
                grid-template-columns:1fr;
            }
            .topbar{
                flex-direction:column;
                gap:15px;
                text-align:center;
            }
        }
    </style>
</head>

<body>

    <!-- منطقة التحفيز -->
    <div class="sidebar-trigger"></div>

    <!-- تلميح القائمة -->
    <div class="hover-hint">
        <i class="fa-solid fa-bars"></i>
        MENU
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>FTS System</h2>
        <ul>
            <li>
                <a href="student_dashboard.php" class="active">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="profile.php">
                    <i class="fa-solid fa-user"></i> Profile
                </a>
            </li>
            <li>
                <a href="training_registration.php">
                    <i class="fa-solid fa-clipboard"></i> Training Registration
                </a>
            </li>
            <li>
                <a href="daily_reports.php">
                    <i class="fa-solid fa-file"></i> Daily Reports
                </a>
            </li>
                <li>
                <a href="student_tasks.php">
                    <i class="fa-solid fa-file"></i> tasks
                </a>
            </li>
            
            <li>
                <a href="training_activities.php">
                    <i class="fa-solid fa-image"></i> Training Activities
                </a>
            </li>
            <li>
<a href="student_attendance.php">
<i class="fa fa-calendar-check"></i>
Attendance
</a>
</li>
            <li>
                <a href="problems.php">
                    <i class="fa-solid fa-triangle-exclamation"></i> Problems
                </a>
            </li>
            <li>
                <a href="student_certificates.php">
                    <i class="fa-solid fa-certificate"></i> My Certificates
                </a>
            </li>
            <li>
                <a href="pledge.php">
                    <i class="fa-solid fa-file-signature"></i> Training Pledge
                </a>
            </li>
            <li>
               <a href="group_chat.php">
                    <i class="fa-solid fa-comments"></i> Training Group Chat
                </a>
            </li>
            <li>
    <a href="my_uploads.php">
        <i class="fa-solid fa-folder-open"></i> My Uploads Status
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
            <h1>Student Dashboard</h1>
            <div class="profile">
                <span>Welcome <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                <img src="<?php echo $profile_image; ?>" alt="Profile">
            </div>
        </div>

        <!-- Cards -->
        <div class="cards">

            <div class="card">
                <i class="fa-solid fa-file-circle-check"></i>
                <h3>12 Reports</h3>
                <p>Submitted Reports</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-clock"></i>
                <h3>5 Pending</h3>
                <p>Pending Tasks</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-chart-pie"></i>
                <h3>80%</h3>
                <p>Training Progress</p>
            </div>

            <div class="card">
                <i class="fa-solid fa-file"></i>
                <h3>Daily Reports</h3>
                <p>Add your daily follow-up reports and training activities.</p>
                <a href="daily_reports.php" style="text-decoration:none;">
                    <button>Open</button>
                </a>
            </div>

            <div class="card">
                <i class="fa-solid fa-image"></i>
                <h3>Training Activities</h3>
                <p>Upload daily training activities, signatures and images.</p>
                <a href="training_activities.php" style="text-decoration:none;">
                    <button>Open</button>
                </a>
            </div>

            <div class="card">
                <i class="fa-solid fa-star"></i>
                <h3>Excellent</h3>
                <p>Performance Rate</p>
            </div>

        </div>

        <!-- Table -->
        <div class="table-container">
            <h2>Recent Activities</h2>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Activity</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>2026-05-20</td>
                    <td>Daily Report Submitted</td>
                    <td>
                        <span class="status completed">Completed</span>
                    </td>
                </tr>
                <tr>
                    <td>2026-05-21</td>
                    <td>Training Registration</td>
                    <td>
                        <span class="status pending">Pending</span>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>