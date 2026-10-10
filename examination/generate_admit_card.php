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

if(!isset($_GET['id'])){
    die("Invalid Request");
}

$id=(int)$_GET['id'];

/*====================================
LOAD EXAM INFORMATION
====================================*/

$sql="

SELECT

exams.*,

subjects.subject_code,
subjects.subject_name,

faculties.faculty_name,

programs.program_name

FROM exams

LEFT JOIN subjects
ON subjects.id=exams.subject_id

LEFT JOIN faculties
ON faculties.id=exams.faculty_id

LEFT JOIN programs
ON programs.id=exams.program_id

WHERE exams.id='$id'

LIMIT 1

";

$result=$conn->query($sql);

if($result->num_rows==0){

die("Exam Not Found");

}

$exam=$result->fetch_assoc();

/*====================================
LOAD APPROVED STUDENTS
====================================*/

$students=$conn->query("

SELECT

applications.*,

students.first_name,

students.last_name,

students.father_name,

students.email,

students.mobile

FROM applications

LEFT JOIN students

ON students.id=applications.student_id

WHERE applications.program_id='".$exam['program_id']."'

AND applications.faculty_id='".$exam['faculty_id']."'

AND applications.application_status='Approved'

ORDER BY students.first_name ASC

");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Generate Admit Cards

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.10);

}

.info-box{

background:#0B1F52;

color:white;

padding:20px;

border-radius:12px;

margin-bottom:25px;

}

.table th{

background:#0B1F52;

color:white;

}

</style>

</head>

<body>

<div class="container mt-4">

<div class="card">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h2>

<i class="fas fa-id-card me-2"></i>

Generate Admit Cards

</h2>

<p class="text-muted">

Global Tech University ERP

</p>

</div>

<div>

<a
href="admit_cards.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

<hr>

<div class="info-box">

<div class="row">

<div class="col-md-4">

<strong>Exam Title</strong>

<br>

<?php echo htmlspecialchars($exam['exam_title']); ?>

</div>

<div class="col-md-4">

<strong>Subject</strong>

<br>

<?php

echo htmlspecialchars($exam['subject_code']);

echo " - ";

echo htmlspecialchars($exam['subject_name']);

?>

</div>

<div class="col-md-4">

<strong>Exam Date</strong>

<br>

<?php

echo date("d M Y",strtotime($exam['exam_date']));

?>

</div>

</div>

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Registration No</th>

<th>Student Name</th>

<th>Father Name</th>

<th>Email</th>

<th>Mobile</th>

<th width="180">

Action

</th>

</tr>

</thead>

<tbody>
<?php

if($students && $students->num_rows>0){

$sr=1;

while($student=$students->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<strong>

<?php

if(!empty($student['registration_no'])){

echo htmlspecialchars($student['registration_no']);

}else{

echo '<span class="text-danger">Not Generated</span>';

}

?>

</strong>

</td>

<td>

<?php

echo htmlspecialchars($student['first_name']);

echo " ";

echo htmlspecialchars($student['last_name']);

?>

</td>

<td>

<?php

echo htmlspecialchars($student['father_name']);

?>

</td>

<td>

<?php

echo htmlspecialchars($student['email']);

?>

</td>

<td>

<?php

echo htmlspecialchars($student['mobile']);

?>

</td>

<td>

<a

href="print_admit_card.php?student=<?php echo $student['student_id'];?>&exam=<?php echo $exam['id'];?>"

class="btn btn-success btn-sm">

<i class="fas fa-print me-1"></i>

Print

</a>

<a

href="preview_admit_card.php?student=<?php echo $student['student_id'];?>&exam=<?php echo $exam['id'];?>"

class="btn btn-primary btn-sm">

<i class="fas fa-eye me-1"></i>

Preview

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center text-danger">

<i class="fas fa-circle-info me-2"></i>

No Approved Students Found For This Examination.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<hr>

<div class="row">

<div class="col-md-3">

<div class="alert alert-primary text-center">

<h3>

<?php

echo $students->num_rows;

?>

</h3>

Approved Students

</div>

</div>

<div class="col-md-3">

<div class="alert alert-success text-center">

<h3>

<?php

echo htmlspecialchars($exam['faculty_name']);

?>

</h3>

Faculty

</div>

</div>

<div class="col-md-3">

<div class="alert alert-warning text-center">

<h3>

<?php

echo htmlspecialchars($exam['program_name']);

?>

</h3>

Program

</div>

</div>

<div class="col-md-3">

<div class="alert alert-info text-center">

<h3>

Semester

<?php

echo $exam['semester'];

?>

</h3>

Current Semester

</div>

</div>

</div>

<br>

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Admit Card Instructions

</h5>

</div>

<div class="card-body">

<ul class="list-group">

<li class="list-group-item">

✔ Student must bring Original Admit Card.

</li>

<li class="list-group-item">

✔ Student ID Card is compulsory.

</li>

<li class="list-group-item">

✔ Late arrival is not allowed.

</li>

<li class="list-group-item">

✔ Mobile Phones are strictly prohibited.

</li>

<li class="list-group-item">

✔ Examination Office reserves all rights.

</li>

</ul>

</div>

</div>

<br>

<div class="text-center">

<a

href="admit_cards.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back To Admit Cards

</a>

</div>

</div>

</div>

</body>

</html>