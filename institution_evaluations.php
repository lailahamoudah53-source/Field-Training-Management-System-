<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// جلب تقييمات المؤسسة للطلاب
$evaluations = mysqli_query($conn, "
    SELECT ie.*, s.full_name
    FROM institution_evaluations ie
    JOIN students s ON s.id = ie.student_id
    ORDER BY ie.id DESC
");
// شروط الالتحاق

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Institution Evaluations</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
body{
    font-family:'Poppins', sans-serif;
    background:#f1f5f9;
    margin:0;
    padding:30px;
}

.container{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

h2{
    margin-bottom:20px;
    color:#0f172a;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:15px;
    border-bottom:1px solid #eee;
    text-align:left;
}

th{
    background:#38bdf8;
    color:white;
}
/**زر للعودة لداشبورد */
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
/**=========================================== */

.badge{
    padding:6px 10px;
    border-radius:8px;
    color:white;
    font-size:13px;
}

.good{ background:green; }
.medium{ background:orange; }
.poor{ background:red; }

</style>
</head>

<body>

<div class="container">
    <a href="supervisor_dashboard.php" class="back-btn">
    <i class="fa-solid fa-arrow-left"></i>
    Back to Dashboard
</a>
    <h2><i class="fa-solid fa-building"></i> Institution Evaluation of Students</h2>

    <?php if(mysqli_num_rows($evaluations) > 0): ?>
    <table>
        <tr>
            <th>Student</th>
            <th>Performance</th>
            <th>Strengths</th>
            <th>Weaknesses</th>
            <th>Recommendation</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($evaluations)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td>
                <?php
                $class = "medium";
                if($row['performance'] == "Excellent") $class = "good";
                if($row['performance'] == "Weak") $class = "poor";
                ?>
                <span class="badge <?php echo $class; ?>">
                    <?php echo $row['performance']; ?>
                </span>
            </td>
            <td><?php echo htmlspecialchars($row['strengths']); ?></td>
            <td><?php echo htmlspecialchars($row['weaknesses']); ?></td>
            <td><?php echo htmlspecialchars($row['recommendation']); ?></td>
        </tr>
        <?php endwhile; ?>

    </table>
    <?php else: ?>
        <p>No evaluations available.</p>
    <?php endif; ?>
</div>

</body>
</html>