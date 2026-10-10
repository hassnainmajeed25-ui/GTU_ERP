<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

$applications=$conn->query("

SELECT

a.id,

a.student_id,

a.application_id,

a.application_status,

a.created_at,

s.first_name,

s.last_name,

s.email,

f.faculty_name,

p.program_name

FROM applications a

LEFT JOIN students s
ON a.student_id=s.id

LEFT JOIN faculties f
ON a.faculty_id=f.id

LEFT JOIN programs p
ON a.program_id=p.id

ORDER BY a.id DESC

");

$total=$conn->query("

SELECT COUNT(*) total

FROM applications

");

$count=$total->fetch_assoc();

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<title>

Manage Applications

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f5f6fa;

}

.card{

margin-top:30px;

border:none;

border-radius:15px;

box-shadow:0 5px 20px rgba(0,0,0,.10);

}

.card-header{

background:#198754;

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fas fa-file-alt me-2"></i>

Manage Applications

</div>

<div class="card-body">

<div class="alert alert-success">

<b>Total Applications :</b>

<?php echo $count['total']; ?>

</div>
<div class="row mb-3">

<div class="col-md-6">

<input

type="text"

id="searchApplication"

class="form-control"

placeholder="Search by Application No, Student Name or Email">

</div>

<div class="col-md-6 text-end">

<a

href="dashboard.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back Dashboard

</a>

</div>

</div>

<div class="table-responsive">

<table

id="applicationTable"

class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th width="70">

ID

</th>

<th>

Application No

</th>

<th>

Student

</th>

<th>

Faculty

</th>

<th>

Program

</th>

<th>

Status

</th>

<th>

Applied On

</th>

<th width="270">

Action

</th>

</tr>

</thead>

<tbody>
    <?php

if($applications->num_rows>0){

while($row=$applications->fetch_assoc()){

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<b>

<?php echo htmlspecialchars($row['application_id']); ?>

</b>

</td>

<td>

<?php

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

?>

<br>

<small class="text-muted">

<?php echo htmlspecialchars($row['email']); ?>

</small>

</td>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<?php

if($row['application_status']=="Draft"){

echo '<span class="badge bg-secondary">Draft</span>';

}elseif($row['application_status']=="Submitted"){

echo '<span class="badge bg-primary">Submitted</span>';

}elseif($row['application_status']=="Under Review"){

echo '<span class="badge bg-warning text-dark">Under Review</span>';

}elseif($row['application_status']=="Approved"){

echo '<span class="badge bg-success">Approved</span>';

}elseif($row['application_status']=="Rejected"){

echo '<span class="badge bg-danger">Rejected</span>';

}else{

echo '<span class="badge bg-dark">'.htmlspecialchars($row['application_status']).'</span>';

}

?>

</td>

<td>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</td>

<td>

<a

href="view_application.php?id=<?php echo $row['id']; ?>"

class="btn btn-info btn-sm">

<i class="fas fa-eye"></i>

View

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8" class="text-center">

No Applications Found

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

</div>

<script>

document.getElementById("searchApplication").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#applicationTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>