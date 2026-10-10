<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location: ../admins/login.php");
    exit;

}

if($_SESSION['department']!="Finance Office"){

    header("Location: ../admins/login.php");
    exit;

}

$admin_id=$_SESSION['admin_id'];
$admin_name=$_SESSION['admin_name'];

/*=========================================
PAGE TITLE
=========================================*/

$page_title="Summer Fee Verification";

/*=========================================
COUNT PENDING
=========================================*/

$pending=$conn->query("

SELECT COUNT(*) total

FROM summer_fee_payments

WHERE payment_status='Pending'

")->fetch_assoc()['total'];

/*=========================================
COUNT APPROVED
=========================================*/

$approved=$conn->query("

SELECT COUNT(*) total

FROM summer_fee_payments

WHERE payment_status='Approved'

")->fetch_assoc()['total'];

/*=========================================
COUNT REJECTED
=========================================*/

$rejected=$conn->query("

SELECT COUNT(*) total

FROM summer_fee_payments

WHERE payment_status='Rejected'

")->fetch_assoc()['total'];

/*=========================================
TODAY PAYMENTS
=========================================*/

$today=$conn->query("

SELECT COUNT(*) total

FROM summer_fee_payments

WHERE DATE(created_at)=CURDATE()

")->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1.0">

<title>

Summer Fee Verification

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#F4F4F4;

font-family:'Segoe UI',sans-serif;

}

/*==========================
HEADER
==========================*/

.header-card{

background:linear-gradient(135deg,#000000,#B8860B,#C0C0C0);

color:white;

padding:30px;

border-radius:18px;

margin-bottom:25px;

box-shadow:0 10px 25px rgba(0,0,0,.20);

}

/*==========================
CARDS
==========================*/

.dashboard-card{

border:none;

border-radius:18px;

overflow:hidden;

box-shadow:0 5px 15px rgba(0,0,0,.12);

}

.dashboard-card .card-body{

color:white;

text-align:center;

padding:25px;

}

.gold{

background:#B8860B;

}

.silver{

background:#808080;

}

.black{

background:#212121;

}

.darkgold{

background:#8B7500;

}

.dashboard-card i{

font-size:45px;

margin-bottom:15px;

}

.dashboard-card h2{

font-weight:bold;

margin-top:10px;

}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="header-card">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2 class="fw-bold">

<i class="fas fa-money-check-dollar"></i>

Summer Fee Verification

</h2>

<p class="mb-0">

Finance Office Control Panel

</p>

</div>

<div>

<i class="fas fa-building-columns"

style="font-size:70px;opacity:.25;"></i>

</div>

</div>

</div>

<div class="row">

<div class="col-lg-3 col-md-6 mb-4">

<div class="dashboard-card">

<div class="card-body gold">

<i class="fas fa-hourglass-half"></i>

<h5>Pending</h5>

<h2>

<?php echo $pending; ?>

</h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="dashboard-card">

<div class="card-body silver">

<i class="fas fa-circle-check"></i>

<h5>Approved</h5>

<h2>

<?php echo $approved; ?>

</h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="dashboard-card">

<div class="card-body black">

<i class="fas fa-circle-xmark"></i>

<h5>Rejected</h5>

<h2>

<?php echo $rejected; ?>

</h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="dashboard-card">

<div class="card-body darkgold">

<i class="fas fa-calendar-day"></i>

<h5>Today</h5>

<h2>

<?php echo $today; ?>

</h2>

</div>

</div>

</div>

</div>

<!-- =========================================
PAYMENT REQUESTS TABLE
========================================= -->

<div class="card border-0 shadow-lg">

<div class="card-header"

style="background:#000;color:#FFD700;">

<div class="d-flex justify-content-between align-items-center">

<h4 class="mb-0">

<i class="fas fa-list-check me-2"></i>

Summer Fee Payment Requests

</h4>

<input

type="text"

id="searchBox"

class="form-control"

placeholder="Search Student..."

style="width:280px;">

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table

class="table table-bordered table-hover align-middle"

id="paymentTable">

<thead

style="background:#B8860B;color:white;">

<tr>

<th width="60">#</th>

<th>Student</th>

<th>Registration No</th>

<th>Program</th>

<th>Total Subjects</th>

<th>Total Fee</th>

<th>Status</th>

<th width="120">Action</th>

</tr>

</thead>

<tbody>

<?php

$list = $conn->query("

SELECT

summer_fee_payments.*,

students.first_name,
students.last_name,
students.registration_no,

programs.program_name

FROM summer_fee_payments

INNER JOIN students
ON summer_fee_payments.student_id = students.id

INNER JOIN programs
ON students.program_id = programs.id

ORDER BY summer_fee_payments.created_at DESC

");

$sr=1;

while($row=$list->fetch_assoc()){

$statusColor="warning";

if($row['payment_status']=="Approved"){

$statusColor="success";

}

if($row['payment_status']=="Rejected"){

$statusColor="danger";

}

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<strong>

<?php

echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);

?>

</strong>

</td>

<td>

<?php echo htmlspecialchars($row['registration_no']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<?php echo $row['total_subjects']; ?>

</td>

<td>

<strong class="text-primary">

Rs.

<?php echo number_format($row['total_fee']); ?>

</strong>

</td>

<td>

<span class="badge bg-<?php echo $statusColor; ?>">

<?php echo $row['payment_status']; ?>

</span>

</td>

<td>

<a

href="view_summer_fee.php?id=<?php echo $row['id']; ?>"

class="btn btn-dark btn-sm">

<i class="fas fa-eye"></i>

View

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

<!-- =========================================
SEARCH SCRIPT
========================================= -->

<script>

const searchBox = document.getElementById("searchBox");

searchBox.addEventListener("keyup", function () {

    let value = this.value.toLowerCase();

    let table = document.getElementById("paymentTable");

    let rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {

        let text = rows[i].innerText.toLowerCase();

        if (text.indexOf(value) > -1) {

            rows[i].style.display = "";

        } else {

            rows[i].style.display = "none";

        }

    }

});

</script>

<!-- =========================================
FOOTER
========================================= -->

<footer class="mt-5">

<div class="card border-0 shadow">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-6">

<h5 class="fw-bold text-dark">

Global Tech University ERP

</h5>

<p class="text-muted mb-0">

Finance Office - Summer Fee Verification

</p>

</div>

<div class="col-md-6 text-end">

<strong>

Version 1.0

</strong>

<br>

<span class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University

</span>

</div>

</div>

</div>

</div>

</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>