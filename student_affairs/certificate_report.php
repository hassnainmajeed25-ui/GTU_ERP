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

$search=$conn->real_escape_string(trim($_GET['search']));

$where.=" AND (

certificate_requests.request_no LIKE '%$search%'

OR certificate_requests.certificate_type LIKE '%$search%'

OR students.first_name LIKE '%$search%'

OR students.last_name LIKE '%$search%'

)";

}

/*=========================================
GET REPORT
=========================================*/

$sql="

SELECT

certificate_requests.*,

students.first_name,

students.last_name

FROM certificate_requests

LEFT JOIN students

ON students.id=certificate_requests.student_id

$where

ORDER BY certificate_requests.id DESC

";

$result=$conn->query($sql);

/*=========================================
TOTAL
=========================================*/

$total=0;

$q=$conn->query("SELECT COUNT(*) total FROM certificate_requests");

if($q){

$total=$q->fetch_assoc()['total'];

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Certificate Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef3fb;

font-family:Segoe UI,sans-serif;

}

.main{

padding:30px;

}

.header{

background:linear-gradient(135deg,#0B1F52,#2563eb);

padding:25px;

border-radius:20px;

color:white;

box-shadow:0 10px 25px rgba(0,0,0,.15);

margin-bottom:30px;

}

.logo{

width:70px;

}

.card-box{

background:white;

border:none;

border-radius:18px;

box-shadow:0 4px 15px rgba(0,0,0,.08);

padding:25px;

margin-bottom:25px;

}

.table-card{

background:white;

border:none;

border-radius:18px;

box-shadow:0 4px 15px rgba(0,0,0,.08);

overflow:hidden;

}

.table thead{

background:#0B1F52;

color:white;

}

</style>

</head>

<body>

<div class="container-fluid main">

<!-- =====================================
HEADER
====================================== -->

<div class="header">

<div class="row align-items-center">

<div class="col-lg-8">

<div class="d-flex align-items-center">

<img

src="../assets/images/logo.png"

class="logo me-4"

alt="GTU Logo">

<div>

<h2 class="fw-bold mb-1">

Certificate Reports Dashboard

</h2>

<p class="mb-1">

Global Tech University ERP System

</p>

<small>

<i class="fas fa-calendar-days me-2"></i>

<?php echo date("l, d F Y"); ?>

</small>

</div>

</div>

</div>

<div class="col-lg-4 text-end">

<div class="bg-white text-dark rounded-4 p-3 shadow">

<h5 class="mb-1">

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

<!-- =====================================
STATISTICS
====================================== -->

<div class="row">

<div class="col-lg-3 col-md-6 mb-4">

<div class="card-box text-center">

<i class="fas fa-certificate fa-4x text-primary mb-3"></i>

<h1 class="fw-bold">

<?php echo $total; ?>

</h1>

<p class="text-muted mb-0">

Total Requests

</p>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card-box text-center">

<i class="fas fa-hourglass-half fa-4x text-warning mb-3"></i>

<h1 class="fw-bold text-warning">

<?php
$pending=$conn->query("SELECT COUNT(*) total FROM certificate_requests WHERE status='Pending'");
echo $pending->fetch_assoc()['total'];
?>

</h1>

<p class="text-muted mb-0">

Pending

</p>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card-box text-center">

<i class="fas fa-circle-check fa-4x text-success mb-3"></i>

<h1 class="fw-bold text-success">

<?php
$approved=$conn->query("SELECT COUNT(*) total FROM certificate_requests WHERE status='Approved'");
echo $approved->fetch_assoc()['total'];
?>

</h1>

<p class="text-muted mb-0">

Approved

</p>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card-box text-center">

<i class="fas fa-circle-xmark fa-4x text-danger mb-3"></i>

<h1 class="fw-bold text-danger">

<?php
$rejected=$conn->query("SELECT COUNT(*) total FROM certificate_requests WHERE status='Rejected'");
echo $rejected->fetch_assoc()['total'];
?>

</h1>

<p class="text-muted mb-0">

Rejected

</p>

</div>

</div>

</div>

<!-- =====================================
SEARCH BAR
====================================== -->

<div class="card-box">

<div class="row align-items-end">

<div class="col-lg-8">

<form method="GET">

<label class="form-label fw-bold">

<i class="fas fa-search me-2 text-primary"></i>

Search Certificate

</label>

<div class="input-group">

<input

type="text"

name="search"

value="<?php echo htmlspecialchars($search); ?>"

class="form-control form-control-lg"

placeholder="Search by Request No, Student Name or Certificate Type">

<button

class="btn btn-primary btn-lg"

type="submit">

<i class="fas fa-search me-2"></i>

Search

</button>

</div>

</form>

</div>

<div class="col-lg-4 text-end">

<button

onclick="window.print();"

class="btn btn-success btn-lg me-2">

<i class="fas fa-print me-2"></i>

Print

</button>

<a

href="#"

class="btn btn-danger btn-lg">

<i class="fas fa-file-pdf me-2"></i>

PDF

</a>

</div>

</div>

</div>

<!-- =====================================
CERTIFICATE REPORT TABLE
====================================== -->

<div class="table-card">

<div class="card-header text-white d-flex justify-content-between align-items-center"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">

<h3 class="mb-0">

<i class="fas fa-file-certificate me-2"></i>

Certificate Records

</h3>

<span class="badge bg-light text-dark fs-6">

Total :

<?php echo $total; ?>

</span>

</div>

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead>

<tr>

<th>#</th>

<th>Request No</th>

<th>Student</th>

<th>Certificate</th>

<th>Purpose</th>

<th>Applied Date</th>

<th>Status</th>

<th class="text-center">Action</th>

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

<strong class="text-primary">

<?php echo htmlspecialchars($row['request_no']); ?>

</strong>

</td>

<td>

<div class="d-flex align-items-center">

<div

class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"

style="width:45px;height:45px;font-weight:bold;">

<?php echo strtoupper(substr($row['first_name'],0,1)); ?>

</div>

<div class="ms-3">

<strong>

<?php

echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);

