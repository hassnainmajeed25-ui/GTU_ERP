<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

$message="";

/* Active Faculties */

$faculties=$conn->query("

SELECT *

FROM faculties

WHERE status='Active'

ORDER BY faculty_name ASC

");

/* Active Sessions */

$sessions=$conn->query("

SELECT *

FROM admission_sessions

WHERE status='Active'

ORDER BY session_name ASC

");

if(isset($_POST['save'])){

$faculty_id=(int)$_POST['faculty_id'];

$program_id=(int)$_POST['program_id'];

$session_id=(int)$_POST['session_id'];

$shift=$_POST['shift'];

$semester=(int)$_POST['semester'];

$admission_fee=(float)$_POST['admission_fee'];

$tuition_fee=(float)$_POST['tuition_fee'];

$exam_fee=(float)$_POST['exam_fee'];

$library_fee=(float)$_POST['library_fee'];

$lab_fee=(float)$_POST['lab_fee'];

$other_fee=(float)$_POST['other_fee'];

$status=$_POST['status'];

/* Duplicate Check */

$check=$conn->prepare("

SELECT id
FROM fee_structure
WHERE faculty_id=?
AND program_id=?
AND session_id=?
AND semester=?
AND shift=?
LIMIT 1

");

$check->bind_param(
"iiiis",
$faculty_id,
$program_id,
$session_id,
$semester,
$shift
);


$check->execute();

$result=$check->get_result();

if($result->num_rows>0){

$message="<div class='alert alert-danger'>Fee Structure already exists.</div>";

}else{

$insert=$conn->prepare("

INSERT INTO fee_structure
(

faculty_id,

program_id,

session_id,

semester,

shift,

admission_fee,

tuition_fee,

exam_fee,

library_fee,

lab_fee,

other_fee,

status

)

VALUES

(?,?,?,?,?,?,?,?,?,?,?,?)

");

$insert->bind_param(

"iiiisdddddds",
$faculty_id,

$program_id,

$session_id,

$semester,

$shift,

$admission_fee,

$tuition_fee,

$exam_fee,

$library_fee,

$lab_fee,

$other_fee,

$status

);

if($insert->execute()){

header("Location: manage_fee_structure.php");

exit;

}else{

$message="<div class='alert alert-danger'>Unable to save Fee Structure.</div>";

}

}

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Fee Structure</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f4f6f9;

}

.card{

margin-top:40px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#198754;

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

.form-control,
.form-select{

height:50px;
border-radius:10px;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card">

<div class="card-header">

<i class="fas fa-money-check-dollar me-2"></i>

Add Fee Structure

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Faculty

</label>

<select

name="faculty_id"

id="faculty"

class="form-select"

required>

<option value="">

Select Faculty

</option>

<?php while($faculty=$faculties->fetch_assoc()){ ?>

<option value="<?php echo $faculty['id']; ?>">

<?php echo $faculty['faculty_name']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Program

</label>

<select

name="program_id"

id="program"

class="form-select"

required>

<option value="">

First Select Faculty

</option>

</select>

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Shift

</label>

<select
name="shift"
class="form-select"
required>

<option value="Morning">

Morning

</option>

<option value="Evening">

Evening

</option>


<option value="Replica">

Replica

</option>
</select>

</div>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Semester

</label>

<select
name="semester"
class="form-select"
required>

<option value="1">Semester 1 (Admission)</option>

<option value="2">Semester 2 (Promotion)</option>

<option value="3">Semester 3</option>

<option value="4">Semester 4</option>

<option value="5">Semester 5</option>

<option value="6">Semester 6</option>

<option value="7">Semester 7</option>

<option value="8">Semester 8</option>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Admission Session

</label>


<select

name="session_id"

class="form-select"

required>

<option value="">

Select Session

</option>

<?php while($session=$sessions->fetch_assoc()){ ?>

<option value="<?php echo $session['id']; ?>">

<?php echo $session['session_name']; ?>

</option>

<?php } ?>

</select>

</div>

</div>
<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Admission Fee

</label>

<input

type="number"

name="admission_fee"

class="form-control"

value="0"

min="0"

step="0.01"

required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Tuition Fee

</label>

<input

type="number"

name="tuition_fee"

class="form-control"

value="0"

min="0"

step="0.01"

required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Exam Fee

</label>

<input

type="number"

name="exam_fee"

class="form-control"

value="0"

min="0"

step="0.01"

required>

</div>

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Library Fee

</label>

<input

type="number"

name="library_fee"

class="form-control"

value="0"

min="0"

step="0.01"

required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Lab Fee

</label>

<input

type="number"

name="lab_fee"

class="form-control"

value="0"

min="0"

step="0.01"

required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Other Fee

</label>

<input

type="number"

name="other_fee"

class="form-control"

value="0"

min="0"

step="0.01"

required>

</div>

</div>

<div class="mb-4">

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

<div class="d-flex justify-content-between">

<a

href="manage_fee_structure.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="save"

class="btn btn-success">

<i class="fas fa-save me-2"></i>

Save Fee Structure

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script>

document.getElementById("faculty").addEventListener("change",function(){

let faculty=this.value;

let program=document.getElementById("program");

program.innerHTML="<option>Loading...</option>";

fetch("programs.php?faculty_id="+faculty)

.then(response=>response.text())

.then(data=>{

program.innerHTML=data;

});

});

</script>

</body>

</html>