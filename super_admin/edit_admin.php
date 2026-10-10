<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");

exit;

}

if(!isset($_GET['id'])){

header("Location: manage_admins.php");

exit;

}

$id=(int)$_GET['id'];

$message="";

/*==============================
GET ADMIN DATA
==============================*/

$sql=$conn->prepare("

SELECT *

FROM admins

WHERE id=?

LIMIT 1

");

$sql->bind_param("i",$id);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

die("Admin not found.");

}

$admin=$result->fetch_assoc();

/*==============================
UPDATE ADMIN
==============================*/

if(isset($_POST['update_admin'])){

$full_name=trim($_POST['full_name']);

$email=trim($_POST['email']);

$mobile=trim($_POST['mobile']);

$department=$_POST['department'];

$password=trim($_POST['password']);

$status=$_POST['status'];

$check=$conn->prepare("

SELECT id

FROM admins

WHERE email=?

AND id!=?

LIMIT 1

");

$check->bind_param("si",$email,$id);

$check->execute();

$r=$check->get_result();

if($r->num_rows>0){

$message="Email already exists.";

}else{

$update=$conn->prepare("

UPDATE admins

SET

full_name=?,

email=?,

mobile=?,

department=?,

password=?,

status=?

WHERE id=?

");

$update->bind_param(

"ssssssi",

$full_name,

$email,

$mobile,

$department,

$password,

$status,

$id

);

if($update->execute()){

header("Location: manage_admins.php?updated=1");

exit;

}else{

$message="Unable to update record.";

}

}

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.container-box{

max-width:850px;

margin:40px auto;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 20px rgba(0,0,0,.10);

}

.card-header{

background:#0B1F52;

color:#fff;

padding:18px;

font-size:24px;

font-weight:bold;

}

.btn-update{

background:#0d6efd;

color:#fff;

font-weight:bold;

}

.btn-back{

background:#6c757d;

color:#fff;

font-weight:bold;

}

</style>

</head>

<body>

<div class="container container-box">

<div class="card">

<div class="card-header">

<i class="fas fa-user-edit me-2"></i>

Edit Administrator

</div>

<div class="card-body">

<?php if($message!=""){ ?>

<div class="alert alert-danger">

<?php echo $message; ?>

</div>

<?php } ?>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Full Name

</label>

<input

type="text"

name="full_name"

class="form-control"

value="<?php echo htmlspecialchars($admin['full_name']); ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Email Address

</label>

<input

type="email"

name="email"

class="form-control"

value="<?php echo htmlspecialchars($admin['email']); ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Mobile Number

</label>

<input

type="text"

name="mobile"

class="form-control"

value="<?php echo htmlspecialchars($admin['mobile']); ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Department

</label>

<select

name="department"

class="form-select"

required>

<option value="Admission Office" <?php if($admin['department']=="Admission Office") echo "selected"; ?>>

Admission Office

</option>

<option value="Finance Office" <?php if($admin['department']=="Finance Office") echo "selected"; ?>>

Finance Office

</option>

<option value="Examination Office" <?php if($admin['department']=="Examination Office") echo "selected"; ?>>

Examination Office

</option>

<option value="Student Affairs" <?php if($admin['department']=="Student Affairs") echo "selected"; ?>>

Student Affairs

</option>

<option value="Super Admin" <?php if($admin['department']=="Super Admin") echo "selected"; ?>>

Super Admin

</option>

</select>

</div>
<div class="col-md-6 mb-3">

<label class="form-label">

Password

</label>

<div class="input-group">

<input

type="password"

name="password"

id="password"

class="form-control"

value="<?php echo htmlspecialchars($admin['password']); ?>"

required>

<button

type="button"

class="btn btn-outline-secondary"

onclick="togglePassword()">

<i

id="eye"

class="fas fa-eye">

</i>

</button>

</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select"

required>

<option value="Active"

<?php if($admin['status']=="Active") echo "selected"; ?>>

Active

</option>

<option value="Inactive"

<?php if($admin['status']=="Inactive") echo "selected"; ?>>

Inactive

</option>

</select>

</div>

</div>

<hr>

<div class="d-flex justify-content-between">

<a

href="manage_admins.php"

class="btn btn-back">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="update_admin"

class="btn btn-update">

<i class="fas fa-save me-2"></i>

Update Admin

</button>

</div>

</form>

</div>

</div>

</div>

<script>

function togglePassword(){

let password=document.getElementById("password");

let eye=document.getElementById("eye");

if(password.type==="password"){

password.type="text";

eye.classList.remove("fa-eye");

eye.classList.add("fa-eye-slash");

}else{

password.type="password";

eye.classList.remove("fa-eye-slash");

eye.classList.add("fa-eye");

}

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>