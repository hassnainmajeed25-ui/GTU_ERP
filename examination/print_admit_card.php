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
LOAD DATA
====================================*/

$sql="

SELECT

students.*,

applications.registration_no,

applications.shift,

subjects.subject_code,
subjects.subject_name,

faculties.faculty_name,

programs.program_name,

exams.exam_title,
exams.exam_date,
exams.start_time,
exams.end_time,
exams.room_no

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

<html>

<head>

<meta charset="UTF-8">

<title>

Print Admit Card

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#f4f6f9;

font-family:Arial,Helvetica,sans-serif;

}

.admit-card{

width:900px;

margin:20px auto;

background:#fff;

border:3px solid #0B1F52;

padding:35px;

box-shadow:0 5px 15px rgba(0,0,0,.15);

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

flex:1;

text-align:center;

}

.title h2{

margin:0;

color:#0B1F52;

font-weight:bold;

}

.title h4{

margin-top:8px;

color:#555;

}

.photo{

width:120px;

height:140px;

border:2px solid #000;

display:flex;

align-items:center;

justify-content:center;

font-size:12px;

font-weight:bold;

}

.info-table td{

padding:8px;

font-size:15px;

}

.section-title{

background:#0B1F52;

color:#fff;

padding:10px;

margin-top:20px;

margin-bottom:10px;

font-weight:bold;

}

.signature{

margin-top:60px;

display:flex;

justify-content:space-between;

}

.signature div{

width:250px;

text-align:center;

}

.signature hr{

border:1px solid #000;

margin-bottom:5px;

}

@media print{

body{

background:white;

}

.admit-card{

width:100%;

border:none;

box-shadow:none;

margin:0;

}

.no-print{

display:none;

}

}

</style>

</head>

<body onload="window.print();">

<div class="admit-card">

<div class="header">

<img src="../assets/images/logo.png" class="logo">

<div class="title">

<h2>GLOBAL TECH UNIVERSITY</h2>

<h4>OFFICIAL ADMIT CARD</h4>

<p>Examination Office</p>

</div>

<div class="photo">

Student Photo

</div>

</div>

<div class="section-title">

Student Information

</div>

<table class="table table-bordered info-table">

<tr>

<th width="220">Student Name</th>

<td>

<?php

echo htmlspecialchars($row['first_name'])." ".htmlspecialchars($row['last_name']);

?>

</td>

<th width="220">Registration No</th>

<td>

<?php echo htmlspecialchars($row['registration_no']); ?>

</td>

</tr>

<tr>

<th>Father Name</th>

<td><?php echo htmlspecialchars($row['father_name']); ?></td>

<th>Mobile</th>

<td><?php echo htmlspecialchars($row['mobile']); ?></td>

</tr>

<tr>

<th>Email</th>

<td><?php echo htmlspecialchars($row['email']); ?></td>

<th>Shift</th>

<td><?php echo htmlspecialchars($row['shift']); ?></td>

</tr>

<tr>

<th>Faculty</th>

<td><?php echo htmlspecialchars($row['faculty_name']); ?></td>

<th>Program</th>

<td><?php echo htmlspecialchars($row['program_name']); ?></td>

</tr>

</table>

<!-- =====================================
EXAMINATION INFORMATION
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

<th width="220">

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

Examination Status

</th>

<td>

Scheduled

</td>

</tr>

</table>

<!-- =====================================
IMPORTANT INSTRUCTIONS
===================================== -->

<div class="section-title">

Instructions For Students

</div>

<ol>

<li>

Student must bring this Admit Card to every examination.

</li>

<li>

University Student ID Card is compulsory.

</li>

<li>

Reporting time is 30 minutes before examination.

</li>

<li>

Mobile phones, Smart Watches and electronic devices are strictly prohibited.

</li>

<li>

Any unfair means shall lead to cancellation of examination.

</li>

<li>

Follow all instructions issued by the Examination Office.

</li>

<li>

Keep this Admit Card safe until final result declaration.

</li>

</ol>

<br>

<div class="row">

<div class="col-md-6">

<div style="border:2px dashed #888;height:120px;display:flex;align-items:center;justify-content:center;">

University Stamp

</div>

</div>

<div class="col-md-6">

<div style="border:2px dashed #888;height:120px;display:flex;align-items:center;justify-content:center;">

QR Code

</div>

</div>

</div>

<br>
<!-- =====================================
SIGNATURE SECTION
===================================== -->

<br><br>

<div class="signature">

<div>

<hr>

<b>

Student Signature

</b>

</div>

<div>

<hr>

<b>

Invigilator Signature

</b>

</div>

<div>

<hr>

<b>

Controller of Examination

</b>

<br>

Global Tech University

</div>

</div>

<br><br>

<!-- =====================================
DECLARATION
===================================== -->

<div class="alert alert-light border">

<b>Declaration:</b>

<p class="mb-0">

I hereby declare that all the information printed on this Admit Card is correct.
I shall follow all examination rules and regulations issued by the University.
Any violation of examination policy may result in cancellation of my examination.

</p>

</div>

<!-- =====================================
FOOTER
===================================== -->

<hr>

<div class="text-center">

<h6>

GLOBAL TECH UNIVERSITY

</h6>

<p>

Examination Office

</p>

<p>

This is a Computer Generated Admit Card.

</p>

<p>

No Signature Required if QR Verification is Enabled.

</p>

<p>

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

<!-- =====================================
BUTTONS
===================================== -->

<div class="text-center mt-4 no-print">

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

window.print();

};

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>