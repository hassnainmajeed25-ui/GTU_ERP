<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
header("Location: ../admins/login.php");
exit;
}

if($_SESSION['department']!="Examination Office"){
header("Location: ../admins/login.php");
exit;
}

$admin_name=$_SESSION['admin_name'];

$success="";
$error="";

/*=========================================
GET FACULTIES
=========================================*/

$faculties=$conn->query("

SELECT *

FROM faculties

WHERE status='Active'

ORDER BY faculty_name ASC

");

/*=========================================
GET PROGRAMS
=========================================*/

$programs=$conn->query("

SELECT *

FROM programs

WHERE status='Active'

ORDER BY program_name ASC

");

/*=========================================
GET TEACHERS
=========================================*/

$teachers=$conn->query("

SELECT *

FROM teachers

WHERE status='Active'

ORDER BY teacher_name ASC

");

/*=========================================
SAVE SUBJECT
=========================================*/

if(isset($_POST['save'])){

$subject_code=trim($_POST['subject_code']);

$subject_name=trim($_POST['subject_name']);

$credit_hours=(int)$_POST['credit_hours'];

$faculty_id=(int)$_POST['faculty_id'];

$program_id=(int)$_POST['program_id'];

$semester=(int)$_POST['semester'];

$teacher_id=!empty($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : NULL;

$status=$_POST['status'];


/*=========================================
CHECK DUPLICATE SUBJECT CODE
=========================================*/

$check=$conn->prepare("

SELECT id

FROM subjects

WHERE subject_code=?

LIMIT 1

");

$check->bind_param("s",$subject_code);

$check->execute();

$checkResult=$check->get_result();

if($checkResult->num_rows>0){

$error="Subject Code already exists.";

}else{

$stmt=$conn->prepare("

INSERT INTO subjects(

subject_code,

subject_name,

credit_hours,

faculty_id,

program_id,

semester,

teacher_id,

status

)

VALUES(?,?,?,?,?,?,?,?)

");

$stmt->bind_param(

"ssiiiiis",

$subject_code,

$subject_name,

$credit_hours,

$faculty_id,

$program_id,

$semester,

$teacher_id,

$status

);

if($stmt->execute()){

header("Location: manage_subjects.php");

exit;

}else{

$error="Database Error.";

}

}

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>

Add Subject

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.sidebar{

width:260px;

height:100vh;

background:#0B1F52;

position:fixed;

left:0;

top:0;

overflow:auto;

}

.sidebar h3{

color:#fff;

padding:20px;

text-align:center;

border-bottom:1px solid rgba(255,255,255,.15);

}

.sidebar a{

display:block;

padding:14px 20px;

color:#fff;

text-decoration:none;

transition:.3s;

}

.sidebar a:hover{

background:#173a8b;

}

.sidebar a.active{

background:#2563eb;

}

.main{

margin-left:260px;

padding:30px;

}

.topbar{

background:#fff;

padding:20px;

border-radius:12px;

box-shadow:0 3px 10px rgba(0,0,0,.1);

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:30px;

}

.card{

border:none;

border-radius:18px;

box-shadow:0 5px 15px rgba(0,0,0,.10);

}

.form-label{

font-weight:bold;

}

</style>

</head>
<body>

<!-- ===========================
SIDEBAR
=========================== -->

<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php">
<i class="fas fa-home me-2"></i>
Dashboard
</a>

<a href="manage_subjects.php" class="active">
<i class="fas fa-book me-2"></i>
Manage Subjects
</a>

<a href="manage_exams.php">
<i class="fas fa-calendar-days me-2"></i>
Manage Exams
</a>

<a href="enter_marks.php">
<i class="fas fa-pen me-2"></i>
Enter Marks
</a>

<a href="results.php">
<i class="fas fa-square-poll-vertical me-2"></i>
Results
</a>

<a href="admit_cards.php">
<i class="fas fa-id-card me-2"></i>
Admit Cards
</a>

<a href="reports.php">
<i class="fas fa-chart-column me-2"></i>
Reports
</a>

<a href="compose_message.php">
<i class="fas fa-paper-plane me-2"></i>
Compose Message
</a>

<a href="inbox.php">
<i class="fas fa-envelope me-2"></i>
Inbox
</a>

<a href="profile.php">
<i class="fas fa-user-circle me-2"></i>
Profile
</a>

<a href="../admins/logout.php">
<i class="fas fa-right-from-bracket me-2"></i>
Logout
</a>

</div>

<!-- ===========================
MAIN AREA
=========================== -->

<div class="main">

<div class="topbar">

<div>

<h3>

Add New Subject

</h3>

<small class="text-muted">

Global Tech University ERP

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-book-open me-2"></i>

Subject Information

</h5>

</div>

<div class="card-body">

<?php

if($success!=""){

?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php

}

?>

<?php

if($error!=""){

?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php

}

?>

<form method="POST">

<div class="row">
<!-- Subject Code -->

<div class="col-md-6 mb-3">

<label class="form-label">

Subject Code

</label>

<input

type="text"

name="subject_code"

class="form-control"

placeholder="Example : CS101"

required>

</div>

<!-- Subject Name -->

<div class="col-md-6 mb-3">

<label class="form-label">

Subject Name

</label>

<input

type="text"

name="subject_name"

class="form-control"

placeholder="Object Oriented Programming"

required>

</div>

<!-- Credit Hours -->

<div class="col-md-4 mb-3">

<label class="form-label">

Credit Hours

</label>

<select

name="credit_hours"

class="form-select"

required>

<option value="">Choose</option>

<option>1</option>

<option>2</option>

<option>3</option>

<option>4</option>

<option>5</option>

<option>6</option>

</select>

</div>

<!-- Faculty -->

<div class="col-md-4 mb-3">

<label class="form-label">

Faculty

</label>

<select

name="faculty_id"

class="form-select"

required>

<option value="">Select Faculty</option>

<?php

while($row=$faculties->fetch_assoc()){

?>

<option value="<?php echo $row['id'];?>">

<?php echo htmlspecialchars($row['faculty_name']);?>

</option>

<?php

}

?>

</select>

</div>

<!-- Program -->

<div class="col-md-4 mb-3">

<label class="form-label">

Program

</label>

<select

name="program_id"

class="form-select"

required>

<option value="">Select Program</option>

<?php

while($row=$programs->fetch_assoc()){

?>

<option value="<?php echo $row['id'];?>">

<?php echo htmlspecialchars($row['program_name']);?>

</option>

<?php

}

?>

</select>

</div>

<!-- Semester -->

<div class="col-md-4 mb-3">

<label class="form-label">

Semester

</label>

<select

name="semester"

class="form-select"

required>

<option value="">Select Semester</option>

<?php

for($i=1;$i<=8;$i++){

?>

<option value="<?php echo $i;?>">

Semester <?php echo $i;?>

</option>

<?php

}

?>

</select>

</div>

<!-- Teacher -->

<div class="col-md-4 mb-3">

<label class="form-label">

Assign Teacher

</label>

<select

name="teacher_id"

class="form-select">

<option value="">

Not Assigned

</option>

<?php

while($row=$teachers->fetch_assoc()){

?>

<option value="<?php echo $row['id'];?>">

<?php echo htmlspecialchars($row['teacher_name']);?>

</option>

<?php

}

?>

</select>

</div>

<!-- Status -->

<div class="col-md-4 mb-3">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select">

<option value="Active">

Active

</option>

<option value="Inactive">

Inactive

</option>

</select>

</div>
</div>

<hr>

<div class="d-flex justify-content-between">

<a
href="manage_subjects.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<div>

<button
type="reset"
class="btn btn-warning">

<i class="fas fa-rotate-left me-2"></i>

Reset

</button>

<button
type="submit"
name="save"
class="btn btn-success ms-2">

<i class="fas fa-save me-2"></i>

Save Subject

</button>

</div>

</div>

</form>

</div>

</div>

<br>

<!-- ===============================
INFORMATION CARD
================================ -->

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Subject Entry Guidelines

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Subject Code must be unique.

</li>

<li class="list-group-item">

✔ Example Code:
CS101
CS205
SE301

</li>

<li class="list-group-item">

✔ Credit Hours normally
3 or 4.

</li>

<li class="list-group-item">

✔ Semester should match Program Scheme.

</li>

</ul>

</div>

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Teacher assignment is optional.

</li>

<li class="list-group-item">

✔ Subject can be activated later.

</li>

<li class="list-group-item">

✔ Faculty and Program should already exist.

</li>

<li class="list-group-item">

✔ Subject becomes available in Examination Module after saving.

</li>

</ul>

</div>

</div>

</div>

</div>

<br>

<!-- ===============================
QUICK STATISTICS
================================ -->

<div class="row">

<div class="col-md-3">

<div class="alert alert-primary text-center">

<h3>

<?php

$total=$conn->query("SELECT COUNT(*) total FROM subjects")->fetch_assoc()['total'];

echo $total;

?>

</h3>

Total Subjects

</div>

</div>

<div class="col-md-3">

<div class="alert alert-success text-center">

<h3>

<?php

$total=$conn->query("SELECT COUNT(*) total FROM faculties")->fetch_assoc()['total'];

echo $total;

?>

</h3>

Faculties

</div>

</div>

<div class="col-md-3">

<div class="alert alert-info text-center">

<h3>

<?php

$total=$conn->query("SELECT COUNT(*) total FROM programs")->fetch_assoc()['total'];

echo $total;

?>

</h3>

Programs

</div>

</div>

<div class="col-md-3">

<div class="alert alert-warning text-center">

<h3>

<?php

$total=$conn->query("SELECT COUNT(*) total FROM teachers")->fetch_assoc()['total'];

echo $total;

?>

</h3>

Teachers

</div>

</div>

</div>

<hr>

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>