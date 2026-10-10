<?php

session_start();

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
LOAD STUDENT PROFILE
=========================================*/

$sql="

SELECT

students.*,

programs.program_name,
programs.program_code,

faculties.faculty_name,

application_personal.gender,
application_personal.date_of_birth,
application_personal.religion,
application_personal.nationality,
application_personal.address,
application_personal.city,
application_personal.province,
application_personal.postal_code

FROM students

LEFT JOIN programs
ON students.program_id=programs.id

LEFT JOIN faculties
ON programs.faculty_id=faculties.id

LEFT JOIN applications
ON applications.student_id=students.id

LEFT JOIN application_personal
ON application_personal.application_id=applications.id

WHERE students.id=?

LIMIT 1

";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$student_id);

$stmt->execute();

$result=$stmt->get_result();

$student=$result->fetch_assoc();

$page_title="My Profile";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>

My Profile | GTU ERP

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{

background:#eef5fb;

font-family:'Segoe UI',sans-serif;

}

/*=====================================
SIDEBAR
=====================================*/

.sidebar{

position:fixed;

left:0;

top:0;

width:270px;

height:100vh;

background:#0B3D91;

overflow-y:auto;

}

.sidebar .logo{

text-align:center;

padding:25px;

border-bottom:1px solid rgba(255,255,255,.15);

}

.sidebar .logo img{

width:80px;

margin-bottom:10px;

}

.sidebar .logo h4{

color:#fff;

margin:0;

font-weight:bold;

}

.sidebar .logo small{

color:#ddd;

}

.sidebar ul{

list-style:none;

padding:15px 0;

}

.sidebar ul li{

margin-bottom:5px;

}

.sidebar ul li a{

display:block;

padding:15px 25px;

color:#fff;

text-decoration:none;

transition:.3s;

}

.sidebar ul li a:hover{

background:#198754;

padding-left:35px;

}

.sidebar ul li a.active{

background:#FDB913;

color:#000;

font-weight:bold;

}

/*=====================================
MAIN CONTENT
=====================================*/

.main{

margin-left:270px;

padding:35px;

}

/*=====================================
PROFILE CARD
=====================================*/

.profile-card{

background:#fff;

border-radius:20px;

box-shadow:0 8px 25px rgba(0,0,0,.08);

padding:35px;

}

</style>

</head>

<body>

<!-- =========================================
SIDEBAR
========================================= -->

<div class="sidebar">

<div class="logo">

<img src="../assets/images/logo.png">

<h4>GTU ERP</h4>

<small>Student Portal</small>

</div>

<ul>

<li>
<a href="dashboard.php">
<i class="fas fa-house"></i>
Dashboard
</a>
</li>

<li>
<a href="profile.php" class="active">
<i class="fas fa-user"></i>
My Profile
</a>
</li>

<li>
<a href="attendance.php">
<i class="fas fa-calendar-check"></i>
Attendance
</a>
</li>

<li>
<a href="results.php">
<i class="fas fa-square-poll-vertical"></i>
Results
</a>
</li>

<li>
<a href="fee_status.php">
<i class="fas fa-money-check-dollar"></i>
Fee Status
</a>
</li>

<li>
<a href="messages.php">
<i class="fas fa-envelope"></i>
Messages
</a>
</li>

<li>
<a href="logout.php">
<i class="fas fa-right-from-bracket"></i>
Logout
</a>
</li>

</ul>

</div>

<!-- =========================================
MAIN CONTENT
========================================= -->

<div class="main">

<div class="profile-card">

<div class="row">

<div class="col-lg-4">

<?php

$image="uploads/profile/".$student['profile_photo'];

if(!empty($student['profile_photo']) && file_exists($image)){

?>

<img
src="<?php echo $image; ?>"
style="width:180px;height:180px;border-radius:50%;object-fit:cover;border:6px solid #0B3D91;">

<?php

}else{

?>

<img
src="../assets/images/default-user.png"
style="width:180px;height:180px;border-radius:50%;object-fit:cover;border:6px solid #FDB913;">

<?php

}

?>

<h3 class="mt-3">

<?php echo $student['first_name']." ".$student['last_name']; ?>

</h3>

<p class="text-primary fw-bold">

<?php echo $student['program_name']; ?>

</p>

</div>

<hr>

<form
action="upload_profile_photo.php"
method="POST"
enctype="multipart/form-data">

<div class="mb-3">

<input
type="file"
name="profile_photo"
class="form-control"
accept="image/*"
required>

</div>

<button
class="btn btn-warning w-100">

<i class="fas fa-camera"></i>

Upload Profile Photo

</button>

</form>

</div>

<!-- LEFT COLUMN END -->

<div class="col-lg-8">

<form action="update_profile.php" method="POST">

<h3 class="text-primary mb-4">

<i class="fas fa-id-card"></i>

Student Information

</h3>

<div class="row">

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Registration Number

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['registration_no']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Roll Number

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['roll_no']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

First Name

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['first_name']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Last Name

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['last_name']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Father Name

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['father_name']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Email Address

</label>

<input
type="email"
class="form-control"
value="<?php echo htmlspecialchars($student['email']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Mobile Number

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['mobile']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

CNIC

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['cnic']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Faculty

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['faculty_name']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Program

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['program_name']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Semester

</label>

<input
type="text"
class="form-control"
value="Semester <?php echo htmlspecialchars($student['semester']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Shift

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['shift']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Gender

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['gender']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Date of Birth

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['date_of_birth']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Religion

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['religion']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="fw-bold text-primary">

Nationality

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['nationality']); ?>"
readonly>

</div>

<div class="col-12 mb-3">

<label class="fw-bold text-primary">

Address

</label>

<textarea
class="form-control"
rows="3"><?php echo htmlspecialchars($student['address']); ?></textarea>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold text-primary">

City

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['city']); ?>">

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold text-primary">

Province

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['province']); ?>">

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold text-primary">

Postal Code

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($student['postal_code']); ?>">

</div>

<div class="col-12 mt-4">

<button
type="submit"
class="btn btn-primary px-5">

<i class="fas fa-floppy-disk"></i>

Update Profile

</button>

<a
href="dashboard.php"
class="btn btn-warning px-5 ms-2">

<i class="fas fa-arrow-left"></i>

Back to Dashboard

</a>

</div> <!-- row -->

</form>

</div> <!-- profile-card -->

</div> <!-- main -->

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>