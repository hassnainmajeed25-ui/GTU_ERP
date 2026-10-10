<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*====================================
LOGIN SECURITY
====================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Examination Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

$message="";
$messageType="";

/*====================================
CHECK EXAM ID
====================================*/

if(!isset($_GET['id'])){

header("Location: manage_exams.php");

exit;

}

$id=(int)$_GET['id'];

/*====================================
LOAD EXAM
====================================*/

$stmt=$conn->prepare("

SELECT *

FROM exams

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location: manage_exams.php");

exit;

}

$exam=$result->fetch_assoc();

/*====================================
LOAD DROPDOWNS
====================================*/

$subjects=$conn->query("

SELECT
id,
subject_code,
subject_name

FROM subjects

WHERE status='Active'

ORDER BY subject_name ASC

");

$faculties=$conn->query("

SELECT
id,
faculty_name

FROM faculties

ORDER BY faculty_name ASC

");

$programs=$conn->query("

SELECT
id,
program_name

FROM programs

ORDER BY program_name ASC

");

/*====================================
UPDATE EXAM
====================================*/

if(isset($_POST['update_exam'])){

$subject_id=$_POST['subject_id'];

$exam_title=trim($_POST['exam_title']);

$exam_type=$_POST['exam_type'];

$faculty_id=$_POST['faculty_id'];

$program_id=$_POST['program_id'];

$semester=$_POST['semester'];

$session=trim($_POST['session']);

$exam_date=$_POST['exam_date'];

$start_time=$_POST['start_time'];

$end_time=$_POST['end_time'];

$room_no=trim($_POST['room_no']);

$status=$_POST['status'];

/*====================================
SAVE UPDATED EXAM
====================================*/

$stmt=$conn->prepare("

UPDATE exams

SET

subject_id=?,
exam_title=?,
exam_type=?,
faculty_id=?,
program_id=?,
semester=?,
session=?,
exam_date=?,
start_time=?,
end_time=?,
room_no=?,
status=?

WHERE id=?

");

$stmt->bind_param(

"isssiissssssi",

$subject_id,
$exam_title,
$exam_type,
$faculty_id,
$program_id,
$semester,
$session,
$exam_date,
$start_time,
$end_time,
$room_no,
$status,
$id

);

if($stmt->execute()){

header("Location: manage_exams.php?updated=1");

exit;

}else{

$message="Unable to update examination.";

$messageType="danger";

}

}

/*====================================
PAGE START
====================================*/

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Edit Examination

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

</head>

<body>

<div class="container mt-4">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card shadow">

<div class="card-header bg-primary text-white d-flex justify-content-between">

<h4>

<i class="fas fa-edit me-2"></i>

Edit Examination

</h4>

<a
href="manage_exams.php"
class="btn btn-light">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

<div class="card-body">

<?php

if($message!=""){

?>

<div class="alert alert-<?php echo $messageType; ?>">

<?php echo $message; ?>

</div>

<?php

}

?>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>

Exam Title

</label>

<input
type="text"
name="exam_title"
class="form-control"
value="<?php echo htmlspecialchars($exam['exam_title']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>

Subject

</label>

<select
name="subject_id"
class="form-select"
required>

<?php

while($subject=$subjects->fetch_assoc()){

?>

<option

value="<?php echo $subject['id']; ?>"

<?php

if($subject['id']==$exam['subject_id']){

echo "selected";

}

?>

>

<?php

echo $subject['subject_code'];

echo " - ";

echo $subject['subject_name'];

?>

</option>

<?php

}

?>

</select>

</div>

<!-- Exam Type -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Type

</label>

<select
name="exam_type"
class="form-select"
required>

<option value="Mid Term" <?php if($exam['exam_type']=="Mid Term") echo "selected"; ?>>

Mid Term

</option>

<option value="Final Term" <?php if($exam['exam_type']=="Final Term") echo "selected"; ?>>

Final Term

</option>

<option value="Quiz" <?php if($exam['exam_type']=="Quiz") echo "selected"; ?>>

Quiz

</option>

<option value="Assignment" <?php if($exam['exam_type']=="Assignment") echo "selected"; ?>>

Assignment

</option>

<option value="Practical" <?php if($exam['exam_type']=="Practical") echo "selected"; ?>>

Practical

</option>

<option value="Viva" <?php if($exam['exam_type']=="Viva") echo "selected"; ?>>

Viva

</option>

</select>

</div>

<!-- Faculty -->

<div class="col-md-6 mb-3">

<label class="form-label">

Faculty

</label>

<select
name="faculty_id"
class="form-select"
required>

<?php

while($faculty=$faculties->fetch_assoc()){

?>

<option
value="<?php echo $faculty['id']; ?>"

<?php

if($faculty['id']==$exam['faculty_id']){

echo "selected";

}

?>

>

<?php echo htmlspecialchars($faculty['faculty_name']); ?>

</option>

<?php

}

?>

</select>

</div>

<!-- Program -->

<div class="col-md-6 mb-3">

<label class="form-label">

Program

</label>

<select
name="program_id"
class="form-select"
required>

<?php

while($program=$programs->fetch_assoc()){

?>

<option
value="<?php echo $program['id']; ?>"

<?php

if($program['id']==$exam['program_id']){

echo "selected";

}

?>

>

<?php echo htmlspecialchars($program['program_name']); ?>

</option>

<?php

}

?>

</select>

</div>

<!-- Semester -->

<div class="col-md-6 mb-3">

<label class="form-label">

Semester

</label>

<select
name="semester"
class="form-select"
required>

<?php

for($i=1;$i<=8;$i++){

?>

<option

value="<?php echo $i; ?>"

<?php

if($i==$exam['semester']){

echo "selected";

}

?>

>

Semester <?php echo $i; ?>

</option>

<?php

}

?>

</select>

</div>

<!-- Session -->

<div class="col-md-6 mb-3">

<label class="form-label">

Session

</label>

<input
type="text"
name="session"
class="form-control"
value="<?php echo htmlspecialchars($exam['session']); ?>"
required>

</div>

<!-- Exam Date -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Date

</label>

<input
type="date"
name="exam_date"
class="form-control"
value="<?php echo $exam['exam_date']; ?>"
required>

</div>

<!-- Start Time -->

<div class="col-md-6 mb-3">

<label class="form-label">

Start Time

</label>

<input
type="time"
name="start_time"
class="form-control"
value="<?php echo $exam['start_time']; ?>"
required>

</div>

<!-- End Time -->

<div class="col-md-6 mb-3">

<label class="form-label">

End Time

</label>

<input
type="time"
name="end_time"
class="form-control"
value="<?php echo $exam['end_time']; ?>"
required>

</div>

<!-- Room Number -->

<div class="col-md-6 mb-3">

<label class="form-label">

Room Number

</label>

<input
type="text"
name="room_no"
class="form-control"
value="<?php echo htmlspecialchars($exam['room_no']); ?>"
required>

</div>

<!-- Status -->

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select"
required>

<option value="Upcoming" <?php if($exam['status']=="Upcoming") echo "selected"; ?>>

Upcoming

</option>

<option value="Running" <?php if($exam['status']=="Running") echo "selected"; ?>>

Running

</option>

<option value="Completed" <?php if($exam['status']=="Completed") echo "selected"; ?>>

Completed

</option>

</select>

</div>

</div>

<hr>

<div class="d-flex justify-content-end">

<button
type="submit"
name="update_exam"
class="btn btn-success btn-lg me-2">

<i class="fas fa-save me-2"></i>

Update Examination

</button>

<a
href="manage_exams.php"
class="btn btn-secondary btn-lg">

<i class="fas fa-times me-2"></i>

Cancel

</a>

</div>

</form>

</div>

</div>

<br>

<!-- ===========================
EXAM INFORMATION
=========================== -->

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Examination Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Edit only if officially approved.

</li>

<li class="list-group-item">

✔ Subject cannot be removed after marks entry.

</li>

<li class="list-group-item">

✔ Room Number should match official schedule.

</li>

<li class="list-group-item">

✔ Date & Time should be verified carefully.

</li>

</ul>

</div>

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Teachers will enter marks after examination.

</li>

<li class="list-group-item">

✔ Admit Cards depend on this schedule.

</li>

<li class="list-group-item">

✔ Results are generated from examination records.

</li>

<li class="list-group-item">

✔ All updates are saved permanently.

</li>

</ul>

</div>

</div>

</div>

</div>

</div>

</div>

<footer class="text-center mt-5 mb-3">

<hr>

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>