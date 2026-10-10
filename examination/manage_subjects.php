<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*====================================
LOGIN SECURITY
====================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Examination Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*====================================
LIVE SUBJECT LIST
====================================*/

$sql="

SELECT

subjects.*,

faculties.faculty_name,

programs.program_name,

teachers.teacher_name

FROM subjects

LEFT JOIN faculties
ON faculties.id=subjects.faculty_id

LEFT JOIN programs
ON programs.id=subjects.program_id

LEFT JOIN teachers
ON teachers.id=subjects.teacher_id

ORDER BY subjects.subject_name ASC

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

Manage Subjects

</title>

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
color:white;
padding:20px;
text-align:center;
border-bottom:1px solid rgba(255,255,255,.15);
}

.sidebar a{
display:block;
padding:14px 20px;
color:white;
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
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 3px 10px rgba(0,0,0,.1);
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.1);
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

<a href="manage_subjects.php" class="active">
<i class="fas fa-book me-2"></i>
Manage Subjects
</a>

<a href="manage_exams.php">
<i class="fas fa-calendar-days me-2"></i>
Manage Exams
</a>


<a href="admit_cards.php">
<i class="fas fa-id-card me-2"></i>
Admit Cards
</a>

<a href="reports.php">
<i class="fas fa-chart-column me-2"></i>
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

<!-- Main -->

<div class="main">

<?php

if(isset($_GET['updated'])){
?>

<div class="alert alert-success alert-dismissible fade show">

<i class="fas fa-check-circle me-2"></i>

Subject Updated Successfully.

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php
}

if(isset($_GET['deleted'])){
?>

<div class="alert alert-danger alert-dismissible fade show">

<i class="fas fa-trash me-2"></i>

Subject Deleted Successfully.

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php
}

if(isset($_GET['added'])){
?>

<div class="alert alert-primary alert-dismissible fade show">

<i class="fas fa-plus-circle me-2"></i>

Subject Added Successfully.

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php
}
?>

<div class="topbar">

<div>

<h3>Manage Subjects</h3>

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
<div class="card">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="fas fa-book me-2"></i>

Subject Management

</h5>

<a
href="add_subject.php"
class="btn btn-light btn-sm">

<i class="fas fa-plus"></i>

Add Subject

</a>

</div>

<div class="card-body">

<div class="row mb-3">

<div class="col-md-4">

<input

type="text"

id="search"

class="form-control"

placeholder="Search Subject...">

</div>

</div>
<div class="table-responsive">

<table
class="table table-bordered table-hover align-middle"
id="subjectTable">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Subject Code</th>

<th>Subject Name</th>

<th>Faculty</th>

<th>Program</th>

<th>Semester</th>

<th>Credit Hours</th>

<th>Teacher</th>

<th>Status</th>

<th width="170">

Action

</th>

</tr>

</thead>

<tbody>
<?php

if($result && $result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td>

<strong>

<?php echo htmlspecialchars($row['subject_code']); ?>

</strong>

</td>

<td>

<?php echo htmlspecialchars($row['subject_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td class="text-center">

<?php echo $row['semester']; ?>

</td>

<td class="text-center">

<?php echo $row['credit_hours']; ?>

</td>

<td>

<?php

if(!empty($row['teacher_name'])){

echo htmlspecialchars($row['teacher_name']);

}else{

echo '<span class="text-danger">Not Assigned</span>';

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

<span class="badge bg-danger">

Inactive

</span>

<?php

}

?>

</td>

<td>

<a

href="edit_subject.php?id=<?php echo $row['id'];?>"

class="btn btn-primary btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<a

href="delete_subject.php?id=<?php echo $row['id'];?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this subject?')">

<i class="fas fa-trash"></i>

Delete

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="10" class="text-center text-danger">

<i class="fas fa-circle-info me-2"></i>

No Subjects Found.

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

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-chart-bar me-2"></i>

Subject Statistics

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3">

<div class="alert alert-primary text-center">

<h3>

<?php

$total=$conn->query("SELECT COUNT(*) total FROM subjects")->fetch_assoc()['total'];

echo $total;

?>

</h3>

Total Subjects

</div>

</div>

<div class="col-md-3">

<div class="alert alert-success text-center">

<h3>

<?php

$total=$conn->query("

SELECT COUNT(*) total

FROM subjects

WHERE status='Active'

")->fetch_assoc()['total'];

echo $total;

?>

</h3>

Active Subjects

</div>

</div>

<div class="col-md-3">

<div class="alert alert-danger text-center">

<h3>

<?php

$total=$conn->query("

SELECT COUNT(*) total

FROM subjects

WHERE status='Inactive'

")->fetch_assoc()['total'];

echo $total;

?>

</h3>

Inactive Subjects

</div>

</div>

<div class="col-md-3">

<div class="alert alert-warning text-center">

<h3>

<?php

$total=$conn->query("

SELECT COUNT(*) total

FROM subjects

WHERE teacher_id IS NULL

")->fetch_assoc()['total'];

echo $total;

?>

</h3>

Teacher Not Assigned

</div>

</div>

</div>

</div>

</div>

<hr>

<div class="footer">

<p>

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<script>

document.getElementById("search").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#subjectTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().indexOf(value)>-1?"":"none";

});

});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>