?>

</strong>

</div>

</div>

</td>

<td>

<span class="badge bg-info text-dark">

<?php echo htmlspecialchars($row['certificate_type']); ?>

</span>

</td>

<td>

<?php

echo htmlspecialchars(

substr($row['purpose'],0,45)

);

?>

...

</td>

<td>

<?php

echo date(

"d M Y",

strtotime($row['created_at'])

);

?>

</td>

<td>

<?php

$status=$row['status'];

if($status=="Pending"){

echo '<span class="badge bg-warning text-dark">Pending</span>';

}elseif($status=="Approved"){

echo '<span class="badge bg-success">Approved</span>';

}elseif($status=="Rejected"){

echo '<span class="badge bg-danger">Rejected</span>';

}elseif($status=="Completed"){

echo '<span class="badge bg-primary">Completed</span>';

}else{

echo '<span class="badge bg-secondary">'.$status.'</span>';

}

?>

</td>

<td class="text-center">

<a

href="view_certificate_request.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm"

title="View">

<i class="fas fa-eye"></i>

</a>

<a

href="print_certificate.php?id=<?php echo $row['id']; ?>"

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

<td colspan="8" class="text-center py-5">

<i class="fas fa-folder-open fa-4x text-secondary mb-3"></i>

<h4 class="text-muted">

No Certificate Requests Found

</h4>

<p class="text-muted">

There are currently no certificate records available.

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

<!-- =====================================
REPORT SUMMARY
====================================== -->

<div class="row mt-5">

<div class="col-lg-8">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#198754,#20c997);">

<h4 class="mb-0">

<i class="fas fa-chart-pie me-2"></i>

Certificate Report Summary

</h4>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<i class="fas fa-file-alt fa-3x text-primary mb-3"></i>

<h3>

<?php echo $total; ?>

</h3>

<p>Total Requests</p>

</div>

<div class="col-md-3">

<i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>

<h3>

<?php
echo $conn->query("SELECT COUNT(*) total FROM certificate_requests WHERE status='Pending'")->fetch_assoc()['total'];
?>

</h3>

<p>Pending</p>

</div>

<div class="col-md-3">

<i class="fas fa-circle-check fa-3x text-success mb-3"></i>

<h3>

<?php
echo $conn->query("SELECT COUNT(*) total FROM certificate_requests WHERE status='Approved'")->fetch_assoc()['total'];
?>

</h3>

<p>Approved</p>

</div>

<div class="col-md-3">

<i class="fas fa-circle-xmark fa-3x text-danger mb-3"></i>

<h3>

<?php
echo $conn->query("SELECT COUNT(*) total FROM certificate_requests WHERE status='Rejected'")->fetch_assoc()['total'];
?>

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

Quick Actions

</h4>

</div>

<div class="card-body d-grid gap-3">

<a

href="export_certificate_pdf.php"

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

</div>

</div>

</div>

</div>

<!-- =====================================
FOOTER
====================================== -->

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

Student Affairs Office - Certificate Report Management

</p>

<hr>

<div class="row">

<div class="col-md-4">

<i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>

<h6>Secure Reports</h6>

<small class="text-muted">

Protected Administrative Access

</small>

</div>

<div class="col-md-4">

<i class="fas fa-certificate fa-2x text-success mb-2"></i>

<h6>Certificate Records</h6>

<small class="text-muted">

Real-Time Student Certificate Data

</small>

</div>

<div class="col-md-4">

<i class="fas fa-university fa-2x text-warning mb-2"></i>

<h6>Global Tech University</h6>

<small class="text-muted">

Digital Campus ERP System

</small>

</div>

</div>

<hr>

<p class="small text-secondary mb-0">

© <?php echo date("Y"); ?>

Global Tech University ERP System.

All Rights Reserved.

</p>

</div>

</div>

</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>