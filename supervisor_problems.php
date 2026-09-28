<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* --------------------------------------
   Reply to Problem - Using Prepared Statement
-------------------------------------- */
if(isset($_POST['reply'])){
    $id = (int)$_POST['id'];
    $reply_text = trim($_POST['reply_text']);
    
    // Validate input
    if(empty($reply_text)){
        $error_message = "Reply text cannot be empty!";
    } else {
        // Use prepared statement for security
        $stmt = mysqli_prepare($conn, "UPDATE problems SET reply=?, supervisor_status='sent' WHERE id=?");
        
        if($stmt){
            mysqli_stmt_bind_param($stmt, "si", $reply_text, $id);
            $result = mysqli_stmt_execute($stmt);
            
            if($result && mysqli_stmt_affected_rows($stmt) > 0){
                mysqli_stmt_close($stmt);
                header("Location: supervisor_problems.php?success=1");
                exit();
            } else {
                $error_message = "Failed to update reply. Please try again.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Database error: " . mysqli_error($conn);
        }
    }
}

/* --------------------------------------
   Fetch All Problems with Error Handling
-------------------------------------- */
$problems_query = "SELECT * FROM problems ORDER BY id DESC";
$problems_result = mysqli_query($conn, $problems_query);

if(!$problems_result){
    die("Error fetching problems: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Problems</title>
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
/* --------------------------------------
   General Styles
-------------------------------------- */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', 'Cairo', sans-serif;
}

body{
    background:#f4f7fc;
    display:flex;
    min-height:100vh;
}

/* --------------------------------------
   Sidebar
-------------------------------------- */
.sidebar{
    width:250px;
    height:100vh;
    background:#0f172a;
    padding:25px;
    position:fixed;
    left: 0;
    right: auto;
    overflow-y: auto;
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
   Main Content
-------------------------------------- */
.main{
    margin-left:250px;
    margin-right: 0;
    padding:40px;
    width:calc(100% - 250px);
    min-height:100vh;
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
   Alert Messages
-------------------------------------- */
.alert{
    padding:15px 20px;
    border-radius:10px;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:500;
}

.alert-success{
    background:#f0fdf4;
    color:#16a34a;
    border:1px solid #dcfce7;
}

.alert-error{
    background:#fef2f2;
    color:#dc2626;
    border:1px solid #fecaca;
}

/* --------------------------------------
   Problem Card
-------------------------------------- */
.problem-card{
    background:white;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    margin-bottom:25px;
    overflow:hidden;
    border-left:5px solid #38bdf8;
    border-right: none;
    transition:.3s;
}

.problem-card:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 25px rgba(0,0,0,.12);
}

.problem-card.answered{
    border-left-color:#16a34a;
}

.card-header{
    padding:20px 25px;
    border-bottom:1px solid #f1f5f9;
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
}

.problem-title{
    font-size:18px;
    font-weight:600;
    color:#0f172a;
    display:flex;
    align-items:center;
    gap:10px;
}

.problem-title i{ color:#38bdf8; }

.status-badge{
    padding:6px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.badge-pending{
    background:#fff7ed;
    color:#ea580c;
    border:1px solid #ffedd5;
}

.badge-answered{
    background:#f0fdf4;
    color:#16a34a;
    border:1px solid #dcfce7;
}

.card-body{
    padding:25px;
}

.message-box{
    background:#f8fafc;
    padding:20px;
    border-radius:10px;
    color:#334155;
    line-height:1.7;
    margin-bottom:20px;
    border-left:3px solid #cbd5e1;
    border-right: none;
}

.message-label{
    font-weight:600;
    color:#0f172a;
    margin-bottom:8px;
    display:flex;
    align-items:center;
    gap:8px;
    font-size:14px;
}

.message-label i{ color:#64748b; }

/* --------------------------------------
   Reply Section
-------------------------------------- */
.reply-section{
    background:#f0f9ff;
    padding:20px;
    border-radius:10px;
    border:1px solid #bae6fd;
}

.reply-display{
    margin-bottom:15px;
}

.reply-text{
    color:#0369a1;
    line-height:1.6;
    font-size:15px;
    padding:15px;
    background:white;
    border-radius:8px;
    border-left:3px solid #38bdf8;
    border-right: none;
}

.reply-form{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.reply-form textarea{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    font-size:14px;
    font-family:inherit;
    resize:vertical;
    min-height:80px;
    outline:none;
    transition:.3s;
}

.reply-form textarea:focus{
    border-color:#38bdf8;
    box-shadow:0 0 0 3px rgba(56,189,248,.15);
}

.reply-btn{
    align-self:flex-end;
    padding:10px 20px;
    border:none;
    border-radius:8px;
    background:linear-gradient(to right, #38bdf8, #2563eb);
    color:white;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
    display:flex;
    align-items:center;
    gap:8px;
}

.reply-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 4px 12px rgba(56,189,248,.4);
}

/* --------------------------------------
   Empty State
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
   Responsive Design
-------------------------------------- */
@media(max-width:768px){
    .sidebar{ width:100%; height:auto; position:relative; }
    .main{ margin-left:0; padding:20px; width:100%; }
    .card-header{ flex-direction:column; align-items:flex-start; }
    .reply-btn{ width:100%; justify-content:center; }
}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>FTS System</h2>
    <ul>
        <li><a href="supervisor_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li><a href="supervisor_profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
        <li><a href="registration_requests.php"><i class="fa-solid fa-clipboard-list"></i> Registration Requests</a></li>
        <li><a href="official_letters.php"><i class="fa-solid fa-envelope-open-text"></i> Official Letters</a></li>
        <li><a href="supervisor_attendance.php"><i class="fa-solid fa-calendar-check"></i> Attendance</a></li>
        <li><a href="supervisor_reports.php"><i class="fa-solid fa-file-lines"></i> Reports</a></li>
        <li><a href="supervisor_problems.php" class="active"><i class="fa-solid fa-circle-exclamation"></i> Student Problems</a></li>
        <li><a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main">

    <h1 class="page-title">
        <i class="fa-solid fa-circle-exclamation"></i>
        Student Problems
    </h1>

    <!-- Success/Error Messages -->
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            Reply sent successfully!
        </div>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if(mysqli_num_rows($problems_result) > 0): ?>
        
        <?php while($row = mysqli_fetch_assoc($problems_result)){ 
            
            $problem_id = $row['id'] ?? 0;
            $title = $row['title'] ?? 'Untitled Problem';
            $status = $row['status'] ?? 'Pending';
            
            $message = $row['description'] ?? $row['message'] ?? 'No details.';
            $reply = $row['reply'] ?? '';
            
            $is_answered = ($status == 'Answered');
            $card_class = $is_answered ? 'answered' : '';
            $badge_class = $is_answered ? 'badge-answered' : 'badge-pending';
            $badge_icon = $is_answered ? 'fa-circle-check' : 'fa-clock';
            $status_text = $is_answered ? 'Answered' : 'Pending';
        ?>
        
        <div class="problem-card <?php echo $card_class; ?>">
            
            <!-- Card Header -->
            <div class="card-header">
                <div class="problem-title">
                    <i class="fa-solid fa-tag"></i>
                    <?php echo htmlspecialchars($title); ?>
                </div>
                <span class="status-badge <?php echo $badge_class; ?>">
                    <i class="fa-solid <?php echo $badge_icon; ?>"></i>
                    <?php echo $status_text; ?>
                </span>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                
                <!-- Student Message -->
                <div class="message-box">
                    <div class="message-label">
                        <i class="fa-solid fa-comment-dots"></i> Student Message:
                    </div>
                    <?php echo nl2br(htmlspecialchars($message)); ?>
                </div>

                <!-- Reply Section -->
                <div class="reply-section">
                    <?php if($is_answered && !empty($reply)): ?>
                        <div class="reply-display">
                            <div class="message-label" style="color:#0369a1;">
                                <i class="fa-solid fa-reply"></i> Your Previous Reply:
                            </div>
                            <div class="reply-text">
                                <?php echo nl2br(htmlspecialchars($reply)); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Reply Form -->
                    <form method="POST" class="reply-form">
                        <input type="hidden" name="id" value="<?php echo (int)$problem_id; ?>">
                        <textarea name="reply_text" placeholder="Type your reply to the student here..." required><?php echo htmlspecialchars($reply); ?></textarea>
                        <button type="submit" name="reply" class="reply-btn">
                            <i class="fa-solid fa-paper-plane"></i> 
                            <?php echo $is_answered ? 'Update Reply' : 'Send Reply'; ?>
                        </button>
                    </form>
                </div>

            </div>
        </div>

        <?php } ?>

    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fa-regular fa-face-smile"></i>
            <p>No problems reported by students. Everything is running smoothly!</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>