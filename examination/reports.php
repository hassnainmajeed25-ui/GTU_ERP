<?php

require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

$adminQuery = mysqli_query($conn, "SELECT * FROM admins WHERE id='$admin_id'");
$admin = mysqli_fetch_assoc($adminQuery);

function totalRecords($conn, $table)
{
    $query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM $table");
    $row = mysqli_fetch_assoc($query);
    return $row['total'];
}

$totalStudents     = totalRecords($conn, "students");
$totalApplications = totalRecords($conn, "applications");
$totalPrograms     = totalRecords($conn, "programs");
$totalFaculties    = totalRecords($conn, "faculties");
$totalSubjects     = totalRecords($conn, "subjects");
$totalExams        = totalRecords($conn, "exams");
$totalMarks        = totalRecords($conn, "marks");
$totalTeachers     = totalRecords($conn, "teachers");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>GTU ERP | Examination Office Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#f4f7fc;
font-family:'Segoe UI',sans-serif;
}

.navbar{
background:#003366;
}

.navbar-brand{
color:#fff;
font-weight:bold;
font-size:24px;
}

.navbar-brand:hover{
color:#fff;
}

.admin-name{
color:#fff;
font-weight:600;
}

.page-title{
font-size:32px;
font-weight:bold;
color:#003366;
margin-bottom:25px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
transition:.3s;
}

.card:hover{
transform:translateY(-5px);
}

.counter{
font-size:34px;
font-weight:bold;
}

.bg1{
background:#0d6efd;
color:#fff;
}

.bg2{
background:#198754;
color:#fff;
}

.bg3{
background:#dc3545;
color:#fff;
}

.bg4{
background:#fd7e14;
color:#fff;
}

.bg5{
background:#6f42c1;
color:#fff;
}

.bg6{
background:#20c997;
color:#fff;
}

.bg7{
background:#0dcaf0;
color:#fff;
}

.bg8{
background:#212529;
color:#fff;
}

.table-section{
margin-top:35px;
}

.table thead{
background:#003366;
color:#fff;
}

.btn-print{
background:#198754;
color:#fff;
}

.btn-pdf{
background:#dc3545;
color:#fff;
}

.btn-excel{
background:#0d6efd;
color:#fff;
}

.footer{
margin-top:40px;
padding:20px;
text-align:center;
color:#666;
}

@media print{

.btn{
display:none;
}

.navbar{
display:none;
}

}

</style>

</head>

<body>

<nav class="navbar navbar-expand-lg">

<div class="container-fluid">

<a class="navbar-brand" href="#">

GTU ERP Examination Office

</a>

<div class="admin-name">

Welcome,

<strong>

<?php echo $admin['full_name']; ?>

</strong>

</div>

</div>

</nav>

<div class="container-fluid mt-4">

<div class="d-flex justify-content-between align-items-center">

<h2 class="page-title">

<i class="fa-solid fa-chart-column"></i>

Reports Dashboard

</h2>

<div>

<button onclick="window.print()" class="btn btn-print">

<i class="fa-solid fa-print"></i>

Print

</button>

<button class="btn btn-pdf">

<i class="fa-solid fa-file-pdf"></i>

PDF

</button>

<button class="btn btn-excel">

<i class="fa-solid fa-file-excel"></i>

Excel

</button>

</div>

</div>

<div class="row">

<div class="col-lg-3 col-md-6 mb-4">

<div class="card bg1">

<div class="card-body">

<h5>Total Students</h5>

<div class="counter">

<?php echo $totalStudents; ?>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card bg2">

<div class="card-body">

<h5>Total Applications</h5>

<div class="counter">

<?php echo $totalApplications; ?>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card bg3">

<div class="card-body">

<h5>Total Programs</h5>

<div class="counter">

<?php echo $totalPrograms; ?>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card bg4">

<div class="card-body">

<h5>Total Faculties</h5>

<div class="counter">

<?php echo $totalFaculties; ?>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card bg5">

<div class="card-body">

<h5>Total Subjects</h5>

<div class="counter">

<?php echo $totalSubjects; ?>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card bg6">

<div class="card-body">

<h5>Total Exams</h5>

<div class="counter">

<?php echo $totalExams; ?>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card bg7">

<div class="card-body">

<h5>Total Marks</h5>

<div class="counter">

<?php echo $totalMarks; ?>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card bg8">

<div class="card-body">

<h5>Total Teachers</h5>

<div class="counter">

<?php echo $totalTeachers; ?>

</div>

</div>

</div>

</div>

</div>

<!-- ===================================== -->
<!-- Latest Students Report -->
<!-- ===================================== -->

<div class="card table-section">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fa-solid fa-user-graduate"></i>

Latest Students Report

</h5>

</div>

<div class="card-body">

<?php

$students=mysqli_query($conn,"
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

<th>Student Name</th>

<th>Father Name</th>

<th>Email</th>

<th>Mobile</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($students))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['first_name'].' '.$row['last_name']; ?></td>

