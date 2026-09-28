<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
if(isset($_POST['upload'])){

    $file_type = $_POST['file_type'];

    $file_name = $_FILES['file']['name'];
    $tmp_name  = $_FILES['file']['tmp_name'];

    $new_name = time().'_'.$file_name;

    $folder = "uploads/".$new_name;

    if(move_uploaded_file($tmp_name,$folder)){

        mysqli_query($conn,"
        INSERT INTO student_uploads
        (student_id,file_type,file_path,status)
        VALUES
        ('$id','$file_type','$folder','Pending')
        ");

        header("Location: my_uploads.php");
        exit();
    }
}

$files = mysqli_query($conn, "
    SELECT *
    FROM student_uploads
    WHERE student_id='$id'
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Uploads</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
body{
    font-family:Poppins;
    background:#f1f5f9;
    padding:30px;
}

.container{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:15px;
    border-bottom:1px solid #eee;
}
/** خاص بزر العودة لداشبورد */
.back-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:12px 18px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    border-radius:10px;
    transition:0.3s;
}

.back-btn:hover{
    background:#1e293b;
}
/**============================================== */
th{
    background:#38bdf8;
    color:white;
}

.status{
    padding:6px 10px;
    border-radius:8px;
    color:white;
    font-size:13px;
}

.pending{background:orange;}
.approved{background:green;}
.rejected{background:red;}

</style>
</head>

<body>

<div class="container">
    <a href="student_dashboard.php" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
    Back to Dashboard
</a>

<h2><i class="fa-solid fa-folder-open"></i> My Uploaded Files Status</h2>
<form method="POST" enctype="multipart/form-data" style="margin-bottom:25px;">

    <select name="file_type" required>
        <option value="">Choose Type</option>
        <option value="Daily Signature">Daily Signature</option>
        <option value="Training Activity">Training Activity</option>
        <option value="Certificate">Certificate</option>
    </select>

    <input type="file" name="file" required>

    <button type="submit" name="upload">
        Upload File
    </button>

</form>


<table>
<tr>
    <th>File</th>
    <th>Type</th>
    <th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($files)): ?>
<tr>
    <td>
        <a href="<?php echo $row['file_path']; ?>" target="_blank">
            View File
        </a>
    </td>

    <td><?php echo $row['file_type']; ?></td>

    <td>
        <span class="status <?php echo strtolower($row['status']); ?>">
            <?php echo $row['status']; ?>
        </span>
    </td>
</tr>
<?php endwhile; ?>

</table>

</div>

</body>
</html>