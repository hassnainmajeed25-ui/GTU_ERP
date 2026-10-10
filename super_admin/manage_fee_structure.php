<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

$result=$conn->query("

SELECT

fee_structure.*,

faculties.faculty_name,

programs.program_name,

admission_sessions.session_name

FROM fee_structure

INNER JOIN faculties
ON fee_structure.faculty_id=faculties.id

INNER JOIN programs
ON fee_structure.program_id=programs.id

INNER JOIN admission_sessions
ON fee_structure.session_id=admission_sessions.id

ORDER BY fee_structure.id ASC

");

$total=$conn->query("SELECT COUNT(*) total FROM fee_structure");

$count=$total->fetch_assoc();

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Fee Structure</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f5f6fa;

}

.card{

margin-top:35px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#198754;

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

.table th{

vertical-align:middle;

}

.table td{

vertical-align:middle;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fas fa-money-check-dollar me-2"></i>

Manage Fee Structure

</div>

<div class="card-body">

<div class="alert alert-success">

<b>Total Fee Records :</b>

<?php echo $count['total']; ?>

</div>

<div class="row mb-3">

<div class="col-md-6">

<a href="add_fee_structure.php"

class="btn btn-success">

<i class="fas fa-plus me-2"></i>

Add Fee Structure

</a>

</div>

<div class="col-md-6">

<input

type="text"

id="searchFee"

class="form-control"

placeholder="Search Fee Structure...">

</div>

</div>

<table

id="feeTable"

class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Faculty</th>

<th>Program</th>

<th>Session</th>

<th>Total Fee</th>

<th>Status</th>

<th width="240">

Action

</th>

</tr>

</thead>

<tbody>

<?php while($row=$result->fetch_assoc()){ ?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<?php echo $row['faculty_name']; ?>

</td>

<td>

<?php echo $row['program_name']; ?>

</td>

<td>

<?php echo $row['session_name']; ?>

</td>

<td>

<?php

$totalFee=

$row['admission_fee']+

$row['tuition_fee']+

$row['exam_fee']+

$row['library_fee']+

$row['lab_fee']+

$row['other_fee'];

echo "Rs. ".number_format($totalFee);

?>

</td>

<td>

<?php

if($row['status']=="Active"){

echo '<span class="badge bg-success">Active</span>';

}else{

echo '<span class="badge bg-danger">Inactive</span>';

}

?>

</td>

<td>

<a href="edit_fee_structure.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<a href="change_fee_status.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm">

<i class="fas fa-sync"></i>

Status

</a>

<a href="delete_fee_structure.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this Fee Structure?');">

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

document.getElementById("searchFee").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#feeTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

</script>

</body>

</html>