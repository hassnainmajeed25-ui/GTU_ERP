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

$admin_id=$_SESSION['admin_id'];
$admin_name=$_SESSION['admin_name'];

/*====================================================
DASHBOARD LIVE COUNTS
====================================================*/

// Total Applications
$totalStudents=0;
$q=$conn->query("SELECT COUNT(*) total FROM applications");
if($q){
    $totalStudents=$q->fetch_assoc()['total'];
}

// Pending Verification
$pendingVerification=0;
$q=$conn->query("SELECT COUNT(*) total
FROM application_payments
WHERE verification_status='Pending'");
if($q){
    $pendingVerification=$q->fetch_assoc()['total'];
}

// Verified Payments
$verifiedPayments=0;
$q=$conn->query("SELECT COUNT(*) total
FROM application_payments
WHERE verification_status='Verified'");
if($q){
    $verifiedPayments=$q->fetch_assoc()['total'];
}

// Rejected Payments
$rejectedPayments=0;
$q=$conn->query("SELECT COUNT(*) total
FROM application_payments
WHERE verification_status='Rejected'");
if($q){
    $rejectedPayments=$q->fetch_assoc()['total'];
}

// Today's Collection
$todayCollection=0;
$q=$conn->query("
SELECT IFNULL(SUM(processing_fee),0) total
FROM application_payments
WHERE verification_status='Verified'
AND DATE(verified_at)=CURDATE()
");
if($q){
    $todayCollection=$q->fetch_assoc()['total'];
}

// Monthly Collection
$monthlyCollection=0;
$q=$conn->query("
SELECT IFNULL(SUM(processing_fee),0) total
FROM application_payments
WHERE verification_status='Verified'
AND MONTH(verified_at)=MONTH(CURDATE())
AND YEAR(verified_at)=YEAR(CURDATE())
");
if($q){
    $monthlyCollection=$q->fetch_assoc()['total'];
}

// Unread Messages
$unreadMessages=0;

if($conn->query("SHOW TABLES LIKE 'finance_messages'")->num_rows>0){

$q=$conn->query("
SELECT COUNT(*) total
FROM finance_messages
WHERE is_read=0
");

if($q){
$unreadMessages=$q->fetch_assoc()['total'];
}

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Finance Office Dashboard</title>

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
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 3px 10px rgba(0,0,0,.10);
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.dashboard-card{
border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.10);
transition:.3s;
}

.dashboard-card:hover{
transform:translateY(-5px);
}

.card-icon{
font-size:45px;
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

<a href="dashboard.php" class="active">
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

<a href="semester_fee.php">

<i class="fas fa-money-check-dollar me-2"></i>

Semester Fee
</a>

<a href="semester_fee_verification.php">

<i class="fas fa-money-check-dollar me-2"></i>

Semester Verify Fee
</a>

<a href="summer_fee_verification.php">
    <i class="fas fa-sun me-2"></i>
    Summer Fee 
</a>

<a href="reports.php">
<i class="fas fa-chart-column me-2"></i>
Reports
</a>

<a href="compose_message.php">
<i class="fas fa-paper-plane me-2"></i>
Compose Message
</a>

<a href="inbox.php">
<i class="fas fa-envelope me-2"></i>
Admission Inbox
</a>

<a href="student_inbox.php">
<i class="fas fa-envelope me-2"></i>
Student Message
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

<h3>Finance Office Dashboard</h3>

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

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-primary">
<i class="fas fa-users"></i>
</div>

<h2><?php echo $totalStudents; ?></h2>

<h6>Total Applications</h6>

</div>

</div>

</div>


<div class="col-md-3 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-warning">
<i class="fas fa-money-check-dollar"></i>
</div>

<h2><?php echo $pendingVerification; ?></h2>

<h6>Pending Verification</h6>

</div>

</div>

</div>


<div class="col-md-3 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-success">
<i class="fas fa-circle-check"></i>
</div>

<h2><?php echo $verifiedPayments; ?></h2>

<h6>Verified Payments</h6>

</div>

</div>

</div>


<div class="col-md-3 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-danger">
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

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-info">
<i class="fas fa-calendar-day"></i>
</div>

<h2>Rs. <?php echo number_format($todayCollection); ?></h2>

<h6>Today's Collection</h6>

</div>

</div>

</div>



<div class="col-md-4 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-success">
<i class="fas fa-calendar-alt"></i>
</div>

<h2>Rs. <?php echo number_format($monthlyCollection); ?></h2>

<h6>Monthly Collection</h6>

</div>

</div>

</div>



<div class="col-md-4 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-primary">
<i class="fas fa-envelope"></i>
</div>

<h2><?php echo $unreadMessages; ?></h2>

<h6>Unread Messages</h6>

</div>

</div>

</div>

</div>

<?php

$recent=$conn->query("

SELECT

students.first_name,
students.last_name,

applications.application_id,

application_payments.processing_fee,
application_payments.verification_status,
application_payments.verified_at

FROM application_payments

INNER JOIN applications
ON applications.id=application_payments.application_id

INNER JOIN students
ON students.id=applications.student_id

ORDER BY application_payments.id DESC

LIMIT 5

");

?>
<div class="card dashboard-card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-clock me-2"></i>

Recent Processing Fee Payments

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Student</th>

<th>Application ID</th>

<th>Amount</th>

<th>Status</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php

if($recent && $recent->num_rows>0){

$sr=1;

while($row=$recent->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td>
<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['application_id']); ?>
</td>

<td>
Rs. <?php echo number_format($row['processing_fee'],2); ?>
</td>

<td>

<?php

if($row['verification_status']=="Pending"){

echo '<span class="badge bg-warning">Pending</span>';

}elseif($row['verification_status']=="Verified"){

echo '<span class="badge bg-success">Verified</span>';

}else{

echo '<span class="badge bg-danger">Rejected</span>';

}

?>

</td>

<td>

<?php

if(!empty($row['verified_at'])){

echo date("d M Y",strtotime($row['verified_at']));

}else{

echo "-";

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center text-muted">

No Processing Fee Records Found.

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