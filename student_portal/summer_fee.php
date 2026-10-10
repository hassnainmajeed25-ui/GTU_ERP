<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}

$student_id = $_SESSION['student_id'];

/*=========================================
STUDENT INFORMATION
=========================================*/

$student = $conn->prepare("

SELECT

students.*,

programs.program_name

FROM students

LEFT JOIN programs

ON students.program_id=programs.id

WHERE students.id=?

LIMIT 1

");

$student->bind_param("i",$student_id);

$student->execute();

$studentData = $student->get_result()->fetch_assoc();

/*=========================================
SUMMER ENROLLMENT
=========================================*/

$summer = $conn->prepare("

SELECT *

FROM summer_enrollments

WHERE student_id=?

AND status='Approved'

ORDER BY id DESC

LIMIT 1

");

$summer->bind_param("i",$student_id);

$summer->execute();

$summerData = $summer->get_result()->fetch_assoc();

if(!$summerData){

die("Summer Enrollment not approved.");

}

/*=========================================
BANK DETAILS
=========================================*/

$bank = $conn->query("

SELECT *

FROM bank_details

WHERE status='Active'

LIMIT 1

");

$bankData = $bank->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Summer Fee Payment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#F4F7FC;
font-family:'Segoe UI',sans-serif;
}

.header-card{
background:linear-gradient(135deg,#0B3D91,#1565C0);
color:white;
padding:30px;
border-radius:18px;
box-shadow:0 10px 25px rgba(0,0,0,.15);
margin-bottom:25px;
}

.info-card{
background:#fff;
border-radius:18px;
padding:20px;
margin-bottom:20px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
border-left:8px solid #FF9800;
}

.section-title{
font-size:24px;
font-weight:bold;
color:#0B3D91;
margin-bottom:20px;
}

.student-box{
background:#F8F9FA;
padding:15px;
border-radius:12px;
margin-bottom:15px;
}

.student-box h6{
color:#0B3D91;
font-weight:bold;
margin-bottom:5px;
}

.fee-box{
background:#FFF3E0;
padding:20px;
border-radius:15px;
text-align:center;
}

.fee-box h2{
color:#E65100;
font-weight:bold;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="header-card">

<h2>

<i class="fas fa-money-check-dollar"></i>

Summer Fee Payment

</h2>

<p class="mb-0">

Global Tech University ERP

</p>

</div>

<div class="info-card">

<h3 class="section-title">

Student Information

</h3>

<div class="row">

<div class="col-md-4">

<div class="student-box">

<h6>Name</h6>

<?php echo $studentData['first_name']." ".$studentData['last_name']; ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Registration No</h6>

<?php echo $studentData['registration_no']; ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Program</h6>

<?php echo $studentData['program_name']; ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Semester</h6>

<?php echo $studentData['semester']; ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Shift</h6>

<?php echo $studentData['shift']; ?>

</div>

</div>

<div class="col-md-4">

<div class="fee-box">

<h6>Total Subjects</h6>

<h2>

<?php echo $summerData['total_subjects']; ?>

</h2>

</div>

</div>

</div>

</div>

<!-- =========================================
FEE DETAILS
========================================= -->

<?php

/*=========================================
PER SUBJECT FEE
=========================================*/

$fee_per_subject = 0;

if($studentData['shift']=="Morning"){

    $fee_per_subject = 6000;

}
elseif($studentData['shift']=="Replica"){

    $fee_per_subject = 8000;

}
else{

    $fee_per_subject = 10000;

}

$total_fee = $summerData['total_subjects'] * $fee_per_subject;

/*=========================================
SUMMER SUBJECTS
=========================================*/

$subjects = $conn->prepare("

SELECT

summer_enrollment_subjects.*,

subjects.subject_name,

subjects.subject_code

FROM summer_enrollment_subjects

INNER JOIN subjects

ON summer_enrollment_subjects.subject_id=subjects.id

WHERE enrollment_id=?

ORDER BY subjects.subject_code ASC

");

$subjects->bind_param("i",$summerData['id']);

$subjects->execute();

$subjectList = $subjects->get_result();

?>

<div class="info-card">

<h3 class="section-title">

<i class="fas fa-file-invoice-dollar"></i>

Summer Fee Details

</h3>

<div class="table-responsive">

<table class="table table-bordered">

<thead class="table-warning">

<tr>

<th>#</th>

<th>Subject</th>

<th>Code</th>

<th>Repeat Type</th>

<th>Fee</th>

</tr>

</thead>

<tbody>

<?php

$sr = 1;

while($row = $subjectList->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td><?php echo htmlspecialchars($row['subject_name']); ?></td>

<td><?php echo htmlspecialchars($row['subject_code']); ?></td>

<td>

<span class="badge bg-primary">

<?php echo $row['repeat_type']; ?>

</span>

</td>

<td>

Rs. <?php echo number_format($fee_per_subject); ?>

</td>

</tr>

<?php

}

?>

</tbody>

<tfoot>

<tr>

<th colspan="4" class="text-end">

Total Fee

</th>

<th class="text-danger">

Rs. <?php echo number_format($total_fee); ?>

</th>

</tr>

</tfoot>

</table>

</div>

</div>

<!-- =========================================
BANK DETAILS
========================================= -->

<div class="info-card">

<h3 class="section-title">

<i class="fas fa-building-columns"></i>

Bank Account Details

</h3>

<div class="alert alert-primary">

<div class="row">

<div class="col-md-4">

<strong>Bank Name</strong><br>

<?php echo htmlspecialchars($bankData['bank_name']); ?>

</div>

<div class="col-md-4">

<strong>Account Title</strong><br>

<?php echo htmlspecialchars($bankData['account_title']); ?>

</div>

<div class="col-md-4">

<strong>Account Number</strong><br>

<span class="fw-bold text-danger">

<?php echo htmlspecialchars($bankData['account_number']); ?>

</span>

</div>

</div>

<hr>

<p class="mb-0">

<i class="fas fa-circle-info text-primary"></i>

Please deposit the exact fee amount into the above account and upload your payment slip below.

</p>

</div>

</div>

<!-- =========================================
UPLOAD PAYMENT SLIP
========================================= -->

<div class="info-card">

<h3 class="section-title">

<i class="fas fa-upload"></i>

Upload Payment Slip

</h3>

<form

action="submit_summer_fee.php"

method="POST"

enctype="multipart/form-data">

<input

type="hidden"

name="enrollment_id"

value="<?php echo $summerData['id']; ?>">

<input

type="hidden"

name="total_fee"

value="<?php echo $total_fee; ?>">

<div class="mb-3">

<label class="form-label">

Payment Slip

</label>

<input

type="file"

name="payment_slip"

class="form-control"

accept=".jpg,.jpeg,.png,.pdf"

required>

<small class="text-muted">

Allowed Files :

JPG, PNG, PDF

</small>

</div>

<div class="mb-3">

<label class="form-label">

Transaction ID / Deposit Slip No

</label>

<input

type="text"

name="transaction_id"

class="form-control"

placeholder="Enter Transaction ID"

required>

</div>

<div class="text-end">

<button

type="submit"

class="btn btn-success btn-lg">

<i class="fas fa-paper-plane me-2"></i>

Submit Fee

</button>

</div>

</form>

</div>

<!-- =========================================
FOOTER
========================================= -->

<footer class="mt-5">

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-6">

<div class="d-flex align-items-center">

<img
src="../assets/images/logo.png"
style="height:55px;"
class="me-3">

<div>

<h5 class="mb-1">

Global Tech University ERP

</h5>

<p class="mb-0 text-muted">

Summer Fee Payment Portal

</p>

</div>

</div>

</div>

<div class="col-md-6 text-md-end mt-3 mt-md-0">

<p class="mb-1">

<strong>

Version 1.0

</strong>

</p>

<p class="mb-1 text-muted">

Student Summer Fee Payment System

</p>

<p class="mb-0 text-secondary">

© <?php echo date("Y"); ?>

Global Tech University

</p>

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