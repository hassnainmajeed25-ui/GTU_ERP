<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

$message="";

if(isset($_POST['save'])){

$session_name=trim($_POST['session_name']);

$start_date=$_POST['start_date'];

$end_date=$_POST['end_date'];

$status=$_POST['status'];

/* Duplicate Check */

$check=$conn->prepare("

SELECT id

FROM admission_sessions

WHERE session_name=?

LIMIT 1

");

$check->bind_param("s",$session_name);

$check->execute();

$result=$check->get_result();

if($result->num_rows>0){

$message="<div class='alert alert-danger'>Admission Session already exists.</div>";

}else{

$insert=$conn->prepare("

INSERT INTO admission_sessions

(session_name,start_date,end_date,status)

VALUES

(?,?,?,?)

");

$insert->bind_param(

"ssss",

$session_name,

$start_date,

$end_date,

$status

);

if($insert->execute()){

header("Location: manage_sessions.php");

exit;

}else{

$message="<div class='alert alert-danger'>Unable to save Admission Session.</div>";

}

}

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Admission Session</title>

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

.form-control{

height:50px;

border-radius:10px;

}

.form-select{

height:50px;

border-radius:10px;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card">

<div class="card-header">

<i class="fas fa-calendar-plus me-2"></i>

Add Admission Session

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">
    <div class="mb-3">

<label class="form-label">

Admission Session

</label>

<input

type="text"

name="session_name"

class="form-control"

placeholder="Example : Fall 2026"

required>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Start Date

</label>

<input

type="date"

name="start_date"

class="form-control"

required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

End Date

</label>

<input

type="date"

name="end_date"

class="form-control"

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

href="manage_sessions.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="save"

class="btn btn-success">

<i class="fas fa-save me-2"></i>

Save Admission Session

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