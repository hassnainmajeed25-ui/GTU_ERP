<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$sql = $conn->prepare("

SELECT

a.id,
a.application_id,
a.application_status,

s.first_name,
s.last_name,
s.email,

p.program_name,

f.faculty_name,

pay.challan_no,
pay.processing_fee,
pay.payment_status

FROM applications a

INNER JOIN students s
ON s.id=a.student_id

LEFT JOIN programs p
ON p.id=a.program_id

LEFT JOIN faculties f
ON f.id=a.faculty_id

LEFT JOIN application_payments pay
ON pay.application_id=a.id

WHERE a.student_id=?

LIMIT 1

");

$sql->bind_param("i",$student_id);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

die("Application not found.");

}

$data=$result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Online Payment | GTU</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#f4f7fb;
font-family:Arial,Helvetica,sans-serif;
}

.header{
background:#0B1F52;
color:#fff;
padding:20px;
text-align:center;
font-size:28px;
font-weight:bold;
}

.payment-card{

background:#fff;

border-radius:15px;

box-shadow:0 5px 20px rgba(0,0,0,.10);

padding:30px;

margin-top:30px;

}

.method{

border:2px solid #ddd;

border-radius:10px;

padding:20px;

margin-bottom:20px;

cursor:pointer;

transition:.3s;

}

.method:hover{

border-color:#0B1F52;

background:#f8fbff;

}

.method input{

margin-right:10px;

}

.summary{

background:#eef5ff;

padding:20px;

border-radius:10px;

}

.btn-next{

background:#198754;

color:white;

font-size:18px;

font-weight:bold;

padding:12px 35px;

}

.btn-back{

background:#6c757d;

color:white;

font-size:18px;

font-weight:bold;

padding:12px 35px;

}

</style>

</head>

<body>

<div class="header">

GLOBAL TECH UNIVERSITY

<br>

Online Admission Fee Payment

</div>

<div class="container">

<div class="row">

<div class="col-md-8">

<div class="payment-card">

<h4 class="mb-4">

Choose Payment Method

</h4>

<label class="method d-block">

<input type="radio" name="payment_method" value="jazzcash" checked>

<i class="fa-solid fa-mobile-screen-button text-success"></i>

<strong>JazzCash</strong>

<br>

<small>Secure payment using JazzCash Wallet.</small>

</label>

<label class="method d-block">

<input type="radio" name="payment_method" value="easypaisa">

<i class="fa-solid fa-wallet text-primary"></i>

<strong>Easypaisa</strong>

<br>

<small>Pay using Easypaisa Mobile Wallet.</small>

</label>

<label class="method d-block">

<input type="radio" name="payment_method" value="card">

<i class="fa-solid fa-credit-card text-dark"></i>

<strong>Debit / Credit Card</strong>

<br>

<small>Visa & Mastercard Accepted.</small>

</label>

<label class="method d-block">

<input type="radio" name="payment_method" value="bank">

<i class="fa-solid fa-building-columns text-warning"></i>

<strong>Bank Transfer</strong>

<br>

<small>Transfer to GTU Official Bank Account.</small>

</label>

</div>

</div>

<div class="col-md-4">

<div class="summary">

<h5>

Payment Summary

</h5>

<hr>

<p>

<strong>Student</strong>

<br>

<?php echo $data['first_name']." ".$data['last_name']; ?>

</p>

<p>

<strong>Application ID</strong>

<br>

<?php echo $data['application_id']; ?>

</p>

<p>

<strong>Faculty</strong>

<br>

<?php echo $data['faculty_name']; ?>

</p>

<p>

<strong>Program</strong>

<br>

<?php echo $data['program_name']; ?>

</p>

<hr>

<h4 class="text-success">

Rs.

<?php echo number_format($data['processing_fee'],2); ?>

</h4>

</div>

</div>

</div>
<hr>

<div class="d-flex justify-content-between mt-4">

<a href="fee_challan.php" class="btn btn-back">

<i class="fa-solid fa-arrow-left"></i>

Back to Fee Challan

</a>

<button
type="button"
class="btn btn-next"
onclick="continuePayment()">

<i class="fa-solid fa-lock"></i>

Continue Payment

</button>

</div>

</div>

</div>

</div>

<script>

function continuePayment(){

let method=document.querySelector('input[name="payment_method"]:checked').value;

if(method=="jazzcash"){

window.location.href="payment_success.php?method=JazzCash";

}

else if(method=="easypaisa"){

window.location.href="payment_success.php?method=Easypaisa";

}

else if(method=="card"){

window.location.href="payment_success.php?method=Card";

}

else if(method=="bank"){

window.location.href="payment_success.php?method=Bank";

}

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GTU Payment Portal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
background:#eef3f9;
font-family:Arial;
}

.header{
background:#0B1F52;
color:white;
padding:20px;
text-align:center;
font-size:26px;
font-weight:bold;
}

.card-box{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 5px 20px rgba(0,0,0,0.1);
margin-top:25px;
}

