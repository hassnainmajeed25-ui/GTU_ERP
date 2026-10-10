<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(isset($_SESSION['student_id'])){
    header("Location: dashboard.php");
    exit;
}

$message="";
$type="";

if(isset($_POST['login'])){

    $email=trim($_POST['email']);
    $password=$_POST['password'];

    $stmt=$conn->prepare("SELECT * FROM students WHERE email=? LIMIT 1");
    $stmt->bind_param("s",$email);
    $stmt->execute();

    $result=$stmt->get_result();

    if($result->num_rows==1){

        $student=$result->fetch_assoc();

        if(password_verify($password,$student['password'])){
            $_SESSION['student_id']=$student['id'];
            $_SESSION['student_name']=$student['first_name']." ".$student['last_name'];
            $_SESSION['student_email']=$student['email'];

            header("Location: dashboard.php");
            exit;

        }else{

            $message="Incorrect Password.";
            $type="danger";

        }

    }else{

        $message="Account not found.";
        $type="warning";

    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Student Login | GTU</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/style.css">

<style>

body{

background:linear-gradient(rgba(8,20,55,.90),rgba(8,20,55,.90)),
url('../assets/images/hero-building.jpg');

background-size:cover;
background-position:center;
min-height:100vh;
display:flex;
align-items:center;
justify-content:center;
padding:30px;

}

.login-card{

border:none;
border-radius:25px;
overflow:hidden;
box-shadow:0 25px 70px rgba(0,0,0,.35);

}

.login-left{

background:#0B1F52;
color:#fff;
padding:60px 40px;
height:100%;

}

.login-left img{

width:90px;
margin-bottom:20px;

}

.login-right{

padding:50px;

}

.form-control{

height:55px;
border-radius:12px;

}

.btn-login{

height:55px;
border:none;
border-radius:50px;
background:#FDB913;
font-weight:700;
width:100%;

}

.btn-login:hover{

background:#e9aa00;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-9">

<div class="card login-card">

<div class="row g-0">

<div class="col-lg-5">

<div class="login-left">

<img src="../assets/images/logo.png">

<h2>Welcome Back</h2>

<p class="mt-3">

Login to your GTU Student Portal.

</p>

<hr>

<p>

Don't have an account?

</p>

<a href="register.php" class="btn btn-light">

Create Account

</a>

</div>

</div>

<div class="col-lg-7">

<div class="login-right">

<h3 class="mb-4">

Student Login

</h3>

<?php

if(isset($_GET['registered'])){
    echo '<div class="alert alert-success">
    Account Created Successfully. Please Login.
    </div>';
}

if(isset($_GET['reset'])){
    echo '<div class="alert alert-success">
    Password changed successfully. Please Login.
    </div>';
}

if($message!=""){
    echo '<div class="alert alert-'.$type.'">'.$message.'</div>';
}

?>

<form method="POST">

<div class="mb-3">

<label>Email Address</label>

<input

type="email"

name="email"

class="form-control"

required>

</div>

<div class="mb-4">

<label>Password</label>

<input

type="password"

name="password"

class="form-control"

required>

</div>

<button

type="submit"

name="login"

class="btn-login">

<i class="fas fa-sign-in-alt me-2"></i>

Login

</button>

<div class="text-center mt-3">

    <a href="forgot_password.php" class="text-decoration-none">

        <i class="fas fa-key me-1"></i>

        Forgot Password?

    </a>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>