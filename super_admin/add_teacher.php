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

$success="";
$error="";

/*=========================================
SAVE TEACHER
=========================================*/

if(isset($_POST['save_teacher'])){

$teacher_name=trim($_POST['teacher_name']);

$father_name=trim($_POST['father_name']);

$cnic=trim($_POST['cnic']);

$password=$cnic;

$gender=$_POST['gender'];

$date_of_birth=$_POST['date_of_birth'];

$email=trim($_POST['email']);

$phone=trim($_POST['phone']);

$address=trim($_POST['address']);

$faculty_id=(int)$_POST['faculty_id'];

$designation=trim($_POST['designation']);

$qualification=trim($_POST['qualification']);

$joining_date=$_POST['joining_date'];

$employment_type=$_POST['employment_type'];

$role=$_POST['role'];

$status=$_POST['status'];


/*=========================================
AUTO EMPLOYEE ID (Faculty Wise)
=========================================*/

// Faculty Prefix

$prefix = "";

switch($faculty_id){

    case 1:
        $prefix="CS";
    break;

    case 2:
        $prefix="MGT";
    break;

    case 3:
        $prefix="ENG";
    break;

    case 4:
        $prefix="SCI";
    break;

    case 5:
        $prefix="ART";
    break;

    default:
        $prefix="GEN";

}

// Last Employee of Same Faculty

$q=$conn->query("

SELECT employee_id

FROM teachers

WHERE faculty_id='$faculty_id'

ORDER BY id DESC

LIMIT 1

");

if($q->num_rows>0){

    $row=$q->fetch_assoc();

    $last=$row['employee_id'];

    preg_match('/(\d+)$/',$last,$match);

    $next=((int)$match[1])+1;

}else{

    $next=1;

}

$employee_id="GTU-".$prefix."-".str_pad($next,4,"0",STR_PAD_LEFT);


/*=========================================
CHECK EMAIL
=========================================*/

$check=$conn->prepare("

SELECT id

FROM teachers

WHERE email=?

LIMIT 1

");

$check->bind_param("s",$email);

$check->execute();

$r=$check->get_result();

if($r->num_rows>0){

$error="Email already exists.";

}else{

$stmt=$conn->prepare("

INSERT INTO teachers(

teacher_name,
father_name,
cnic,
gender,
date_of_birth,
employee_id,
password,
email,
phone,
address,
faculty_id,
designation,
qualification,
joining_date,
employment_type,
role,
status

)

VALUES(

?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?

)

");

$stmt->bind_param(

"sssssssssssssssss",

$teacher_name,
$father_name,
$cnic,
$gender,
$date_of_birth,
$employee_id,
$password,
$email,
$phone,
$address,
$faculty_id,
$designation,
$qualification,
$joining_date,
$employment_type,
$role,
$status

);

if($stmt->execute()){

$success="Teacher Added Successfully.";

}else{

$error="Database Error.";

}

}

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Teacher</title>

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
box-shadow:0 5px 15px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<!-- =========================
SIDEBAR
========================= -->

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

<a href="manage_teachers.php">
<i class="fas fa-chalkboard-teacher me-2"></i>
Manage Teachers
</a>

<a href="add_teacher.php" class="active">
<i class="fas fa-user-plus me-2"></i>
Add Teacher
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

<!-- =========================
MAIN
========================= -->

<div class="main">

<div class="topbar">

<div>

<h3>Add New Teacher</h3>

<small class="text-muted">

Global Tech University ERP System

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

if($success!=""){

?>

<div class="alert alert-success">

<i class="fas fa-circle-check me-2"></i>

<?php echo $success; ?>

</div>

<?php

}

?>

<?php

if($error!=""){

?>

<div class="alert alert-danger">

<i class="fas fa-circle-xmark me-2"></i>

<?php echo $error; ?>

</div>

<?php

}

?>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-user-plus me-2"></i>

Teacher Registration Form

</h5>

</div>

<div class="card-body">

<form method="POST">
<div class="row">

<!-- Teacher Name -->

<div class="col-md-6 mb-3">

<label class="form-label">

Teacher Full Name

</label>

<input
type="text"
name="teacher_name"
class="form-control"
placeholder="Enter Teacher Full Name"
required>

</div>

<!-- Father Name -->

<div class="col-md-6 mb-3">

<label class="form-label">

Father Name

</label>

<input
type="text"
name="father_name"
class="form-control"
placeholder="Enter Father Name"
required>

</div>


<!-- CNIC -->

<div class="col-md-6 mb-3">

<label class="form-label">

CNIC

</label>

<input
type="text"
name="cnic"
class="form-control"
placeholder="XXXXX-XXXXXXX-X"
required>

</div>


<!-- Gender -->

<div class="col-md-6 mb-3">

<label class="form-label">

Gender

</label>

<select name="gender" class="form-select">

<option value="Male">Male</option>

<option value="Female">Female</option>

</select>

</div>


<!-- Date of Birth -->

<div class="col-md-6 mb-3">

<label class="form-label">

Date of Birth

</label>

<input
type="date"
name="date_of_birth"
class="form-control">

</div>

<!-- Email -->

<div class="col-md-6 mb-3">

<label class="form-label">

Email Address

</label>

<input
type="email"
name="email"
class="form-control"
placeholder="teacher@gtu.edu.pk"
required>

</div>

<!-- Phone -->

<div class="col-md-6 mb-3">

<label class="form-label">

Phone Number

</label>

<input
type="text"
name="phone"
class="form-control"
placeholder="03XXXXXXXXX"
required>

</div>

<!-- Address -->

<div class="col-md-12 mb-3">

<label class="form-label">

Address

</label>

<textarea
name="address"
class="form-control"
rows="3"
placeholder="Complete Address"></textarea>

</div>

<!-- Faculty -->

<div class="col-md-6 mb-3">

<label class="form-label">

Faculty

</label>

<select
name="faculty_id"
class="form-select"
required>

<option value="">Select Faculty</option>

<?php

$faculty=$conn->query("

SELECT *

FROM faculties

WHERE status='Active'

ORDER BY faculty_name ASC

");

while($row=$faculty->fetch_assoc()){

?>

<option value="<?php echo $row['id']; ?>">

<?php echo htmlspecialchars($row['faculty_name']); ?>

</option>

<?php

}

?>

</select>

</div>

<!-- Designation -->

<div class="col-md-6 mb-3">

<label class="form-label">

Designation

</label>

<select
name="designation"
class="form-select"
required>

<option value="">Select Designation</option>

<option>Lecturer</option>

<option>Assistant Professor</option>

<option>Associate Professor</option>

<option>Professor</option>

<option>Lab Instructor</option>

<option>Visiting Faculty</option>

</select>

</div>

<!-- Qualification -->

<div class="col-md-6 mb-3">

<label class="form-label">

Qualification

</label>

<input
type="text"
name="qualification"
class="form-control"
placeholder="MS Computer Science"
required>

</div>

<!-- Joining Date -->

<div class="col-md-6 mb-3">

<label class="form-label">

Joining Date

</label>

<input
type="date"
name="joining_date"
class="form-control">

</div>


<!-- Employment Type -->

<div class="col-md-6 mb-3">

<label class="form-label">

Employment Type

</label>

<select name="employment_type" class="form-select">

<option value="Permanent">
Permanent
</option>

<option value="Contract">
Contract
</option>

<option value="Visiting">
Visiting
</option>

</select>

</div>


<!-- Role -->

<div class="col-md-6 mb-3">

<label class="form-label">

Role

</label>

<select name="role" class="form-select">

<option value="Teacher">
Teacher
</option>

<option value="HOD">
HOD
</option>

<option value="Dean">
Dean
</option>

</select>

</div>

<!-- Status -->

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select"
required>

<option value="Active" selected>

Active

</option>

<option value="Inactive">

Inactive

</option>

</select>

</div>

<!-- Employee ID Preview -->

<div class="col-md-6 mb-3">

<label class="form-label">

Employee ID

</label>

<input
type="text"
class="form-control"
value="Auto Generated"
readonly>

<small class="text-muted">

Employee ID will be generated automatically.

</small>

</div>

</div>

<hr>

<div class="text-end">

<button
type="reset"
class="btn btn-secondary">

<i class="fas fa-rotate-left me-2"></i>

Reset

</button>

<button
type="submit"
name="save_teacher"
class="btn btn-success">

<i class="fas fa-floppy-disk me-2"></i>

Save Teacher

</button>

<a
href="manage_teachers.php"
class="btn btn-primary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</form>

</div>

</div>
<!-- ==========================================
TEACHER INFORMATION
========================================== -->

<br>

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Teacher Registration Guidelines

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Teacher Name should match official documents.

</li>

<li class="list-group-item">

✔ Employee ID is generated automatically.

</li>

<li class="list-group-item">

✔ Email address must be unique.

</li>

<li class="list-group-item">

✔ Phone number should be valid.

</li>

</ul>

</div>

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Faculty must be selected correctly.

</li>

<li class="list-group-item">

✔ Teacher can later be assigned to multiple subjects.

</li>

<li class="list-group-item">

✔ Examination Office will assign subjects.

</li>

<li class="list-group-item">

✔ Teacher profile can be edited anytime by Super Admin.

</li>

</ul>

</div>

</div>

</div>

</div>

<!-- ==========================================
FOOTER
========================================== -->

<hr class="mt-5">

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<!-- ==========================================
BOOTSTRAP
========================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>