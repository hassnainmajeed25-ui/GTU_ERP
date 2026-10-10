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
GET APPROVED APPLICATIONS
=========================================*/

$sql="

SELECT

applications.id,
applications.application_id,
applications.application_status,
applications.merit_marks,
applications.created_at,

students.first_name,
students.last_name,

faculties.faculty_name,

programs.program_name

FROM applications

INNER JOIN students
ON students.id=applications.student_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

WHERE applications.application_status='Approved'

ORDER BY applications.merit_marks DESC,
applications.created_at ASC

";

$result=$conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Merit List</title>

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

.sidebar .logo{

padding:25px;

text-align:center;

color:#fff;

font-size:24px;

font-weight:bold;

border-bottom:1px solid rgba(255,255,255,.15);

}

.sidebar a{

display:block;

padding:15px 20px;

color:white;

text-decoration:none;

}

.sidebar a:hover{

background:#173a8b;

}

.sidebar a.active{

background:#1d4ed8;

}

.main{

margin-left:260px;

padding:30px;

}

.topbar{

background:white;

padding:20px;

border-radius:12px;

box-shadow:0 3px 10px rgba(0,0,0,.1);

margin-bottom:25px;

display:flex;

justify-content:space-between;

align-items:center;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 3px 10px rgba(0,0,0,.1);

}

.table th{

background:#0B1F52;

color:white;

}

</style>

</head>

<body>

<div class="sidebar">

<div class="logo">

GTU ERP

</div>

<a href="dashboard.php">

<i class="fas fa-home me-2"></i>

Dashboard

</a>

<a href="manage_applications.php">

<i class="fas fa-file-alt me-2"></i>

Applications

</a>

<a href="merit_list.php"

class="active">

<i class="fas fa-ranking-star me-2"></i>

Merit List

</a>

<a href="selected_students.php">

<i class="fas fa-user-graduate me-2"></i>

Selected Students

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

Merit List

</h3>

<small class="text-muted">

Admission Office

</small>

</div>

<div>

Welcome,

<strong>

<?php echo $admin_name; ?>

</strong>

</div>

</div>

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

Approved Applications

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Application ID</th>

<th>Student</th>

<th>Faculty</th>

<th>Program</th>

<th>Merit</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>
<?php

if($result && $result->num_rows>0){

    $sr=1;

    while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['application_id']); ?>

</strong>

</td>

<td>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<span class="badge bg-primary">

<?php

if($row['merit_marks']>0){

echo number_format($row['merit_marks'],2)." %";

}else{

echo "Pending";

}

?>

</span>

</td>

<td>

<span class="badge bg-success">

<?php echo htmlspecialchars($row['application_status']); ?>

</span>

</td>

<td>

<a

href="view_application.php?id=<?php echo $row['id'];?>"

class="btn btn-info btn-sm">

<i class="fas fa-eye"></i>

View

</a>

<a
href="calculate_merit.php?id=<?php echo $row['id'];?>"
class="btn btn-warning btn-sm"
onclick="return confirm('Calculate Merit?');">

<i class="fas fa-calculator"></i>

Calculate Merit

</a>

<?php

if($row['merit_marks']>0){

?>

<a

href="select_student.php?id=<?php echo $row['id'];?>"

class="btn btn-success btn-sm"

onclick="return confirm('Select this student for Merit List?');">

<i class="fas fa-user-check"></i>

Select

</a>

<?php

}else{

?>

<button

class="btn btn-secondary btn-sm"

disabled>

No Merit

</button>

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

<td colspan="8" class="text-center">

No Approved Applications Found.

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
</table>

</div>

</div>

</div>
<?php if(isset($_GET['calculated'])){ ?>

<div class="alert alert-success mt-3">
    <i class="fas fa-check-circle me-2"></i>
    Merit Calculated Successfully.
</div>

<?php } ?>

<?php if(isset($_GET['success'])){ ?>

<div class="alert alert-success mt-3">
    <i class="fas fa-check-circle me-2"></i>
    Student has been selected successfully.
</div>

<?php } ?>

<?php if(isset($_GET['error'])){ ?>

<div class="alert alert-danger mt-3">
    <i class="fas fa-times-circle me-2"></i>
    Unable to select student.
</div>

<?php } ?>

<?php

if(isset($_GET['error'])){

?>

<div class="alert alert-danger mt-3">

<i class="fas fa-times-circle me-2"></i>

Unable to select student.

</div>

<?php

}

?>

<!-- ===============================
SUMMARY CARDS
=============================== -->

<div class="row mt-4">

<?php

$totalApproved=$conn->query("SELECT COUNT(*) total FROM applications WHERE application_status='Approved'")->fetch_assoc()['total'];

$totalSelected=$conn->query("SELECT COUNT(*) total FROM applications WHERE application_status='Selected'")->fetch_assoc()['total'];

?>

<div class="col-md-6">

<div class="card shadow border-0">

<div class="card-body text-center">

<i class="fas fa-file-alt fa-3x text-primary mb-3"></i>

<h2>

<?php echo $totalApproved; ?>

</h2>

<h5>

Approved Applications

</h5>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card shadow border-0">

<div class="card-body text-center">

<i class="fas fa-user-check fa-3x text-success mb-3"></i>

<h2>

<?php echo $totalSelected; ?>

</h2>

<h5>

Selected Students

</h5>

</div>

</div>

</div>

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