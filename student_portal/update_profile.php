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

$student_id = $_SESSION['student_id'];

$message = "";
$type = "";

/*=========================================
LOAD STUDENT DATA
=========================================*/

$sql = "

SELECT

students.*,

programs.program_name,

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
ON students.program_id = programs.id

LEFT JOIN faculties
ON programs.faculty_id = faculties.id

LEFT JOIN applications
ON applications.student_id = students.id

LEFT JOIN application_personal
ON application_personal.application_id = applications.id

WHERE students.id = ?

LIMIT 1

";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$student_id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    session_destroy();

    header("Location: login.php");
    exit;

}

$student = $result->fetch_assoc();

/*=========================================
UPDATE PROFILE
=========================================*/

if(isset($_POST['update_profile'])){

    $email       = trim($_POST['email']);
    $mobile      = trim($_POST['mobile']);
    $address     = trim($_POST['address']);
    $city        = trim($_POST['city']);
    $province    = trim($_POST['province']);
    $postal_code = trim($_POST['postal_code']);

        /*=========================================
    PROFILE PHOTO UPLOAD
    =========================================*/

    $profile_photo = $student['profile_photo'];

    if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error']==0){

        $allowed = ['jpg','jpeg','png','webp'];

        $fileName = $_FILES['profile_photo']['name'];

        $tmpName = $_FILES['profile_photo']['tmp_name'];

        $size = $_FILES['profile_photo']['size'];

        $ext = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));

        if(in_array($ext,$allowed)){

            if($size <= 5*1024*1024){

                $newName = "STU_".$student_id."_".time().".".$ext;

                $uploadPath = "uploads/profile/".$newName;

                if(move_uploaded_file($tmpName,$uploadPath)){

                    if(!empty($student['profile_photo'])){

                        $oldImage = "uploads/profile/".$student['profile_photo'];

                        if(file_exists($oldImage)){

                            unlink($oldImage);

                        }

                    }

                    $profile_photo = $newName;

                }

            }else{

                $message = "Image size must be less than 5MB.";

                $type = "danger";

            }

        }else{

            $message = "Only JPG, JPEG, PNG and WEBP images are allowed.";

            $type = "danger";

        }

    }

    /*=========================================
    UPDATE STUDENTS TABLE
    =========================================*/

    $updateStudent = $conn->prepare("

    UPDATE students

    SET

    email=?,

    mobile=?,

    profile_photo=?

    WHERE id=?

    ");

    $updateStudent->bind_param(

    "sssi",

    $email,

    $mobile,

    $profile_photo,

    $student_id

    );

    $updateStudent->execute();

        /*=========================================
    UPDATE PERSONAL INFORMATION
    =========================================*/

    $updatePersonal = $conn->prepare("

    UPDATE application_personal

    INNER JOIN applications

    ON applications.id = application_personal.application_id

    SET

    application_personal.address=?,

    application_personal.city=?,

    application_personal.province=?,

    application_personal.postal_code=?

    WHERE applications.student_id=?

    ");

    $updatePersonal->bind_param(

    "ssssi",

    $address,

    $city,

    $province,

    $postal_code,

    $student_id

    );

    $updatePersonal->execute();

    /*=========================================
    SUCCESS MESSAGE
    =========================================*/

    $_SESSION['profile_success'] = "Profile Updated Successfully.";

    header("Location: update_profile.php");

    exit;

}

/*=========================================
PAGE TITLE
=========================================*/

$page_title = "Update Profile";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>

Update Profile | GTU ERP

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

</style>

</head>

<body>

<!-- =========================================
SIDEBAR
========================================= -->

<div class="sidebar">

<div class="logo text-center py-4">

<img src="../assets/images/logo.png" width="80">

<h4 class="text-white mt-2">

GTU ERP

</h4>

<small class="text-light">

Student Portal

</small>

</div>

<ul class="list-unstyled mt-4">

<li>
<a href="dashboard.php">
<i class="fas fa-house"></i>
Dashboard
</a>
</li>

<li>
<a href="profile.php">
<i class="fas fa-user"></i>
My Profile
</a>
</li>

<li>
<a href="update_profile.php" class="active">
<i class="fas fa-user-pen"></i>
Update Profile
</a>
</li>

<li>
<a href="subjects.php">
<i class="fas fa-book-open"></i>
My Subjects
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

<div class="container-fluid py-4">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-primary text-white">

<h3>

<i class="fas fa-user-pen"></i>

Update My Profile

</h3>

</div>

<div class="card-body">

<?php

if(isset($_SESSION['profile_success'])){

?>

<div class="alert alert-success">

<i class="fas fa-circle-check"></i>

<?php

echo $_SESSION['profile_success'];

unset($_SESSION['profile_success']);

?>

</div>

<?php

}

?>

<form

method="POST"

enctype="multipart/form-data">

<div class="row">

<div class="col-lg-4 text-center">

<?php

$image="uploads/profile/".$student['profile_photo'];

if(!empty($student['profile_photo']) && file_exists($image)){

?>

<img

src="<?php echo $image; ?>"

style="width:180px;height:180px;border-radius:50%;object-fit:cover;border:5px solid #0B3D91;">

<?php

}else{

?>

<img

src="../assets/images/default-user.png"

style="width:180px;height:180px;border-radius:50%;object-fit:cover;border:5px solid #FDB913;">

<?php

}

?>

<div class="mt-4">

<label class="form-label fw-bold">

Profile Photo

</label>

<input

type="file"

name="profile_photo"

class="form-control"

accept=".jpg,.jpeg,.png,.webp">

</div>

</div>

<div class="col-lg-8">

<div class="row">

<div class="col-md-6 mb-3">

<label>Email Address</label>

<input

type="email"

name="email"

class="form-control"

value="<?php echo htmlspecialchars($student['email']); ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label>Mobile Number</label>

<input

type="text"

name="mobile"

class="form-control"

value="<?php echo htmlspecialchars($student['mobile']); ?>"

required>

</div>

<div class="col-12 mb-3">

<label>Address</label>

<textarea

name="address"

class="form-control"

rows="3"><?php echo htmlspecialchars($student['address']); ?></textarea>

</div>

<div class="col-md-4 mb-3">

<label>City</label>

<input

type="text"

name="city"

class="form-control"

value="<?php echo htmlspecialchars($student['city']); ?>">

</div>

<div class="col-md-4 mb-3">

<label>Province</label>

<input

type="text"

name="province"

class="form-control"

value="<?php echo htmlspecialchars($student['province']); ?>">

</div>

<div class="col-md-4 mb-3">

<label>Postal Code</label>

<input

type="text"

name="postal_code"

class="form-control"

value="<?php echo htmlspecialchars($student['postal_code']); ?>">

</div>

<div class="col-12 mt-4">

<button

type="submit"

name="update_profile"

class="btn btn-primary btn-lg">

<i class="fas fa-floppy-disk"></i>

Save Changes

</button>

<a

href="profile.php"

class="btn btn-warning btn-lg ms-2">

<i class="fas fa-arrow-left"></i>

Back to Profile

</a>

</div>

</div>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

<style>

.sidebar{

position:fixed;

left:0;

top:0;

width:270px;

height:100vh;

background:#0B3D91;

overflow-y:auto;

}

.sidebar ul{

padding:0;

margin:0;

}

.sidebar ul li{

list-style:none;

}

.sidebar ul li a{

display:block;

padding:15px 25px;

color:#fff;

text-decoration:none;

transition:.3s;

}

.sidebar ul li a:hover{

background:#FDB913;

color:#000;

padding-left:35px;

}

.sidebar ul li a.active{

background:#198754;

color:#fff;

font-weight:bold;

}

.main{

margin-left:270px;

padding:30px;

}

.card{

border-radius:20px;

}

.form-control{

height:50px;

border-radius:12px;

}

textarea.form-control{

height:auto;

}

.btn-primary{

background:#0B3D91;

border:none;

}

.btn-primary:hover{

background:#072d6b;

}

.btn-warning{

font-weight:bold;

}

</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>