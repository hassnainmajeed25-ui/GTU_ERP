<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

header("Location: ../admins/login.php");
exit;

}

if($_SESSION['department']!="Academic Office"){

header("Location: ../admins/login.php");
exit;

}

/*=========================================
LOAD ANNOUNCEMENTS
=========================================*/

$sql=$conn->prepare("

SELECT

id,
title,
department,
status,
created_at

FROM announcements

WHERE

department='Teacher'

OR

department='All'

ORDER BY id DESC

");

$sql->execute();

$result=$sql->get_result();

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>

Manage Teacher Announcements

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#edf2f7;

}

.card{

margin-top:30px;

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.10);

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h3>

<i class="fas fa-bullhorn me-2"></i>

Manage Teacher Announcements

</h3>

<a

href="add_announcement.php"

class="btn btn-light">

<i class="fas fa-plus me-2"></i>

Add Announcement

</a>

</div>

<div class="card-body">

<?php

if(isset($_SESSION['success'])){

?>

<div class="alert alert-success">

<?php

echo $_SESSION['success'];

unset($_SESSION['success']);

?>

</div>

<?php

}

?>

<?php

if(isset($_SESSION['error'])){

?>

<div class="alert alert-danger">

<?php

echo $_SESSION['error'];

unset($_SESSION['error']);

?>

</div>

<?php

}

?>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th width="70">

ID

</th>

<th>

Title

</th>

<th width="150">

Department

</th>

<th width="150">

Status

</th>

<th width="170">

Created

</th>

<th width="180">

Action

</th>

</tr>

</thead>

<tbody>

<?php

while($row = $result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $row['id']; ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['title']); ?>

</strong>

</td>

<td>

<span class="badge bg-info">

<?php echo htmlspecialchars($row['department']); ?>

</span>

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

<?php

echo date(

"d M Y",

strtotime($row['created_at'])

);

?>

</td>

<td>

<a

href="edit_announcement.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<a

href="delete_announcement.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this announcement?')">

<i class="fas fa-trash"></i>

Delete

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>