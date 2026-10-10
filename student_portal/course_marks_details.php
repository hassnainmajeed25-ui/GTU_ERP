<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location:login.php");
    exit;

}

if(!isset($_GET['subject_id'])){

    header("Location:course_marks.php");
    exit;

}

$student_id=(int)$_SESSION['student_id'];

$subject_id=(int)$_GET['subject_id'];

/*=========================================
LOAD STUDENT
=========================================*/

$studentQuery=$conn->prepare("

SELECT

students.*,

programs.program_name

FROM students

LEFT JOIN programs
ON students.program_id=programs.id

WHERE students.id=?

LIMIT 1

");

$studentQuery->bind_param("i",$student_id);

$studentQuery->execute();

$student=$studentQuery->get_result()->fetch_assoc();

/*=========================================
LOAD SUBJECT
=========================================*/

$subjectQuery=$conn->prepare("

SELECT

subjects.*,

teachers.teacher_name,
teachers.profile_photo

FROM subjects

LEFT JOIN teachers
ON teachers.id=subjects.teacher_id

WHERE subjects.id=?

LIMIT 1

");

$subjectQuery->bind_param("i",$subject_id);

$subjectQuery->execute();

$subject=$subjectQuery->get_result()->fetch_assoc();

$page_title="Course Marks Details";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,initial-scale=1.0">

<title>

<?php echo $page_title; ?>

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#eef5fb;

font-family:'Segoe UI',sans-serif;

}

.main{

padding:35px;

}

.header-card{

background:linear-gradient(135deg,#29b6f6,#ff9800);

border-radius:20px;

padding:30px;

color:white;

box-shadow:0 10px 25px rgba(0,0,0,.15);

}

.subject-card{

margin-top:25px;

background:#fff;

border-radius:18px;

padding:25px;

box-shadow:0 6px 18px rgba(0,0,0,.08);

}

.teacher-photo{

width:90px;

height:90px;

border-radius:50%;

object-fit:cover;

border:4px solid orange;

}

.default-photo{

width:90px;

height:90px;

border-radius:50%;

background:#29b6f6;

display:flex;

align-items:center;

justify-content:center;

font-size:35px;

color:#fff;

border:4px solid orange;

}

.info-title{

font-size:14px;

color:#666;

font-weight:bold;

}

.info-value{

font-size:18px;

font-weight:bold;

color:#222;

}

.table thead{

background:#343a40;

color:#fff;

}

</style>

</head>

<body>

<div class="main">

<div class="header-card">

<h2>

<i class="fas fa-square-poll-vertical"></i>

Course Marks Details

</h2>

<p class="mb-0">

Complete Assessment Report of Selected Subject

</p>

</div>

<!-- =========================================
SUBJECT INFORMATION CARD
========================================= -->

<div class="subject-card">

<div class="row align-items-center">

<div class="col-lg-2 text-center">

<?php

$teacherImage="../uploads/teachers/".$subject['profile_photo'];

if(!empty($subject['profile_photo']) && file_exists($teacherImage)){

?>

<img
src="<?php echo $teacherImage; ?>"
class="teacher-photo">

<?php

}else{

?>

<div class="default-photo">

<i class="fas fa-user-tie"></i>

</div>

<?php

}

?>

</div>

<div class="col-lg-10">

<div class="row">

<div class="col-md-6 mb-3">

<div class="info-title">

Subject Name

</div>

<div class="info-value">

<?php echo htmlspecialchars($subject['subject_name']); ?>

</div>

</div>

<div class="col-md-6 mb-3">

<div class="info-title">

Subject Code

</div>

<div class="info-value">

<?php echo htmlspecialchars($subject['subject_code']); ?>

</div>

</div>

<div class="col-md-6 mb-3">

<div class="info-title">

Teacher Name

</div>

<div class="info-value">

<?php

echo !empty($subject['teacher_name'])

? htmlspecialchars($subject['teacher_name'])

: "Not Assigned Yet";

?>

</div>

</div>

<div class="col-md-6 mb-3">

<div class="info-title">

Credit Hours

</div>

<div class="info-value">

<?php echo $subject['credit_hours']; ?>

Credit Hours

</div>

</div>

<div class="col-md-6 mb-3">

<div class="info-title">

Program

</div>

<div class="info-value">

<?php echo htmlspecialchars($student['program_name']); ?>

</div>

</div>

<div class="col-md-6 mb-3">

<div class="info-title">

Roll Number

</div>

<div class="info-value">

<?php echo htmlspecialchars($student['roll_no']); ?>

</div>

</div>

<div class="col-md-6 mb-3">

<div class="info-title">

Semester

</div>

<div class="info-value">

Semester <?php echo $student['semester']; ?>

</div>

</div>

<div class="col-md-6 mb-3">

<div class="info-title">

Subject Type

</div>

<div class="info-value">

<?php

if($subject['has_practical']=="Yes"){

?>

<span class="badge bg-success">

Theory + Practical

</span>

<?php

}else{

?>

<span class="badge bg-primary">

Theory Only

</span>

<?php

}

?>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- =========================================
MARKS TABLE
========================================= -->

<div class="subject-card mt-4">

<h3 class="mb-4 text-dark">

<i class="fas fa-table"></i>

Assessment Marks

</h3>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead>

<tr>

<th width="70">

#

</th>

<th>

Assessment

</th>

<th>

Total Marks

</th>

<th>

Obtained Marks

</th>

<th>

Status

</th>

<th>

Remarks

</th>

</tr>

</thead>

<tbody>

<?php

$assessmentTypes=array(

"Quiz",
"Assignment",
"Presentation",
"Mid",
"Final"

);

if($subject['has_practical']=="Yes"){

$assessmentTypes[]="Practical";

}

$sr=1;

foreach($assessmentTypes as $assessment){

/*=========================================
LOAD MARKS
=========================================*/

$marksQuery=$conn->prepare("

SELECT

mark_sessions.total_marks,

marks_details.obtained_marks,

marks_details.remarks,

mark_sessions.status

FROM mark_sessions

LEFT JOIN marks_details

ON mark_sessions.id=marks_details.mark_session_id

AND marks_details.student_id=?

WHERE

mark_sessions.subject_id=?

AND mark_sessions.assessment_type=?

LIMIT 1

"); 

$marksQuery->bind_param(

"iis",

$student_id,

$subject_id,

$assessment

);

$marksQuery->execute();

$marksResult=$marksQuery->get_result();

$data=$marksResult->fetch_assoc();

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<strong>

<?php echo $assessment; ?>

</strong>

</td>

<td>

<?php

echo !empty($data['total_marks'])

? $data['total_marks']

: "-";

?>

</td>

<td>

<?php

echo isset($data['obtained_marks'])

? $data['obtained_marks']

: "<span class='text-danger'>Not Uploaded</span>";

?>

</td>

<td>

<?php

if(isset($data['status'])){

if($data['status']=="Verified"){

?>

<span class="badge bg-success">

Verified

</span>

<?php

}elseif($data['status']=="Submitted"){

?>

<span class="badge bg-warning text-dark">

Submitted

</span>

<?php

}else{

?>

<span class="badge bg-secondary">

Open

</span>

<?php

}

}else{

?>

<span class="badge bg-danger">

Pending

</span>

<?php

}

?>

</td>

<td>

<?php

echo !empty($data['remarks'])

? htmlspecialchars($data['remarks'])

: "-";

?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>


<!-- =========================================
TOTAL MARKS SUMMARY
========================================= -->

<?php

/*=========================================
CALCULATE TOTAL MARKS
=========================================*/

$totalQuery=$conn->prepare("

SELECT

SUM(mark_sessions.total_marks) AS total_marks,

SUM(marks_details.obtained_marks) AS obtained_marks

FROM mark_sessions

LEFT JOIN marks_details

ON mark_sessions.id=marks_details.mark_session_id

AND marks_details.student_id=?

WHERE

mark_sessions.subject_id=?

");

$totalQuery->bind_param(

"ii",

$student_id,

$subject_id

);

$totalQuery->execute();

$total=$totalQuery->get_result()->fetch_assoc();


$totalMarks = !empty($total['total_marks'])

? $total['total_marks']

: 0;


$obtainedMarks = !empty($total['obtained_marks'])

? $total['obtained_marks']

: 0;


if($totalMarks>0){

$percentage = round(($obtainedMarks/$totalMarks)*100,2);

}else{

$percentage=0;

}


/*=========================================
GRADE SYSTEM
=========================================*/


if($percentage>=85){

$grade="A+";

}elseif($percentage>=75){

$grade="A";

}elseif($percentage>=65){

$grade="B";

}elseif($percentage>=55){

$grade="C";

}elseif($percentage>=50){

$grade="D";

}else{

$grade="F";

}



if($percentage>=50){

$status="Pass";

$statusColor="success";

}else{

$status="Fail";

$statusColor="danger";

}


?>


<div class="subject-card mt-4">


<h3 class="mb-4 text-dark">

<i class="fas fa-chart-line"></i>

Overall Result Summary

</h3>


<div class="row text-center">


<div class="col-md-3 mb-3">

<div class="p-3 rounded shadow-sm bg-light">

<h6 class="text-secondary">

Total Marks

</h6>

<h2 class="fw-bold text-dark">

<?php echo $totalMarks; ?>

</h2>

</div>

</div>



<div class="col-md-3 mb-3">

<div class="p-3 rounded shadow-sm bg-light">

<h6 class="text-secondary">

Obtained Marks

</h6>

<h2 class="fw-bold text-primary">

<?php echo $obtainedMarks; ?>

</h2>

</div>

</div>



<div class="col-md-3 mb-3">

<div class="p-3 rounded shadow-sm bg-light">

<h6 class="text-secondary">

Percentage

</h6>

<h2 class="fw-bold text-warning">

<?php echo $percentage; ?>%

</h2>

</div>

</div>



<div class="col-md-3 mb-3">

<div class="p-3 rounded shadow-sm bg-light">

<h6 class="text-secondary">

Grade

</h6>

<h2 class="fw-bold text-success">

<?php echo $grade; ?>

</h2>

</div>

</div>


</div>



<!-- PROGRESS BAR -->

<div class="mt-4">


<h6 class="fw-bold">

Performance

</h6>


<div class="progress"

style="height:25px;">


<div

class="progress-bar bg-warning"

role="progressbar"

style="width:<?php echo $percentage; ?>%;">


<?php echo $percentage; ?>%


</div>


</div>


</div>



<div class="text-center mt-4">


<span class="badge bg-<?php echo $statusColor; ?> fs-5 px-4 py-2">


<?php echo $status; ?>


</span>


</div>


</div>

<!-- =========================================
NAVIGATION BUTTONS
========================================= -->

<div class="text-center mt-5 mb-4">


<a
href="subjects.php"
class="btn btn-secondary btn-lg me-2">

<i class="fas fa-arrow-left"></i>

Back To Subjects

</a>


<a
href="dashboard.php"
class="btn btn-warning btn-lg">

<i class="fas fa-house"></i>

Dashboard

</a>


</div>



<!-- =========================================
FOOTER
========================================= -->

<div class="text-center mt-5 pb-4">


<hr>


<img
src="../assets/images/logo.png"
style="height:60px;">


<h5 class="mt-3 fw-bold text-dark">

Global Tech University ERP

</h5>


<p class="text-muted mb-1">

Student Course Marks Portal

</p>


<p class="text-muted">

© <?php echo date("Y"); ?>

All Rights Reserved

</p>


</div>


</div>


<!-- =========================================
BOOTSTRAP JS
========================================= -->


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>