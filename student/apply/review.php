<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

/*=====================================
GET COMPLETE APPLICATION
=====================================*/

$sql = $conn->prepare("
SELECT

a.*,

s.first_name,
s.last_name,
s.email,

ap.gender,
ap.date_of_birth,
ap.religion,
ap.nationality,
ap.address,
ap.city,
ap.province,
ap.postal_code,

aa.matric_board,
aa.matric_roll_no,
aa.matric_total_marks,
aa.matric_obtained_marks,

aa.inter_board,
aa.inter_roll_no,
aa.inter_total_marks,
aa.inter_obtained_marks,

p.program_name,

f.faculty_name,

d.student_photo,
d.student_cnic,
d.father_cnic,
d.matric_result_card,
d.inter_result_card

FROM applications a

INNER JOIN students s
ON a.student_id=s.id

LEFT JOIN application_personal ap
ON ap.application_id=a.id

LEFT JOIN application_academic aa
ON aa.application_id=a.id

LEFT JOIN application_documents d
ON d.application_id=a.id

LEFT JOIN programs p
ON a.program_id=p.id

LEFT JOIN faculties f
ON a.faculty_id=f.id

WHERE a.student_id=?

LIMIT 1

");

$sql->bind_param("i",$student_id);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

header("Location: personal.php");

exit;

}

$data=$result->fetch_assoc();

/*=====================================
SUBMIT APPLICATION
=====================================*/

