<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

$message="";

if(isset($_POST['save'])){

$faculty_name=trim($_POST['faculty_name']);

$status=$_POST['status'];

/* Check Duplicate */

$check=$conn->prepare("

SELECT id

FROM faculties

WHERE faculty_name=?

LIMIT 1

");

$check->bind_param("s",$faculty_name);

$check->execute();

$result=$check->get_result();

if($result->num_rows>0){

$message="<div class='alert alert-danger'>Faculty already exists.</div>";

}else{

$insert=$conn->prepare("

INSERT INTO faculties

(faculty_name,status)

VALUES

(?,?)

");

$insert->bind_param("ss",$faculty_name,$status);

if($insert->execute()){

header("Location: manage_faculties.php");

exit;

}else{

$message="<div class='alert alert-danger'>Unable to save faculty.</div>";

}

}

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Add Faculty</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

</head>

<body style="background:#f4f6f9;">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-plus-circle"></i>

Add New Faculty

</h4>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Faculty Name

</label>

<input

type="text"

name="faculty_name"

class="form-control"

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

<a href="manage_faculties.php"

class="btn btn-secondary">

Back

</a>

<button

type="submit"

name="save"

class="btn btn-success">

<i class="fas fa-save"></i>

Save Faculty

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