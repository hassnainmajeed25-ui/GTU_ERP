<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Examination Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*==================================
LIVE DASHBOARD COUNTS
==================================*/

// Total Students
$totalStudents=$conn->query("
SELECT COUNT(*) total
FROM students
")->fetch_assoc()['total'];


// Total Subjects
$totalSubjects=$conn->query("
SELECT COUNT(*) total
FROM subjects
")->fetch_assoc()['total'];


// Total Exams
$totalExams=$conn->query("
SELECT COUNT(*) total
FROM exams
")->fetch_assoc()['total'];


// Upcoming Exams
// Upcoming Exams

$upcomingExams = $conn->query("
SELECT COUNT(*) AS total
FROM exams
WHERE exam_start >= CURDATE()
")->fetch_assoc()['total'];


// Published Results
$publishedResults=$conn->query("
SELECT COUNT(*) total
FROM results
")->fetch_assoc()['total'];


// Pending Results
$pendingResults=$totalStudents-$publishedResults;

if($pendingResults<0){
    $pendingResults=0;
}


// Admit Cards
$admitCards=$conn->query("
SELECT COUNT(*) total
FROM admit_cards
")->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Examination Office Dashboard

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

.dashboard-card{
border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.10);
transition:.3s;
}

.dashboard-card:hover{
transform:translateY(-5px);
}

.card-icon{
font-size:45px;
margin-bottom:15px;
}

.footer{
margin-top:40px;
text-align:center;
color:#666;
}

</style>
</head>

<body>
    <div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php" class="active">
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

<a href="verify_marks.php">
<i class="fas fa-check-circle me-2"></i>
Verify Marks
</a>

<a href="pending_classes.php">
<i class="fas fa-square-poll-vertical me-2"></i>
Pending classes 
</a>

<a href="admit_cards.php">
<i class="fas fa-id-card me-2"></i>
Admit Cards
</a>

<li>
    <a href="summer_enrollment.php">
        <i class="fas fa-sun"></i>
        Summer Enrollment
    </a>
</li>

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

<a href="student_inbox.php">
<i class="fas fa-share"></i>
Student Messages
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
<div class="main">

<div class="topbar">

<div>

<h3>Examination Office Dashboard</h3>

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

<div class="row">

<!-- Total Students -->

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-primary">
<i class="fas fa-user-graduate"></i>
</div>

<h2><?php echo $totalStudents; ?></h2>

<h6>Total Students</h6>

</div>

</div>

</div>

<!-- Total Subjects -->

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-success">
<i class="fas fa-book"></i>
</div>

<h2><?php echo $totalSubjects; ?></h2>

<h6>Total Subjects</h6>

</div>

</div>

</div>

<!-- Total Exams -->

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-danger">
<i class="fas fa-file-signature"></i>
</div>

<h2><?php echo $totalExams; ?></h2>

<h6>Total Exams</h6>

</div>

</div>

</div>

<!-- Upcoming Exams -->

<div class="col-lg-3 col-md-6 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-warning">
<i class="fas fa-calendar-days"></i>
</div>

<h2><?php echo $upcomingExams; ?></h2>

<h6>Upcoming Exams</h6>

</div>

</div>

</div>

</div>

<!-- Second Row -->

<div class="row">

<!-- Published Results -->

<div class="col-lg-4 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-success">
<i class="fas fa-square-poll-vertical"></i>
</div>

<h2><?php echo $publishedResults; ?></h2>

<h6>Published Results</h6>

</div>

</div>

</div>

<!-- Pending Results -->

<div class="col-lg-4 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-danger">
<i class="fas fa-clock"></i>
</div>

<h2><?php echo $pendingResults; ?></h2>

<h6>Pending Results</h6>

</div>

</div>

</div>

<!-- Admit Cards -->

<div class="col-lg-4 mb-4">

<div class="card dashboard-card text-center">

<div class="card-body">

<div class="card-icon text-info">
<i class="fas fa-id-card"></i>
</div>

<h2><?php echo $admitCards; ?></h2>

<h6>Issued Admit Cards</h6>

</div>

</div>

</div>

</div>
<div class="card mt-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-calendar-check me-2"></i>

Upcoming Examination Schedule

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Subject Code</th>

<th>Subject Name</th>

<th>Exam Type</th>

<th>Exam Date</th>

<th>Time</th>

<th>Room</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$sql="

SELECT

subjects.subject_code,
subjects.subject_name,

exam_types.exam_name,

exams.exam_date,
exams.start_time,
exams.end_time,
exams.room_no,
exams.status

FROM exams

LEFT JOIN subjects
ON subjects.id=exams.subject_id

LEFT JOIN exam_types
ON exam_types.id=exams.exam_type_id

ORDER BY exams.exam_date ASC

LIMIT 10

";

$result=$conn->query($sql);

if($result && $result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td><?php echo htmlspecialchars($row['subject_code']); ?></td>

<td><?php echo htmlspecialchars($row['subject_name']); ?></td>

<?php echo htmlspecialchars($row['exam_name']); ?>

<td><?php echo date("d M Y",strtotime($row['exam_date'])); ?></td>

<td>

<?php

echo date("h:i A",strtotime($row['start_time']));

?>

-

<?php

echo date("h:i A",strtotime($row['end_time']));

?>

</td>

<td>

<?php echo htmlspecialchars($row['room_no']); ?>

</td>

<td>

<?php

if($row['status']=="Scheduled"){

echo '<span class="badge bg-primary">Scheduled</span>';

}elseif($row['status']=="Completed"){

echo '<span class="badge bg-success">Completed</span>';

}else{

echo '<span class="badge bg-danger">Cancelled</span>';

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8" class="text-center text-danger">

No Examination Schedule Available.

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
<div class="card mt-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-square-poll-vertical me-2"></i>

Latest Published Results

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-success">

<tr>

<th>#</th>

<th>Student</th>

<th>Semester</th>

<th>Percentage</th>

<th>CGPA</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$sql="

SELECT

students.first_name,
students.last_name,

results.semester,
results.percentage,
results.cgpa,
results.result_status

FROM results

LEFT JOIN students
ON students.id=results.student_id

ORDER BY results.published_at DESC

LIMIT 10

";

$result=$conn->query($sql);

if($result && $result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>

<td><?php echo $row['semester']; ?></td>

<td><?php echo number_format($row['percentage'],2); ?>%</td>

<td><?php echo number_format($row['cgpa'],2); ?></td>

<td>

<?php

if($row['result_status']=="Pass"){

echo '<span class="badge bg-success">PASS</span>';

}else{

echo '<span class="badge bg-danger">FAIL</span>';

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center text-muted">

No Published Results Found.

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
<hr class="mt-5">

<div class="footer">

<p>

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
