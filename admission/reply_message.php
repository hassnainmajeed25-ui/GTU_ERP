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

$msg=$result->fetch_assoc();

/*=========================================
SEND REPLY
=========================================*/

$success="";
$error="";

if(isset($_POST['send'])){

    $subject=trim($_POST['subject']);

    $message_text=trim($_POST['message']);

    $sender_role="Admission Office";

    $sender_id=$admin_id;

    $receiver_role=$msg['sender_role'];

    $receiver_id=$msg['sender_id'];

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

    $message_text

    );

    if($insert->execute()){

    /*
    ===============================
    SEND NOTIFICATION TO STUDENT
    ===============================
    */

    if($receiver_role=="Student"){

        $title="New Reply";

        $notify=$conn->prepare("

        INSERT INTO student_notifications

        (

        student_id,

        title,

        message

        )

        VALUES

        (?,?,?)

        ");

        $notify->bind_param(

        "iss",

        $receiver_id,

        $title,

        $subject

        );

        $notify->execute();

    }

    $success="Reply sent successfully.";

}else{

    $error="Unable to send reply.";

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
box-shadow:0 4px 12px rgba(0,0,0,.08);
}

textarea{
resize:none;
}

</style>

</head>

<body>

<!-- Sidebar -->

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

<!-- Main -->

<div class="main">

<div class="topbar">

<div>

<h3>Reply Message</h3>

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

<?php

if($success!=""){

?>

<div class="alert alert-success">

<i class="fas fa-check-circle me-2"></i>

<?php echo $success; ?>

</div>

<?php

}

?>

<?php

if($error!=""){

?>

<div class="alert alert-danger">

<i class="fas fa-times-circle me-2"></i>

<?php echo $error; ?>

</div>

<?php

}

?>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-reply me-2"></i>

Reply Message

</h5>

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
value="<?php

if($msg['sender_role']=="Student"){

    echo "Student";

}else{

    echo htmlspecialchars($msg['sender_role']);

}

?>"
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
value="RE: <?php echo htmlspecialchars($msg['subject']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Original Message

</label>

<div class="border rounded p-3 bg-light">

<?php

echo nl2br(htmlspecialchars($msg['message']));

?>

</div>

</div>

<div class="mb-4">

<label class="form-label">

Your Reply

</label>

<textarea
name="message"
class="form-control"
rows="8"
required
placeholder="Write your reply here..."></textarea>

</div>

<div class="d-flex justify-content-between">

<a
href="view_message.php?id=<?php echo $msg['id'];?>"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button
type="submit"
name="send"
class="btn btn-primary">

<i class="fas fa-paper-plane me-2"></i>

Send Reply

</button>

</div>

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