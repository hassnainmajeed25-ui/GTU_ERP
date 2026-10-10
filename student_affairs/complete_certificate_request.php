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

$admin_id=$_SESSION['admin_id'];
$admin_name=$_SESSION['admin_name'];

/*=========================================
GET REQUEST ID
=========================================*/

if(!isset($_GET['id'])){
    header("Location: certificate_requests.php");
    exit;
}

$id=(int)$_GET['id'];

/*=========================================
GET REQUEST DETAILS
=========================================*/

$stmt=$conn->prepare("

SELECT

certificate_requests.*,

students.first_name,

students.last_name,

students.email,

students.mobile

FROM certificate_requests

LEFT JOIN students
ON students.id=certificate_requests.student_id

WHERE certificate_requests.id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location: certificate_requests.php");

exit;

}

$row=$result->fetch_assoc();

/*=========================================
MARK AS COMPLETED
=========================================*/

if(isset($_POST['complete'])){

$remarks=trim($_POST['remarks']);

$update=$conn->prepare("

UPDATE certificate_requests

SET

status='Completed',

remarks=?,

approved_by=?,

approved_at=NOW()

WHERE id=?

");

$update->bind_param(

"sii",

$remarks,

$admin_id,

$id

);

if($update->execute()){

header("Location: certificate_requests.php?success=completed");

exit;

}else{

$error="Unable to complete request.";

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Complete Certificate Request</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:linear-gradient(135deg,#eefbf1,#ffffff);
font-family:Arial,Helvetica,sans-serif;
}

.main{
max-width:1150px;
margin:40px auto;
}

.card{
border:none;
border-radius:20px;
box-shadow:0 10px 30px rgba(0,0,0,.12);
overflow:hidden;
}

.header{
background:linear-gradient(90deg,#198754,#20c997);
color:#fff;
padding:25px;
}

.logo{
width:70px;
height:70px;
}

.profile-card{
background:#fff;
border-radius:15px;
padding:25px;
box-shadow:0 3px 12px rgba(0,0,0,.08);
text-align:center;
height:100%;
}

.info-card{
background:#fff;
border-radius:15px;
padding:25px;
box-shadow:0 3px 12px rgba(0,0,0,.08);
height:100%;
}

.table-borderless th{
width:35%;
color:#555;
}

</style>

</head>

<body>

<div class="main">

<div class="card">

<div class="header">

<div class="d-flex align-items-center">

<img

src="../assets/images/logo.png"

class="logo me-3"

alt="GTU Logo">

<div>

<h2 class="mb-1">

Complete Certificate Request

</h2>

<p class="mb-0">

Global Tech University ERP • Student Affairs Office

</p>

</div>

</div>

</div>

<div class="card-body p-4">

<?php if(isset($error)){ ?>

<div class="alert alert-danger">

<i class="fas fa-circle-exclamation me-2"></i>

<?php echo $error; ?>

</div>

<?php } ?>

<div class="row g-4">

<!-- Student Profile -->

<div class="col-lg-4">

<div class="profile-card">

<div class="mb-3">

<i class="fas fa-user-graduate fa-5x text-success"></i>

</div>

<h4 class="fw-bold">

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</h4>

<hr>

<p class="mb-2">

<i class="fas fa-envelope text-primary me-2"></i>

<?php echo htmlspecialchars($row['email']); ?>

</p>

<p class="mb-2">

<i class="fas fa-phone text-success me-2"></i>

<?php echo htmlspecialchars($row['mobile']); ?>

</p>

<p class="mb-0">

<i class="fas fa-hashtag text-danger me-2"></i>

<strong>

<?php echo htmlspecialchars($row['request_no']); ?>

</strong>

</p>

</div>

</div>

<!-- Certificate Details -->

<div class="col-lg-8">

<div class="info-card">

<h4 class="text-success mb-4">

<i class="fas fa-circle-check me-2"></i>

Completion Details

</h4>

<table class="table table-borderless">

<tr>

<th>Certificate Type</th>

<td>

<span class="badge bg-primary fs-6">

<?php echo htmlspecialchars($row['certificate_type']); ?>

</span>

</td>

</tr>

<tr>

<th>Purpose</th>

<td>

<?php echo nl2br(htmlspecialchars($row['purpose'])); ?>

</td>

</tr>

<tr>

<th>Request Date</th>

<td>

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</td>

</tr>

<tr>

<th>Current Status</th>

<td>

<span class="badge bg-warning text-dark fs-6">

<?php echo htmlspecialchars($row['status']); ?>

</span>

</td>

</tr>

</table>

<form method="POST">

<div class="mt-4">

<label class="form-label fw-bold">

<i class="fas fa-comments me-2 text-success"></i>

Completion Remarks

</label>

<textarea

name="remarks"

class="form-control"

rows="6"

placeholder="Write completion remarks for the student..."

required><?php echo htmlspecialchars($row['remarks']); ?></textarea>

</div>

<div class="d-flex justify-content-between mt-4">

<a

href="certificate_requests.php"

class="btn btn-outline-secondary btn-lg">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="complete"

class="btn btn-success btn-lg px-5">

<i class="fas fa-circle-check me-2"></i>

Mark as Completed

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<hr class="mt-5">

<div class="card border-0 shadow-lg">

<div class="card-body text-center py-4">

<img

src="../assets/images/logo.png"

width="85"

class="mb-3"

alt="GTU Logo">

<h3 class="fw-bold text-success">

Global Tech University ERP

</h3>

<p class="text-muted mb-1">

Student Affairs Office

</p>

<p class="text-success fw-semibold">

Certificate Request Completion Panel

</p>

<hr>

<div class="row">

<div class="col-md-4">

<i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>

<h6>Secure ERP</h6>

<small class="text-muted">

Protected Administrative Access

</small>

</div>

<div class="col-md-4">

<i class="fas fa-award fa-2x text-warning mb-2"></i>

<h6>Professional Workflow</h6>

<small class="text-muted">

Student Services Management

</small>

</div>

<div class="col-md-4">

<i class="fas fa-university fa-2x text-success mb-2"></i>

<h6>Global Tech University</h6>

<small class="text-muted">

Digital Campus ERP System

</small>

</div>

</div>

<hr>

<p class="small text-secondary mb-0">

© <?php echo date("Y"); ?> Global Tech University ERP System. All Rights Reserved.

</p>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>