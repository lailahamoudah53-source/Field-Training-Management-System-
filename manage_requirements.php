<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

/* حاليا مؤسسة رقم 1 */
$institution_id = 1;

/* إضافة شرط */
if(isset($_POST['add_requirement'])){

    $requirement = mysqli_real_escape_string(
        $conn,
        $_POST['requirement']
    );

    mysqli_query($conn,"
    INSERT INTO institution_requirements
    (institution_id, requirement)
    VALUES
    ('$institution_id','$requirement')
    ");
}

/* حذف شرط */
if(isset($_GET['delete'])){

    $id = (int)$_GET['delete'];

    mysqli_query($conn,"
    DELETE FROM institution_requirements
    WHERE id='$id'
    ");
}

/* تعديل شرط */
if(isset($_POST['update_requirement'])){

    $id = (int)$_POST['id'];

    $requirement = mysqli_real_escape_string(
        $conn,
        $_POST['requirement']
    );

    mysqli_query($conn,"
    UPDATE institution_requirements
    SET requirement='$requirement'
    WHERE id='$id'
    ");
}

/* جلب الشروط */
$requirements = mysqli_query($conn,"
SELECT *
FROM institution_requirements
WHERE institution_id='$institution_id'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Requirements</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    font-family:Poppins,sans-serif;
    background:#f1f5f9;
    padding:30px;
}

.container{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

h2{
    margin-bottom:20px;
}

form{
    margin-bottom:20px;
}

input[type=text]{
    width:70%;
    padding:10px;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    padding:10px 15px;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

.add-btn{
    background:#10b981;
    color:white;
}

.edit-btn{
    background:#f59e0b;
    color:white;
}

.delete-btn{
    background:#ef4444;
    color:white;
    text-decoration:none;
    padding:10px 15px;
    border-radius:8px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:12px;
    border-bottom:1px solid #eee;
}

th{
    background:#10b981;
    color:white;
}

.back{
    display:inline-block;
    margin-bottom:15px;
    text-decoration:none;
    background:#0f172a;
    color:white;
    padding:10px 15px;
    border-radius:8px;
}

</style>
</head>

<body>

<div class="container">

<a href="institution_dashboard.php" class="back">
<i class="fa fa-arrow-left"></i> Back
</a>

<h2>
<i class="fa fa-list-check"></i>
Manage Institution Requirements
</h2>

<form method="POST">

    <input
    type="text"
    name="requirement"
    placeholder="Enter new requirement"
    required>

    <button
    type="submit"
    name="add_requirement"
    class="add-btn">

    Add Requirement

    </button>

</form>

<table>

<tr>
    <th>ID</th>
    <th>Requirement</th>
    <th>Actions</th>
</tr>

<?php while($row=mysqli_fetch_assoc($requirements)){ ?>

<tr>

<td>
<?php echo $row['id']; ?>
</td>

<td>

<form method="POST">

<input
type="hidden"
name="id"
value="<?php echo $row['id']; ?>">

<input
type="text"
name="requirement"
value="<?php echo htmlspecialchars($row['requirement']); ?>">

<button
type="submit"
name="update_requirement"
class="edit-btn">

Update

</button>

</form>

</td>

<td>

<a
class="delete-btn"
href="?delete=<?php echo $row['id']; ?>"
onclick="return confirm('Delete requirement?')">

Delete

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>