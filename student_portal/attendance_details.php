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

$student_id=$_SESSION['student_id'];

if(!isset($_GET['subject_id'])){

header("Location:attendance.php");
exit;

}

$subject_id=(int)$_GET['subject_id'];


/*=========================================
STUDENT INFORMATION
=========================================*/

$studentQuery=$conn->prepare("

SELECT

students.*,

programs.program_name,

faculties.faculty_name

FROM students

LEFT JOIN programs

ON students.program_id=programs.id

LEFT JOIN faculties

ON programs.faculty_id=faculties.id

WHERE students.id=?

LIMIT 1

");

$studentQuery->bind_param("i",$student_id);

$studentQuery->execute();

$student=$studentQuery->get_result()->fetch_assoc();


/*=========================================
SUBJECT INFORMATION
=========================================*/

$subjectQuery=$conn->prepare("

SELECT

subjects.*,

teachers.teacher_name,
teachers.profile_photo,
teachers.designation

FROM subjects

LEFT JOIN teacher_subjects
ON subjects.id = teacher_subjects.subject_id

LEFT JOIN teachers
ON teacher_subjects.teacher_id = teachers.id

WHERE

subjects.id=?

AND teacher_subjects.status='Active'

LIMIT 1

");

$subjectQuery->bind_param("i",$subject_id);

$subjectQuery->execute();

$result=$subjectQuery->get_result();

if($result->num_rows==0){

die("Subject Not Found");

}

$subject=$result->fetch_assoc();


$page_title="Attendance Details";

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

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

background:#36454F;

font-family:Segoe UI;

}

/*==============================*/

.main-card{

background:#708090;

border-radius:20px;

padding:30px;

box-shadow:0 10px 25px rgba(0,0,0,.30);

}

/*==============================*/

.header-card{

background:linear-gradient(135deg,#C71585,#8A2BE2);

border-radius:20px;

padding:30px;

color:#fff;

}

/*==============================*/

.teacher-photo{

width:90px;

height:90px;

border-radius:50%;

object-fit:cover;

border:4px solid gold;

}

/*==============================*/

.info-card{

background:#ffffff;

border-radius:18px;

padding:20px;

box-shadow:0 5px 18px rgba(0,0,0,.12);

}

/*==============================*/

.summary-card{

border-radius:18px;

padding:20px;

text-align:center;

color:#fff;

font-weight:bold;

}

.present{

background:#198754;

}

.absent{

background:#dc3545;

}

.leave{

background:#0d6efd;

}

.percentage{

background:#ffc107;

color:#000;

}

/*==============================*/

.table thead{

background:#C71585;

color:#fff;

}

.progress{

height:28px;

border-radius:50px;

}

.progress-bar{

background:#FFD700;

font-weight:bold;

color:#000;

}

.warning-box{

background:#fff3cd;

border-left:6px solid #ffc107;

padding:20px;

border-radius:15px;

font-size:18px;

}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="main-card">

<div class="header-card">

<div class="row align-items-center">

<div class="col-md-2 text-center">

<?php

$image="../uploads/teachers/".$subject['profile_photo'];

if(!empty($subject['profile_photo']) && file_exists($image)){

?>

<img

src="<?php echo $image; ?>"

class="teacher-photo">

<?php

}else{

?>

<img

src="../assets/images/default-user.png"

class="teacher-photo">

<?php

}

?>

</div>

<div class="col-md-10">

<h2>

<?php echo htmlspecialchars($subject['subject_name']); ?>

</h2>

<h5>

<?php echo htmlspecialchars($subject['teacher_name']); ?>

</h5>

<p class="mb-0">

<?php echo htmlspecialchars($subject['designation']); ?>

</p>

</div>

</div>

</div>

<br>

<div class="info-card">

<div class="row">

<div class="col-md-3">

<b>Student</b><br>

<?php echo htmlspecialchars($student['first_name']." ".$student['last_name']); ?>

</div>

<div class="col-md-3">

<b>Roll No</b><br>

<?php echo htmlspecialchars($student['roll_no']); ?>

</div>

<div class="col-md-3">

<b>Program</b><br>

<?php echo htmlspecialchars($student['program_name']); ?>

</div>

<div class="col-md-3">

<b>Semester</b><br>

Semester <?php echo $student['semester']; ?>

</div>

</div>

</div>

<br>

<?php

/*=========================================
ATTENDANCE SUMMARY
=========================================*/

$summary=$conn->prepare("

SELECT

SUM(CASE WHEN attendance_details.status='Present' THEN 1 ELSE 0 END) AS presents,

SUM(CASE WHEN attendance_details.status='Absent' THEN 1 ELSE 0 END) AS absents,

SUM(CASE WHEN attendance_details.status='Leave' THEN 1 ELSE 0 END) AS leaves,

COUNT(attendance_details.id) AS total

FROM attendance_details

INNER JOIN attendance

ON attendance.id=attendance_details.attendance_id

WHERE

attendance.subject_id=?

AND attendance_details.student_id=?

");

$summary->bind_param(

"ii",

$subject_id,

$student_id

);

$summary->execute();

$data=$summary->get_result()->fetch_assoc();

$presents=$data['presents'] ?? 0;

$absents=$data['absents'] ?? 0;

$leaves=$data['leaves'] ?? 0;

$total=$data['total'] ?? 0;

if($total>0){

$percentage=round(($presents/$total)*100,2);

}else{

$percentage=0;

}

?>

<div class="row text-center">

<div class="col-md-3 mb-3">

<div class="summary-card present">

<h5>Present</h5>

<h1><?php echo $presents; ?></h1>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="summary-card absent">

<h5>Absent</h5>

<h1><?php echo $absents; ?></h1>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="summary-card leave">

<h5>Leave</h5>

<h1><?php echo $leaves; ?></h1>

</div>

</div>

<div class="col-md-3 mb-3">

<div class="summary-card percentage">

<h5>Attendance</h5>

<h1><?php echo $percentage; ?>%</h1>

</div>

</div>

</div>

<div class="info-card mt-4">

<h4>

<i class="fas fa-chart-line"></i>

Attendance Percentage

</h4>

<br>

<div class="progress">

<div

class="progress-bar"

style="width:<?php echo $percentage; ?>%;">

<?php echo $percentage; ?>%

</div>

</div>

</div>

<br>

<?php

if($percentage>=75){

?>

<div class="alert alert-success">

<h4>

<i class="fas fa-circle-check"></i>

Eligible For Final Examination

</h4>

<p class="mb-0">

Congratulations!

Your attendance fulfills the university requirement.

You are eligible to appear in the Final Examination.

</p>

</div>

<?php

}else{

?>

<div class="alert alert-danger">

<h4>

<i class="fas fa-triangle-exclamation"></i>

Final Examination Warning

</h4>

<p class="mb-0">

Your attendance is below the required

<strong>75%</strong>.

According to Global Tech University policy,

you are currently

<strong>NOT ELIGIBLE</strong>

to appear in the Final Examination.

<br><br>

Please contact your

<strong>Head of Department (HOD)</strong>

for further guidance.

</p>

</div>

<?php

}

?>

<br>

<div class="info-card">

<h3>

<i class="fas fa-calendar-check"></i>

Complete Attendance Record

</h3>

<br>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead>

<tr>

<th>#</th>

<th>Date</th>

<th>Day</th>

<th>Start Time</th>

<th>End Time</th>

<th>Status</th>

<th>Late Minutes</th>

<th>Remarks</th>

</tr>

</thead>

<tbody>

<?php

/*=========================================
LOAD COMPLETE ATTENDANCE
=========================================*/

$history=$conn->prepare("

SELECT

lecture_sessions.lecture_date,

lecture_sessions.start_time,

lecture_sessions.end_time,

attendance_details.status,

attendance_details.remarks,

attendance_details.late_minutes

FROM attendance_details

INNER JOIN attendance

ON attendance.id=attendance_details.attendance_id

INNER JOIN lecture_sessions

ON lecture_sessions.id=attendance.lecture_session_id

WHERE

attendance.subject_id=?

AND attendance_details.student_id=?

ORDER BY lecture_sessions.lecture_date DESC

");

$history->bind_param(

"ii",

$subject_id,

$student_id

);

$history->execute();

$records=$history->get_result();

$sr=1;

if($records->num_rows>0){

while($row=$records->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php echo date("d M Y",strtotime($row['lecture_date'])); ?>

</td>

<td>

<?php echo date("l",strtotime($row['lecture_date'])); ?>

</td>

<td>

<?php

echo !empty($row['start_time'])

? date("h:i A",strtotime($row['start_time']))

: "-";

?>

</td>

<td>

<?php

echo !empty($row['end_time'])

? date("h:i A",strtotime($row['end_time']))

: "-";

?>

</td>

<td>

<?php

if($row['status']=="Present"){

?>

<span class="badge bg-success">

Present

</span>

<?php

}elseif($row['status']=="Absent"){

?>

<span class="badge bg-danger">

Absent

</span>

<?php

}elseif($row['status']=="Leave"){

?>

<span class="badge bg-warning text-dark">

Leave

</span>

<?php

}else{

?>

<span class="badge bg-secondary">

-

</span>

<?php

}

?>

</td>

<td>

<?php

if($row['status']=="Late"){

    echo $row['late_minutes']." Minutes";

}else{

    echo "-";

}

?>

</td>

<td>

<?php

echo !empty($row['remarks'])

? htmlspecialchars($row['remarks'])

: "-";

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center py-5">

<i class="fas fa-calendar-times"

style="font-size:60px;color:#999;"></i>

<h4 class="mt-3">

No Attendance Record Found

</h4>

<p class="text-muted">

Attendance will appear automatically

after your teacher marks attendance.

</p>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

<div class="text-center mt-4">

<a

href="attendance.php"

class="btn btn-secondary btn-lg">

<i class="fas fa-arrow-left"></i>

Back to Attendance

</a>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>