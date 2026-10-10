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

if($_SESSION['department']!="Admission Office"){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['admin_name'];

/*=========================================
CHECK APPLICATION ID
=========================================*/

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Invalid Application ID");
}

$id=(int)$_GET['id'];

/*=========================================
GET COMPLETE APPLICATION
=========================================*/

$sql=$conn->prepare("

SELECT

applications.*,

application_payments.verification_status,
students.first_name,
students.last_name,
students.father_name,
students.email,
students.mobile,
students.cnic,

faculties.faculty_name,

programs.program_name,

application_personal.gender,
application_personal.date_of_birth,
application_personal.religion,
application_personal.nationality,
application_personal.address,
application_personal.city,
application_personal.province,
application_personal.postal_code,

application_academic.matric_board,
application_academic.matric_roll_no,
application_academic.matric_total_marks,
application_academic.matric_obtained_marks,
application_academic.inter_board,
application_academic.inter_roll_no,
application_academic.inter_total_marks,
application_academic.inter_obtained_marks,

application_documents.student_photo,
application_documents.student_cnic,
application_documents.father_cnic,
application_documents.matric_result_card,
application_documents.inter_result_card

FROM applications

INNER JOIN students
ON students.id=applications.student_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

LEFT JOIN application_personal
ON application_personal.application_id=applications.id

LEFT JOIN application_academic
ON application_academic.application_id=applications.id

LEFT JOIN application_documents
ON application_documents.application_id=applications.id

LEFT JOIN application_payments
ON application_payments.application_id=applications.id

WHERE applications.id=?

LIMIT 1

");

$sql->bind_param("i",$id);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

die("Application not found.");

}

$row=$result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>View Application</title>

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
    overflow-y:auto;
}

.sidebar .logo{
    color:#fff;
    font-size:24px;
    font-weight:bold;
    text-align:center;
    padding:25px;
    border-bottom:1px solid rgba(255,255,255,.15);
}

.sidebar a{
    display:block;
    color:#fff;
    text-decoration:none;
    padding:15px 20px;
    transition:.3s;
}

.sidebar a:hover{
    background:#173a8b;
}

.sidebar a.active{
    background:#1d4ed8;
}

.main{
    margin-left:260px;
    padding:30px;
}

.topbar{
    background:#fff;
    padding:18px 25px;
    border-radius:12px;
    box-shadow:0 3px 10px rgba(0,0,0,.1);
    margin-bottom:25px;

    display:flex;
    justify-content:space-between;
    align-items:center;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 3px 10px rgba(0,0,0,.1);
}

.section-title{
    color:#0B1F52;
    font-weight:bold;
    margin-bottom:20px;
}

.profile-photo{
    width:180px;
    height:180px;
    object-fit:cover;
    border-radius:12px;
    border:4px solid #0B1F52;
}

.info-table td{
    padding:10px;
}

</style>

</head>

<body>

<!-- Sidebar -->

<div class="sidebar">

<div class="logo">

GTU ERP

</div>

<a href="dashboard.php">

<i class="fas fa-home me-2"></i>

Dashboard

</a>

<a href="manage_applications.php" class="active">

<i class="fas fa-file-alt me-2"></i>

Manage Applications

</a>

<a href="merit_list.php">

<i class="fas fa-ranking-star me-2"></i>

Merit List

</a>

<a href="reports.php">

<i class="fas fa-chart-bar me-2"></i>

Reports

</a>

<a href="compose_message.php">

<i class="fas fa-paper-plane me-2"></i>

Compose Message

</a>

<a href="inbox.php">

<i class="fas fa-envelope me-2"></i>

Inbox

</a>

<a href="../admins/logout.php">

<i class="fas fa-right-from-bracket me-2"></i>

Logout

</a>

</div>

<!-- Main -->

<div class="main">

<div class="topbar">

<div>

<h3 class="mb-0">

Application Details

</h3>

<small class="text-muted">

Admission Office Panel

</small>

</div>

<div>

<strong>

Welcome,

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>

<div class="mb-3">

<a href="manage_applications.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back to Applications

