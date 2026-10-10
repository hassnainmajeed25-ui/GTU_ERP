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
SEARCH FILTERS
=========================================*/

$search = "";

$where = " WHERE 1 ";

if(isset($_GET['search']) && $_GET['search']!=""){

    $search = trim($_GET['search']);

    $search = $conn->real_escape_string($search);

    $where .= " AND (

        first_name LIKE '%$search%'

        OR last_name LIKE '%$search%'

        OR email LIKE '%$search%'

        OR registration_no LIKE '%$search%'

    )";

}

/*=========================================
GET STUDENTS
=========================================*/

$sql="

SELECT

students.*,

programs.program_name

FROM students

LEFT JOIN applications

ON applications.student_id = students.id

LEFT JOIN programs

ON programs.id = applications.program_id

$where

ORDER BY students.id ASC

";

$result=$conn->query($sql);

$totalStudents=0;

$count=$conn->query("SELECT COUNT(*) total FROM students");

if($count){

$totalStudents=$count->fetch_assoc()['total'];

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Student Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#edf3fa;

font-family:'Segoe UI',sans-serif;

}

.main{

padding:30px;

}

.report-header{

background:linear-gradient(135deg,#0B1F52,#2563eb);

color:#fff;

padding:25px;

border-radius:20px;

box-shadow:0 10px 25px rgba(0,0,0,.15);

margin-bottom:30px;

}

.logo{

width:70px;

}

.search-card{

background:#fff;

border-radius:18px;

box-shadow:0 4px 15px rgba(0,0,0,.08);

padding:25px;

margin-bottom:25px;

}

.table-card{

background:#fff;

border-radius:18px;

box-shadow:0 4px 15px rgba(0,0,0,.08);

overflow:hidden;

}

.table thead{

background:#0B1F52;

color:#fff;

}

</style>

</head>

<body>

<div class="container-fluid main">

<!-- ===========================
REPORT HEADER
============================ -->

<div class="report-header">

<div class="row align-items-center">

<div class="col-lg-8">

<div class="d-flex align-items-center">

<img

src="../assets/images/logo.png"

class="logo me-4"

alt="GTU Logo">

<div>

<h1 class="fw-bold mb-1">

Student Report Dashboard

</h1>

<h5 class="mb-2 opacity-75">

Global Tech University ERP System

</h5>

<p class="mb-0">

<i class="fas fa-calendar-alt me-2"></i>

<?php echo date("l, d F Y"); ?>

</p>

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

<!-- ===========================
STATISTICS
============================ -->

<div class="row mt-4">

<div class="col-lg-3 col-md-6 mb-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<div class="mb-3">

<i class="fas fa-user-graduate fa-4x text-primary"></i>

</div>

<h1 class="fw-bold">

<?php echo $totalStudents; ?>

</h1>

<p class="text-muted mb-0">

Total Students

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<div class="mb-3">

<i class="fas fa-user-check fa-4x text-success"></i>

</div>

<h1 class="fw-bold text-success">

<?php echo $totalStudents; ?>

</h1>

<p class="text-muted mb-0">

Active Students

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<div class="mb-3">

<i class="fas fa-user-clock fa-4x text-warning"></i>

</div>

<h1 class="fw-bold text-warning">

0

</h1>

<p class="text-muted mb-0">

Pending Records

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<div class="mb-3">

<i class="fas fa-chart-line fa-4x text-danger"></i>

</div>

<h1 class="fw-bold text-danger">

100%

</h1>

<p class="text-muted mb-0">

System Status

</p>

</div>

</div>

</div>

</div>

<!-- ===========================
SEARCH PANEL
============================ -->

<div class="search-card">

<div class="row align-items-end">

<div class="col-lg-8">

<label class="form-label fw-bold">

<i class="fas fa-search me-2 text-primary"></i>

Search Student

</label>

<form method="GET">

<div class="input-group">

<input

type="text"

name="search"

value="<?php echo htmlspecialchars($search); ?>"

class="form-control form-control-lg"

placeholder="Search by Name, Registration No or Email">

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

<!-- ===========================
STUDENT REPORT TABLE
============================ -->

<div class="table-card">

<div class="card-header text-white d-flex justify-content-between align-items-center"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">

<h3 class="mb-0">

<i class="fas fa-users me-2"></i>

Student Records

</h3>

<span class="badge bg-light text-dark fs-6">

Total :

<?php echo $totalStudents; ?>

</span>

</div>

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead>

<tr>

<th>#</th>

<th>Student</th>

<th>Registration No</th>

<th>Program</th>

<th>Email</th>

<th>Mobile</th>

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

<div class="d-flex align-items-center">

<div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"

style="width:50px;height:50px;font-size:18px;font-weight:bold;">

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

Student ID :

<?php echo $row['id']; ?>

</small>

</div>

</div>

</td>

<td>

<strong>

<?php

if(empty($row['registration_no'])){

    echo '<span class="badge bg-warning text-dark">Pending Fee</span>';

}else{

    echo htmlspecialchars($row['registration_no']);

}

?>

</strong>

</td>

<td>

<span class="badge bg-info text-dark">

<?php echo htmlspecialchars($row['program_name']); ?>

</span>

</td>

<td>

<?php echo htmlspecialchars($row['email']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['mobile']); ?>

</td>

<td>

<span class="badge bg-success">

Active

</span>

</td>

<td class="text-center">

<a

href="view_student.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-eye"></i>

</a>

<a

href="print_student.php?id=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm">

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

No Student Records Found

</h4>

<p class="text-muted">

There are currently no student records matching your search.

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

<!-- ===========================
REPORT SUMMARY & EXPORT CENTER
=========================== -->

<div class="row mt-5">

<div class="col-lg-8">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#198754,#20c997);">

<h4 class="mb-0">

<i class="fas fa-chart-pie me-2"></i>

Report Summary

</h4>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-4">

<i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>

<h3><?php echo $totalStudents; ?></h3>

<p>Total Students</p>

</div>

<div class="col-md-4">

<i class="fas fa-user-check fa-3x text-success mb-3"></i>

<h3><?php echo $totalStudents; ?></h3>

<p>Active Students</p>

</div>

<div class="col-md-4">

<i class="fas fa-calendar-alt fa-3x text-warning mb-3"></i>

<h3><?php echo date("Y"); ?></h3>

<p>Academic Year</p>

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

Quick Export

</h4>

</div>

<div class="card-body d-grid gap-3">

<a href="export_student_pdf.php"
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

<!-- ===========================
FOOTER
=========================== -->

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

Student Affairs Office - Student Report Management

</p>

<hr>

<div class="row">

<div class="col-md-4">

<i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>

<h6>Secure System</h6>

<small class="text-muted">

Protected ERP Access

</small>

</div>

<div class="col-md-4">

<i class="fas fa-chart-bar fa-2x text-success mb-2"></i>

<h6>Professional Reports</h6>

<small class="text-muted">

Real-Time Student Data

</small>

</div>

<div class="col-md-4">

<i class="fas fa-university fa-2x text-warning mb-2"></i>

<h6>Global Tech University</h6>

<small class="text-muted">

Digital Campus ERP

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