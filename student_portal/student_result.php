<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location:login.php");
    exit;

}

$student_id=$_SESSION['student_id'];

/*=========================================
LOAD STUDENT INFORMATION
=========================================*/

$studentQuery=$conn->prepare("

SELECT

students.*,

programs.program_name,

programs.program_code,

faculties.faculty_name

FROM students

LEFT JOIN programs
ON students.program_id=programs.id

LEFT JOIN faculties
ON programs.faculty_id=faculties.id

WHERE students.id=?

LIMIT 1

");

$studentQuery->bind_param("i",$student_id);

$studentQuery->execute();

$result=$studentQuery->get_result();

if($result->num_rows==0){

    die("Student Not Found");

}

$student=$result->fetch_assoc();

$page_title="Student Result";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

<?php echo $page_title; ?>

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#d9d9d9;

font-family:'Segoe UI',sans-serif;

}

/*=========================================
MAIN RESULT CARD
=========================================*/

.result-card{

max-width:1200px;

margin:35px auto;

background:#ffffff;

border-radius:18px;

overflow:hidden;

box-shadow:0 10px 30px rgba(0,0,0,.18);

}

/*=========================================
HEADER
=========================================*/

.result-header{

background:linear-gradient(90deg,#111,#444,#f39c12);

padding:25px;

display:flex;

justify-content:space-between;

align-items:center;

color:#fff;

}

.result-header h2{

margin:0;

font-weight:bold;

}

.result-header p{

margin:5px 0 0;

font-size:15px;

color:#f1f1f1;

}

/*=========================================
TOP BUTTON
=========================================*/

.top-btn{

background:#f39c12;

color:#111;

padding:12px 24px;

border-radius:8px;

text-decoration:none;

font-weight:bold;

transition:.3s;

}

.top-btn:hover{

background:#ffb62e;

color:#000;

}

/*=========================================
CONTENT
=========================================*/

.result-content{

padding:30px;

}

</style>

</head>

<body>

<div class="result-card">

<!--=========================================
HEADER
=========================================-->

<div class="result-header">

<div>

<h2>

<i class="fas fa-graduation-cap"></i>

Student Result

</h2>

<p>

Result & Academic Performance

</p>

</div>

<a

href="top_students.php"

class="top-btn">

<i class="fas fa-trophy"></i>

Top 10 Students

</a>

</div>

<div class="result-content">

<!--=========================================
CONGRATULATIONS POPUP
=========================================-->

<div

class="modal fade"

id="congratulationsModal"

tabindex="-1"

data-bs-backdrop="static"

data-bs-keyboard="false">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content border-0 shadow-lg">

<div

class="modal-header"

style="background:#f39c12;color:#111;">

<h4 class="modal-title">

🎉 Congratulations

</h4>

</div>

<div class="modal-body text-center">

<h3 class="text-success">

<?php

echo htmlspecialchars(

$student['first_name']." ".$student['last_name']

);

?>

</h3>

<br>

<p style="font-size:18px;">

Your Semester Result

has been published successfully.

</p>

<p>

Keep working hard.

Success comes to those

who never stop learning.

</p>

<button

class="btn btn-dark mt-3"

data-bs-dismiss="modal">

View Result

</button>

</div>

</div>

</div>

</div>

<!--=========================================
STUDENT INFORMATION
=========================================-->

<div class="card border-0 shadow-sm mb-4">

<div

class="card-header"

style="background:#444;color:white;">

<h4 class="mb-0">

Student Information

</h4>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<label class="fw-bold">

Student Name

</label>

<input

type="text"

class="form-control"

readonly

value="<?php echo htmlspecialchars($student['first_name'].' '.$student['last_name']); ?>">

</div>

<div class="col-md-3 mb-3">

<label class="fw-bold">

Roll No

</label>

<input

type="text"

class="form-control"

readonly

value="<?php echo htmlspecialchars($student['roll_no']); ?>">

</div>

<div class="col-md-3 mb-3">

<label class="fw-bold">

Semester

</label>

<input

type="text"

class="form-control"

readonly

value="<?php echo htmlspecialchars($student['semester']); ?>">

</div>

<div class="col-md-6">

<label class="fw-bold">

Program

</label>

<input

type="text"

class="form-control"

readonly

value="<?php echo htmlspecialchars($student['program_name']); ?>">

</div>

<div class="col-md-6">

<label class="fw-bold">

Faculty

</label>

<input

type="text"

class="form-control"

readonly

value="<?php echo htmlspecialchars($student['faculty_name']); ?>">

</div>

</div>

</div>

</div>

<!--=========================================
RESULT TABLE
=========================================-->

<div class="card border-0 shadow-sm">

<div
class="card-header"
style="background:#f39c12;color:#111;">

<h4 class="mb-0">

<i class="fas fa-file-alt"></i>

Semester Result

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table
class="table table-bordered table-hover text-center align-middle">

<thead class="table-dark">

<tr>

<th width="60">

#

</th>

<th>

Subject

</th>

<th width="120">

Credit Hours

</th>

<th width="120">

Total Marks

</th>

<th width="120">

Obtained

</th>

<th width="100">

Grade

</th>

<th width="100">

Status

</th>

</tr>

</thead>

<tbody>

<?php

$resultQuery=$conn->prepare("

SELECT

subjects.subject_name,

subjects.credit_hours,

marks_details.obtained_marks,

marks_details.quiz_marks,

marks_details.assignment_marks,

marks_details.presentation_marks,

marks_details.practical_marks,

marks_details.mid_marks,

marks_details.final_marks

FROM marks_details

INNER JOIN mark_sessions

ON mark_sessions.id=marks_details.mark_session_id

INNER JOIN subjects

ON subjects.id=mark_sessions.subject_id

WHERE

marks_details.student_id=?

AND

mark_sessions.result_status='Published'

ORDER BY

subjects.subject_name ASC

");

$resultQuery->bind_param(

"i",

$student_id

);

$resultQuery->execute();

$resultList=$resultQuery->get_result();

$hasResult = ($resultList->num_rows > 0);

$sr=1;

if($resultList->num_rows==0){

?>

<tr>

<td colspan="7" class="text-center">

<div class="alert alert-warning m-3">

<h4>

Result has not been published yet.

</h4>

</div>

</td>

</tr>

<?php

}else{

while($row=$resultList->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php echo htmlspecialchars($row['subject_name']); ?>

</td>

<td>

<?php echo $row['credit_hours']; ?>

</td>

<td>

<?php echo $row['total_marks']; ?>

</td>

<td>

<?php echo $row['obtained_marks']; ?>

</td>

<td>

<?php

$marks=$row['obtained_marks'];

if($marks>=90){

    echo "A+";

}
elseif($marks>=80){

    echo "A";

}
elseif($marks>=70){

    echo "B+";

}
elseif($marks>=60){

    echo "B";

}
elseif($marks>=50){

    echo "C";

}
elseif($marks>=40){

    echo "D";

}
else{

    echo "F";

}

?>

</td>

<td>

<?php

if($row['obtained_marks']>=40){

?>

<span class="badge bg-success">

Pass

</span>

<?php

}else{

?>

<span class="badge bg-danger">

Fail

</span>

<?php

}

?>

</td>

</tr>

<?php

}

?>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

<!--=========================================
MOTIVATIONAL QUOTE
=========================================-->

<div class="card mt-5 border-0 shadow">

<div class="card-body text-center">

<h4 style="color:#f39c12;">

<i class="fas fa-lightbulb"></i>

Motivational Quote

</h4>

<hr>

<p style="font-size:18px;color:#444;line-height:32px;">

"Success is not about being the best.

It is about being better than yesterday.

Every exam, every semester and every challenge

brings you one step closer to your dreams.

Keep learning.

Keep improving.

Your future is waiting for you."

</p>

</div>

</div>

<!--=========================================
BACK BUTTON
=========================================-->

<div class="text-center mt-5">

<a

href="dashboard.php"

class="btn btn-dark btn-lg">

<i class="fas fa-arrow-left"></i>

Back To Dashboard

</a>

<a

href="top_students.php"

class="btn btn-warning btn-lg ms-2">

<i class="fas fa-trophy"></i>

Top 10 Students

</a>

</div>

</div>

<!--=========================================
POPUP SCRIPT
=========================================-->

<?php
if($resultList->num_rows > 0){
?>

<script>
window.onload=function(){

let resultModal=new bootstrap.Modal(
document.getElementById('congratulationsModal')
);

resultModal.show();

}
</script>

<?php
}
?>

<!--=========================================
BOOTSTRAP
=========================================-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>