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
GET ORIGINAL MESSAGE
=========================================*/

$stmt=$conn->prepare("

SELECT *

FROM messages

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$message_id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location: inbox.php");

exit;

}

$message=$result->fetch_assoc();

/*=========================================
SET RECEIVER
=========================================*/

$receiver_role=$message['sender_role'];

$receiver_id=$message['sender_id'];

/*=========================================
SEND REPLY
=========================================*/

if(isset($_POST['send'])){

$subject=trim($_POST['subject']);

$reply=trim($_POST['message']);

if($subject!="" && $reply!=""){

$insert=$conn->prepare("

INSERT INTO messages
(
sender_role,
sender_id,
receiver_role,
receiver_id,
subject,
message
)

VALUES
(
?,?,?,?,?,?
)

");

$sender_role="Examination Office";

$insert->bind_param(

"sissss",

$sender_role,
$admin_id,
$receiver_role,
$receiver_id,
$subject,
$reply

);

if($insert->execute()){

$success="Reply sent successfully.";

}else{

$error="Unable to send reply.";

}

}else{

$error="Please fill all fields.";

}

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Reply Message</title>

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

.main{
margin-left:260px;
padding:30px;
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

<h3 class="text-white text-center py-3">GTU ERP</h3>

<a href="dashboard.php" class="d-block p-3 text-white text-decoration-none">
<i class="fas fa-home me-2"></i> Dashboard
</a>

<a href="exam_schedule.php" class="d-block p-3 text-white text-decoration-none">
<i class="fas fa-calendar-alt me-2"></i> Exam Schedule
</a>

<a href="marks_management.php" class="d-block p-3 text-white text-decoration-none">
<i class="fas fa-square-poll-vertical me-2"></i> Marks Management
</a>

<a href="results.php" class="d-block p-3 text-white text-decoration-none">
<i class="fas fa-award me-2"></i> Results
</a>

<a href="reports.php" class="d-block p-3 text-white text-decoration-none">
<i class="fas fa-chart-bar me-2"></i> Reports
</a>

<a href="compose_message.php" class="d-block p-3 text-white text-decoration-none">
<i class="fas fa-paper-plane me-2"></i> Compose Message
</a>

<a href="inbox.php" class="d-block p-3 text-white text-decoration-none active">
<i class="fas fa-envelope me-2"></i> Inbox
</a>

<a href="profile.php" class="d-block p-3 text-white text-decoration-none">
<i class="fas fa-user-circle me-2"></i> Profile
</a>

<a href="../admins/logout.php" class="d-block p-3 text-white text-decoration-none">
<i class="fas fa-sign-out-alt me-2"></i> Logout
</a>

</div>

<div class="main">

<div class="card mb-4">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h3>Reply Message</h3>

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

</div>

<?php if(isset($success)){ ?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php } ?>

<?php if(isset($error)){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

Reply Message

</h5>

</div>

<div class="card-body">

<div class="mb-3">

<label class="form-label">

Reply To

</label>

<input
type="text"
class="form-control"
readonly
value="<?php

if($receiver_role=="Student"){

echo "Student";

}elseif($receiver_role=="Super Admin"){

echo "Super Admin";

}else{

echo htmlspecialchars($receiver_role);

}

?>">

</div>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Subject

</label>

<input
type="text"
name="subject"
class="form-control"
value="Re: <?php echo htmlspecialchars($message['subject']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Reply

</label>

<textarea
name="message"
rows="8"
class="form-control"
required></textarea>

</div>

<button
type="submit"
name="send"
class="btn btn-success">

<i class="fas fa-paper-plane me-2"></i>

Send Reply

</button>

<a
href="view_message.php?id=<?php echo $message_id; ?>"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</form>

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