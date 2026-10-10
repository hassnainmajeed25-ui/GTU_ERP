<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit;
}

$student_id=$_SESSION['student_id'];

/*=============================
MARK AS READ
=============================*/

if(isset($_GET['read'])){

$id=(int)$_GET['read'];

$update=$conn->prepare("

UPDATE student_notifications

SET is_read=1

WHERE id=?

AND student_id=?

");

$update->bind_param("ii",$id,$student_id);

$update->execute();

header("Location: notifications.php");

exit;

}

/*=============================
GET NOTIFICATIONS
=============================*/

$sql=$conn->prepare("

SELECT *

FROM student_notifications

WHERE student_id=?

ORDER BY created_at DESC

");

$sql->bind_param("i",$student_id);

$sql->execute();

$result=$sql->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Notifications</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

}

.card{

margin-top:30px;

border:none;

border-radius:15px;

box-shadow:0 5px 20px rgba(0,0,0,.12);

}

.card-header{

background:#0B1F52;

color:white;

font-size:25px;

font-weight:bold;

padding:18px;

}

.unread{

border-left:6px solid #0d6efd;

background:#f8fbff;

}

.read{

border-left:6px solid #198754;

background:white;

}

.time{

font-size:13px;

color:#777;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header text-center">

<i class="fas fa-bell"></i>

Notifications

</div>

<div class="card-body">

<?php

if($result->num_rows==0){

?>

<div class="alert alert-info">

No notifications available.

</div>

<?php

}else{

while($row=$result->fetch_assoc()){

$class=$row['is_read']==0 ? "unread" : "read";

?>

<div class="card mb-3 <?php echo $class; ?>">

<div class="card-body">

<h5>

<?php echo $row['title']; ?>

</h5>

<p>

<?php echo $row['message']; ?>

</p>

<div class="time">

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</div>

<?php

if($row['is_read']==0){

?>

<a

href="?read=<?php echo $row['id']; ?>"

class="btn btn-sm btn-primary mt-3">

Mark as Read

</a>

<?php

}else{

?>

<span class="badge bg-success mt-3">

Read

</span>

<?php

}

?>

</div>

</div>

<?php

}

}

?>

<hr>

<a href="dashboard.php" class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back to Dashboard

</a>

</div>

</div>

</div>

</body>

</html>