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
SEARCH
=========================================*/

$search="";

$where="";

if(isset($_GET['search'])){

$search=trim($_GET['search']);

$search=mysqli_real_escape_string($conn,$search);

$where="

WHERE

students.first_name LIKE '%$search%'

OR students.last_name LIKE '%$search%'

OR students.email LIKE '%$search%'

OR students.mobile LIKE '%$search%'

OR students.cnic LIKE '%$search%'

";

}

/*=========================================
GET STUDENTS
=========================================*/

$sql="

SELECT *

FROM students

$where

ORDER BY id DESC

";

$students=$conn->query($sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Students</title>

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

Manage Students

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

<div class="card mb-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-search me-2"></i>

Search Student

</h5>

</div>

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-10">

<input

type="text"

name="search"

class="form-control"

placeholder="Search by Name, Email, Mobile or CNIC"

value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-md-2 d-grid">

<button

type="submit"

class="btn btn-primary">

<i class="fas fa-search me-2"></i>

Search

</button>

</div>

</div>

</form>

</div>

</div>

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-users me-2"></i>

Students List

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Name</th>

<th>Father Name</th>

<th>Email</th>

<th>Mobile</th>

<th>Status</th>

<th width="180">Actions</th>

</tr>

</thead>

<tbody>

<?php

if($students && $students->num_rows>0){

while($row=$students->fetch_assoc()){

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</strong>

</td>

<td>

<?php echo htmlspecialchars($row['father_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['email']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['mobile']); ?>

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

<span class="badge bg-danger">

Inactive

</span>

<?php

}

?>

</td>

<td>

<a

href="view_student.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-eye"></i>

View

</a>

<a

href="edit_student.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<?php

if($row['status']=="Active"){

?>

<a

href="change_student_status.php?id=<?php echo $row['id']; ?>&status=Inactive"

class="btn btn-danger btn-sm"

onclick="return confirm('Deactivate this student?')">

<i class="fas fa-user-slash"></i>

</a>

<?php

}else{

?>

<a

href="change_student_status.php?id=<?php echo $row['id']; ?>&status=Active"

class="btn btn-success btn-sm"

onclick="return confirm('Activate this student?')">

<i class="fas fa-user-check"></i>

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

<td colspan="7" class="text-center text-danger">

<i class="fas fa-users-slash fa-2x mb-2"></i>

<br>

No Students Found

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
TOTAL STUDENTS
=========================================*/

$totalStudentsResult=$conn->query("

SELECT COUNT(*) AS total

FROM students

");

$totalStudents=0;

if($totalStudentsResult){

$totalStudents=$totalStudentsResult->fetch_assoc()['total'];

}

/*=========================================
ACTIVE STUDENTS
=========================================*/

$activeStudentsResult=$conn->query("

SELECT COUNT(*) AS total

FROM students

WHERE status='Active'

");

$activeStudents=0;

if($activeStudentsResult){

$activeStudents=$activeStudentsResult->fetch_assoc()['total'];

}

/*=========================================
INACTIVE STUDENTS
=========================================*/

$inactiveStudentsResult=$conn->query("

SELECT COUNT(*) AS total

FROM students

WHERE status='Inactive'

");

$inactiveStudents=0;

if($inactiveStudentsResult){

$inactiveStudents=$inactiveStudentsResult->fetch_assoc()['total'];

}

?>

<div class="row mt-4">

<div class="col-md-4">

<div class="card shadow border-0">

<div class="card-body text-center">

<i class="fas fa-users fa-3x text-primary mb-3"></i>

<h2>

<?php echo $totalStudents; ?>

</h2>

<h5>

Total Students

</h5>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow border-0">

<div class="card-body text-center">

<i class="fas fa-user-check fa-3x text-success mb-3"></i>

<h2>

<?php echo $activeStudents; ?>

</h2>

<h5>

Active Students

</h5>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card shadow border-0">

<div class="card-body text-center">

<i class="fas fa-user-slash fa-3x text-danger mb-3"></i>

<h2>

<?php echo $inactiveStudents; ?>

</h2>

<h5>

Inactive Students

</h5>

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

<p class="text-muted">

Student Affairs Office

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>