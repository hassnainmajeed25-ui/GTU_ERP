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
GET STUDENT
=========================================*/

$stmt=$conn->prepare("

SELECT *

FROM students

WHERE id=?

LIMIT 1

");

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

/*=========================================
UPDATE STUDENT
=========================================*/

if(isset($_POST['update'])){

$first_name=trim($_POST['first_name']);
$last_name=trim($_POST['last_name']);
$father_name=trim($_POST['father_name']);
$email=trim($_POST['email']);
$mobile=trim($_POST['mobile']);
$cnic=trim($_POST['cnic']);
$status=$_POST['status'];

$update=$conn->prepare("

UPDATE students

SET

first_name=?,
last_name=?,
father_name=?,
email=?,
mobile=?,
cnic=?,
status=?

WHERE id=?

");

$update->bind_param(

"sssssssi",

$first_name,
$last_name,
$father_name,
$email,
$mobile,
$cnic,
$status,
$student_id

);

if($update->execute()){

header("Location: manage_students.php?updated=1");

exit;

}else{

$error="Unable to update student.";

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Student</title>

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

<h3>Edit Student</h3>

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

<?php

if(isset($error)){

?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php

}

?>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-user-edit me-2"></i>

Edit Student Information

</h5>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

First Name

</label>

<input
type="text"
name="first_name"
class="form-control"
value="<?php echo htmlspecialchars($row['first_name']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Last Name

</label>

<input
type="text"
name="last_name"
class="form-control"
value="<?php echo htmlspecialchars($row['last_name']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Father Name

</label>

<input
type="text"
name="father_name"
class="form-control"
value="<?php echo htmlspecialchars($row['father_name']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Email

</label>

<input
type="email"
name="email"
class="form-control"
value="<?php echo htmlspecialchars($row['email']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Mobile

</label>

<input
type="text"
name="mobile"
class="form-control"
value="<?php echo htmlspecialchars($row['mobile']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

CNIC

</label>

<input
type="text"
name="cnic"
class="form-control"
value="<?php echo htmlspecialchars($row['cnic']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select"

required>

<option value="Active"

<?php if($row['status']=="Active") echo "selected"; ?>>

Active

</option>

<option value="Inactive"

<?php if($row['status']=="Inactive") echo "selected"; ?>>

Inactive

</option>

</select>

</div>

</div>

<hr>

<div class="d-flex justify-content-between">

<a

href="manage_students.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<div>

<button

type="reset"

class="btn btn-warning">

<i class="fas fa-rotate-left me-2"></i>

Reset

</button>

<button

type="submit"

name="update"

class="btn btn-success">

<i class="fas fa-save me-2"></i>

Update Student

</button>

</div>

</div>

</form>

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