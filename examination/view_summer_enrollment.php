<?php

session_start();

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
GET APPLICATION ID
=========================================*/

if(!isset($_GET['id'])){
    die("Invalid Request.");
}

$enrollment_id = (int)$_GET['id'];

/*=========================================
GET APPLICATION DETAILS
=========================================*/

$query = $conn->prepare("

SELECT

summer_enrollments.*,

students.first_name,
students.last_name,
students.registration_no,
students.roll_no,
students.semester,

programs.program_name,
faculties.faculty_name

FROM summer_enrollments

INNER JOIN students
ON summer_enrollments.student_id = students.id

INNER JOIN programs
ON students.program_id = programs.id

INNER JOIN faculties
ON programs.faculty_id = faculties.id

WHERE summer_enrollments.id = ?

LIMIT 1

");

$query->bind_param("i",$enrollment_id);

$query->execute();

$application = $query->get_result()->fetch_assoc();

if(!$application){
    die("Application Not Found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
View Summer Enrollment
</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{
background:#F4F6F9;
font-family:'Segoe UI',sans-serif;
}

.header-card{
background:linear-gradient(135deg,#0B3D91,#1565C0);
color:#fff;
padding:25px;
border-radius:18px;
box-shadow:0 5px 15px rgba(0,0,0,.15);
margin-bottom:25px;
}

.info-card{
background:#fff;
border-radius:18px;
padding:20px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
margin-bottom:20px;
}

.info-title{
font-size:22px;
font-weight:bold;
color:#0B3D91;
margin-bottom:20px;
}

.student-box{
background:#F8F9FA;
padding:15px;
border-radius:12px;
border-left:5px solid #1565C0;
height:100%;
}

.student-box h6{
margin-bottom:6px;
color:#666;
font-size:14px;
}

.student-box strong{
font-size:16px;
color:#222;
}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="header-card">

<h2>

<i class="fas fa-sun me-2"></i>

Summer Enrollment Application

</h2>

<p class="mb-0">

View Complete Student Summer Enrollment Details

</p>

</div>

<div class="info-card">

<h3 class="info-title">

<i class="fas fa-user-graduate me-2"></i>

Student Information

</h3>

<div class="row">

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Student Name</h6>

<strong>

<?php
echo htmlspecialchars($application['first_name']." ".$application['last_name']);
?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Registration No</h6>

<strong>

<?php echo htmlspecialchars($application['registration_no']); ?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Roll No</h6>

<strong>

<?php echo htmlspecialchars($application['roll_no']); ?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Faculty</h6>

<strong>

<?php echo htmlspecialchars($application['faculty_name']); ?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Program</h6>

<strong>

<?php echo htmlspecialchars($application['program_name']); ?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Semester</h6>

<strong>

Semester <?php echo $application['semester']; ?>

</strong>

</div>

</div>

<?php

session_start();

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
GET APPLICATION ID
=========================================*/

if(!isset($_GET['id'])){
    die("Invalid Request.");
}

$enrollment_id = (int)$_GET['id'];

/*=========================================
GET APPLICATION DETAILS
=========================================*/

$query = $conn->prepare("

SELECT

summer_enrollments.*,

students.first_name,
students.last_name,
students.registration_no,
students.roll_no,
students.semester,

programs.program_name,
faculties.faculty_name

FROM summer_enrollments

INNER JOIN students
ON summer_enrollments.student_id = students.id

INNER JOIN programs
ON students.program_id = programs.id

INNER JOIN faculties
ON programs.faculty_id = faculties.id

WHERE summer_enrollments.id = ?

LIMIT 1

");

$query->bind_param("i",$enrollment_id);

$query->execute();

$application = $query->get_result()->fetch_assoc();

if(!$application){
    die("Application Not Found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
View Summer Enrollment
</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{
background:#F4F6F9;
font-family:'Segoe UI',sans-serif;
}

.header-card{
background:linear-gradient(135deg,#0B3D91,#1565C0);
color:#fff;
padding:25px;
border-radius:18px;
box-shadow:0 5px 15px rgba(0,0,0,.15);
margin-bottom:25px;
}

.info-card{
background:#fff;
border-radius:18px;
padding:20px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
margin-bottom:20px;
}

.info-title{
font-size:22px;
font-weight:bold;
color:#0B3D91;
margin-bottom:20px;
}

.student-box{
background:#F8F9FA;
padding:15px;
border-radius:12px;
border-left:5px solid #1565C0;
height:100%;
}

.student-box h6{
margin-bottom:6px;
color:#666;
font-size:14px;
}

.student-box strong{
font-size:16px;
color:#222;
}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="header-card">

<h2>

<i class="fas fa-sun me-2"></i>

Summer Enrollment Application

</h2>

<p class="mb-0">

View Complete Student Summer Enrollment Details

</p>

</div>

<div class="info-card">

<h3 class="info-title">

<i class="fas fa-user-graduate me-2"></i>

Student Information

</h3>

<div class="row">

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Student Name</h6>

<strong>

<?php
echo htmlspecialchars($application['first_name']." ".$application['last_name']);
?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Registration No</h6>

<strong>

<?php echo htmlspecialchars($application['registration_no']); ?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Roll No</h6>

<strong>

<?php echo htmlspecialchars($application['roll_no']); ?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Faculty</h6>

<strong>

<?php echo htmlspecialchars($application['faculty_name']); ?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Program</h6>

<strong>

<?php echo htmlspecialchars($application['program_name']); ?>

</strong>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Semester</h6>

<strong>

Semester <?php echo $application['semester']; ?>

</strong>

</div>

</div>

<!-- =========================================
APPLICATION SUMMARY
========================================= -->

<div class="info-card">

<h3 class="info-title">

<i class="fas fa-file-circle-check me-2"></i>

Application Summary

</h3>


<div class="row">


<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Summer Session</h6>

<strong>

<?php 

echo htmlspecialchars($application['summer_session']);

?>

</strong>

</div>

</div>



<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Total Subjects</h6>

<strong>

<?php 

echo $application['total_subjects'];

?>

 Subjects

</strong>

</div>

</div>



<div class="col-md-4 mb-3">

<div class="student-box">

<h6>Application Date</h6>

<strong>

<?php

echo date(

"d M Y",

strtotime($application['created_at'])

);

?>

</strong>

</div>

</div>


</div>


</div>



<!-- =========================================
CURRENT STATUS
========================================= -->

<div class="info-card">


<h3 class="info-title">

<i class="fas fa-clock me-2"></i>

Application Status

</h3>



<div class="text-center p-4">


<?php


$status = $application['status'];


if($status=="Pending"){

$badge="warning";

$icon="fa-hourglass-half";

}

elseif($status=="Approved"){

$badge="success";

$icon="fa-circle-check";

}

else{

$badge="danger";

$icon="fa-circle-xmark";

}


?>


<div>

<i class="fas <?php echo $icon; ?> fa-4x text-<?php echo $badge; ?> mb-3"></i>

</div>



<h3>


<span class="badge bg-<?php echo $badge; ?> px-4 py-3">


<?php echo $status; ?>


</span>


</h3>



<p class="text-muted mt-3">


<?php


if($status=="Pending"){

echo "Application is waiting for Examination Office approval.";

}

elseif($status=="Approved"){

echo "Application approved. Student can proceed for Summer Fee Payment.";

}

else{

echo "Application has been rejected by Examination Office.";

}


?>


</p>



</div>


</div>

<!-- =========================================
EXAMINATION ACTIONS
========================================= -->

<div class="info-card">

<h3 class="info-title">

<i class="fas fa-user-shield me-2"></i>

Examination Office Decision

</h3>


<form action="process_summer_enrollment.php" method="POST">


<input 
type="hidden"
name="enrollment_id"
value="<?php echo $enrollment_id; ?>">



<div class="row">


<div class="col-md-12 mb-3">


<label class="form-label fw-bold">

Remarks (Optional)

</label>


<textarea

name="remarks"

class="form-control"

rows="4"

placeholder="Enter approval or rejection remarks..."

></textarea>


</div>



</div>



<?php

if($application['status']=="Pending"){

?>

<div class="text-center mt-3">


<button

type="submit"

name="action"

value="Approved"

class="btn btn-success btn-lg px-5 me-3"

>

<i class="fas fa-check-circle me-2"></i>

Approve

</button>



<button

type="submit"

name="action"

value="Rejected"

class="btn btn-danger btn-lg px-5"

>

<i class="fas fa-times-circle me-2"></i>

Reject

</button>


</div>


<?php

}

else{

?>

<div class="alert alert-info text-center">


<i class="fas fa-info-circle me-2"></i>


Decision already submitted.


</div>


<?php

}

?>


</form>


</div>

<!-- =========================================
FOOTER
========================================= -->

<footer class="mt-5 mb-3">

<div class="card border-0 shadow">

<div class="card-body">

<div class="row align-items-center">


<div class="col-md-6">

<h5 class="fw-bold text-primary mb-1">

Global Tech University ERP

</h5>

<p class="text-muted mb-0">

Examination Office - Summer Enrollment Management

</p>

</div>



<div class="col-md-6 text-md-end mt-3 mt-md-0">


<strong>

Version 1.0

</strong>

<br>


<span class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University

</span>


</div>


</div>

</div>

</div>

</footer>



</div>
<!-- Container End -->


<script

src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">

</script>


</body>

</html>