</a>

</div>

<!-- Student Profile -->

<div class="card mb-4">

<div class="card-body">

<div class="row">

<div class="col-md-3 text-center">

<?php

if(!empty($row['student_photo'])){

?>

<img

src="../uploads/student_photos/<?php echo htmlspecialchars($row['student_photo']); ?>"

class="profile-photo">

<?php

}else{

?>

<img

src="../assets/images/user.png"

class="profile-photo">

<?php

}

?>

</div>

<div class="col-md-9">

<h3>

<?php

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

?>

</h3>

<hr>

<div class="row">

<div class="col-md-6">

<p>

<strong>Application ID:</strong>

<?php echo htmlspecialchars($row['application_id']); ?>

</p>

<p>

<strong>Email:</strong>

<?php echo htmlspecialchars($row['email']); ?>

</p>

<p>

<strong>Mobile:</strong>

<?php echo htmlspecialchars($row['mobile']); ?>

</p>

<p>

<strong>CNIC:</strong>

<?php echo htmlspecialchars($row['cnic']); ?>

</p>

</div>

<div class="col-md-6">

<p>

<strong>Faculty:</strong>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</p>

<p>

<strong>Program:</strong>

<?php echo htmlspecialchars($row['program_name']); ?>

</p>

<p>

<strong>Status:</strong>

<span class="badge bg-primary">

<?php echo htmlspecialchars($row['application_status']); ?>

</span>

</p>

<p>

<strong>Applied On:</strong>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</p>

</div>

</div>

</div>

</div>

</div>

</div>
<!-- ===========================
     PERSONAL INFORMATION
=========================== -->

<div class="card mb-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-user me-2"></i>

Personal Information

</h5>

</div>

<div class="card-body">

<table class="table table-bordered info-table">

<tr>

<th width="30%">Father Name</th>

<td><?php echo htmlspecialchars($row['father_name']); ?></td>

</tr>

<tr>

<th>Gender</th>

<td><?php echo htmlspecialchars($row['gender']); ?></td>

</tr>

<tr>

<th>Date of Birth</th>

<td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>

</tr>

<tr>

<th>Religion</th>

<td><?php echo htmlspecialchars($row['religion']); ?></td>

</tr>

<tr>

<th>Nationality</th>

<td><?php echo htmlspecialchars($row['nationality']); ?></td>

</tr>

<tr>

<th>Address</th>

<td><?php echo htmlspecialchars($row['address']); ?></td>

</tr>

<tr>

<th>City</th>

<td><?php echo htmlspecialchars($row['city']); ?></td>

</tr>

<tr>

<th>Province</th>

<td><?php echo htmlspecialchars($row['province']); ?></td>

</tr>

<tr>

<th>Postal Code</th>

<td><?php echo htmlspecialchars($row['postal_code']); ?></td>

</tr>

</table>

</div>

</div>

<!-- ===========================
     ACADEMIC INFORMATION
=========================== -->

<div class="card mb-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-graduation-cap me-2"></i>

Academic Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<h5 class="text-primary">

Matric Information

</h5>

<table class="table table-bordered">

<tr>

<th>Board</th>

<td><?php echo htmlspecialchars($row['matric_board']); ?></td>

</tr>

<tr>

<th>Roll No</th>

<td><?php echo htmlspecialchars($row['matric_roll_no']); ?></td>

</tr>

<tr>

<th>Total Marks</th>

<td><?php echo htmlspecialchars($row['matric_total_marks']); ?></td>

</tr>

<tr>

<th>Obtained Marks</th>

<td><?php echo htmlspecialchars($row['matric_obtained_marks']); ?></td>

</tr>

<tr>

<th>Percentage</th>

<td>

<?php

if($row['matric_total_marks']>0){

echo round(($row['matric_obtained_marks']/$row['matric_total_marks'])*100,2)."%";

}else{

echo "-";

}

?>

</td>

</tr>

</table>

</div>

<div class="col-md-6">

<h5 class="text-success">

Intermediate Information

</h5>

<table class="table table-bordered">

<tr>

