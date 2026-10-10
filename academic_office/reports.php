<?php

session_start();


require_once '../config/config.php';
require_once '../config/database.php';



if(!isset($_SESSION['admin_id'])){

    header("Location: ../admins/login.php");
    exit;

}



if($_SESSION['department']!="Academic Office"){

    header("Location: ../admins/login.php");
    exit;

}



$page_title="Academic Reports";



/* =========================
   STATISTICS
========================= */


$total_programs=mysqli_fetch_assoc(
mysqli_query($conn,"
SELECT COUNT(*) total 
FROM programs
")
)['total'];



$total_subjects=mysqli_fetch_assoc(
mysqli_query($conn,"
SELECT COUNT(*) total 
FROM subjects
")
)['total'];



$total_teachers=mysqli_fetch_assoc(
mysqli_query($conn,"
SELECT COUNT(*) total 
FROM teachers
")
)['total'];



$total_faculties=mysqli_fetch_assoc(
mysqli_query($conn,"
SELECT COUNT(*) total 
FROM faculties
")
)['total'];



?>



<!DOCTYPE html>

<html lang="en">


<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">



<title>

<?php echo $page_title; ?>

</title>




<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{

background:#f4f6f9;

font-family:'Segoe UI',sans-serif;

}



.page-title{

font-size:30px;

font-weight:700;

color:#0d6efd;

}



.card{

border:none;

border-radius:15px;

box-shadow:0 4px 15px rgba(0,0,0,.08);

}



.stat-card{

padding:20px;

}



.stat-icon{

font-size:35px;

}



.report-card{

transition:.3s;

cursor:pointer;

}



.report-card:hover{

transform:translateY(-5px);

}



</style>



</head>



<body>


<div class="container-fluid py-4">



<div class="mb-4">


<h2 class="page-title">

<i class="fa-solid fa-chart-line"></i>

Academic Reports


</h2>


<p class="text-muted">

Global Tech University - Academic Office Reporting System

</p>


</div>

<!-- STATISTICS CARDS -->


<div class="row mb-4">



<div class="col-md-3 mb-3">


<div class="card stat-card">


<div class="d-flex justify-content-between align-items-center">


<div>

<h6 class="text-muted">

Total Programs

</h6>


<h2>

<?php echo $total_programs; ?>

</h2>


</div>



<div class="stat-icon text-primary">

<i class="fa-solid fa-graduation-cap"></i>

</div>


</div>


</div>


</div>





<div class="col-md-3 mb-3">


<div class="card stat-card">


<div class="d-flex justify-content-between align-items-center">


<div>

<h6 class="text-muted">

Total Subjects

</h6>


<h2>

<?php echo $total_subjects; ?>

</h2>


</div>



<div class="stat-icon text-success">

<i class="fa-solid fa-book-open"></i>

</div>


</div>


</div>


</div>






<div class="col-md-3 mb-3">


<div class="card stat-card">


<div class="d-flex justify-content-between align-items-center">


<div>

<h6 class="text-muted">

Total Teachers

</h6>


<h2>

<?php echo $total_teachers; ?>

</h2>


</div>



<div class="stat-icon text-warning">

<i class="fa-solid fa-user-tie"></i>

</div>


</div>


</div>


</div>







<div class="col-md-3 mb-3">


<div class="card stat-card">


<div class="d-flex justify-content-between align-items-center">


<div>

<h6 class="text-muted">

Total Faculties

</h6>


<h2>

<?php echo $total_faculties; ?>

</h2>


</div>



<div class="stat-icon text-danger">

<i class="fa-solid fa-building-columns"></i>

</div>


</div>


</div>


</div>




</div>







<!-- REPORT MODULES -->


<div class="row">



<div class="col-md-4 mb-4">


<div class="card report-card p-4">


<div class="text-primary mb-3">

<i class="fa-solid fa-graduation-cap fa-2x"></i>

</div>


<h5>

Program Report

</h5>


<p class="text-muted">

View all programs with faculty details.

</p>



<a href="program_report.php" 
class="btn btn-primary">


<i class="fa fa-eye"></i>

View Report


</a>


</div>


</div>







<div class="col-md-4 mb-4">


<div class="card report-card p-4">


<div class="text-success mb-3">

<i class="fa-solid fa-user-tie fa-2x"></i>

</div>


<h5>

Teacher Assignment Report

</h5>


<p class="text-muted">

Check teacher assigned programs and subjects.

</p>



<a href="teacher_report.php" 
class="btn btn-success">


<i class="fa fa-eye"></i>

View Report


</a>


</div>


</div>







<div class="col-md-4 mb-4">


<div class="card report-card p-4">


<div class="text-warning mb-3">

<i class="fa-solid fa-book fa-2x"></i>

</div>


<h5>

Subject Report

</h5>


<p class="text-muted">

Semester wise subject details.

</p>



<a href="subject_report.php" 
class="btn btn-warning text-white">


<i class="fa fa-eye"></i>

View Report


</a>


</div>


</div>





<div class="col-md-4 mb-4">


<div class="card report-card p-4">


<div class="text-danger mb-3">

<i class="fa-solid fa-building fa-2x"></i>

</div>


<h5>

Faculty Report

</h5>


<p class="text-muted">

Faculty wise academic information.

</p>



<a href="faculty_report.php" 
class="btn btn-danger">


<i class="fa fa-eye"></i>

View Report


</a>


</div>


</div>

</div>

<!-- ACTION BUTTONS -->


<div class="card mt-4">


<div class="card-body text-center">



<button onclick="window.print()" 
class="btn btn-dark me-2">


<i class="fa-solid fa-print"></i>

Print Reports


</button>





<a href="dashboard.php" 
class="btn btn-secondary">


<i class="fa-solid fa-arrow-left"></i>

Back To Dashboard


</a>



</div>


</div>






</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>


</html>