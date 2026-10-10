<?php

require_once '../config/config.php';
require_once '../config/database.php';

// Agar pehle se login hai to uske dashboard par bhejo

if(isset($_SESSION['super_admin_id'])){

    header("Location: ../super_admin/dashboard.php");
    exit;

}

if(isset($_SESSION['admin_id'])){

    switch($_SESSION['department']){

        case "Admission Office":
            header("Location: ../admission/dashboard.php");
            break;

        case "Finance Office":
            header("Location: ../finance/dashboard.php");
            break;

        case "Examination Office":
            header("Location: ../examination/dashboard.php");
            break;

        case "Student Affairs":
            header("Location: ../student_affairs/dashboard.php");
            break;
            
        case "Academic Office":
header("Location: ../academic_office/dashboard.php");
break;

        default:
            session_destroy();
            header("Location: login.php");
            break;
    }

    exit;
}

$message="";

if(isset($_POST['login'])){

$email=trim($_POST['email']);

$password=trim($_POST['password']);

/*=================================
CHECK SUPER ADMIN
=================================*/

$sql=$conn->prepare("

SELECT *

FROM super_admin

WHERE email=?

AND status='Active'

LIMIT 1

");

$sql->bind_param("s",$email);

$sql->execute();

$result=$sql->get_result();
/*=================================
SUPER ADMIN LOGIN
=================================*/

if($result->num_rows==1){

$admin=$result->fetch_assoc();

if($password==$admin['password']){

$_SESSION['super_admin_id']=$admin['id'];

$_SESSION['super_admin_name']=$admin['full_name'];

$_SESSION['admin_role']="Super Admin";

header("Location: ../super_admin/dashboard.php");

exit;

}

}

/*=================================
CHECK DEPARTMENT ADMINS
=================================*/

$sql=$conn->prepare("

SELECT *

FROM admins

WHERE email=?

AND status='Active'

LIMIT 1

");

$sql->bind_param("s",$email);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==1){

$admin=$result->fetch_assoc();

if($password==$admin['password']){

$_SESSION['admin_id']=$admin['id'];

$_SESSION['admin_name']=$admin['full_name'];

$_SESSION['department']=$admin['department'];

/*=================================
REDIRECT BY DEPARTMENT
=================================*/

switch($admin['department']){

case "Admission Office":

header("Location: ../admission/dashboard.php");

break;

case "Finance Office":

header("Location: ../finance/dashboard.php");

break;

case "Examination Office":

header("Location: ../examination/dashboard.php");

break;

case "Student Affairs":

header("Location: ../student_affairs/dashboard.php");

break;

case "Academic Office":

case "Academic Office":

header("Location: ../academic_office/dashboard.php");

break;


default:

header("Location: ../admin/login.php");

}

exit;

}

}

$message="Invalid Email or Password.";

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>GTU Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#edf2f7;

font-family:Arial,Helvetica,sans-serif;

}

.login-box{

width:460px;

margin:60px auto;

background:#fff;

border-radius:15px;

box-shadow:0 5px 25px rgba(0,0,0,.12);

padding:35px;

}

.logo{

width:90px;

margin-bottom:15px;

}

.login-title{

font-size:28px;

font-weight:bold;

color:#0B1F52;

}

.login-sub{

color:#666;

margin-bottom:25px;

}

.btn-login{

background:#0B1F52;

color:#fff;

font-weight:bold;

}

.btn-login:hover{

background:#173a8b;

color:#fff;

}

</style>

</head>

<body>

<div class="login-box text-center">

<img src="../assets/images/logo.png" class="logo">

<h2 class="login-title">

Global Tech University

</h2>

<p class="login-sub">

Administration Login Portal

</p>

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

<div class="mb-3 text-start">

<label class="form-label">

Email Address

</label>

<input

type="email"

name="email"

class="form-control"

required>

</div>

<div class="mb-3 text-start">

<label class="form-label">

Password

</label>

<div class="input-group">

<input

type="password"

name="password"

id="password"

class="form-control"

required>

<button

type="button"

class="btn btn-outline-secondary"

onclick="togglePassword()">

<i

id="eye"

class="fa-solid fa-eye">

</i>

</button>

</div>

</div>
<div class="form-check text-start mb-3">

<input
class="form-check-input"
type="checkbox"
id="remember">

<label
class="form-check-label"
for="remember">

Remember Me

</label>

</div>

<button
type="submit"
name="login"
class="btn btn-login w-100">

<i class="fas fa-right-to-bracket me-2"></i>

Login

</button>

</form>

<hr>

<p class="text-muted mb-1">

Global Tech University ERP System

</p>

<p class="text-muted">

© <?php echo date("Y"); ?>

All Rights Reserved

</p>

</div>

<script>

function togglePassword(){

var password=document.getElementById("password");

var eye=document.getElementById("eye");

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