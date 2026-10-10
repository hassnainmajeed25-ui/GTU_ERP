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

if(!isset($_GET['id'])){
    header("Location: inbox.php");
    exit;
}

$message_id=(int)$_GET['id'];

/*=========================================
MARK AS READ
=========================================*/

$update=$conn->prepare("

UPDATE messages

SET is_read=1

WHERE id=?

");

$update->bind_param("i",$message_id);

$update->execute();

/*=========================================
GET MESSAGE
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

WHERE messages.id=?

LIMIT 1

";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$message_id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location: inbox.php");

exit;

}

$row=$result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>View Message</title>

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

<a href="inbox.php" class="active">
<i class="fas fa-envelope me-2"></i>
Inbox
</a>

<a href="sent_messages.php">
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

View Message

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

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-envelope-open-text me-2"></i>

Message Details

</h5>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th width="20%">From</th>

<td>

<?php

if($row['sender_role']=="Super Admin"){

echo "<span class='badge bg-primary'>Super Admin</span> Administrator";

}

elseif($row['sender_role']=="Student"){

echo "<span class='badge bg-success'>Student</span> ";

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

if(!empty($row['application_id'])){

echo "<br><small>Application ID : ".htmlspecialchars($row['application_id'])."</small>";

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

</tr>

<tr>

<th>Subject</th>

<td>

<strong>

<?php echo htmlspecialchars($row['subject']); ?>

</strong>

</td>

</tr>

<tr>

<th>Date</th>

<td>

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</td>

</tr>

<tr>

<th>Message</th>

<td style="white-space:pre-wrap;">

<?php echo nl2br(htmlspecialchars($row['message'])); ?>

</td>

</tr>

</table>

<div class="mt-4">

<a
href="reply_message.php?id=<?php echo $row['id']; ?>"
class="btn btn-success">

<i class="fas fa-reply me-2"></i>

Reply

</a>

<a
href="inbox.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

</div>

<hr class="mt-5">

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>