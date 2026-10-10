<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Examination Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

/*=========================================
PAGE TITLE
=========================================*/

$page_title = "Summer Enrollment";

/*=========================================
COUNT PENDING
=========================================*/

$pending = $conn->query("
SELECT COUNT(*) AS total
FROM summer_enrollments
WHERE status='Pending'
")->fetch_assoc()['total'];

/*=========================================
COUNT APPROVED
=========================================*/

$approved = $conn->query("
SELECT COUNT(*) AS total
FROM summer_enrollments
WHERE status='Approved'
")->fetch_assoc()['total'];

/*=========================================
COUNT REJECTED
=========================================*/

$rejected = $conn->query("
SELECT COUNT(*) AS total
FROM summer_enrollments
WHERE status='Rejected'
")->fetch_assoc()['total'];

/*=========================================
TODAY APPLICATIONS
=========================================*/

$today = $conn->query("
SELECT COUNT(*) AS total
FROM summer_enrollments
WHERE DATE(created_at)=CURDATE()
")->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1.0">

<title>Summer Enrollment | GTU ERP</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<body style="background:#F5F7FA;">

<div class="container-fluid py-4">

<!-- =========================================
PAGE HEADER
========================================= -->

<div class="card border-0 shadow-lg mb-4">

<div class="card-body text-white"

style="background:linear-gradient(135deg,#0B3D91,#1565C0);border-radius:15px;">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2 class="fw-bold mb-2">

<i class="fas fa-sun me-2"></i>

Summer Enrollment Management

</h2>

<p class="mb-0">

Manage Student Summer Enrollment Applications

</p>

</div>

<div>

<i class="fas fa-user-shield"

style="font-size:70px;opacity:.25;"></i>

</div>

</div>

</div>

</div>

<!-- =========================================
DASHBOARD CARDS
========================================= -->

<div class="row">

<div class="col-lg-3 col-md-6 mb-4">

<div class="card border-0 shadow">

<div class="card-body text-center"

style="background:#FF9800;color:white;border-radius:15px;">

<i class="fas fa-hourglass-half fa-3x mb-3"></i>

<h6>Pending Applications</h6>

<h2 class="fw-bold">

<?php echo $pending; ?>

</h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card border-0 shadow">

<div class="card-body text-center"

style="background:#FB8C00;color:white;border-radius:15px;">

<i class="fas fa-circle-check fa-3x mb-3"></i>

<h6>Approved</h6>

<h2 class="fw-bold">

<?php echo $approved; ?>

</h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card border-0 shadow">

<div class="card-body text-center"

style="background:#EF6C00;color:white;border-radius:15px;">

<i class="fas fa-circle-xmark fa-3x mb-3"></i>

<h6>Rejected</h6>

<h2 class="fw-bold">

<?php echo $rejected; ?>

</h2>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-4">

<div class="card border-0 shadow">

<div class="card-body text-center"

style="background:#F57C00;color:white;border-radius:15px;">

<i class="fas fa-calendar-day fa-3x mb-3"></i>

<h6>Today's Applications</h6>

<h2 class="fw-bold">

<?php echo $today; ?>

</h2>

</div>

</div>

</div>

</div>

<!-- =========================================
SUMMER ENROLLMENT APPLICATIONS
========================================= -->

<div class="card border-0 shadow">

<div class="card-header"

style="background:#1565C0;color:white;">

<div class="d-flex justify-content-between align-items-center">

<h4 class="mb-0">

<i class="fas fa-list"></i>

Summer Enrollment Applications

</h4>

<input

type="text"

id="searchBox"

class="form-control"

placeholder="Search Student..."

style="width:260px;">

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table

class="table table-bordered table-hover align-middle"

id="applicationTable">

<thead style="background:#FF9800;color:white;">

<tr>

<th width="60">#</th>

<th>Student</th>

<th>Registration No</th>

<th>Program</th>

<th>Semester</th>

<th>Total Subjects</th>

<th>Status</th>

<th width="120">Action</th>

</tr>

</thead>

<tbody>

<?php

$list = $conn->query("

SELECT

summer_enrollments.*,

students.first_name,
students.last_name,
students.registration_no,

programs.program_name

FROM summer_enrollments

INNER JOIN students

ON summer_enrollments.student_id=students.id

INNER JOIN programs

ON students.program_id=programs.id

ORDER BY summer_enrollments.created_at DESC

");

$sr=1;

while($row=$list->fetch_assoc()){

$statusColor="warning";

if($row['status']=="Approved"){

$statusColor="success";

}

if($row['status']=="Rejected"){

$statusColor="danger";

}

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<strong>

<?php

echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);

?>

</strong>

</td>

<td>

<?php echo htmlspecialchars($row['registration_no']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<?php echo $row['semester']; ?>

</td>

<td>

<?php echo $row['total_subjects']; ?>

</td>

<td>

<span class="badge bg-<?php echo $statusColor; ?>">

<?php echo $row['status']; ?>

</span>

</td>

<td>

<a

href="view_summer_enrollment.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-eye"></i>

View

</a>

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

<!-- =========================================
SEARCH & FILTER SCRIPT
========================================= -->

<script>

const searchBox=document.getElementById("searchBox");

searchBox.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let table=document.getElementById("applicationTable");

let rows=table.getElementsByTagName("tr");

for(let i=1;i<rows.length;i++){

let text=rows[i].innerText.toLowerCase();

if(text.indexOf(value)>-1){

rows[i].style.display="";

}else{

rows[i].style.display="none";

}

}

});

</script>

<!-- =========================================
FOOTER
========================================= -->

<footer class="mt-5">

<div class="card border-0 shadow">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-6">

<h5 class="fw-bold text-primary">

Global Tech University ERP

</h5>

<p class="text-muted mb-0">

Summer Enrollment Management System

</p>

</div>

<div class="col-md-6 text-end">

<strong>

Version 1.0

</strong>

<br>

<span class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University

</span>

</div>

</div>

</div>

</div>

</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>