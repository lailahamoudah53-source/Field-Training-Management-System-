<?php
// الاتصال بالسيرفر
$host = "localhost";
//  اسم المستخدم 
$user = "root";
// باسورد فارغ في xampp
$password = "";
// filed tranning system  قاعدة البيانات يلي انشاناها 
$database = "fts_system";

$conn = mysqli_connect($host, $user, $password, $database);// الاتصال بقاعدة البيانات 

if(!$conn){

    die("Connection Failed");
}

?>