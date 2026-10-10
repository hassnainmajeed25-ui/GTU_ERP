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

$admin_id=$_SESSION['admin_id'];

$success="";
$error="";


/*=========================================
PROFILE IMAGE UPLOAD
=========================================*/

$success="";
$error="";


/*=========================================
PROFILE IMAGE UPLOAD
=========================================*/

if(isset($_POST['upload_image'])){


if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error']==0){


$file=$_FILES['profile_image'];

$ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));


$allowed=['jpg','jpeg','png','webp'];


if(in_array($ext,$allowed)){


$new_name="admin_".$admin_id."_".time().".".$ext;


$upload_path="../uploads/admin_profiles/".$new_name;


/* OLD IMAGE */

$getOld=$conn->prepare("

SELECT profile_image

FROM admins

WHERE id=?

LIMIT 1

");


$getOld->bind_param("i",$admin_id);

$getOld->execute();


$oldResult=$getOld->get_result();


$oldData=$oldResult->fetch_assoc();



if(move_uploaded_file($file['tmp_name'],$upload_path)){



if(!empty($oldData['profile_image'])){


$old_file="../uploads/admin_profiles/".$oldData['profile_image'];


if(file_exists($old_file)){


unlink($old_file);


}


}



/* SAVE IMAGE NAME */

$update=$conn->prepare("

UPDATE admins

SET profile_image=?

WHERE id=?

");


$update->bind_param(

"si",

$new_name,

$admin_id

);



if($update->execute()){


header("Location: profile.php?image_success=1");

exit;


}else{


$error="Database update failed.";

}


}else{


$error="Image upload failed.";

}



}else{


$error="Only JPG PNG WEBP images allowed.";

}



}


}

/*=========================================
GET ADMIN DETAILS
=========================================*/

$stmt=$conn->prepare("

SELECT *

FROM admins

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$admin_id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location:dashboard.php");

exit;

}

$admin=$result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Admission Office Profile</title>

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
box-shadow:0 3px 10px rgba(0,0,0,.1);
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
}

.profile-card{

background:#fff;

border:none;

border-radius:18px;

box-shadow:0 4px 15px rgba(0,0,0,.1);

padding:40px;

}

.profile-icon{

width:120px;

height:120px;

border-radius:50%;

background:#0B1F52;

color:#fff;

font-size:55px;

display:flex;

align-items:center;

justify-content:center;

margin:auto;

margin-bottom:20px;

}

.table td{

padding:14px;

vertical-align:middle;

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

<a href="manage_applications.php">
<i class="fas fa-file-alt me-2"></i>
Manage Applications
</a>

<a href="merit_list.php">
<i class="fas fa-ranking-star me-2"></i>
Merit List
</a>

<a href="selected_students.php">
<i class="fas fa-user-check me-2"></i>
Selected Students
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

<a href="profile.php" class="active">
<i class="fas fa-user-circle me-2"></i>
Profile
</a>

<a href="../admins/logout.php">
<i class="fas fa-sign-out-alt me-2"></i>
Logout
</a>

</div>

<!-- Main -->

<div class="main">

<div class="topbar">

<div>

<h3>My Profile</h3>

<small class="text-muted">

Admission Office

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin['full_name']); ?>

</strong>

</div>

</div>

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="profile-card">

<div class="profile-icon">

<?php

if(!empty($admin['profile_image'])){

?>

<img

src="../uploads/admin_profiles/<?php echo $admin['profile_image']; ?>"

style="width:120px;height:120px;border-radius:50%;object-fit:cover;">

<?php

}else{

?>

<i class="fas fa-user"></i>

<?php

}

?>

</div>

<?php

if($success!=""){

?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php

}


if($error!=""){

?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php

}

?>

<h3 class="text-center mb-4">

<?php echo htmlspecialchars($admin['full_name']); ?>

</h3>

<table class="table table-bordered">

<tr>

<th width="35%">Full Name</th>

<td>

<?php echo htmlspecialchars($admin['full_name']); ?>

</td>

</tr>

<tr>

<th>Email Address</th>

<td>

<?php echo htmlspecialchars($admin['email']); ?>

</td>

</tr>

<tr>

<th>Department</th>

<td>

<?php echo htmlspecialchars($admin['department']); ?>

</td>

</tr>

<tr>

<th>Status</th>

<td>

<span class="badge bg-success">

<?php echo htmlspecialchars($admin['status']); ?>

</span>

</td>

</tr>

<tr>

<th>Admin ID</th>

<td>

ADM-<?php echo str_pad($admin['id'],4,"0",STR_PAD_LEFT); ?>

</td>

</tr>

</table>
<form method="POST" enctype="multipart/form-data">


<div class="mb-3">

<label class="form-label">

Upload Profile Image

</label>


<input

type="file"

name="profile_image"

class="form-control"

accept="image/*"

required>

</div>


<button

type="submit"

name="upload_image"

class="btn btn-primary">

<i class="fas fa-upload me-2"></i>

Upload Images

</button>


</form>

<hr class="mt-4">

<div class="row mt-4">

<div class="col-md-4 mb-3">

<button
class="btn btn-primary w-100"
disabled>

<i class="fas fa-user-edit me-2"></i>

Edit Profile

<br>

<small>(Coming Soon)</small>

</button>

</div>

<div class="col-md-4 mb-3">

<button
class="btn btn-warning w-100"
disabled>

<i class="fas fa-key me-2"></i>

Change Password

<br>

<small>(Coming Soon)</small>

</button>

</div>

<div class="col-md-4 mb-3">

<a
href="dashboard.php"
class="btn btn-success w-100">

<i class="fas fa-arrow-left me-2"></i>

Back to Dashboard

</a>

</div>

</div>

<hr class="mt-5">

<div class="alert alert-info">

<h5>

<i class="fas fa-circle-info me-2"></i>

Profile Information

</h5>

<p class="mb-2">

This profile information is managed by the <strong>Super Administrator</strong>.

</p>

<p class="mb-0">

To update your profile details or reset your password, please contact the Super Admin.

</p>

</div>

</div>

</div>

</div>

</div>

<hr class="mt-5">

<div class="text-center">

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>