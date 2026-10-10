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

$admin_name=$_SESSION['admin_name'];

/*=========================================
SEARCH
=========================================*/

$search="";

$where=" WHERE 1 ";

if(isset($_GET['search']) && $_GET['search']!=""){

    $search=trim($_GET['search']);

    $search=$conn->real_escape_string($search);

    $where.=" AND (

        complaints.subject LIKE '%$search%'

        OR complaints.message LIKE '%$search%'

        OR students.first_name LIKE '%$search%'

        OR students.last_name LIKE '%$search%'

    )";

}

/*=========================================
GET COMPLAINTS
=========================================*/

$sql="

SELECT

complaints.*,

students.first_name,

students.last_name

FROM complaints

LEFT JOIN students

ON students.id=complaints.student_id

$where

ORDER BY complaints.id DESC

";

$result=$conn->query($sql);

/*=========================================
STATISTICS
=========================================*/

$totalComplaints=$conn->query("SELECT COUNT(*) total FROM complaints")->fetch_assoc()['total'];

$pendingComplaints=$conn->query("SELECT COUNT(*) total FROM complaints WHERE status='Pending'")->fetch_assoc()['total'];

$resolvedComplaints=$conn->query("SELECT COUNT(*) total FROM complaints WHERE status='Resolved'")->fetch_assoc()['total'];

$rejectedComplaints=$conn->query("SELECT COUNT(*) total FROM complaints WHERE status='Rejected'")->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Complaint Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef4fb;

font-family:'Segoe UI',sans-serif;

}

.main{

padding:30px;

}

/*======================
HEADER
======================*/

