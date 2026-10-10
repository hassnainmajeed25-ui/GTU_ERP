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

if($_SESSION['department']!="Student Affairs"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_id=$_SESSION['admin_id'];
$admin_name=$_SESSION['admin_name'];

/*=========================================
TOTAL STUDENTS
=========================================*/

$totalStudents=0;

$q1=$conn->query("SELECT COUNT(*) total FROM students");

if($q1){

$r1=$q1->fetch_assoc();

$totalStudents=$r1['total'];

}

/*=========================================
ACTIVE STUDENTS
=========================================*/

$activeStudents=0;

$q2=$conn->query("SELECT COUNT(*) total FROM students WHERE status='Active'");

if($q2){

$r2=$q2->fetch_assoc();

$activeStudents=$r2['total'];

}

/*=========================================
TOTAL APPLICATIONS
=========================================*/

$totalApplications=0;

$q3=$conn->query("SELECT COUNT(*) total FROM applications");

if($q3){

$r3=$q3->fetch_assoc();

$totalApplications=$r3['total'];

}

/*=========================================
TOTAL PROGRAMS
=========================================*/

$totalPrograms=0;

$q4=$conn->query("SELECT COUNT(*) total FROM programs");

if($q4){

$r4=$q4->fetch_assoc();

$totalPrograms=$r4['total'];

}

/*=========================================
TOTAL FACULTIES
=========================================*/

$totalFaculties=0;

$q5=$conn->query("SELECT COUNT(*) total FROM faculties");

if($q5){

$r5=$q5->fetch_assoc();

$totalFaculties=$r5['total'];

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Student Affairs Dashboard</title>

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

padding-top:20px;

overflow-y:auto;

overflow-x:hidden;

}

.sidebar h3{

color:#fff;

text-align:center;

margin-bottom:25px;

}

.sidebar a{

display:block;

padding:14px 20px;

color:#fff;

text-decoration:none;

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

box-shadow:0 3px 10px rgba(0,0,0,.1);

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:25px;

}

.card-box{

border:none;

border-radius:15px;

box-shadow:0 3px 10px rgba(0,0,0,.1);

padding:25px;

text-align:center;

background:#fff;

}

.card-box i{

font-size:40px;

margin-bottom:15px;

color:#0B1F52;

}

</style>

</head>

<body>

<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php" class="active">
<i class="fas fa-home me-2"></i>
Dashboard
</a>

<a href="manage_students.php">
<i class="fas fa-user-graduate me-2"></i>
Manage Students
</a>

<a href="student_requests.php">
<i class="fas fa-file-signature me-2"></i>
Student Requests
</a>

<a href="certificate_requests.php">
<i class="fas fa-certificate me-2"></i>
Certificate Requests
</a>

<a href="complaints.php">
<i class="fas fa-triangle-exclamation me-2"></i>
Complaints
</a>

<a href="announcements.php">
<i class="fas fa-bullhorn me-2"></i>
Announcements
</a>

<a href="reports.php">
<i class="fas fa-chart-column me-2"></i>
Reports
</a>

<a href="compose_message.php">
<i class="fas fa-paper-plane me-2"></i>
Compose Message
</a>

<a href="inbox.php">
<i class="fas fa-envelope me-2"></i>
Inbox
</a>

<a href="sent_messages.php">
<i class="fas fa-share"></i>
Sent Messages
</a>

<a href="student_inbox.php">
<i class="fas fa-share"></i>
Student Messages
</a>


<a href="profile.php">
<i class="fas fa-user-circle me-2"></i>
Profile
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

Student Affairs Dashboard

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

<div class="row">

<div class="col-md-3 mb-4">

<div class="card-box">

<i class="fas fa-user-graduate"></i>

<h2>

<?php echo $totalStudents; ?>

</h2>

<h5>

Total Students

</h5>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card-box">

<i class="fas fa-user-check"></i>

<h2>

<?php echo $activeStudents; ?>

</h2>

<h5>

Active Students

</h5>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card-box">

<i class="fas fa-file-lines"></i>

<h2>

<?php echo $totalApplications; ?>

</h2>

<h5>

Applications

</h5>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card-box">

<i class="fas fa-graduation-cap"></i>

<h2>

<?php echo $totalPrograms; ?>

</h2>

<h5>

Programs

</h5>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card-box">

<i class="fas fa-building"></i>

<h2>

<?php echo $totalFaculties; ?>

</h2>

<h5>

Faculties

</h5>

</div>

</div>

</div>

<!-- ============================ -->
<!-- QUICK ACTIONS -->
<!-- ============================ -->

<div class="card mt-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-bolt me-2"></i>

Quick Actions

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3 mb-3">

<a href="manage_students.php" class="btn btn-primary w-100 p-3">

<i class="fas fa-user-graduate fa-2x"></i>

<br><br>

Manage Students

</a>

</div>

<div class="col-md-3 mb-3">

<a href="student_requests.php" class="btn btn-success w-100 p-3">

<i class="fas fa-file-signature fa-2x"></i>

<br><br>

Student Requests

</a>

<div class="col-md-3 mb-3">

<a href="certificate_requests.php" class="btn btn-primary w-100 p-3">

<i class="fas fa-certificate fa-2x mb-2"></i>

<br>

Certificate Requests

</a>

</div>

</div>

<div class="col-md-3 mb-3">

<a href="complaints.php" class="btn btn-warning w-100 p-3 text-white">

<i class="fas fa-triangle-exclamation fa-2x"></i>

<br><br>

Complaints

</a>

</div>

<div class="col-md-3 mb-3">

<a href="announcements.php" class="btn btn-danger w-100 p-3">

<i class="fas fa-bullhorn fa-2x"></i>

<br><br>

Announcements

</a>

</div>

</div>

</div>

</div>

<!-- ============================ -->
<!-- LATEST STUDENTS -->
<!-- ============================ -->

<div class="card mt-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-user-graduate me-2"></i>

Latest Students

</h5>

</div>

<div class="card-body">

<?php

$students=$conn->query("

SELECT *

FROM students

ORDER BY id DESC

LIMIT 10

");

?>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>ID</th>

<th>Name</th>

<th>Father Name</th>

<th>Email</th>

<th>Mobile</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

if($students && $students->num_rows>0){

while($row=$students->fetch_assoc()){

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['father_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['email']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['mobile']); ?>

</td>

<td>

<?php

if($row['status']=="Active"){

?>

<span class="badge bg-success">

Active

</span>

<?php

}else{

?>

<span class="badge bg-danger">

Inactive

</span>

<?php

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center text-danger">

No Students Found

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

<!-- ========================================= -->
<!-- LATEST APPLICATIONS -->
<!-- ========================================= -->

<div class="card mt-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-file-alt me-2"></i>

Latest Applications

</h5>

</div>

<div class="card-body">

<?php

$applications=$conn->query("

SELECT

applications.id,
applications.application_id,
applications.application_status,

students.first_name,
students.last_name,

programs.program_name

FROM applications

LEFT JOIN students
ON students.id=applications.student_id

LEFT JOIN programs
ON programs.id=applications.program_id

ORDER BY applications.id DESC

LIMIT 10

");

?>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>ID</th>

<th>Application ID</th>

<th>Student</th>

<th>Program</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

if($applications && $applications->num_rows>0){

while($row=$applications->fetch_assoc()){

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['application_id']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<?php

$status=$row['application_status'];

if($status=="Approved"){

echo "<span class='badge bg-success'>$status</span>";

}elseif($status=="Rejected"){

echo "<span class='badge bg-danger'>$status</span>";

}elseif($status=="Under Review"){

echo "<span class='badge bg-warning text-dark'>$status</span>";

}else{

echo "<span class='badge bg-secondary'>$status</span>";

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="5" class="text-center text-danger">

No Applications Found

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

<!-- ========================================= -->
<!-- DASHBOARD SUMMARY -->
<!-- ========================================= -->

<div class="row mt-4">

<div class="col-md-4">

<div class="card text-center shadow">

<div class="card-body">

<i class="fas fa-users fa-3x text-primary mb-3"></i>

<h3>

<?php echo $totalStudents; ?>

</h3>

<p>Total Students</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card text-center shadow">

<div class="card-body">

<i class="fas fa-user-check fa-3x text-success mb-3"></i>

<h3>

<?php echo $activeStudents; ?>

</h3>

<p>Active Students</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card text-center shadow">

<div class="card-body">

<i class="fas fa-file-alt fa-3x text-warning mb-3"></i>

<h3>

<?php echo $totalApplications; ?>

</h3>

<p>Total Applications</p>

</div>

</div>

</div>

</div>

<hr class="mt-5">

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

<p class="text-muted">

Student Affairs Office Dashboard

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>