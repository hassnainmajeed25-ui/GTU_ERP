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

if(!isset($_GET['id'])){

header("Location: manage_students.php");

exit;

}

$student_id=(int)$_GET['id'];

/*=========================================
GET STUDENT DETAILS
=========================================*/

$sql="

SELECT

students.*,

applications.application_id,
applications.registration_no,

programs.program_name,

faculties.faculty_name

FROM students

LEFT JOIN applications

ON applications.student_id=students.id

LEFT JOIN programs

ON programs.id=applications.program_id

LEFT JOIN faculties

ON faculties.id=applications.faculty_id

WHERE students.id=?

LIMIT 1

";

$stmt=$conn->prepare($sql);

$stmt->bind_param(

"i",

$student_id

);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location: manage_students.php");

exit;

}

$row=$result->fetch_assoc();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>View Student</title>

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

.profile-icon{

font-size:90px;

color:#0B1F52;

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

<a href="manage_students.php" class="active">
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

<div>

<h3>

Student Profile

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

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-user-graduate me-2"></i>

Student Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3 text-center">

<i class="fas fa-user-circle profile-icon"></i>

<h4 class="mt-3">

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</h4>

<?php

if($row['status']=="Active"){

?>

<span class="badge bg-success">

Active Student

</span>

<?php

}else{

?>

<span class="badge bg-danger">

Inactive Student

</span>

<?php

}

?>

</div>

<div class="col-md-9">

<table class="table table-bordered">

<tr>

<th width="30%">

Application ID

</th>

<td>

<?php echo htmlspecialchars($row['application_id']); ?>

</td>

</tr>

<tr>

<th>

Registration No

</th>

<td>

<?php echo htmlspecialchars($row['registration_no']); ?>

</td>

</tr>

<tr>

<th>

Full Name

</th>

<td>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

</tr>

<tr>

<th>

Father Name

</th>

<td>

<?php echo htmlspecialchars($row['father_name']); ?>

</td>

</tr>

<tr>

<th>

Email

</th>

<td>

<?php echo htmlspecialchars($row['email']); ?>

</td>

</tr>

<tr>

<th>

Mobile

</th>

<td>

<?php echo htmlspecialchars($row['mobile']); ?>

</td>

</tr>

<tr>

<th>

CNIC

</th>

<td>

<?php echo htmlspecialchars($row['cnic']); ?>

</td>

</tr>

<tr>

<th>

Faculty

</th>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

</tr>

<tr>

<th>

Program

</th>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

</tr>

<tr>

<th>

Created Date

</th>

<td>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</td>

</tr>

</table>

<div class="mt-4">

<a
href="manage_students.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button
onclick="window.print();"
class="btn btn-primary">

<i class="fas fa-print me-2"></i>

Print Profile

</button>

</div>

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