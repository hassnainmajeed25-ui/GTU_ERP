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
GET NOTIFICATIONS
=========================================*/


$notifications=$conn->prepare("

SELECT *

FROM super_admin_notifications

WHERE status='Active'

ORDER BY id DESC

");


$notifications->execute();


$result=$notifications->get_result();



?>


<!DOCTYPE html>

<html lang="en">


<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>

Super Admin Notifications

</title>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{


background:#eef2f7;

font-family:'Segoe UI',sans-serif;


}



/*==============================
HEADER
==============================*/


.notification-header{


background:linear-gradient(

135deg,

#000000,

#0B3D91,

#2563eb

);


color:white;


padding:25px;


border-radius:18px;


margin-bottom:30px;


box-shadow:0 8px 25px rgba(0,0,0,.20);


}



.notification-header h2{


font-weight:700;


margin:0;


}



/*==============================
CARD
==============================*/


.notification-card{


background:white;


border-radius:18px;


box-shadow:0 8px 25px rgba(0,0,0,.12);


border:none;


overflow:hidden;


}



.card-title-bar{


background:linear-gradient(

90deg,

#0B3D91,

#2563eb

);


color:white;


padding:16px;


font-size:20px;


font-weight:bold;


}



/*==============================
BUTTON
==============================*/


.create-btn{


background:#FFD54F;


color:#000;


font-weight:bold;


border:none;


padding:10px 18px;


border-radius:10px;


}



.create-btn:hover{


background:#FFC107;


color:#000;


}



</style>



</head>



<body>



<div class="container-fluid py-4">


<div class="notification-header">


<div class="d-flex justify-content-between align-items-center">


<h2>

<i class="fas fa-bell me-2"></i>

Super Admin Notifications

</h2>



<a href="add_super_admin_notification.php"

class="btn create-btn">


<i class="fas fa-plus me-2"></i>

Create Notification


</a>


</div>


</div>

<!-- ==============================
NOTIFICATION LIST CARD
============================== -->


<div class="notification-card">


<div class="card-title-bar">

<i class="fas fa-list me-2"></i>

All Active Notifications

</div>



<div class="card-body">



<div class="table-responsive">


<table class="table table-bordered table-hover align-middle">


<thead>


<tr>


<th width="5%">#</th>

<th>Title</th>

<th>Type</th>

<th>Message</th>

<th>Date</th>

<th width="15%">Action</th>


</tr>


</thead>



<tbody>


<?php


if($result->num_rows>0){


$sr=1;


while($row=$result->fetch_assoc()){


?>



<tr>


<td>

<?php echo $sr++; ?>

</td>



<td>


<strong class="text-primary">


<?php echo htmlspecialchars($row['title']); ?>


</strong>


</td>




<td>



<?php


if($row['notification_type']=="Important"){


?>


<span class="badge bg-danger">


<i class="fas fa-circle-exclamation me-1"></i>

Important


</span>


<?php


}elseif($row['notification_type']=="Result"){


?>


<span class="badge bg-primary">


<i class="fas fa-file-lines me-1"></i>

Result


</span>


<?php


}elseif($row['notification_type']=="Fee"){


?>


<span class="badge bg-warning text-dark">


<i class="fas fa-money-bill me-1"></i>

Fee


</span>


<?php


}else{


?>


<span class="badge bg-success">


<i class="fas fa-info-circle me-1"></i>

General


</span>


<?php


}


?>


</td>




<td>


<div style="max-width:350px;">


<?php


echo htmlspecialchars($row['message']);


?>


</div>


</td>




<td>


<?php


echo date(

"d M Y",

strtotime($row['created_at'])

);


?>


<br>


<small class="text-muted">


<?php


echo date(

"h:i A",

strtotime($row['created_at'])

);


?>


</small>


</td>





<td>



<a href="delete_super_admin_notification.php?id=<?php echo $row['id']; ?>"


onclick="return confirm('Are you sure you want to delete this notification?')"


class="btn btn-danger btn-sm">


<i class="fas fa-trash me-1"></i>


Delete


</a>



</td>



</tr>



<?php


}


}else{


?>



<tr>


<td colspan="6" class="text-center py-5">


<i class="fas fa-bell-slash fa-3x text-secondary mb-3"></i>


<h5>

No Active Notifications Found

</h5>


<p class="text-muted">

Create a new notification for students.

</p>


</td>


</tr>



<?php


}


?>



</tbody>



</table>



</div>



</div>


</div>


</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>