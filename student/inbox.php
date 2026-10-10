<?php

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
GET MY MESSAGES
=========================================*/

$sql=$conn->prepare("

SELECT

id,
sender_role,
receiver_role,
subject,
message,
is_read,
created_at

FROM messages

WHERE

sender_role='Student'

AND sender_id=?

OR

receiver_role='Student'

AND receiver_id=?

ORDER BY id DESC

");

$sql->bind_param("ii",$student_id,$student_id);

$sql->execute();

$result=$sql->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Inbox</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.card{

margin-top:35px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#0B1F52;

color:#fff;

padding:18px;

font-size:24px;

font-weight:bold;

}

.table th{

background:#0B1F52;

color:white;

}

.badge{

font-size:13px;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fas fa-envelope me-2"></i>

My Inbox

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead>

<tr>

<th width="8%">#</th>

<th>From</th>

<th>To</th>

<th>Subject</th>

<th>Status</th>

<th>Date</th>

<th width="12%">Action</th>

</tr>

</thead>

<tbody>

<?php

if($result && $result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php echo htmlspecialchars($row['sender_role']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['receiver_role']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['subject']); ?>

</td>

<td>

<?php

if($row['is_read']==0){

?>

<span class="badge bg-danger">

Unread

</span>

<?php

}else{

?>

<span class="badge bg-success">

Read

</span>

<?php

}

?>

</td>

<td>

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</td>

<td>

<a

href="view_message.php?id=<?php echo $row['id'];?>"

class="btn btn-primary btn-sm">

<i class="fas fa-eye"></i>

View

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center text-danger">

No Messages Found.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>
</div>

<hr>

<div class="d-flex justify-content-between">

<a

href="dashboard.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back to Dashboard

</a>

<a

href="compose_message.php"

class="btn btn-primary">

<i class="fas fa-paper-plane me-2"></i>

Compose New Message

</a>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>