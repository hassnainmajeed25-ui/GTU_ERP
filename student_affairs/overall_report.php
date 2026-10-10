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
OVERALL COUNTS
=========================================*/


$totalStudents=0;
$totalApplications=0;
$totalCertificates=0;
$totalAnnouncements=0;
$totalMessages=0;
$totalComplaints=0;
$totalDocuments=0;



/* Students */

$query=$conn->query("
SELECT COUNT(*) total 
FROM students
");

if($query){

    $totalStudents=$query->fetch_assoc()['total'];

}



/* Applications */

$query=$conn->query("
SELECT COUNT(*) total 
FROM applications
");

if($query){

    $totalApplications=$query->fetch_assoc()['total'];

}



/* Certificate Requests */

$query=$conn->query("
SELECT COUNT(*) total 
FROM certificate_requests
");

if($query){

    $totalCertificates=$query->fetch_assoc()['total'];

}



/* Announcements */

$query=$conn->query("
SELECT COUNT(*) total 
FROM announcements
");

if($query){

    $totalAnnouncements=$query->fetch_assoc()['total'];

}



/* Messages */

$query=$conn->query("
SELECT COUNT(*) total 
FROM messages
");

if($query){

    $totalMessages=$query->fetch_assoc()['total'];

}



/* Complaints */

$query=$conn->query("
SELECT COUNT(*) total 
FROM complaints
");

if($query){

    $totalComplaints=$query->fetch_assoc()['total'];

}



/* Documents */

$query=$conn->query("
SELECT COUNT(*) total 
FROM application_documents
");

if($query){

    $totalDocuments=$query->fetch_assoc()['total'];

}



/*=========================================
LATEST RECORDS
=========================================*/


$students=$conn->query("
SELECT *
FROM students
ORDER BY id DESC
LIMIT 5
");



$announcements=$conn->query("
SELECT *
FROM announcements
ORDER BY id DESC
LIMIT 5
");



$messages=$conn->query("
SELECT *
FROM messages
ORDER BY id DESC
LIMIT 5
");



$complaints=$conn->query("
SELECT *
FROM complaints
ORDER BY id DESC
LIMIT 5
");


?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>
GTU Overall Report
</title>


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



.header{

background:linear-gradient(135deg,#0B1F52,#2563eb);

padding:30px;

border-radius:25px;

color:white;

box-shadow:0 10px 30px rgba(0,0,0,.15);

}



.logo{

width:80px;

}



.admin-box{

background:white;

color:#000;

padding:18px;

border-radius:18px;

}



.stat-card{

border:none;

border-radius:20px;

box-shadow:0 10px 25px rgba(0,0,0,.1);

}



.icon-box{

width:75px;

height:75px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

font-size:30px;

color:white;

margin:auto;

}



.blue{

background:#2563eb;

}


.green{

background:#10b981;

}


.orange{

background:#f59e0b;

}


.red{

background:#ef4444;

}


.purple{

background:#7c3aed;

}


</style>


</head>


<body>


<div class="container-fluid main">

<!-- ==========================
HEADER SECTION
========================== -->


<div class="header mb-4">


<div class="row align-items-center">


<div class="col-lg-8">


<div class="d-flex align-items-center">


<img

src="../assets/images/logo.png"

class="logo me-4"

alt="GTU Logo">



<div>


<h1 class="fw-bold">

Overall Report Dashboard

</h1>



<h5>

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
STATISTICS CARDS
========================== -->



<div class="row">



<!-- STUDENTS -->


<div class="col-lg-3 col-md-6 mb-4">


<div class="card stat-card">


<div class="card-body text-center">


<div class="icon-box blue">


<i class="fas fa-user-graduate"></i>


</div>


<h2 class="fw-bold mt-3">


<?php echo $totalStudents; ?>


</h2>


<p class="text-muted">

Total Students

</p>


</div>


</div>


</div>





<!-- APPLICATIONS -->


<div class="col-lg-3 col-md-6 mb-4">


<div class="card stat-card">


<div class="card-body text-center">


<div class="icon-box green">


<i class="fas fa-file-signature"></i>


</div>


<h2 class="fw-bold mt-3">


<?php echo $totalApplications; ?>


</h2>


<p class="text-muted">

Applications

</p>


</div>


</div>


</div>






<!-- CERTIFICATES -->


<div class="col-lg-3 col-md-6 mb-4">


<div class="card stat-card">


<div class="card-body text-center">


<div class="icon-box orange">


<i class="fas fa-certificate"></i>


</div>


<h2 class="fw-bold mt-3">


<?php echo $totalCertificates; ?>


</h2>


<p class="text-muted">

Certificates Requests

</p>


</div>


</div>


</div>





<!-- ANNOUNCEMENTS -->


<div class="col-lg-3 col-md-6 mb-4">


<div class="card stat-card">


<div class="card-body text-center">


<div class="icon-box red">


<i class="fas fa-bullhorn"></i>


</div>


<h2 class="fw-bold mt-3">


<?php echo $totalAnnouncements; ?>


</h2>


<p class="text-muted">

Announcements

</p>


</div>


</div>


</div>




</div>






<div class="row">



<!-- MESSAGES -->


<div class="col-lg-3 col-md-6 mb-4">


<div class="card stat-card">


<div class="card-body text-center">


<div class="icon-box purple">


<i class="fas fa-envelope"></i>


</div>


<h2 class="fw-bold mt-3">


<?php echo $totalMessages; ?>


</h2>


<p class="text-muted">

Messages

</p>


</div>


</div>


</div>






<!-- COMPLAINTS -->


<div class="col-lg-3 col-md-6 mb-4">


<div class="card stat-card">


<div class="card-body text-center">


<div class="icon-box red">


<i class="fas fa-exclamation-circle"></i>


</div>


<h2 class="fw-bold mt-3">


<?php echo $totalComplaints; ?>


</h2>


<p class="text-muted">

Complaints

</p>


</div>


</div>


</div>







<!-- DOCUMENTS -->


<div class="col-lg-3 col-md-6 mb-4">


<div class="card stat-card">


<div class="card-body text-center">


<div class="icon-box blue">


<i class="fas fa-folder-open"></i>


</div>


<h2 class="fw-bold mt-3">


<?php echo $totalDocuments; ?>


</h2>


<p class="text-muted">

Documents

</p>


</div>


</div>


</div>






<!-- YEAR -->


<div class="col-lg-3 col-md-6 mb-4">


<div class="card stat-card">


<div class="card-body text-center">


<div class="icon-box green">


<i class="fas fa-calendar-check"></i>


</div>


<h2 class="fw-bold mt-3">


<?php echo date("Y"); ?>


</h2>


<p class="text-muted">

Academic Year

</p>


</div>


</div>


</div>



</div>

<!-- ==========================
OVERALL SUMMARY
========================== -->


<div class="card border-0 shadow-lg rounded-4 mb-5">


<div class="card-header text-white"

style="background:linear-gradient(90deg,#198754,#20c997);">


<h3 class="mb-0">

<i class="fas fa-chart-line me-2"></i>

Overall Performance Summary

</h3>


</div>



<div class="card-body">


<div class="row text-center">



<div class="col-md-3">


<i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>


<h3>

<?php echo $totalStudents; ?>

</h3>


<p class="text-muted">

Registered Students

</p>


</div>




<div class="col-md-3">


<i class="fas fa-file-alt fa-3x text-success mb-3"></i>


<h3>

<?php echo $totalApplications; ?>

</h3>


<p class="text-muted">

Admission Applications

</p>


</div>





<div class="col-md-3">


<i class="fas fa-comments fa-3x text-warning mb-3"></i>


<h3>

<?php echo $totalMessages + $totalComplaints; ?>

</h3>


<p class="text-muted">

Communication Records

</p>


</div>





<div class="col-md-3">


<i class="fas fa-folder-open fa-3x text-danger mb-3"></i>


<h3>

<?php echo $totalDocuments + $totalCertificates; ?>

</h3>


<p class="text-muted">

Academic Records

</p>


</div>



</div>


</div>


</div>






<!-- ==========================
LATEST STUDENTS
========================== -->


<div class="card border-0 shadow-lg rounded-4 mb-5">



<div class="card-header text-white"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">


<h3 class="mb-0">

<i class="fas fa-users me-2"></i>

Latest Students

</h3>


</div>




<div class="table-responsive">


<table class="table table-hover mb-0">



<thead class="table-dark">


<tr>

<th>#</th>

<th>Name</th>

<th>Email</th>

<th>Mobile</th>

<th>Status</th>

</tr>


</thead>




<tbody>


<?php


$count=1;


if($students && $students->num_rows>0){


while($row=$students->fetch_assoc()){


?>


<tr>


<td>

<?php echo $count++; ?>

</td>



<td>


<strong>

<?php

echo htmlspecialchars(
$row['first_name']." ".$row['last_name']
);

?>

</strong>


</td>




<td>

<?php

echo htmlspecialchars($row['email']);

?>

</td>




<td>

<?php

echo htmlspecialchars($row['mobile']);

?>

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

<td colspan="5" class="text-center py-4">


No Student Records Found


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
LATEST ANNOUNCEMENTS
========================== -->


<div class="card border-0 shadow-lg rounded-4 mb-5">


<div class="card-header text-white"

style="background:linear-gradient(90deg,#f59e0b,#d97706);">


<h3 class="mb-0">

<i class="fas fa-bullhorn me-2"></i>

Latest Announcements

</h3>


</div>




<div class="table-responsive">


<table class="table table-hover mb-0">



<thead class="table-dark">


<tr>

<th>#</th>

<th>Title</th>

<th>Department</th>

<th>Status</th>

<th>Date</th>

</tr>


</thead>




<tbody>


<?php


$count=1;


if($announcements && $announcements->num_rows>0){


while($row=$announcements->fetch_assoc()){


?>


<tr>


<td>

<?php echo $count++; ?>

</td>



<td>

<?php

echo htmlspecialchars($row['title']);

?>

</td>




<td>

<?php

echo htmlspecialchars($row['department']);

?>

</td>




<td>


<span class="badge bg-success">

<?php echo $row['status']; ?>

</span>


</td>




<td>

<?php

echo date(
"d-m-Y",
strtotime($row['created_at'])
);

?>

</td>


</tr>


<?php


}


}else{


?>


<tr>

<td colspan="5" class="text-center py-4">

No Announcement Found

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
LATEST MESSAGES
========================== -->


<div class="card border-0 shadow-lg rounded-4 mb-5">


<div class="card-header text-white"

style="background:linear-gradient(90deg,#7c3aed,#9333ea);">


<h3 class="mb-0">

<i class="fas fa-envelope me-2"></i>

Latest Messages

</h3>


</div>




<div class="table-responsive">


<table class="table table-hover mb-0">



<thead class="table-dark">


<tr>

<th>#</th>

<th>Subject</th>

<th>Sender</th>

<th>Receiver</th>

<th>Status</th>

<th>Date</th>

</tr>


</thead>




<tbody>


<?php


$count=1;


if($messages && $messages->num_rows>0){


while($row=$messages->fetch_assoc()){


?>


<tr>


<td>

<?php echo $count++; ?>

</td>



<td>

<?php

echo htmlspecialchars($row['subject']);

?>

</td>




<td>

<?php

echo htmlspecialchars($row['sender_role']);

?>

</td>




<td>

<?php

echo htmlspecialchars($row['receiver_role']);

?>

</td>




<td>


<?php

if($row['is_read']==1){

?>

<span class="badge bg-success">

Read

</span>


<?php

}else{

?>


<span class="badge bg-warning text-dark">

Unread

</span>


<?php

}

?>


</td>




<td>

<?php

echo date(
"d-m-Y",
strtotime($row['created_at'])
);

?>

</td>


</tr>


<?php

}


}else{


?>


<tr>

<td colspan="6" class="text-center py-4">

No Messages Found

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
LATEST COMPLAINTS
========================== -->


<div class="card border-0 shadow-lg rounded-4 mb-5">



<div class="card-header text-white"

style="background:linear-gradient(90deg,#dc2626,#ef4444);">


<h3 class="mb-0">

<i class="fas fa-exclamation-triangle me-2"></i>

Latest Complaints

</h3>


</div>




<div class="table-responsive">


<table class="table table-hover mb-0">



<thead class="table-dark">


<tr>

<th>#</th>

<th>Subject</th>

<th>Status</th>

<th>Date</th>

</tr>


</thead>




<tbody>


<?php


$count=1;


if($complaints && $complaints->num_rows>0){


while($row=$complaints->fetch_assoc()){


?>


<tr>


<td>

<?php echo $count++; ?>

</td>




<td>

<?php

echo htmlspecialchars($row['subject']);

?>

</td>




<td>


<span class="badge bg-info text-dark">

<?php echo htmlspecialchars($row['status']); ?>

</span>


</td>




<td>

<?php

echo date(
"d-m-Y",
strtotime($row['created_at'])
);

?>

</td>


</tr>


<?php


}


}else{


?>


<tr>

<td colspan="4" class="text-center py-4">

No Complaints Found

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
ACTION BUTTONS
========================== -->


<div class="text-center mb-5">


<a href="export_overall_report_pdf.php"

class="btn btn-danger btn-lg me-3">


<i class="fas fa-file-pdf me-2"></i>

Export PDF

</a>




<button

onclick="window.print();"

class="btn btn-success btn-lg me-3">


<i class="fas fa-print me-2"></i>

Print Report

</button>





<a href="student_affairs_dashboard.php"

class="btn btn-dark btn-lg">


<i class="fas fa-arrow-left me-2"></i>

Back Dashboard

</a>


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

Student Affairs Office - Overall Report

</p>



<hr>




<div class="row">



<div class="col-md-4">


<i class="fas fa-chart-line fa-2x text-primary mb-2"></i>

<h6>

Professional Reports

</h6>

<small>

Real Time ERP Statistics

</small>


</div>





<div class="col-md-4">


<i class="fas fa-shield-alt fa-2x text-success mb-2"></i>

<h6>

Secure ERP System

</h6>

<small>

Protected Administrative Access

</small>


</div>





<div class="col-md-4">


<i class="fas fa-university fa-2x text-warning mb-2"></i>

<h6>

Global Tech University

</h6>

<small>

Digital Campus ERP

</small>


</div>



</div>




<hr>




<div class="row">


<div class="col-md-6 text-start">


<strong>

Generated By:

</strong>

<?php echo htmlspecialchars($admin_name); ?>


</div>



<div class="col-md-6 text-end">


<strong>

Generated On:

</strong>

<?php echo date("d F Y h:i A"); ?>


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