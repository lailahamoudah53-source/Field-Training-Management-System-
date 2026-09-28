<?php
session_start();
include "db.php";

// التحقق من تسجيل الدخول
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}


//====للقبول بالمجموعة=============
$user_id = (int)$_SESSION['user_id'];

if($_SESSION['role']=="supervisor"){

    $group_id = (int)$_GET['group_id'];

}else{

    $check_group = mysqli_query($conn,"
    SELECT group_id
    FROM group_members
    WHERE user_id='$user_id'
    LIMIT 1
    ");

    if(mysqli_num_rows($check_group)==0){
?>
<div style="
max-width:600px;
margin:100px auto;
background:white;
padding:30px;
border-radius:15px;
text-align:center;
box-shadow:0 5px 20px rgba(0,0,0,.1);
">

<h2>لم يتم إضافتك إلى أي مجموعة تدريب بعد</h2>

<p>يرجى التواصل مع مشرف التدريب الميداني</p>

<a href="student_dashboard.php"
style="
display:inline-block;
padding:12px 20px;
background:#0f172a;
color:white;
text-decoration:none;
border-radius:10px;
margin-top:15px;
">
العودة إلى لوحة التحكم
</a>

</div>

<?php
exit();
}

    $group_data = mysqli_fetch_assoc($check_group);

    $group_id = $group_data['group_id'];

}
//=========================================================
 

// جلب معلومات المجموعة
$group_result = mysqli_query($conn, "SELECT * FROM `groups` WHERE id='$group_id'");
$group = mysqli_fetch_assoc($group_result);
$group_name = $group['name'] ?? 'Training Group';

// جلب معلومات المستخدم الحالي
$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$current_user = mysqli_fetch_assoc($user_result);
$current_username = $current_user['username'] ?? 'You';
$current_image = !empty($current_user['profile_image']) ? "uploads/" . $current_user['profile_image'] : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";

/* --------------------------------------
   إرسال رسالة جديدة
-------------------------------------- */
if(isset($_POST['send'])){
    $msg = mysqli_real_escape_string($conn, trim($_POST['message']));
    
    if(!empty($msg)){
       mysqli_query($conn,"
INSERT INTO group_messages(group_id,user_id,message,created_at)
VALUES('$group_id','$user_id','$msg',NOW())
");
    }
    
    // إعادة التوجيه لمنع إعادة الإرسال عند التحديث
    header("Location: group_chat.php?group_id=$group_id");
    exit();
}

/* --------------------------------------
   جلب الرسائل (للصفحة الرئيسية)
-------------------------------------- */
$messages = mysqli_query($conn, "
    SELECT gm.*, u.username, u.profile_image
    FROM group_messages gm
    LEFT JOIN users u ON u.id = gm.user_id
    WHERE gm.group_id='$group_id'
    ORDER BY gm.id ASC
");
/* --------------------------------------
   جلب عدد الأعضاء
-------------------------------------- */
$members_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM group_members WHERE group_id='$group_id'"))['c'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Group Chat - <?php echo htmlspecialchars($group_name); ?></title>
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

.sidebar-trigger{
    position:fixed;
    top:0;
    left:0;
    width:15px;
    height:100vh;
    z-index:999;
    cursor:pointer;
}

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

/* تلميح القائمة */
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
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

/* --------------------------------------
   رأس المحادثة
-------------------------------------- */
.chat-header{
    background:white;
    padding:20px 25px;
    border-radius:15px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    margin-bottom:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
}

.chat-header-info{
    display:flex;
    align-items:center;
    gap:15px;
}

.group-icon{
    width:55px;
    height:55px;
    background:linear-gradient(135deg, #38bdf8, #2563eb);
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:white;
    font-size:22px;
}

.chat-header-text h1{
    color:#0f172a;
    font-size:22px;
    margin-bottom:3px;
}

.chat-header-text p{
    color:#64748b;
    font-size:13px;
    display:flex;
    align-items:center;
    gap:6px;
}

.chat-header-text p i{
    color:#16a34a;
}

.chat-header-actions{
    display:flex;
    gap:10px;
}

.header-btn{
    padding:10px 15px;
    border:none;
    border-radius:10px;
    background:#f1f5f9;
    color:#475569;
    cursor:pointer;
    transition:0.3s;
    display:flex;
    align-items:center;
    gap:6px;
    font-size:14px;
    text-decoration:none;
}

.header-btn:hover{
    background:#38bdf8;
    color:white;
}

/* --------------------------------------
   منطقة الرسائل
-------------------------------------- */
.chat-container{
    background:white;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    flex:1;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    min-height:500px;
}

.messages-area{
    flex:1;
    padding:25px;
    overflow-y:auto;
    background:#f8fafc;
    display:flex;
    flex-direction:column;
    gap:15px;
    max-height:calc(100vh - 320px);
}

/* تخصيص شريط التمرير */
.messages-area::-webkit-scrollbar{
    width:6px;
}

.messages-area::-webkit-scrollbar-track{
    background:transparent;
}

.messages-area::-webkit-scrollbar-thumb{
    background:#cbd5e1;
    border-radius:3px;
}

/* فقاعات الرسائل */
.message{
    display:flex;
    gap:10px;
    max-width:70%;
    animation:fadeIn 0.3s ease;
}

@keyframes fadeIn{
    from{ opacity:0; transform:translateY(10px); }
    to{ opacity:1; transform:translateY(0); }
}

.message.sent{
    align-self:flex-end;
    flex-direction:row-reverse;
}

.message.received{
    align-self:flex-start;
}

.message-avatar{
    width:40px;
    height:40px;
    border-radius:50%;
    object-fit:cover;
    flex-shrink:0;
    border:2px solid white;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
}

.message-content{
    display:flex;
    flex-direction:column;
    gap:4px;
}

.message-sender{
    font-size:12px;
    font-weight:600;
    color:#475569;
    padding:0 12px;
}

.message.sent .message-sender{
    text-align:right;
    color:#2563eb;
}

.message-bubble{
    padding:12px 16px;
    border-radius:18px;
    font-size:14px;
    line-height:1.5;
    word-wrap:break-word;
    position:relative;
    box-shadow:0 1px 3px rgba(0,0,0,0.08);
}

.message.received .message-bubble{
    background:white;
    color:#0f172a;
    border-top-left-radius:4px;
}

.message.sent .message-bubble{
    background:linear-gradient(135deg, #38bdf8, #2563eb);
    color:white;
    border-top-right-radius:4px;
}

.message-time{
    font-size:11px;
    color:#94a3b8;
    padding:0 12px;
    display:flex;
    align-items:center;
    gap:4px;
}

.message.sent .message-time{
    justify-content:flex-end;
}

/* رسالة فارغة */
.empty-chat{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    flex:1;
    color:#94a3b8;
    padding:40px;
    text-align:center;
}

.empty-chat i{
    font-size:60px;
    margin-bottom:15px;
    color:#cbd5e1;
}

.empty-chat p{
    font-size:15px;
}

/* --------------------------------------
   نموذج إرسال الرسالة
-------------------------------------- */
.message-input-area{
    padding:20px 25px;
    background:white;
    border-top:1px solid #e2e8f0;
    display:flex;
    gap:12px;
    align-items:center;
}

.message-input-area input{
    flex:1;
    padding:14px 20px;
    border:2px solid #e2e8f0;
    border-radius:25px;
    font-size:14px;
    outline:none;
    transition:0.3s;
    font-family:inherit;
}

.message-input-area input:focus{
    border-color:#38bdf8;
    box-shadow:0 0 0 3px rgba(56,189,248,0.15);
}

.send-btn{
    width:50px;
    height:50px;
    border:none;
    border-radius:50%;
    background:linear-gradient(135deg, #38bdf8, #2563eb);
    color:white;
    cursor:pointer;
    transition:0.3s;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    flex-shrink:0;
}

.send-btn:hover{
    transform:scale(1.05);
    box-shadow:0 5px 15px rgba(56,189,248,0.4);
}

.send-btn:active{
    transform:scale(0.95);
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
    .main-content{
        padding:15px;
    }
    .chat-header{
        flex-direction:column;
        align-items:flex-start;
    }
    .message{
        max-width:85%;
    }
    .messages-area{
        max-height:calc(100vh - 380px);
    }
    .message-input-area{
        padding:15px;
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
            <a href="student_dashboard.php">
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
            <a href="training_activities.php">
                <i class="fa-solid fa-image"></i> Training Activities
            </a>
        </li>
        <li>
            <a href="problems.php">
                <i class="fa-solid fa-triangle-exclamation"></i> Problems
            </a>
        </li>
        <li>
            <a href="certificates.php">
                <i class="fa-solid fa-certificate"></i> Certificates
            </a>
        </li>
        <li>
            <a href="group_chat.php?group_id=<?php echo $group_id; ?>" class="active">
                <i class="fa-solid fa-comments"></i> Group Chat
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

    <!-- رأس المحادثة -->
    <div class="chat-header">
        <div class="chat-header-info">
            <div class="group-icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="chat-header-text">
                <h1><?php echo htmlspecialchars($group_name); ?></h1>
                <p>
                    <i class="fa-solid fa-circle"></i>
                    <?php echo $members_count; ?> member<?php echo $members_count != 1 ? 's' : ''; ?> online
                </p>
            </div>
        </div>
        <div class="chat-header-actions">
           <?php
$back =
($_SESSION['role']=="supervisor")
?
"supervisor_dashboard.php"
:
"student_dashboard.php";
?>

<a href="<?php echo $back; ?>" class="header-btn">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- حاوية المحادثة -->
    <div class="chat-container">
        
        <!-- منطقة الرسائل -->
        <div class="messages-area" id="messagesArea">
            
            <?php if(mysqli_num_rows($messages) > 0): ?>
                
                <?php while($row = mysqli_fetch_assoc($messages)): 
                    // تحديد إذا كانت الرسالة مرسلة من المستخدم الحالي
                    $is_sent = ($row['user_id'] == $user_id);
                    $message_class = $is_sent ? 'sent' : 'received';
                    
                    // جلب اسم المرسل
                   $sender_name = $row['username'] ?? 'Unknown';
                    if($is_sent) $sender_name = 'You';
                    
                    // صورة المرسل
                    $sender_image = !empty($row['profile_image']) ? "uploads/" . $row['profile_image'] : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
                    
                    // وقت الرسالة
                   $sent_time = $row['created_at'];
                    $time_display = !empty($sent_time) ? date('h:i A', strtotime($sent_time)) : '';
                ?>
                    
                    <div class="message <?php echo $message_class; ?>">
                        <img src="<?php echo $sender_image; ?>" alt="Avatar" class="message-avatar">
                        
                        <div class="message-content">
                            <span class="message-sender"><?php echo htmlspecialchars($sender_name); ?></span>
                            <div class="message-bubble">
                                <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                            </div>
                            <?php if(!empty($time_display)): ?>
                                <span class="message-time">
                                    <i class="fa-regular fa-clock"></i>
                                    <?php echo $time_display; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                
                <?php endwhile; ?>
            
            <?php else: ?>
                <div class="empty-chat">
                    <i class="fa-regular fa-comments"></i>
                    <p>No messages yet. Start the conversation!</p>
                </div>
            <?php endif; ?>
            
        </div>

        <!-- نموذج إرسال الرسالة -->
        <form method="POST" class="message-input-area">
            <input type="text" name="message" placeholder="Type your message here..." required autocomplete="off" id="messageInput">
            <button type="submit" name="send" class="send-btn" title="Send Message">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>

    </div>

</div>

<!-- JavaScript للتمرير التلقائي والتحديث -->
<script>
// التمرير التلقائي إلى آخر رسالة
const messagesArea = document.getElementById('messagesArea');
messagesArea.scrollTop = messagesArea.scrollHeight;

 let input = document.getElementById("messageInput");

setInterval(function(){

    if(document.activeElement !== input){
        location.reload();
    }

},5000);
// التركيز على حقل الإدخال عند تحميل الصفحة
document.getElementById('messageInput').focus();
</script>

</body>
</html>