<th>Board</th>

<td><?php echo htmlspecialchars($row['inter_board']); ?></td>

</tr>

<tr>

<th>Roll No</th>

<td><?php echo htmlspecialchars($row['inter_roll_no']); ?></td>

</tr>

<tr>

<th>Total Marks</th>

<td><?php echo htmlspecialchars($row['inter_total_marks']); ?></td>

</tr>

<tr>

<th>Obtained Marks</th>

<td><?php echo htmlspecialchars($row['inter_obtained_marks']); ?></td>

</tr>

<tr>

<th>Percentage</th>

<td>

<?php

if($row['inter_total_marks']>0){

echo round(($row['inter_obtained_marks']/$row['inter_total_marks'])*100,2)."%";

}else{

echo "-";

}

?>

</td>

</tr>

</table>

</div>

</div>

</div>

</div>
<!-- ==========================================
        UPLOADED DOCUMENTS
========================================== -->

<div class="card mb-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-folder-open me-2"></i>

Uploaded Documents

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-light">

<tr>

<th width="30%">Document</th>

<th>Status</th>

<th width="220">Action</th>

</tr>

</thead>

<tbody>

<!-- Student CNIC -->

<tr>

<td>

Student CNIC

</td>

<td>

<?php

if(!empty($row['student_cnic'])){

echo '<span class="badge bg-success">Uploaded</span>';

}else{

echo '<span class="badge bg-danger">Missing</span>';

}

?>

</td>

<td>

<?php

if(!empty($row['student_cnic'])){

?>

<a

href="../uploads/student_cnic/<?php echo $row['student_cnic'];?>"

target="_blank"

class="btn btn-info btn-sm">

<i class="fas fa-eye"></i>

View

</a>

<a

href="../uploads/student_cnic/<?php echo $row['student_cnic'];?>"

download

class="btn btn-success btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<?php

}else{

echo "-";

}

?>

</td>

</tr>

<!-- Father CNIC -->

<tr>

<td>

Father CNIC

</td>

<td>

<?php

if(!empty($row['father_cnic'])){

echo '<span class="badge bg-success">Uploaded</span>';

}else{

echo '<span class="badge bg-danger">Missing</span>';

}

?>

</td>

<td>

<?php

if(!empty($row['father_cnic'])){

?>

<a

href="../uploads/father_cnic/<?php echo $row['father_cnic'];?>"

target="_blank"

class="btn btn-info btn-sm">

<i class="fas fa-eye"></i>

View

</a>

<a

href="../uploads/father_cnic/<?php echo $row['father_cnic'];?>"

download

class="btn btn-success btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<?php

}else{

echo "-";

}

?>

</td>

</tr>

<!-- Matric Result -->

<tr>

<td>

Matric Result Card

</td>

<td>

<?php

if(!empty($row['matric_result_card'])){

echo '<span class="badge bg-success">Uploaded</span>';

}else{

echo '<span class="badge bg-danger">Missing</span>';

}

?>

</td>

<td>

<?php

if(!empty($row['matric_result_card'])){

?>

<a

href="../uploads/matric_result/<?php echo $row['matric_result_card'];?>"

target="_blank"

class="btn btn-info btn-sm">

<i class="fas fa-eye"></i>

View

</a>

<a

href="../uploads/matric_result/<?php echo $row['matric_result_card'];?>"

download

class="btn btn-success btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<?php

}else{

echo "-";

}

?>

</td>

</tr>

<!-- Inter Result -->

<tr>

<td>

Intermediate Result Card

</td>

<td>

<?php

if(!empty($row['inter_result_card'])){

echo '<span class="badge bg-success">Uploaded</span>';

}else{

echo '<span class="badge bg-danger">Missing</span>';

}

?>

</td>

<td>

<?php

if(!empty($row['inter_result_card'])){

?>

<a

href="../uploads/inter_result/<?php echo $row['inter_result_card'];?>"

target="_blank"

class="btn btn-info btn-sm">

<i class="fas fa-eye"></i>

View

</a>

<a

href="../uploads/inter_result/<?php echo $row['inter_result_card'];?>"

download

class="btn btn-success btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<?php

}else{

echo "-";

}

