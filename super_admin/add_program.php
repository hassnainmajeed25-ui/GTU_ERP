<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

$message="";

/* Faculty List */

$faculty=$conn->query("

SELECT *

FROM faculties

WHERE status='Active'

ORDER BY faculty_name ASC

");

if(isset($_POST['save'])){

$faculty_id=$_POST['faculty_id'];

$program_name=trim($_POST['program_name']);

$program_code=trim($_POST['program_code']);

$duration=trim($_POST['duration']);

$status=$_POST['status'];

/* Duplicate Check */

$check=$conn->prepare("

SELECT id

FROM programs

WHERE program_name=?

LIMIT 1

");

$check->bind_param("s",$program_name);

$check->execute();

$result=$check->get_result();

if($result->num_rows>0){

$message="<div class='alert alert-danger'>Program already exists.</div>";

}else{

$insert=$conn->prepare("

INSERT INTO programs

(faculty_id,program_name,program_code,duration,status)

VALUES

(?,?,?,?,?)

");

$insert->bind_param(

"issss",

$faculty_id,

$program_name,

$program_code,

$duration,

$status

);

if($insert->execute()){

header("Location: manage_programs.php");

exit;

}else{

$message="<div class='alert alert-danger'>Unable to save program.</div>";

}

}

}

?>
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Add Program</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

</head>

<body style="background:#f4f6f9;">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-plus-circle me-2"></i>

Add New Program

</h4>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">
<div class="mb-3">

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

<?php while($fac=$faculty->fetch_assoc()){ ?>

<option value="<?php echo $fac['id']; ?>">

<?php echo $fac['faculty_name']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Program Name

</label>

<input

type="text"

name="program_name"

class="form-control"

placeholder="Enter Program Name"

required>

</div>

<div class="mb-3">

<label class="form-label">

Program Code

</label>

<input

type="text"

name="program_code"

class="form-control"

placeholder="Example : BSCS"

required>

</div>

<div class="mb-3">

<label class="form-label">

Duration

</label>

<input

type="text"

name="duration"

class="form-control"

placeholder="Example : 4 Years"

required>

</div>

<div class="mb-3">

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

<a href="manage_programs.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back

</a>

<button

type="submit"

name="save"

class="btn btn-success">

<i class="fas fa-save"></i>

Save Program

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>