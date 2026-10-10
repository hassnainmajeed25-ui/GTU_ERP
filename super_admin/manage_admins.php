<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){
    header("Location: login.php");
    exit;
}

$admin_name=$_SESSION['super_admin_name'];

/*=============================
GET ADMINS
=============================*/

$search="";

if(isset($_GET['search'])){
    $search=trim($_GET['search']);
}

if($search!=""){

    $sql=$conn->prepare("

    SELECT *

    FROM admins

    WHERE

    full_name LIKE ?

    OR

    email LIKE ?

    OR

    department LIKE ?

    ORDER BY id DESC

    ");

    $like="%".$search."%";

    $sql->bind_param("sss",$like,$like,$like);

}else{

    $sql=$conn->prepare("

    SELECT *

    FROM admins

    ORDER BY id DESC

    ");

}

$sql->execute();

$result=$sql->get_result();

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Admins</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.sidebar{

position:fixed;

left:0;

top:0;

width:260px;

height:100vh;

background:#0B1F52;

padding-top:20px;

}

.sidebar h3{

color:#fff;

text-align:center;

margin-bottom:25px;

}

.sidebar a{

display:block;

padding:14px 20px;

color:#fff;

text-decoration:none;

}

.sidebar a:hover{

background:#16367d;

}

.main{

margin-left:260px;

padding:30px;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 4px 15px rgba(0,0,0,.12);

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

<a href="manage_admins.php">
<i class="fas fa-user-shield me-2"></i>
Manage Admins
</a>

<a href="#">
<i class="fas fa-building me-2"></i>
Departments
</a>

<a href="#">
<i class="fas fa-user-graduate me-2"></i>
Students
</a>

<a href="logout.php">
<i class="fas fa-sign-out-alt me-2"></i>
Logout
</a>

</div>

<div class="main">

<div class="card">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h4 class="mb-0">

Manage Admins

</h4>

<a href="add_admin.php" class="btn btn-light">

<i class="fas fa-plus"></i>

Add New Admin

</a>

</div>

<div class="card-body">

<form method="GET" class="mb-3">

<div class="row">

<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search by Name, Email or Department"
value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">

Search

</button>

</div>

</div>

</form>
<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th width="70">ID</th>

<th>Full Name</th>

<th>Email</th>

<th>Mobile</th>

<th>Department</th>

<th>Status</th>

<th width="180">Actions</th>

</tr>

</thead>

<tbody>

<?php

if($result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['full_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['email']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['mobile']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['department']); ?>

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

<a href="edit_admin.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<?php

if($row['status']=="Active"){

?>

<a

href="change_admin_status.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm"

onclick="return confirm('Deactivate this admin?')">

<i class="fas fa-user-lock"></i>

Deactivate

</a>

<?php

}else{

?>

<a

href="change_admin_status.php?id=<?php

if($row['status']=="Active"){

echo '<span class="badge bg-success">Active</span>';

}else{

echo '<span class="badge bg-danger">Inactive</span>';

}

?>"

class="btn btn-success btn-sm"

onclick="return confirm('Activate this admin?')">

<i class="fas fa-user-check"></i>

Activate

</a>

<?php

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center text-muted">

No Admin Found.

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>