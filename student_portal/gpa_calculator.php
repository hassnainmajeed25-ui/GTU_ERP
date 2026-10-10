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
LOAD STUDENT
=========================================*/

$studentQuery=$conn->prepare("

SELECT

students.*,

programs.program_name,

programs.program_code

FROM students

LEFT JOIN programs

ON students.program_id=programs.id

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

$page_title="GPA Calculator";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
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
MAIN CARD
=========================================*/

.main-card{

max-width:1200px;

margin:35px auto;

background:#ffffff;

border-radius:18px;

box-shadow:0 10px 25px rgba(0,0,0,.18);

overflow:hidden;

}

/*=========================================
HEADER
=========================================*/

.header{

background:#f4d03f;

color:#111;

padding:25px;

display:flex;

justify-content:space-between;

align-items:center;

border-bottom:5px solid #333;

}

.header h2{

margin:0;

font-weight:bold;

}

.cgpa-btn{

background:#333;

color:#f4d03f;

padding:10px 22px;

border-radius:8px;

text-decoration:none;

font-weight:bold;

transition:.3s;

}

.cgpa-btn:hover{

background:#000;

color:#fff176;

}

.content{

padding:30px;

}

</style>

</head>

<body>

<div class="main-card">

<!--=========================================
HEADER
=========================================-->

<div class="header">

<div>

<h2>

<i class="fas fa-calculator"></i>

GPA Calculator

</h2>

<p class="mb-0">

Calculate Your Semester GPA

</p>

</div>

<a

href="cgpa_calculator.php"

class="cgpa-btn">

<i class="fas fa-chart-line"></i>

CGPA Calculator

</a>

</div>

<!--=========================================
CONTENT
=========================================-->

<div class="content">

<!--=========================================
STUDENT INFORMATION
=========================================-->

<div class="row mb-4">

<div class="col-md-4">

<label class="fw-bold">

Student Name

</label>

<input

type="text"

class="form-control"

value="<?php echo htmlspecialchars($student['first_name']." ".$student['last_name']); ?>"

readonly>

</div>

<div class="col-md-4">

<label class="fw-bold">

Roll Number

</label>

<input

type="text"

class="form-control"

value="<?php echo htmlspecialchars($student['roll_no']); ?>"

readonly>

</div>

<div class="col-md-4">

<label class="fw-bold">

Program

</label>

<input

type="text"

class="form-control"

value="<?php echo htmlspecialchars($student['program_name']); ?>"

readonly>

</div>

</div>

<!--=========================================
SUBJECT TABLE
=========================================-->

<div class="table-responsive">

<table

class="table table-bordered table-hover align-middle text-center"

id="gpaTable">

<thead class="table-dark">

<tr>

<th width="60">

#

</th>

<th>

Subject Name

</th>

<th width="150">

Credit Hours

</th>

<th width="150">

Marks

</th>

<th width="120">

Grade

</th>

<th width="120">

Grade Point

</th>

</tr>

</thead>

<tbody>

<tr>

<td>

1

</td>

<td>

<select

class="form-select subject-select"

onchange="loadSubject(this)">

<option value="">

Select Subject

</option>

<?php

$subjectQuery=$conn->prepare("

SELECT

id,
subject_name

FROM subjects

WHERE

program_id=?

AND semester=?

AND status='Active'

ORDER BY subject_name

");

$subjectQuery->bind_param(

"ii",

$student['program_id'],

$student['semester']

);

$subjectQuery->execute();

$subjectList=$subjectQuery->get_result();

while($subject=$subjectList->fetch_assoc()){

?>

<option

value="<?php echo $subject['id']; ?>">

<?php echo htmlspecialchars($subject['subject_name']); ?>

</option>

<?php

}

?>

</select>

</td>

<td>

<input

type="text"

class="form-control credit-hour"

readonly>
</td>

<td>

<input

type="number"

class="form-control marks"

min="0"

max="100">

</td>

<td>

<input

type="text"

class="form-control grade"

readonly>

</td>

<td>

<input

type="text"

class="form-control grade-point"

readonly>

</td>

</tr>

</tbody>

</table>

</div>

<!--=========================================
BUTTONS
=========================================-->

<div class="mt-4 d-flex justify-content-between">

<button

type="button"

class="btn btn-dark"

onclick="addSubjectRow()">

<i class="fas fa-plus-circle"></i>

Add Subject

</button>

<button

type="button"

class="btn btn-warning fw-bold"

onclick="calculateGPA()">

<i class="fas fa-calculator"></i>

Calculate GPA

</button>

</div>

<!--=========================================
RESULT CARD
=========================================-->

<div class="card mt-4 border-dark">

<div class="card-header bg-dark text-warning">

<h4 class="mb-0">

<i class="fas fa-chart-line"></i>

Semester GPA

</h4>

</div>

<div class="card-body text-center">

<h1

id="semesterGPA"

style="font-size:60px;font-weight:bold;color:#333;">

0.00

</h1>

<p class="mb-0">

Overall Semester GPA

</p>

</div>

</div>

<!--=========================================
FOOTER
=========================================-->

<hr class="mt-5">

<div class="row">

<div class="col-md-6">

<strong>

Student :

</strong>

<?php echo htmlspecialchars($student['first_name']." ".$student['last_name']); ?>

</div>

<div class="col-md-6 text-end">

<strong>

Program :

</strong>

<?php echo htmlspecialchars($student['program_name']); ?>

</div>

</div>

<br>

<div class="text-center">

<a

href="dashboard.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back To Dashboard

</a>

</div>

</div>

</div>

<script>

let sr=1;

/*=========================================
ADD SUBJECT
=========================================*/

function addSubjectRow(){

sr++;

let tbody=document.querySelector("#gpaTable tbody");

let row=`

<tr>

<td>${sr}</td>

<td>

<select

class="form-select subject-select"

onchange="loadSubject(this)">

<option value="">

Select Subject

</option>

<?php

$subjectQuery=$conn->prepare("

SELECT

id,
subject_name

FROM subjects

WHERE

program_id=?

AND semester=?

AND status='Active'

ORDER BY semester,subject_name

");

$subjectQuery->bind_param(

"ii",

$student['program_id'],

$student['semester']

);

$subjectQuery->execute();

$subjectList=$subjectQuery->get_result();

while($subject=$subjectList->fetch_assoc()){

?>

<option

value="<?php echo $subject['id']; ?>">

<?php echo htmlspecialchars($subject['subject_name']); ?>

</option>

<?php

}

?>

</select>

</td>

<td>

<input

type="text"

class="form-control credit-hour"

readonly

value="">

</td>

<td>

<input
type="number"
class="form-control marks"
min="0"
max="100">

</td>

<td>

<input
type="text"
class="form-control grade"
readonly>

</td>

<td>

<input
type="text"
class="form-control grade-point"
readonly>

</td>

</tr>

`;

tbody.insertAdjacentHTML("beforeend",row);

}

/*=========================================
LOAD SUBJECT
=========================================*/

function loadSubject(selectBox){

let subjectId=selectBox.value;

let row=selectBox.closest("tr");

let creditBox=row.querySelector(".credit-hour");

if(subjectId==""){

creditBox.value="";

return;

}

fetch("get_subject_details.php?id="+subjectId)

.then(response=>response.json())

.then(data=>{

creditBox.value=data.credit_hours;

});

}

/*=========================================
CALCULATE GPA
=========================================*/

function calculateGPA(){

let rows=document.querySelectorAll("#gpaTable tbody tr");

let totalCredits=0;

let totalPoints=0;

rows.forEach(function(row){

let credit=parseFloat(row.querySelector(".credit-hour").value)||0;

let marks=parseFloat(row.querySelector(".marks").value)||0;

let grade="";

let point=0;

if(marks>=85){

grade="A";
point=4.00;

}else if(marks>=80){

grade="A-";
point=3.67;

}else if(marks>=75){

grade="B+";
point=3.33;

}else if(marks>=71){

grade="B";
point=3.00;

}else if(marks>=68){

grade="B-";
point=2.67;

}else if(marks>=64){

grade="C+";
point=2.33;

}else if(marks>=61){

grade="C";
point=2.00;

}else if(marks>=58){

grade="C-";
point=1.67;

}else if(marks>=50){

grade="D";
point=1.00;

}else{

grade="F";
point=0.00;

}

row.querySelector(".grade").value=grade;

row.querySelector(".grade-point").value=point.toFixed(2);

totalCredits+=credit;

totalPoints+=(credit*point);

});

let gpa=0;

if(totalCredits>0){

gpa=totalPoints/totalCredits;

}

document.getElementById("semesterGPA").innerHTML=gpa.toFixed(2);

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>