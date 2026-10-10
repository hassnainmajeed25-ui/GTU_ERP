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

if(!isset($_GET['student']) || !isset($_GET['exam'])){
    die("Invalid Request");
}

$student_id=(int)$_GET['student'];
$exam_id=(int)$_GET['exam'];

/*====================================
LOAD STUDENT + EXAM
====================================*/

$sql="

SELECT

students.*,

applications.registration_no,

applications.shift,

subjects.subject_code,
subjects.subject_name,

exams.exam_title,
exams.exam_date,
exams.start_time,
exams.end_time,
exams.room_no,

faculties.faculty_name,

programs.program_name

FROM students

LEFT JOIN applications
ON applications.student_id=students.id

LEFT JOIN exams
ON exams.id='$exam_id'

LEFT JOIN subjects
ON subjects.id=exams.subject_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

WHERE students.id='$student_id'

LIMIT 1

";

$result=$conn->query($sql);

if($result->num_rows==0){

die("Student Not Found");

}

$row=$result->fetch_assoc();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>

GTU Admit Card

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#edf2f7;

font-family:Arial,Helvetica,sans-serif;

}

.admit-card{

width:900px;

margin:30px auto;

background:white;

padding:35px;

border-radius:12px;

box-shadow:0 5px 20px rgba(0,0,0,.15);

border:3px solid #0B1F52;

}

.header{

display:flex;

justify-content:space-between;

align-items:center;

border-bottom:3px solid #0B1F52;

padding-bottom:20px;

margin-bottom:25px;

}

.logo{

width:90px;

}

.title{

text-align:center;

flex:1;

}

.title h2{

margin:0;

color:#0B1F52;

font-weight:bold;

}

.title h4{

margin-top:8px;

color:#444;

}

.photo{

width:120px;

height:140px;

border:2px solid #000;

display:flex;

align-items:center;

justify-content:center;

font-size:12px;

}

.info-table td{

padding:10px;

}

.section-title{

background:#0B1F52;

color:white;

padding:10px;

margin-top:20px;

margin-bottom:15px;

font-weight:bold;

}

.signature{

margin-top:70px;

text-align:right;

}

.signature hr{

width:250px;

margin-left:auto;

border:1px solid #000;

}

.print-btn{

text-align:center;

margin-top:30px;

}

@media print{

.print-btn{

display:none;

}

body{

background:white;

}

.admit-card{

box-shadow:none;

border:none;

margin:0;

width:100%;

}

}

</style>

</head>

<body>

<div class="admit-card">

<div class="header">

<img
src="../assets/images/logo.png"
class="logo">

<div class="title">

<h2>

GLOBAL TECH UNIVERSITY

</h2>

<h4>

OFFICIAL ADMIT CARD

</h4>

</div>

<div class="photo">

Student Photo

</div>

</div>
<!-- =====================================
STUDENT INFORMATION
===================================== -->

<div class="section-title">

Student Information

</div>

<table class="table table-bordered info-table">

<tr>

<th width="220">

Student Name

</th>

<td>

<?php

echo htmlspecialchars($row['first_name']);

echo " ";

echo htmlspecialchars($row['last_name']);

?>

</td>

<th width="220">

Registration No

</th>

<td>

<?php

if(!empty($row['registration_no'])){

echo htmlspecialchars($row['registration_no']);

}else{

echo "Not Generated";

}

?>

</td>

</tr>

<tr>

<th>

Father Name

</th>

<td>

<?php echo htmlspecialchars($row['father_name']); ?>

</td>

<th>

Roll Number

</th>

<td>

<?php

if(!empty($row['roll_no'])){

echo htmlspecialchars($row['roll_no']);

}else{

echo "Not Generated";

}

?>

</td>

</tr>

<tr>

<th>

Email

</th>

<td>

<?php echo htmlspecialchars($row['email']); ?>

</td>

<th>

Mobile

</th>

<td>

<?php echo htmlspecialchars($row['mobile']); ?>

</td>

</tr>

<tr>

<th>

Faculty

</th>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<th>

Program

</th>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

</tr>

<tr>

<th>

Shift

</th>

<td>

<?php echo htmlspecialchars($row['shift']); ?>

</td>

<th>

Semester

</th>

<td>

1

</td>

</tr>

</table>

<!-- =====================================
EXAM INFORMATION
===================================== -->

<div class="section-title">

Examination Information

</div>

<table class="table table-bordered info-table">

<tr>

<th width="220">

Exam Title

</th>

<td>

<?php echo htmlspecialchars($row['exam_title']); ?>

</td>

<th>

Subject Code

</th>

<td>

<?php echo htmlspecialchars($row['subject_code']); ?>

</td>

</tr>

<tr>

<th>

Subject Name

</th>

<td>

<?php echo htmlspecialchars($row['subject_name']); ?>

</td>

<th>

Exam Date

</th>

<td>

<?php echo date("d M Y",strtotime($row['exam_date'])); ?>

</td>

</tr>

<tr>

<th>

Start Time

</th>

<td>

<?php echo date("h:i A",strtotime($row['start_time'])); ?>

</td>

<th>

End Time

</th>

<td>

<?php echo date("h:i A",strtotime($row['end_time'])); ?>

</td>

</tr>

<tr>

<th>

Room Number

</th>

<td>

<?php echo htmlspecialchars($row['room_no']); ?>

</td>

<th>

Status

</th>

<td>

<?php echo htmlspecialchars($row['status']); ?>

</td>

</tr>

</table>
<!-- =====================================
IMPORTANT INSTRUCTIONS
===================================== -->

<div class="section-title">

Important Instructions

</div>

<ul class="list-group">

<li class="list-group-item">

✔ Student must bring this Admit Card in every examination.

</li>

<li class="list-group-item">

✔ University Student ID Card is compulsory.

</li>

<li class="list-group-item">

✔ Mobile Phones, Smart Watches and Electronic Devices are not allowed.

</li>

<li class="list-group-item">

✔ Student must arrive at least 30 minutes before examination.

</li>

<li class="list-group-item">

✔ Any unfair means during examination will result in disciplinary action.

</li>

<li class="list-group-item">

✔ This Admit Card is valid only for the mentioned examination.

</li>

</ul>

<br>

<!-- =====================================
QR + SIGNATURE
===================================== -->

<div class="row">

<div class="col-md-4 text-center">

<div style="width:150px;height:150px;border:2px solid #000;margin:auto;display:flex;align-items:center;justify-content:center;">

QR CODE

</div>

<p class="mt-2">

QR Verification

</p>

</div>

<div class="col-md-4 text-center">

<div style="margin-top:90px;">

<hr>

<b>

Student Signature

</b>

</div>

</div>

<div class="col-md-4 text-center">

<div style="margin-top:90px;">

<hr>

<b>

Controller of Examination

</b>

<br>

Global Tech University

</div>

</div>

</div>

<br>

<!-- =====================================
UNIVERSITY STAMP
===================================== -->

<div class="alert alert-light border">

<b>Official Stamp</b>

<div style="height:90px;border:2px dashed #888;margin-top:10px;display:flex;align-items:center;justify-content:center;">

University Seal / Stamp

</div>

</div>

<!-- =====================================
BUTTONS
===================================== -->

<div class="print-btn">

<button

onclick="window.print();"

class="btn btn-success btn-lg">

<i class="fas fa-print me-2"></i>

Print Admit Card

</button>

<a

href="generate_admit_card.php?id=<?php echo $exam_id; ?>"

class="btn btn-secondary btn-lg">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

<script>

window.onload=function(){

console.log("GTU Admit Card Loaded Successfully");

}

</script>

</body>

</html>