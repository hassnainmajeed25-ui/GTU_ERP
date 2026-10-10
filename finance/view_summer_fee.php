<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location: ../admins/login.php");
    exit;

}

if($_SESSION['department']!="Finance Office"){

    header("Location: ../admins/login.php");
    exit;

}

/*=========================================
CHECK ID
=========================================*/

if(!isset($_GET['id'])){

    die("Invalid Request");

}

$id=(int)$_GET['id'];

/*=========================================
LOAD PAYMENT
=========================================*/

$stmt=$conn->prepare("

SELECT

summer_fee_payments.*,

students.first_name,
students.last_name,
students.registration_no,
students.roll_no,
students.shift,

programs.program_name,
faculties.faculty_name

FROM summer_fee_payments

INNER JOIN students
ON students.id=summer_fee_payments.student_id

INNER JOIN programs
ON programs.id=students.program_id

INNER JOIN faculties
ON faculties.id=programs.faculty_id

WHERE summer_fee_payments.id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$data=$stmt->get_result()->fetch_assoc();

if(!$data){

die("Payment Record Not Found");

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,initial-scale=1.0">

<title>

Summer Fee Details

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#EEF2F7;

font-family:'Segoe UI',sans-serif;

}

/*=============================
HEADER
=============================*/

.header-card{

background:linear-gradient(135deg,#000000,#FF9800,#29B6F6,#7B1FA2);

color:white;

padding:30px;

border-radius:18px;

margin-bottom:25px;

box-shadow:0 10px 25px rgba(0,0,0,.25);

}

/*=============================
INFO CARD
=============================*/

.info-card{

background:white;

border-radius:18px;

padding:25px;

margin-bottom:20px;

box-shadow:0 5px 15px rgba(0,0,0,.10);

border-left:8px solid #7B1FA2;

}

.info-title{

font-size:24px;

font-weight:bold;

color:#7B1FA2;

margin-bottom:20px;

}

.student-box{

background:#F8F9FA;

border-radius:12px;

padding:15px;

margin-bottom:15px;

}

.student-box h6{

font-weight:bold;

color:#FF9800;

margin-bottom:5px;

}

</style>

</head>

<body>

<div class="container py-4">

<div class="header-card">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

<i class="fas fa-money-check-dollar"></i>

Summer Fee Payment Details

</h2>

<p class="mb-0">

Finance Office Verification Panel

</p>

</div>

<div>

<i class="fas fa-building-columns"

style="font-size:70px;opacity:.25;"></i>

</div>

</div>

</div>

<!-- =========================================
STUDENT INFORMATION
========================================= -->

<div class="info-card">

<h3 class="info-title">

<i class="fas fa-user-graduate me-2"></i>

Student Information

</h3>

<div class="row">

<div class="col-md-4">

<div class="student-box">

<h6>Student Name</h6>

<?php

echo htmlspecialchars(

$data['first_name']." ".$data['last_name']

);

?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Registration No</h6>

<?php echo htmlspecialchars($data['registration_no']); ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Roll No</h6>

<?php echo htmlspecialchars($data['roll_no']); ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Faculty</h6>

<?php echo htmlspecialchars($data['faculty_name']); ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Program</h6>

<?php echo htmlspecialchars($data['program_name']); ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Shift</h6>

<?php echo htmlspecialchars($data['shift']); ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Total Subjects</h6>

<?php echo $data['total_subjects']; ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Per Subject Fee</h6>

<strong class="text-primary">

Rs. <?php echo number_format($data['per_subject_fee']); ?>

</strong>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Total Fee</h6>

<strong class="text-success fs-5">

Rs. <?php echo number_format($data['total_fee']); ?>

</strong>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Payment Status</h6>

<span class="badge bg-<?php

if($data['payment_status']=="Approved"){

echo "success";

}elseif($data['payment_status']=="Rejected"){

echo "danger";

}else{

echo "warning";

}

?>">

<?php echo $data['payment_status']; ?>

</span>

</div>

</div>

<div class="col-md-8">

<div class="student-box">

<h6>Application Date</h6>

<?php

echo date(

"d M Y h:i A",

strtotime($data['created_at'])

);

?>

</div>

</div>

</div>

</div>

<!-- =========================================
PAYMENT SLIP & BANK DETAILS
========================================= -->

<div class="info-card">

<h3 class="info-title">

<i class="fas fa-file-invoice-dollar me-2"></i>

Payment Slip & Bank Information

</h3>

<div class="row">

<!-- Uploaded Slip -->

<div class="col-lg-6">

<div class="student-box">

<h6>

<i class="fas fa-upload text-primary me-2"></i>

Uploaded Payment Slip

</h6>

<?php

if(!empty($data['payment_slip'])){

?>

<div class="text-center mt-3">

<img

src="../uploads/summer_fee_slips/<?php echo htmlspecialchars($data['payment_slip']); ?>"

class="img-fluid rounded shadow"

style="max-height:350px;">

</div>

<div class="text-center mt-3">

<a

href="../uploads/summer_fee_slips/<?php echo htmlspecialchars($data['payment_slip']); ?>"

target="_blank"

class="btn btn-primary">

<i class="fas fa-eye me-2"></i>

View Full Slip

</a>

<a

href="../uploads/summer_fee_slips/<?php echo htmlspecialchars($data['payment_slip']); ?>"

download

class="btn btn-success">

<i class="fas fa-download me-2"></i>

Download

</a>

</div>

<?php

}else{

?>

<div class="alert alert-danger mt-3">

<i class="fas fa-circle-xmark me-2"></i>

Payment Slip Not Uploaded.

</div>

<?php

}

?>

</div>

</div>

<!-- Bank Details -->

<div class="col-lg-6">

<div class="student-box">

<h6>

<i class="fas fa-building-columns text-warning me-2"></i>

Official Bank Details

</h6>

<table class="table table-bordered mt-3">

<tr>

<th width="40%">

Bank Name

</th>

<td>

Meezan Bank Limited

</td>

</tr>

<tr>

<th>

Account Title

</th>

<td>

Ali Hamza

</td>

</tr>

<tr>

<th>

Account Number

</th>

<td>

98690111864042

</td>

</tr>

<tr>

<th>

Payment Amount

</th>

<td>

<strong class="text-success">

Rs.

<?php echo number_format($data['total_fee']); ?>

</strong>

</td>

</tr>

<tr>

<th>

Current Status

</th>

<td>

<span class="badge bg-<?php

if($data['payment_status']=="Approved"){

echo "success";

}elseif($data['payment_status']=="Rejected"){

echo "danger";

}else{

echo "warning";

}

?>">

<?php echo $data['payment_status']; ?>

</span>

</td>

</tr>

</table>

<div class="alert alert-info mt-3">

<i class="fas fa-circle-info me-2"></i>

Finance Office will verify the uploaded payment slip before approving the Summer Enrollment Fee.

</div>

</div>

</div>

</div>

</div>

<!-- =========================================
FINANCE VERIFICATION
========================================= -->

<div class="info-card">

<h3 class="info-title">

<i class="fas fa-user-check me-2"></i>

Finance Verification

</h3>

<form action="process_summer_fee.php" method="POST">

<input
type="hidden"
name="payment_id"
value="<?php echo $data['id']; ?>">

<div class="mb-3">

<label class="form-label fw-bold">

Finance Remarks

</label>

<textarea

name="finance_remarks"

class="form-control"

rows="5"

placeholder="Write approval or rejection remarks here..."

><?php echo htmlspecialchars($data['finance_remarks']); ?></textarea>

</div>

<div class="row">

<div class="col-md-6">

<button

type="submit"

name="action"

value="Approved"

class="btn btn-success btn-lg w-100">

<i class="fas fa-circle-check me-2"></i>

Approve Payment

</button>

</div>

<div class="col-md-6">

<button

type="submit"

name="action"

value="Rejected"

class="btn btn-danger btn-lg w-100">

<i class="fas fa-circle-xmark me-2"></i>

Reject Payment

</button>

</div>

</div>

</form>

</div>

<!-- =========================================
FOOTER
========================================= -->

<footer class="mt-5">

<div class="card border-0 shadow">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-6">

<h5 class="fw-bold text-dark">

Global Tech University ERP

</h5>

<p class="text-muted mb-0">

Finance Office Summer Fee Verification System

</p>

</div>

<div class="col-md-6 text-end">

<strong>

Version 1.0

</strong>

<br>

<span class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University

</span>

</div>

</div>

</div>

</div>

</footer>

</div>
<!-- Container End -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>