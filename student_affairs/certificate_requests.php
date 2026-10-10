<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
header("Location: ../admins/login.php");
exit;
}

if($_SESSION['department']!="Student Affairs"){
header("Location: ../admins/login.php");
exit;
}

$admin_id=$_SESSION['admin_id'];

$admin_name=$_SESSION['admin_name'];

/*=========================================
GET ALL REQUESTS
=========================================*/

$sql="

SELECT

certificate_requests.*,

students.first_name,

students.last_name,

students.email,

students.mobile

FROM certificate_requests

LEFT JOIN students

ON students.id=certificate_requests.student_id

ORDER BY certificate_requests.id DESC

";

$result=$conn->query($sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Certificate Requests</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef4fb;

font-family:Arial,Helvetica,sans-serif;

}

.sidebar{

width:260px;

height:100vh;

position:fixed;

left:0;

top:0;

overflow-y:auto;

background:linear-gradient(180deg,#0B1F52,#16367d);

}

.sidebar h3{

color:#fff;

text-align:center;

padding:20px;

border-bottom:1px solid rgba(255,255,255,.2);

}

.sidebar a{

display:block;

padding:15px 20px;

color:#fff;

text-decoration:none;

transition:.3s;

}

.sidebar a:hover{

background:#2563eb;

padding-left:28px;

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

padding:18px 25px;

border-radius:15px;

box-shadow:0 4px 12px rgba(0,0,0,.08);

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:25px;

}

.logo{

width:55px;

height:55px;

margin-right:15px;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 4px 12px rgba(0,0,0,.08);

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

<a href="manage_students.php">
<i class="fas fa-user-graduate me-2"></i>
Manage Students
</a>

<a href="student_requests.php">
<i class="fas fa-file-signature me-2"></i>
Student Requests
</a>

<a href="certificate_requests.php" class="active">
<i class="fas fa-certificate me-2"></i>
Certificate Requests
</a>

<a href="complaints.php">
<i class="fas fa-triangle-exclamation me-2"></i>
Complaints
</a>

<a href="announcements.php">
<i class="fas fa-bullhorn me-2"></i>
Announcements
</a>

<a href="reports.php">
<i class="fas fa-chart-bar me-2"></i>
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

<a href="sent_messages.php">
<i class="fas fa-share me-2"></i>
Sent Messages
</a>

<a href="profile.php">
<i class="fas fa-user-circle me-2"></i>
Profile
</a>

<a href="../admins/logout.php">
<i class="fas fa-sign-out-alt me-2"></i>
Logout
</a>

</div>

<div class="main">

<div class="topbar">

<div class="d-flex align-items-center">

<img

src="../assets/images/logo.png"

class="logo"

alt="GTU Logo">

<div>

<h3 class="mb-0">

Certificate Requests

</h3>

<small class="text-muted">

Student Affairs Office

</small>

</div>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>

<div class="card">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="fas fa-certificate me-2"></i>

Certificate Requests

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover table-bordered align-middle">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Request No</th>

<th>Student</th>

<th>Certificate</th>

<th>Status</th>

<th>Date</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if($result && $result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['request_no']); ?>

</strong>

</td>

<td>

<strong>

<?php

echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);

?>

</strong>

<br>

<small class="text-muted">

<?php echo htmlspecialchars($row['email']); ?>

</small>

</td>

<td>

<?php echo htmlspecialchars($row['certificate_type']); ?>

</td>

<td>

<?php

if($row['status']=="Pending"){

echo "<span class='badge bg-warning text-dark'>Pending</span>";

}elseif($row['status']=="Approved"){

echo "<span class='badge bg-success'>Approved</span>";

}elseif($row['status']=="Rejected"){

echo "<span class='badge bg-danger'>Rejected</span>";

}else{

echo "<span class='badge bg-primary'>Completed</span>";

}

?>

</td>

<td>

<?php

echo date(

"d M Y",

strtotime($row['created_at'])

);

?>

</td>

<td>

<a

href="view_certificate_request.php?id=<?php echo $row['id'];?>"

class="btn btn-primary btn-sm">

<i class="fas fa-eye"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center text-danger">

<i class="fas fa-folder-open fa-3x mb-3"></i>

<br>

No Certificate Requests Found.

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

<?php

/*=========================================
TOTAL REQUESTS
=========================================*/

$total=0;

$q1=$conn->query("

SELECT COUNT(*) total

FROM certificate_requests

");

if($q1){

$total=$q1->fetch_assoc()['total'];

}

/*=========================================
PENDING REQUESTS
=========================================*/

$pending=0;

$q2=$conn->query("

SELECT COUNT(*) total

FROM certificate_requests

WHERE status='Pending'

");

if($q2){

$pending=$q2->fetch_assoc()['total'];

}

/*=========================================
APPROVED REQUESTS
=========================================*/

$approved=0;

$q3=$conn->query("

SELECT COUNT(*) total

FROM certificate_requests

WHERE status='Approved'

");

if($q3){

$approved=$q3->fetch_assoc()['total'];

}

/*=========================================
REJECTED REQUESTS
=========================================*/

$rejected=0;

$q4=$conn->query("

SELECT COUNT(*) total

FROM certificate_requests

WHERE status='Rejected'

");

if($q4){

$rejected=$q4->fetch_assoc()['total'];

}

?>

<div class="row mt-4">

<div class="col-md-3 mb-4">

<div class="card border-0 shadow-lg">

<div class="card-body text-center">

<i class="fas fa-folder-open fa-3x text-primary mb-3"></i>

<h2>

<?php echo $total; ?>

</h2>

<h5>

Total Requests

</h5>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card border-0 shadow-lg">

<div class="card-body text-center">

<i class="fas fa-clock fa-3x text-warning mb-3"></i>

<h2>

<?php echo $pending; ?>

</h2>

<h5>

Pending

</h5>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card border-0 shadow-lg">

<div class="card-body text-center">

<i class="fas fa-check-circle fa-3x text-success mb-3"></i>

<h2>

<?php echo $approved; ?>

</h2>

<h5>

Approved

</h5>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card border-0 shadow-lg">

<div class="card-body text-center">

<i class="fas fa-times-circle fa-3x text-danger mb-3"></i>

<h2>

<?php echo $rejected; ?>

</h2>

<h5>

Rejected

</h5>

</div>

</div>

</div>

</div>

<hr class="mt-5">

<div class="text-center">

<img

src="../assets/images/logo.png"

width="60"

class="mb-3"

alt="GTU Logo">

<p class="text-muted mb-1">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

<p class="text-muted">

Student Affairs Office

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>