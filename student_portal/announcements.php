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

$student_id=$_SESSION['student_id'];

/*=========================================
GET ALL ANNOUNCEMENTS
=========================================*/

$sql="

SELECT

id,

title,

message,

notification_type,

created_at,

'Super Admin' AS source

FROM super_admin_notifications

WHERE status='Active'

UNION ALL

SELECT

id,

title,

message,

category AS notification_type,

created_at,

'System' AS source

FROM notifications

WHERE student_id=?

ORDER BY created_at DESC

";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$student_id);

$stmt->execute();

$result=$stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Announcements

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

background:linear-gradient(135deg,#000000,#1b1b1b,#2d2d2d);

color:#fff;

padding:22px 28px;

border-radius:18px;

margin-bottom:30px;

box-shadow:0 8px 20px rgba(0,0,0,.25);

}

.page-header h3{

margin:0;

font-weight:700;

letter-spacing:.5px;

}

.back-btn{

background:#FFD54F;

color:#000;

font-weight:700;

border:none;

padding:10px 22px;

border-radius:10px;

transition:.3s;

}

.back-btn:hover{

background:#FFC107;

color:#000;

transform:translateY(-2px);

}

/*=========================================
CARD
=========================================*/

.card{

border:none;

border-radius:18px;

overflow:hidden;

box-shadow:0 10px 25px rgba(0,0,0,.12);

}

/*=========================================
CARD HEADER
=========================================*/

.card-header{

background:linear-gradient(135deg,#FFD54F,#FFC107);

color:#000;

font-weight:700;

font-size:22px;

padding:18px 22px;

}

/*=========================================
TABLE
=========================================*/

.table{

margin-bottom:0;

}

.table thead th{

background:#111111;

color:#FFD54F;

text-align:center;

vertical-align:middle;

border:none;

padding:16px;

font-size:15px;

}

.table tbody td{

vertical-align:middle;

padding:16px;

border-color:#ececec;

}

/*=========================================
BADGES
=========================================*/

.badge-super{

background:#000;

color:#FFD54F;

padding:8px 14px;

font-size:13px;

border-radius:20px;

}

.badge-system{

background:#FFD54F;

color:#000;

padding:8px 14px;

font-size:13px;

border-radius:20px;

font-weight:700;

}

/*=========================================
TITLE
=========================================*/

.notice-title{

font-weight:700;

font-size:17px;

color:#111;

}

/*=========================================
DATE
=========================================*/

.notice-date{

font-size:14px;

color:#666;

}

/*=========================================
VIEW BUTTON
=========================================*/

.btn-view{

background:#111;

color:#FFD54F;

font-weight:700;

border-radius:10px;

padding:8px 18px;

transition:.3s;

}

.btn-view:hover{

background:#FFD54F;

color:#000;

}

/*=========================================
NO DATA
=========================================*/

.empty-box{

text-align:center;

padding:70px 20px;

}

.empty-box i{

font-size:70px;

color:#c7c7c7;

margin-bottom:15px;

}

.empty-box h4{

font-weight:700;

color:#222;

}

.empty-box p{

color:#777;

}

/*=========================================
RESPONSIVE
=========================================*/

@media(max-width:768px){

.page-header{

text-align:center;

}

.page-header .d-flex{

display:block!important;

}

.back-btn{

margin-top:15px;

}

}

</style>

</head>

<body>

<div class="container py-4">

<div class="page-header d-flex justify-content-between align-items-center">

<h3>

<i class="fas fa-bullhorn me-2"></i>

University Announcements

</h3>

<a

href="dashboard.php"

class="btn back-btn">

<i class="fas fa-arrow-left me-2"></i>

Dashboard

</a>

</div>

<div class="card">

<div class="card-header">

<i class="fas fa-newspaper me-2"></i>

Latest Announcements

</div>

<div class="card-body p-0">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead>

<tr>

<th width="70">

#

</th>

<th>

Type

</th>

<th>

Title

</th>

<th>

Source

</th>

<th>

Date

</th>

<th width="120">

Action

</th>

</tr>

</thead>

<tbody>

<?php

if($result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td class="text-center">

<?php echo $sr++; ?>

</td>

<td class="text-center">

<?php

if($row['source']=="Super Admin"){

?>

<span class="badge-super">

<i class="fas fa-user-shield me-1"></i>

Super Admin

</span>

<?php

}else{

?>

<span class="badge-system">

<i class="fas fa-graduation-cap me-1"></i>

System

</span>

<?php

}

?>

</td>

<td>

<div class="notice-title">

<?php echo htmlspecialchars($row['title']); ?>

</div>

<small class="text-muted">

<?php echo htmlspecialchars($row['notification_type']); ?>

</small>

</td>

<td class="text-center">

<?php echo htmlspecialchars($row['source']); ?>

</td>

<td class="text-center">

<div class="notice-date">

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

<br>

<small>

<?php echo date("h:i A",strtotime($row['created_at'])); ?>

</small>

</div>

</td>

<td class="text-center">

<a

href="announcement_details.php?type=<?php echo urlencode($row['source']); ?>&id=<?php echo $row['id']; ?>"

class="btn btn-view">

<i class="fas fa-eye me-1"></i>

View

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6">

<div class="empty-box">

<i class="fas fa-bullhorn"></i>

<h4>

No Announcements Available

</h4>

<p>

There are no announcements available at the moment.

Please check again later.

</p>

</div>

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

<div class="row mt-4">

<div class="col-md-6">

<a

href="dashboard.php"

class="btn back-btn">

<i class="fas fa-arrow-left me-2"></i>

Back To Dashboard

</a>

</div>

<div class="col-md-6 text-end">

<span class="text-muted">

<i class="fas fa-database me-2"></i>

Showing latest university announcements

</span>

</div>

</div>

</div>

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">

</script>

</body>

</html>