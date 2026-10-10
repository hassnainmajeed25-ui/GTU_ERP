<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}

$student_id = $_SESSION['student_id'];

/*=========================================
LOAD STUDENT INFORMATION
=========================================*/

$sql="

SELECT

students.id,
students.first_name,
students.last_name,
students.registration_no,
students.roll_no,
students.program_id,
students.semester,
students.shift,

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

";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$student_id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

    session_destroy();

    header("Location:login.php");

    exit;

}

$student=$result->fetch_assoc();

$page_title="Course Marks";

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

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{

background:#edf4fb;

font-family:'Segoe UI',sans-serif;

}

/*=========================================
SIDEBAR
=========================================*/

.sidebar{

position:fixed;

top:0;
left:0;

width:270px;

height:100vh;

background:#1f1f1f;

overflow-y:auto;

}

.logo{

padding:25px;

text-align:center;

border-bottom:1px solid rgba(255,255,255,.10);

}

.logo img{

width:80px;

margin-bottom:10px;

}

.logo h4{

color:#ff9800;

font-weight:bold;

}

.logo small{

color:#ccc;

}

.sidebar ul{

list-style:none;

padding:20px 0;

}

.sidebar ul li{

margin-bottom:5px;

}

.sidebar ul li a{

display:block;

padding:15px 25px;

color:#fff;

text-decoration:none;

transition:.30s;

}

.sidebar ul li a:hover{

background:#ff9800;

color:#fff;

padding-left:35px;

}

.sidebar ul li a.active{

background:#29b6f6;

font-weight:bold;

}

/*=========================================
MAIN
=========================================*/

.main{

margin-left:270px;

padding:30px;

}

/*=========================================
HEADER
=========================================*/

.page-header{

background:linear-gradient(135deg,#29b6f6,#ff9800);

border-radius:20px;

padding:30px;

color:#fff;

box-shadow:0 10px 30px rgba(0,0,0,.15);

}

.page-header h2{

font-weight:bold;

margin-bottom:10px;

}

.page-header p{

margin:0;

font-size:16px;

}

.info-card{

background:#fff;

border-radius:18px;

padding:25px;

margin-top:25px;

box-shadow:0 6px 20px rgba(0,0,0,.08);

}

.info-title{

color:#666;

font-size:14px;

font-weight:bold;

margin-bottom:5px;

}

.info-value{

font-size:17px;

font-weight:bold;

color:#333;

}

</style>

</head>

<body>

<!-- =========================================
SIDEBAR
========================================= -->

<div class="sidebar">

<div class="logo">

<img src="../assets/images/logo.png">

<h4>GTU ERP</h4>

<small>Student Portal</small>

</div>

<ul>

<li>
<a href="dashboard.php">
<i class="fas fa-house"></i>
Dashboard
</a>
</li>

<li>
<a href="profile.php">
<i class="fas fa-user"></i>
My Profile
</a>
</li>

<li>
<a href="course_marks.php" class="active">
<i class="fas fa-square-poll-vertical"></i>
Course Marks
</a>
</li>

<li>
<a href="attendance.php">
<i class="fas fa-calendar-check"></i>
Attendance
</a>
</li>

<li>
<a href="results.php">
<i class="fas fa-award"></i>
Results
</a>
</li>

<li>
<a href="logout.php">
<i class="fas fa-right-from-bracket"></i>
Logout
</a>
</li>

</ul>

</div>

<!-- =========================================
MAIN CONTENT
========================================= -->

<div class="main">

<div class="page-header">

<h2>

<i class="fas fa-square-poll-vertical"></i>

Course Marks

</h2>

<p>

View Quiz, Assignment, Presentation, Mid, Final and Practical Marks

</p>

</div>

<!-- =========================================
LOAD STUDENT SUBJECTS
========================================= -->

<?php

$subjectQuery=$conn->prepare("

SELECT

subjects.*,

teachers.teacher_name,
teachers.profile_photo

FROM subjects

LEFT JOIN teachers
ON teachers.id=subjects.teacher_id

WHERE

subjects.program_id=?

AND

subjects.semester=?

AND

subjects.status='Active'

ORDER BY subjects.subject_name ASC

");

$subjectQuery->bind_param(

"ii",

$student['program_id'],

$student['semester']

);

$subjectQuery->execute();

$subjects=$subjectQuery->get_result();

?>

<div class="row mt-4">

<?php

if($subjects->num_rows>0){

while($subject=$subjects->fetch_assoc()){

?>

<div class="col-lg-6 mb-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<h4 class="fw-bold text-dark mb-1">

<?php echo htmlspecialchars($subject['subject_name']); ?>

</h4>

<span class="badge bg-dark">

<?php echo htmlspecialchars($subject['subject_code']); ?>

</span>

<span class="badge bg-warning text-dark ms-2">

<?php echo $subject['credit_hours']; ?> Credit Hours

</span>

<?php

if($subject['has_practical']=="Yes"){

?>

<span class="badge bg-success ms-2">

Practical Subject

</span>

<?php

}else{

?>

<span class="badge bg-info text-dark ms-2">

Theory Subject

</span>

<?php

}

?>

</div>

<div>

<?php

$image="../uploads/teachers/".$subject['profile_photo'];

if(!empty($subject['profile_photo']) && file_exists($image)){

?>

<img

src="<?php echo $image; ?>"

style="width:70px;
height:70px;
border-radius:50%;
object-fit:cover;
border:3px solid orange;">

<?php

}else{

?>

<div

style="width:70px;
height:70px;
border-radius:50%;
background:#29b6f6;
display:flex;
align-items:center;
justify-content:center;
color:white;
font-size:28px;">

<i class="fas fa-user-tie"></i>

</div>

<?php

}

?>

</div>

</div>

<hr>

<div class="row">

<div class="col-6">

<p class="mb-1 text-secondary">

Teacher

</p>

<h6 class="fw-bold">

<?php

echo !empty($subject['teacher_name'])

? htmlspecialchars($subject['teacher_name'])

: "Not Assigned";

?>

</h6>

</div>

<div class="col-6 text-end">

<p class="mb-1 text-secondary">

Semester

</p>

<h6 class="fw-bold">

Semester <?php echo $student['semester']; ?>

</h6>

</div>

</div>

<hr>

<div class="d-grid">

<a

href="course_marks_details.php?subject_id=<?php echo $subject['id']; ?>"

class="btn btn-warning btn-lg">

<i class="fas fa-square-poll-vertical"></i>

View Course Marks

</a>

</div>

</div>

</div>

</div>

<?php

}

}else{

?>

<div class="col-12">

<div class="alert alert-warning text-center shadow">

<h4>

<i class="fas fa-book-open"></i>

No Subjects Found

</h4>

<p class="mb-0">

No subjects have been assigned to your semester yet.

</p>

</div>

</div>

<?php

}

?>

</div>

<!-- =========================================
BACK BUTTON
========================================= -->

<div class="text-center mt-4">

<a
href="subjects.php"
class="btn btn-secondary btn-lg me-2">

<i class="fas fa-arrow-left"></i>

Back to Subjects

</a>

<a
href="dashboard.php"
class="btn btn-primary btn-lg">

<i class="fas fa-house"></i>

Dashboard

</a>

</div>

<!-- =========================================
FOOTER
========================================= -->

<hr class="mt-5">

<div class="text-center text-muted mb-3">

<img
src="../assets/images/logo.png"
style="height:55px;">

<h5 class="mt-3 text-dark">

Global Tech University ERP

</h5>

<p>

Student Course Marks Portal

</p>

<p>

© <?php echo date("Y"); ?>

All Rights Reserved.

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>