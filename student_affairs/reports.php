<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Student Affairs"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*==============================
TOTAL STUDENTS
==============================*/

$totalStudents=0;

$q1=$conn->query("SELECT COUNT(*) total FROM students");

if($q1){

$totalStudents=$q1->fetch_assoc()['total'];

}

/*==============================
CERTIFICATE REQUESTS
==============================*/

$totalCertificates=0;

$q2=$conn->query("SELECT COUNT(*) total FROM certificate_requests");

if($q2){

$totalCertificates=$q2->fetch_assoc()['total'];

}

/*==============================
COMPLAINTS
==============================*/

$totalComplaints=0;

$q3=$conn->query("SELECT COUNT(*) total FROM complaints");

if($q3){

$totalComplaints=$q3->fetch_assoc()['total'];

}

/*==============================
ANNOUNCEMENTS
==============================*/

$totalAnnouncements=0;

$q4=$conn->query("SELECT COUNT(*) total FROM announcements");

if($q4){

$totalAnnouncements=$q4->fetch_assoc()['total'];

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Student Affairs Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef4fb;

font-family:Segoe UI,sans-serif;

}

.main{

padding:35px;

}

.top-card{

background:linear-gradient(135deg,#0B1F52,#2563eb);

color:white;

border-radius:20px;

padding:25px;

box-shadow:0 15px 35px rgba(0,0,0,.15);

}

.logo{

width:75px;

}

.stat-card{

border:none;

border-radius:18px;

padding:25px;

color:white;

transition:.3s;

}

.stat-card:hover{

transform:translateY(-8px);

}

</style>

</head>

<body>

<div class="container-fluid main">

<div class="top-card">

<div class="d-flex justify-content-between align-items-center">

<div class="d-flex align-items-center">

<img

src="../assets/images/logo.png"

class="logo me-4"

alt="GTU Logo">

<div>

<h2 class="fw-bold mb-1">

Student Affairs Reports Dashboard

</h2>

<p class="mb-0">

Global Tech University ERP System

</p>

</div>

</div>

<div class="text-end">

<h5>

Welcome,

<?php echo htmlspecialchars($admin_name); ?>

</h5>

<small>

<?php echo date("l, d F Y"); ?>

</small>

</div>

</div>

</div>

<div class="row mt-4 g-4">

<div class="col-lg-3 col-md-6">

<div class="stat-card"

style="background:linear-gradient(135deg,#0d6efd,#4dabff);">

<div class="d-flex justify-content-between">

<div>

<h6>Total Students</h6>

<h1>

<?php echo $totalStudents; ?>

</h1>

</div>

<i class="fas fa-user-graduate fa-4x opacity-75"></i>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="stat-card"

style="background:linear-gradient(135deg,#198754,#5dd39e);">

<div class="d-flex justify-content-between">

<div>

<h6>Certificates</h6>

<h1>

<?php echo $totalCertificates; ?>

</h1>

</div>

<i class="fas fa-certificate fa-4x opacity-75"></i>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="stat-card"

style="background:linear-gradient(135deg,#dc3545,#ff7b89);">

<div class="d-flex justify-content-between">

<div>

<h6>Complaints</h6>

<h1>

<?php echo $totalComplaints; ?>

</h1>

</div>

<i class="fas fa-triangle-exclamation fa-4x opacity-75"></i>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="stat-card"

style="background:linear-gradient(135deg,#fd7e14,#ffb347);">

<div class="d-flex justify-content-between">

<div>

<h6>Announcements</h6>

<h1>

<?php echo $totalAnnouncements; ?>

</h1>

</div>

<i class="fas fa-bullhorn fa-4x opacity-75"></i>

</div>

</div>

</div>

</div>

<div class="card border-0 shadow-lg mt-5">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">

<h3 class="mb-0">

<i class="fas fa-chart-line me-2"></i>

Report Center

</h3>

</div>

<div class="card-body">

<div class="row g-4">

<div class="col-lg-4">

<div class="card h-100 border-0 shadow-sm">

<div class="card-body text-center">

<i class="fas fa-user-graduate fa-4x text-primary mb-3"></i>

<h4>

Student Report

</h4>

<p class="text-muted">

Generate complete student records and summary reports.

</p>

<a

href="student_report.php"

class="btn btn-primary">

<i class="fas fa-file-lines me-2"></i>

Generate Report

</a>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card h-100 border-0 shadow-sm">

<div class="card-body text-center">

<i class="fas fa-certificate fa-4x text-success mb-3"></i>

<h4>

Certificate Report

</h4>

<p class="text-muted">

View approved, pending and completed certificates.

</p>

<a

href="certificate_report.php"

class="btn btn-success">

<i class="fas fa-file-lines me-2"></i>

Generate Report

</a>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card h-100 border-0 shadow-sm">

<div class="card-body text-center">

<i class="fas fa-triangle-exclamation fa-4x text-danger mb-3"></i>

<h4>

Complaint Report

</h4>

<p class="text-muted">

Generate complaints summary and status report.

</p>

<a

href="complaint_report.php"

class="btn btn-danger">

<i class="fas fa-file-lines me-2"></i>

Generate Report

</a>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card h-100 border-0 shadow-sm">

<div class="card-body text-center">

<i class="fas fa-bullhorn fa-4x text-warning mb-3"></i>

<h4>

Announcement Report

</h4>

<p class="text-muted">

Generate announcements history report.

</p>

<a

href="announcement_report.php"

class="btn btn-warning text-dark">

<i class="fas fa-file-lines me-2"></i>

Generate Report

</a>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card h-100 border-0 shadow-sm">

<div class="card-body text-center">

<i class="fas fa-envelope fa-4x text-info mb-3"></i>

<h4>

Message Report

</h4>

<p class="text-muted">

Generate inbox and sent messages reports.

</p>

<a

href="message_report.php"

class="btn btn-info text-white">

<i class="fas fa-file-lines me-2"></i>

Generate Report

</a>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card h-100 border-0 shadow-sm">

<div class="card-body text-center">

<i class="fas fa-chart-pie fa-4x text-secondary mb-3"></i>

<h4>

Overall Report

</h4>

<p class="text-muted">

Generate complete Student Affairs Office report.

</p>

<a

href="overall_report.php"

class="btn btn-secondary">

<i class="fas fa-chart-column me-2"></i>

Generate Report

</a>

</div>

</div>

</div>

</div>

</div>

</div>

<div class="row mt-5">

<div class="col-lg-8">

<div class="card border-0 shadow-lg">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#198754,#20c997);">

<h4 class="mb-0">

<i class="fas fa-filter me-2"></i>

Report Filters

</h4>

</div>

<div class="card-body">

<form action="#" method="GET">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label fw-bold">

From Date

</label>

<input

type="date"

name="from_date"

class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label fw-bold">

To Date

</label>

<input

type="date"

name="to_date"

class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label fw-bold">

Report Type

</label>

<select

name="report"

class="form-select">

<option value="">Select Report</option>

<option>Student Report</option>

<option>Certificate Report</option>

<option>Complaint Report</option>

<option>Announcement Report</option>

<option>Message Report</option>

<option>Overall Report</option>

</select>

</div>

<div class="col-md-6 d-flex align-items-end">

<button

type="submit"

class="btn btn-success w-100">

<i class="fas fa-magnifying-glass me-2"></i>

Generate Report

</button>

</div>

</div>

</form>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow-lg">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">

<h4 class="mb-0">

<i class="fas fa-download me-2"></i>

Export Center

</h4>

</div>

<div class="card-body d-grid gap-3">

<a

href="#"

class="btn btn-danger btn-lg">

<i class="fas fa-file-pdf me-2"></i>

Export PDF

</a>

<a

href="#"

class="btn btn-success btn-lg">

<i class="fas fa-file-excel me-2"></i>

Export Excel

</a>

<a

href="#"

onclick="window.print();"

class="btn btn-primary btn-lg">

<i class="fas fa-print me-2"></i>

Print Report

</a>

</div>

</div>

</div>

</div>

<div class="row mt-5">

<div class="col-lg-8">

<div class="card border-0 shadow-lg">

<div class="card-header bg-dark text-white">

<h4 class="mb-0">

<i class="fas fa-clock-rotate-left me-2"></i>

Recent Report Activities

</h4>

</div>

<div class="card-body">

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Date</th>

<th>Report</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<tr>

<td><?php echo date("d M Y"); ?></td>

<td>Student Report Generated</td>

<td>

<span class="badge bg-success">

Completed

</span>

</td>

</tr>

<tr>

<td><?php echo date("d M Y"); ?></td>

<td>Certificate Report Generated</td>

<td>

<span class="badge bg-primary">

Ready

</span>

</td>

</tr>

<tr>

<td><?php echo date("d M Y"); ?></td>

<td>Complaint Report Generated</td>

<td>

<span class="badge bg-warning text-dark">

Pending

</span>

</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow-lg">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#6f42c1,#8e44ad);">

<h4 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

System Information

</h4>

</div>

<div class="card-body">

<p>

<i class="fas fa-user-graduate text-primary me-2"></i>

Students :

<strong>

<?php echo $totalStudents; ?>

</strong>

</p>

<p>

<i class="fas fa-certificate text-success me-2"></i>

Certificates :

<strong>

<?php echo $totalCertificates; ?>

</strong>

</p>

<p>

<i class="fas fa-triangle-exclamation text-danger me-2"></i>

Complaints :

<strong>

<?php echo $totalComplaints; ?>

</strong>

</p>

<p>

<i class="fas fa-bullhorn text-warning me-2"></i>

Announcements :

<strong>

<?php echo $totalAnnouncements; ?>

</strong>

</p>

<hr>

<p class="mb-0">

<i class="fas fa-calendar-days text-secondary me-2"></i>

<?php echo date("l, d F Y"); ?>

</p>

</div>

</div>

</div>

</div>

<hr class="mt-5">

<footer class="text-center py-4">

<img

src="../assets/images/logo.png"

width="80"

class="mb-3"

alt="GTU Logo">

<h4 class="fw-bold text-primary">

Global Tech University ERP

</h4>

<p class="text-muted mb-1">

Student Affairs Office • Reports Management System

</p>

<p class="small text-secondary">

© <?php echo date("Y"); ?> Global Tech University ERP System. All Rights Reserved.

</p>

</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>