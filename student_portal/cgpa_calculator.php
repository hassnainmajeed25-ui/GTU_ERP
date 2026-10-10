<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){

header("Location:login.php");
exit;

}

$student_id=$_SESSION['student_id'];

$studentQuery=$conn->prepare("

SELECT

students.*,

programs.program_name

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

$page_title="CGPA Calculator";

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

.main-card{

max-width:900px;

margin:35px auto;

background:#ffffff;

border-radius:18px;

overflow:hidden;

box-shadow:0 10px 25px rgba(0,0,0,.18);

}

.header{

background:#0d6efd;

color:#FFD700;

padding:25px;

display:flex;

justify-content:space-between;

align-items:center;

border-bottom:5px solid #111;

}

.header h2{

margin:0;

font-weight:bold;

}

.content{

padding:30px;

}

.table thead{

background:#111;

color:#FFD700;

}

.btn-yellow{

background:#FFD700;

color:#111;

font-weight:bold;

border:none;

}

.btn-yellow:hover{

background:#e6c200;

}

.result-box{

background:#0d6efd;

color:#FFD700;

padding:20px;

border-radius:12px;

text-align:center;

margin-top:25px;

}

</style>

</head>

<body>

<div class="main-card">

<div class="header">

<div>

<h2>

<i class="fas fa-chart-line"></i>

CGPA Calculator

</h2>

<p class="mb-0">

Semester Wise CGPA Calculation

</p>

</div>

<a

href="gpa_calculator.php"

class="btn btn-warning fw-bold">

<i class="fas fa-arrow-left"></i>

Back GPA

</a>

</div>

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
SEMESTER TABLE
=========================================-->

<div class="table-responsive">

<table

class="table table-bordered table-hover text-center align-middle"

id="cgpaTable">

<thead class="table-dark">

<tr>

<th width="70">

#

</th>

<th width="180">

Semester

</th>

<th width="180">

Semester GPA

</th>

<th width="180">

Credit Hours

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

class="form-select semester-select"

onchange="loadCreditHours(this)">

<option value="">

Select Semester

</option>

<option value="1">

Semester 1

</option>

<option value="2">

Semester 2

</option>

<option value="3">

Semester 3

</option>

<option value="4">

Semester 4

</option>

<option value="5">

Semester 5

</option>

<option value="6">

Semester 6

</option>

<option value="7">

Semester 7

</option>

<option value="8">

Semester 8

</option>

</select>

</td>

<td>

<input

type="number"

step="0.01"

min="0"

max="4"

class="form-control semester-gpa"

placeholder="Enter GPA">

</td>

<td>

<input

type="text"

class="form-control credit-hours"

readonly>

</td>

</tr>

<td>

<input

type="text"

class="form-control credit-hours"

readonly>

</td>

</tr>

</tbody>

</table>

</div>

<!--=========================================
BUTTONS
=========================================-->

<div class="d-flex justify-content-between mt-4">

<button

type="button"

class="btn btn-dark"

onclick="addSemester()">

<i class="fas fa-plus-circle"></i>

Add Semester

</button>

<button

type="button"

class="btn btn-yellow"

onclick="calculateCGPA()">

<i class="fas fa-calculator"></i>

Calculate CGPA

</button>

</div>

<!--=========================================
RESULT BOX
=========================================-->

<div class="result-box">

<h5>

Overall CGPA

</h5>

<h1

id="cgpaResult"

style="font-size:55px;font-weight:bold;">

0.00

</h1>

</div>

<!--=========================================
JAVASCRIPT
=========================================-->

<script>

let semester=1;

/*=========================================
ADD SEMESTER
=========================================*/

function addSemester(){

if(semester>=8){

alert("Maximum 8 Semesters Allowed.");

return;

}

semester++;

let tbody=document.querySelector("#cgpaTable tbody");

let row=`

<tr>

<td>${semester}</td>

<td>

<select

class="form-select semester-select"

onchange="loadCreditHours(this)">

<option value="">

Select Semester

</option>

<option value="1">Semester 1</option>

<option value="2">Semester 2</option>

<option value="3">Semester 3</option>

<option value="4">Semester 4</option>

<option value="5">Semester 5</option>

<option value="6">Semester 6</option>

<option value="7">Semester 7</option>

<option value="8">Semester 8</option>

</select>

</td>

<td>

<input

type="number"

step="0.01"

min="0"

max="4"

class="form-control semester-gpa"

placeholder="Enter GPA">

</td>

<td>

<input

type="text"

class="form-control credit-hours"

readonly>

</td>

</tr>

`;

tbody.insertAdjacentHTML("beforeend",row);

}


/*=========================================
CALCULATE CGPA
=========================================*/

function calculateCGPA(){

let inputs=document.querySelectorAll(".semester-gpa");

let total=0;

let count=0;

inputs.forEach(function(input){

let value=parseFloat(input.value);

if(!isNaN(value)){

total+=value;

count++;

}

});

let cgpa=0;

if(count>0){

cgpa=total/count;

}

document.getElementById("cgpaResult").innerHTML=cgpa.toFixed(2);

}

</script>

<!--=========================================
BUTTONS
=========================================-->

<div class="text-center mt-4">

<a

href="dashboard.php"

class="btn btn-dark btn-lg me-2">

<i class="fas fa-arrow-left"></i>

Back To Dashboard

</a>

<button

class="btn btn-warning btn-lg"

onclick="window.print();">

<i class="fas fa-print"></i>

Print CGPA

</button>

</div>

</div>

</div>

<!--=========================================
BOOTSTRAP JS
=========================================-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>