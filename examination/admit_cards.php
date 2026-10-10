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
LIVE COUNTS
====================================*/

$totalCards=0;

$q=$conn->query("SELECT COUNT(*) total FROM exams");

if($q){
    $totalCards=$q->fetch_assoc()['total'];
}

/*====================================
LOAD EXAMS
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

ORDER BY exams.exam_date ASC

";

$result=$conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Admit Cards</title>

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

<!-- SIDEBAR -->

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

<a href="manage_exams.php">
<i class="fas fa-calendar-days me-2"></i>
Manage Exams
</a>

<a href="enter_marks.php">
<i class="fas fa-pen me-2"></i>
Enter Marks
</a>

<a href="results.php">
<i class="fas fa-square-poll-vertical me-2"></i>
Results
</a>

<a href="admit_cards.php" class="active">
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

<!-- MAIN -->

<div class="main">

<div class="topbar">

<div>

<h3>Admit Cards</h3>

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

<!-- LIVE CARD -->

<div class="row">

<div class="col-lg-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-primary">

<i class="fas fa-id-card"></i>

</div>

<h2>

<?php echo $totalCards; ?>

</h2>

<h6>

Available Admit Cards

</h6>

</div>

</div>

</div>

<div class="col-lg-8">

<div class="card">

<div class="card-body">

<div class="row">

<div class="col-md-6">

<input
type="text"
id="searchExam"
class="form-control"
placeholder="Search Subject / Exam">

</div>

<div class="col-md-6 text-end">

<button
class="btn btn-success">

<i class="fas fa-print me-2"></i>

Print All

</button>

</div>

</div>

</div>

</div>

</div>

</div>

<br>

<!-- TABLE -->

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-id-card me-2"></i>

Admit Card List

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table
class="table table-bordered table-hover align-middle"
id="admitTable">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Exam</th>

<th>Subject</th>

<th>Faculty</th>

<th>Program</th>

<th>Date</th>

<th>Status</th>

<th width="220">

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

<?php echo htmlspecialchars($row['exam_title']); ?>

</strong>

</td>

<td>

<?php

echo htmlspecialchars($row['subject_code']);

echo " - ";

echo htmlspecialchars($row['subject_name']);

?>

</td>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<?php echo date("d M Y",strtotime($row['exam_date'])); ?>

</td>

<td>

<?php

if($row['status']=="Upcoming"){

echo '<span class="badge bg-warning">Upcoming</span>';

}elseif($row['status']=="Running"){

echo '<span class="badge bg-primary">Running</span>';

}else{

echo '<span class="badge bg-success">Completed</span>';

}

?>

</td>

<td>

<a
href="generate_admit_card.php?id=<?php echo $row['id'];?>"
class="btn btn-primary btn-sm">

<i class="fas fa-id-card"></i>

Generate

</a>

<a
href="print_admit_card.php?id=<?php echo $row['id'];?>"
class="btn btn-success btn-sm">

<i class="fas fa-print"></i>

Print

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8" class="text-center text-danger">

No Admit Cards Available.

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

<!-- =====================================
INFORMATION BOX
====================================== -->

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-circle-info me-2"></i>

Admit Card Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Admit Card is generated only for Scheduled Exams.

</li>

<li class="list-group-item">

✔ Student must carry Admit Card during Examination.

</li>

<li class="list-group-item">

✔ Examination Date, Time and Room are printed automatically.

</li>

<li class="list-group-item">

✔ Controller of Examination Signature will appear on Admit Card.

</li>

</ul>

</div>

<div class="col-md-6">

<ul class="list-group">

<li class="list-group-item">

✔ Student Photo will be printed automatically.

</li>

<li class="list-group-item">

✔ QR Code verification will be available later.

</li>

<li class="list-group-item">

✔ Admit Card can be printed multiple times.

</li>

<li class="list-group-item">

✔ All Admit Cards remain permanently saved.

</li>

</ul>

</div>

</div>

</div>

</div>

<br>

<!-- =====================================
EXAM STATISTICS
====================================== -->

<div class="card">

<div class="card-header bg-dark text-white">

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

<?php echo $totalCards; ?>

</h3>

Total Exams

</div>

</div>

<div class="col-md-3">

<div class="alert alert-warning text-center">

<h3>

<?php

echo $conn->query("SELECT COUNT(*) total FROM exams WHERE status='Upcoming'")->fetch_assoc()['total'];

?>

</h3>

Upcoming

</div>

</div>

<div class="col-md-3">

<div class="alert alert-success text-center">

<h3>

<?php

echo $conn->query("SELECT COUNT(*) total FROM exams WHERE status='Running'")->fetch_assoc()['total'];

?>

</h3>

Running

</div>

</div>

<div class="col-md-3">

<div class="alert alert-danger text-center">

<h3>

<?php

echo $conn->query("SELECT COUNT(*) total FROM exams WHERE status='Completed'")->fetch_assoc()['total'];

?>

</h3>

Completed

</div>

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

<!-- =====================================
LIVE SEARCH
====================================== -->

<script>

document.getElementById("searchExam").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#admitTable tbody tr");

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