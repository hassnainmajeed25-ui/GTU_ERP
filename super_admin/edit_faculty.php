<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

if(!isset($_GET['id'])){

header("Location: manage_faculties.php");
exit;

}

$id=(int)$_GET['id'];

$sql=$conn->prepare("

SELECT *

FROM faculties

WHERE id=?

LIMIT 1

");

$sql->bind_param("i",$id);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

header("Location: manage_faculties.php");
exit;

}

$row=$result->fetch_assoc();

$message="";

if(isset($_POST['update'])){

$faculty_name=trim($_POST['faculty_name']);

$status=$_POST['status'];

$update=$conn->prepare("

UPDATE faculties

SET faculty_name=?,

status=?

WHERE id=?

");

$update->bind_param(

"ssi",

$faculty_name,

$status,

$id

);

if($update->execute()){

header("Location: manage_faculties.php");

exit;

}else{

$message="<div class='alert alert-danger'>Unable to update faculty.</div>";

}

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Edit Faculty</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

</head>

<body style="background:#f4f6f9;">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="fas fa-edit"></i>

Edit Faculty

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

value="<?php echo htmlspecialchars($row['faculty_name']); ?>"

required>

</div>

<div class="mb-3">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select">

<option value="Active"

<?php if($row['status']=="Active") echo "selected"; ?>>

Active

</option>

<option value="Inactive"

<?php if($row['status']=="Inactive") echo "selected"; ?>>

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

name="update"

class="btn btn-primary">

<i class="fas fa-save"></i>

Update Faculty

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