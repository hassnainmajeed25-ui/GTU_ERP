<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit;
}

$student_id=$_SESSION['student_id'];

$message="";

/*=========================================
GET APPLICATION ID
=========================================*/

$app=$conn->prepare("

SELECT id

FROM applications

WHERE student_id=?

LIMIT 1

");

$app->bind_param("i",$student_id);

$app->execute();

$appResult=$app->get_result();

if($appResult->num_rows==0){

die("Please submit your admission application first.");

}

$appRow=$appResult->fetch_assoc();

$application_id=$appRow['id'];

/*=========================================
GET PROFILE
=========================================*/

$sql=$conn->prepare("

SELECT

s.id,
s.first_name,
s.last_name,
s.father_name,
s.mobile,
s.email,
s.cnic,
s.created_at,

ap.gender,
ap.date_of_birth,
ap.religion,
ap.nationality,
ap.address,
ap.city,
ap.province,
ap.postal_code,

d.student_photo

FROM students s

LEFT JOIN applications a
ON a.student_id=s.id

LEFT JOIN application_personal ap
ON ap.application_id=a.id

LEFT JOIN application_documents d
ON d.application_id=a.id

WHERE s.id=?

LIMIT 1

");

$sql->bind_param("i",$student_id);

$sql->execute();

$result=$sql->get_result();

$data=$result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>My Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#eef2f7;

font-family:Arial,Helvetica,sans-serif;

}

.card{

margin-top:30px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.12);

}

.card-header{

background:#0B1F52;

color:#fff;

padding:20px;

font-size:28px;

font-weight:bold;

}

.profile-photo{

width:170px;

height:170px;

border-radius:50%;

border:5px solid #0B1F52;

object-fit:cover;

}

.section-title{

font-size:22px;

font-weight:bold;

color:#0B1F52;

margin-top:30px;

margin-bottom:20px;

border-bottom:2px solid #ddd;

padding-bottom:10px;

}

label{

font-weight:bold;

}

</style>

</head>

<body>

<div class="container">

<div class="card">

<div class="card-header text-center">

<i class="fa-solid fa-user-graduate"></i>

My Profile

</div>

<div class="card-body">

<?php

if($message!=""){

?>

<div class="alert alert-success">

<?php echo $message; ?>

</div>

<?php } ?>

<form method="POST" enctype="multipart/form-data">

<div class="text-center mb-4">

<?php

if(!empty($data['student_photo'])){

?>

<img

src="../uploads/documents/<?php echo $data['student_photo']; ?>"

class="profile-photo">

<?php

}else{

?>

<img

src="../assets/images/default-user.png"

class="profile-photo">

<?php } ?>

<h3 class="mt-3">

<?php

echo $data['first_name']." ".$data['last_name'];

?>

</h3>

<p class="text-muted">

<?php echo $data['email']; ?>

</p>

<div class="mt-3">

<label class="btn btn-primary">

<i class="fa-solid fa-camera"></i>

Change Photo

<input

type="file"

name="profile_photo"

hidden>

</label>

</div>

</div>

<div class="section-title">

Basic Information

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label>

First Name

</label>

<input

type="text"

name="first_name"

class="form-control"

value="<?php echo $data['first_name']; ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label>

Last Name

</label>

<input

type="text"

name="last_name"

class="form-control"

value="<?php echo $data['last_name']; ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label>

Father Name

</label>

<input

type="text"

name="father_name"

class="form-control"

value="<?php echo $data['father_name']; ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label>

Mobile Number

</label>

<input

type="text"

name="mobile"

class="form-control"

value="<?php echo $data['mobile']; ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label>

Email Address

</label>

<input

type="email"

class="form-control"

value="<?php echo $data['email']; ?>"

readonly>

</div>

<div class="col-md-6 mb-3">

<label>

CNIC

</label>

<input

type="text"

class="form-control"

value="<?php echo $data['cnic']; ?>"

readonly>

</div>
<div class="section-title">

Personal Information

</div>

<div class="col-md-6 mb-3">

<label>Gender</label>

<select name="gender" class="form-select">

<option value="">Select Gender</option>

<option value="Male" <?php if($data['gender']=="Male") echo "selected"; ?>>Male</option>

<option value="Female" <?php if($data['gender']=="Female") echo "selected"; ?>>Female</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Date of Birth</label>

<input
type="date"
name="dob"
class="form-control"
value="<?php echo $data['date_of_birth']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Religion</label>

<input
type="text"
name="religion"
class="form-control"
value="<?php echo $data['religion']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Nationality</label>

<input
type="text"
name="nationality"
class="form-control"
value="<?php echo $data['nationality']; ?>">

</div>

<div class="col-md-12 mb-3">

<label>Address</label>

<textarea
name="address"
class="form-control"
rows="3"><?php echo $data['address']; ?></textarea>

</div>

<div class="col-md-6 mb-3">

<label>City</label>

<input
type="text"
name="city"
class="form-control"
value="<?php echo $data['city']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Province</label>

<select name="province" class="form-select">

<option value="">Select Province</option>

<option value="Punjab" <?php if($data['province']=="Punjab") echo "selected"; ?>>Punjab</option>

<option value="Sindh" <?php if($data['province']=="Sindh") echo "selected"; ?>>Sindh</option>

<option value="KPK" <?php if($data['province']=="KPK") echo "selected"; ?>>KPK</option>

<option value="Balochistan" <?php if($data['province']=="Balochistan") echo "selected"; ?>>Balochistan</option>

<option value="AJK" <?php if($data['province']=="AJK") echo "selected"; ?>>AJK</option>

<option value="Gilgit Baltistan" <?php if($data['province']=="Gilgit Baltistan") echo "selected"; ?>>Gilgit Baltistan</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Postal Code</label>

<input
type="text"
name="postal_code"
class="form-control"
value="<?php echo $data['postal_code']; ?>">

</div>

</div>

<hr>

<div class="d-flex justify-content-between">

<a href="dashboard.php" class="btn btn-secondary">

<i class="fa-solid fa-arrow-left"></i>

Back to Dashboard

</a>

<div>

<a href="change_password.php" class="btn btn-warning">

<i class="fa-solid fa-key"></i>

Change Password

</a>

<button
type="submit"
name="update_profile"
class="btn btn-success">

<i class="fa-solid fa-floppy-disk"></i>

Save Changes

</button>

</div>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>