<td><?php echo $row['father_name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['mobile']; ?></td>

<td>

<span class="badge bg-success">

<?php echo $row['status']; ?>

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

<!-- ===================================== -->
<!-- Latest Applications Report -->
<!-- ===================================== -->

<div class="card table-section">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fa-solid fa-file-circle-check"></i>

Latest Admission Applications

</h5>

</div>

<div class="card-body">

<?php

$applications=mysqli_query($conn,"
SELECT
a.id,
a.application_id,
CONCAT(s.first_name,' ',s.last_name) AS student_name,
p.program_name,
a.application_status,
a.registration_no,
a.created_at
FROM applications a
LEFT JOIN students s
ON a.student_id=s.id
LEFT JOIN programs p
ON a.program_id=p.id
ORDER BY a.id DESC
LIMIT 10
");

?>

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>ID</th>

<th>Application ID</th>

<th>Student</th>

<th>Program</th>

<th>Status</th>

<th>Registration No</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($applications))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['application_id']; ?></td>

<td><?php echo $row['student_name']; ?></td>

<td><?php echo $row['program_name']; ?></td>

<td>

<span class="badge bg-warning text-dark">

<?php echo $row['application_status']; ?>

</span>

</td>

<td><?php echo $row['registration_no']; ?></td>

<td><?php echo date("d-M-Y",strtotime($row['created_at'])); ?></td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

<!-- ===================================== -->
<!-- Programs Report -->
<!-- ===================================== -->

<div class="card table-section">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fa-solid fa-graduation-cap"></i>

Programs Report

</h5>

</div>

<div class="card-body">

<?php

$programs=mysqli_query($conn,"
SELECT *
FROM programs
ORDER BY id DESC
LIMIT 10
");

?>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>ID</th>

<th>Program Name</th>

<th>Duration</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($programs))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['program_name']; ?></td>

<td><?php echo $row['duration']; ?></td>

<td>

<span class="badge bg-success">

<?php echo $row['status']; ?>

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

<!-- ===================================== -->
<!-- Faculties Report -->
<!-- ===================================== -->

<div class="card table-section">

<div class="card-header bg-secondary text-white">

<h5 class="mb-0">

<i class="fa-solid fa-building-columns"></i>

Faculties Report

</h5>

</div>

<div class="card-body">

<?php

$faculties=mysqli_query($conn,"
SELECT *
FROM faculties
ORDER BY id DESC
LIMIT 10
");

?>

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>ID</th>

<th>Faculty Name</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($faculties))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['faculty_name']; ?></td>

<td>

<span class="badge bg-primary">

<?php echo $row['status']; ?>

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

<!-- ===================================== -->
<!-- Subjects Report -->
<!-- ===================================== -->

<div class="card table-section">

<div class="card-header bg-danger text-white">

<h5 class="mb-0">

<i class="fa-solid fa-book-open"></i>

Subjects Report

</h5>

</div>

<div class="card-body">

<?php

$subjects=mysqli_query($conn,"
SELECT *
FROM subjects
ORDER BY id DESC
LIMIT 10
");

?>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>ID</th>

<th>Subject Code</th>

<th>Subject Name</th>

<th>Credit Hours</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($subjects))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['subject_code']; ?></td>

<td><?php echo $row['subject_name']; ?></td>

<td><?php echo $row['credit_hours']; ?></td>

<td>

<span class="badge bg-success">

<?php echo $row['status']; ?>

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

<!-- ===================================== -->
<!-- Latest Exams Report -->
<!-- ===================================== -->

<div class="card table-section">

<div class="card-header bg-warning">

<h5 class="mb-0">

<i class="fa-solid fa-file-lines"></i>

Latest Exams Report

</h5>

</div>

<div class="card-body">

<?php

$exams=mysqli_query($conn,"
SELECT
e.*,
p.program_name,
f.faculty_name
FROM exams e
LEFT JOIN programs p
ON e.program_id=p.id
LEFT JOIN faculties f
ON e.faculty_id=f.id
ORDER BY e.id DESC
LIMIT 10
");

?>

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>ID</th>

<th>Exam Title</th>

<th>Exam Type</th>

<th>Program</th>

<th>Faculty</th>

<th>Semester</th>

<th>Exam Date</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($exams))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['exam_title']; ?></td>

<td><?php echo $row['exam_type']; ?></td>

<td><?php echo $row['program_name']; ?></td>

<td><?php echo $row['faculty_name']; ?></td>

<td><?php echo $row['semester']; ?></td>

<td><?php echo $row['exam_date']; ?></td>

<td>

<span class="badge bg-info">

<?php echo $row['status']; ?>

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

<!-- ===================================== -->
<!-- Latest Marks Report -->
<!-- ===================================== -->

<div class="card table-section">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fa-solid fa-square-poll-vertical"></i>

Latest Marks Report

</h5>

</div>

<div class="card-body">

<?php

$marks=mysqli_query($conn,"
SELECT
m.*,
CONCAT(s.first_name,' ',s.last_name) AS student_name,
sub.subject_name,
e.exam_title
FROM marks m
LEFT JOIN students s
ON m.student_id=s.id
LEFT JOIN subjects sub
ON m.subject_id=sub.id
LEFT JOIN exams e
ON m.exam_id=e.id
ORDER BY m.id DESC
LIMIT 10
");

?>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>ID</th>

<th>Student</th>

<th>Subject</th>

<th>Exam</th>

<th>Obtained</th>

<th>Total</th>

<th>Grade</th>

<th>Grade Point</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($marks))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['student_name']; ?></td>

<td><?php echo $row['subject_name']; ?></td>

<td><?php echo $row['exam_title']; ?></td>

<td><?php echo $row['obtained_marks']; ?></td>

<td><?php echo $row['total_marks']; ?></td>

<td><?php echo $row['grade']; ?></td>

<td><?php echo $row['grade_point']; ?></td>

<td>

<span class="badge bg-primary">

<?php echo $row['status']; ?>

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

<footer class="footer">

<hr>

<p>

&copy; <?php echo date("Y"); ?>

GTU ERP | Examination Office Reports Module

</p>

</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>