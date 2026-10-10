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
GET ANNOUNCEMENT ID
=========================================*/

if(!isset($_GET['id'])){
    header("Location: announcements.php");
    exit;
}

$id=(int)$_GET['id'];

/*=========================================
GET ANNOUNCEMENT
=========================================*/

$stmt=$conn->prepare("

SELECT *

FROM announcements

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location: announcements.php");

exit;

}

$row=$result->fetch_assoc();

/*=========================================
UPDATE ANNOUNCEMENT
=========================================*/

if(isset($_POST['update'])){

$title=trim($_POST['title']);

$description=trim($_POST['description']);

$status=$_POST['status'];

if($title!="" && $description!=""){

$update=$conn->prepare("

UPDATE announcements

SET

title=?,

description=?,

status=?

WHERE id=?

");

$update->bind_param(

"sssi",

$title,

$description,

$status,

$id

);

if($update->execute()){

header("Location: announcements.php?success=updated");

exit;

}else{

$error="Unable to update announcement.";

}

}else{

$error="Please fill all required fields.";

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Announcement</title>

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

Edit Announcement

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

<?php if(isset($error)){ ?>

<div class="alert alert-danger">

<i class="fas fa-times-circle me-2"></i>

<?php echo $error; ?>

</div>

<?php } ?>

<div class="card">

<div class="card-header bg-warning text-dark">

<h5 class="mb-0">

<i class="fas fa-edit me-2"></i>

Update Announcement

</h5>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label class="form-label">

Announcement Title

</label>

<input

type="text"

name="title"

class="form-control"

value="<?php echo htmlspecialchars($row['title']); ?>"

required>

</div>

<div class="mb-3">

<label class="form-label">

Announcement Details

</label>

<textarea

name="description"

class="form-control"

rows="8"

required><?php echo htmlspecialchars($row['description']); ?></textarea>

</div>

<div class="mb-4">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select">

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

<div class="d-flex justify-content-between">

<a

href="announcements.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="update"

class="btn btn-warning">

<i class="fas fa-save me-2"></i>

Update Announcement

</button>

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