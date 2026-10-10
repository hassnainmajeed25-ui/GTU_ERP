<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

$result=$conn->query("

SELECT

programs.*,

faculties.faculty_name

FROM programs

INNER JOIN faculties

ON faculties.id=programs.faculty_id

ORDER BY programs.id ASC

");

$total=$conn->query("SELECT COUNT(*) total FROM programs");

$count=$total->fetch_assoc();

?>
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Manage Programs</title>

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

<i class="fas fa-graduation-cap me-2"></i>

Manage Programs

</div>

<div class="card-body">

<div class="alert alert-success">

<b>Total Programs :</b>

<?php echo $count['total']; ?>

</div>

<div class="row mb-3">

<div class="col-md-6">

<a href="add_program.php"

class="btn btn-success">

<i class="fas fa-plus"></i>

Add New Program

</a>

</div>

<div class="col-md-6">

<input

type="text"

id="searchProgram"

class="form-control"

placeholder="Search Program...">

</div>

</div>
<table
id="programTable"
class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th width="60">ID</th>

<th>Faculty</th>

<th>Program Name</th>

<th>Program Code</th>

<th>Duration</th>

<th>Status</th>

<th width="260">Action</th>

</tr>

</thead>

<tbody>

<?php while($row=$result->fetch_assoc()){ ?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['program_code']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['duration']); ?>

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

href="edit_program.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<a

href="change_program_status.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm">

<i class="fas fa-sync"></i>

Status

</a>

<a

href="delete_program.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Are you sure you want to delete this program?');">

<i class="fas fa-trash"></i>

Delete

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>
</div>

</div>

</div>

<script>

document.getElementById("searchProgram").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#programTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)
? ""
: "none";

});

});

</script>

</body>

</html>