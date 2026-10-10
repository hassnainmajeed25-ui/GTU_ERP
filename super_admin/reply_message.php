<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

if(!isset($_GET['id'])){

    header("Location: inbox.php");
    exit;

}

$message_id=(int)$_GET['id'];

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

$original=$result->fetch_assoc();

$message="";

if(isset($_POST['send'])){

    $reply=trim($_POST['reply']);

    $sender_role="Super Admin";

    $sender_id=$_SESSION['super_admin_id'];

    $receiver_role=$original['sender_role'];

    $receiver_id=$original['sender_id'];

    $subject="RE: ".$original['subject'];
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

    (?,?,?,?,?,?)

    ");

    $insert->bind_param(

    "sissss",

    $sender_role,

    $sender_id,

    $receiver_role,

    $receiver_id,

    $subject,

    $reply

    );

    if($insert->execute()){

        $notify=$conn->prepare("

        INSERT INTO admin_notifications

        (

        user_role,

        user_id,

        title,

        message

        )

        VALUES

        (?,?,?,?)

        ");

        $title="Reply Received";

        $notify->bind_param(

        "siss",

        $receiver_role,

        $receiver_id,

        $title,

        $subject

        );

        $notify->execute();

        $message="<div class='alert alert-success'>Reply sent successfully.</div>";

    }else{

        $message="<div class='alert alert-danger'>Unable to send reply.</div>";

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

background:#f4f6f9;

}

.card{

margin-top:35px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#198754;

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

.message-box{

background:#f8f9fa;

border:1px solid #dee2e6;

border-radius:10px;

padding:20px;

margin-bottom:20px;

white-space:pre-wrap;

}

textarea{

border-radius:10px;

resize:none;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card">

<div class="card-header">

<i class="fas fa-reply me-2"></i>

Reply Message

</div>

<div class="card-body">

<?php echo $message; ?>

<h5>

Original Subject

</h5>

<div class="message-box">

<strong>

<?php echo htmlspecialchars($original['subject']); ?>

</strong>

</div>

<h5>

Original Message

</h5>

<div class="message-box">

<?php echo nl2br(htmlspecialchars($original['message'])); ?>

</div>

<form method="POST">
<div class="mb-4">

<label class="form-label">

Your Reply

</label>

<textarea

name="reply"

class="form-control"

rows="8"

placeholder="Type your reply here..."

required>

</textarea>

</div>

<div class="d-flex justify-content-between">

<a

href="view_message.php?id=<?php echo $message_id; ?>"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="send"

class="btn btn-success">

<i class="fas fa-paper-plane me-2"></i>

Send Reply

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>