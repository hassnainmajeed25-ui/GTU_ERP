<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: manage_programs.php");
    exit;
}

$id=(int)$_GET['id'];

$faculties=$conn->query("
SELECT *
FROM faculties
WHERE status='Active'
ORDER BY faculty_name ASC
");

$get=$conn->prepare("
SELECT *
FROM programs
WHERE id=?
LIMIT 1
");

$get->bind_param("i",$id);
$get->execute();

$result=$get->get_result();

if($result->num_rows==0){

header("Location: manage_programs.php");
exit;

}

$row=$result->fetch_assoc();

$message="";

if(isset($_POST['update'])){

$faculty_id=$_POST['faculty_id'];

$program_name=trim($_POST['program_name']);

$program_code=trim($_POST['program_code']);

$duration=trim($_POST['duration']);

$status=$_POST['status'];

$update=$conn->prepare("

UPDATE programs

SET

faculty_id=?,

program_name=?,

program_code=?,

duration=?,

status=?

WHERE id=?

");

$update->bind_param(

"issssi",

$faculty_id,

$program_name,

$program_code,

$duration,

$status,

$id

);

if($update->execute()){

header("Location: manage_programs.php");

exit;

}else{

$message="<div class='alert alert-danger'>Update Failed.</div>";

}

}

?>
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Edit Program</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

</head>

<body style="background:#f4f6f9;">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="fas fa-edit me-2"></i>

Edit Program

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

<?php while($fac=$faculties->fetch_assoc()){ ?>

<option

value="<?php echo $fac['id']; ?>"

<?php if($fac['id']==$row['faculty_id']) echo "selected"; ?>>

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

value="<?php echo htmlspecialchars($row['program_name']); ?>"

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

value="<?php echo htmlspecialchars($row['program_code']); ?>"

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

value="<?php echo htmlspecialchars($row['duration']); ?>"

required>

</div>

<div class="mb-3">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select">

<option value="Active" <?php if($row['status']=="Active") echo "selected"; ?>>

Active

</option>

<option value="Inactive" <?php if($row['status']=="Inactive") echo "selected"; ?>>

Inactive

</option>

</select>

</div>

<div class="d-flex justify-content-between">

<a

href="manage_programs.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back

</a>

<button

type="submit"

name="update"

class="btn btn-primary">

<i class="fas fa-save"></i>

Update Program

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