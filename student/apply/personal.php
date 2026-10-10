<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

if(!isset($_SESSION['student_id'])){

    header("Location: ../login.php");
    exit;

}

$student_id=$_SESSION['student_id'];

$message="";
$type="";

$app_id=0;

/*-----------------------------
CHECK APPLICATION
------------------------------*/

$check=$conn->prepare("SELECT id FROM applications WHERE student_id=? LIMIT 1");

$check->bind_param("i",$student_id);

$check->execute();

$result=$check->get_result();

if($result->num_rows==1){

$app=$result->fetch_assoc();

$app_id=$app['id'];

}

/*-----------------------------
SAVE FORM
------------------------------*/

if(isset($_POST['save_continue'])){

$gender=$_POST['gender'];

$dob=$_POST['dob'];

$religion=$_POST['religion'];

$nationality=$_POST['nationality'];

$address=$_POST['address'];

$city=$_POST['city'];

$province=$_POST['province'];

$postal_code=$_POST['postal_code'];

if($app_id==0){

$application_no="GTU-".date("Y")."-".str_pad(rand(1,999999),6,"0",STR_PAD_LEFT);

$stmt = $conn->prepare("
INSERT INTO applications(
application_id,
student_id
)
VALUES(?,?)
");

$stmt->bind_param(
"si",
$application_no,
$student_id
);

$stmt->execute();

$app_id=$conn->insert_id;

}

$check2=$conn->prepare("SELECT id FROM application_personal WHERE application_id=?");

$check2->bind_param("i",$app_id);

$check2->execute();

$r2=$check2->get_result();

if($r2->num_rows>0){

$update=$conn->prepare("UPDATE application_personal
SET
gender=?,
date_of_birth=?,
religion=?,
nationality=?,
address=?,
city=?,
province=?,
postal_code=?
WHERE application_id=?");

$update->bind_param(

"ssssssssi",

$gender,
$dob,
$religion,
$nationality,
$address,
$city,
$province,
$postal_code,
$app_id

);

$update->execute();

}else{

$insert=$conn->prepare("INSERT INTO application_personal
(application_id,gender,date_of_birth,religion,nationality,address,city,province,postal_code)

VALUES(?,?,?,?,?,?,?,?,?)");

$insert->bind_param(

"issssssss",

$app_id,
$gender,
$dob,
$religion,
$nationality,
$address,
$city,
$province,
$postal_code

);

$insert->execute();

}
header("Location: academic.php");
exit;


}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>GTU Admission | Personal Information</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f4f7fb;

}

.topbar{

background:#0B1F52;
color:white;
padding:18px;

}

.progress{

height:10px;

}

.card{

border:none;
border-radius:15px;
box-shadow:0 10px 30px rgba(0,0,0,.08);

}

.card-header{

background:#0B1F52;
color:white;
font-size:22px;
font-weight:bold;

}

.form-control,
.form-select{

height:52px;
border-radius:10px;

}

textarea.form-control{

height:120px;

}

.btn-next{

background:#FDB913;
border:none;
height:55px;
font-size:18px;
font-weight:bold;
border-radius:50px;

}

.btn-next:hover{

background:#e3a700;

}

.step{

font-size:18px;
font-weight:bold;
color:#0B1F52;

}

</style>

</head>

<body>

<div class="topbar">

<div class="container">

<h3>

Global Tech University (GTU)

</h3>

</div>

</div>

<div class="container mt-4">

<div class="row">

<div class="col-lg-12">

<div class="step">

Step 1 of 5

</div>

<div class="progress mt-2 mb-4">

<div class="progress-bar bg-success"

style="width:20%">

</div>

</div>

<div class="card">

<div class="card-header">

<i class="fas fa-user"></i>

Personal Information

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>Gender</label>

<select

name="gender"

class="form-select"

required>

<option value="">Select Gender</option>

<option>Male</option>

<option>Female</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Date of Birth</label>

<input

type="date"

name="dob"

class="form-control"

required>

</div>

<div class="col-md-6 mb-3">

<label>Religion</label>

<input

type="text"

name="religion"

class="form-control"

required>

</div>

<div class="col-md-6 mb-3">

<label>Nationality</label>

<input

type="text"

name="nationality"

value="Pakistani"

class="form-control"

required>

</div>

<div class="col-md-6 mb-3">

<label>City</label>

<input

type="text"

name="city"

class="form-control"

required>

</div>

<div class="col-md-6 mb-3">

<label>Province</label>

<select

name="province"

class="form-select"

required>

<option>Punjab</option>

<option>Sindh</option>

<option>KPK</option>

<option>Balochistan</option>

<option>AJK</option>

<option>Gilgit Baltistan</option>

<option>Islamabad</option>

</select>

</div>

<div class="col-lg-12 mb-3">

<label>Postal Code</label>

<input

type="text"

name="postal_code"

class="form-control"

required>

</div>

<div class="col-lg-12 mb-4">

<label>Complete Address</label>

<textarea

name="address"

class="form-control"

required>

</textarea>

</div>

</div>

<div class="text-end">

<button

type="submit"

name="save_continue"

class="btn btn-next px-5">

Save & Continue

<i class="fas fa-arrow-right ms-2"></i>

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>