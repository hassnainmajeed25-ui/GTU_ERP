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

if($_SESSION['department']!="Admission Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*=========================================
GET SELECTED STUDENTS
=========================================*/

$sql="

SELECT

applications.id,
applications.application_id,
applications.registration_no,
applications.application_status,
applications.created_at,

students.first_name,
students.last_name,
students.email,
students.mobile,

faculties.faculty_name,

programs.program_name

FROM applications

INNER JOIN students
ON students.id=applications.student_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

WHERE applications.application_status='Selected'

ORDER BY applications.created_at DESC

";

$result=$conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Selected Students - Admission Office</title>

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
padding:20px 10px;
border-bottom:1px solid rgba(255,255,255,.2);
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
box-shadow:0 3px 10px rgba(0,0,0,.1);
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 4px 12px rgba(0,0,0,.08);
}

.table th{
background:#0B1F52;
color:white;
vertical-align:middle;
}

.table td{
vertical-align:middle;
}

</style>

</head>

<body>

<!-- Sidebar -->

<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php">
<i class="fas fa-home me-2"></i>
Dashboard
</a>

<a href="manage_applications.php">
<i class="fas fa-file-alt me-2"></i>
Manage Applications
</a>

<a href="merit_list.php">
<i class="fas fa-ranking-star me-2"></i>
Merit List
</a>

<a href="selected_students.php" class="active">
<i class="fas fa-user-check me-2"></i>
Selected Students
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

<a href="profile.php">
<i class="fas fa-user-circle me-2"></i>
Profile
</a>

<a href="../admins/logout.php">
<i class="fas fa-sign-out-alt me-2"></i>
Logout
</a>

</div>

<!-- Main -->

<div class="main">

<div class="topbar">

<div>

<h3 class="mb-1">

Selected Students

</h3>

<small class="text-muted">

Admission Office Dashboard

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>
<?php

if(isset($_GET['reg_success'])){

?>

<div class="alert alert-success alert-dismissible fade show">

<i class="fas fa-check-circle me-2"></i>

Registration Number Generated Successfully.

<button
type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php

}

?>

<?php

if(isset($_GET['reg_error'])){

?>

<div class="alert alert-danger alert-dismissible fade show">

<i class="fas fa-times-circle me-2"></i>

Unable to Generate Registration Number.

<button
type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php

}

?>
<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-user-check me-2"></i>

Selected Students List

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Application ID</th>

<th>Registration No</th>

<th>Student Name</th>

<th>Email</th>

<th>Mobile</th>

<th>Faculty</th>

<th>Program</th>

<th>Status</th>

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

<?php echo htmlspecialchars($row['application_id']); ?>

</strong>

</td>

<td>

<?php

if(!empty($row['registration_no'])){

?>

<span class="badge bg-success">

<?php echo htmlspecialchars($row['registration_no']); ?>

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark">

Not Generated

</span>

<?php

}

?>

</td>

<td>

<?php

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

?>

</td>

<td>

<?php echo htmlspecialchars($row['email']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['mobile']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<span class="badge bg-primary">

<?php echo htmlspecialchars($row['application_status']); ?>

</span>

</td>

<td>

<a

href="view_application.php?id=<?php echo $row['id'];?>"

class="btn btn-info btn-sm mb-1">

<i class="fas fa-eye"></i>

View

</a>

<?php

if(empty($row['registration_no'])){

?>

<a

href="generate_registration.php?id=<?php echo $row['id'];?>"

class="btn btn-success btn-sm mb-1"

onclick="return confirm('Generate Registration Number?');">

<i class="fas fa-id-card"></i>

Generate Reg No

</a>

<?php

}else{

?>

<a

href="print_admission_letter.php?id=<?php echo $row['id'];?>"

class="btn btn-primary btn-sm">

<i class="fas fa-print"></i>

Print Letter

</a>

<?php

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="10" class="text-center text-danger">

No Selected Students Found.

</td>

</tr>

<?php

}

?>
