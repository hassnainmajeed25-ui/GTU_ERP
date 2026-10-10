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
SEMESTER FILTER
=========================================*/

$current_semester=1;

if(isset($_GET['semester'])){

    $current_semester=(int)$_GET['semester'];

    if($current_semester<1 || $current_semester>8){

        $current_semester=1;

    }

}

/*=========================================
GET SEMESTER FEE PAYMENTS
=========================================*/

$sql="

SELECT

semester_fee_payments.*,

students.first_name,
students.last_name,
students.email,
students.mobile,

applications.roll_no

FROM semester_fee_payments

INNER JOIN students

ON semester_fee_payments.student_id=students.id

LEFT JOIN applications

ON applications.application_id=semester_fee_payments.application_id

WHERE semester_fee_payments.semester=?

ORDER BY semester_fee_payments.id DESC

";

$stmt=$conn->prepare($sql);

$stmt->bind_param(

"i",

$current_semester

);

$stmt->execute();

$result=$stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Semester Fee Verification</title>

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

color:#fff;

text-align:center;

padding:20px;

}

.sidebar a{

display:block;

color:#fff;

padding:14px 20px;

text-decoration:none;

}

.sidebar a:hover{

background:#173a8b;

}

.sidebar .active{

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

color:#fff;

}

</style>

</head>

<body>

<div class="sidebar">

<h3>GTU ERP</h3>

<a href="dashboard.php">
Dashboard
</a>

<a href="processing_fee.php">
Processing Fee
</a>

<a href="semester_fee_verification.php" class="active">
Semester Fee
</a>

<a href="reports.php">
Reports
</a>

<a href="../admins/logout.php">
Logout
</a>

</div>

<div class="main">

<div class="card">

<div class="card-header bg-success text-white">

<h4>

Semester Fee Verification

</h4>

</div>

<div class="card-body">

<form method="GET" class="row mb-4">

<div class="col-md-4">

<label class="form-label">

Select Semester

</label>

<select

name="semester"

class="form-select"

onchange="this.form.submit();">

<?php

for($i=1;$i<=8;$i++){

?>

<option

value="<?php echo $i; ?>"

<?php

if($current_semester==$i){

echo "selected";

}

?>>

<?php echo $i; ?> Semester

</option>

<?php

}

?>

</select>

</div>

</form>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Student</th>

<th>Registration No</th>

<th>Semester</th>

<th>Amount</th>

<th>Status</th>

<th>Slip</th>

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

<td>

<?php echo $sr++; ?>

</td>

<td>

<strong>

<?php

echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);

?>

</strong>

<br>

<small>

<?php echo htmlspecialchars($row['email']); ?>

</small>

<br>

<small>

<?php echo htmlspecialchars($row['mobile']); ?>

</small>

</td>

<td>

<?php

if(!empty($row['registration_no'])){

?>

<span class="badge bg-success">

<?php echo htmlspecialchars($row['registration_no']); ?>

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark">

Not Generated

</span>

<?php

}

?>

</td>

<td>

<?php

$semester=(int)$row['semester'];

$color="primary";

if($semester==1){

    $color="success";

}elseif($semester==2){

    $color="primary";

}elseif($semester==3){

    $color="info";

}elseif($semester==4){

    $color="warning";

}elseif($semester==5){

    $color="secondary";

}elseif($semester==6){

    $color="dark";

}elseif($semester==7){

    $color="danger";

}else{

    $color="primary";

}

?>

<span class="badge bg-<?php echo $color; ?>">

Semester

<?php echo $semester; ?>

</span>

</td>

<td>

<strong>

Rs.

<?php

echo number_format($row['amount']);

?>

</strong>

</td>

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

<td>

<?php

if(!empty($row['slip'])){

?>

<a

href="../uploads/semester_fee/<?php echo $row['slip']; ?>"

target="_blank"

class="btn btn-info btn-sm">

<i class="fas fa-eye"></i>

View Slip

</a>

<?php

}else{

echo "<span class='text-danger'>No Slip</span>";

}

?>

</td>

<td>

<?php

if($row['status']=="Pending"){

?>

<a

href="approve_semester_fee.php?id=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm mb-1"

onclick="return confirm('Approve this Semester Fee?');">

<i class="fas fa-check"></i>

Approve

</a>

<br>

<a

href="reject_semester_fee.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Reject this Semester Fee?');">

<i class="fas fa-times"></i>

Reject

</a>

<?php

}else{

echo "<span class='text-success'>Completed</span>";

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center text-danger">

No Semester Fee Record Found.

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

<?php

if(isset($_GET['approve'])){

?>

<div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">

<div class="alert alert-success alert-dismissible fade show">

<i class="fas fa-circle-check me-2"></i>

Semester Fee Approved Successfully.

<button

type="button"

class="btn-close"

data-bs-dismiss="alert">

</button>

</div>

</div>

<?php

}

?>

<?php

if(isset($_GET['reject'])){

?>

<div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">

<div class="alert alert-danger alert-dismissible fade show">

<i class="fas fa-circle-xmark me-2"></i>

Semester Fee Rejected Successfully.

<button

type="button"

class="btn-close"

data-bs-dismiss="alert">

</button>

</div>

</div>

<?php

}

?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>