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
GET REQUEST
=========================================*/

$stmt=$conn->prepare("

SELECT *

FROM certificate_requests

WHERE id=?

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
APPROVE REQUEST
=========================================*/

if(isset($_POST['approve'])){

$remarks=trim($_POST['remarks']);

$update=$conn->prepare("

UPDATE certificate_requests

SET

status='Approved',

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

header("Location: certificate_requests.php?success=approved");

exit;

}else{

$error="Unable to approve request.";

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Approve Certificate Request</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef4fb;

font-family:Arial,Helvetica,sans-serif;

}

.sidebar{

width:260px;

height:100vh;

position:fixed;

left:0;

top:0;

overflow-y:auto;

background:linear-gradient(180deg,#0B1F52,#16367d);

}

.sidebar h3{

color:#fff;

text-align:center;

padding:20px;

}

.sidebar a{

display:block;

padding:15px 20px;

color:#fff;

text-decoration:none;

}

.sidebar a:hover{

background:#2563eb;

}

.sidebar a.active{

background:#2563eb;

}

.main{

margin-left:260px;

padding:30px;

}

.topbar{

background:#fff;

padding:20px;

border-radius:15px;

display:flex;

justify-content:space-between;

align-items:center;

box-shadow:0 4px 12px rgba(0,0,0,.08);

margin-bottom:25px;

}

.logo{

width:55px;

height:55px;

margin-right:15px;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 4px 12px rgba(0,0,0,.08);

}

</style>

</head>

<body>

<div class="main">

<div class="card shadow-lg border-0">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">

<div class="d-flex align-items-center">

<img src="../assets/images/logo.png"

width="70"

class="me-3">

<div>

<h3 class="mb-0">

Approve Certificate Request

</h3>

<small>

Global Tech University ERP

</small>

</div>

</div>

</div>

<div class="card-body">

<div class="row">

<div class="col-lg-4">

<div class="card border-0 shadow-sm">

<div class="card-body text-center">

<i class="fas fa-user-circle fa-5x text-primary mb-3"></i>

<h4>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</h4>

<hr>

<p>

<strong>Email :</strong>

<br>

<?php echo htmlspecialchars($row['email']); ?>

</p>

<p>

<strong>Mobile :</strong>

<br>

<?php echo htmlspecialchars($row['mobile']); ?>

</p>

</div>

</div>

</div>

<div class="col-lg-8">

<div class="card border-0 shadow-sm">

<div class="card-body">

<h4 class="text-primary mb-4">

Certificate Information

</h4>
<table class="table table-borderless">

<tr>

<th width="35%">

Request Number

</th>

<td>

<?php echo htmlspecialchars($row['request_no']); ?>

</td>

</tr>

<tr>

<th>

Certificate Type

</th>

<td>

<span class="badge bg-primary fs-6">

<?php echo htmlspecialchars($row['certificate_type']); ?>

</span>

</td>

</tr>

<tr>

<th>

Purpose

</th>

<td>

<?php echo nl2br(htmlspecialchars($row['purpose'])); ?>

</td>

</tr>

<tr>

<th>

Request Date

</th>

<td>

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</td>

</tr>

<tr>

<th>

Current Status

</th>

<td>

<?php

if($row['status']=="Pending"){

echo "<span class='badge bg-warning text-dark fs-6'>Pending</span>";

}elseif($row['status']=="Approved"){

echo "<span class='badge bg-success fs-6'>Approved</span>";

}elseif($row['status']=="Rejected"){

echo "<span class='badge bg-danger fs-6'>Rejected</span>";

}else{

echo "<span class='badge bg-primary fs-6'>Completed</span>";

}

?>

</td>

</tr>

</table>

<hr class="my-4">

<?php if(isset($error)){ ?>

<div class="alert alert-danger">

<i class="fas fa-circle-exclamation me-2"></i>

<?php echo $error; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-4">

<label class="form-label fw-bold">

<i class="fas fa-comments me-2 text-primary"></i>

Admin Remarks

</label>

<textarea

name="remarks"

class="form-control"

rows="6"

placeholder="Write approval remarks here..."><?php echo htmlspecialchars($row['remarks']); ?></textarea>

</div>

<div class="d-flex justify-content-between">

<a

href="certificate_requests.php"

class="btn btn-outline-secondary btn-lg">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="approve"

class="btn btn-success btn-lg px-5">

<i class="fas fa-check-circle me-2"></i>

Approve Request

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

width="80"

class="mb-3"

alt="GTU Logo">

<h4 class="fw-bold text-primary">

Global Tech University ERP

</h4>

<p class="text-muted mb-1">

Student Affairs Office

</p>

<p class="text-muted">

Certificate Request Approval Panel

</p>

<p class="small text-secondary">

© <?php echo date("Y"); ?> Global Tech University ERP System. All Rights Reserved.

</p>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>