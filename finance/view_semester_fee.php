<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['admin_id'])){
    header("Location:../admins/login.php");
    exit;
}

if($_SESSION['department']!="Finance Office"){
    header("Location:../admins/login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location:semester_fee_verification.php");
    exit;
}

$id=(int)$_GET['id'];

$stmt=$conn->prepare("

SELECT

semester_fee_payments.*,

students.first_name,
students.last_name,
students.email,
students.mobile,

applications.registration_no,
applications.roll_no

FROM semester_fee_payments

INNER JOIN students
ON students.id=semester_fee_payments.student_id

LEFT JOIN applications
ON applications.application_id=semester_fee_payments.application_id

WHERE semester_fee_payments.id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

die("Record not found.");

}

$row=$result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>View Semester Fee</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial;

}

.card{

margin-top:35px;

border:none;

border-radius:15px;

box-shadow:0 5px 15px rgba(0,0,0,.10);

}

.card-header{

background:#0B1F52;

color:#fff;

padding:18px;

font-size:24px;

font-weight:bold;

}

.table th{

width:250px;

background:#f8f9fa;

}

.receipt{

max-width:100%;

border:2px solid #ddd;

border-radius:10px;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fas fa-money-check-dollar me-2"></i>

Semester Fee Details

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th>Student Name</th>

<td>

<?php

echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);

?>

</td>

</tr>

<tr>

<th>Email</th>

<td>

<?php echo htmlspecialchars($row['email']); ?>

</td>

</tr>

<tr>

<th>Mobile</th>

<td>

<?php echo htmlspecialchars($row['mobile']); ?>

</td>

</tr>

<tr>

<th>Registration Number</th>

<td>

<?php echo htmlspecialchars($row['registration_no']); ?>

</td>

</tr>

<tr>

<th>Roll Number</th>

<td>

<?php

echo !empty($row['roll_no'])

?

htmlspecialchars($row['roll_no'])

:

"Not Generated";

?>

</td>

</tr>

<tr>

<th>Amount</th>

<td>

Rs. <?php echo number_format($row['amount']); ?>

</td>

</tr>

<tr>

<th>Bank Name</th>

<td>

<?php echo htmlspecialchars($row['bank_name']); ?>

</td>

</tr>

<tr>

<th>Transaction ID</th>

<td>

<?php echo htmlspecialchars($row['transaction_id']); ?>

</td>

</tr>

<tr>

<th>Payment Date</th>

<td>

<?php echo date("d M Y",strtotime($row['payment_date'])); ?>

</td>

</tr>

<tr>

<th>Status</th>

<td>

<?php

if($row['status']=="Pending"){

echo '<span class="badge bg-warning text-dark">Pending</span>';

}elseif($row['status']=="Approved"){

echo '<span class="badge bg-success">Approved</span>';

}else{

echo '<span class="badge bg-danger">Rejected</span>';

}

?>

</td>

</tr>

</table>

<hr>

<h4 class="mb-3">

Uploaded Bank Receipt

</h4>

<?php

if(!empty($row['slip'])){

$ext=strtolower(pathinfo($row['slip'],PATHINFO_EXTENSION));

if($ext=="jpg" || $ext=="jpeg" || $ext=="png"){

?>

<img

src="../uploads/semester_fee/<?php echo $row['slip']; ?>"

class="receipt img-fluid">

<?php

}else{

?>

<a

href="../uploads/semester_fee/<?php echo $row['slip']; ?>"

target="_blank"

class="btn btn-primary">

<i class="fas fa-file-pdf me-2"></i>

Open PDF Receipt

</a>

<?php

}

}else{

?>

<div class="alert alert-danger">

No receipt uploaded.

</div>

<?php

}

?>

<hr>

<div class="mt-4">

<a

href="semester_fee_verification.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<?php

if($row['status']=="Pending"){

?>

<a

href="approve_semester_fee.php?id=<?php echo $row['id']; ?>"

class="btn btn-success"

onclick="return confirm('Approve this semester fee?');">

<i class="fas fa-check me-2"></i>

Approve

</a>

<a

href="reject_semester_fee.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger"

onclick="return confirm('Reject this semester fee?');">

<i class="fas fa-times me-2"></i>

Reject

</a>

<?php

}

?>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
s
</html>