if(isset($_POST['submit_application'])){

$status="Submitted";

$update=$conn->prepare("

UPDATE applications

SET application_status=?

WHERE id=?

");

$update->bind_param(

"si",

$status,

$data['id']

);

$update->execute();

/*==========================
CREATE CHALLAN
==========================*/

$challan_no="CH-".date("Y")."-".str_pad($data['id'],6,"0",STR_PAD_LEFT);

$fee=1050;

$check=$conn->prepare("

SELECT id

FROM application_payments

WHERE application_id=?

");

$check->bind_param("i",$data['id']);

$check->execute();

$r=$check->get_result();

if($r->num_rows==0){

$insert=$conn->prepare("

INSERT INTO application_payments(

application_id,

challan_no,

processing_fee

)

VALUES(?,?,?)

");

$insert->bind_param(

"isd",

$data['id'],

$challan_no,

$fee

);

$insert->execute();

}

header("Location: fee_challan.php");

exit;

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Review Application | GTU</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f5f7fb;

font-family:Arial,Helvetica,sans-serif;

}

.topbar{

background:#0B1F52;

color:white;

padding:18px 0;

}

.topbar h3{

margin:0;

font-weight:bold;

}

.progress{

height:10px;

border-radius:20px;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 20px rgba(0,0,0,.08);

margin-bottom:25px;

}

.card-header{

background:#0B1F52;

color:white;

font-size:20px;

font-weight:bold;

}

.info-row{

padding:10px 0;

border-bottom:1px solid #eee;

}

.info-title{

font-weight:bold;

color:#0B1F52;

}

.submit-btn{

background:#FDB913;

color:#000;

font-weight:bold;

border:none;

padding:12px 40px;

border-radius:40px;

}

.submit-btn:hover{

background:#e4a700;

}

</style>

</head>

<body>

<div class="topbar">

<div class="container">

<h3>Global Tech University (GTU)</h3>

</div>

</div>

<div class="container mt-4">

<div class="progress mb-4">

<div class="progress-bar bg-success" style="width:100%"></div>

</div>

<div class="alert alert-success">

<h5 class="mb-1">

<i class="fa-solid fa-circle-check"></i>

Review Your Admission Application

</h5>

Please verify all information before submitting your application.

</div>

<div class="card">

<div class="card-header">

<i class="fa-solid fa-user"></i>

Personal Information

</div>

<div class="card-body">

<div class="row info-row">

<div class="col-md-4 info-title">

Full Name

</div>

<div class="col-md-8">

<?php echo $data['first_name']." ".$data['last_name']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Email Address

</div>

<div class="col-md-8">

<?php echo $data['email']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Gender

</div>

<div class="col-md-8">

<?php echo $data['gender']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Date of Birth

</div>

<div class="col-md-8">

<?php echo $data['date_of_birth']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Religion

</div>

<div class="col-md-8">

<?php echo $data['religion']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Nationality

</div>

<div class="col-md-8">

<?php echo $data['nationality']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

City

</div>

<div class="col-md-8">

<?php echo $data['city']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Province

</div>

<div class="col-md-8">

<?php echo $data['province']; ?>

</div>

</div>

<div class="row">

<div class="col-md-4 info-title">

Address

</div>

<div class="col-md-8">

<?php echo $data['address']; ?>

</div>

</div>

</div>

</div>
<!-- Academic Information -->

<div class="card">

<div class="card-header">

<i class="fa-solid fa-graduation-cap"></i>

Academic Information

</div>

<div class="card-body">

<div class="row info-row">

<div class="col-md-4 info-title">

Matric Board

</div>

<div class="col-md-8">

<?php echo $data['matric_board']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Matric Roll No

</div>

<div class="col-md-8">

<?php echo $data['matric_roll_no']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Matric Marks

</div>

<div class="col-md-8">

<?php echo $data['matric_obtained_marks']; ?>

 /

<?php echo $data['matric_total_marks']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Intermediate Board

</div>

<div class="col-md-8">

<?php echo $data['inter_board']; ?>

</div>

</div>

<div class="row info-row">

<div class="col-md-4 info-title">

Intermediate Roll No

</div>

<div class="col-md-8">

<?php echo $data['inter_roll_no']; ?>

</div>

</div>

<div class="row">

<div class="col-md-4 info-title">

Intermediate Marks

</div>

<div class="col-md-8">

<?php echo $data['inter_obtained_marks']; ?>

 /

<?php echo $data['inter_total_marks']; ?>

</div>

</div>

</div>

</div>

<!-- Program Information -->

<div class="card">

<div class="card-header">

<i class="fa-solid fa-building-columns"></i>

Selected Program

</div>

<div class="card-body">

<div class="row info-row">

<div class="col-md-4 info-title">

Faculty

</div>

<div class="col-md-8">

<?php echo $data['faculty_name']; ?>

</div>

</div>

<div class="row">

<div class="col-md-4 info-title">

Program

</div>

<div class="col-md-8">

<?php echo $data['program_name']; ?>

</div>

</div>

</div>

</div>
<!-- Uploaded Documents -->

<div class="card">

<div class="card-header">

<i class="fa-solid fa-folder-open"></i>

Uploaded Documents

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3 text-center mb-4">

<h6>Student Photo</h6>

<?php
if(!empty($data['student_photo'])){
?>

<img
src="../../uploads/student_photos/<?php echo $data['student_photo']; ?>"
class="img-fluid rounded border"
style="height:180px;width:160px;object-fit:cover;">

<?php
}else{
?>

<div class="alert alert-warning">

Photo Not Uploaded

</div>

<?php
}
?>

</div>

<div class="col-md-9">

<table class="table table-bordered table-hover">

<tr>

<th width="40%">

Student CNIC / B-Form

</th>

<td>

<?php
if(!empty($data['student_cnic'])){
?>

<a
href="../../uploads/student_cnic/<?php echo $data['student_cnic']; ?>"
target="_blank"
class="btn btn-primary btn-sm">

<i class="fa-solid fa-eye"></i>

View Document

</a>

<?php
}else{
echo "<span class='text-danger'>Not Uploaded</span>";
}
?>

</td>

</tr>

<tr>

<th>

Father CNIC

</th>

<td>

<?php
if(!empty($data['father_cnic'])){
?>

<a
href="../../uploads/father_cnic/<?php echo $data['father_cnic']; ?>"
target="_blank"
class="btn btn-primary btn-sm">

<i class="fa-solid fa-eye"></i>

View Document

</a>

<?php
}else{
echo "<span class='text-danger'>Not Uploaded</span>";
}
?>

</td>

</tr>

<tr>

<th>

Matric Result Card

</th>

<td>

<?php
if(!empty($data['matric_result_card'])){
?>

<a
href="../../uploads/matric_result/<?php echo $data['matric_result_card']; ?>"
target="_blank"
class="btn btn-success btn-sm">

<i class="fa-solid fa-file"></i>

Open Result Card

</a>

<?php
}else{
echo "<span class='text-danger'>Not Uploaded</span>";
}
?>

</td>

</tr>

<tr>

<th>

Intermediate Result Card

</th>

<td>

<?php
if(!empty($data['inter_result_card'])){
?>

<a
href="../../uploads/inter_result/<?php echo $data['inter_result_card']; ?>"
target="_blank"
class="btn btn-success btn-sm">

<i class="fa-solid fa-file"></i>

Open Result Card

</a>

<?php
}else{
echo "<span class='text-danger'>Not Uploaded</span>";
}
?>

</td>

</tr>

</table>

</div>

</div>

</div>

</div>
<!-- Declaration -->

<div class="card">

<div class="card-header">

<i class="fa-solid fa-circle-check"></i>

Declaration

</div>

<div class="card-body">

<form method="POST">

<div class="form-check mb-4">

<input
class="form-check-input"
type="checkbox"
id="declaration"
required>

<label
class="form-check-label"
for="declaration">

I hereby declare that all information provided in this application is true and correct. I understand that Global Tech University (GTU) has the right to cancel my admission if any information or document is found to be incorrect or fake.

</label>

</div>

<div class="d-flex justify-content-between">

<a href="documents.php" class="btn btn-secondary btn-lg">

<i class="fa-solid fa-arrow-left"></i>

Previous

</a>

<button
type="submit"
name="submit_application"
class="submit-btn">

<i class="fa-solid fa-paper-plane"></i>

Submit Application

</button>

</div>

</form>

</div>

</div>

<div class="text-center mt-4 mb-4">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University (GTU)

Admission Management System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>