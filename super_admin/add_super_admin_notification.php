<?php

session_start();


require_once '../config/config.php';
require_once '../config/database.php';



/*=========================================
SUPER ADMIN SECURITY
=========================================*/


if(!isset($_SESSION['super_admin_id'])){


    header("Location: login.php");

    exit;


}



$super_admin_id=$_SESSION['super_admin_id'];



/*=========================================
ADD NOTIFICATION
=========================================*/


if(isset($_POST['save_notification'])){


$title=trim($_POST['title']);

$message=trim($_POST['message']);

$type=trim($_POST['notification_type']);



if(empty($title) || empty($message)){


$_SESSION['error']="Please fill all fields.";


header("Location:add_super_admin_notification.php");


exit;


}



/*=========================================
INSERT DATA
=========================================*/


$insert=$conn->prepare("


INSERT INTO super_admin_notifications

(

title,

message,

notification_type,

created_by

)

VALUES

(

?,?,?,?

)


");



$insert->bind_param(

"sssi",

$title,

$message,

$type,

$super_admin_id

);



if($insert->execute()){


$_SESSION['success']="Notification Created Successfully.";


}else{


$_SESSION['error']="Unable to Create Notification.";


}



header("Location:super_admin_notifications.php");


exit;



}



?>



<!DOCTYPE html>

<html lang="en">


<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>

Create Notification

</title>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{


background:#eef2f7;

font-family:'Segoe UI',sans-serif;


}



.page-header{


background:linear-gradient(

135deg,

#000,

#0B3D91,

#2563eb

);


color:white;


padding:25px;


border-radius:18px;


margin-bottom:30px;


box-shadow:0 8px 25px rgba(0,0,0,.20);


}



.page-header h2{


font-weight:700;


}



.card{


border:none;


border-radius:18px;


box-shadow:0 8px 25px rgba(0,0,0,.12);


}



.card-header{


background:linear-gradient(

90deg,

#0B3D91,

#2563eb

);


color:white;


font-size:20px;


font-weight:bold;


}



.form-control,
.form-select{


border-radius:10px;


padding:12px;


}



.save-btn{


background:#FFD54F;


color:#000;


font-weight:bold;


border:none;


padding:12px 25px;


border-radius:10px;


}



.save-btn:hover{


background:#FFC107;


}



</style>

<!-- ==============================
PAGE CONTENT
============================== -->


</head>


<body>



<div class="container py-4">



<div class="page-header d-flex justify-content-between align-items-center">


<h2>

<i class="fas fa-bell me-2"></i>

Create Notification

</h2>



<a href="super_admin_notifications.php"

class="btn btn-warning fw-bold">


<i class="fas fa-arrow-left me-2"></i>

Back


</a>


</div>





<div class="row justify-content-center">


<div class="col-lg-8">



<div class="card">



<div class="card-header">


<i class="fas fa-plus-circle me-2"></i>

New Student Notification


</div>




<div class="card-body p-4">



<form method="POST">





<div class="mb-3">


<label class="form-label fw-bold">


Notification Title


</label>



<input type="text"

name="title"

class="form-control"

placeholder="Enter notification title"

required>



</div>






<div class="mb-3">


<label class="form-label fw-bold">


Notification Type


</label>



<select name="notification_type"

class="form-select"

required>


<option value="General">

General

</option>



<option value="Important">

Important

</option>



<option value="Result">

Result

</option>



<option value="Fee">

Fee

</option>


</select>



</div>







<div class="mb-3">


<label class="form-label fw-bold">


Notification Message


</label>




<textarea

name="message"

rows="6"

class="form-control"

placeholder="Write notification message here..."

required></textarea>



</div>







<div class="d-flex justify-content-between">



<a href="super_admin_notifications.php"

class="btn btn-secondary">


<i class="fas fa-times me-2"></i>

Cancel


</a>





<button type="submit"

name="save_notification"

class="btn save-btn">


<i class="fas fa-paper-plane me-2"></i>

Publish Notification


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