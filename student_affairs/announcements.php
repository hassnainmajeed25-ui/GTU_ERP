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
GET ALL ANNOUNCEMENTS
=========================================*/

$sql="

SELECT *

FROM announcements

ORDER BY id DESC

";

$result=$conn->query($sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Announcements</title>

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

overflow-y:auto;

}

.sidebar h3{

color:#fff;

text-align:center;

padding:20px;

}

.sidebar a{

display:block;

padding:14px 20px;

color:#fff;

text-decoration:none;

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

box-shadow:0 3px 10px rgba(0,0,0,.1);

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:25px;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 3px 10px rgba(0,0,0,.1);

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

<a href="complaints.php">
<i class="fas fa-triangle-exclamation me-2"></i>
Complaints
</a>

<a href="announcements.php" class="active">
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

<div>

<h3>

Announcements

</h3>

<small class="text-muted">

Student Affairs Office

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

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="fas fa-bullhorn me-2"></i>

University Announcements

</h5>

<a

href="add_announcement.php"

class="btn btn-light btn-sm">

<i class="fas fa-plus-circle me-2"></i>

Add Announcement

</a>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th width="6%">ID</th>

<th width="25%">Title</th>

<th>Description</th>

<th width="10%">Status</th>

<th width="15%">Date</th>

<th width="18%">Action</th>

</tr>

</thead>

<tbody>

<?php

if($result && $result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['title']); ?>

</strong>

</td>

<td>

<?php

echo substr(

htmlspecialchars($row['description']),

0,

80

);

if(strlen($row['description'])>80){

echo "...";

}

?>

</td>

<td>

<?php

if($row['status']=="Active"){

?>

<span class="badge bg-success">

Active

</span>

<?php

}else{

?>

<span class="badge bg-secondary">

Inactive

</span>

<?php

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

href="view_announcement.php?id=<?php echo $row['id'];?>"

class="btn btn-primary btn-sm">

<i class="fas fa-eye"></i>

</a>

<a

href="edit_announcement.php?id=<?php echo $row['id'];?>"

class="btn btn-warning btn-sm">

<i class="fas fa-edit"></i>

</a>

<a

href="delete_announcement.php?id=<?php echo $row['id'];?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this announcement?')">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center text-danger">

<i class="fas fa-bullhorn fa-2x mb-2"></i>

<br>

No Announcements Found

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
TOTAL ANNOUNCEMENTS
=========================================*/

$totalAnnouncements=0;

$q1=$conn->query("

SELECT COUNT(*) AS total

FROM announcements

");

if($q1){

$totalAnnouncements=$q1->fetch_assoc()['total'];

}

/*=========================================
ACTIVE ANNOUNCEMENTS
=========================================*/

$activeAnnouncements=0;

$q2=$conn->query("

SELECT COUNT(*) AS total

FROM announcements

WHERE status='Active'

");

if($q2){

$activeAnnouncements=$q2->fetch_assoc()['total'];

}

/*=========================================
INACTIVE ANNOUNCEMENTS
=========================================*/

$inactiveAnnouncements=0;

$q3=$conn->query("

SELECT COUNT(*) AS total

FROM announcements

WHERE status='Inactive'

");

if($q3){

$inactiveAnnouncements=$q3->fetch_assoc()['total'];

}

?>

<div class="row mt-4">

<div class="col-md-4 mb-3">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-bullhorn fa-3x text-primary mb-3"></i>

<h2>

<?php echo $totalAnnouncements; ?>

</h2>

<h5>

Total Announcements

</h5>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-circle-check fa-3x text-success mb-3"></i>

<h2>

<?php echo $activeAnnouncements; ?>

</h2>

<h5>

Active Announcements

</h5>

</div>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-circle-xmark fa-3x text-danger mb-3"></i>

<h2>

<?php echo $inactiveAnnouncements; ?>

</h2>

<h5>

Inactive Announcements

</h5>

</div>

</div>

</div>

</div>

<hr class="mt-5">

<div class="text-center">

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