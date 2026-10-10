<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

$result=$conn->query("SELECT * FROM faculties ORDER BY id ASC");

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Manage Faculties</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

</head>

<body style="background:#f5f6fa;">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

<i class="fas fa-building"></i>

Manage Faculties

</h4>

</div>

<div class="card-body">

<?php

$total=$conn->query("SELECT COUNT(*) total FROM faculties");

$count=$total->fetch_assoc();

?>

<div class="alert alert-primary">

<b>Total Faculties :</b>

<?php echo $count['total']; ?>

</div>

<div class="row mb-3">

<div class="col-md-6">

<a href="add_faculty.php"
class="btn btn-success">

<i class="fas fa-plus"></i>

Add New Faculty

</a>

</div>

<div class="col-md-6">

<input
type="text"
id="searchFaculty"
class="form-control"
placeholder="Search Faculty...">

</div>

</div>

<table
id="facultyTable"
class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Faculty Name</th>

<th>Status</th>

<th width="250">Action</th>

</tr>

</thead>

<tbody>

<?php while($row=$result->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['faculty_name']; ?></td>

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

<a href="edit_faculty.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<a href="change_faculty_status.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">

<i class="fas fa-sync"></i>

Status

</a>

<a href="delete_faculty.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"

onclick="return confirm('Delete this Faculty?');">

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

document.getElementById('searchFaculty').addEventListener('keyup',function(){

var value=this.value.toLowerCase();

var rows=document.querySelectorAll('#facultyTable tbody tr');

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)
? ''
: 'none';

});

});

</script>

</body>

</html>