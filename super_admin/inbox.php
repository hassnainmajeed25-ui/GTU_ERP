<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

$admin_id=$_SESSION['super_admin_id'];

$result=$conn->prepare("

SELECT

m.*,

a.full_name,

a.department

FROM messages m

LEFT JOIN admins a

ON m.sender_id=a.id

WHERE

m.receiver_role='Super Admin'

AND

m.receiver_id=?

ORDER BY m.created_at DESC

");

$result->bind_param("i",$admin_id);

$result->execute();

$messages=$result->get_result();

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

background:#f4f6f9;

}

.card{

margin-top:30px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#0B5ED7;

color:white;

font-size:24px;

font-weight:bold;

padding:18px;

}

.table td{

vertical-align:middle;

}

.unread{

font-weight:bold;

background:#eef6ff;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fas fa-envelope me-2"></i>

Inbox

</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th width="70">

ID

</th>

<th>

From

</th>

<th>

Subject

</th>

<th>

Date

</th>

<th width="120">

Action

</th>

</tr>

</thead>

<tbody>
    <?php

if($messages->num_rows>0){

while($row=$messages->fetch_assoc()){

?>

<tr class="<?php echo ($row['is_read']==0) ? 'unread' : ''; ?>">

<td>

<?php echo $row['id']; ?>

</td>

<td>

<?php

if(!empty($row['full_name'])){

echo $row['full_name'];

}else{

echo $row['sender_role'];

}

?>

<br>

<small class="text-muted">

<?php echo $row['department']; ?>

</small>

</td>

<td>

<?php echo htmlspecialchars($row['subject']); ?>

</td>

<td>

<?php echo date("d M Y h:i A",strtotime($row['created_at'])); ?>

</td>

<td>

<a

href="view_message.php?id=<?php echo $row['id']; ?>"

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

<td colspan="5" class="text-center text-muted">

<i class="fas fa-inbox fa-3x mb-3"></i>

<br>

No Messages Found

</td>

</tr>

<?php

}

?>
</tbody>

</table>

<div class="mt-3">

<a

href="dashboard.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back to Dashboard

</a>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>