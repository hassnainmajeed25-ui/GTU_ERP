<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

if(!isset($_GET['id'])){

    header("Location: manage_applications.php");
    exit;

}

$id=(int)$_GET['id'];

$stmt=$conn->prepare("

SELECT

a.*,

s.first_name,

s.last_name,

s.father_name,

s.email,

s.mobile,

s.cnic,

f.faculty_name,

p.program_name

FROM applications a

LEFT JOIN students s
ON a.student_id=s.id

LEFT JOIN faculties f
ON a.faculty_id=f.id

LEFT JOIN programs p
ON a.program_id=p.id

WHERE a.id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

    header("Location: manage_applications.php");
    exit;

}

$row=$result->fetch_assoc();
$documents=$conn->prepare("

SELECT *

FROM application_documents

WHERE application_id=?

LIMIT 1

");

$documents->bind_param("i",$id);

$documents->execute();

$doc=$documents->get_result()->fetch_assoc();

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

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

.info-box{

background:#f8f9fa;

border-left:5px solid #0B5ED7;

padding:18px;

border-radius:10px;

margin-bottom:20px;

}

img.photo{

width:180px;

height:200px;

object-fit:cover;

border:3px solid #dee2e6;

border-radius:10px;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header">

<i class="fas fa-file-alt me-2"></i>

Application Details

</div>

<div class="card-body">
<div class="row">

<div class="col-lg-6">

<div class="info-box">

<h5 class="mb-3">

<i class="fas fa-user-graduate me-2"></i>

Student Information

</h5>

<table class="table table-bordered">

<tr>

<th width="40%">First Name</th>

<td><?php echo htmlspecialchars($row['first_name']); ?></td>

</tr>

<tr>

<th>Last Name</th>

<td><?php echo htmlspecialchars($row['last_name']); ?></td>

</tr>

<tr>

<th>Father Name</th>

<td><?php echo htmlspecialchars($row['father_name']); ?></td>

</tr>

<tr>

<th>Email</th>

<td><?php echo htmlspecialchars($row['email']); ?></td>

</tr>

<tr>

<th>Mobile</th>

<td><?php echo htmlspecialchars($row['mobile']); ?></td>

</tr>

<tr>

<th>CNIC</th>

<td><?php echo htmlspecialchars($row['cnic']); ?></td>

</tr>

</table>

</div>

</div>

<div class="col-lg-6">

<div class="info-box">

<h5 class="mb-3">

<i class="fas fa-id-card me-2"></i>

Application Information

</h5>

<table class="table table-bordered">

<tr>

<th width="40%">Application ID</th>

<td><?php echo htmlspecialchars($row['application_id']); ?></td>

</tr>

<tr>

<th>Faculty</th>

<td><?php echo htmlspecialchars($row['faculty_name']); ?></td>

</tr>

<tr>

<th>Program</th>

<td><?php echo htmlspecialchars($row['program_name']); ?></td>

</tr>

<tr>

<th>Shift</th>

<td><?php echo htmlspecialchars($row['shift']); ?></td>

</tr>

<tr>

<th>Merit Marks</th>

<td><?php echo htmlspecialchars($row['merit_marks']); ?></td>

</tr>

<tr>

<th>Registration No</th>

<td>

<?php

if(!empty($row['registration_no'])){

    echo htmlspecialchars($row['registration_no']);

}else{

    echo '<span class="text-muted">Not Generated</span>';

}

?>

</td>

</tr>

<tr>

<th>Status</th>

<td>

<?php

$status = $row['application_status'];

if($status=="Draft"){

    echo '<span class="badge bg-secondary">Draft</span>';

}elseif($status=="Submitted"){

    echo '<span class="badge bg-primary">Submitted</span>';

}elseif($status=="Under Review"){

    echo '<span class="badge bg-warning text-dark">Under Review</span>';

}elseif($status=="Approved"){

    echo '<span class="badge bg-success">Approved</span>';

}elseif($status=="Rejected"){

    echo '<span class="badge bg-danger">Rejected</span>';

}else{

    echo '<span class="badge bg-dark">'.htmlspecialchars($status).'</span>';

}

?>

</td>

</tr>

</table>

</div>

</div>

</div>
<div class="info-box">

<h5 class="mb-4">

<i class="fas fa-folder-open me-2"></i>

Uploaded Documents

</h5>

<div class="row">

<div class="col-lg-3 text-center">

<?php if(!empty($doc['student_photo'])){ ?>

<img

src="../uploads/student_photos/<?php echo $doc['student_photo']; ?>"

class="photo mb-3">

<br><br>

<a

href="../uploads/student_photos/<?php echo $doc['student_photo']; ?>"

target="_blank"

class="btn btn-primary btn-sm me-2">

<i class="fas fa-eye"></i>

View

</a>

<a

href="../uploads/student_photos/<?php echo $doc['student_photo']; ?>"

download

class="btn btn-success btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<?php }else{ ?>

<div class="alert alert-danger">

Photo Not Uploaded

</div>

<?php } ?>

</div>

<div class="col-lg-9">

<table class="table table-bordered align-middle">

<tr>

<th width="35%">

Student CNIC

</th>

<td>

<?php if(!empty($doc['student_cnic'])){ ?>

<a

href="../uploads/student_cnic/<?php echo $doc['student_cnic']; ?>"

target="_blank"

class="btn btn-primary btn-sm me-2">

<i class="fas fa-eye"></i>

View

</a>

<a

href="../uploads/student_cnic/<?php echo $doc['student_cnic']; ?>"

download

class="btn btn-success btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<?php }else{ ?>

<span class="text-danger">

Not Uploaded

</span>

<?php } ?>

</td>

</tr>

<tr>

<th>

Father CNIC

</th>

<td>

<?php if(!empty($doc['father_cnic'])){ ?>

<a

href="../uploads/father_cnic/<?php echo $doc['father_cnic']; ?>"

target="_blank"

class="btn btn-primary btn-sm me-2">

<i class="fas fa-eye"></i>

View

</a>

<a

href="../uploads/father_cnic/<?php echo $doc['father_cnic']; ?>"

download

class="btn btn-success btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<?php }else{ ?>

<span class="text-danger">

Not Uploaded

</span>

<?php } ?>

</td>

</tr>

<tr>

<th>

Matric Result Card

</th>

<td>

<?php if(!empty($doc['matric_result_card'])){ ?>

<a

href="../uploads/matric_result/<?php echo $doc['matric_result_card']; ?>"

target="_blank"

class="btn btn-primary btn-sm me-2">

<i class="fas fa-eye"></i>

View

</a>

<a

href="../uploads/matric_result/<?php echo $doc['matric_result_card']; ?>"

download

class="btn btn-success btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<?php }else{ ?>

<span class="text-danger">

Not Uploaded

</span>

<?php } ?>

</td>

</tr>

<tr>

<th>

Intermediate Result Card

</th>

<td>

<?php if(!empty($doc['inter_result_card'])){ ?>

<a

href="../uploads/inter_result/<?php echo $doc['inter_result_card']; ?>"

target="_blank"

class="btn btn-primary btn-sm me-2">

<i class="fas fa-eye"></i>

View

</a>

<a

href="../uploads/inter_result/<?php echo $doc['inter_result_card']; ?>"

download

class="btn btn-success btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<?php }else{ ?>

<span class="text-danger">

Not Uploaded

</span>

<?php } ?>

</td>

</tr>

</table>

</div>

</div>

<div class="d-flex justify-content-between mt-4">

<a

href="manage_applications.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="button"

onclick="window.print();"

class="btn btn-dark">

<i class="fas fa-print me-2"></i>

Print Application

</button>

</div>

</div>
</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>