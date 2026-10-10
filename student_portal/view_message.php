<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}


$student_id=$_SESSION['student_id'];


if(!isset($_GET['id'])){

    header("Location: sent_messages.php");
    exit;

}


$message_id=(int)$_GET['id'];



/*==============================
MARK MESSAGE READ
==============================*/

$read=$conn->prepare("

UPDATE student_messages

SET is_read=1

WHERE id=?

AND student_id=?

");


$read->bind_param(

"ii",

$message_id,

$student_id

);


$read->execute();




/*==============================
GET MESSAGE DETAIL
==============================*/


$stmt=$conn->prepare("

SELECT

student_messages.*,

admins.full_name,

admins.department

FROM student_messages

LEFT JOIN admins

ON admins.id = student_messages.admin_id

WHERE

student_messages.id=?

AND student_messages.student_id=?

LIMIT 1

");


$stmt->bind_param(

"ii",

$message_id,

$student_id

);


$stmt->execute();


$result=$stmt->get_result();



if($result->num_rows==0){

header("Location: sent_messages.php");

exit;

}



$message=$result->fetch_assoc();



?>


<!DOCTYPE html>

<html>

<head>

<title>View Message</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">


<style>

body{

background:#edf2f7;
font-family:Arial;

}


.header{

background:linear-gradient(135deg,#000,#2563eb);

color:white;

padding:20px;

border-radius:15px;

margin-bottom:25px;

}



.card{

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.12);

}


.title{

color:#0B1F52;

font-weight:bold;

}


.message{

background:#f8f9fa;

border-left:5px solid #2563eb;

padding:20px;

border-radius:10px;

white-space:pre-wrap;

}



.reply{

background:#e8f5e9;

border-left:5px solid #198754;

padding:20px;

border-radius:10px;

white-space:pre-wrap;

}


</style>


</head>


<body>


<div class="container py-4">


<div class="header">


<h3>

<i class="fas fa-envelope me-2"></i>

Message Details

</h3>


</div>



<div class="card">


<div class="card-body">



<h5 class="title">

Subject

</h5>


<p>

<?php echo htmlspecialchars($message['subject']); ?>

</p>




<hr>



<h5 class="title">

Department

</h5>


<p>

<?php echo htmlspecialchars($message['department']); ?>

</p>




<hr>



<h5 class="title">

Your Message

</h5>


<div class="message">

<?php

echo htmlspecialchars($message['message']);

?>

</div>




<hr>



<h5 class="title">

Reply From Office

</h5>


<?php

if(!empty($message['reply'])){

?>


<div class="reply">

<?php

echo htmlspecialchars($message['reply']);

?>

</div>


<?php

}else{

?>


<div class="alert alert-warning">

<i class="fas fa-clock me-2"></i>

Waiting for reply from office.

</div>


<?php

}

?>



<hr>



<p class="text-muted">

Sent Date:

<?php

echo date("d M Y h:i A",strtotime($message['created_at']));

?>

</p>




<a href="sent_messages.php" class="btn btn-dark">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>



</div>

</div>



</div>


</body>

</html>