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
GET CURRENT STUDENT INFORMATION
=========================================*/

$getStudent=$conn->prepare("

SELECT

program_id,
semester,
shift

FROM students

WHERE id=?

LIMIT 1

");

$getStudent->bind_param(

"i",

$student_id

);

$getStudent->execute();

$student=$getStudent->get_result()->fetch_assoc();

$program_id=$student['program_id'];

$semester=$student['semester'];

$shift=$student['shift'];

$page_title="Top 10 Students";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo $page_title; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef4ff;

font-family:'Segoe UI',sans-serif;

}

.main-card{

max-width:1200px;

margin:35px auto;

background:white;

border-radius:20px;

overflow:hidden;

box-shadow:0 8px 30px rgba(0,0,0,.18);

}

.header{

background:linear-gradient(135deg,#0B5ED7,#000000,#FFD400);

padding:25px;

color:white;

}

.table thead{

background:#0B5ED7;

color:white;

}

.position{

font-size:18px;

font-weight:bold;

color:#FFD400;

}

.btn-back{

background:#FFD400;

color:#000;

font-weight:bold;

}

.btn-back:hover{

background:#e6bf00;

color:#000;

}

</style>

</head>

<body>

<div class="container-fluid">

<div class="main-card">

<div class="header">

<h2>

<i class="fas fa-trophy"></i>

Top 10 Students

</h2>

<p class="mb-0">

Same Program • Same Semester • Same Shift

</p>

</div>

<div class="p-4">

<h4 class="mb-4">

<i class="fas fa-ranking-star"></i>

Class Position List

</h4>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead>

<tr>

<th width="90">

Position

</th>

<th>

Roll No

</th>

<th>

Student Name

</th>

<th width="180">

GPA / CGPA

</th>

</tr>

</thead>

<tbody>

<?php

/*=========================================
TOP 10 STUDENTS
=========================================*/

$top=$conn->prepare("

SELECT

students.roll_no,

students.first_name,

students.last_name,

ROUND(

SUM(

CASE

WHEN marks_details.obtained_marks>=85 THEN 4.00
WHEN marks_details.obtained_marks>=80 THEN 3.70
WHEN marks_details.obtained_marks>=75 THEN 3.30
WHEN marks_details.obtained_marks>=70 THEN 3.00
WHEN marks_details.obtained_marks>=65 THEN 2.70
WHEN marks_details.obtained_marks>=60 THEN 2.30
WHEN marks_details.obtained_marks>=55 THEN 2.00
WHEN marks_details.obtained_marks>=50 THEN 1.00
ELSE 0.00

END

* subjects.credit_hours

)

/SUM(subjects.credit_hours)

,2) AS gpa

FROM students

INNER JOIN marks_details
ON students.id=marks_details.student_id

INNER JOIN mark_sessions
ON mark_sessions.id=marks_details.mark_session_id

INNER JOIN subjects
ON subjects.id=mark_sessions.subject_id

WHERE

students.program_id=?

AND students.semester=?

AND students.shift=?

AND mark_sessions.result_status='Published'

GROUP BY students.id

ORDER BY gpa DESC

LIMIT 10

");

$top->bind_param(

"iis",

$program_id,

$semester,

$shift

);

$top->execute();

$list=$top->get_result();

$position=1;

while($row=$list->fetch_assoc()){

?>

<tr>

<td>

<?php

if($position==1){

echo "🥇";

}elseif($position==2){

echo "🥈";

}elseif($position==3){

echo "🥉";

}else{

echo $position;

}

?>

</td>

<td>

<?php echo htmlspecialchars($row['roll_no']); ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</strong>

</td>

<td>

<span class="badge bg-success fs-6">

<?php echo number_format($row['gpa'],2); ?>

</span>

</td>

</tr>

<?php

$position++;

}

?>

</tbody>

</table>

</div>

<div class="row mt-4">

<div class="col-md-12 text-end">

<a

href="result.php"

class="btn btn-back btn-lg">

<i class="fas fa-arrow-left"></i>

Back To Result

</a>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>