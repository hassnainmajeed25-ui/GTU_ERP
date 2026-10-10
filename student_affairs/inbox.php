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

$admin_id=$_SESSION['admin_id'];
$admin_name=$_SESSION['admin_name'];

/*=========================================
GET INBOX MESSAGES
=========================================*/

$sql="

SELECT

messages.*,

students.first_name,
students.last_name,

applications.application_id,

programs.program_name,

faculties.faculty_name

FROM messages

LEFT JOIN students
ON students.id=messages.sender_id
AND messages.sender_role='Student'

LEFT JOIN applications
ON applications.student_id=students.id

LEFT JOIN programs
ON programs.id=applications.program_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

WHERE

(

messages.receiver_role='Student Affairs'

AND

messages.receiver_id=?

)

OR

(

messages.sender_role='Student Affairs'

AND

messages.sender_id=?

)

ORDER BY messages.created_at ASC

";

$stmt=$conn->prepare($sql);

$stmt->bind_param(

"ii",

$admin_id,

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

<title>Inbox</title>

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

.badge-unread{
background:#dc3545;
}

.badge-read{
background:#198754;
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

<a href="manage_applications.php">
<i class="fas fa-file-alt me-2"></i>
Manage student
</a>

<a href="reports.php">
<i class="fas fa-chart-bar me-2"></i>
Reports
</a>

<a href="compose_message.php">
<i class="fas fa-paper-plane me-2"></i>
Compose Message
</a>

<a href="inbox.php" class="active">
<i class="fas fa-envelope me-2"></i>
Inbox
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

<h3>Inbox</h3>

<small class="text-muted">

Student Affairs Office

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

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-envelope me-2"></i>

Inbox Messages

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th width="5%">#</th>

<th width="20%">From</th>

<th>Subject</th>

<th width="15%">Date</th>

<th width="10%">Status</th>

<th width="10%">Action</th>

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

if($row['sender_role']=="Super Admin"){

echo "<span class='badge bg-primary me-2'>Super Admin</span>";

echo "Administrator";

}

elseif($row['sender_role']=="Student"){

echo "<span class='badge bg-success me-2'>Student</span><br>";

echo "<strong>";

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

echo "</strong>";

if(!empty($row['application_id'])){

echo "<br><small>";

echo "Application : ".htmlspecialchars($row['application_id']);

echo "</small>";

}

if(!empty($row['program_name'])){

echo "<br><small>";

echo htmlspecialchars($row['faculty_name']);

echo " / ";

echo htmlspecialchars($row['program_name']);

echo "</small>";

}

}

else{

echo htmlspecialchars($row['sender_role']);

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

<?php

echo date("d M Y",strtotime($row['created_at']));

?>

<br>

<small>

<?php

echo date("h:i A",strtotime($row['created_at']));

?>

</small>

</td>

<td>

<?php

if($row['is_read']==0){

?>

<span class="badge badge-unread">

Unread

</span>

<?php

}else{

?>

<span class="badge badge-read">

Read

</span>

<?php

}

?>

</td>

<td>

<a

href="view_message.php?id=<?php echo $row['id'];?>"

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

<i class="fas fa-inbox fa-2x mb-2"></i>

<br>

No Messages Found.

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

$totalMessages=$conn->prepare("

SELECT COUNT(*) total

FROM messages

WHERE receiver_role='Student Affairs'
AND receiver_id=?

");

$totalMessages->bind_param("i",$admin_id);

$totalMessages->execute();

$total=$totalMessages->get_result()->fetch_assoc()['total'];



$unreadMessages=$conn->prepare("

SELECT COUNT(*) total

FROM messages

WHERE receiver_role='Student Affairs'
AND receiver_id=?
AND is_read=0

");

$unreadMessages->bind_param("i",$admin_id);

$unreadMessages->execute();

$unread=$unreadMessages->get_result()->fetch_assoc()['total'];



$read=$total-$unread;

?>

<div class="row mt-4">

<div class="col-md-4">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-envelope fa-3x text-primary mb-3"></i>

<h2><?php echo $total; ?></h2>

<h5>Total Messages</h5>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-envelope-open fa-3x text-success mb-3"></i>

<h2><?php echo $read; ?></h2>

<h5>Read Messages</h5>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow">

<div class="card-body text-center">

<i class="fas fa-envelope-circle-check fa-3x text-danger mb-3"></i>

<h2><?php echo $unread; ?></h2>

<h5>Unread Messages</h5>

</div>

</div>

</div>

</div>

<hr class="mt-5">

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?> Global Tech University ERP System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>