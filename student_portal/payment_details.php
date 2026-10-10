<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

/*=========================================
GET STUDENT INFORMATION
=========================================*/

$student = $conn->prepare("

SELECT

students.*,

programs.program_name,

faculties.faculty_name

FROM students

LEFT JOIN programs
ON students.program_id = programs.id

LEFT JOIN faculties
ON programs.faculty_id = faculties.id

WHERE students.id=?

LIMIT 1

");

$student->bind_param("i",$student_id);

$student->execute();

$studentData = $student->get_result()->fetch_assoc();

/*=========================================
NEXT SEMESTER
=========================================*/

$nextSemester = $studentData['semester'] + 1;

/*=========================================
NEXT SEMESTER FEE
=========================================*/

$fee = $conn->prepare("

SELECT *

FROM fee_structure

WHERE

program_id=?
AND semester=?
AND shift=?
AND session_id=?
AND status='Active'

LIMIT 1

");

$fee->bind_param(

"iisi",

$studentData['program_id'],
$nextSemester,
$studentData['shift'],
$studentData['session_id']

);

$fee->execute();

$feeData = $fee->get_result()->fetch_assoc();

if(!$feeData){

die("No fee record found.");

}

/*=========================================
TOTAL
=========================================*/

$total =

$feeData['admission_fee'] +

$feeData['tuition_fee'] +

$feeData['exam_fee'] +

$feeData['library_fee'] +

$feeData['lab_fee'] +

$feeData['other_fee'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#eef7ff;
    font-family:'Segoe UI',sans-serif;
}

.main-card{

    max-width:1200px;
    margin:40px auto;
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 0 25px rgba(0,0,0,.10);

}

.header{

    background:#0d6efd;
    color:#fff;
    padding:20px;
    text-align:center;
    font-size:28px;
    font-weight:bold;

}

.section-title{

    color:#0d6efd;
    font-weight:bold;
    margin-bottom:20px;

}

.info-card{

    border:2px solid #E6FF00;
    border-radius:15px;
    padding:20px;
    background:#fff;

}

.bank-card{

    border:2px solid #0d6efd;
    border-radius:15px;
    padding:20px;
    background:#f8fbff;

}

.form-control{

    height:50px;
    border-radius:10px;

}

.btn-upload{

    background:#E6FF00;
    color:#000;
    font-weight:bold;
    border:none;
    padding:12px;

}

.btn-upload:hover{

    background:#d4eb00;

}

</style>

</head>

<body>

<div class="main-card">

<div class="header">

<i class="fas fa-building-columns me-2"></i>

Payment Details

</div>

<div class="container p-4">

<div class="row">

<div class="col-md-6">

<div class="info-card">

<h4 class="section-title">

Student Information

</h4>

<p><strong>Registration No :</strong> <?php echo $studentData['registration_no']; ?></p>

<p><strong>Roll No :</strong> <?php echo $studentData['roll_no']; ?></p>

<p><strong>Faculty :</strong> <?php echo $studentData['faculty_name']; ?></p>

<p><strong>Program :</strong> <?php echo $studentData['program_name']; ?></p>

<p><strong>Semester :</strong> <?php echo $nextSemester; ?></p>

<p><strong>Shift :</strong> <?php echo $studentData['shift']; ?></p>

</div>

</div>

<div class="col-md-6">

<div class="bank-card">

<h4 class="section-title">

University Bank Details

</h4>

<p><strong>Bank Name :</strong> Bank of Punjab</p>

<p><strong>Account Title :</strong> GTU ERP Fee Collection</p>

<p><strong>Account Number :</strong> 123456789012</p>

<p><strong>IBAN :</strong> PK12BOPP000123456789012</p>

<p><strong>Total Fee :</strong>

<span class="text-primary fw-bold">

Rs. <?php echo number_format($total,2); ?>

</span>

</p>

</div>

</div>

</div>

<div class="row mt-4">

<div class="col-md-12">

<form
action="upload_payment.php"
method="POST"
enctype="multipart/form-data">

<input
type="hidden"
name="fee_structure_id"
value="<?php echo $feeData['id']; ?>">

<div class="card border-primary">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-upload me-2"></i>

Upload Deposit Receipt

</h5>

</div>

<div class="card-body">

<div class="mb-3">

<label class="form-label fw-bold">

Select Deposit Receipt

</label>

<input
type="file"
name="receipt"
class="form-control"
accept=".jpg,.jpeg,.png,.pdf"
required>

<small class="text-muted">

Allowed Files :
JPG, JPEG, PNG, PDF

</small>

</div>

<div class="row">

<div class="col-md-6 d-grid">

<button
type="submit"
class="btn btn-upload">

<i class="fas fa-cloud-upload-alt me-2"></i>

Upload Receipt

</button>

</div>

<div class="col-md-6 d-grid">

<a
href="fee_status.php"
class="btn btn-outline-primary">

<i class="fas fa-money-check-dollar me-2"></i>

Back To Fee Status

</a>

</div>

</div>

</div>

</div>

</form>

</div>

</div>

<div class="text-center mt-4 mb-4">

<a
href="dashboard.php"
class="btn btn-warning">

<i class="fas fa-house me-2"></i>

Dashboard

</a>

</div>

</div>

</body>

</html>