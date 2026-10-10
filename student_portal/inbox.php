<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}


$student_id=$_SESSION['student_id'];



/*=========================================
GET REPLIES
=========================================*/


$stmt=$conn->prepare("

SELECT

student_messages.*,

admins.full_name,

admins.department


FROM student_messages


LEFT JOIN admins

ON admins.id = student_messages.admin_id


WHERE

student_messages.student_id=?

AND

student_messages.reply IS NOT NULL


AND

student_messages.reply!=''


ORDER BY

student_messages.id DESC


");


$stmt->bind_param(

"i",

$student_id

);


$stmt->execute();


$result=$stmt->get_result();



?>


<!DOCTYPE html>

<html lang="en">


<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Message Inbox</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{

background:#edf2f7;

font-family:Arial,Helvetica,sans-serif;

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



.card-header{

background:#0B1F52;

color:white;

font-weight:bold;

font-size:20px;

}



.reply-card{

border-left:6px solid #198754;

background:#fff;

padding:20px;

border-radius:12px;

margin-bottom:20px;

box-shadow:0 5px 12px rgba(0,0,0,.08);

}



.office{

color:#2563eb;

font-weight:bold;

font-size:18px;

}



.subject{

font-weight:bold;

color:#0B1F52;

}



.message-box{

background:#f1f5f9;

padding:15px;

border-radius:10px;

}



.reply-box{

background:#e8f5e9;

padding:15px;

border-radius:10px;

border-left:4px solid #198754;

}



</style>


</head>


<body>


<div class="container py-4">


<div class="header d-flex justify-content-between align-items-center">


<h3>

<i class="fas fa-envelope-open-text me-2"></i>

Message Inbox

</h3>


<a href="dashboard.php" class="btn btn-light">

<i class="fas fa-arrow-left me-2"></i>

Dashboard

</a>


</div>

<div class="card">

<div class="card-header">

<i class="fas fa-reply me-2"></i>

Office Replies

</div>


<div class="card-body">


<?php


if($result->num_rows>0){


while($row=$result->fetch_assoc()){


?>


<div class="reply-card">


<div class="row">


<div class="col-md-6">


<p class="office mb-2">

<i class="fas fa-building me-2"></i>

<?php

echo htmlspecialchars($row['department']);

?>

</p>


</div>


<div class="col-md-6 text-md-end">


<small class="text-muted">


<i class="fas fa-calendar me-1"></i>


<?php

echo date(

"d M Y h:i A",

strtotime($row['created_at'])

);

?>


</small>


</div>


</div>



<hr>



<h5 class="subject">

Subject

</h5>


<p>

<?php

echo htmlspecialchars($row['subject']);

?>

</p>



<h5 class="subject mt-3">

Your Message

</h5>


<div class="message-box">


<?php

echo nl2br(

htmlspecialchars($row['message'])

);

?>


</div>




<h5 class="subject mt-4 text-success">

<i class="fas fa-reply me-2"></i>

Admin Reply

</h5>


<div class="reply-box">


<?php

echo nl2br(

htmlspecialchars($row['reply'])

);

?>


</div>



<div class="mt-3">


<span class="badge bg-success">

Replied

</span>


</div>



</div>



<?php


}


}else{


?>


<div class="text-center py-5">


<i class="fas fa-envelope-open-text"

style="font-size:60px;color:#ccc;"></i>


<h5 class="mt-3">

No Reply Available

</h5>


<p class="text-muted">

When office replies to your message, it will appear here.

</p>


</div>



<?php


}


?>


</div>

</div>



<div class="text-center mt-4">


<a href="sent_messages.php" class="btn btn-primary">


<i class="fas fa-paper-plane me-2"></i>


Sent Messages


</a>



<a href="dashboard.php" class="btn btn-dark ms-2">


<i class="fas fa-home me-2"></i>


Dashboard


</a>


</div>



</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>