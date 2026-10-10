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
SUCCESS MESSAGES
====================================*/

$success=" ";

if(isset($_GET['added'])){

echo '<div class="alert alert-success alert-dismissible fade show">

<i class="fas fa-check-circle me-2"></i>

Examination Added Successfully.

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>';

}

if(isset($_GET['updated'])){

echo '<div class="alert alert-primary alert-dismissible fade show">

<i class="fas fa-pen me-2"></i>

Examination Updated Successfully.

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>';

}

if(isset($_GET['deleted'])){

echo '<div class="alert alert-danger alert-dismissible fade show">

<i class="fas fa-trash me-2"></i>

Examination Deleted Successfully.

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>';

}

if(isset($_GET['error'])){

echo '<div class="alert alert-warning alert-dismissible fade show">

<i class="fas fa-circle-exclamation me-2"></i>

Unable to perform this operation.

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>';

}




/*====================================
LIVE COUNTS
====================================*/

$totalExams=0;
$upcomingExams=0;
$runningExams=0;
$completedExams=0;

$q=$conn->query("SELECT COUNT(*) total FROM exams");
if($q){
    $totalExams=$q->fetch_assoc()['total'];
}

$q=$conn->query("
SELECT COUNT(*) total
FROM exams
WHERE status='Upcoming'
");
if($q){
    $upcomingExams=$q->fetch_assoc()['total'];
}

$q=$conn->query("
SELECT COUNT(*) total
FROM exams
WHERE status='Running'
");
if($q){
    $runningExams=$q->fetch_assoc()['total'];
}

$q=$conn->query("
SELECT COUNT(*) total
FROM exams
WHERE status='Completed'
");
if($q){
    $completedExams=$q->fetch_assoc()['total'];
}

/*====================================
EXAMS LIST
====================================*/

$sql="

SELECT

exams.*,

subjects.subject_code,
subjects.subject_name,

faculties.faculty_name,

programs.program_name

FROM exams

LEFT JOIN subjects
ON subjects.id=exams.subject_id

LEFT JOIN faculties
ON faculties.id=exams.faculty_id

LEFT JOIN programs
ON programs.id=exams.program_id

ORDER BY exams.exam_date DESC

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

Manage Examinations

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
color:#fff;
padding:20px;
text-align:center;
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
box-shadow:0 5px 15px rgba(0,0,0,.10);
}

.dashboard-card{
transition:.3s;
}

.dashboard-card:hover{
transform:translateY(-5px);
}

.card-icon{
font-size:45px;
margin-bottom:15px;
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

<a href="manage_subjects.php">
<i class="fas fa-book me-2"></i>
Manage Subjects
</a>

<a href="manage_exams.php" class="active">
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

<a href="../admins/logout.php">
<i class="fas fa-right-from-bracket me-2"></i>
Logout
</a>

</div>

<div class="main">

<div class="topbar">

<div>

<h3>Manage Examinations</h3>

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
<?php
if($success!=""){
?>
<div class="alert alert-success alert-dismissible fade show">

<i class="fas fa-check-circle me-2"></i>

<?php echo $success; ?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

<?php
}
?>

<!-- ===========================
LIVE CARDS
=========================== -->

<div class="row">

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-primary">

<i class="fas fa-calendar-days"></i>

</div>

<h2>

<?php echo $totalExams; ?>

</h2>

<h6>Total Exams</h6>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-warning">

<i class="fas fa-clock"></i>

</div>

<h2>

<?php echo $upcomingExams; ?>

</h2>

<h6>Upcoming Exams</h6>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-success">

<i class="fas fa-play-circle"></i>

</div>

<h2>

<?php echo $runningExams; ?>

</h2>

<h6>Running Exams</h6>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-danger">

<i class="fas fa-check-circle"></i>

</div>

<h2>

<?php echo $completedExams; ?>

</h2>

<h6>Completed Exams</h6>

</div>

</div>

</div>

</div>

<!-- ===========================
SEARCH + BUTTON
=========================== -->

<div class="card">

<div class="card-body">

<div class="row">

<div class="col-md-6">

<input
type="text"
id="searchExam"
class="form-control"
placeholder="Search Exam Title / Subject">

</div>

<div class="col-md-6 text-end">

<a
href="add_exam.php"
class="btn btn-success">

<i class="fas fa-plus me-2"></i>

Add Examination

</a>

</div>

</div>

</div>

</div>

<br>

<!-- ===========================
EXAM TABLE
=========================== -->

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-calendar-days me-2"></i>

Examinations List

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table
class="table table-bordered table-hover align-middle"
id="examTable">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Exam Title</th>

<th>Subject</th>

<th>Faculty</th>

<th>Program</th>

<th>Semester</th>

<th>Exam Date</th>

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

<td>

<?php echo $sr++; ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['exam_title']); ?>

</strong>

</td>

<td>

<?php

if(!empty($row['subject_code'])){

echo htmlspecialchars($row['subject_code']);

echo " - ";

echo htmlspecialchars($row['subject_name']);

}else{

echo '<span class="text-danger">Subject Missing</span>';

}

?>

</td>

<td>

<?php

echo htmlspecialchars($row['faculty_name']);

?>

</td>

<td>

<?php

echo htmlspecialchars($row['program_name']);

?>

</td>

<td class="text-center">

<?php echo $row['semester']; ?>

</td>

<td>

<?php

if(!empty($row['exam_date'])){

echo date("d M Y",strtotime($row['exam_date']));

}else{

echo "-";

}

?>

</td>

<td>

<?php

if($row['status']=="Upcoming"){

?>

<span class="badge bg-warning">

Upcoming

</span>

<?php

}elseif($row['status']=="Running"){

?>

<span class="badge bg-primary">

Running

</span>

<?php

}else{

?>

<span class="badge bg-success">

Completed

</span>

<?php

}

?>

</td>

<td>

<a

href="edit_exam.php?id=<?php echo $row['id'];?>"

class="btn btn-primary btn-sm">

<i class="fas fa-edit"></i>

</a>

<a

href="delete_exam.php?id=<?php echo $row['id'];?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this Examination?')">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="9" class="text-center text-danger">

<i class="fas fa-circle-info me-2"></i>

No Examination Found.

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
EXAM STATISTICS
=========================== -->

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-chart-bar me-2"></i>

Examination Statistics

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3">

<div class="alert alert-primary text-center">

<h3>

<?php echo $totalExams; ?>

</h3>

Total Exams

</div>

</div>

<div class="col-md-3">

<div class="alert alert-warning text-center">

<h3>

<?php echo $upcomingExams; ?>

</h3>

Upcoming Exams

</div>

</div>

<div class="col-md-3">

<div class="alert alert-info text-center">

<h3>

<?php echo $runningExams; ?>

</h3>

Running Exams

</div>

</div>

<div class="col-md-3">

<div class="alert alert-success text-center">

<h3>

<?php echo $completedExams; ?>

</h3>

Completed Exams

</div>

</div>

</div>

</div>

</div>

<br>

<!-- ===========================
INFORMATION BOX
=========================== -->

<div class="card">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Examination Office Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Every examination belongs to one Subject.

</li>

<li class="list-group-item">

✔ Examination Office manages exam schedules.

</li>

<li class="list-group-item">

✔ Date, Time and Room are compulsory.

</li>

<li class="list-group-item">

✔ Examination Status updates automatically.

</li>

</ul>

</div>

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Admit Cards are generated from Exam Schedule.

</li>

<li class="list-group-item">

✔ Teachers enter marks after exams.

</li>

<li class="list-group-item">

✔ Results are published after verification.

</li>

<li class="list-group-item">

✔ All examination records remain permanently saved.

</li>

</ul>

</div>

</div>

</div>

</div>

<hr>

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<!-- ===========================
LIVE SEARCH
=========================== -->

<script>

document.getElementById("searchExam").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#examTable tbody tr");

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>