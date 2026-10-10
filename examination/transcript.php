<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Examination Office"){
    header("Location: ../admins/login.php");
    exit;
}

/*=========================================
CHECK STUDENT ID
=========================================*/

if(!isset($_GET['student_id'])){
    header("Location: view_results.php");
    exit;
}

$student_id = intval($_GET['student_id']);

/*=========================================
LOAD STUDENT INFORMATION
=========================================*/

$stmt = $conn->prepare("

SELECT

students.id,
students.first_name,
students.last_name,

applications.registration_no,
applications.roll_no,

programs.program_name

FROM students

INNER JOIN applications
ON applications.student_id=students.id

INNER JOIN programs
ON programs.id=applications.program_id

WHERE students.id=?

LIMIT 1

");

$stmt->bind_param("i",$student_id);

$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

if(!$student){

die("Student Not Found.");

}

/*=========================================
LOAD COMPLETE TRANSCRIPT
=========================================*/

$sql="

SELECT

semester_results.*,

subjects.subject_code,
subjects.subject_name

FROM semester_results

INNER JOIN subjects
ON subjects.id=semester_results.subject_id

WHERE semester_results.student_id=?

ORDER BY

semester ASC,
subject_name ASC

";

$get = $conn->prepare($sql);

$get->bind_param("i",$student_id);

$get->execute();

$results = $get->get_result();

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>

Official Transcript

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{

background:#f2f4f7;

font-family:Arial;

}

.transcript{

background:white;

padding:30px;

margin-top:30px;

border:2px solid #000;

}

.title{

font-size:30px;

font-weight:bold;

text-align:center;

}

.subtitle{

text-align:center;

margin-bottom:30px;

font-size:18px;

}

</style>

</head>

<body>

<div class="container">

<div class="transcript">

<div class="title">

GLOBAL TECH UNIVERSITY

</div>

<div class="subtitle">

OFFICIAL ACADEMIC TRANSCRIPT

</div>

<hr>

<div class="row">

<div class="col-md-6">

<table class="table table-bordered">

<tr>

<th width="40%">

Registration No

</th>

<td>

<?php echo htmlspecialchars($student['registration_no']); ?>

</td>

</tr>

<tr>

<th>

Roll No

</th>

<td>

<?php echo htmlspecialchars($student['roll_no']); ?>

</td>

</tr>

<tr>

<th>

Student Name

</th>

<td>

<?php

echo htmlspecialchars(

$student['first_name']." ".$student['last_name']

);

?>

</td>

</tr>

<tr>

<th>

Program

</th>

<td>

<?php echo htmlspecialchars($student['program_name']); ?>

</td>

</tr>

</table>

</div>

</div>

<hr>

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Semester</th>

<th>Subject Code</th>

<th>Subject Name</th>

<th>CH</th>

<th>Total</th>

<th>Obtained</th>

<th>%</th>

<th>Grade</th>

<th>GP</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$sr = 1;

$totalCH = 0;
$totalGP = 0;

while($row = $results->fetch_assoc()){

$totalCH += $row['credit_hours'];

$totalGP += ($row['grade_point'] * $row['credit_hours']);

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php echo $row['semester']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['subject_code']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['subject_name']); ?>

</td>

<td>

<?php echo $row['credit_hours']; ?>

</td>

<td>

<?php echo $row['total_marks']; ?>

</td>

<td>

<?php echo $row['obtained_marks']; ?>

</td>

<td>

<?php echo number_format($row['percentage'],2); ?>%

</td>

<td>

<?php echo $row['grade']; ?>

</td>

<td>

<?php echo number_format($row['grade_point'],2); ?>

</td>

<td>

<?php

if($row['status']=="Pass"){

?>

<span class="badge bg-success">

PASS

</span>

<?php

}else{

?>

<span class="badge bg-danger">

FAIL

</span>

<?php

}

?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php

/*=========================================
CGPA CALCULATION
=========================================*/

$cgpa = 0;

if($totalCH > 0){

$cgpa = round($totalGP / $totalCH,2);

}

?>

<hr>

<div class="row">

<div class="col-md-4">

<div class="alert alert-primary text-center">

<h4>

<?php echo $totalCH; ?>

</h4>

<b>Total Credit Hours</b>

</div>

</div>

<div class="col-md-4">

<div class="alert alert-success text-center">

<h4>

<?php echo number_format($cgpa,2); ?>

</h4>

<b>CGPA</b>

</div>

</div>

<div class="col-md-4">

<div class="alert <?php echo ($cgpa>=2.00)?'alert-success':'alert-danger'; ?> text-center">

<h4>

<?php

if($cgpa>=3.50){

echo "Excellent";

}elseif($cgpa>=3.00){

echo "Very Good";

}elseif($cgpa>=2.50){

echo "Good";

}elseif($cgpa>=2.00){

echo "Satisfactory";

}else{

echo "Probation";

}

?>

</h4>

<b>Academic Standing</b>

</div>

</div>

</div>

<hr>

<div class="row mt-5">

<div class="col-md-6 text-center">

<br><br>

_____________________________

<br>

<b>Controller of Examination</b>

</div>

<div class="col-md-6 text-center">

<br><br>

_____________________________

<br>

<b>Registrar</b>

</div>

</div>

<div class="alert alert-info mt-4">

<b>Official Note:</b>

This transcript has been generated from the Global Tech University ERP System.

Any alteration, overwriting or cutting will make this transcript invalid.

</div>

<div class="text-end mt-4 no-print">

<button

class="btn btn-primary"

onclick="window.print()">

<i class="fas fa-print me-2"></i>

Print Transcript

</button>

<a

href="view_results.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

</div>

<style>

@media print{

.no-print{

display:none;

}

body{

background:white;

}

.transcript{

border:2px solid #000;

}

}

</style>

<script>

window.onload=function(){

window.print();

};

</script>

</body>

</html>