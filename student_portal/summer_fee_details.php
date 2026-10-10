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
LOAD STUDENT
=========================================*/

$stmt = $conn->prepare("
SELECT
students.*,
programs.program_name,
faculties.faculty_name
FROM students
INNER JOIN programs
ON students.program_id = programs.id
INNER JOIN faculties
ON programs.faculty_id = faculties.id
WHERE students.id=?
LIMIT 1
");

$stmt->bind_param("i",$student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if(!$student){
    die("Student not found.");
}

/*=========================================
CHECK APPROVED SUMMER ENROLLMENT
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
    die("
    <div style='padding:40px;text-align:center;font-family:Arial'>
    <h2 style='color:red;'>Summer Enrollment Not Approved</h2>
    <p>Your Summer Enrollment has not been approved by the Examination Office.</p>
    </div>
    ");
}

/*=========================================
CALCULATE FEE
=========================================*/

$totalSubjects = $summerData['total_subjects'];

$shift = strtolower(trim($student['shift']));

if($shift=="morning"){
    $perSubjectFee = 6000;
}
elseif($shift=="replica"){
    $perSubjectFee = 8000;
}
else{
    $perSubjectFee = 10000;
}

$totalFee = $perSubjectFee * $totalSubjects;

/*=========================================
CHECK PAYMENT RECORD
=========================================*/

$payment = $conn->prepare("
SELECT *
FROM summer_fee_payments
WHERE student_id=?
AND summer_enrollment_id=?
LIMIT 1
");

$payment->bind_param(
"ii",
$student_id,
$summerData['id']
);

$payment->execute();
$paymentData = $payment->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Summer Fee Details
</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{
background:#EEF2F7;
font-family:'Segoe UI',sans-serif;
}

.header-card{
background:linear-gradient(135deg,#0D47A1,#FF9800,#7B1FA2);
color:white;
padding:30px;
border-radius:18px;
margin-bottom:25px;
box-shadow:0 10px 25px rgba(0,0,0,.20);
}

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

<h2>
<i class="fas fa-money-check-dollar me-2"></i>
Summer Fee Details
</h2>

<p class="mb-0">
Summer Semester Payment Portal
</p>

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
$student['first_name']." ".$student['last_name']
);
?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Registration No</h6>

<?php echo htmlspecialchars($student['registration_no']); ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Roll No</h6>

<?php echo htmlspecialchars($student['roll_no']); ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Faculty</h6>

<?php echo htmlspecialchars($student['faculty_name']); ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Program</h6>

<?php echo htmlspecialchars($student['program_name']); ?>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Shift</h6>

<?php echo htmlspecialchars($student['shift']); ?>

</div>

</div>

</div>

</div>

<!-- =========================================
SUMMER FEE DETAILS
========================================= -->

<div class="info-card">

<h3 class="info-title">
<i class="fas fa-file-invoice-dollar me-2"></i>
Summer Fee Information
</h3>

<div class="row">

<div class="col-md-4">

<div class="student-box">

<h6>Total Subjects</h6>

<strong>

<?php echo $totalSubjects; ?>

</strong>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Per Subject Fee</h6>

<strong class="text-primary">

Rs.
<?php echo number_format($perSubjectFee); ?>

</strong>

</div>

</div>

<div class="col-md-4">

<div class="student-box">

<h6>Total Fee</h6>

<strong class="text-success fs-5">

Rs.
<?php echo number_format($totalFee); ?>

</strong>

</div>

</div>

</div>

</div>

<!-- =========================================
BANK DETAILS
========================================= -->

<div class="info-card">

<h3 class="info-title">
<i class="fas fa-building-columns me-2"></i>
Official Bank Details
</h3>

<table class="table table-bordered">

<tr>

<th width="35%">
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

<td class="fw-bold">
98690111864042
</td>

</tr>

<tr>

<th>
Amount To Pay
</th>

<td class="text-success fw-bold">

Rs.
<?php echo number_format($totalFee); ?>

</td>

</tr>

</table>

</div>

<div class="alert alert-info mt-3">

<i class="fas fa-circle-info me-2"></i>

Please deposit the fee in the above bank account and upload the original paid bank slip.

</div>

</div>

</div>

<!-- =========================================
UPLOAD PAYMENT SLIP
========================================= -->

<div class="info-card">

<h3 class="info-title">

<i class="fas fa-upload me-2"></i>

Upload Payment Slip

</h3>

<?php

if($paymentData){

?>

<div class="alert alert-success">

<strong>Payment Record Already Submitted.</strong>

</div>

<div class="row">

<div class="col-md-6">

<div class="student-box">

<h6>Current Status</h6>

<span class="badge bg-<?php

if($paymentData['payment_status']=="Approved"){

echo "success";

}elseif($paymentData['payment_status']=="Rejected"){

echo "danger";

}else{

echo "warning";

}

?>">

<?php echo $paymentData['payment_status']; ?>

</span>

</div>

</div>

<div class="col-md-6">

<div class="student-box">

<h6>Submitted Date</h6>

<?php

echo date(

"d M Y h:i A",

strtotime($paymentData['created_at'])

);

?>

</div>

</div>

</div>

<?php

if(!empty($paymentData['payment_slip'])){

?>

<div class="text-center mt-4">

<img

src="../uploads/summer_fee_slips/<?php echo htmlspecialchars($paymentData['payment_slip']); ?>"

class="img-fluid rounded shadow"

style="max-height:400px;">

</div>

<?php

}

?>

<?php

}else{

?>

<form

action="submit_summer_fee.php"

method="POST"

enctype="multipart/form-data">

<input

type="hidden"

name="summer_enrollment_id"

value="<?php echo $summerData['id']; ?>">

<input

type="hidden"

name="total_subjects"

value="<?php echo $totalSubjects; ?>">

<input

type="hidden"

name="per_subject_fee"

value="<?php echo $perSubjectFee; ?>">

<input

type="hidden"

name="total_fee"

value="<?php echo $totalFee; ?>">

<div class="mb-4">

<label class="form-label fw-bold">

Select Paid Bank Slip

</label>

<input

type="file"

name="payment_slip"

class="form-control"

accept=".jpg,.jpeg,.png,.pdf"

required>

<small class="text-muted">

Allowed: JPG, PNG, PDF

</small>

</div>

<div class="alert alert-warning">

<i class="fas fa-triangle-exclamation me-2"></i>

Please make sure:

<ul class="mt-2 mb-0">

<li>Payment has been deposited in the official GTU account.</li>

<li>The uploaded bank slip is clear and readable.</li>

<li>Wrong or fake slips will be rejected by the Finance Office.</li>

<li>Your payment will remain <strong>Pending</strong> until Finance Office verifies it.</li>

</ul>

</div>

<div class="text-end mt-4">

<button
type="submit"
class="btn btn-success btn-lg">

<i class="fas fa-upload me-2"></i>

Upload Payment Slip

</button>

<a
href="dashboard.php"
class="btn btn-secondary btn-lg ms-2">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</form>

<?php

}

?>

</div>

</div>

<!-- =========================================
IMPORTANT NOTICE
========================================= -->

<div class="info-card">

<h3 class="info-title">

<i class="fas fa-bullhorn me-2"></i>

Important Instructions

</h3>

<div class="alert alert-danger">

<ul class="mb-0">

<li>Only upload the original paid bank slip.</li>

<li>Fake or edited payment slips will be rejected.</li>

<li>Your payment will remain <strong>Pending</strong> until Finance Office verifies it.</li>

<li>After Finance Office approves the payment, your Summer Fee status will automatically become <strong>Approved</strong>.</li>

<li>If your payment is rejected, please upload a new valid bank slip.</li>

</ul>

</div>

</div>

<!-- =========================================
FOOTER
========================================= -->

<footer class="mt-5">

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-6">

<h5 class="fw-bold">

Global Tech University ERP

</h5>

<p class="text-muted mb-0">

Summer Fee Payment Portal

</p>

</div>

<div class="col-md-6 text-md-end">

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>