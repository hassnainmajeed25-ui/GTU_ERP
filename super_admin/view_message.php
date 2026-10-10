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

$update=$conn->prepare("

UPDATE messages

SET is_read=1

WHERE id=?

");

$update->bind_param("i",$message_id);
$update->execute();

$stmt=$conn->prepare("

SELECT

m.*,

a.full_name,

a.department

FROM messages m

LEFT JOIN admins a

ON m.sender_id=a.id

WHERE m.id=?

LIMIT 1

");

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
background:#f4f6f9;
}

.card{
margin-top:35px;
border:none;
border-radius:15px;
box-shadow:0 8px 25px rgba(0,0,0,.10);
}

.card-header{
background:#0B5ED7;
color:#fff;
font-size:24px;
font-weight:bold;
padding:18px;
}

.info-box{
background:#eef5ff;
border-left:5px solid #0B5ED7;
padding:18px;
border-radius:10px;
margin-bottom:20px;
}

.message-box{
background:#fff;
border:1px solid #dee2e6;
padding:20px;
border-radius:10px;
min-height:180px;
white-space:pre-wrap;
}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card">

<div class="card-header">

<i class="fas fa-envelope-open-text me-2"></i>

View Message

</div>

<div class="card-body">
<div class="info-box">

<h5 class="mb-3">

<i class="fas fa-user me-2"></i>

Sender Information

</h5>

<div class="row">

<div class="col-md-6">

<strong>Name :</strong><br>

<?php

echo !empty($row['full_name'])
? htmlspecialchars($row['full_name'])
: htmlspecialchars($row['sender_role']);

?>

</div>

<div class="col-md-6">

<strong>Department :</strong><br>

<?php echo htmlspecialchars($row['department']); ?>

</div>

</div>

<hr>

<div class="row">

<div class="col-md-6">

<strong>Subject :</strong><br>

<?php echo htmlspecialchars($row['subject']); ?>

</div>

<div class="col-md-6">

<strong>Date & Time :</strong><br>

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

<div class="mt-4 d-flex justify-content-between">

<a

href="inbox.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back to Inbox

</a>

<a

href="reply_message.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary">

<i class="fas fa-reply me-2"></i>

Reply

</a>

</div>
</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>