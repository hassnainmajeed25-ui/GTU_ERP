<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit;
}

$student_id=$_SESSION['student_id'];

$app=$conn->prepare("SELECT id FROM applications WHERE student_id=? LIMIT 1");
$app->bind_param("i",$student_id);
$app->execute();

$result=$app->get_result();

if($result->num_rows==0){
    header("Location: personal.php");
    exit;
}

$row=$result->fetch_assoc();
$app_id=$row['id'];

if(isset($_POST['save_continue'])){

    $matric_board=$_POST['matric_board'];
    $matric_roll_no=$_POST['matric_roll_no'];
    $matric_total_marks=$_POST['matric_total_marks'];
    $matric_obtained_marks=$_POST['matric_obtained_marks'];

    $inter_board=$_POST['inter_board'];
    $inter_roll_no=$_POST['inter_roll_no'];
    $inter_total_marks=$_POST['inter_total_marks'];
    $inter_obtained_marks=$_POST['inter_obtained_marks'];

    $check=$conn->prepare("SELECT id FROM application_academic WHERE application_id=?");
    $check->bind_param("i",$app_id);
    $check->execute();

    $res=$check->get_result();

    if($res->num_rows>0){

        $update=$conn->prepare("UPDATE application_academic SET

        matric_board=?,
        matric_roll_no=?,
        matric_total_marks=?,
        matric_obtained_marks=?,

        inter_board=?,
        inter_roll_no=?,
        inter_total_marks=?,
        inter_obtained_marks=?

        WHERE application_id=?");

        $update->bind_param(

        "ssiissiii",

        $matric_board,
        $matric_roll_no,
        $matric_total_marks,
        $matric_obtained_marks,

        $inter_board,
        $inter_roll_no,
        $inter_total_marks,
        $inter_obtained_marks,

        $app_id

        );

        $update->execute();

    }else{

        $insert=$conn->prepare("INSERT INTO application_academic(

        application_id,

        matric_board,
        matric_roll_no,
        matric_total_marks,
        matric_obtained_marks,

        inter_board,
        inter_roll_no,
        inter_total_marks,
        inter_obtained_marks

        )

        VALUES(?,?,?,?,?,?,?,?,?)");

        $insert->bind_param(

        "issiissii",

        $app_id,

        $matric_board,
        $matric_roll_no,
        $matric_total_marks,
        $matric_obtained_marks,

        $inter_board,
        $inter_roll_no,
        $inter_total_marks,
        $inter_obtained_marks

        );

        $insert->execute();

    }

    header("Location: program.php");
    exit;

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>GTU Admission | Academic Information</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#f4f7fb;
}

.topbar{
background:#0B1F52;
color:#fff;
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
color:#fff;
font-size:22px;
font-weight:bold;
}

.form-control,
.form-select{
height:52px;
border-radius:10px;
}

.section-title{
background:#eef3ff;
padding:10px 15px;
border-left:5px solid #0B1F52;
margin:20px 0;
font-weight:bold;
font-size:18px;
}

.btn-next{
background:#FDB913;
border:none;
height:55px;
border-radius:50px;
font-size:18px;
font-weight:bold;
}

.btn-next:hover{
background:#e3a700;
}

.note{
background:#fff8e1;
border-left:5px solid #FDB913;
padding:15px;
border-radius:10px;
margin-bottom:20px;
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

<div class="progress-bar bg-success" style="width:40%;"></div>

</div>

<div class="card">

<div class="card-header">

<i class="fas fa-graduation-cap me-2"></i>

Academic Information

</div>

<div class="card-body">

<div class="note">

<strong>Important:</strong><br>

Admission ke waqt apne <strong>Intermediate Marks</strong> me sirf <strong>1st Year</strong> ke marks enter karein. Final result announce hone ke baad aap dashboard se apne marks aur result card update kar sakenge.

</div>

<form method="POST">

<div class="section-title">

Matric Information

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label>Matric Board</label>

<input
type="text"
name="matric_board"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Matric Roll Number</label>

<input
type="text"
name="matric_roll_no"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Matric Total Marks</label>

<input
type="number"
name="matric_total_marks"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Matric Obtained Marks</label>

<input
type="number"
name="matric_obtained_marks"
class="form-control"
required>

</div>

</div>

<div class="section-title">

Intermediate Information

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label>Intermediate Board</label>

<input
type="text"
name="inter_board"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Intermediate Roll Number</label>

<input
type="text"
name="inter_roll_no"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Intermediate Total Marks</label>

<input
type="number"
name="inter_total_marks"
class="form-control"
required>

</div>

<div class="col-md-6 mb-4">

<label>Intermediate Obtained Marks (1st Year)</label>

<input
type="number"
name="inter_obtained_marks"
class="form-control"
required>

</div>

</div>

<div class="d-flex justify-content-between">

<a href="personal.php" class="btn btn-secondary px-4">

<i class="fas fa-arrow-left me-2"></i>

Previous

</a>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>