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

$page_title="Sent Messages";

$admin_id=$_SESSION['admin_id'];


/*=========================================
LOAD SENT MESSAGES
=========================================*/

$sql="

SELECT

m.id,
m.receiver_role,
m.receiver_id,
m.subject,
m.message,
m.created_at,

CASE

WHEN m.receiver_role='Teacher'

THEN

(

SELECT CONCAT(employee_id,' | ',teacher_name)

FROM teachers

WHERE id=m.receiver_id

)

WHEN m.receiver_role='Super Admin'

THEN

(

SELECT full_name

FROM super_admin

WHERE id=m.receiver_id

)

ELSE

m.receiver_role

END AS receiver_name

FROM messages m

WHERE

m.sender_role='Academic Office'

AND

m.sender_id=?

ORDER BY m.created_at DESC

";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(

$stmt,

"i",

$admin_id

);

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

background:#eef5f3;

font-family:'Segoe UI',sans-serif;

}

.header-card{

background:linear-gradient(135deg,#198754,#20c997);

color:white;

border-radius:18px;

box-shadow:0 8px 25px rgba(25,135,84,.25);

}

.logo{

height:80px;

}

.page-title{

font-size:30px;

font-weight:700;

}

.stat-card{

border:none;

border-radius:16px;

box-shadow:0 3px 15px rgba(0,0,0,.08);

transition:.3s;

}

.stat-card:hover{

transform:translateY(-5px);

}

.stat-icon{

font-size:35px;

}

.table-card{

border:none;

border-radius:18px;

overflow:hidden;

box-shadow:0 3px 15px rgba(0,0,0,.08);

}

.table thead{

background:#198754;

color:#fff;

}

.badge-delivered{

background:#198754;

font-size:13px;

padding:8px 12px;

}

.receiver-box{

font-weight:600;

}

.receiver-role{

font-size:12px;

color:#777;

}

.btn-view{

background:#0d6efd;

color:#fff;

}

.btn-view:hover{

background:#0b5ed7;

color:#fff;

}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="header-card p-4 mb-4">

<div class="row align-items-center">

<div class="col-md-2 text-center">

<img src="../assets/images/logo.png" class="logo">

</div>

<div class="col-md-10">

<h2 class="page-title">

<i class="fa-solid fa-paper-plane me-2"></i>

Sent Messages

</h2>

<p class="mb-0">

Global Tech University ERP • Academic Office

</p>

</div>

</div>

</div>

<?php

$total=mysqli_num_rows($result);

mysqli_data_seek($result,0);

$teacher=0;

$superadmin=0;

while($row=mysqli_fetch_assoc($result)){

if($row['receiver_role']=="Teacher"){

$teacher++;

}else if($row['receiver_role']=="Super Admin"){

$superadmin++;

}

}

mysqli_data_seek($result,0);

?>

<div class="row mb-4">

<div class="col-md-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="stat-icon text-success">

<i class="fa-solid fa-paper-plane"></i>

</div>

<h5 class="mt-2">

Total Sent

</h5>

<h2>

<?php echo $total; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="stat-icon text-primary">

<i class="fa-solid fa-chalkboard-user"></i>

</div>

<h5 class="mt-2">

Teachers

</h5>

<h2>

<?php echo $teacher; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="stat-icon text-warning">

<i class="fa-solid fa-user-shield"></i>

</div>

<h5 class="mt-2">

Super Admin

</h5>

<h2>

<?php echo $superadmin; ?>

</h2>

</div>

</div>

</div>

</div>

<!-- SENT MESSAGE TABLE -->

<div class="card table-card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fa-solid fa-paper-plane me-2"></i>

Sent Messages List

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead>

<tr>

<th width="70">#</th>

<th>Receiver</th>

<th>Subject</th>

<th width="180">Date</th>

<th width="140">Status</th>

<th width="120">Action</th>

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

<strong>

<?php echo $count++; ?>

</strong>

</td>

<td>

<div class="receiver-box">

<i class="fa-solid fa-user text-success me-2"></i>

<?php echo htmlspecialchars($row['receiver_name']); ?>

</div>

<div class="receiver-role">

<?php echo htmlspecialchars($row['receiver_role']); ?>

</div>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['subject']); ?>

</strong>

<br>

<small class="text-muted">

<?php

echo substr(strip_tags($row['message']),0,60);

?>...

</small>

</td>

<td>

<span class="badge bg-light text-dark">

<i class="fa-solid fa-calendar-days me-1"></i>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</span>

<br>

<small class="text-muted">

<?php echo date("h:i A",strtotime($row['created_at'])); ?>

</small>

</td>

<td>

<span class="badge badge-delivered">

<i class="fa-solid fa-circle-check me-1"></i>

Delivered

</span>

</td>

<td>

<a

href="view_sent_message.php?id=<?php echo $row['id']; ?>"

class="btn btn-view btn-sm">

<i class="fa-solid fa-eye"></i>

View

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center text-danger py-4">

<i class="fa-solid fa-envelope-open-text fa-2x mb-3"></i>

<br>

No Sent Messages Found

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

<!-- BOTTOM ACTION BUTTONS -->

<div class="row mt-4">

<div class="col-md-12 text-center">

<a
href="compose_message.php"
class="btn btn-success btn-lg me-2">

<i class="fa-solid fa-paper-plane me-2"></i>

Compose New Message

</a>

<a
href="inbox.php"
class="btn btn-primary btn-lg me-2">

<i class="fa-solid fa-inbox me-2"></i>

Inbox

</a>

<a
href="dashboard.php"
class="btn btn-dark btn-lg">

<i class="fa-solid fa-house me-2"></i>

Dashboard

</a>

</div>

</div>


<!-- FOOTER -->

<div class="text-center mt-5 mb-3">

<hr>

<p class="text-muted mb-1">

<img
src="../assets/images/logo.png"
style="height:28px;">

<strong>

Global Tech University ERP

</strong>

</p>

<small class="text-secondary">

Academic Office • Sent Messages Module

</small>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>