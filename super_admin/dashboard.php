<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['super_admin_name'];

/*=====================================
TOTAL STUDENTS
=====================================*/

$students=0;

$q1=$conn->query("SELECT COUNT(*) AS total FROM students");

if($q1){

$r1=$q1->fetch_assoc();

$students=$r1['total'];

}

/*=====================================
TOTAL APPLICATIONS
=====================================*/

$applications=0;

$q2=$conn->query("SELECT COUNT(*) AS total FROM applications");

if($q2){

$r2=$q2->fetch_assoc();

$applications=$r2['total'];

}

/*=====================================
TOTAL PROGRAMS
=====================================*/

$programs=0;

$q3=$conn->query("SELECT COUNT(*) AS total FROM programs");

if($q3){

$r3=$q3->fetch_assoc();

$programs=$r3['total'];

}

/*=====================================
TOTAL FACULTIES
=====================================*/

$faculties=0;

$q4=$conn->query("SELECT COUNT(*) AS total FROM faculties");

if($q4){

$r4=$q4->fetch_assoc();

$faculties=$r4['total'];

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Super Admin Dashboard</title>

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

.sidebar::-webkit-scrollbar{

width:8px;

}

.sidebar::-webkit-scrollbar-track{

background:#0B1F52;

}

.sidebar::-webkit-scrollbar-thumb{

background:#4b6cb7;

border-radius:10px;

}

.sidebar::-webkit-scrollbar-thumb:hover{

background:#6d8de3;

}

.sidebar h3{

color:white;

text-align:center;

margin-bottom:30px;

}

.sidebar a{

display:block;

padding:14px 20px;

color:white;

text-decoration:none;

transition:.3s;

}

.sidebar a:hover{

background:#16367d;

}

.main{

margin-left:260px;

padding:30px;

}

.topbar{

background:white;

padding:15px 25px;

border-radius:12px;

box-shadow:0 3px 10px rgba(0,0,0,.1);

margin-bottom:30px;

display:flex;

justify-content:space-between;

align-items:center;

}

.card-box{

border:none;

border-radius:15px;

box-shadow:0 4px 15px rgba(0,0,0,.12);

padding:25px;

text-align:center;

background:white;

}

.card-box i{

font-size:45px;

margin-bottom:15px;

color:#0B1F52;

}

</style>

</head>

<body>

<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>

<a href="manage_admins.php"><i class="fas fa-user-shield me-2"></i> Manage Admins</a>

<a href="#"><i class="fas fa-building me-2"></i> Departments</a>

<a href="#"><i class="fas fa-user-graduate me-2"></i> Students</a>

<a href="manage_applications.php"><i class="fas fa-file-alt me-2"></i> Applications</a>



<a href="university_settings.php" class="nav-link">
<i class="fas fa-university me-2"></i>

University Settings

</a>

<a href="manage_teachers.php">
    <i class="fas fa-chalkboard-teacher me-2"></i>
    Manage Teachers
</a>

<a href="teacher_profile.php">
    <i class="fas fa-chalkboard-teacher me-2"></i>
    Teacher Profile 
</a>

<a href="manage_faculties.php" class="nav-link">

<i class="fas fa-building me-2"></i>

Manage Faculties

</a>

<li class="nav-item">

<a href="manage_programs.php" class="nav-link">

<i class="fas fa-graduation-cap me-2"></i>

Manage Programs

</a>

</li>

<a href="manage_sessions.php" class="nav-link">

<i class="fas fa-calendar-alt me-2"></i>

Admission Sessions

</a>

<a href="manage_fee_structure.php" class="nav-link">

<i class="fas fa-money-check-dollar me-2"></i>

Manage Fee Structure

</a>

<li class="nav-item">

<a href="manage_scholarships.php" class="nav-link">

<i class="fas fa-award"></i>

<span>Scholarships</span>

</a>

</li>

<li class="nav-item">

<a href="super_admin_notifications.php" class="nav-link">

<i class="fas fa-award"></i>

<span>Student Notifications</span>

</a>

</li>



<li class="nav-item">

<a href="inbox.php" class="nav-link">

<i class="fas fa-envelope"></i>

<span>Inbox</span>

</a>

</li>

<li class="nav-item">

<a href="compose_message.php" class="nav-link">

<i class="fas fa-paper-plane"></i>

<span>Compose Message</span>

</a>

</li>

<a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>

</div>

<div class="main">

<div class="topbar">

<h4>

Welcome,

<?php echo $admin_name; ?>

</h4>

<span>

Super Administrator

</span>

</div>

<div class="row">
    <div class="col-md-3 mb-4">

<div class="card-box">

<i class="fas fa-user-graduate"></i>

<h2>

<?php echo $students; ?>

</h2>

<h5>Total Students</h5>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card-box">

<i class="fas fa-file-alt"></i>

<h2>

<?php echo $applications; ?>

</h2>

<h5>Total Applications</h5>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card-box">

<i class="fas fa-book"></i>

<h2>

<?php echo $programs; ?>

</h2>

<h5>Total Programs</h5>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card-box">

<i class="fas fa-building"></i>

<h2>

<?php echo $faculties; ?>

</h2>

<h5>Total Faculties</h5>

</div>

</div>

</div>

<!-- Quick Actions -->

<div class="card mt-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

Quick Actions

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3 mb-3">

<a href="#" class="btn btn-primary w-100">

<i class="fas fa-user-plus"></i>

<br>

Create Admin

</a>

</div>

<div class="col-md-3 mb-3">

<a href="#" class="btn btn-success w-100">

<i class="fas fa-building"></i>

<br>

Departments

</a>

</div>

<div class="col-md-3 mb-3">

<a href="#" class="btn btn-warning w-100">

<i class="fas fa-graduation-cap"></i>

<br>

Programs

</a>

</div>

<div class="col-md-3 mb-3">

<a href="#" class="btn btn-danger w-100">

<i class="fas fa-chart-bar"></i>

<br>

Reports

</a>

</div>

</div>

</div>

</div>

<!-- Recent Activities -->

<div class="card mt-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

Recent Activities

</h5>

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>

<th>ID</th>

<th>Activity</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<tr>

<td>1</td>

<td>Super Admin Logged In</td>

<td>

<span class="badge bg-success">

Success

</span>

</td>

</tr>

<tr>

<td>2</td>

<td>Dashboard Loaded</td>

<td>

<span class="badge bg-primary">

Running

</span>

</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>