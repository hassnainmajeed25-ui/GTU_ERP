
<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['super_admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['super_admin_name'];

/*=========================================
GET TEACHER ID
=========================================*/

if(!isset($_GET['id'])){

header("Location: manage_teachers.php");

exit;

}

$id=(int)$_GET['id'];

/*=========================================
GET TEACHER DETAILS
=========================================*/

$sql="

SELECT

teachers.*,

faculties.faculty_name

FROM teachers

LEFT JOIN faculties

ON faculties.id=teachers.faculty_id

WHERE teachers.id='$id'

LIMIT 1

";

$result=$conn->query($sql);

if($result->num_rows==0){

header("Location: manage_teachers.php");

exit;

}

$teacher=$result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Teacher Profile</title>

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

.profile-card{
background:#fff;
border:none;
border-radius:18px;
box-shadow:0 5px 15px rgba(0,0,0,.10);
padding:35px;
}

.profile-image{

width:140px;
height:140px;
border-radius:50%;
background:#0B1F52;
color:#fff;
display:flex;
align-items:center;
justify-content:center;
font-size:60px;
margin:auto;
margin-bottom:20px;

}

.table td{

padding:14px;
vertical-align:middle;

}

</style>

</head>

<body>

<!-- ===========================
SIDEBAR
=========================== -->

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

<a href="manage_teachers.php" class="active">
<i class="fas fa-chalkboard-teacher me-2"></i>
Manage Teachers
</a>

<a href="manage_faculties.php">
<i class="fas fa-building me-2"></i>
Manage Faculties
</a>

<a href="manage_programs.php">
<i class="fas fa-book me-2"></i>
Manage Programs
</a>

<a href="manage_admins.php">
<i class="fas fa-user-shield me-2"></i>
Manage Admins
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
<i class="fas fa-right-from-bracket me-2"></i>
Logout
</a>

</div>

<!-- ===========================
MAIN
=========================== -->

<div class="main">

<div class="topbar">

<div>

<h3>

Teacher Profile

</h3>

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

<div class="row justify-content-center">

<div class="col-lg-9">

<div class="profile-card">

<div class="profile-image">

<i class="fas fa-user"></i>

</div>

<h3 class="text-center">

<?php echo htmlspecialchars($teacher['teacher_name']); ?>

</h3>

<p class="text-center text-muted">

<?php echo htmlspecialchars($teacher['designation']); ?>

</p>

<hr>
<div class="table-responsive">

<table class="table table-bordered">

<tr>

<th width="35%">

Employee ID

</th>

<td>

<?php echo htmlspecialchars($teacher['employee_id']); ?>

</td>

</tr>

<tr>

<th>

Teacher Name

</th>

<td>

<?php echo htmlspecialchars($teacher['teacher_name']); ?>

</td>

</tr>

<tr>

<th>

Father Name

</th>

<td>

<?php

echo !empty($teacher['father_name'])
? htmlspecialchars($teacher['father_name'])
: "-";

?>

</td>

</tr>


<tr>

<th>

CNIC

</th>

<td>

<?php

echo !empty($teacher['cnic'])
? htmlspecialchars($teacher['cnic'])
: "-";

?>

</td>

</tr>


<tr>

<th>

Gender

</th>

<td>

<?php

echo !empty($teacher['gender'])
? htmlspecialchars($teacher['gender'])
: "-";

?>

</td>

</tr>


<tr>

<th>

Date of Birth

</th>

<td>

<?php

echo !empty($teacher['date_of_birth'])
? date("d M Y",strtotime($teacher['date_of_birth']))
: "-";

?>

</td>

</tr>

<tr>

<th>

Email Address

</th>

<td>

<?php

echo !empty($teacher['email'])
? htmlspecialchars($teacher['email'])
: "-";

?>

</td>

</tr>

<tr>

<th>

Phone Number

</th>

<td>

<?php

echo !empty($teacher['phone'])
? htmlspecialchars($teacher['phone'])
: "-";

?>

</td>

</tr>


<tr>

<th>

Address

</th>

<td>

<?php

echo !empty($teacher['address'])
? htmlspecialchars($teacher['address'])
: "-";

?>

</td>

</tr>

<tr>

<th>

Faculty

</th>

<td>

<?php

echo !empty($teacher['faculty_name'])
? htmlspecialchars($teacher['faculty_name'])
: "-";

?>

</td>

</tr>

<tr>

<th>

Designation

</th>

<td>

<?php

echo !empty($teacher['designation'])
? htmlspecialchars($teacher['designation'])
: "-";

?>

</td>

</tr>

<tr>

<th>

Qualification

</th>

<td>

<?php

echo !empty($teacher['qualification'])
? htmlspecialchars($teacher['qualification'])
: "-";

?>

</td>

</tr>


<tr>

<th>

Joining Date

</th>

<td>

<?php

echo !empty($teacher['joining_date'])
? date("d M Y",strtotime($teacher['joining_date']))
: "-";

?>

</td>

</tr>


<tr>

<th>

Employment Type

</th>

<td>

<?php

echo !empty($teacher['employment_type'])
? htmlspecialchars($teacher['employment_type'])
: "-";

?>

</td>

</tr>


<tr>

<th>

Role

</th>

<td>

<?php

echo !empty($teacher['role'])
? htmlspecialchars($teacher['role'])
: "-";

?>

</td>

</tr>

<tr>

<th>

Status

</th>

<td>

<?php

if($teacher['status']=="Active"){

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

</tr>

<tr>

<th>

Created Date

</th>

<td>

<?php

echo date("d M Y",strtotime($teacher['created_at']));

?>

</td>

</tr>

</table>

</div>

<hr>

<div class="text-end">

<a
href="manage_teachers.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<a
href="edit_teacher.php?id=<?php echo $teacher['id'];?>"
class="btn btn-primary">

<i class="fas fa-pen me-2"></i>

Edit Teacher

</a>

<a
href="delete_teacher.php?id=<?php echo $teacher['id'];?>"
class="btn btn-danger"
onclick="return confirm('Are you sure you want to delete this teacher?');">

<i class="fas fa-trash me-2"></i>

Delete Teacher

</a>

</div>
<br>

<!-- ==========================================
ADDITIONAL INFORMATION
========================================== -->

<div class="card mt-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Teacher Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

<ul class="list-group">

<li class="list-group-item">

<b>Employee ID:</b>

<?php echo htmlspecialchars($teacher['employee_id']); ?>

</li>


<li class="list-group-item">

<b>Role:</b>

<?php echo htmlspecialchars($teacher['role']); ?>

</li>


<li class="list-group-item">

<b>Employment:</b>

<?php echo htmlspecialchars($teacher['employment_type']); ?>

</li>


<li class="list-group-item">

<b>Joining Date:</b>

<?php

echo !empty($teacher['joining_date'])
? date("d M Y",strtotime($teacher['joining_date']))
: "-";

?>

</li>

</ul>

</ul>

</div>

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Teacher can teach multiple subjects.

</li>

<li class="list-group-item">

✔ Teacher marks are managed by Examination Office.

</li>

<li class="list-group-item">

✔ Teacher profile is controlled by Super Admin.

</li>

<li class="list-group-item">

✔ All changes are reflected throughout the ERP System.

</li>

</ul>

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