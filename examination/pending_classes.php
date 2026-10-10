<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


/*=========================================
EXAMINATION LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location:login.php");
    exit;

}


$admin_id=$_SESSION['admin_id'];



/*=========================================
GET PENDING CLASSES
=========================================*/


$query=$conn->prepare("

SELECT DISTINCT


mark_sessions.id AS session_id,


programs.program_name,


subjects.subject_name,

subjects.subject_code,


teachers.teacher_name,


mark_sessions.program_id,

mark_sessions.semester,

mark_sessions.shift



FROM mark_sessions



INNER JOIN marks_details

ON marks_details.mark_session_id = mark_sessions.id



INNER JOIN programs

ON programs.id = mark_sessions.program_id



INNER JOIN subjects

ON subjects.id = mark_sessions.subject_id



INNER JOIN teachers

ON teachers.id = mark_sessions.teacher_id



WHERE

marks_details.remarks='Pending Verification'



ORDER BY

programs.program_name ASC,

mark_sessions.semester ASC



");



$query->execute();


$classes=$query->get_result();



$page_title="Pending Marks Classes";


?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>

<?php echo $page_title; ?>

</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{

background:#eef3f9;

font-family:'Segoe UI',sans-serif;

}



/* MAIN CARD */

.main-card{

width:95%;

margin:40px auto;

background:white;

border-radius:20px;

overflow:hidden;

box-shadow:0 10px 30px rgba(0,0,0,.15);

}



/* HEADER */

.header{

background:linear-gradient(135deg,#000000,#0d6efd);

color:white;

padding:30px;

}



.header h2{

font-weight:bold;

}



/* INFO CARD */

.info-card{

background:#f8f9fa;

border-left:5px solid #0d6efd;

padding:18px;

border-radius:12px;

}



/* TABLE */

.table thead{

background:#000;

color:white;

}



.table tbody tr:hover{

background:#e8f2ff;

}



/* VIEW BUTTON */

.btn-view{

background:#0d6efd;

color:white;

font-weight:bold;

}



.btn-view:hover{

background:#084298;

color:white;

}



</style>


</head>


<body>



<div class="main-card">



<div class="header">


<h2>

<i class="fas fa-layer-group"></i>

Pending Marks Verification Classes

</h2>


<p class="mb-0">

Examination Office Class Wise Verification Panel

</p>


</div>



<div class="p-4">

<div class="row mb-4">


<div class="col-md-4">

<div class="info-card text-center">


<h3 class="text-primary">

<i class="fas fa-clock"></i>

<?php echo $classes->num_rows; ?>

</h3>


<p class="mb-0">

Pending Classes

</p>


</div>

</div>



<div class="col-md-4">

<div class="info-card text-center">


<h3 class="text-dark">

<i class="fas fa-file-signature"></i>

Marks

</h3>


<p class="mb-0">

Waiting For Verification

</p>


</div>

</div>




<div class="col-md-4">

<div class="info-card text-center">


<h3 class="text-primary">

<i class="fas fa-building-columns"></i>

Examination

</h3>


<p class="mb-0">

Result Control Panel

</p>


</div>

</div>


</div>





<div class="table-responsive">


<table class="table table-bordered table-hover align-middle">



<thead>


<tr>


<th width="60">

#

</th>


<th>

Program

</th>


<th>

Semester

</th>


<th>

Shift

</th>


<th>

Subject

</th>


<th>

Teacher

</th>


<th width="150">

Action

</th>


</tr>


</thead>



<tbody>


<?php


$sr=1;


if($classes->num_rows > 0){


while($row=$classes->fetch_assoc()){


?>


<tr>


<td>

<?php echo $sr++; ?>

</td>



<td>


<strong>

<?php echo htmlspecialchars($row['program_name']); ?>

</strong>


</td>



<td>

Semester

<?php echo $row['semester']; ?>

</td>



<td>

<?php echo htmlspecialchars($row['shift']); ?>

</td>



<td>


<?php echo htmlspecialchars($row['subject_name']); ?>


<br>


<small class="text-muted">

<?php echo htmlspecialchars($row['subject_code']); ?>

</small>


</td>



<td>

<?php echo htmlspecialchars($row['teacher_name']); ?>

</td>



<td>


<a

href="class_marks.php?session_id=<?php echo $row['session_id']; ?>"

class="btn btn-view btn-sm w-100">


<i class="fas fa-users"></i>

View Class


</a>


</td>


</tr>


<?php


}


}else{


?>


<tr>


<td colspan="7" class="text-center text-danger">


<h5>

No Pending Verification Classes Found

</h5>


<p>

All teacher marks are verified.

</p>


</td>


</tr>


<?php


}


?>

</tbody>


</table>


</div>




<div class="text-end mt-4">


<a

href="dashboard.php"

class="btn btn-dark btn-lg">


<i class="fas fa-arrow-left"></i>

Back To Dashboard


</a>


</div>



</div>


</div>


</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>


</html>
