<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

$admin_name=$_SESSION['super_admin_name'];

if(!isset($_GET['id'])){
    header("Location: manage_teachers.php");
    exit;
}

$id=(int)$_GET['id'];

$sql=$conn->query("

SELECT *

FROM teachers

WHERE id='$id'

LIMIT 1

");

if($sql->num_rows==0){

header("Location: manage_teachers.php");

exit;

}

$teacher=$sql->fetch_assoc();

$success="";
$error="";

if(isset($_POST['update_teacher'])){

$teacher_name=trim($_POST['teacher_name']);

$father_name=trim($_POST['father_name']);

$cnic=trim($_POST['cnic']);

$gender=$_POST['gender'];

$date_of_birth=$_POST['date_of_birth'];

$email=trim($_POST['email']);

$phone=trim($_POST['phone']);

$address=trim($_POST['address']);

$faculty_id=(int)$_POST['faculty_id'];

$designation=trim($_POST['designation']);

$qualification=trim($_POST['qualification']);

$joining_date=$_POST['joining_date'];

$employment_type=$_POST['employment_type'];

$role=$_POST['role'];

$status=$_POST['status'];

/*==========================
Duplicate Email Check
==========================*/

$check=$conn->prepare("

SELECT id

FROM teachers

WHERE email=?

AND id!=?

LIMIT 1

");

$check->bind_param("si",$email,$id);

$check->execute();

$r=$check->get_result();

if($r->num_rows>0){

$error="Email already exists.";

}else{

$update=$conn->prepare("

UPDATE teachers

SET

teacher_name=?,
father_name=?,
cnic=?,
gender=?,
date_of_birth=?,
email=?,
phone=?,
address=?,
faculty_id=?,
designation=?,
qualification=?,
joining_date=?,
employment_type=?,
role=?,
status=?

WHERE id=?

");

$update->bind_param(

"ssssssssissssssi",

$teacher_name,
$father_name,
$cnic,
$gender,
$date_of_birth,
$email,
$phone,
$address,
$faculty_id,
$designation,
$qualification,
$joining_date,
$employment_type,
$role,
$status,
$id

);

if($update->execute()){

$success="Teacher Updated Successfully.";

$sql=$conn->query("SELECT * FROM teachers WHERE id='$id'");
$teacher=$sql->fetch_assoc();

}else{

$error="Database Error.";

}

}

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Teacher</title>

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

}

.sidebar h3{

color:#fff;
text-align:center;
padding:20px;

}

.sidebar a{

display:block;
padding:14px 20px;
color:#fff;
text-decoration:none;

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
margin-bottom:30px;
display:flex;
justify-content:space-between;
align-items:center;

}

.card{

border:none;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);

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

<a href="manage_teachers.php" class="active">
<i class="fas fa-chalkboard-teacher me-2"></i>
Manage Teachers
</a>

<a href="../admins/logout.php">
<i class="fas fa-right-from-bracket me-2"></i>
Logout
</a>

</div>

<div class="main">

<div class="topbar">

<div>

<h3>Edit Teacher</h3>

<small class="text-muted">

Global Tech University ERP

</small>

</div>

<div>

Welcome,

<strong>

<?php echo htmlspecialchars($admin_name); ?>

</strong>

</div>

</div>

<?php if($success!=""){ ?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

Edit Teacher Information

</h5>

</div>

<div class="card-body">

<form method="POST">
<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Teacher Name

</label>

<input
type="text"
name="teacher_name"
class="form-control"
value="<?php echo htmlspecialchars($teacher['teacher_name']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Father Name

</label>

<input
type="text"
name="father_name"
class="form-control"
value="<?php echo htmlspecialchars($teacher['father_name']); ?>">

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

CNIC

</label>

<input
type="text"
name="cnic"
class="form-control"
value="<?php echo htmlspecialchars($teacher['cnic']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Gender

</label>

<select name="gender" class="form-select">

<option value="Male" <?php if($teacher['gender']=="Male") echo "selected"; ?>>
Male
</option>

<option value="Female" <?php if($teacher['gender']=="Female") echo "selected"; ?>>
Female
</option>

</select>

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

Date of Birth

</label>

<input
type="date"
name="date_of_birth"
class="form-control"
value="<?php echo $teacher['date_of_birth']; ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Email

</label>

<input
type="email"
name="email"
class="form-control"
value="<?php echo htmlspecialchars($teacher['email']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Phone

</label>

<input
type="text"
name="phone"
class="form-control"
value="<?php echo htmlspecialchars($teacher['phone']); ?>">

</div>

<div class="col-md-12 mb-3">

<label class="form-label">

Address

</label>

<textarea
name="address"
class="form-control"
rows="3"><?php echo htmlspecialchars($teacher['address']); ?></textarea>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Faculty

</label>

<select
name="faculty_id"
class="form-select"
required>

<?php

$f=$conn->query("

SELECT *

FROM faculties

ORDER BY faculty_name

");

while($fac=$f->fetch_assoc()){

?>

<option
value="<?php echo $fac['id'];?>"

<?php

if($teacher['faculty_id']==$fac['id']){

echo "selected";

}

?>

>

<?php echo htmlspecialchars($fac['faculty_name']);?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Designation

</label>

<input
type="text"
name="designation"
class="form-control"
value="<?php echo htmlspecialchars($teacher['designation']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Qualification

</label>

<input
type="text"
name="qualification"
class="form-control"
value="<?php echo htmlspecialchars($teacher['qualification']); ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Joining Date

</label>

<input
type="date"
name="joining_date"
class="form-control"
value="<?php echo $teacher['joining_date']; ?>">

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

Employment Type

</label>

<select name="employment_type" class="form-select">

<option <?php if($teacher['employment_type']=="Permanent") echo "selected"; ?>>
Permanent
</option>

<option <?php if($teacher['employment_type']=="Contract") echo "selected"; ?>>
Contract
</option>

<option <?php if($teacher['employment_type']=="Visiting") echo "selected"; ?>>
Visiting
</option>

</select>

</div>


<div class="col-md-6 mb-3">

<label class="form-label">

Role

</label>

<select name="role" class="form-select">

<option <?php if($teacher['role']=="Teacher") echo "selected"; ?>>
Teacher
</option>

<option <?php if($teacher['role']=="HOD") echo "selected"; ?>>
HOD
</option>

<option <?php if($teacher['role']=="Dean") echo "selected"; ?>>
Dean
</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Status

</label>

<select
name="status"
class="form-select">

<option value="Active"
<?php if($teacher['status']=="Active") echo "selected";?>>

Active

</option>

<option value="Inactive"
<?php if($teacher['status']=="Inactive") echo "selected";?>>

Inactive

</option>

</select>

</div>

</div>

<hr>

<div class="text-end">

<a
href="manage_teachers.php"
class="btn btn-secondary">

Back

</a>

<button
type="submit"
name="update_teacher"
class="btn btn-success">

Update Teacher

</button>

</div>

</form>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>