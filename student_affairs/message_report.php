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

if($_SESSION['department']!="Student Affairs"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*=========================================
SEARCH
=========================================*/

$search="";

$where=" WHERE 1 ";

if(isset($_GET['search']) && $_GET['search']!=""){

    $search=trim($_GET['search']);

    $search=$conn->real_escape_string($search);

    $where.=" AND (

        subject LIKE '%$search%'

        OR message LIKE '%$search%'

        OR sender_role LIKE '%$search%'

        OR receiver_role LIKE '%$search%'

    )";

}

/*=========================================
GET MESSAGES
=========================================*/

$sql="

SELECT *

FROM messages

$where

ORDER BY id DESC

";

$result=$conn->query($sql);

/*=========================================
STATISTICS
=========================================*/

$totalMessages=$conn->query("
SELECT COUNT(*) total
FROM messages
")->fetch_assoc()['total'];

$readMessages=$conn->query("
SELECT COUNT(*) total
FROM messages
WHERE is_read=1
")->fetch_assoc()['total'];

$unreadMessages=$conn->query("
SELECT COUNT(*) total
FROM messages
WHERE is_read=0
")->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Message Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef4fb;

font-family:'Segoe UI',sans-serif;

}

.main{

padding:30px;

}

.report-header{

background:linear-gradient(135deg,#0B1F52,#2563eb);

padding:30px;

border-radius:25px;

color:#fff;

box-shadow:0 12px 30px rgba(0,0,0,.18);

margin-bottom:30px;

}

.logo{

width:75px;

}

.admin-box{

background:#fff;

color:#000;

padding:18px;

border-radius:18px;

box-shadow:0 5px 20px rgba(0,0,0,.15);

}

.stat-card{

border:none;

border-radius:20px;

overflow:hidden;

box-shadow:0 8px 20px rgba(0,0,0,.10);

transition:.3s;

}

.stat-card:hover{

transform:translateY(-5px);

}

.icon-circle{

width:80px;

height:80px;

border-radius:50%;

display:flex;

align-items:center;

justify-content:center;

margin:auto;

font-size:34px;

color:#fff;

}

.blue{

background:#2563eb;

}

.green{

background:#10b981;

}

.orange{

background:#f59e0b;

}

.card-title{

font-size:34px;

font-weight:bold;

margin-top:15px;

}

.card-text{

font-size:16px;

font-weight:600;

color:#6b7280;

}

</style>

</head>

<body>

<div class="container-fluid main">

<div class="report-header">

<div class="row align-items-center">

<div class="col-lg-8">

<div class="d-flex align-items-center">

<img src="../assets/images/logo.png"

class="logo me-4"

alt="GTU Logo">

<div>

<h1 class="fw-bold">

Message Report Dashboard

</h1>

<h5 class="opacity-75">

Global Tech University ERP

</h5>

<p class="mb-0">

<i class="fas fa-calendar-alt me-2"></i>

<?php echo date("l, d F Y"); ?>

</p>

</div>

</div>

</div>

<div class="col-lg-4 text-end">

<div class="admin-box">

<h5>

<i class="fas fa-user-shield text-primary me-2"></i>

<?php echo htmlspecialchars($admin_name); ?>

</h5>

<small class="text-muted">

Student Affairs Office

</small>

</div>

</div>

</div>

</div>

<div class="row">

<div class="col-lg-4 col-md-6 mb-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="icon-circle blue">

<i class="fas fa-envelope"></i>

</div>

<h2 class="card-title">

<?php echo $totalMessages; ?>

</h2>

<p class="card-text">

Total Messages

</p>

</div>

</div>

</div>

<div class="col-lg-4 col-md-6 mb-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="icon-circle green">

<i class="fas fa-envelope-open"></i>

</div>

<h2 class="card-title text-success">

<?php echo $readMessages; ?>

</h2>

<p class="card-text">

Read Messages

</p>

</div>

</div>

</div>

<div class="col-lg-4 col-md-6 mb-4">

<div class="card stat-card">

<div class="card-body text-center">

<div class="icon-circle orange">

<i class="fas fa-envelope-open-text"></i>

</div>

<h2 class="card-title text-warning">

<?php echo $unreadMessages; ?>

</h2>

<p class="card-text">

Unread Messages

</p>

</div>

</div>

</div>

</div>

<!-- ==========================
SEARCH PANEL
========================== -->

<div class="card border-0 shadow-lg rounded-4 mb-4">

<div class="card-body">

<div class="row align-items-end">

<div class="col-lg-8">

<form method="GET">

<label class="form-label fw-bold">

<i class="fas fa-search me-2 text-primary"></i>

Search Message

</label>

<div class="input-group">

<input

type="text"

name="search"

class="form-control form-control-lg"

placeholder="Search by Subject, Message, Sender or Receiver"

value="<?php echo htmlspecialchars($search); ?>">

<button

type="submit"

class="btn btn-primary btn-lg">

<i class="fas fa-search me-2"></i>

Search

</button>

</div>

</form>

</div>

<div class="col-lg-4 text-end">

<a

href="export_message_pdf.php"

class="btn btn-danger btn-lg me-2">

<i class="fas fa-file-pdf me-2"></i>

Export PDF

</a>

<button

onclick="window.print();"

class="btn btn-success btn-lg">

<i class="fas fa-print me-2"></i>

Print

</button>

</div>

</div>

</div>

</div>

<!-- ==========================
MESSAGE TABLE
========================== -->

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">

<div class="d-flex justify-content-between align-items-center">

<h3 class="mb-0">

<i class="fas fa-envelope me-2"></i>

Message Records

</h3>

<span class="badge bg-light text-dark fs-6">

Total :

<?php echo $totalMessages; ?>

</span>

</div>

</div>

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Sender</th>

<th>Receiver</th>

<th>Subject</th>

<th>Message</th>

<th>Status</th>

<th>Date</th>

<th class="text-center">

Action

</th>

</tr>

</thead>

<tbody>

<?php

$sr=1;

if($result && $result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<span class="badge bg-primary px-3 py-2">

<?php echo htmlspecialchars($row['sender_role']); ?>

</span>

</td>

<td>

<span class="badge bg-info text-dark px-3 py-2">

<?php echo htmlspecialchars($row['receiver_role']); ?>

</span>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['subject']); ?>

</strong>

</td>

<td>

<?php

$text=$row['message'];

if(strlen($text)>70){

echo htmlspecialchars(substr($text,0,70))."...";

}else{

echo htmlspecialchars($text);

}

?>

</td>

<td>

<?php

if($row['is_read']==1){

?>

<span class="badge bg-success px-3 py-2">

<i class="fas fa-envelope-open me-1"></i>

Read

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark px-3 py-2">

<i class="fas fa-envelope me-1"></i>

Unread

</span>

<?php

}

?>

</td>

<td>

<?php

echo date("d M Y",strtotime($row['created_at']));

?>

</td>

<td class="text-center">

<a

href="view_message.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm"

title="View Message">

<i class="fas fa-eye"></i>

</a>

<a

href="print_message.php?id=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm"

title="Print Message">

<i class="fas fa-print"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8" class="text-center py-5">

<i class="fas fa-envelope-open-text fa-5x text-secondary mb-3"></i>

<h3 class="text-muted">

No Messages Found

</h3>

<p class="text-muted">

There are currently no messages available.

</p>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

<!-- ==========================
REPORT SUMMARY
========================== -->

<div class="row mt-5">

<div class="col-lg-8">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#198754,#20c997);">

<h4 class="mb-0">

<i class="fas fa-chart-pie me-2"></i>

Message Report Summary

</h4>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-4">

<i class="fas fa-envelope fa-3x text-primary mb-3"></i>

<h3>

<?php echo $totalMessages; ?>

</h3>

<p>Total Messages</p>

</div>

<div class="col-md-4">

<i class="fas fa-envelope-open fa-3x text-success mb-3"></i>

<h3>

<?php echo $readMessages; ?>

</h3>

<p>Read Messages</p>

</div>

<div class="col-md-4">

<i class="fas fa-envelope-open-text fa-3x text-warning mb-3"></i>

<h3>

<?php echo $unreadMessages; ?>

</h3>

<p>Unread Messages</p>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header text-white"

style="background:linear-gradient(90deg,#0B1F52,#2563eb);">

<h4 class="mb-0">

<i class="fas fa-bolt me-2"></i>

Quick Actions

</h4>

</div>

<div class="card-body d-grid gap-3">

<a

href="export_message_pdf.php"

class="btn btn-danger btn-lg">

<i class="fas fa-file-pdf me-2"></i>

Export PDF

</a>

<button

onclick="window.print();"

class="btn btn-success btn-lg">

<i class="fas fa-print me-2"></i>

Print Report

</button>

<a

href="student_affairs_dashboard.php"

class="btn btn-dark btn-lg">

<i class="fas fa-arrow-left me-2"></i>

Back to Dashboard

</a>

</div>

</div>

</div>

</div>

<!-- ==========================
FOOTER
========================== -->

<footer class="mt-5">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<img

src="../assets/images/logo.png"

width="90"

class="mb-3"

alt="GTU Logo">

<h3 class="fw-bold text-primary">

Global Tech University ERP

</h3>

<p class="text-muted">

Student Affairs Office - Message Report Management

</p>

<hr>

<div class="row">

<div class="col-md-4">

<i class="fas fa-envelope fa-2x text-primary mb-2"></i>

<h6>Message Management</h6>

<small class="text-muted">

Official Internal Communication System

</small>

</div>

<div class="col-md-4">

<i class="fas fa-shield-alt fa-2x text-success mb-2"></i>

<h6>Secure ERP System</h6>

<small class="text-muted">

Protected Administrative Access

</small>

</div>

<div class="col-md-4">

<i class="fas fa-university fa-2x text-warning mb-2"></i>

<h6>Global Tech University</h6>

<small class="text-muted">

Digital Campus ERP

</small>

</div>

</div>

<hr>

<div class="row">

<div class="col-md-6 text-start">

<strong>

Generated By :

</strong>

<?php echo htmlspecialchars($admin_name); ?>

</div>

<div class="col-md-6 text-end">

<strong>

Generated On :

</strong>

<?php echo date("d F Y h:i A"); ?>

</div>

</div>

<hr>

<div class="row text-center">

<div class="col-md-3">

<i class="fas fa-envelope text-primary fa-2x mb-2"></i>

<h6>Total Messages</h6>

<strong>

<?php echo $totalMessages; ?>

</strong>

</div>

<div class="col-md-3">

<i class="fas fa-envelope-open text-success fa-2x mb-2"></i>

<h6>Read</h6>

<strong>

<?php echo $readMessages; ?>

</strong>

</div>

<div class="col-md-3">

<i class="fas fa-envelope-open-text text-warning fa-2x mb-2"></i>

<h6>Unread</h6>

<strong>

<?php echo $unreadMessages; ?>

</strong>

</div>

<div class="col-md-3">

<i class="fas fa-calendar-check text-info fa-2x mb-2"></i>

<h6>Report Date</h6>

<strong>

<?php echo date("d-m-Y"); ?>

</strong>

</div>

</div>

<hr>

<p class="small text-secondary mb-0">

© <?php echo date("Y"); ?>

Global Tech University ERP System.

All Rights Reserved.

Version 1.0 | Student Affairs Department

</p>

</div>

</div>

</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
