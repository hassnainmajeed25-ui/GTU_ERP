<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Finance Office"){
    header("Location: ../admins/login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: inbox.php");
    exit;
}

$message_id=(int)$_GET['id'];

$q=$conn->query("

SELECT *

FROM messages

WHERE id=$message_id

LIMIT 1

");

if($q->num_rows==0){
    die("Message not found.");
}

$msg=$q->fetch_assoc();

if($_SERVER['REQUEST_METHOD']=="POST"){

    $subject=trim($_POST['subject']);
    $message=trim($_POST['message']);

    $stmt=$conn->prepare("

    INSERT INTO messages(

    sender_role,
    sender_id,

    receiver_role,
    receiver_id,

    subject,
    message,
    is_read

    )

    VALUES(

    'Finance Office',
    ?,

    ?,
    ?,

    ?,
    ?,

    0

    )

    ");

    $stmt->bind_param(

    "isiss",

    $_SESSION['admin_id'],

    $msg['sender_role'],
    $msg['sender_id'],

    $subject,
    $message

    );

    if($stmt->execute()){

        header("Location: inbox.php?reply=1");
        exit;

    }else{

        echo $conn->error;

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Reply Message</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

</head>

<body style="background:#eef2f7;">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-reply"></i>

Reply Message

</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label class="form-label">

Reply To

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($msg['sender_role']); ?>"
readonly>

</div>

<div class="mb-3">

<label class="form-label">

Subject

</label>

<input
type="text"
name="subject"
class="form-control"
value="Re: <?php echo htmlspecialchars($msg['subject']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Message

</label>

<textarea
name="message"
rows="8"
class="form-control"
required></textarea>

</div>

<button
type="submit"
class="btn btn-success">

<i class="fas fa-paper-plane"></i>

Send Reply

</button>

<a
href="view_message.php?id=<?php echo $message_id; ?>"
class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

</div>

</body>

</html>