.method{
border:1px solid #ddd;
padding:15px;
border-radius:10px;
cursor:pointer;
margin-bottom:10px;
transition:0.3s;
}

.method:hover{
border-color:#0B1F52;
background:#f7fbff;
}

.summary{
background:#f1f6ff;
padding:20px;
border-radius:12px;
}

.btn-pay{
background:#198754;
color:white;
width:100%;
font-size:18px;
font-weight:bold;
}

.btn-back{
background:#6c757d;
color:white;
}
</style>
</head>

<body>

<div class="header">
GLOBAL TECH UNIVERSITY<br>
Online Fee Payment Portal
</div>

<div class="container">

<div class="row">

<!-- LEFT SIDE -->
<div class="col-md-7">

<div class="card-box">

<h4>Select Payment Method</h4>

<div class="method">
<input type="radio" name="method" value="JazzCash" checked>
💚 JazzCash
</div>

<div class="method">
<input type="radio" name="method" value="Easypaisa">
🔵 Easypaisa
</div>

<div class="method">
<input type="radio" name="method" value="Card">
💳 Debit / Credit Card
</div>

<div class="method">
<input type="radio" name="method" value="Bank">
🏦 Bank Transfer
</div>

</div>

</div>

<!-- RIGHT SIDE -->
<div class="col-md-5">

<div class="card-box summary">

<h5>Payment Summary</h5>
<hr>

<p><b>Name:</b> <?php echo $data['first_name']." ".$data['last_name']; ?></p>
<p><b>Application ID:</b> <?php echo $data['application_id']; ?></p>
<p><b>Faculty:</b> <?php echo $data['faculty_name']; ?></p>
<p><b>Program:</b> <?php echo $data['program_name']; ?></p>
<hr>

<h3 class="text-success">
Rs. <?php echo number_format($data['processing_fee'],2); ?>
</h3>

</div>

</div>

</div>

<!-- BUTTONS -->
<div class="d-flex justify-content-between mt-4">

<a href="fee_challan.php" class="btn btn-back">
⬅ Back
</a>

<button class="btn btn-pay" onclick="payNow()">
Proceed to Payment
</button>

</div>

</div>

<script>
function payNow(){

let method = document.querySelector('input[name="method"]:checked').value;

alert("Payment method selected: " + method + "\n(Next step: integration with gateway)");

window.location.href = "payment_success.php?method="+method;

}
</script>

</body>
</html><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>GTU Payment Portal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
background:#eef3f9;
font-family:Arial;
}

.header{
background:#0B1F52;
color:white;
padding:20px;
text-align:center;
font-size:26px;
font-weight:bold;
}

.card-box{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 5px 20px rgba(0,0,0,0.1);
margin-top:25px;
}

.method{
border:1px solid #ddd;
padding:15px;
border-radius:10px;
cursor:pointer;
margin-bottom:10px;
transition:0.3s;
}

.method:hover{
border-color:#0B1F52;
background:#f7fbff;
}

.summary{
background:#f1f6ff;
padding:20px;
border-radius:12px;
}

.btn-pay{
background:#198754;
color:white;
width:100%;
font-size:18px;
font-weight:bold;
}

.btn-back{
background:#6c757d;
color:white;
}
</style>
</head>

<body>

<div class="header">
GLOBAL TECH UNIVERSITY<br>
Online Fee Payment Portal
</div>

<div class="container">

<div class="row">

<!-- LEFT SIDE -->
<div class="col-md-7">

<div class="card-box">

<h4>Select Payment Method</h4>

<div class="method">
<input type="radio" name="method" value="JazzCash" checked>
💚 JazzCash
</div>

<div class="method">
<input type="radio" name="method" value="Easypaisa">
🔵 Easypaisa
</div>

<div class="method">
<input type="radio" name="method" value="Card">
💳 Debit / Credit Card
</div>

<div class="method">
<input type="radio" name="method" value="Bank">
🏦 Bank Transfer
</div>

</div>

</div>

<!-- RIGHT SIDE -->
<div class="col-md-5">

<div class="card-box summary">

<h5>Payment Summary</h5>
<hr>

<p><b>Name:</b> <?php echo $data['first_name']." ".$data['last_name']; ?></p>
<p><b>Application ID:</b> <?php echo $data['application_id']; ?></p>
<p><b>Faculty:</b> <?php echo $data['faculty_name']; ?></p>
<p><b>Program:</b> <?php echo $data['program_name']; ?></p>
<hr>

<h3 class="text-success">
Rs. <?php echo number_format($data['processing_fee'],2); ?>
</h3>

</div>

</div>

</div>

<!-- BUTTONS -->
<div class="d-flex justify-content-between mt-4">

<a href="fee_challan.php" class="btn btn-back">
⬅ Back
</a>

<button class="btn btn-pay" onclick="payNow()">
Proceed to Payment
</button>

</div>

</div>

<script>
function payNow(){

let method = document.querySelector('input[name="method"]:checked').value;

alert("Payment method selected: " + method + "\n(Next step: integration with gateway)");

window.location.href = "payment_success.php?method="+method;

}
</script>

</body>
</html>
</html>