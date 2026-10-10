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
LOAD DROPDOWNS
====================================*/

$subjects=$conn->query("
SELECT id,subject_code,subject_name
FROM subjects
WHERE status='Active'
ORDER BY subject_name ASC
");

$faculties=$conn->query("
SELECT id,faculty_name
FROM faculties
ORDER BY faculty_name ASC
");

$programs=$conn->query("
SELECT id,program_name
FROM programs
ORDER BY program_name ASC
");

/*====================================
SAVE EXAM
====================================*/

if(isset($_POST['save_exam'])){

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

$created_by=$_SESSION['admin_id'];

/*====================================
CHECK DUPLICATE
====================================*/

$check=$conn->prepare("
SELECT id
FROM exams
WHERE
subject_id=?
AND exam_date=?
AND start_time=?
LIMIT 1
");

$check->bind_param(
"iss",
$subject_id,
$exam_date,
$start_time
);

$check->execute();

$res=$check->get_result();

if($res->num_rows>0){

$message="This examination already exists.";

$messageType="danger";

}else{

$stmt=$conn->prepare("
INSERT INTO exams(

subject_id,
exam_title,
exam_type,
faculty_id,
program_id,
semester,
session,
exam_date,
start_time,
end_time,
room_no,
status,
created_by

)

VALUES(

?,?,?,?,?,?,?,?,?,?,?,?,?

)
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
$created_by

);

if($stmt->execute()){

header("Location: manage_exams.php?added=1");

exit;

}else{

$message="Unable to save examination.";

$messageType="danger";

}

}

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Add Examination

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">
<body>

<div class="container mt-4">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card shadow">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h4 class="mb-0">

<i class="fas fa-calendar-days me-2"></i>

Add New Examination

</h4>

<a
href="manage_exams.php"
class="btn btn-light btn-sm">

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

<!-- Exam Title -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Title

</label>

<input

type="text"

name="exam_title"

class="form-control"

placeholder="Example: Mid Term OOP"

required>

</div>

<!-- Subject -->

<div class="col-md-6 mb-3">

<label class="form-label">

Subject

</label>

<select
name="subject_id"
class="form-select"
required>

<option value="">

Select Subject

</option>

<?php

while($subject=$subjects->fetch_assoc()){

?>

<option value="<?php echo $subject['id']; ?>">

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

<option>Mid Term</option>

<option>Final Term</option>

<option>Quiz</option>

<option>Assignment</option>

<option>Practical</option>

<option>Viva</option>

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

<option value="">

Select Faculty

</option>

<?php

while($faculty=$faculties->fetch_assoc()){

?>

<option value="<?php echo $faculty['id']; ?>">

<?php echo $faculty['faculty_name']; ?>

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

<option value="">

Select Program

</option>

<?php

while($program=$programs->fetch_assoc()){

?>

<option value="<?php echo $program['id']; ?>">

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

<option value="">Select Semester</option>

<?php

for($i=1;$i<=8;$i++){

?>

<option value="<?php echo $i; ?>">

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
placeholder="Example: Fall 2026"
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
placeholder="Example: Room A-101"
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

<option value="Upcoming">Upcoming</option>

<option value="Running">Running</option>

<option value="Completed">Completed</option>

</select>

</div>

</div>

<hr>

<div class="text-end">

<button
type="submit"
name="save_exam"
class="btn btn-success btn-lg">

<i class="fas fa-save me-2"></i>

Save Examination

</button>

<a
href="manage_exams.php"
class="btn btn-secondary btn-lg">

Cancel

</a>

</div>

</form>

</div>

</div>

</div>

</div>

</div>
<hr>

<div class="row mt-4">

<div class="col-md-6">

<div class="alert alert-info">

<h5>

<i class="fas fa-circle-info me-2"></i>

Instructions

</h5>

<ul class="mb-0">

<li>Exam Title should be meaningful.</li>

<li>Select the correct Subject.</li>

<li>Select the correct Faculty and Program.</li>

<li>Exam Date and Time should be accurate.</li>

<li>Room Number should match the official schedule.</li>

<li>Duplicate exams are not allowed.</li>

</ul>

</div>

</div>

<div class="col-md-6">

<div class="alert alert-success">

<h5>

<i class="fas fa-lightbulb me-2"></i>

Examination Office Tips

</h5>

<ul class="mb-0">

<li>Every exam belongs to one subject.</li>

<li>Teachers will enter marks after the exam.</li>

<li>Admit Cards are generated from this schedule.</li>

<li>Results depend on the saved examination record.</li>

<li>All examination data is permanently stored.</li>

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