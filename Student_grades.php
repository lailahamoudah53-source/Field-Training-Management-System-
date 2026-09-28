<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['save_grade'])){

    $student_id = $_POST['student_id'];
    $grade = $_POST['grade'];
    $notes = $_POST['notes'];

    $check = mysqli_query($conn,
    "SELECT * FROM student_grades WHERE student_id='$student_id'");

    if(mysqli_num_rows($check)>0){

        mysqli_query($conn,"
        UPDATE student_grades
        SET grade='$grade',
            notes='$notes'
        WHERE student_id='$student_id'
        ");

    }else{

        mysqli_query($conn,"
        INSERT INTO student_grades(student_id,grade,notes)
        VALUES('$student_id','$grade','$notes')
        ");
    }
}

$students = mysqli_query($conn,"
SELECT students.*,
student_grades.grade,
student_grades.notes
FROM students
LEFT JOIN student_grades
ON students.id = student_grades.student_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Grades</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    background:#f1f5f9;
    font-family:'Poppins',sans-serif;
    padding:30px;
}

.table-container{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/**زر لرجوع ال الداشبورد  */
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
/**==================================================== */
.table-container h2{
    margin-bottom:20px;
    color:#0f172a;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    padding:15px;
    border-bottom:1px solid #eee;
    text-align:left;
}

table th{
    background:#38bdf8;
    color:white;
}

input[type="number"],
input[type="text"]{
    width:100%;
    padding:10px;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    background:#38bdf8;
    color:white;
    border:none;
    padding:10px 18px;
    border-radius:10px;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#0ea5e9;
}

</style>

</head>

<body>

<div class="table-container">
<a href="supervisor_dashboard.php" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
    Back to Dashboard
</a>

<h2>
<i class="fa-solid fa-graduation-cap"></i>
Student Grades
</h2>

<table>

<tr>
    <th>Student Name</th>
    <th>Grade</th>
    <th>Notes</th>
    <th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($students)): ?>

<form method="POST">

<tr>

<td>
<?php echo htmlspecialchars($row['full_name']); ?>

<input
type="hidden"
name="student_id"
value="<?php echo $row['id']; ?>">
</td>

<td>

<input
type="number"
name="grade"
min="0"
max="100"
value="<?php echo $row['grade']; ?>"
required>

</td>

<td>

<input
type="text"
name="notes"
value="<?php echo $row['notes']; ?>">

</td>

<td>

<button
type="submit"
name="save_grade">

Save

</button>

</td>

</tr>

</form>

<?php endwhile; ?>

</table>

</div>

</body>
</html>