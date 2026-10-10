<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

$result=$conn->query("

SELECT *

FROM admission_sessions

ORDER BY id ASC

");

$total=$conn->query("SELECT COUNT(*) total FROM admission_sessions");

$count=$total->fetch_assoc();

?>
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Manage Admission Sessions</title>

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

background:#0B5ED7;

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

<i class="fas fa-calendar-alt me-2"></i>

Manage Admission Sessions

</div>

<div class="card-body">

<div class="alert alert-primary">

<b>Total Sessions :</b>

<?php echo $count['total']; ?>

</div>

<div class="row mb-3">

<div class="col-md-6">

<a href="add_session.php"

class="btn btn-success">

<i class="fas fa-plus"></i>

Add New Session

</a>

</div>

<div class="col-md-6">

<input

type="text"

id="searchSession"

class="form-control"

placeholder="Search Admission Session...">

</div>

</div>

<table

id="sessionTable"

class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th width="70">

ID

</th>

<th>

Admission Session

</th>

<th>

Start Date

</th>

<th>

End Date

</th>

<th>

Status

</th>

<th width="260">

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

<?php echo $row['session_name']; ?>

</td>

<td>

<?php echo date("d M Y",strtotime($row['start_date'])); ?>

</td>

<td>

<?php echo date("d M Y",strtotime($row['end_date'])); ?>

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

<a href="edit_session.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<a href="change_session_status.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm">

<i class="fas fa-sync"></i>

Status

</a>

<a href="delete_session.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this Admission Session?');">

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

document.getElementById("searchSession").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#sessionTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)
? ""
: "none";

});

});

</script>

</body>

</html>