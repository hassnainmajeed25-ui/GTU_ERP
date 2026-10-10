<?php

session_start();

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

$page_title="Inbox Messages";


/*=========================================
CURRENT USER
=========================================*/

$admin_id=$_SESSION['admin_id'];


/*=========================================
LOAD INBOX MESSAGES
=========================================*/

$sql="

SELECT

m.id,
m.sender_role,
m.sender_id,
m.subject,
m.message,
m.is_read,
m.created_at,

CASE

WHEN m.sender_role='Teacher'

THEN

(
SELECT CONCAT(employee_id,' | ',teacher_name)

FROM teachers

WHERE id=m.sender_id
)

WHEN m.sender_role='Super Admin'

THEN

(
SELECT full_name

FROM super_admin

WHERE id=m.sender_id
)

ELSE

m.sender_role

END AS sender_name

FROM messages m

WHERE

m.receiver_role='Academic Office'

AND

m.receiver_id=?

ORDER BY m.created_at DESC

";


$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt,"i",$admin_id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

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

background:#f4f6f9;

font-family:'Segoe UI',sans-serif;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 3px 12px rgba(0,0,0,.08);

}

.page-title{

font-size:28px;

font-weight:700;

color:#0d6efd;

}

.logo{

height:70px;

}

.table th{

background:#0d6efd;

color:#fff;

}

.badge-new{

background:#dc3545;

}

.badge-read{

background:#198754;

}

</style>

</head>

<body>

<div class="container-fluid py-4">

<!-- HEADER -->

<div class="card mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-2 text-center">

<img src="../assets/images/logo.png" class="logo">

</div>

<div class="col-md-10">

<h2 class="page-title">

<i class="fa-solid fa-envelope me-2"></i>

Inbox Messages

</h2>

<p class="text-muted mb-0">

Global Tech University ERP - Academic Office

</p>

</div>

</div>

</div>

</div>

<?php

$total_messages=mysqli_num_rows($result);

mysqli_data_seek($result,0);

$unread=0;
$read=0;

while($row=mysqli_fetch_assoc($result)){

if($row['is_read']==0){

$unread++;

}else{

$read++;

}

}

mysqli_data_seek($result,0);

?>

<div class="row mb-4">

<div class="col-md-4">

<div class="card">

<div class="card-body text-center">

<h6>Total Messages</h6>

<h2 class="text-primary">

<?php echo $total_messages; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card">

<div class="card-body text-center">

<h6>Unread</h6>

<h2 class="text-danger">

<?php echo $unread; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card">

<div class="card-body text-center">

<h6>Read</h6>

<h2 class="text-success">

<?php echo $read; ?>

</h2>

</div>

</div>

</div>

</div>

<!-- INBOX TABLE -->

<div class="card">

<div class="card-header bg-primary text-white">

<i class="fa-solid fa-inbox me-2"></i>

Inbox Messages

</div>


<div class="card-body">


<div class="table-responsive">


<table class="table table-bordered table-hover align-middle">


<thead>


<tr>

<th width="60">

#

</th>

<th>

From

</th>

<th>

Subject

</th>

<th width="180">

Date & Time

</th>

<th width="120">

Status

</th>

<th width="120">

Action

</th>

</tr>


</thead>


<tbody>


<?php

$count=1;

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>


<tr>


<td>

<?php echo $count++; ?>

</td>


<td>

<b>

<?php echo $row['sender_name']; ?>

</b>

<br>

<small class="text-muted">

<?php echo $row['sender_role']; ?>

</small>

</td>


<td>

<?php echo htmlspecialchars($row['subject']); ?>

</td>


<td>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

<br>

<small class="text-muted">

<?php echo date("h:i A",strtotime($row['created_at'])); ?>

</small>

</td>


<td>

<?php

if($row['is_read']==0){

?>

<span class="badge bg-danger">

New

</span>

<?php

}else{

?>

<span class="badge bg-success">

Read

</span>

<?php

}

?>

</td>


<td>

<a

href="view_message.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fa fa-eye"></i>

View

</a>

</td>


</tr>


<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center text-danger">

No Messages Found

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

<!-- ACTION BUTTONS -->

<div class="card mt-4">

<div class="card-body text-center">

<a href="compose_message.php" class="btn btn-success">

<i class="fa-solid fa-paper-plane"></i>

Compose Message

</a>

<a href="dashboard.php" class="btn btn-secondary ms-2">

<i class="fa-solid fa-arrow-left"></i>

Back To Dashboard

</a>

</div>

</div>


</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>