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



$admin_id=$_SESSION['admin_id'];



/*=========================================
CHECK MESSAGE ID
=========================================*/


if(!isset($_GET['id'])){

    header("Location: student_inbox.php");
    exit;

}


$message_id=(int)$_GET['id'];



/*=========================================
GET MESSAGE DATA
=========================================*/


$stmt=$conn->prepare("

SELECT


student_messages.*,


students.first_name,

students.last_name,


students.email,


students.mobile


FROM student_messages


INNER JOIN students


ON students.id = student_messages.student_id



WHERE


student_messages.id=?


AND


student_messages.admin_id=?


LIMIT 1


");



$stmt->bind_param(

"ii",

$message_id,

$admin_id

);



$stmt->execute();



$result=$stmt->get_result();



if($result->num_rows==0){


    header("Location: student_inbox.php");

    exit;

}



$message=$result->fetch_assoc();



/*=========================================
MARK READ
=========================================*/


$read=$conn->prepare("

UPDATE student_messages

SET is_read=1

WHERE id=?

");


$read->bind_param(

"i",

$message_id

);


$read->execute();



?>

<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>

Reply Student Message

</title>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{

background:#edf2f7;

font-family:'Segoe UI',sans-serif;

}


/* HEADER */

.page-header{


background:linear-gradient(135deg,#000,#2563eb);

color:white;

padding:20px;

border-radius:15px;

margin-bottom:25px;

box-shadow:0 8px 20px rgba(0,0,0,.15);

}



.page-header h3{

margin:0;

font-weight:700;

}



/* CARD */


.card{

border:none;

border-radius:15px;

box-shadow:0 5px 20px rgba(0,0,0,.12);

margin-bottom:25px;

}



.card-header{

background:#0B1F52;

color:white;

font-size:20px;

font-weight:bold;

}



/* INFO BOX */


.info-box{


background:#f8f9fa;

border-left:5px solid #2563eb;

padding:15px;

border-radius:10px;

margin-bottom:20px;

}



.info-title{

font-weight:bold;

color:#0B1F52;

}



/* MESSAGE */


.message-box{


background:#ffffff;

border-left:5px solid #2563eb;

padding:20px;

border-radius:12px;

white-space:pre-wrap;

}



.reply-area{


border-left:5px solid #198754;

padding:20px;

background:#f1fff5;

border-radius:12px;

}



</style>


</head>



<body>



<div class="container py-4">



<div class="page-header d-flex justify-content-between align-items-center">


<h3>

<i class="fas fa-reply me-2"></i>

Reply Student Message

</h3>


<a href="student_inbox.php" class="btn btn-warning">


<i class="fas fa-arrow-left me-2"></i>

Back Inbox


</a>


</div>





<div class="card">


<div class="card-header">


<i class="fas fa-user-graduate me-2"></i>

Student Information


</div>



<div class="card-body">



<div class="row">


<div class="col-md-6">


<div class="info-box">


<span class="info-title">

Student Name:

</span>


<br>


<?php

echo htmlspecialchars(

$message['first_name']." ".$message['last_name']

);

?>


</div>


</div>




<div class="col-md-6">


<div class="info-box">


<span class="info-title">

Email:

</span>


<br>


<?php

echo htmlspecialchars(

$message['email']

);

?>


</div>


</div>



<div class="col-md-6">


<div class="info-box">


<span class="info-title">

Mobile:

</span>


<br>


<?php

echo htmlspecialchars(

$message['mobile']

);

?>


</div>


</div>




<div class="col-md-6">


<div class="info-box">


<span class="info-title">

Subject:

</span>


<br>


<?php

echo htmlspecialchars(

$message['subject']

);

?>


</div>


</div>


</div>



</div>


</div>

<!--=========================================
STUDENT MESSAGE
=========================================-->


<div class="card">


<div class="card-header">

<i class="fas fa-envelope-open-text me-2"></i>

Student Message

</div>



<div class="card-body">



<div class="mb-3">


<label class="fw-bold text-primary">

Subject

</label>


<div class="message-box">


<?php


echo htmlspecialchars(

$message['subject']

);


?>


</div>


</div>




<div class="mb-3">


<label class="fw-bold text-primary">

Message

</label>


<div class="message-box">


<?php


echo nl2br(

htmlspecialchars(

$message['message']

)

);


?>


</div>


</div>



<?php

if(!empty($message['reply'])){


?>


<div class="mb-3">


<label class="fw-bold text-success">

Previous Reply

</label>



<div class="reply-area">


<?php


echo nl2br(

htmlspecialchars(

$message['reply']

)

);


?>


</div>



</div>


<?php


}


?>



</div>


</div>






<!--=========================================
REPLY FORM
=========================================-->



<div class="card">


<div class="card-header bg-success">


<i class="fas fa-paper-plane me-2"></i>

Send Reply


</div>




<div class="card-body">



<form method="POST" action="save_student_reply.php">



<input type="hidden"

name="message_id"

value="<?php echo $message_id; ?>">





<div class="mb-3">


<label class="form-label fw-bold">


Reply Message


</label>



<textarea

name="reply"

class="form-control"

rows="6"

placeholder="Write reply to student..."

required></textarea>



</div>




<button

type="submit"

class="btn btn-success btn-lg">


<i class="fas fa-paper-plane me-2"></i>

Send Reply


</button>




<a

href="student_inbox.php"

class="btn btn-secondary btn-lg ms-2">


<i class="fas fa-arrow-left me-2"></i>

Back
</a>

</form>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>