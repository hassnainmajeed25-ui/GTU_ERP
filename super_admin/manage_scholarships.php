<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

$result=$conn->query("

SELECT *

FROM scholarships

ORDER BY id ASC

");

$total=$conn->query("

SELECT COUNT(*) total

FROM scholarships

");

$count=$total->fetch_assoc();

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Scholarships</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f4f6f9;

}

.card{

margin-top:30px;

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

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fas fa-award me-2"></i>

Manage Scholarships

</div>

<div class="card-body">

<div class="alert alert-success">

<b>Total Scholarships :</b>

<?php echo $count['total']; ?>

</div>

<div class="row mb-3">

<div class="col-md-6">

<a href="add_scholarship.php"

class="btn btn-success">

<i class="fas fa-plus"></i>

Add New Scholarship

</a>

</div>

<div class="col-md-6">

<input

type="text"

id="searchScholarship"

class="form-control"

placeholder="Search Scholarship...">

</div>

</div>

<table

id="scholarshipTable"

class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th width="60">ID</th>

<th>Scholarship Name</th>

<th>Type</th>

<th>Discount</th>

<th>Last Date</th>

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

<?php echo $row['scholarship_name']; ?>

</td>

<td>

<?php echo $row['scholarship_type']; ?>

</td>

<td>

<?php

if($row['discount_type']=="Percentage"){

echo $row['discount_value']." %";

}else{

echo "Rs. ".number_format($row['discount_value'],2);

}

?>

</td>

<td>

<?php

if(!empty($row['last_date'])){

echo date("d M Y",strtotime($row['last_date']));

}else{

echo "-";

}

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

<a

href="edit_scholarship.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<a

href="change_scholarship_status.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm">

<i class="fas fa-sync"></i>

Status

</a>

<a

href="delete_scholarship.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this Scholarship?');">

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

document.getElementById("searchScholarship").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#scholarshipTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

</script>

</body>

</html>
