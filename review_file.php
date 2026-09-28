<?php
include "db.php";

if(isset($_GET['id']) && isset($_GET['status'])){

    $id = $_GET['id'];
    $status = $_GET['status'];

    mysqli_query($conn, "
        UPDATE student_uploads
        SET status='$status'
        WHERE id='$id'
    ");
}

header("Location: student_files_review.php");
exit();