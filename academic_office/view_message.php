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

if(!isset($_GET['id'])){

    header("Location: inbox.php");
    exit;

}


$message_id=(int)$_GET['id'];

$admin_id=$_SESSION['admin_id'];


/*=========================================
MARK AS READ
=========================================*/

$update="

UPDATE messages

SET is_read=1

WHERE id=?

AND receiver_role='Academic Office'

AND receiver_id=?

";

$stmt=mysqli_prepare($conn,$update);

mysqli_stmt_bind_param($stmt,"ii",$message_id,$admin_id);

mysqli_stmt_execute($stmt);


/*=========================================
LOAD MESSAGE
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

ELSE

m.sender_role

END AS sender_name

FROM messages m

WHERE m.id=?

AND m.receiver_role='Academic Office'

AND m.receiver_id=?

";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt,"ii",$message_id,$admin_id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

$message=mysqli_fetch_assoc($result);

if(!$message){

die("Message Not Found");

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>View Message</title>

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

padding:20px;

border-radius:10px;

border:1px solid #dee2e6;

min-height:180px;

white-space:pre-line;

font-size:16px;

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

<i class="fa-solid fa-envelope-open-text me-2"></i>

View Message

</h2>

<p class="text-muted">

Global Tech University ERP - Academic Office

</p>

</div>

</div>

</div>

</div>

<div class="card">

<div class="card-header bg-primary text-white">

<i class="fa-solid fa-envelope-open me-2"></i>

Message Details

</div>

<div class="card-body">

<div class="row mb-3">

<div class="col-md-6">

<p>

<span class="info-title">

From :

</span>

<br>

<?php echo htmlspecialchars($message['sender_name']); ?>

</p>

</div>

<div class="col-md-6">

<p>

<span class="info-title">

Date :

</span>

<br>

<?php echo date("d M Y h:i A",strtotime($message['created_at'])); ?>

</p>

</div>

</div>

<div class="mb-3">

<p>

<span class="info-title">

Subject :

</span>

<br>

<?php echo htmlspecialchars($message['subject']); ?>

</p>

</div>

<div class="mb-3">

<span class="info-title">

Message :

</span>

<div class="message-box">

<?php echo nl2br(htmlspecialchars($message['message'])); ?>

</div>

</div>

<!-- ACTION BUTTONS -->

<div class="mt-4 text-center">

<a
href="reply_message.php?id=<?php echo $message['id']; ?>"
class="btn btn-success">

<i class="fa-solid fa-reply"></i>

Reply Message

</a>


<a
href="inbox.php"
class="btn btn-secondary ms-2">

<i class="fa-solid fa-arrow-left"></i>

Back To Inbox

</a>

</div>


</div>

</div>


</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>