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

/*=========================================
GET PROCESSING FEE RECORDS
=========================================*/
$sql = "

SELECT

application_payments.*,

students.first_name,
students.last_name,

applications.application_id,

faculties.faculty_name,

programs.program_name

FROM application_payments

INNER JOIN applications
ON applications.id = application_payments.application_id

INNER JOIN students
ON students.id = applications.student_id

LEFT JOIN faculties
ON faculties.id = applications.faculty_id

LEFT JOIN programs
ON programs.id = applications.program_id

ORDER BY application_payments.created_at DESC

";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Verify Processing Fee</title>

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

.card{
border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.10);
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

<a href="verify_processing_fee.php" class="active">
<i class="fas fa-money-check-dollar me-2"></i>
Verify Processing Fee
</a>

<a href="verified_payments.php">
<i class="fas fa-circle-check me-2"></i>
Verified Payments
</a>

<a href="rejected_payments.php">
<i class="fas fa-circle-xmark me-2"></i>
Rejected Payments
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

<h3>Verify Processing Fee</h3>

<small class="text-muted">

Finance Office

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>
<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-money-check-dollar me-2"></i>

Processing Fee Verification

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Student</th>

<th>Application ID</th>

<th>Faculty</th>

<th>Program</th>

<th>Fee</th>

<th>Slip</th>

<th>Date</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if($result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td>

<strong>

<?php

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

?>

</strong>

</td>

<td>

<?php echo htmlspecialchars($row['application_id']); ?>

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

<a href="../uploads/receipts/<?php echo $row['receipt_image']; ?>"
target="_blank"
class="btn btn-primary btn-sm">

<i class="fas fa-image"></i>

View Slip

</a>

</td>

<td>

<?php
if(!empty($row['payment_date'])){
    echo date("d M Y",strtotime($row['payment_date']));
}else{
    echo date("d M Y",strtotime($row['submitted_at']));
}
?>

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

<a href="verify_fee.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm me-1">
    <i class="fas fa-check"></i> Verify
</a>

<a href="reject_fee.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Reject this payment?')">
    <i class="fas fa-times"></i> Reject
</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="10" class="text-center">

No Processing Fee Submitted Yet.

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
<div class="row mt-4">

<div class="col-md-3">

<div class="card shadow">

<div class="card-body text-center">

<i class="fas fa-users fa-3x text-primary mb-3"></i>

<h2>0</h2>

<h6>Total Applications</h6>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card shadow">

<div class="card-body text-center">

<i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>

<h2>0</h2>

<h6>Pending</h6>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card shadow">

<div class="card-body text-center">

<i class="fas fa-circle-check fa-3x text-success mb-3"></i>

<h2>0</h2>

<h6>Verified</h6>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card shadow">

<div class="card-body text-center">

<i class="fas fa-circle-xmark fa-3x text-danger mb-3"></i>

<h2>0</h2>

<h6>Rejected</h6>

</div>

</div>

</div>

</div>
<hr class="mt-5">

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>