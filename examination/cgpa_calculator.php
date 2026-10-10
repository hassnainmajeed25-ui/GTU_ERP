<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*====================================
LOGIN SECURITY
====================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Examination Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>

CGPA Calculator

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.sidebar{

width:260px;

height:100vh;

background:#0B1F52;

position:fixed;

left:0;

top:0;

overflow:auto;

}

.sidebar h3{

color:#fff;

padding:20px;

text-align:center;

border-bottom:1px solid rgba(255,255,255,.20);

}

.sidebar a{

display:block;

padding:14px 20px;

text-decoration:none;

color:#fff;

transition:.3s;

}

.sidebar a:hover{

background:#173a8b;

}

.sidebar a.active{

background:#2563eb;

}

.main{

margin-left:260px;

padding:30px;

}

.topbar{

background:#fff;

padding:20px;

border-radius:12px;

box-shadow:0 3px 10px rgba(0,0,0,.10);

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:25px;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.10);

}

</style>

</head>

<body>

<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php">

<i class="fas fa-home me-2"></i>

Dashboard

</a>

<a href="manage_subjects.php">

<i class="fas fa-book me-2"></i>

Manage Subjects

</a>

<a href="manage_exams.php">

<i class="fas fa-calendar-days me-2"></i>

Manage Exams

</a>

<a href="enter_marks.php">

<i class="fas fa-pen me-2"></i>

Enter Marks

</a>

<a href="results.php">

<i class="fas fa-square-poll-vertical me-2"></i>

Results

</a>

<a href="cgpa_calculator.php" class="active">

<i class="fas fa-calculator me-2"></i>

CGPA Calculator

</a>

<a href="admit_cards.php">

<i class="fas fa-id-card me-2"></i>

Admit Cards

</a>

<a href="reports.php">

<i class="fas fa-chart-column me-2"></i>

Reports

</a>

<a href="../admins/logout.php">

<i class="fas fa-right-from-bracket me-2"></i>

Logout

</a>

</div>

<div class="main">

<div class="topbar">

<div>

<h3>

CGPA Calculator

</h3>

<small class="text-muted">

Global Tech University ERP

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-search me-2"></i>

Select Student

</h5>

</div>

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-10">

<select

name="student"

class="form-select"

required>

<option value="">

Select Student

</option>

<?php

