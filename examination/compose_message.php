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

$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

/*=========================================
GET SUPER ADMIN
=========================================*/

$super = $conn->query("

SELECT
id,
full_name

FROM super_admin

LIMIT 1

");

$superAdmin = $super->fetch_assoc();

/*=========================================
SEND MESSAGE
=========================================*/

if(isset($_POST['send'])){

    $receiver_id = (int)$_POST['receiver_id'];

    $subject = trim($_POST['subject']);

    $message = trim($_POST['message']);

    if($subject!="" && $message!=""){

        $stmt = $conn->prepare("

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

        (?,?,?,?,?,?)

        ");

        $sender_role   = "Examination Office";
        $receiver_role = "Super Admin";

        $stmt->bind_param(

        "sissss",

        $sender_role,
        $admin_id,
        $receiver_role,
        $receiver_id,
        $subject,
        $message

        );

        if($stmt->execute()){

            $success = "Message sent successfully.";

        }else{

            $error = "Unable to send message.";

        }

    }else{

        $error = "Please fill all fields.";

    }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Compose Message | Examination Office</title>

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

<a href="compose_message.php" class="active">
<i class="fas fa-paper-plane me-2"></i>
Compose Message
</a>

<a href="inbox.php">
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

Compose Message

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

<?php if(isset($success)){ ?>

<div class="alert alert-success">

<i class="fas fa-check-circle me-2"></i>

<?php echo $success; ?>

</div>

<?php } ?>

<?php if(isset($error)){ ?>

<div class="alert alert-danger">

<i class="fas fa-times-circle me-2"></i>

<?php echo $error; ?>

</div>

<?php } ?>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-paper-plane me-2"></i>

New Message

</h5>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label class="form-label">

Receiver

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($superAdmin['full_name']); ?>"
readonly>

<input
type="hidden"
name="receiver_id"
value="<?php echo $superAdmin['id']; ?>">

<small class="text-muted">

Only Super Administrator can receive new messages from Examination Office.

</small>

</div>

<div class="mb-3">

<label class="form-label">

Subject

</label>

<input
type="text"
name="subject"
class="form-control"
maxlength="255"
placeholder="Enter message subject"
required>

</div>

<div class="mb-4">

<label class="form-label">

Message

</label>

<textarea

name="message"

class="form-control"

rows="8"

placeholder="Write your message here..."

required></textarea>

</div>

<div class="d-flex justify-content-between">

<a
href="dashboard.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button
type="submit"
name="send"
class="btn btn-primary">

<i class="fas fa-paper-plane me-2"></i>

Send Message

</button>

</div>

</form>

</div>

</div>

<hr class="mt-5">

<div class="text-center">

<p class="text-muted mb-1">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

<p class="text-muted">

Examination Office Messaging Module

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

const form=document.querySelector("form");

form.addEventListener("submit",function(e){

let subject=document.querySelector("input[name='subject']").value.trim();

let message=document.querySelector("textarea[name='message']").value.trim();

if(subject==="" || message===""){

e.preventDefault();

alert("Please fill all required fields.");

}

});

</script>

</body>

</html>