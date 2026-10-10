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

if($_SESSION['department']!="Academic Office"){

    header("Location: ../admins/login.php");
    exit;

}


$admin_id=$_SESSION['admin_id'];


if(!isset($_GET['id'])){

    header("Location: inbox.php");
    exit;

}


$message_id=(int)$_GET['id'];


/*=========================================
LOAD ORIGINAL MESSAGE
=========================================*/

$sql="

SELECT

m.*,

CASE

WHEN m.sender_role='Teacher'

THEN

(
SELECT CONCAT(employee_id,' | ',teacher_name)

FROM teachers

WHERE id=m.sender_id
)

WHEN m.sender_role='Super Admin'

THEN

(
SELECT full_name

FROM super_admin

WHERE id=m.sender_id
)

END AS sender_name

FROM messages m

WHERE

m.id=?

AND receiver_role='Academic Office'

AND receiver_id=?

LIMIT 1

";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(

$stmt,

"ii",

$message_id,

$admin_id

);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

$message=mysqli_fetch_assoc($result);


if(!$message){

die("Message Not Found");

}



/*=========================================
SEND REPLY
=========================================*/

$success="";
$error="";

if(isset($_POST['send_reply'])){

$subject=trim($_POST['subject']);

$reply=trim($_POST['message']);

if($subject=="" || $reply==""){

$error="Please fill all fields.";

}else{

$insert="

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

'Academic Office',
?,
?,
?,
?,
?

)

";

$stmt=mysqli_prepare($conn,$insert);

mysqli_stmt_bind_param(

$stmt,

"isiss",

$admin_id,

$message['sender_role'],

$message['sender_id'],

$subject,

$reply

);

if(mysqli_stmt_execute($stmt)){

$success="Reply Sent Successfully.";

}else{

$error="Failed to send reply.";

}

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reply Message</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f4f6f9;

font-family:'Segoe UI',sans-serif;

}

.card{

border:none;

border-radius:15px;

box-shadow:0 3px 12px rgba(0,0,0,.08);

}

.logo{

height:80px;

}

.page-title{

font-size:28px;

font-weight:700;

color:#0d6efd;

}

.info-title{

font-weight:bold;

color:#0d6efd;

}

.message-box{

background:#f8f9fa;

border:1px solid #dee2e6;

border-radius:10px;

padding:20px;

min-height:140px;

white-space:pre-line;

}

textarea{

resize:none;

}

</style>

</head>

<body>

<div class="container py-4">

<div class="card mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-2 text-center">

<img src="../assets/images/logo.png" class="logo">

</div>

<div class="col-md-10">

<h2 class="page-title">

<i class="fa-solid fa-reply me-2"></i>

Reply Message

</h2>

<p class="text-muted">

Global Tech University ERP - Academic Office

</p>

</div>

</div>

</div>

</div>

<?php if($success!=""){ ?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>

<div class="card">

<div class="card-header bg-primary text-white">

<i class="fa-solid fa-envelope-open-text me-2"></i>

Original Message

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<p>

<span class="info-title">

From:

</span>

<br>

<?php echo htmlspecialchars($message['sender_name']); ?>

</p>

</div>

<div class="col-md-6">

<p>

<span class="info-title">

Date:

</span>

<br>

<?php echo date("d M Y h:i A",strtotime($message['created_at'])); ?>

</p>

</div>

</div>

<div class="mb-3">

<span class="info-title">

Subject:

</span>

<br>

<?php echo htmlspecialchars($message['subject']); ?>

</div>

<div class="mb-4">

<span class="info-title">

Message:

</span>

<div class="message-box">

<?php echo nl2br(htmlspecialchars($message['message'])); ?>

</div>

</div>

<hr>

<form method="POST">

<div class="mb-3">

<label class="form-label fw-bold">

Reply Subject

</label>

<input

type="text"

name="subject"

class="form-control"

value="Re: <?php echo htmlspecialchars($message['subject']); ?>"

required>

</div>

<div class="mb-3">

<label class="form-label fw-bold">

Reply Message

</label>

<textarea

name="message"

class="form-control"

rows="8"

placeholder="Write your reply..."

required></textarea>

</div>

<div class="d-flex justify-content-between mt-4">

<a
href="inbox.php"
class="btn btn-secondary">

<i class="fa-solid fa-arrow-left me-2"></i>

Back To Inbox

</a>


<button
type="submit"
name="send_reply"
class="btn btn-success">

<i class="fa-solid fa-paper-plane me-2"></i>

Send Reply

</button>

</div>

</form>

</div>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>