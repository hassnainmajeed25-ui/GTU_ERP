<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Finance Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*=========================================
GET ALL SEMESTER FEE PAYMENTS
=========================================*/

$sql="

SELECT

semester_fee_payments.*,

students.first_name,
students.last_name,

applications.registration_no,
applications.roll_no,

semester_fee_payments.semester,

programs.program_name

FROM semester_fee_payments

INNER JOIN students
ON students.id=semester_fee_payments.student_id

INNER JOIN applications
ON applications.application_id=semester_fee_payments.application_id

LEFT JOIN programs
ON programs.id=applications.program_id

ORDER BY semester_fee_payments.id DESC

";

$result=$conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Semester Fee Verification

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.sidebar{

width:260px;

height:100vh;

position:fixed;

left:0;

top:0;

background:#0B1F52;

overflow:auto;

}

.sidebar h3{

color:#fff;

padding:20px;

text-align:center;

}

.sidebar a{

display:block;

padding:14px 20px;

color:#fff;

text-decoration:none;

}

.sidebar a:hover{

background:#163a8c;

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

box-shadow:0 5px 15px rgba(0,0,0,.08);

}

.table th{

background:#0B1F52;

color:white;

}

</style>

</head>

<body>

<!-- Sidebar -->

<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php">
<i class="fas fa-home me-2"></i>
Dashboard
</a>

<a href="processing_fee.php">
<i class="fas fa-money-check-dollar me-2"></i>
Processing Fee
</a>

<a href="semester_fee.php" class="active">
<i class="fas fa-wallet me-2"></i>
Semester Fee
</a>

<a href="compose_message.php">
<i class="fas fa-paper-plane me-2"></i>
Compose Message
</a>

<a href="inbox.php">
<i class="fas fa-envelope me-2"></i>
Inbox
</a>

<a href="profile.php">
<i class="fas fa-user me-2"></i>
Profile
</a>

<a href="../admins/logout.php">
<i class="fas fa-sign-out-alt me-2"></i>
Logout
</a>

</div>

<!-- Main -->

<div class="main">

<div class="card">

<div class="card-header bg-success text-white">

<h4 class="mb-0">

<i class="fas fa-money-check-dollar me-2"></i>

Semester Fee Verification

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Registration No</th>

<th>Roll No</th>

<th>Student</th>

<th>Program</th>

<th>Semester</th>

<th>Amount</th>

<th>Bank</th>

<th>Transaction ID</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php echo htmlspecialchars($row['registration_no']); ?>

</td>

<td>

<?php

if(!empty($row['roll_no'])){

echo $row['roll_no'];

}else{

echo "<span class='text-danger'>Not Generated</span>";

}

?>

</td>

<td>

<?php

echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);

?>

</td>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

<td>

<span class="badge bg-primary">

Semester <?php echo $row['semester']; ?>

</span>

</td>

<td>

<b>

Rs.

<?php echo number_format($row['amount']); ?>

</b>

</td>

<td>

<?php

echo !empty($row['bank_name'])

? htmlspecialchars($row['bank_name'])

: "Meezan Bank Limited";

?>

</td>

<td>

<?php echo htmlspecialchars($row['transaction_id']); ?>

</td>

<td>

<?php

if($row['status']=="Pending"){

echo "<span class='badge bg-warning text-dark'>Pending</span>";

}elseif($row['status']=="Approved"){

echo "<span class='badge bg-success'>Approved</span>";

}else{

echo "<span class='badge bg-danger'>Rejected</span>";

}

?>

</td>

<td>

<?php

if(!empty($row['slip'])){

?>

<a

href="../uploads/semester_fee/<?php echo $row['slip']; ?>"

target="_blank"

class="btn btn-info btn-sm mb-1">

<i class="fas fa-eye"></i>

View Slip

</a>

<br>

<?php

}else{

?>

<span class="text-danger">

No Slip

</span>

<br>

<?php

}

/*=========================================
VERIFY BUTTON
=========================================*/

if($row['status']=="Pending"){

?>

<a

href="approve_semester_fee.php?id=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm mt-1"

onclick="return confirm('Approve this Semester Fee?');">

<i class="fas fa-check"></i>

Verify

</a>

<a

href="reject_semester_fee.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm mt-1"

onclick="return confirm('Reject this Semester Fee?');">

<i class="fas fa-times"></i>

Reject

</a>

<?php

}else{

?>

<span class="badge bg-secondary">

Completed

</span>

<?php

}

?>

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