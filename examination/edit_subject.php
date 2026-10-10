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

/*=========================================
CHECK SUBJECT ID
=========================================*/

if(!isset($_GET['id'])){
    header("Location: manage_subjects.php");
    exit;
}

$id=(int)$_GET['id'];

/*=========================================
GET SUBJECT DATA
=========================================*/

$sql=$conn->prepare("
SELECT *
FROM subjects
WHERE id=?
LIMIT 1
");

$sql->bind_param("i",$id);
$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){
    header("Location: manage_subjects.php");
    exit;
}

$subject=$result->fetch_assoc();

/*=========================================
LOAD FACULTIES
=========================================*/

$faculties=$conn->query("
SELECT *
FROM faculties
WHERE status='Active'
ORDER BY faculty_name
");

/*=========================================
LOAD PROGRAMS
=========================================*/

$programs=$conn->query("
SELECT *
FROM programs
WHERE status='Active'
ORDER BY program_name
");

/*=========================================
UPDATE SUBJECT
=========================================*/

$message="";

if(isset($_POST['update'])){

    $subject_code=trim($_POST['subject_code']);
    $subject_name=trim($_POST['subject_name']);
    $credit_hours=(int)$_POST['credit_hours'];
    $faculty_id=(int)$_POST['faculty_id'];
    $program_id=(int)$_POST['program_id'];
    $semester=(int)$_POST['semester'];
    $status=$_POST['status'];

    $update=$conn->prepare("
    UPDATE subjects SET

    subject_code=?,
    subject_name=?,
    credit_hours=?,
    faculty_id=?,
    program_id=?,
    semester=?,
    status=?

    WHERE id=?
    ");

    $update->bind_param(

    "ssiiiisi",

    $subject_code,
    $subject_name,
    $credit_hours,
    $faculty_id,
    $program_id,
    $semester,
    $status,
    $id

    );

    if($update->execute()){

        header("Location: manage_subjects.php?updated=1");
        exit;

    }else{

        $message="Unable to update subject.";

    }

}

/*=========================================
RELOAD UPDATED DATA
=========================================*/

$sql=$conn->prepare("
SELECT *
FROM subjects
WHERE id=?
");

$sql->bind_param("i",$id);
$sql->execute();

$subject=$sql->get_result()->fetch_assoc();

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>Edit Subject</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.10);

}

</style>

</head>

<body>

<div class="container mt-5">
<div class="row justify-content-center">

<div class="col-lg-9">

<div class="card">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

<i class="fas fa-edit me-2"></i>

Edit Subject

</h4>

</div>

<div class="card-body">

<?php

if($message!=""){

?>

<div class="alert alert-danger">

<?php echo $message; ?>

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
required
value="<?php echo htmlspecialchars($subject['subject_code']); ?>">

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
required
value="<?php echo htmlspecialchars($subject['subject_name']); ?>">

</div>

<!-- Credit Hours -->

<div class="col-md-4 mb-3">

<label class="form-label">

Credit Hours

</label>

<input
type="number"
name="credit_hours"
class="form-control"
required
min="1"
max="6"
value="<?php echo $subject['credit_hours']; ?>">

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

<option value="">

Select Faculty

</option>

<?php

while($faculty=$faculties->fetch_assoc()){

?>

<option
value="<?php echo $faculty['id']; ?>"

<?php

if($faculty['id']==$subject['faculty_id']){

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

<div class="col-md-4 mb-3">

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

<option
value="<?php echo $program['id']; ?>"

<?php

if($program['id']==$subject['program_id']){

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
value="<?php echo $i;?>"

<?php

if($subject['semester']==$i){

echo "selected";

}

?>

>

Semester <?php echo $i;?>

</option>

<?php

}

?>

</select>

</div>

<!-- Status -->

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select">

<option
value="Active"

<?php

if($subject['status']=="Active"){

echo "selected";

}

?>

>

Active

</option>

<option
value="Inactive"

<?php

if($subject['status']=="Inactive"){

echo "selected";

}

?>

>

Inactive

</option>

</select>

</div>

</div>
<!-- ===========================
BUTTONS
=========================== -->

<hr>

<div class="d-flex justify-content-between">

<a
href="manage_subjects.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button
type="submit"
name="update"
class="btn btn-success">

<i class="fas fa-save me-2"></i>

Update Subject

</button>

</div>

</form>

</div>

</div>

</div>

</div>

<br>

<!-- ===========================
INFORMATION
=========================== -->

<div class="row justify-content-center">

<div class="col-lg-9">

<div class="card mt-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Subject Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Subject Code should be unique.

</li>

<li class="list-group-item">

✔ Credit Hours should be correct.

</li>

<li class="list-group-item">

✔ Faculty must match Program.

</li>

<li class="list-group-item">

✔ Semester should be selected carefully.

</li>

</ul>

</div>

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Examination Office manages Subjects.

</li>

<li class="list-group-item">

✔ Teachers will be assigned later.

</li>

<li class="list-group-item">

✔ Updated information is used in Exams.

</li>

<li class="list-group-item">

✔ Changes affect the whole ERP System.

</li>

</ul>

</div>

</div>

</div>

</div>

</div>

</div>

<hr class="mt-5">

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