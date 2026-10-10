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

if($_SESSION['department']!="Admission Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*=========================================
REPORT COUNTS
=========================================*/

$totalApplications=$conn->query("
SELECT COUNT(*) total
FROM applications
")->fetch_assoc()['total'];

$totalApproved=$conn->query("
SELECT COUNT(*) total
FROM applications
WHERE application_status='Approved'
")->fetch_assoc()['total'];

$totalRejected=$conn->query("
SELECT COUNT(*) total
FROM applications
WHERE application_status='Rejected'
")->fetch_assoc()['total'];

$totalPending=$conn->query("
SELECT COUNT(*) total
FROM applications
WHERE application_status='Submitted'
OR application_status='Under Review'
")->fetch_assoc()['total'];

$totalSelected=$conn->query("
SELECT COUNT(*) total
FROM applications
WHERE application_status='Selected'
")->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Admission Reports</title>

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
overflow:auto;
}

.sidebar h3{
color:#fff;
text-align:center;
padding:20px;
border-bottom:1px solid rgba(255,255,255,.2);
}

.sidebar a{
display:block;
padding:14px 20px;
color:#fff;
text-decoration:none;
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
margin-bottom:25px;
display:flex;
justify-content:space-between;
align-items:center;
}

.report-card{
border:none;
border-radius:15px;
padding:25px;
text-align:center;
color:#fff;
box-shadow:0 5px 15px rgba(0,0,0,.15);
}

.report-card i{
font-size:40px;
margin-bottom:15px;
}

.report-card h2{
font-weight:bold;
}

</style>

</head>

<body>

<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php">
<i class="fas fa-home me-2"></i>
Dashboard
</a>

<a href="manage_applications.php">
<i class="fas fa-file-alt me-2"></i>
Manage Applications
</a>

<a href="merit_list.php">
<i class="fas fa-ranking-star me-2"></i>
Merit List
</a>

<a href="selected_students.php">
<i class="fas fa-user-check me-2"></i>
Selected Students
</a>

<a href="reports.php" class="active">
<i class="fas fa-chart-bar me-2"></i>
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

<a href="profile.php">
<i class="fas fa-user"></i>
Profile
</a>

<a href="../admins/logout.php">
<i class="fas fa-sign-out-alt me-2"></i>
Logout
</a>

</div>

<div class="main">

<div class="topbar">

<div>

<h3>Admission Reports</h3>

<small class="text-muted">

Admission Office

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

<div class="col-md-4 mb-4">

<div class="report-card bg-primary">

<i class="fas fa-file-alt"></i>

<h2>

<?php echo $totalApplications; ?>

</h2>

<h5>Total Applications</h5>

</div>

</div>

<div class="col-md-4 mb-4">

<div class="report-card bg-success">

<i class="fas fa-check-circle"></i>

<h2>

<?php echo $totalApproved; ?>

</h2>

<h5>Approved Applications</h5>

</div>

</div>

<div class="col-md-4 mb-4">

<div class="report-card bg-danger">

<i class="fas fa-times-circle"></i>

<h2>

<?php echo $totalRejected; ?>

</h2>

<h5>Rejected Applications</h5>

</div>

</div>

<div class="col-md-6 mb-4">

<div class="report-card bg-warning">

<i class="fas fa-hourglass-half"></i>

<h2>

<?php echo $totalPending; ?>

</h2>

<h5>Pending Applications</h5>

</div>

</div>

<div class="col-md-6 mb-4">

<div class="report-card bg-info">

<i class="fas fa-user-graduate"></i>

<h2>

<?php echo $totalSelected; ?>

</h2>

<h5>Selected Students</h5>

</div>

</div>

</div>
<?php

/*=========================================
FACULTY WISE REPORT
=========================================*/

$facultyReport=$conn->query("

SELECT

faculties.faculty_name,

COUNT(applications.id) total

FROM faculties

LEFT JOIN applications
ON applications.faculty_id=faculties.id

GROUP BY faculties.id

ORDER BY faculties.faculty_name

");

/*=========================================
PROGRAM WISE REPORT
=========================================*/

$programReport=$conn->query("

SELECT

programs.program_name,

COUNT(applications.id) total

FROM programs

LEFT JOIN applications
ON applications.program_id=programs.id

GROUP BY programs.id

ORDER BY programs.program_name

");

?>

<div class="row">

<!-- Faculty Report -->

<div class="col-lg-6 mb-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-building me-2"></i>

Faculty Wise Applications

</h5>

</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>Faculty</th>

<th>Total Applications</th>

</tr>

</thead>

<tbody>

<?php

while($row=$facultyReport->fetch_assoc()){

?>

<tr>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo $row['total']; ?>

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

<!-- Program Report -->

<div class="col-lg-6 mb-4">

<div class="card shadow border-0">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-graduation-cap me-2"></i>

Program Wise Applications

</h5>

</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>Program</th>

<th>Total Applications</th>

</tr>

</thead>

<tbody>

<?php

while($row=$programReport->fetch_assoc()){

?>

<tr>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<?php echo $row['total']; ?>

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

</div>

<!-- Print Buttons -->

<div class="text-center mt-4">

<button
onclick="window.print()"
class="btn btn-primary me-2">

<i class="fas fa-print me-2"></i>

Print Report

</button>

<button
class="btn btn-success me-2"
disabled>

<i class="fas fa-file-excel me-2"></i>

Export Excel

</button>

<button
class="btn btn-danger"
disabled>

<i class="fas fa-file-pdf me-2"></i>

Export PDF

</button>

</div>

<hr class="mt-5">

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>