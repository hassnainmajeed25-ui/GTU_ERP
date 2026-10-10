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

if($_SESSION['department']!="Admission Office"){
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
MARK MESSAGE AS READ
=========================================*/

$read=$conn->prepare("

UPDATE messages

SET is_read=1

WHERE id=?

");

$read->bind_param("i",$message_id);
$read->execute();

/*=========================================
GET MESSAGE
=========================================*/

$sql=$conn->prepare("

SELECT

messages.*,

students.first_name,
students.last_name,

applications.application_id,

faculties.faculty_name,

programs.program_name

FROM messages

LEFT JOIN students
ON students.id=messages.sender_id
AND messages.sender_role='Student'

LEFT JOIN applications
ON applications.student_id=students.id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

WHERE

messages.id=?

AND

(

(messages.receiver_role='Admission Office' AND messages.receiver_id=?)

OR

(messages.sender_role='Admission Office' AND messages.sender_id=?)

)

LIMIT 1

");

$sql->bind_param(

"iii",

$message_id,

$admin_id,

$admin_id

);

$sql->execute();

$result=$sql->get_result();

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
box-shadow:0 4px 12px rgba(0,0,0,.1);
}

.info-box{
background:#f8f9fa;
padding:15px;
border-radius:10px;
margin-bottom:15px;
}

.message-box{
background:#ffffff;
border:1px solid #dee2e6;
padding:20px;
border-radius:10px;
min-height:200px;
white-space:pre-wrap;
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
Manage Applications
</a>

<a href="merit_list.php">
<i class="fas fa-ranking-star me-2"></i>
Merit List
</a>

<a href="selected_students.php">
<i class="fas fa-user-check me-2"></i>
Selected Students
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

<h3>View Message</h3>

<small class="text-muted">

Admission Office

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
<div class="info-box">

<div class="row">

<div class="col-md-6">

<label class="fw-bold">

From

</label>

<p>

<?php

if($row['sender_role']=="Super Admin"){

echo "<span class='badge bg-primary'>Super Admin</span>";

}elseif($row['sender_role']=="Student"){

echo "<span class='badge bg-success'>Student</span>";

echo "<br>";

echo "<strong>";

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

echo "</strong>";

if(!empty($row['application_id'])){

echo "<br>";

echo "Application ID : ";

echo htmlspecialchars($row['application_id']);

}

if(!empty($row['faculty_name'])){

echo "<br>";

echo htmlspecialchars($row['faculty_name']);

echo " / ";

echo htmlspecialchars($row['program_name']);

}

}else{

echo htmlspecialchars($row['sender_role']);

}

?>

</p>

</div>

<div class="col-md-6">

<label class="fw-bold">

Date & Time

</label>

<p>

<?php

echo date("d M Y",strtotime($row['created_at']));

?>

<br>

<?php

echo date("h:i A",strtotime($row['created_at']));

?>

</p>

</div>

</div>

</div>

<div class="mb-3">

<label class="fw-bold">

Subject

</label>

<div class="form-control bg-light">

<?php echo htmlspecialchars($row['subject']); ?>

</div>

</div>

<div class="mb-4">

<label class="fw-bold">

Message

</label>

<div class="message-box">

<?php echo nl2br(htmlspecialchars($row['message'])); ?>

</div>

</div>

<div class="d-flex justify-content-between">

<a
href="inbox.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back to Inbox

</a>

<a
href="reply_message.php?id=<?php echo $row['id'];?>"
class="btn btn-primary">

<i class="fas fa-reply me-2"></i>

Reply

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