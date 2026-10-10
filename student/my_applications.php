<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit;
}

$student_id=$_SESSION['student_id'];

$sql=$conn->prepare("

SELECT

a.id,
a.application_id,
a.application_status,
a.registration_no,

f.faculty_name,

p.program_name,

pay.payment_status,

pay.processing_fee,

pay.challan_no,

a.created_at

FROM applications a

LEFT JOIN faculties f
ON a.faculty_id=f.id

LEFT JOIN programs p
ON a.program_id=p.id

LEFT JOIN application_payments pay
ON pay.application_id=a.id

WHERE a.student_id=?

LIMIT 1

");

$sql->bind_param("i",$student_id);

$sql->execute();

$result=$sql->get_result();

$data=$result->fetch_assoc();

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>My Applications</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial;

}

.card{

margin-top:30px;

border:none;

border-radius:15px;

box-shadow:0 5px 20px rgba(0,0,0,.10);

}

.card-header{

background:#0B1F52;

color:white;

font-size:25px;

font-weight:bold;

padding:20px;

}

.table th{

width:35%;

background:#f8f9fa;

}

.badge{

font-size:14px;

padding:8px 14px;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fa-solid fa-folder-open"></i>

My Application

</div>

<div class="card-body">
    <?php if($result->num_rows==0){ ?>

<div class="alert alert-warning text-center">

<h4>

<i class="fa-solid fa-circle-info"></i>

No Application Found

</h4>

<p>

You have not submitted any admission application yet.

</p>

<a href="apply/personal.php" class="btn btn-primary">

Apply Admission

</a>

</div>

<?php } else { ?>

<table class="table table-bordered">

<tr>

<th>

Application ID

</th>

<td>

<?php echo $data['application_id']; ?>

</td>

</tr>

<tr>

<th>

Faculty

</th>

<td>

<?php echo $data['faculty_name']; ?>

</td>

</tr>

<tr>

<th>

Program

</th>

<td>

<?php echo $data['program_name']; ?>

</td>

</tr>

<tr>

<th>

Application Status

</th>

<td>

<?php

$status=$data['application_status'];

if($status=="Submitted"){

echo '<span class="badge bg-primary">'.$status.'</span>';

}

elseif($status=="Under Review"){

echo '<span class="badge bg-warning text-dark">'.$status.'</span>';

}

elseif($status=="Selected"){

echo '<span class="badge bg-success">'.$status.'</span>';

}

elseif($status=="Rejected"){

echo '<span class="badge bg-danger">'.$status.'</span>';

}

else{

echo '<span class="badge bg-secondary">'.$status.'</span>';

}

?>

</td>

</tr>

<tr>

<th>

Payment Status

</th>

<td>

<?php

$payment=$data['payment_status'];

if($payment=="Paid"){

echo '<span class="badge bg-success">'.$payment.'</span>';

}

elseif($payment=="Verification Pending"){

echo '<span class="badge bg-warning text-dark">'.$payment.'</span>';

}

elseif($payment=="Pending"){

echo '<span class="badge bg-danger">'.$payment.'</span>';

}

else{

echo '<span class="badge bg-secondary">'.$payment.'</span>';

}

?>

</td>

</tr>

<tr>

<th>

Processing Fee

</th>

<td>

Rs. <?php echo number_format($data['processing_fee'],2); ?>

</td>

</tr>

<tr>

<th>

Challan Number

</th>

<td>

<?php echo $data['challan_no']; ?>

</td>

</tr>

<tr>

<th>

Registration Number

</th>

<td>

<?php

if(empty($data['registration_no'])){

echo "<span class='text-muted'>Not Generated Yet</span>";

}else{

echo "<strong>".$data['registration_no']."</strong>";

}

?>

</td>

</tr>

<tr>

<th>

Application Date

</th>

<td>

<?php echo date("d M Y",strtotime($data['created_at'])); ?>

</td>

</tr>

</table>

<hr>

<h5 class="mb-3">

Application Progress

</h5>

<div class="progress mb-4" style="height:25px;">

<div class="progress-bar progress-bar-striped progress-bar-animated bg-success"

style="width:70%;">

70% Completed

</div>

</div>

<?php } ?>
<hr>

<h5 class="mb-3">

<i class="fa-solid fa-list-check"></i>

Application Timeline

</h5>

<ul class="list-group mb-4">

<li class="list-group-item d-flex justify-content-between">

Personal Information

<span class="badge bg-success">

Completed

</span>

</li>

<li class="list-group-item d-flex justify-content-between">

Academic Information

<span class="badge bg-success">

Completed

</span>

</li>

<li class="list-group-item d-flex justify-content-between">

Program Selection

<span class="badge bg-success">

Completed

</span>

</li>

<li class="list-group-item d-flex justify-content-between">

Documents Uploaded

<span class="badge bg-success">

Completed

</span>

</li>

<li class="list-group-item d-flex justify-content-between">

Application Submitted

<span class="badge bg-primary">

<?php echo $data['application_status']; ?>

</span>

</li>

<li class="list-group-item d-flex justify-content-between">

Payment Status

<span class="badge bg-warning text-dark">

<?php echo $data['payment_status']; ?>

</span>

</li>

<li class="list-group-item d-flex justify-content-between">

Merit List

<span class="badge bg-secondary">

Pending

</span>

</li>

<li class="list-group-item d-flex justify-content-between">

Registration Number

<span class="badge bg-secondary">

<?php

if(empty($data['registration_no'])){

echo "Pending";

}else{

echo "Generated";

}

?>

</span>

</li>

</ul>

<div class="row">

<div class="col-md-3 mb-2">

<a href="apply/review.php" class="btn btn-primary w-100">

<i class="fa-solid fa-eye"></i>

View Application

</a>

</div>

<div class="col-md-3 mb-2">

<button onclick="window.print()" class="btn btn-warning w-100">

<i class="fa-solid fa-print"></i>

Print

</button>

</div>

<div class="col-md-3 mb-2">

<a href="apply/fee_challan.php" class="btn btn-success w-100">

<i class="fa-solid fa-download"></i>

Fee Challan

</a>

</div>

<div class="col-md-3 mb-2">

<a href="dashboard.php" class="btn btn-secondary w-100">

<i class="fa-solid fa-arrow-left"></i>

Dashboard

</a>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>