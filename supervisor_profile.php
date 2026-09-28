```php
<?php

session_start();

include "db.php";

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];

$result = mysqli_query($conn,"
SELECT *
FROM users
WHERE id='$id'
");

$user = mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

    $username = $_POST['username'];

    $password = $_POST['password'];

    mysqli_query($conn,"
    UPDATE users
    SET
    username='$username',
    password='$password'
    WHERE id='$id'
    ");

    $_SESSION['name'] = $username;

    header("Location: supervisor_profile.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Supervisor Profile</title>

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

.main-content{
margin-left:250px;
width:100%;
padding:40px;
}

.profile-card{
background:white;
padding:40px;
border-radius:20px;
box-shadow:0 5px 20px rgba(0,0,0,.1);
max-width:700px;
margin:auto;
}

.profile-image{
text-align:center;
margin-bottom:25px;
}

.profile-image img{
width:130px;
height:130px;
border-radius:50%;
border:5px solid #38bdf8;
}

h1{
margin-bottom:25px;
color:#0f172a;
}

.form-group{
margin-bottom:20px;
}

.form-group label{
display:block;
margin-bottom:8px;
font-weight:bold;
}

.form-group input{
width:100%;
padding:12px;
border:1px solid #ddd;
border-radius:10px;
}

.save-btn{
width:100%;
padding:15px;
border:none;
background:#38bdf8;
color:white;
font-size:16px;
border-radius:10px;
cursor:pointer;
}

.save-btn:hover{
background:#0ea5e9;
}

</style>

</head>

<body>

<div class="sidebar">

<h2>FTS System</h2>

<ul>

<li>
<a href="supervisor_dashboard.php">
<i class="fa-solid fa-house"></i>
 Dashboard
</a>
</li>

<li>
<a href="supervisor_profile.php">
<i class="fa-solid fa-user"></i>
 Profile
</a>
</li>

<li>
<a href="registration_requests.php">
<i class="fa-solid fa-clipboard-check"></i>
 Registration Requests
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

<div class="main-content">

<h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

<div class="profile-card">

<form method="POST">

<div class="profile-image">

<img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">

</div>

<div class="form-group">

<label>Username</label>

<input
type="text"
name="username"
value="<?php echo $user['username']; ?>">

</div>

<div class="form-group">

<label>Role</label>

<input
type="text"
value="<?php echo $user['role']; ?>"
readonly>

</div>

<div class="form-group">

<label>Password</label>

<input
type="text"
name="password"
value="<?php echo $user['password']; ?>">

</div>

<button
type="submit"
name="update"
class="save-btn">

Save Changes

</button>

</form>

</div>

</div>

</body>

</html>
```
