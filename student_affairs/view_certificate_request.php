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

$admin_name=$_SESSION['admin_name'];
$admin_id=$_SESSION['admin_id'];

/*=========================================
GET REQUEST ID
=========================================*/

if(!isset($_GET['id'])){
    header("Location: certificate_requests.php");
    exit;
}

$id=(int)$_GET['id'];

/*=========================================
GET REQUEST DETAILS
=========================================*/

$stmt=$conn->prepare("

SELECT

certificate_requests.*,

students.first_name,

students.last_name,

students.email,

students.mobile

FROM certificate_requests

LEFT JOIN students

ON students.id=certificate_requests.student_id

WHERE certificate_requests.id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location: certificate_requests.php");

exit;

}

$row=$result->fetch_assoc();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>View Certificate Request</title>

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

background:linear-gradient(180deg,#0B1F52,#16367d);

overflow-y:auto;

}

.sidebar h3{

color:#fff;

text-align:center;

padding:20px;

}

.sidebar a{

display:block;

padding:15px 20px;

color:#fff;

text-decoration:none;

}

.sidebar a:hover{

background:#2563eb;

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

border-radius:15px;

display:flex;

justify-content:space-between;

align-items:center;

box-shadow:0 4px 12px rgba(0,0,0,.08);

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

Certificate Request Details

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

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-certificate me-2"></i>

Certificate Request Information

</h5>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th width="30%">Request No</th>

<td>

<?php echo htmlspecialchars($row['request_no']); ?>

</td>

</tr>

<tr>

<th>Student Name</th>

<td>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

</tr>

<tr>

<th>Email</th>

<td>

<?php echo htmlspecialchars($row['email']); ?>

</td>

</tr>

<tr>

<th>Mobile</th>

<td>

<?php echo htmlspecialchars($row['mobile']); ?>

</td>

</tr>

<tr>

<th>Certificate Type</th>

<td>

<?php echo htmlspecialchars($row['certificate_type']); ?>

</td>

</tr>

<tr>

<th>Purpose</th>

<td>

<?php echo nl2br(htmlspecialchars($row['purpose'])); ?>

</td>

</tr>

<tr>

<th>Status</th>

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

</tr>

<tr>

<th>Request Date</th>

<td>

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</td>

</tr>

<tr>

<th>Remarks</th>

<td>

<?php

echo !empty($row['remarks'])

? nl2br(htmlspecialchars($row['remarks']))

: "<span class='text-muted'>No Remarks</span>";

?>

</td>

</tr>

</table>

<div class="mt-4 d-flex flex-wrap gap-2">

<a
href="certificate_requests.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<a
href="approve_certificate_request.php?id=<?php echo $row['id']; ?>"
class="btn btn-success">

<i class="fas fa-check-circle me-2"></i>

Approve

</a>

<a
href="reject_certificate_request.php?id=<?php echo $row['id']; ?>"
class="btn btn-danger">

<i class="fas fa-times-circle me-2"></i>

Reject

</a>

<a
href="complete_certificate_request.php?id=<?php echo $row['id']; ?>"
class="btn btn-primary">

<i class="fas fa-flag-checkered me-2"></i>

Completed

</a>

</div>

</div>

</div>

<hr class="mt-5">

<div class="text-center mt-4">

<img

src="../assets/images/logo.png"

width="65"

class="mb-3"

alt="GTU Logo">

<h5 class="fw-bold text-primary">

Global Tech University ERP System

</h5>

<p class="text-muted mb-1">

Student Affairs Office

</p>

<p class="text-muted">

© <?php echo date("Y"); ?> All Rights Reserved.

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>