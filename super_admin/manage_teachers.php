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
LIVE COUNTS
=========================================*/

// Total Teachers

$totalTeachers=0;

$q=$conn->query("
SELECT COUNT(*) total
FROM teachers
");

if($q){
    $totalTeachers=$q->fetch_assoc()['total'];
}

// Active Teachers

$activeTeachers=0;

$q=$conn->query("
SELECT COUNT(*) total
FROM teachers
WHERE status='Active'
");

if($q){
    $activeTeachers=$q->fetch_assoc()['total'];
}

// Inactive Teachers

$inactiveTeachers=0;

$q=$conn->query("
SELECT COUNT(*) total
FROM teachers
WHERE status='Inactive'
");

if($q){
    $inactiveTeachers=$q->fetch_assoc()['total'];
}

// Faculties

$totalFaculties=0;

$q=$conn->query("
SELECT COUNT(*) total
FROM faculties
");

if($q){
    $totalFaculties=$q->fetch_assoc()['total'];
}

/*=========================================
LOAD TEACHERS
=========================================*/

$sql="

SELECT

teachers.*,

faculties.faculty_name

FROM teachers

LEFT JOIN faculties

ON faculties.id=teachers.faculty_id

ORDER BY teachers.teacher_name ASC

";

$result=$conn->query($sql);

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Manage Teachers

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>

body{
    background:#eef2f7;
    font-family:Arial,Helvetica,sans-serif;
}

/*==========================
SIDEBAR
==========================*/

.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:#0B1F52;
    overflow-y:auto;
}

.sidebar h3{
    color:#fff;
    text-align:center;
    padding:20px;
    border-bottom:1px solid rgba(255,255,255,.15);
}

.sidebar a{
    display:block;
    color:#fff;
    text-decoration:none;
    padding:14px 20px;
    transition:.3s;
}

.sidebar a:hover{
    background:#16367d;
}

.sidebar a.active{
    background:#2563eb;
}

/*==========================
MAIN
==========================*/

.main{
    margin-left:260px;
    padding:30px;
}

/*==========================
TOPBAR
==========================*/

.topbar{

    background:#fff;

    padding:18px 25px;

    border-radius:12px;

    box-shadow:0 4px 12px rgba(0,0,0,.08);

    margin-bottom:30px;

    display:flex;

    justify-content:space-between;

    align-items:center;

}

/*==========================
CARDS
==========================*/

.dashboard-card{

    border:none;

    border-radius:15px;

    box-shadow:0 5px 15px rgba(0,0,0,.08);

    transition:.3s;

}

.dashboard-card:hover{

    transform:translateY(-6px);

}

.card-icon{

    font-size:42px;

    margin-bottom:15px;

}

/*==========================
TABLE
==========================*/

.table td{

    vertical-align:middle;

}

/*==========================
BUTTONS
==========================*/

.btn{

    border-radius:8px;

}

/*==========================
FOOTER
==========================*/

.footer{

    margin-top:40px;

    text-align:center;

    color:#777;

}

</style>

</head>

<body>

<!-- ==========================
SIDEBAR
========================== -->

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

<!-- ==========================
MAIN AREA
========================== -->

<div class="main">

<div class="topbar">

<div>

<h3>Manage Teachers</h3>

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
<!-- =========================================
LIVE DASHBOARD CARDS
========================================= -->

<div class="row">

<!-- Total Teachers -->

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-primary">
<i class="fas fa-users"></i>
</div>

<h2><?php echo $totalTeachers; ?></h2>

<h6>Total Teachers</h6>

</div>

</div>

</div>

<!-- Active Teachers -->

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-success">
<i class="fas fa-user-check"></i>
</div>

<h2><?php echo $activeTeachers; ?></h2>

<h6>Active Teachers</h6>

</div>

</div>

</div>

<!-- Inactive Teachers -->

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-danger">
<i class="fas fa-user-xmark"></i>
</div>

<h2><?php echo $inactiveTeachers; ?></h2>

<h6>Inactive Teachers</h6>

</div>

</div>

</div>

<!-- Faculties -->

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-warning">
<i class="fas fa-building-columns"></i>
</div>

<h2><?php echo $totalFaculties; ?></h2>

<h6>Total Faculties</h6>

</div>

</div>

</div>

</div>

<!-- =========================================
SEARCH + BUTTONS
========================================= -->

<div class="card mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-5 mb-2">

<input
type="text"
id="searchTeacher"
class="form-control"
placeholder="Search Teacher Name, Employee ID or Email">

</div>

<div class="col-md-3 mb-2">

<select
id="facultyFilter"
class="form-select">

<option value="">All Faculties</option>

<?php

$f=$conn->query("

SELECT *

FROM faculties

ORDER BY faculty_name ASC

");

while($fac=$f->fetch_assoc()){

?>

<option>

<?php echo htmlspecialchars($fac['faculty_name']); ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-4 text-end">

<a
href="add_teacher.php"
class="btn btn-success">

<i class="fas fa-plus me-2"></i>

Add Teacher

</a>

<a
href="dashboard.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Dashboard

</a>

</div>

</div>

</div>

</div>

<!-- =========================================
TEACHERS TABLE
========================================= -->

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-chalkboard-teacher me-2"></i>

Teachers List

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table
class="table table-bordered table-hover align-middle"
id="teacherTable">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Employee ID</th>

<th>Teacher Name</th>

<th>Faculty</th>

<th>Designation</th>

<th>Email</th>

<th>Phone</th>

<th>Status</th>

<th width="180">Action</th>

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

<?php echo htmlspecialchars($row['employee_id']); ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['teacher_name']); ?>

</strong>

</td>

<td>

<?php

echo htmlspecialchars($row['faculty_name']);

?>

</td>

<td>

<?php

echo !empty($row['designation'])
? htmlspecialchars($row['designation'])
: "-";

?>

</td>

<td>

<?php

echo !empty($row['email'])
? htmlspecialchars($row['email'])
: "-";

?>

</td>

<td>

<?php

echo !empty($row['phone'])
? htmlspecialchars($row['phone'])
: "-";

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

<span class="badge bg-danger">

Inactive

</span>

<?php

}

?>

</td>

<td>

<a

href="teacher_profile.php?id=<?php echo $row['id'];?>"

class="btn btn-info btn-sm"

title="View">

<i class="fas fa-eye"></i>

</a>

<a

href="edit_teacher.php?id=<?php echo $row['id'];?>"

class="btn btn-primary btn-sm"

title="Edit">

<i class="fas fa-pen"></i>

</a>

<a

href="delete_teacher.php?id=<?php echo $row['id'];?>"

class="btn btn-danger btn-sm"

title="Delete"

onclick="return confirm('Are you sure you want to delete this teacher?');">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="9" class="text-center">

<div class="alert alert-warning mb-0">

<i class="fas fa-circle-info me-2"></i>

No Teachers Found.

Click

<strong>Add Teacher</strong>

to create your first teacher record.

</div>

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

<br>

<!-- ===========================
QUICK INFORMATION
=========================== -->

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Teacher Management Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Employee ID must be unique.

</li>

<li class="list-group-item">

✔ Every teacher belongs to one Faculty.

</li>

<li class="list-group-item">

✔ One teacher can teach multiple subjects.

</li>

<li class="list-group-item">

✔ Teacher status can be Active or Inactive.

</li>

</ul>

</div>

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Examination Office can assign teachers to subjects.

</li>

<li class="list-group-item">

✔ Teachers will enter marks later.

</li>

<li class="list-group-item">

✔ Teacher profile is managed by Super Admin.

</li>

<li class="list-group-item">

✔ All changes are reflected automatically across the ERP.

</li>

</ul>

</div>

</div>

</div>

</div>
<hr class="mt-5">

<div class="footer">

<p>

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<!-- ==========================================
LIVE SEARCH
========================================== -->

<script>

document.getElementById("searchTeacher").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#teacherTable tbody tr");

rows.forEach(function(row){

let text=row.innerText.toLowerCase();

if(text.indexOf(value)>-1){

row.style.display="";

}else{

row.style.display="none";

}

});

});

</script>

<!-- ==========================================
FACULTY FILTER
========================================== -->

<script>

document.getElementById("facultyFilter").addEventListener("change",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#teacherTable tbody tr");

rows.forEach(function(row){

let faculty=row.cells[3].innerText.toLowerCase();

if(value=="" || faculty==value){

row.style.display="";

}else{

row.style.display="none";

}

});

});

</script>

<!-- ==========================================
BOOTSTRAP
========================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>