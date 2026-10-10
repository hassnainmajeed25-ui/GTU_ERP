<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}

if(!isset($_GET['id'])){

    header("Location: inbox.php");
    exit;

}

$student_id=$_SESSION['student_id'];

$message_id=(int)$_GET['id'];

/*=========================================
MARK MESSAGE AS READ
=========================================*/

$update=$conn->prepare("

UPDATE messages

SET is_read=1

WHERE id=?

AND receiver_role='Student'

AND receiver_id=?

");

$update->bind_param(

"ii",

$message_id,

$student_id

);

$update->execute();

/*=========================================
GET MESSAGE
=========================================*/

$sql=$conn->prepare("

SELECT

id,
sender_role,
sender_id,
receiver_role,
receiver_id,
subject,
message,
created_at

FROM messages

WHERE id=?

AND

(

(sender_role='Student' AND sender_id=?)

OR

(receiver_role='Student' AND receiver_id=?)

)

LIMIT 1

");

$sql->bind_param(

"iii",

$message_id,

$student_id,

$student_id

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

.card{

margin-top:35px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#0B1F52;

color:#fff;

padding:18px;

font-size:24px;

font-weight:bold;

}

.info-box{

background:#f8f9fa;

border-left:5px solid #0B1F52;

padding:15px;

border-radius:8px;

margin-bottom:20px;

}

.message-box{

background:#ffffff;

border:1px solid #dee2e6;

border-radius:10px;

padding:20px;

min-height:180px;

white-space:pre-wrap;

line-height:1.8;

font-size:15px;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fas fa-envelope-open-text me-2"></i>

View Message

</div>

<div class="card-body">

<div class="info-box">

<div class="row">

<div class="col-md-6 mb-3">

<strong>From :</strong><br>

<?php echo htmlspecialchars($row['sender_role']); ?>

</div>

<div class="col-md-6 mb-3">

<strong>To :</strong><br>

<?php echo htmlspecialchars($row['receiver_role']); ?>

</div>

<div class="col-md-6 mb-3">

<strong>Subject :</strong><br>

<?php echo htmlspecialchars($row['subject']); ?>

</div>

<div class="col-md-6 mb-3">

<strong>Date :</strong><br>

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</div>

</div>

</div>

<h5 class="mb-3">

<i class="fas fa-comment-dots me-2"></i>

Message

</h5>

<div class="message-box">

<?php echo nl2br(htmlspecialchars($row['message'])); ?>

</div>
<hr class="mt-4">

<div class="d-flex justify-content-between">

<a

href="inbox.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back to Inbox

</a>

<a

href="compose_message.php"

class="btn btn-primary">

<i class="fas fa-paper-plane me-2"></i>

New Message

</a>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>