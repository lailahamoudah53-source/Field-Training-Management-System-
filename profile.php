<?php

// بدء الجلسة
session_start();

// ربط ملف الاتصال بقاعدة البيانات
include "db.php";

// --------------------------------------
// التحقق إذا كان المستخدم مسجل دخول
// --------------------------------------
if(!isset($_SESSION['user_id'])){

    // تحويل المستخدم إلى صفحة تسجيل الدخول
    header("Location: login.php");

    exit();
}

// --------------------------------------
// جلب ID المستخدم الحالي من Session
// --------------------------------------
$id = $_SESSION['user_id'];

// --------------------------------------
// جلب بيانات الطالب من قاعدة البيانات
// --------------------------------------
$result = mysqli_query($conn, "SELECT * FROM students WHERE id=$id");

// تحويل البيانات إلى مصفوفة
$user = mysqli_fetch_assoc($result);

// --------------------------------------
// عند الضغط على زر الحفظ
// --------------------------------------
if(isset($_POST['update'])){

    // أخذ البيانات من الفورم
    $name = $_POST['full_name'];

    $email = $_POST['email'];

    $phone = $_POST['phone'];

    $gender = $_POST['gender'];

    $address = $_POST['address'];

    $password = $_POST['password'];

    // --------------------------------------
    // رفع صورة جديدة إذا اختار المستخدم صورة
    // --------------------------------------
    if(!empty($_FILES['profile_image']['name'])){

        // إنشاء اسم جديد للصورة
        $imageName = time() . "_" . $_FILES['profile_image']['name'];

        // المسار المؤقت للصورة
        $tmp = $_FILES['profile_image']['tmp_name'];

        // نقل الصورة إلى مجلد uploads
        move_uploaded_file($tmp, "uploads/" . $imageName);

        // تحديث الصورة داخل قاعدة البيانات
        mysqli_query($conn, "
        
            UPDATE students 
            SET profile_image='$imageName'
            WHERE id=$id
        
        ");
    }

    // --------------------------------------
    // تحديث بيانات الطالب
    // --------------------------------------
    mysqli_query($conn, "
    
        UPDATE students SET
        
        full_name='$name',
        email='$email',
        phone='$phone',
        gender='$gender',
        address='$address',
        password='$password'
        
        WHERE id=$id
    
    ");
    //ضفنا لقاعدة البيانات علشان اي اسم احطه يتغير مباشرة  فيها ويظهر داخل رسالة الترحيب 
    // تحديث الاسم داخل Session
$_SESSION['name'] = $name;

    // --------------------------------------
    // إعادة تحميل الصفحة بعد الحفظ
    // --------------------------------------
    header("Location: profile.php");

    exit();
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Profile</title>

<!-- Google Font -->

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* --------------------------------------
تنسيقات عامة للصفحة
-------------------------------------- */

*{

    margin:0;

    padding:0;

    box-sizing:border-box;

    font-family:'Poppins', sans-serif;
}

body{

    background:#f1f5f9;

    display:flex;
}

/* --------------------------------------
القائمة الجانبية
-------------------------------------- */

.sidebar{

    width:260px;

    height:100vh;

    background:#0f172a;

    padding:30px 20px;

    position:fixed;
}

.sidebar h2{

    color:#38bdf8;

    text-align:center;

    margin-bottom:40px;
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
}

.sidebar ul li a:hover{

    background:#1e293b;
}

/* --------------------------------------
المحتوى الرئيسي
-------------------------------------- */

.main-content{

    margin-left:260px;

    width:100%;

    padding:35px;
}

/* --------------------------------------
عنوان الصفحة
-------------------------------------- */

.topbar{

    margin-bottom:30px;
}

.topbar h1{

    font-size:38px;

    color:#0f172a;
}

/* --------------------------------------
صندوق البروفايل
-------------------------------------- */

.profile-container{

    background:white;

    border-radius:25px;

    padding:40px;

    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* --------------------------------------
صورة البروفايل
-------------------------------------- */

.profile-image{

    text-align:center;

    margin-bottom:35px;
}

.profile-image img{

    width:140px;

    height:140px;

    border-radius:50%;

    object-fit:cover;

    border:5px solid #38bdf8;
}

/* --------------------------------------
تنسيق الحقول
-------------------------------------- */

.form-grid{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:25px;
}

.input-group{

    display:flex;

    flex-direction:column;
}

.input-group label{

    margin-bottom:8px;

    font-weight:500;

    color:#0f172a;
}

.input-group input,
.input-group select{

    padding:15px;

    border:1px solid #ddd;

    border-radius:12px;

    outline:none;

    font-size:15px;

    transition:0.3s;
}

/* --------------------------------------
تأثير عند الضغط على الحقول
-------------------------------------- */

.input-group input:focus,
.input-group select:focus{

    border-color:#38bdf8;

    box-shadow:0 0 8px rgba(56,189,248,0.4);
}

/* --------------------------------------
حقل بعرض كامل
-------------------------------------- */

.full-width{

    grid-column:1 / 3;
}

/* --------------------------------------
زر الحفظ
-------------------------------------- */

.save-btn{

    width:100%;

    margin-top:30px;

    padding:16px;

    border:none;

    border-radius:14px;

    background:linear-gradient(to right,#38bdf8,#2563eb);

    color:white;

    font-size:17px;

    font-weight:600;

    cursor:pointer;

    transition:0.3s;
}

.save-btn:hover{

    transform:translateY(-3px);
}

/* --------------------------------------
تصميم متجاوب للهاتف
-------------------------------------- */

@media(max-width:768px){

    .form-grid{

        grid-template-columns:1fr;
    }

    .full-width{

        grid-column:1;
    }
}

</style>

</head>

<body>

<!-- --------------------------------------
القائمة الجانبية
-------------------------------------- -->

<div class="sidebar">

    <h2>FTS System</h2>

    <ul>

        <li>
            <a href="student_dashboard.php">
                <i class="fa-solid fa-house"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="profile.php">
                <i class="fa-solid fa-user"></i>
                Profile
            </a>
        </li>

        <li>
            <a href="login.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>
        </li>

    </ul>

</div>

<!-- --------------------------------------
المحتوى الرئيسي
-------------------------------------- -->

<div class="main-content">

    <!-- عنوان الصفحة -->

    <div class="topbar">

        <h1>Student Profile</h1>

    </div>

    <!-- صندوق البروفايل -->

    <div class="profile-container">

        <!-- فورم تعديل البيانات -->

        <form method="POST" enctype="multipart/form-data">

            <!-- صورة البروفايل -->

            <div class="profile-image">

                <!-- إذا لم توجد صورة يتم عرض صورة افتراضية -->

                <?php
                
                if(!empty($user['profile_image'])){
                
                    echo "<img src='uploads/".$user['profile_image']."'>";
                
                }else{
                
                    echo "<img src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png'>";
                }
                
                ?>

                <br><br>

                <!-- رفع صورة جديدة -->

                <input type="file" name="profile_image">

            </div>

            <!-- الحقول -->

            <div class="form-grid">

                <!-- الاسم -->

                <div class="input-group">

                    <label>Full Name</label>

                    <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>">

                </div>

                <!-- الإيميل -->

                <div class="input-group">

                    <label>Email Address</label>

                    <input type="email" name="email" value="<?php echo $user['email']; ?>">

                </div>

                <!-- الهاتف -->

                <div class="input-group">

                    <label>Phone Number</label>

                    <input type="text" name="phone" value="<?php echo $user['phone']; ?>">

                </div>

                <!-- الجنس -->

                <div class="input-group">

                    <label>Gender</label>

                    <select name="gender">

                        <option value="Male">Male</option>

                        <option value="Female">Female</option>

                    </select>

                </div>

                <!-- العنوان -->

                <div class="input-group full-width">

                    <label>Current Address</label>

                    <input type="text" name="address" value="<?php echo $user['address']; ?>">

                </div>

                <!-- كلمة المرور -->

                <div class="input-group full-width">

                    <label>Password</label>

                    <input type="text" name="password" value="<?php echo $user['password']; ?>">

                </div>

            </div>

            <!-- زر الحفظ -->

            <button type="submit" name="update" class="save-btn">

                Save Changes

            </button>

        </form>

    </div>

</div>

</body>

</html>