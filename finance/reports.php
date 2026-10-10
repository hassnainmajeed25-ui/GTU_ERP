<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Finance Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];
/*==============================
TOTAL APPLICATIONS
==============================*/

$totalApplications=$conn->query("
SELECT COUNT(*) total
FROM applications
")->fetch_assoc()['total'];


/*==============================
PENDING PAYMENTS
==============================*/

$pendingPayments=$conn->query("
SELECT COUNT(*) total
FROM application_payments
WHERE verification_status='Pending'
")->fetch_assoc()['total'];


/*==============================
VERIFIED PAYMENTS
==============================*/

$verifiedPayments=$conn->query("
SELECT COUNT(*) total
FROM application_payments
WHERE verification_status='Verified'
")->fetch_assoc()['total'];


/*==============================
REJECTED PAYMENTS
==============================*/

$rejectedPayments=$conn->query("
SELECT COUNT(*) total
FROM application_payments
WHERE verification_status='Rejected'
")->fetch_assoc()['total'];


/*==============================
TODAY COLLECTION
==============================*/

$todayCollection=$conn->query("
SELECT IFNULL(SUM(processing_fee),0) total
FROM application_payments
WHERE verification_status='Verified'
AND DATE(verified_at)=CURDATE()
")->fetch_assoc()['total'];


/*==============================
MONTH COLLECTION
==============================*/

$monthlyCollection=$conn->query("
SELECT IFNULL(SUM(processing_fee),0) total
FROM application_payments
WHERE verification_status='Verified'
AND MONTH(verified_at)=MONTH(CURDATE())
AND YEAR(verified_at)=YEAR(CURDATE())
")->fetch_assoc()['total'];


/*==============================
TOTAL COLLECTION
==============================*/

$totalCollection=$conn->query("
SELECT IFNULL(SUM(processing_fee),0) total
FROM application_payments
WHERE verification_status='Verified'
")->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Finance Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#eef2f7;
font-family:Arial,Helvetica,sans-serif;
}

.sidebar{
width:260px;
height:100vh;
background:#0B1F52;
position:fixed;
left:0;
top:0;
overflow:auto;
}

.sidebar h3{
color:#fff;
text-align:center;
padding:20px;
border-bottom:1px solid rgba(255,255,255,.15);
}

.sidebar a{
display:block;
padding:14px 20px;
color:#fff;
text-decoration:none;
color:white;
text-decoration:none;
transition:.3s;
}

.sidebar a:hover{
background:#173a8b;
}

.sidebar a.active{
background:#2563eb;
}

.main{
margin-left:260px;
padding:30px;
}

.topbar{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 3px 10px rgba(0,0,0,.1);
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.1);
transition:.3s;
}

.card:hover{
transform:translateY(-5px);
}

.icon{
font-size:42px;
margin-bottom:15px;
}

.footer{
margin-top:40px;
text-align:center;
color:#666;
}

</style>

</head>

<body>
<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php">
<i class="fas fa-home me-2"></i>
Dashboard
</a>

<a href="pending_payments.php">
<i class="fas fa-hourglass-half me-2"></i>
Pending Payments
</a>

<a href="verified_payments.php">
<i class="fas fa-circle-check me-2"></i>
Verified Payments
</a>

<a href="rejected_payments.php">
<i class="fas fa-circle-xmark me-2"></i>
Rejected Payments
</a>

<a href="reports.php" class="active">
<i class="fas fa-chart-column me-2"></i>
Reports
</a>

<a href="compose_message.php">
<i class="fas fa-paper-plane me-2"></i>
Compose Message
</a>

<a href="inbox.php">
<i class="fas fa-envelope me-2"></i>
Inbox
</a>

<a href="profile.php">
<i class="fas fa-user-circle me-2"></i>
Profile
</a>

<a href="../admins/logout.php">
<i class="fas fa-right-from-bracket me-2"></i>
Logout
</a>

</div>
<div class="main">

<div class="topbar">

<div>

<h3>Finance Reports</h3>

<small class="text-muted">

Global Tech University ERP

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>
<div class="row">

<div class="col-md-3 mb-4">

<div class="card text-center">

<div class="card-body">

<div class="icon text-primary">

<i class="fas fa-users"></i>

</div>

<h2><?php echo $totalApplications; ?></h2>

<h6>Total Applications</h6>

</div>

</div>

</div>



<div class="col-md-3 mb-4">

<div class="card text-center">

<div class="card-body">

<div class="icon text-warning">

<i class="fas fa-hourglass-half"></i>

</div>

<h2><?php echo $pendingPayments; ?></h2>

<h6>Pending Payments</h6>

</div>

</div>

</div>



<div class="col-md-3 mb-4">

<div class="card text-center">

<div class="card-body">

<div class="icon text-success">

<i class="fas fa-circle-check"></i>

</div>

<h2><?php echo $verifiedPayments; ?></h2>

<h6>Verified Payments</h6>

</div>

</div>

</div>



<div class="col-md-3 mb-4">

<div class="card text-center">

<div class="card-body">

<div class="icon text-danger">

<i class="fas fa-circle-xmark"></i>

</div>

<h2><?php echo $rejectedPayments; ?></h2>

<h6>Rejected Payments</h6>

</div>

</div>

</div>

</div>



<div class="row">

<div class="col-md-4 mb-4">

<div class="card text-center">

<div class="card-body">

<div class="icon text-info">

<i class="fas fa-calendar-day"></i>

</div>

<h3>Rs. <?php echo number_format($todayCollection); ?></h3>

Today's Collection

</div>

</div>

</div>



<div class="col-md-4 mb-4">

<div class="card text-center">

<div class="card-body">

<div class="icon text-success">

<i class="fas fa-calendar-alt"></i>

</div>

<h3>Rs. <?php echo number_format($monthlyCollection); ?></h3>

Monthly Collection

</div>

</div>

</div>



<div class="col-md-4 mb-4">

<div class="card text-center">

<div class="card-body">

<div class="icon text-primary">

<i class="fas fa-money-bill-wave"></i>

</div>

<h3>Rs. <?php echo number_format($totalCollection); ?></h3>

Total Collection

</div>

</div>

</div>

</div>
<div class="card mt-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-file-invoice-dollar me-2"></i>

Complete Payment Report

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Application ID</th>

<th>Student</th>

<th>Faculty</th>

<th>Program</th>

<th>Amount</th>

<th>Payment Date</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$sql="

SELECT

application_payments.processing_fee,
application_payments.payment_date,
application_payments.verification_status,

applications.application_id,

students.first_name,
students.last_name,

faculties.faculty_name,

programs.program_name

FROM application_payments

INNER JOIN applications
ON applications.id=application_payments.application_id

INNER JOIN students
ON students.id=applications.student_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

ORDER BY application_payments.created_at DESC

";

$result=$conn->query($sql);

if($result && $result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td>

<?php echo htmlspecialchars($row['application_id']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

Rs. <?php echo number_format($row['processing_fee'],2); ?>

</td>

<td>

<?php

if(!empty($row['payment_date'])){

echo date("d M Y",strtotime($row['payment_date']));

}else{

echo "-";

}

?>

</td>

<td>

<?php

if($row['verification_status']=="Verified"){

echo '<span class="badge bg-success">Verified</span>';

}elseif($row['verification_status']=="Rejected"){

echo '<span class="badge bg-danger">Rejected</span>';

}else{

echo '<span class="badge bg-warning text-dark">Pending</span>';

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8" class="text-center text-danger">

No Payment Records Found.

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
<div class="mt-4 text-end">

<button
class="btn btn-success"
onclick="window.print()">

<i class="fas fa-print me-2"></i>

Print Report

</button>

<a
href="export_excel.php"
class="btn btn-primary ms-2">

<i class="fas fa-file-excel me-2"></i>

Export Excel

</a>

<a
href="export_report.php"
target="_blank"
class="btn btn-danger ms-2">

<i class="fas fa-file-pdf me-2"></i>

Export PDF

</a>

</div>
<hr class="mt-5">

<div class="footer">

<p>

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>