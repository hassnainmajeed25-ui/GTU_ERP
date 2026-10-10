<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}

/*=========================================
CHECK ID
=========================================*/

if(!isset($_GET['id']) || !isset($_GET['type'])){

    header("Location: announcements.php");
    exit;

}

$id=(int)$_GET['id'];

$type=$_GET['type'];

/*=========================================
GET ANNOUNCEMENT
=========================================*/

if($type=="Super Admin"){

$sql="

SELECT

id,
title,
message,
notification_type,
created_at,
'Super Admin' AS source

FROM super_admin_notifications

WHERE id=?

AND status='Active'

LIMIT 1

";

}else{

$sql="

SELECT

id,
title,
message,
category AS notification_type,
created_at,
'System' AS source

FROM notifications

WHERE id=?

LIMIT 1

";

}

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

    header("Location: announcements.php");
    exit;

}

$row=$result->fetch_assoc();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Announcement Details

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

/*=========================================
BODY
=========================================*/

body{

background:#f4f4f4;

font-family:'Segoe UI',sans-serif;

}

/*=========================================
PAGE HEADER
=========================================*/

.page-header{

background:linear-gradient(135deg,#000000,#1d1d1d,#0B3D91);

padding:22px 30px;

border-radius:18px;

color:#fff;

margin-bottom:30px;

box-shadow:0 10px 25px rgba(0,0,0,.18);

}

.page-header h3{

margin:0;

font-weight:700;

}

/*=========================================
DETAIL CARD
=========================================*/

.detail-card{

background:#fff;

border:none;

border-radius:20px;

overflow:hidden;

box-shadow:0 10px 25px rgba(0,0,0,.12);

}

/*=========================================
CARD HEADER
=========================================*/

.detail-header{

background:linear-gradient(135deg,#FFD54F,#FFC107);

padding:22px;

text-align:center;

}

.detail-header i{

font-size:50px;

color:#000;

margin-bottom:12px;

}

.detail-title{

font-size:32px;

font-weight:800;

color:#111;

margin-bottom:8px;

}

.detail-type{

display:inline-block;

background:#111;

color:#FFD54F;

padding:8px 20px;

border-radius:30px;

font-weight:700;

font-size:14px;

}

/*=========================================
BODY
=========================================*/

.detail-body{

padding:35px;

}

.info-box{

background:#fafafa;

border-left:6px solid #FFD54F;

padding:18px;

border-radius:12px;

margin-bottom:25px;

}

.info-label{

font-weight:700;

color:#000;

margin-bottom:5px;

}

.info-value{

color:#555;

font-size:16px;

}

/*=========================================
MESSAGE
=========================================*/

.message-box{

background:#ffffff;

border:2px solid #FFD54F;

border-radius:15px;

padding:25px;

font-size:17px;

line-height:1.9;

color:#444;

white-space:pre-line;

}

/*=========================================
BUTTON
=========================================*/

.back-btn{

background:#111;

color:#FFD54F;

padding:12px 28px;

border-radius:10px;

font-weight:700;

transition:.3s;

}

.back-btn:hover{

background:#FFD54F;

color:#000;

}

/*=========================================
RESPONSIVE
=========================================*/

@media(max-width:768px){

.detail-title{

font-size:24px;

}

.detail-body{

padding:20px;

}

}

</style>

</head>

<body>

<div class="container py-4">

<div class="page-header d-flex justify-content-between align-items-center">

<h3>

<i class="fas fa-bullhorn me-2"></i>

Announcement Details

</h3>

<a

href="announcements.php"

class="btn back-btn">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

<div class="detail-card">

<div class="detail-header">

<i class="fas fa-bullhorn"></i>

<div class="detail-title">

<?php echo htmlspecialchars($row['title']); ?>

</div>

<div class="detail-type">

<?php echo htmlspecialchars($row['notification_type']); ?>

</div>

</div>

<div class="detail-body">

<div class="row mb-4">

<div class="col-md-6">

<div class="info-box">

<div class="info-label">

<i class="fas fa-building me-2"></i>

Source

</div>

<div class="info-value">

<?php echo htmlspecialchars($row['source']); ?>

</div>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<div class="info-label">

<i class="fas fa-calendar-alt me-2"></i>

Published Date

</div>

<div class="info-value">

<?php echo date("d F Y",strtotime($row['created_at'])); ?>

<br>

<small>

<?php echo date("h:i A",strtotime($row['created_at'])); ?>

</small>

</div>

</div>

</div>

</div>

<h4 class="mb-3 fw-bold text-dark">

<i class="fas fa-file-lines me-2 text-warning"></i>

Notification Message

</h4>

<div class="message-box">

<?php echo nl2br(htmlspecialchars($row['message'])); ?>

</div>

<div class="text-center mt-5">

<a

href="announcements.php"

class="btn back-btn">

<i class="fas fa-arrow-left me-2"></i>

Back To Announcements

</a>

</div>

</div>

</div>

</div>

<footer class="mt-5">

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-6">

<div class="d-flex align-items-center">

<img

src="../assets/images/logo.png"

style="height:55px;"

class="me-3">

<div>

<h5 class="mb-1 text-dark">

Global Tech University ERP

</h5>

<p class="mb-0 text-muted">

Student Announcement Portal

</p>

</div>

</div>

</div>

<div class="col-md-6 text-md-end mt-3 mt-md-0">

<p class="mb-1">

<strong>

Version 1.0

</strong>

</p>

<p class="mb-1 text-muted">

Announcement Management System

</p>

<p class="mb-0 text-secondary">

© <?php echo date("Y"); ?> Global Tech University

</p>

</div>

</div>

</div>

</div>

</footer>

</div>

<script

src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">

</script>

</body>

</html>