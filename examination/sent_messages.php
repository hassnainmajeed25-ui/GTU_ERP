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

if($_SESSION['department']!="Examination Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_id=$_SESSION['admin_id'];
$admin_name=$_SESSION['admin_name'];

/*=========================================
GET SENT MESSAGES
=========================================*/

$sql="

SELECT

messages.*,

students.first_name,
students.last_name,

admins.full_name

FROM messages

LEFT JOIN students

ON students.id=messages.receiver_id

AND messages.receiver_role='Student'

LEFT JOIN admins

ON admins.id=messages.receiver_id

AND messages.receiver_role='Super Admin'

WHERE

messages.sender_role='Examination Office'

AND

messages.sender_id=?

ORDER BY messages.created_at DESC

";

$stmt=$conn->prepare($sql);

$stmt->bind_param(

"i",

$admin_id

);

$stmt->execute();

$result=$stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Sent Messages</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#eef2f7;
font-family:Arial,Helvetica,sans-serif;
}

.sidebar{
width:260px;
height:100vh;
background:#0B1F52;
position:fixed;
left:0;
top:0;
overflow:auto;
}

.sidebar h3{
color:#fff;
text-align:center;
padding:20px;
border-bottom:1px solid rgba(255,255,255,.15);
}

.sidebar a{
display:block;
padding:14px 20px;
color:#fff;
text-decoration:none;
transition:.3s;
}

.sidebar a:hover{
background:#173a8b;
}

.sidebar a.active{
background:#2563eb;
}

.main{
margin-left:260px;
padding:30px;
}

.topbar{
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 3px 10px rgba(0,0,0,.1);
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 3px 10px rgba(0,0,0,.1);
}

.table th{
background:#0B1F52;
color:#fff;
vertical-align:middle;
}

.table td{
vertical-align:middle;
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

<a href="exam_schedule.php">
<i class="fas fa-calendar-alt me-2"></i>
Exam Schedule
</a>

<a href="marks_management.php">
<i class="fas fa-square-poll-vertical me-2"></i>
Marks Management
</a>

<a href="results.php">
<i class="fas fa-award me-2"></i>
Results
</a>

<a href="reports.php">
<i class="fas fa-chart-bar me-2"></i>
Reports
</a>

<a href="compose_message.php">
<i class="fas fa-paper-plane me-2"></i>
Compose Message
</a>

<a href="inbox.php">
<i class="fas fa-envelope me-2"></i>
Inbox
</a>

<a href="sent_messages.php" class="active">
<i class="fas fa-paper-plane me-2"></i>
Sent Messages
</a>

<a href="profile.php">
<i class="fas fa-user-circle me-2"></i>
Profile
</a>

<a href="../admins/logout.php">
<i class="fas fa-sign-out-alt me-2"></i>
Logout
</a>

</div>

<div class="main">

<div class="topbar">

<div>

<h3>

Sent Messages

</h3>

<small class="text-muted">

Examination Office

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-paper-plane me-2"></i>

Sent Messages

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th width="5%">#</th>

<th width="22%">Receiver</th>

<th>Subject</th>

<th width="18%">Date</th>

<th width="12%">Status</th>

<th width="12%">Action</th>

</tr>

</thead>

<tbody>

<?php

if($result && $result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php

if($row['receiver_role']=="Super Admin"){

echo "<span class='badge bg-primary me-2'>Super Admin</span><br>";

echo htmlspecialchars($row['full_name']);

}

elseif($row['receiver_role']=="Student"){

echo "<span class='badge bg-success me-2'>Student</span><br>";

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

}

else{

echo htmlspecialchars($row['receiver_role']);

}

?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['subject']); ?>

</strong>

<br>

<small class="text-muted">

<?php

echo substr(strip_tags($row['message']),0,60);

if(strlen($row['message'])>60){

echo "...";

}

?>

</small>

</td>

<td>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

<br>

<small>

<?php echo date("h:i A",strtotime($row['created_at'])); ?>

</small>

</td>

<td>

<?php

if($row['is_read']==1){

?>

<span class="badge bg-success">

Read

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark">

Pending

</span>

<?php

}

?>

</td>

<td>

<a

href="view_sent_message.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-eye"></i>

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

<i class="fas fa-paper-plane fa-2x mb-2"></i>

<br>

No Sent Messages Found.

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

<?php

/*=========================================
TOTAL SENT MESSAGES
=========================================*/

$totalMessages=$conn->prepare("

SELECT COUNT(*) AS total

FROM messages

WHERE sender_role='Examination Office'
AND sender_id=?

");

$totalMessages->bind_param("i",$admin_id);

$totalMessages->execute();

$total=$totalMessages->get_result()->fetch_assoc()['total'];

/*=========================================
READ SENT MESSAGES
=========================================*/

$readMessages=$conn->prepare("

SELECT COUNT(*) AS total

FROM messages

WHERE sender_role='Examination Office'
AND sender_id=?
AND is_read=1

");

$readMessages->bind_param("i",$admin_id);

$readMessages->execute();

$read=$readMessages->get_result()->fetch_assoc()['total'];

$pending=$total-$read;

?>

<div class="row mt-4">

<div class="col-md-4">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-paper-plane fa-3x text-primary mb-3"></i>

<h2>

<?php echo $total; ?>

</h2>

<h5>

Total Sent Messages

</h5>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-envelope-open fa-3x text-success mb-3"></i>

<h2>

<?php echo $read; ?>

</h2>

<h5>

Read By Receiver

</h5>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-clock fa-3x text-warning mb-3"></i>

<h2>

<?php echo $pending; ?>

</h2>

<h5>

Pending Messages

</h5>

</div>

</div>

</div>

</div>

<hr class="mt-5">

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

<p class="text-muted">

Examination Office Messaging Module

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>