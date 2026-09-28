<?php

include "db.php";

$id = $_GET['id'];

mysqli_query($conn,"
UPDATE training_requests
SET status='Approved'
WHERE id=$id
");

header("Location: view_requests.php");

?>