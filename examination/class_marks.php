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



/*=========================================
CHECK SESSION ID
=========================================*/

if(!isset($_GET['session_id'])){

    die("Invalid Request");

}


$session_id=(int)$_GET['session_id'];



/*=========================================
GET MARK SESSION INFORMATION
=========================================*/


$session=$conn->prepare("

SELECT


mark_sessions.*,


subjects.subject_name,

subjects.subject_code,


programs.program_name,


faculties.faculty_name,


teachers.teacher_name



FROM mark_sessions



INNER JOIN subjects

ON subjects.id = mark_sessions.subject_id



INNER JOIN programs

ON programs.id = mark_sessions.program_id



INNER JOIN faculties

ON faculties.id = programs.faculty_id



INNER JOIN teachers

ON teachers.id = mark_sessions.teacher_id



WHERE mark_sessions.id=?


LIMIT 1


");



$session->bind_param(

"i",

$session_id

);



$session->execute();



$sessionData=$session->get_result()->fetch_assoc();



if(!$sessionData){

    die("Class Session Not Found");

}



$page_title="Class Marks Verification";

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



.main-card{

width:96%;

margin:35px auto;

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



/* INFO BOX */

.info-box{

background:#f8f9fa;

border-left:5px solid #0d6efd;

padding:15px;

border-radius:12px;

}



/* TABLE */

.table thead{

background:#000;

color:white;

}



.table tbody tr:hover{

background:#edf6ff;

}



/* BUTTONS */

.btn-verify{

background:#198754;

color:white;

font-weight:bold;

}



.btn-verify:hover{

background:#146c43;

color:white;

}



.btn-reject{

background:#dc3545;

color:white;

font-weight:bold;

}



.btn-reject:hover{

background:#b02a37;

color:white;

}



.btn-publish{

background:#0d6efd;

color:white;

font-weight:bold;

padding:12px 30px;

}



.btn-publish:hover{

background:#084298;

color:white;

}



</style>



</head>



<body>



<div class="main-card">



<div class="header">


<h2>

<i class="fas fa-clipboard-check"></i>

Class Marks Verification

</h2>


<p class="mb-0">

Examination Office Verification Panel

</p>


</div>



<div class="p-4">

<div class="row mb-4">


<div class="col-md-3 mb-3">


<div class="info-box">


<b>

Faculty

</b>


<br>


<?php echo htmlspecialchars($sessionData['faculty_name']); ?>


</div>


</div>





<div class="col-md-3 mb-3">


<div class="info-box">


<b>

Program

</b>


<br>


<?php echo htmlspecialchars($sessionData['program_name']); ?>


</div>


</div>





<div class="col-md-2 mb-3">


<div class="info-box">


<b>

Semester

</b>


<br>


<?php echo $sessionData['semester']; ?>


</div>


</div>





<div class="col-md-2 mb-3">


<div class="info-box">


<b>

Shift

</b>


<br>


<?php echo htmlspecialchars($sessionData['shift']); ?>


</div>


</div>





<div class="col-md-2 mb-3">


<div class="info-box">


<b>

Subject

</b>


<br>


<?php echo htmlspecialchars($sessionData['subject_name']); ?>


</div>


</div>



</div>





<div class="row mb-4">


<div class="col-md-4">


<div class="info-box">


<b>

Teacher

</b>


<br>


<?php echo htmlspecialchars($sessionData['teacher_name']); ?>


</div>


</div>



<div class="col-md-4">


<div class="info-box">


<b>

Subject Code

</b>


<br>


<?php echo htmlspecialchars($sessionData['subject_code']); ?>


</div>


</div>



<div class="col-md-4">


<div class="info-box">


<b>

Verification Status

</b>


<br>


<span class="badge bg-warning text-dark">

Pending

</span>


</div>


</div>



</div>




<hr>


<h4 class="mb-3">


<i class="fas fa-users"></i>


Students Marks


</h4>

<div class="table-responsive">


<table class="table table-bordered table-hover align-middle">



<thead>


<tr>


<th width="60">

#

</th>


<th>

Roll No

</th>


<th>

Student Name

</th>


<th>

Quiz

</th>


<th>

Assignment

</th>


<th>

Presentation

</th>


<th>

Practical

</th>


<th>

Mid

</th>


<th>

Final

</th>


<th>

Total

</th>


<th width="180">

Action

</th>


</tr>


</thead>



<tbody>



<?php


$sr=1;



/*=========================================
GET STUDENT MARKS
=========================================*/


$marks=$conn->prepare("


SELECT


students.roll_no,

students.registration_no,

students.first_name,

students.last_name,


marks_details.*



FROM marks_details



INNER JOIN students

ON students.id = marks_details.student_id



WHERE marks_details.mark_session_id=?


ORDER BY students.roll_no ASC



");



$marks->bind_param(

"i",

$session_id

);



$marks->execute();



$markList=$marks->get_result();





if($markList->num_rows>0){



while($row=$markList->fetch_assoc()){


?>


<tr>


<td>

<?php echo $sr++; ?>

</td>



<td>


<?php echo htmlspecialchars($row['roll_no']); ?>


</td>



<td>


<strong>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</strong>


<br>


<small class="text-muted">

<?php echo htmlspecialchars($row['registration_no']); ?>

</small>


</td>



<td>

<?php echo $row['quiz_marks']; ?>

</td>



<td>

<?php echo $row['assignment_marks']; ?>

</td>



<td>

<?php echo $row['presentation_marks']; ?>

</td>



<td>


<?php


if($row['practical_marks']>0){

echo $row['practical_marks'];

}else{

echo "-";

}


?>


</td>



<td>

<?php echo $row['mid_marks']; ?>

</td>



<td>

<?php echo $row['final_marks']; ?>

</td>



<td>


<span class="badge bg-primary">


<?php echo $row['obtained_marks']; ?>


Marks


</span>


</td>



<td>

<div class="d-grid gap-2">



<a

href="verify_marks.php?id=<?php echo $row['id']; ?>&session_id=<?php echo $session_id; ?>"

class="btn btn-success btn-sm">


<i class="fas fa-check"></i>

Verify


</a>





<a

href="reject_marks.php?id=<?php echo $row['id']; ?>&session_id=<?php echo $session_id; ?>"

class="btn btn-danger btn-sm">


<i class="fas fa-times"></i>

Reject


</a>



</div>


</td>


</tr>


<?php


}


}else{


?>


<tr>


<td colspan="11" class="text-center text-danger">


<h5>

No Marks Found

</h5>


<p>

Teacher has not uploaded marks yet.

</p>


</td>


</tr>


<?php


}


?>



</tbody>


</table>


</div>

<!--=========================================
PUBLISH RESULT SECTION
=========================================-->


<div class="row mt-5">


<div class="col-md-6">


<a

href="pending_classes.php"

class="btn btn-dark btn-lg w-100">


<i class="fas fa-arrow-left"></i>

Back To Classes


</a>


</div>



<div class="col-md-6">


<a

href="publish_result.php?session_id=<?php echo $session_id; ?>"

class="btn btn-publish btn-lg w-100">


<i class="fas fa-upload"></i>

Publish Result


</a>


</div>



</div>





</div>


</div>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>