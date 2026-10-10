<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location:login.php");
    exit;

}

/*=========================================
PAGE TITLE
=========================================*/

$page_title="Marks Verification";

/*=========================================
LOAD SUBMITTED SESSIONS
=========================================*/

$query=$conn->query("

SELECT

mark_sessions.id,

faculties.faculty_name,

programs.program_name,

subjects.subject_name,

teachers.teacher_name,

mark_sessions.semester,

mark_sessions.shift,

mark_sessions.status,

mark_sessions.created_at

FROM mark_sessions

LEFT JOIN faculties
ON faculties.id=mark_sessions.faculty_id

LEFT JOIN programs
ON programs.id=mark_sessions.program_id

LEFT JOIN subjects
ON subjects.id=mark_sessions.subject_id

LEFT JOIN teachers
ON teachers.id=mark_sessions.teacher_id

WHERE

mark_sessions.status='Submitted'

ORDER BY

mark_sessions.created_at DESC

");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo $page_title; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef8ff;

font-family:'Segoe UI',sans-serif;

}

.card{

border:none;

border-radius:18px;

box-shadow:0 8px 20px rgba(0,0,0,.12);

}

.card-header{

background:#ff8800;

color:white;

}

.table thead{

background:#29b6f6;

color:white;

}

.btn-view{

background:#29b6f6;

color:white;

font-weight:bold;

}

.btn-view:hover{

background:#0288d1;

color:white;

}

.badge-submitted{

background:#ff9800;

font-size:14px;

}

</style>

</head>

<body>

<div class="container mt-5">

<div class="card">

<div class="card-header">

<h3>

<i class="fas fa-circle-check"></i>

Marks Verification

</h3>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead>

<tr>

<th>#</th>

<th>Faculty</th>

<th>Program</th>

<th>Semester</th>

<th>Shift</th>

<th>Subject</th>

<th>Teacher</th>

<th>Status</th>

<th width="140">

Action

</th>

</tr>

</thead>

<tbody>

<?php

$sr=1;

while($row=$query->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

Semester <?php echo $row['semester']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['shift']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['subject_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['teacher_name']); ?>

</td>

<td>

<span class="badge badge-submitted">

<?php echo $row['status']; ?>

</span>

</td>

<td>

<a

href="verify_marks.php?session_id=<?php echo $row['id']; ?>"

class="btn btn-view btn-sm">

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

<?php

if($query->num_rows==0){

?>

<div class="alert alert-warning text-center mt-4">

<i class="fas fa-circle-info me-2"></i>

No Submitted Marks Found.

</div>

<?php

}

?>

<hr>

<div class="text-end">

<a

href="dashboard.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back To Dashboard

</a>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>