<?php

session_start();

include "db.php";

$message = "";

if(isset($_POST['login'])){

    $username = $_POST['username'] ?? '';

    $password = $_POST['password'] ?? '';

    if(empty($username) || empty($password)){

        $message = "Please fill all fields!";

    }else{

        $query = "
        SELECT *
        FROM users
        WHERE username='$username'
        AND password='$password'
        ";

        $result = mysqli_query($conn,$query);

        $user = mysqli_fetch_assoc($result);

        if($user){

            $_SESSION['user_id'] = $user['id'];

            $_SESSION['name'] = $user['username'];

            $_SESSION['role'] = $user['role'];

            if($user['role'] == "student"){

                header("Location: student_dashboard.php");
                exit();
            }

            elseif($user['role'] == "supervisor"){

                header("Location: supervisor_dashboard.php");
                exit();
            }

         elseif($user['role'] == "institution"){

    $_SESSION['company_name'] = $user['username'];

    header("Location: institution_dashboard.php");
    exit();

}
            elseif($user['role'] == "Admissions and Registration Department"){

                header("Location: admissions_dashboard.php");
                exit();
            }

        }else{

            $message = "Invalid username or password!";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Field Training Login</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

option{

    color:black;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:
    linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
    url('https://plus.unsplash.com/premium_photo-1681681082165-fd333bbc037a?q=80&w=1170');
    background-size:cover;
    background-position:center;
}

/* Container */
.login-container{
    width:430px;
    padding:45px;
    border-radius:25px;
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(18px);
    box-shadow:0 10px 40px rgba(0,0,0,0.4);
    color:white;
    transition:0.3s;
}

/* Logo */
.logo{
    text-align:center;
    margin-bottom:25px;
}

.logo i{
    font-size:55px;
    color:#00c8ff;
    margin-bottom:10px;
}

.logo h1{
    font-size:38px;
}

.logo p{
    font-size:14px;
    color:#ddd;
}

/* Message */
.message{
    background:rgba(255,0,0,0.15);
    border:1px solid rgba(255,0,0,0.3);
    padding:12px;
    border-radius:10px;
    text-align:center;
    margin-bottom:20px;
}

/* Input */
.input-group{
    margin-bottom:18px;
}

.input-group label{
    display:block;
    margin-bottom:8px;
    font-size:14px;
    font-weight:500;
}

.input-box{
    position:relative;
}

.input-box i{
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    color:#00c8ff;
}

.input-box input,
.input-box select{
    width:100%;
    padding:14px 15px 14px 45px;
    border:none;
    border-radius:12px;
    background:rgba(255,255,255,0.18);
    color:white;
    outline:none;
    font-size:14px;
    transition:0.3s;
}

.input-box input:focus,
.input-box select:focus{
    background:rgba(255,255,255,0.25);
}

/* Button */
.login-btn{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(to right,#00c8ff,#0072ff);
    color:white;
    font-size:17px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}
.input-box input::placeholder{

    color:#ddd;
}


.login-btn:hover{
    transform:translateY(-2px);
    opacity:0.9;
}

/* Footer */
.footer{
    text-align:center;
    margin-top:20px;
    font-size:13px;
    color:#ccc;
}

</style>

</head>

<body>

<div class="login-container">

    <!-- Logo -->
    <div class="logo">
        <i class="fa-solid fa-graduation-cap"></i>
        <h1>Field Training</h1>
        <p>Smart Management System</p>
    </div>

    <!-- Message -->
    <?php if(!empty($message)) { ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <!-- Form -->
    <form method="POST">

        <div class="input-group">
            <label> Username</label>
            <div class="input-box">
                <i class="fa-solid fa-envelope"></i>
               <input type="text" name="username" placeholder="Enter your name" required>
            </div>
        </div>

        <div class="input-group">
           <label>Password</label>
            <div class="input-box">
                <i class="fa-solid fa-lock"></i>
               <input type="password" name="password" placeholder="Enter your Password" required>
            </div>
        </div>

   

        <button class="login-btn" type="submit" name="login">
            Login
        </button>

    </form>

    <div class="footer">
        © 2026 Field Training System
    </div>

</div>

</body>
</html>