.report-header{

background:linear-gradient(135deg,#0B1F52,#1e40af);

padding:30px;

border-radius:25px;

color:#fff;

box-shadow:0 12px 35px rgba(0,0,0,.18);

margin-bottom:30px;

}

.logo{

width:75px;

}

.admin-box{

background:#fff;

color:#000;

padding:18px;

border-radius:18px;

box-shadow:0 5px 20px rgba(0,0,0,.15);

}

/*======================
CARDS
======================*/

.stat-card{

border:none;

border-radius:20px;

overflow:hidden;

box-shadow:0 8px 20px rgba(0,0,0,.10);

transition:.3s;

}

.stat-card:hover{

transform:translateY(-5px);

}

.icon-box{

width:80px;

height:80px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

margin:auto;

font-size:35px;

color:#fff;

}

.total-bg{

background:#2563eb;

}

.pending-bg{

background:#f59e0b;

}

.resolve-bg{

background:#10b981;

}

.reject-bg{

background:#ef4444;

}

.card-title{

font-size:34px;

font-weight:bold;

margin-top:15px;

}

.card-text{

font-size:16px;

color:#6b7280;

font-weight:600;

}

</style>

</head>

<body>

<div class="container-fluid main">

<!-- ==========================
HEADER
========================== -->

<div class="report-header">

<div class="row align-items-center">

<div class="col-lg-8">

<div class="d-flex align-items-center">

<img src="../assets/images/logo.png"

class="logo me-4">

<div>

<h1 class="fw-bold">

Complaint Report Dashboard

</h1>

<h5 class="opacity-75">

Global Tech University ERP

</h5>

<p class="mb-0">

<i class="fas fa-calendar-alt me-2"></i>

<?php echo date("l, d F Y"); ?>

</p>

</div>

</div>

</div>

<div class="col-lg-4 text-end">

<div class="admin-box">

<h5>

<i class="fas fa-user-shield text-primary me-2"></i>

<?php echo htmlspecialchars($admin_name); ?>

</h5>

<small class="text-muted">

Student Affairs Office

</small>

</div>

</div>

</div>

</div>

<!-- ==========================
STATISTICS
========================== -->

<div class="row">

<div class="col-lg-3 col-md-6 mb-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="icon-box total-bg">

<i class="fas fa-comments"></i>

</div>

<h2 class="card-title">

<?php echo $totalComplaints; ?>

</h2>

<p class="card-text">

Total Complaints

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="icon-box pending-bg">

<i class="fas fa-hourglass-half"></i>

</div>

<h2 class="card-title text-warning">

<?php echo $pendingComplaints; ?>

</h2>

<p class="card-text">

Pending

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="icon-box resolve-bg">

<i class="fas fa-check-circle"></i>

</div>

<h2 class="card-title text-success">

<?php echo $resolvedComplaints; ?>

</h2>

<p class="card-text">

Resolved

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="icon-box reject-bg">

<i class="fas fa-times-circle"></i>

</div>

<h2 class="card-title text-danger">

<?php echo $rejectedComplaints; ?>

</h2>

<p class="card-text">

Rejected

</p>

</div>

</div>

</div>

</div>

<!-- ==========================
SEARCH PANEL
========================== -->

<div class="card border-0 shadow-lg rounded-4 mb-4">

<div class="card-body">

<div class="row align-items-end">

<div class="col-lg-7">

<form method="GET">

<label class="form-label fw-bold">

<i class="fas fa-search me-2 text-primary"></i>

Search Complaint

</label>

<div class="input-group">

<input

type="text"

name="search"

class="form-control form-control-lg"

placeholder="Search by Student Name or Subject"

value="<?php echo htmlspecialchars($search); ?>">

<button

type="submit"

class="btn btn-primary btn-lg">

<i class="fas fa-search me-2"></i>

Search

</button>

</div>

</form>

</div>

<div class="col-lg-5 text-end">

<a

href="export_complaint_pdf.php"

class="btn btn-danger btn-lg">

<i class="fas fa-file-pdf me-2"></i>

Export PDF

</a>

<a

href="export_complaint_excel.php"

class="btn btn-success btn-lg">

<i class="fas fa-file-excel me-2"></i>

Excel

</a>

<button

onclick="window.print()"

class="btn btn-primary btn-lg">

<i class="fas fa-print me-2"></i>

Print

</button>

</div>

</div>

</div>

</div>

<!-- ==========================
TABLE
========================== -->

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">

<div class="d-flex justify-content-between align-items-center">

<h3 class="mb-0">

<i class="fas fa-comments me-2"></i>

Complaint Records

</h3>

<span class="badge bg-light text-dark fs-6">

Total :

<?php echo $totalComplaints; ?>

</span>

</div>

</div>

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Student</th>

<th>Subject</th>

<th>Complaint</th>

<th>Status</th>

<th>Date</th>

<th class="text-center">

Action

</th>

</tr>

</thead>

<tbody>

<?php

$sr=1;

if($result && $result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<div class="d-flex align-items-center">

<div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"

style="width:48px;height:48px;font-weight:bold;">

<?php

echo strtoupper(substr($row['first_name'],0,1));

?>

</div>

<div class="ms-3">

<h6 class="mb-0">

<?php

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

?>

</h6>

<small class="text-muted">

Student Complaint

</small>

</div>

</div>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['subject']); ?>

</strong>

</td>

<td>

<?php

$message = $row['message'];

if(strlen($message) > 70){

    echo htmlspecialchars(substr($message,0,70))."...";

}else{

    echo htmlspecialchars($message);

}

?>

</td>

<td>

<?php

if($row['status']=="Pending"){

?>

<span class="badge bg-warning text-dark px-3 py-2">

<i class="fas fa-hourglass-half me-1"></i>

Pending

</span>

<?php

}elseif($row['status']=="Resolved"){

?>

<span class="badge bg-success px-3 py-2">

<i class="fas fa-check-circle me-1"></i>

Resolved

</span>

<?php

}elseif($row['status']=="Rejected"){

?>

<span class="badge bg-danger px-3 py-2">

<i class="fas fa-times-circle me-1"></i>

Rejected

</span>

<?php

}else{

?>

<span class="badge bg-primary px-3 py-2">

<?php echo htmlspecialchars($row['status']); ?>

</span>

<?php

}

?>

</td>

<td>

<?php

echo date("d M Y",strtotime($row['created_at']));

?>

</td>

<td class="text-center">

<a

href="view_complaint.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm"

title="View">

<i class="fas fa-eye"></i>

</a>

<a

href="print_complaint.php?id=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm"

title="Print">

<i class="fas fa-print"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center py-5">

<i class="fas fa-folder-open fa-5x text-secondary mb-3"></i>

<h3 class="text-muted">

No Complaint Records Found

</h3>

<p class="text-muted">

There are currently no complaints available.

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

<!-- ==========================
REPORT SUMMARY
========================== -->

<div class="row mt-5">

<div class="col-lg-8">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#198754,#20c997);">

<h4 class="mb-0">

<i class="fas fa-chart-pie me-2"></i>

Complaint Summary

</h4>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<i class="fas fa-comments fa-3x text-primary mb-3"></i>

<h3>

<?php echo $totalComplaints; ?>

</h3>

<p>Total</p>

</div>

<div class="col-md-3">

<i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>

<h3>

<?php echo $pendingComplaints; ?>

</h3>

<p>Pending</p>

</div>

<div class="col-md-3">

<i class="fas fa-check-circle fa-3x text-success mb-3"></i>

<h3>

<?php echo $resolvedComplaints; ?>

</h3>

<p>Resolved</p>

</div>

<div class="col-md-3">

<i class="fas fa-times-circle fa-3x text-danger mb-3"></i>

<h3>

<?php echo $rejectedComplaints; ?>

</h3>

<p>Rejected</p>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">

<h4 class="mb-0">

<i class="fas fa-download me-2"></i>

Quick Export Center

</h4>

</div>

<div class="card-body d-grid gap-3">

<a

href="export_complaint_pdf.php"

class="btn btn-danger btn-lg">

<i class="fas fa-file-pdf me-2"></i>

Export PDF

</a>

<button

onclick="window.print();"

class="btn btn-primary btn-lg">

<i class="fas fa-print me-2"></i>

Print Report

</button>

<a

href="student_affairs_dashboard.php"

class="btn btn-dark btn-lg">

<i class="fas fa-arrow-left me-2"></i>

Back to Dashboard

</a>

</div>

</div>

</div>

</div>

<!-- ==========================
FOOTER
========================== -->

<footer class="mt-5">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<img

src="../assets/images/logo.png"

width="90"

class="mb-3"

alt="GTU Logo">

<h3 class="fw-bold text-primary">

Global Tech University ERP

</h3>

<p class="text-muted">

Student Affairs Office - Complaint Report Management

</p>

<hr>

<div class="row">

<div class="col-md-4">

<i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>

<h6>Secure Complaint System</h6>

<small class="text-muted">

Protected Administrative Access

</small>

</div>

<div class="col-md-4">

<i class="fas fa-chart-line fa-2x text-success mb-2"></i>

<h6>Real-Time Reports</h6>

<small class="text-muted">

Live Complaint Monitoring

</small>

</div>

<div class="col-md-4">

<i class="fas fa-university fa-2x text-warning mb-2"></i>

<h6>Global Tech University</h6>

<small class="text-muted">

Digital ERP Management System

</small>

</div>

</div>

<hr>

<div class="row">

<div class="col-md-6 text-start">

<strong>

Generated By :

</strong>

<?php echo htmlspecialchars($admin_name); ?>

</div>

<div class="col-md-6 text-end">

<strong>

Date :

</strong>

<?php echo date("d F Y h:i A"); ?>

</div>

</div>

<hr>

<p class="small text-secondary mb-0">

© <?php echo date("Y"); ?>

Global Tech University ERP System.

All Rights Reserved.

Version 1.0 | Student Affairs Module

</p>

</div>

</div>

</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>