?>

</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>
<?php

/*=========================================
APPROVE / REJECT APPLICATION
=========================================*/
if(isset($_POST['approve'])){

    $admin_id=$_SESSION['admin_id'];

    $update=$conn->prepare("
    UPDATE applications
    SET
    application_status='Approved',
    approved_by=?,
    approved_at=NOW()
    WHERE id=?
    ");

    $update->bind_param("ii",$admin_id,$id);

    if($update->execute()){

    /*=========================================
GENERATE REGISTRATION NUMBER
=========================================*/

$year=date("Y");

$registration_no="REG-".$year."-".str_pad($id,5,"0",STR_PAD_LEFT);

$reg=$conn->prepare("
UPDATE applications
SET registration_no=?
WHERE id=?
");

$reg->bind_param("si",$registration_no,$id);
$reg->execute();

        // Get Student ID
        $getStudent=$conn->prepare("
        SELECT student_id
        FROM applications
        WHERE id=?
        ");

        $getStudent->bind_param("i",$id);
        $getStudent->execute();

        $student=$getStudent->get_result()->fetch_assoc();

        // Send Notification
        $title="Application Approved";

        $message="Congratulations! Your admission application has been approved successfully.";

        $notify=$conn->prepare("
        INSERT INTO notifications
        (
        student_id,
        title,
        message
        )
        VALUES
        (?,?,?)
        ");

        $notify->bind_param(
        "iss",
        $student['student_id'],
        $title,
        $message
        );

        $notify->execute();

        echo "<script>
        alert('Application Approved Successfully');
        window.location='manage_applications.php';
        </script>";

        exit;
    }

}

if(isset($_POST['reject'])){

    $admin_id=$_SESSION['admin_id'];

    $update=$conn->prepare("
    UPDATE applications
    SET
    application_status='Rejected',
    rejected_by=?,
    rejected_at=NOW()
    WHERE id=?
    ");

    $update->bind_param("ii",$admin_id,$id);

    if($update->execute()){

        // Get Student ID
        $getStudent=$conn->prepare("
        SELECT student_id
        FROM applications
        WHERE id=?
        ");

        $getStudent->bind_param("i",$id);
        $getStudent->execute();

        $student=$getStudent->get_result()->fetch_assoc();

        // Send Notification
        $title="Application Rejected";

        $message="Your admission application has been rejected. Please contact Admission Office.";

        $notify=$conn->prepare("
        INSERT INTO notifications
        (
        student_id,
        title,
        message
        )
        VALUES
        (?,?,?)
        ");

        $notify->bind_param(
        "iss",
        $student['student_id'],
        $title,
        $message
        );

        $notify->execute();

        echo "<script>
        alert('Application Rejected Successfully');
        window.location='manage_applications.php';
        </script>";

        exit;
    }

}

?>

<!-- ==========================================
        APPROVE / REJECT
========================================== -->

<div class="card mb-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-check-circle me-2"></i>

Admission Decision

</h5>

</div>

<div class="card-body text-center">

<form method="POST">

<a href="manage_applications.php"
class="btn btn-secondary btn-lg">

<i class="fas fa-arrow-left"></i>

Back

</a>

<?php
if($row['verification_status']=="Verified"){
?>

<button
type="submit"
name="approve"
class="btn btn-success btn-lg mx-2"
onclick="return confirm('Approve this application?');">

<i class="fas fa-check"></i>
Approve

</button>

<?php
}else{
?>

<button
class="btn btn-secondary btn-lg mx-2"
disabled>

<i class="fas fa-lock"></i>
Fee Not Verified

</button>

<?php
}
?>

<button
type="submit"
name="reject"
class="btn btn-danger btn-lg"
onclick="return confirm('Reject this application?');">

<i class="fas fa-times"></i>

Reject

</button>

</form>

</div>

</div>

<!-- Footer -->

<div class="text-center mt-5 mb-3">

<hr>

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

|

Admission Office

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>