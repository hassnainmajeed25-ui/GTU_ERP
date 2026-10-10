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

if($_SESSION['department']!="Academic Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*=========================================
DASHBOARD COUNTS
=========================================*/

$totalPrograms=0;
$totalSubjects=0;
$totalTeachers=0;
$totalFaculties=0;

$q=$conn->query("SELECT COUNT(*) total FROM programs WHERE status='Active'");
if($q){
$row=$q->fetch_assoc();
$totalPrograms=$row['total'];
}

$q=$conn->query("SELECT COUNT(*) total FROM subjects WHERE status='Active'");
if($q){
$row=$q->fetch_assoc();
$totalSubjects=$row['total'];
}

$q=$conn->query("SELECT COUNT(*) total FROM teachers WHERE status='Active'");
if($q){
$row=$q->fetch_assoc();
$totalTeachers=$row['total'];
}

$q=$conn->query("SELECT COUNT(*) total FROM faculties WHERE status='Active'");
if($q){
$row=$q->fetch_assoc();
$totalFaculties=$row['total'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>

Academic Office Dashboard

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;
font-family:Arial,Helvetica,sans-serif;

}

.sidebar{

position:fixed;
left:0;
top:0;
width:260px;
height:100%;
background:#0B1F52;
overflow:auto;

}

.sidebar h3{

color:#fff;
text-align:center;
padding:20px;
border-bottom:1px solid rgba(255,255,255,.15);

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

.sidebar .active{

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
box-shadow:0 5px 15px rgba(0,0,0,.08);
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;

}

.card{

border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);

}

.stat-card{

color:#fff;
border-radius:15px;
padding:25px;

}

.stat-card h2{

font-weight:bold;

}

</style>

</head>

<body>

<div class="sidebar">

<h3>

GTU ERP

</h3>

<a href="dashboard.php" class="active">

<i class="fas fa-home me-2"></i>

Dashboard

</a>

<a href="manage_subjects.php">

<i class="fas fa-book me-2"></i>

Manage Subjects

</a>

<a href="assign_subjects.php">

<i class="fas fa-user-check me-2"></i>

Assign Subjects

</a>

<li class="nav-item">

<a href="assign_student_semester.php">

<i class="fas fa-user-graduate me-2"></i>

Assign Student Semester

</a>

<li class="nav-item">

<a href="manage_timetable.php"

class="nav-link">

<i class="fas fa-calendar-alt me-2"></i>

Teacher Timetable

</a>

</li>

<a href="manage_programs.php">

<i class="fas fa-graduation-cap me-2"></i>

Programs

</a>

<a href="manage_teachers.php">

<i class="fas fa-chalkboard-teacher me-2"></i>

Teachers

</a>

<a href="reports.php">

<i class="fas fa-chart-bar me-2"></i>

Reports

</a>


<a href="manage_announcements.php">

<i class="fas fa-bullhorn"></i>

Teacher Announcements

</a>
<a href="compose_message.php">

<i class="fas fa-paper-plane me-2"></i>

Compose Message

</a>

<a href="inbox.php">

<i class="fas fa-envelope me-2"></i>

Inbox

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

Academic Office Dashboard

</h3>

<small class="text-muted">

Global Tech University ERP System

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>

<!-- =========================================
DASHBOARD STATISTICS
========================================= -->

<div class="row">

<div class="col-lg-3 col-md-6 mb-4">

<div class="stat-card bg-primary">

<i class="fas fa-book fa-2x mb-3"></i>

<h2>

<?php echo $totalSubjects; ?>

</h2>

<h5>

Total Subjects

</h5>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="stat-card bg-success">

<i class="fas fa-user-tie fa-2x mb-3"></i>

<h2>

<?php echo $totalTeachers; ?>

</h2>

<h5>

Total Teachers

</h5>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="stat-card bg-warning">

<i class="fas fa-graduation-cap fa-2x mb-3"></i>

<h2>

<?php echo $totalPrograms; ?>

</h2>

<h5>

Total Programs

</h5>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="stat-card bg-danger">

<i class="fas fa-building fa-2x mb-3"></i>

<h2>

<?php echo $totalFaculties; ?>

</h2>

<h5>

Total Faculties

</h5>

</div>

</div>

</div>

<!-- =========================================
QUICK ACTIONS
========================================= -->

<div class="card mb-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-bolt me-2"></i>

Quick Actions

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3 mb-3">

<a href="manage_subjects.php" class="btn btn-outline-primary w-100">

<i class="fas fa-book mb-2 d-block fa-2x"></i>

Manage Subjects

</a>

</div>

<div class="col-md-3 mb-3">

<a href="assign_subjects.php" class="btn btn-outline-success w-100">

<i class="fas fa-user-check mb-2 d-block fa-2x"></i>

Assign Subjects

</a>

</div>

<div class="col-md-3 mb-3">

<a href="manage_curriculum.php" class="btn btn-outline-warning w-100">

<i class="fas fa-sitemap mb-2 d-block fa-2x"></i>

Curriculum

</a>

</div>

<div class="col-md-3 mb-3">

<a href="reports.php" class="btn btn-outline-danger w-100">

<i class="fas fa-chart-bar mb-2 d-block fa-2x"></i>

Reports

</a>

</div>

</div>

</div>

</div>

<br>

<!-- ===========================
RECENT SUBJECTS
=========================== -->

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-book me-2"></i>

Recently Added Subjects

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Subject Code</th>

<th>Subject Name</th>

<th>Program</th>

<th>Semester</th>

<th>Credit Hours</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$subjects=$conn->query("

SELECT
subjects.*,
programs.program_name

FROM subjects

LEFT JOIN programs
ON programs.id=subjects.program_id

ORDER BY subjects.id DESC

LIMIT 10

");

$no=1;

while($row=$subjects->fetch_assoc()){

?>

<tr>

<td><?php echo $no++; ?></td>

<td><?php echo htmlspecialchars($row['subject_code']); ?></td>

<td><?php echo htmlspecialchars($row['subject_name']); ?></td>

<td><?php echo htmlspecialchars($row['program_name']); ?></td>

<td><?php echo $row['semester']; ?></td>

<td><?php echo $row['credit_hours']; ?></td>

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

?>

</tbody>

</table>

</div>

</div>

</div>

<br>

<!-- ===========================
PROGRAM WISE SUBJECTS
=========================== -->

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-graduation-cap me-2"></i>

Program Wise Subjects

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Program</th>

<th>Total Subjects</th>

</tr>

</thead>

<tbody>

<?php

$programs=$conn->query("

SELECT
programs.program_name,
COUNT(subjects.id) AS total_subjects

FROM programs

LEFT JOIN subjects
ON subjects.program_id=programs.id

GROUP BY programs.id

ORDER BY programs.program_name ASC

");

$no=1;

while($program=$programs->fetch_assoc()){

?>

<tr>

<td><?php echo $no++; ?></td>

<td><?php echo htmlspecialchars($program['program_name']); ?></td>

<td>

<span class="badge bg-primary">

<?php echo $program['total_subjects']; ?>

</span>

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

<hr class="mt-5">

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?> Global Tech University ERP System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>