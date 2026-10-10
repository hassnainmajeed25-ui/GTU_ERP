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
REJECT REQUEST
=========================================*/

if(isset($_POST['reject'])){

$remarks=trim($_POST['remarks']);

$update=$conn->prepare("

UPDATE certificate_requests

SET

status='Rejected',

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

header("Location: certificate_requests.php?success=rejected");

exit;

}else{

$error="Unable to reject request.";

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Reject Certificate Request</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:linear-gradient(135deg,#eef4fb,#fdfdfd);

font-family:Arial,Helvetica,sans-serif;

}

.main{

max-width:1100px;

margin:40px auto;

}

.card{

border:none;

border-radius:20px;

box-shadow:0 10px 30px rgba(0,0,0,.12);

overflow:hidden;

}

.header{

background:linear-gradient(90deg,#8B0000,#dc3545);

color:white;

padding:25px;

}

.logo{

width:70px;

height:70px;

}

.profile-card{

border-radius:15px;

background:#fff;

box-shadow:0 3px 12px rgba(0,0,0,.08);

padding:25px;

text-align:center;

height:100%;

}

.info-card{

border-radius:15px;

background:#fff;

box-shadow:0 3px 12px rgba(0,0,0,.08);

padding:25px;

height:100%;

}

.table-borderless th{

color:#555;

width:35%;

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

Reject Certificate Request

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

<div class="col-lg-4">

<div class="profile-card">

<i class="fas fa-user-graduate fa-5x text-danger mb-3"></i>

<h4>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</h4>

<hr>

<p>

<strong>Email</strong>

<br>

<?php echo htmlspecialchars($row['email']); ?>

</p>

<p>

<strong>Mobile</strong>

<br>

<?php echo htmlspecialchars($row['mobile']); ?>

</p>

<p>

<strong>Request No</strong>

<br>

<?php echo htmlspecialchars($row['request_no']); ?>

</p>

</div>

</div>

<div class="col-lg-8">

<div class="info-card">

<h4 class="text-danger mb-4">

<i class="fas fa-file-circle-xmark me-2"></i>

Certificate Request Details

</h4>

<table class="table table-borderless">

<tr>

<th>Certificate</th>

<td>

<?php echo htmlspecialchars($row['certificate_type']); ?>

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

<th>Status</th>

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

Reason for Rejection

</label>

<textarea

name="remarks"

class="form-control"

rows="6"

placeholder="Enter rejection reason for the student..."

required></textarea>

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

name="reject"

class="btn btn-danger btn-lg px-5">

<i class="fas fa-times-circle me-2"></i>

Reject Request

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<hr class="mt-5">

<div class="card border-0 shadow-sm">

<div class="card-body text-center">

<img

src="../assets/images/logo.png"

width="85"

class="mb-3"

alt="GTU Logo">

<h3 class="fw-bold text-danger">

Global Tech University ERP

</h3>

<p class="text-muted mb-1">

Student Affairs Office

</p>

<p class="text-muted">

Certificate Request Rejection Panel

</p>

<hr>

<p class="small text-secondary mb-0">

© <?php echo date("Y"); ?> Global Tech University ERP System

</p>

<p class="small text-secondary">

All Rights Reserved.

</p>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>