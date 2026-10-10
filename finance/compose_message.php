<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=====================================
LOGIN SECURITY
=====================================*/

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

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Compose Message</title>

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
box-shadow:0 3px 10px rgba(0,0,0,.10);
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.10);
}

</style>

</head>

<body>

<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php">
<i class="fas fa-home me-2"></i>
Dashboard
</a>

<a href="pending_payments.php">
<i class="fas fa-hourglass-half me-2"></i>
Pending Payments
</a>

<a href="verified_payments.php">
<i class="fas fa-circle-check me-2"></i>
Verified Payments
</a>

<a href="rejected_payments.php">
<i class="fas fa-circle-xmark me-2"></i>
Rejected Payments
</a>

<a href="reports.php">
<i class="fas fa-chart-column me-2"></i>
Reports
</a>

<a href="compose_message.php" class="active">
<i class="fas fa-paper-plane me-2"></i>
Compose Message
</a>

<a href="inbox.php">
<i class="fas fa-envelope me-2"></i>
Inbox
</a>

<a href="profile.php">
<i class="fas fa-user-circle me-2"></i>
Profile
</a>

<a href="../admins/logout.php">
<i class="fas fa-right-from-bracket me-2"></i>
Logout
</a>

</div>

<div class="main">

<div class="topbar">

<div>

<h3>Compose Message</h3>

<small class="text-muted">

Finance Office

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-paper-plane me-2"></i>

New Message

</h5>

</div>

<div class="card-body">

<form action="send_message.php" method="POST">

<div class="mb-3">

<label class="form-label">

Send To

</label>

<select
name="receiver_role"
class="form-select"
required>


<option value="Super Admin">
Super Admin
</option>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Subject

</label>

<input
type="text"
name="subject"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Message

</label>

<textarea
name="message"
rows="7"
class="form-control"
required></textarea>

</div>

<button
type="submit"
class="btn btn-primary">

<i class="fas fa-paper-plane me-2"></i>

Send Message
s
</button>

</form>

</div>

</div>
