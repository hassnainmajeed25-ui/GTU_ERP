<?php
session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

/*==============================
LOAD STUDENT
===============================*/

$stmt = $conn->prepare("
SELECT
students.*,
programs.program_name,
programs.program_code,
faculties.faculty_name
FROM students
LEFT JOIN programs
ON students.program_id=programs.id
LEFT JOIN faculties
ON programs.faculty_id=faculties.id
WHERE students.id=?
LIMIT 1
");

$stmt->bind_param("i",$student_id);
$stmt->execute();
$student=$stmt->get_result()->fetch_assoc();

if(!$student){
    die("Student not found");
}

/*=========================================
SUMMER ENROLLMENT STATUS
=========================================*/

$summer = $conn->prepare("

SELECT

id,
status

FROM summer_enrollments

WHERE student_id=?

ORDER BY id DESC

LIMIT 1

");

$summer->bind_param("i",$student_id);

$summer->execute();

$summerResult = $summer->get_result();

$summerData = $summerResult->fetch_assoc();
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Summer Enrollment
</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{
background:#F5F5F5;
font-family:'Segoe UI',sans-serif;
}

/* Header */

.header-card{

background:linear-gradient(135deg,#FFD54F,#7E57C2);

color:white;

padding:30px;

border-radius:18px;

box-shadow:0 10px 25px rgba(0,0,0,.15);

margin-bottom:25px;

}

/* Cards */

.info-card{

background:white;

border-radius:18px;

padding:20px;

margin-bottom:20px;

box-shadow:0 5px 15px rgba(0,0,0,.08);

border-left:8px solid #7E57C2;

}

.section-title{

font-size:24px;

font-weight:bold;

color:#6A1B9A;

margin-bottom:20px;

}

.student-box{

background:#F9F9F9;

border-radius:15px;

padding:18px;

}

.student-box h6{

color:#6A1B9A;

font-weight:bold;

margin-bottom:6px;

}

</style>

</head>

<body>

<div class="container py-4">

<!-- HEADER -->

<div class="header-card">

<h2>

<i class="fas fa-sun"></i>

Summer Enrollment Portal

</h2>

<p class="mb-0">

Repeat Courses Registration (GTU ERP)

</p>

</div>

<!-- STUDENT INFO -->

<div class="info-card">

<h3 class="section-title">

<i class="fas fa-user-graduate"></i>

Student Information

</h3>

<div class="row">

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Student Name</h6>

<?php
echo $student['first_name']." ".$student['last_name'];
?>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Registration No</h6>

<?php
echo $student['registration_no'];
?>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Roll No</h6>

<?php
echo $student['roll_no'];
?>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Program</h6>

<?php
echo $student['program_name'];
?>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Semester</h6>

<?php
echo $student['semester'];
?>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Shift</h6>

<?php
echo $student['shift'];
?>

</div>

</div>

</div>

</div>

<!-- ==========================
SUMMER ENROLLMENT RULES
========================== -->

<div class="info-card">

<h3 class="section-title">

<i class="fas fa-circle-info"></i>

Summer Enrollment Rules

</h3>

<div class="alert alert-warning">

<ul class="mb-0">

<li>
<b>F Grade</b> → <span class="text-danger">Confirm Repeat</span> (Automatically Selected)
</li>

<li>
<b>D Grade</b> → <span class="text-primary">Allowed Repeat</span> (Student Choice)
</li>

<li>
Maximum <b>3 Subjects</b> can be enrolled in Summer.
</li>

<li>
F Grade subjects are compulsory.
</li>

<li>
D Grade subjects are optional.
</li>

<li>
After submitting, your request will go to the Examination Office.
</li>

</ul>

</div>

</div>

<!-- =========================================
AVAILABLE SUBJECTS
========================================= -->

<div class="info-card">

<h3 class="section-title">

<i class="fas fa-book-open"></i>

Available Repeat Subjects

</h3>

<form action="submit_summer_enrollment.php" method="POST">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-warning">

<tr>

<th width="80">

Select

</th>

<th>

Subject

</th>

<th>

Code

</th>

<th>

Grade

</th>

<th>

Repeat Type

</th>

</tr>

</thead>

<tbody>

<?php

$query = $conn->prepare("

SELECT

m.subject_id,
m.grade,

s.subject_name,
s.subject_code

FROM marks m

INNER JOIN subjects s

ON m.subject_id=s.id

WHERE

m.student_id=?

AND (m.grade='F' OR m.grade='D')

ORDER BY s.subject_code ASC

");

$query->bind_param("i",$student_id);

$query->execute();

$result=$query->get_result();

if($result->num_rows==0){

?>

<tr>

<td colspan="5" class="text-center">

<div class="alert alert-success m-3">

Congratulations!

No Repeat Subjects Available.

</div>

</td>

</tr>

<?php

}else{

while($row=$result->fetch_assoc()){

$isMandatory = ($row['grade']=="F");

?>

<tr>

<td>

<?php if($isMandatory){ ?>

<input
type="checkbox"
checked
disabled>

<input
type="hidden"
name="mandatory_subjects[]"
value="<?php echo $row['subject_id']; ?>">

<?php }else{ ?>

<input
type="checkbox"
class="optionalSubject"
name="optional_subjects[]"
value="<?php echo $row['subject_id']; ?>">

<?php } ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['subject_name']); ?>

</strong>

</td>

<td>

<?php echo htmlspecialchars($row['subject_code']); ?>

</td>

<td>

<span class="badge bg-<?php echo ($row['grade']=="F") ? "danger" : "warning"; ?>">

<?php echo $row['grade']; ?>

</span>

</td>

<td>

<?php if($isMandatory){ ?>

<span class="badge bg-danger">

Confirm Repeat

</span>

<?php }else{ ?>

<span class="badge bg-primary">

Allowed Repeat

</span>

<?php } ?>

</td>

</tr>

<?php

}

}

?>

</tbody>

</table>

</div>

<div class="row mt-4">

<div class="col-md-6">

<div class="alert alert-info fw-bold">

Selected Subjects :

<span id="selectedCount">

<?php

$mandatoryCount = 0;

$checkMandatory = $conn->prepare("

SELECT COUNT(*)

FROM marks

WHERE

student_id=?

AND grade='F'

");

$checkMandatory->bind_param("i",$student_id);

$checkMandatory->execute();

$checkMandatory->bind_result($mandatoryCount);

$checkMandatory->fetch();

$checkMandatory->close();

echo $mandatoryCount;

?>

</span>

/ 3

</div>

</div>

<div class="col-md-6 text-end">

<?php

if(!$summerData){

?>

<button
type="submit"
class="btn btn-lg btn-warning">

<i class="fas fa-paper-plane"></i>

Apply Summer Enrollment

</button>

<?php

}elseif($summerData['status']=="Pending"){

?>

<button
class="btn btn-lg btn-warning"
disabled>

<i class="fas fa-hourglass-half"></i>

Application Pending

</button>

<?php

}elseif($summerData['status']=="Approved"){

?>

<a
href="summer_fee.php"
class="btn btn-lg btn-success">

<i class="fas fa-money-check-dollar"></i>

Pay Summer Fee

</a>

<?php

}elseif($summerData['status']=="Rejected"){

?>

<button
class="btn btn-lg btn-danger"
disabled>

<i class="fas fa-circle-xmark"></i>

Application Rejected

</button>

<?php

}

?>

</div>

</div>

</form>

</div>

<script>

let mandatory = <?php echo $mandatoryCount; ?>;

let optional = document.querySelectorAll(".optionalSubject");

let counter = document.getElementById("selectedCount");

counter.innerHTML = mandatory;

optional.forEach(function(box){

box.addEventListener("change",function(){

let total = mandatory;

optional.forEach(function(item){

if(item.checked){

total++;

}

});

if(total>3){

alert("Maximum 3 subjects are allowed.");

this.checked=false;

return;

}

counter.innerHTML = total;

});

});

</script>

<!-- =========================================
IMPORTANT NOTICE
========================================= -->

<div class="info-card mt-4">

<h3 class="section-title">

<i class="fas fa-bullhorn"></i>

Important Notice

</h3>

<div class="alert alert-danger">

<ul class="mb-0">

<li>Summer Enrollment is available only for repeat courses.</li>

<li>Courses with an <strong>F Grade</strong> are compulsory and will be automatically marked as <strong>Confirm Repeat</strong>.</li>

<li>Courses with a <strong>D Grade</strong> are optional and may be repeated at the student's choice.</li>

<li>A student may enroll in a maximum of <strong>three (3)</strong> courses during the Summer Semester.</li>

<li>Once the Summer Enrollment request has been submitted, it cannot be modified or cancelled.</li>

<li>All Summer Enrollment requests are subject to approval by the <strong>Examination Office</strong>.</li>

<li>Students are advised to carefully review their selected courses before submitting the application.</li>

</ul>

</div>

</div>

<!-- =========================================
FOOTER
========================================= -->

<footer class="mt-5">

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-6">

<div class="d-flex align-items-center">

<img
src="../assets/images/logo.png"
style="height:55px;"
class="me-3">

<div>

<h5 class="mb-1">

Global Tech University ERP

</h5>

<p class="mb-0 text-muted">

Summer Enrollment Portal

</p>

</div>

</div>

</div>

<div class="col-md-6 text-md-end mt-3 mt-md-0">

<p class="mb-1">

<strong>

Version 1.0

</strong>

</p>

<p class="mb-1 text-muted">

Student Summer Enrollment System

</p>

<p class="mb-0 text-secondary">

© <?php echo date("Y"); ?>

Global Tech University

</p>

</div>

</div>

</div>

</div>

</footer>

</div>
<!-- Container End -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>