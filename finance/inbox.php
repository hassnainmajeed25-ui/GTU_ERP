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

$admin_id=$_SESSION['admin_id'];
$admin_name=$_SESSION['admin_name'];

$sql="

SELECT *

FROM messages

WHERE
receiver_role='Finance Office'
AND receiver_id=".$admin_id."

ORDER BY created_at DESC
";

$result=$conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Finance Inbox</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#eef2f7;
font-family:Arial;
}

.sidebar{
width:260px;
height:100vh;
background:#0B1F52;
position:fixed;
left:0;
top:0;
}

.sidebar h3{
color:white;
padding:20px;
text-align:center;
}

.sidebar a{
display:block;
padding:14px 20px;
color:white;
text-decoration:none;
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

.card{
border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.1);
}

</style>

</head>

<body>
<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php">Dashboard</a>

<a href="pending_payments.php">Pending Payments</a>

<a href="verified_payments.php">Verified Payments</a>

<a href="rejected_payments.php">Rejected Payments</a>

<a href="reports.php">Reports</a>

<a href="compose_message.php">Compose Message</a>

<a href="inbox.php" class="active">Inbox</a>

<a href="profile.php">Profile</a>

<a href="../admins/logout.php">Logout</a>

</div>
<div class="main">

<div class="card">

<div class="card-header bg-primary text-white">

<h4>

<i class="fas fa-envelope"></i>

Finance Inbox

</h4>

</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>From</th>

<th>Subject</th>

<th>Date</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>
    <?php

if($result && $result->num_rows>0){

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td>

<?php

echo htmlspecialchars($row['sender_role']);

?>

</td>

<td>

<?php

echo htmlspecialchars($row['subject']);

?>

</td>

<td>

<?php

echo date("d M Y h:i A",strtotime($row['created_at']));

?>

</td>

<td>

<?php

if($row['is_read']==0){

echo '<span class="badge bg-danger">Unread</span>';

}else{

echo '<span class="badge bg-success">Read</span>';

}

?>

</td>

<td>

<a
href="view_message.php?id=<?php echo $row['id'];?>"
class="btn btn-primary btn-sm">

Open

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center">

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

</div>

</body>

</html>