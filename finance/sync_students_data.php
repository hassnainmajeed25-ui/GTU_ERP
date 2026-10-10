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
LOAD ALL SELECTED APPLICATIONS
=========================================*/

$sql="

SELECT

applications.student_id,
applications.registration_no,
applications.roll_no,
applications.program_id,
applications.shift,

programs.program_name

FROM applications

INNER JOIN programs

ON programs.id=applications.program_id

WHERE applications.application_status='Selected'

ORDER BY applications.id ASC

";

$result=$conn->query($sql);

if(!$result){

die("Database Error : ".$conn->error);

}

$totalUpdated=0;
$totalSkipped=0;

while($row=$result->fetch_assoc()){

    $semester=1;

    $update=$conn->prepare("

    UPDATE students

    SET

    registration_no=?,

    roll_no=?,

    program_id=?,

    semester=?,

    shift=?,

    program=?

        WHERE id=?

    ");

    $update->bind_param(

    "ssiissi",

    $row['registration_no'],

    $row['roll_no'],

    $row['program_id'],

    $semester,

    $row['shift'],

    $row['program_name'],

    $row['student_id']

    );

    if($update->execute()){

        if($update->affected_rows>0){

            $totalUpdated++;

        }else{

            $totalSkipped++;

        }

    }

    $update->close();

}

/*=========================================
SUCCESS REPORT
=========================================*/
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Student Data Synchronization</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef3f8;

font-family:'Segoe UI',sans-serif;

}

.card{

border:none;

border-radius:18px;

box-shadow:0 5px 20px rgba(0,0,0,.08);

margin-top:60px;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card">

<div class="card-header bg-success text-white text-center">

<h3>

<i class="fas fa-database me-2"></i>

Student Data Synchronization

</h3>

</div>

<div class="card-body text-center">

<h2 class="text-success mb-4">

<i class="fas fa-circle-check"></i>

Synchronization Completed

</h2>

<div class="row">

<div class="col-md-6">

<div class="alert alert-success">

<h3>

<?php echo $totalUpdated; ?>

</h3>

<p class="mb-0">

Students Updated

</p>

</div>

</div>

<div class="col-md-6">

<div class="alert alert-warning">

<h3>

<?php echo $totalSkipped; ?>

</h3>

<p class="mb-0">

Already Up-to-date

</p>

</div>

</div>

</div>

<hr>

<p class="text-muted">

Registration Number, Roll Number, Program, Semester and Shift have been synchronized successfully.

</p>

<a href="semester_fee_verification.php"

class="btn btn-primary btn-lg">

<i class="fas fa-arrow-left me-2"></i>

Back to Semester Fee Verification

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>