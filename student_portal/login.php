<?php
session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if(isset($_SESSION['student_id'])){
    header("Location: dashboard.php");
    exit;
}

$error="";

if(isset($_SESSION['login_error'])){
    $error=$_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>

GTU ERP | Student Portal

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{

font-family:'Segoe UI',sans-serif;

height:100vh;

overflow:hidden;

background:
linear-gradient(
rgba(11,31,82,.75),
rgba(11,31,82,.75)
),
url("../assets/images/hero-building.jpg") center center/cover no-repeat;

position:relative;

}

body::before{

content:"";

position:absolute;

top:0;
left:0;

width:100%;
height:100%;

background:rgba(8,20,45,.35);

backdrop-filter:blur(.4px);

}

.login-wrapper{

position:relative;

z-index:10;

height:100vh;

display:flex;

justify-content:center;

align-items:center;

padding:20px;

}

.login-card{

width:470px;

background:rgba(8,28,70,.88);

backdrop-filter:blur(18px);

border:1px solid rgba(255,255,255,.10);

border-radius:22px;

padding:45px;

box-shadow:0 25px 60px rgba(0,0,0,.55);

animation:fadeUp .8s ease;

}

@keyframes fadeUp{

from{

opacity:0;

transform:translateY(50px);

}

to{

opacity:1;

transform:translateY(0);

}

}

.logo{

width:95px;

display:block;

margin:auto;

margin-bottom:18px;

}

.portal-title{

text-align:center;

color:#fff;

font-size:32px;

font-weight:bold;

margin-bottom:5px;

}

.portal-subtitle{

text-align:center;

color:#f1f1f1;

font-size:15px;

margin-bottom:35px;

}

.quote{

position:absolute;

left:40px;

bottom:130px;

max-width:470px;

z-index:5;

color:#fff;

font-size:30px;

font-weight:700;

line-height:48px;

text-shadow:0 3px 12px rgba(0,0,0,.5);

}

.quote small{

display:block;

margin-top:18px;

font-size:18px;

font-weight:400;

color:#ffc107;

}

.form-label{

color:#fff;

font-weight:600;

margin-bottom:8px;

}

.form-control{

height:55px;

border-radius:12px;

border:none;

padding-left:18px;

font-size:16px;

}

.form-control:focus{

box-shadow:0 0 0 .25rem rgba(13,110,253,.35);

}

.input-group{

position:relative;

}

.password-toggle{

position:absolute;

right:18px;

top:50%;

transform:translateY(-50%);

cursor:pointer;

font-size:18px;

color:#555;

z-index:100;

}

.login-btn{

height:55px;

border-radius:12px;

font-size:18px;

font-weight:bold;

background:linear-gradient(135deg,#0d6efd,#0b5ed7);

border:none;

transition:.3s;

}

.login-btn:hover{

transform:translateY(-2px);

box-shadow:0 8px 20px rgba(13,110,253,.45);

}

.footer-box{

position:absolute;

left:0;

bottom:0;

width:100%;

background:rgba(0,0,0,.70);

color:#fff;

padding:18px 40px;

display:flex;

justify-content:space-between;

align-items:center;

z-index:20;

}

.footer-box h6{

margin-bottom:6px;

font-weight:bold;

color:#ffc107;

}

.footer-box p{

margin:0;

font-size:13px;

line-height:22px;

}

.copy{

text-align:right;

font-size:13px;

color:#d8d8d8;

}

.alert{

border-radius:10px;

}

@media(max-width:991px){

.quote{

display:none;

}

.login-card{

width:100%;

max-width:470px;

}

.footer-box{

flex-direction:column;

text-align:center;

gap:15px;

}

.copy{

text-align:center;

}

}

</style>

</head>

<body>

<div class="quote">

"Education is the most powerful weapon which you can use to change the world."

<small>

— Nelson Mandela

</small>

</div>

<div class="login-wrapper">

<div class="login-card">

<img
src="../assets/images/logo.png"
class="logo">

<h2 class="portal-title">

GTU ERP

</h2>

<p class="portal-subtitle">

Student Portal Login

</p>

<?php if(!empty($error)){ ?>

<div class="alert alert-danger">

<i class="fas fa-circle-exclamation me-2"></i>

<?php echo $error; ?>

</div>

<?php } ?>

<form
action="authenticate.php"
method="POST">

<!-- =========================================
ROLL NUMBER
========================================= -->

<div class="mb-4">

<label class="form-label">

Roll Number

</label>

<input

type="text"

name="roll_no"

class="form-control"

placeholder="Enter Roll Number"

required>

</div>

<!-- =========================================
PASSWORD
========================================= -->

<div class="mb-4">

<label class="form-label">

CNIC (with dashes)

</label>

<div class="input-group">

<input

type="password"

name="password"

id="password"

class="form-control"

placeholder="35202-1234567-1"

required>

<span

class="password-toggle"

onclick="togglePassword()">

<i

id="eyeIcon"

class="fas fa-eye">

</i>

</span>

</div>

</div>

<!-- =========================================
LOGIN BUTTON
========================================= -->

<div class="d-grid mt-4">

<button

type="submit"

class="btn btn-primary login-btn">

<i class="fas fa-right-to-bracket me-2"></i>

Login

</button>

</div>

</form>

</div>

</div>

<!-- =========================================
FOOTER
========================================= -->

<div class="footer-box">

<div>

<h6>

Mission

</h6>

<p>

To provide quality education through innovation,

research and professional excellence.

</p>

</div>

<div>

<h6>

Vision

</h6>

<p>

To become a leading technological university

producing future leaders.

</p>

</div>

<div class="copy">

© <?php echo date("Y"); ?>

Global Tech University ERP

<br>

All Rights Reserved

</div>

</div>

<!-- =========================================
JAVASCRIPT
========================================= -->

<script>

function togglePassword(){

let password=document.getElementById("password");

let eye=document.getElementById("eyeIcon");

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

</body>

</html>