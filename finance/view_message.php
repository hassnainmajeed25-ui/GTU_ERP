<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])){
    header("Location:../admins/login.php");
    exit;
}

if($_SESSION['department']!="Finance Office"){
    header("Location:../admins/login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: inbox.php");
    exit;
}

$id=(int)$_GET['id'];

/*=========================
GET MESSAGE
=========================*/

$sql="

SELECT *

FROM messages

WHERE id=$id

LIMIT 1

";

$result=$conn->query($sql);

if($result->num_rows==0){
    die("Message not found.");
}

$message=$result->fetch_assoc();

/*=========================
MARK AS READ
=========================*/

$conn->query("
UPDATE messages
SET is_read=1
WHERE id=$id
");

?>
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>View Message</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

</head>

<body style="background:#eef2f7;">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="fas fa-envelope-open-text"></i>

View Message

</h4>

</div>

<div class="card-body">

<table class="table">

<tr>

<th width="180">From</th>

<td>

<?php echo htmlspecialchars($message['sender_role']); ?>

</td>

</tr>

<tr>

<th>Subject</th>

<td>

<?php echo htmlspecialchars($message['subject']); ?>

</td>

</tr>

<tr>

<th>Date</th>

<td>

<?php echo date("d M Y h:i A",strtotime($message['created_at'])); ?>

</td>

</tr>

<tr>

<th>Message</th>

<td style="white-space:pre-line">

<?php echo htmlspecialchars($message['message']); ?>

</td>

</tr>

</table>

<hr>

<a
href="reply_message.php?id=<?php echo $message['id'];?>"
class="btn btn-success">

<i class="fas fa-reply"></i>

Reply

</a>

<a
href="inbox.php"
class="btn btn-secondary">

Back

</a>

</div>

</div>

</div>

</body>

</html>