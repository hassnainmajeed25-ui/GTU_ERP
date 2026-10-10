<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=====================================
LOGIN SECURITY
=====================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Admission Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*=====================================
APPROVED APPLICATIONS
=====================================*/

$sql="

SELECT

applications.id,
applications.application_id,
applications.application_status,
applications.merit_marks,
applications.approved_at,

students.first_name,
students.last_name,
students.father_name,

faculties.faculty_name,

programs.program_name

FROM applications

INNER JOIN students
ON students.id=applications.student_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

WHERE applications.application_status='Approved'

ORDER BY applications.approved_at DESC

";

$result=$conn->query($sql);

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>

Approved Applications

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

border-bottom:1px solid rgba(255,255,255,.2);

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

padding:18px 25px;

border-radius:12px;

box-shadow:0 3px 10px rgba(0,0,0,.08);

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:25px;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.08);

}

.table th{

background:#0B1F52;

color:#fff;

text-align:center;

}

.table td{

vertical-align:middle;

text-align:center;

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

<a href="manage_applications.php">

<i class="fas fa-file-alt me-2"></i>

Manage Applications

</a>

<a href="approved_applications.php" class="active">

<i class="fas fa-circle-check me-2"></i>

Approved Applications

</a>

<a href="rejected_applications.php">

<i class="fas fa-circle-xmark me-2"></i>

Rejected Applications

</a>

<a href="merit_list.php">

<i class="fas fa-ranking-star me-2"></i>

Merit List

</a>

<a href="selected_students.php">

<i class="fas fa-user-check me-2"></i>

Selected Students

</a>

<a href="../admins/logout.php">

<i class="fas fa-sign-out-alt me-2"></i>

Logout

</a>

</div>

<div class="main">

<div class="topbar">

<h3>

Approved Applications

</h3>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

Approved Applications List

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Application ID</th>

<th>Student Name</th>

<th>Father Name</th>

<th>Faculty</th>

<th>Program</th>

<th>Merit</th>

<th>Approved Date</th>

<th>Action</th>

</tr>

</thead>

<tbody>
<?php

if($result && $result->num_rows > 0){

$sr = 1;

while($row = $result->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td>

<strong>
<?php echo htmlspecialchars($row['application_id']); ?>
</strong>

</td>

<td>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['father_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<span class="badge bg-primary">

<?php echo $row['merit_marks']; ?> %

</span>

</td>

<td>

<?php

if(!empty($row['approved_at'])){
    echo date("d M Y, h:i A", strtotime($row['approved_at']));
}else{
    echo "N/A";
}

?>

</td>

<td>

<a href="view_application.php?id=<?php echo $row['id']; ?>"
class="btn btn-info btn-sm">

<i class="fas fa-eye"></i> View

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="9" class="text-center text-danger">

No Approved Applications Found.

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

<!-- FOOTER -->

<div class="text-center mt-4">

<small class="text-muted">

© <?php echo date("Y"); ?> Global Tech University ERP System

</small>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>