$list=$conn->query("

SELECT

students.id,

students.first_name,

students.last_name,

applications.registration_no

FROM students

LEFT JOIN applications

ON applications.student_id=students.id

ORDER BY students.first_name ASC

");

while($s=$list->fetch_assoc()){

?>

<option

value="<?php echo $s['id'];?>"

<?php

if(isset($_GET['student']) && $_GET['student']==$s['id']){

echo "selected";

}

?>>

<?php

echo $s['registration_no'];

echo " - ";

echo $s['first_name']." ".$s['last_name'];

?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-2">

<button

class="btn btn-success w-100"

type="submit">

<i class="fas fa-search me-2"></i>

Load

</button>

</div>

</div>

</form>

</div>

</div>

<br>

<?php

if(isset($_GET['student'])){

$student_id=(int)$_GET['student'];

/*====================================
LOAD STUDENT INFORMATION
====================================*/

$studentSQL="

SELECT

students.*,

applications.registration_no,

faculties.faculty_name,

programs.program_name

FROM students

LEFT JOIN applications
ON applications.student_id=students.id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

WHERE students.id='$student_id'

LIMIT 1

";

$studentResult=$conn->query($studentSQL);

if($studentResult && $studentResult->num_rows>0){

$student=$studentResult->fetch_assoc();

?>

<div class="card mb-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-user-graduate me-2"></i>

Student Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<strong>Registration No</strong><br>

<?php echo htmlspecialchars($student['registration_no']); ?>

</div>

<div class="col-md-6 mb-3">

<strong>Student Name</strong><br>

<?php

echo htmlspecialchars(

$student['first_name']." ".$student['last_name']

);

?>

</div>

<div class="col-md-6 mb-3">

<strong>Faculty</strong><br>

<?php echo htmlspecialchars($student['faculty_name']); ?>

</div>

<div class="col-md-6 mb-3">

<strong>Program</strong><br>

<?php echo htmlspecialchars($student['program_name']); ?>

</div>

</div>

</div>

</div>

<?php

/*====================================
CGPA VARIABLES
====================================*/

$totalCreditHours=0;

$totalGradePoints=0;

?>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-calculator me-2"></i>

Semester Wise SGPA

</h5>

</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>Semester</th>

<th>Total Credit Hours</th>

<th>Semester Grade Points</th>

<th>SGPA</th>

</tr>

</thead>

<tbody>

<?php

for($semester=1;$semester<=8;$semester++){

$sql="

SELECT

subjects.credit_hours,

marks.grade_point

FROM marks

INNER JOIN subjects

ON subjects.id=marks.subject_id

WHERE

marks.student_id='$student_id'

AND subjects.semester='$semester'

";

$result=$conn->query($sql);

$semesterCH=0;

$semesterGP=0;

while($row=$result->fetch_assoc()){

$semesterCH+=$row['credit_hours'];

$semesterGP+=($row['credit_hours']*$row['grade_point']);

}

if($semesterCH>0){

$sgpa=round($semesterGP/$semesterCH,2);

$totalCreditHours+=$semesterCH;

$totalGradePoints+=$semesterGP;

?>

<tr>

<td>

Semester <?php echo $semester; ?>

</td>

<td>

<?php echo $semesterCH; ?>

</td>

<td>

<?php echo number_format($semesterGP,2); ?>

</td>

<td>

<strong>

<?php echo number_format($sgpa,2); ?>

</strong>

</td>

</tr>

<?php

}

}

?>

</tbody>

</table>

</div>

</div>

<br>

<?php

}

}

?>
<?php

/*====================================
FINAL CGPA
====================================*/

$cgpa=0;

if($totalCreditHours>0){

    $cgpa=round($totalGradePoints/$totalCreditHours,2);

}

?>

<div class="row">

<div class="col-md-4 mb-3">

<div class="card text-center border-primary">

<div class="card-body">

<h5>Total Credit Hours</h5>

<h2 class="text-primary">

<?php echo $totalCreditHours; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card text-center border-success">

<div class="card-body">

<h5>Final CGPA</h5>

<h2 class="text-success">

<?php echo number_format($cgpa,2); ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card text-center border-info">

<div class="card-body">

<h5>Academic Standing</h5>

<h4>

<?php

if($cgpa>=3.70){

echo '<span class="text-success">Excellent</span>';

}elseif($cgpa>=3.30){

echo '<span class="text-primary">Very Good</span>';

}elseif($cgpa>=3.00){

echo '<span class="text-info">Good</span>';

}elseif($cgpa>=2.00){

echo '<span class="text-warning">Pass</span>';

}else{

echo '<span class="text-danger">Probation</span>';

}

?>

</h4>

</div>

</div>

</div>

</div>

<hr>

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-chart-line me-2"></i>

CGPA Summary

</h5>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th width="40%">Student</th>

<td>

<?php

echo htmlspecialchars(

$student['first_name']." ".$student['last_name']

);

?>

</td>

</tr>

<tr>

<th>Registration Number</th>

<td>

<?php echo htmlspecialchars($student['registration_no']); ?>

</td>

</tr>

<tr>

<th>Total Credit Hours</th>

<td>

<?php echo $totalCreditHours; ?>

</td>

</tr>

<tr>

<th>Final CGPA</th>

<td>

<strong>

<?php echo number_format($cgpa,2); ?>

</strong>

</td>

</tr>

</table>

</div>

</div>

<br>

<div class="text-center">

<button

class="btn btn-primary"

onclick="window.print();">

<i class="fas fa-print me-2"></i>

Print CGPA

</button>

<a

href="transcript.php?student=<?php echo $student_id; ?>"

class="btn btn-success">

<i class="fas fa-file-alt me-2"></i>

View Transcript

</a>

<a

href="results.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

}

}

?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>