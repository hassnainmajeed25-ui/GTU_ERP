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
GET SENT MESSAGES
=========================================*/

$stmt=$conn->prepare("

SELECT

student_messages.*,

admins.full_name,

admins.department

FROM student_messages

LEFT JOIN admins
ON admins.id=student_messages.admin_id

WHERE

student_messages.student_id=?

ORDER BY

student_messages.id DESC

");

$stmt->bind_param(

"i",

$student_id

);

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

Sent Messages

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#edf2f7;

font-family:Arial,Helvetica,sans-serif;

}

/*=========================================
PAGE HEADER
=========================================*/

.page-header{

background:linear-gradient(135deg,#0B1F52,#2563eb);

color:#fff;

padding:20px;

border-radius:15px;

margin-bottom:25px;

box-shadow:0 8px 20px rgba(0,0,0,.15);

}

.page-header h3{

margin:0;

font-weight:bold;

}

/*=========================================
CARD
=========================================*/

.card{

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.10);

}

.card-header{

background:#0B1F52;

color:#fff;

font-weight:bold;

font-size:20px;

}

/*=========================================
TABLE
=========================================*/

.table th{

background:#0B1F52;

color:#fff;

text-align:center;

vertical-align:middle;

}

.table td{

vertical-align:middle;

text-align:center;

}

/*=========================================
BADGES
=========================================*/

.badge{

font-size:13px;

padding:8px 12px;

}

/*=========================================
BUTTONS
=========================================*/

.btn-view{

background:#2563eb;

color:#fff;

}

.btn-view:hover{

background:#0B1F52;

color:#fff;

}

</style>

</head>

<body>

<div class="container py-4">

<div class="page-header d-flex justify-content-between align-items-center">

<h3>

<i class="fas fa-paper-plane me-2"></i>

Sent Messages

</h3>

<a

href="dashboard.php"

class="btn btn-light">

<i class="fas fa-arrow-left me-2"></i>

Dashboard

</a>

</div>

<div class="card">

<div class="card-header">

<i class="fas fa-envelope me-2"></i>

Sent Messages History

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Department</th>

<th>Officer</th>

<th>Subject</th>

<th>Date</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if($result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php echo htmlspecialchars($row['department']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['full_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['subject']); ?>

</td>

<td>

<?php

echo date(

"d M Y",

strtotime($row['created_at'])

);

?>

<br>

<small class="text-muted">

<?php

echo date(

"h:i A",

strtotime($row['created_at'])

);

?>

</small>

</td>

<td>

<?php

if($row['status']=="Pending"){

?>

<span class="badge bg-warning text-dark">

Pending

</span>

<?php

}else{

?>

<span class="badge bg-success">

Replied

</span>

<?php

}

?>

<br><br>

<?php

if($row['is_read']==0){

?>

<span class="badge bg-danger">

Unread

</span>

<?php

}else{

?>

<span class="badge bg-primary">

Read

</span>

<?php

}

?>

</td>

<td>

<a

href="view_message.php?id=<?php echo $row['id']; ?>"

class="btn btn-view btn-sm">

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

<td colspan="7" class="text-center py-5">

<i class="fas fa-envelope-open-text"

style="font-size:60px;color:#cfcfcf;"></i>

<h5 class="mt-3">

No Messages Found

</h5>

<p class="text-muted">

You have not sent any messages yet.

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

</div>

<div class="row mt-4">

<div class="col-lg-6">

<a

href="compose_message.php"

class="btn btn-success">

<i class="fas fa-paper-plane me-2"></i>

Compose New Message

</a>

</div>

<div class="col-lg-6 text-end">

<a

href="dashboard.php"

class="btn btn-secondary">

<i class="fas fa-house me-2"></i>

Back To Dashboard

</a>

</div>

</div>

</div>

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">

</script>

</body>

</html>