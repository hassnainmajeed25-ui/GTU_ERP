<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

if(!isset($_GET['id'])){

    header("Location: manage_fee_structure.php");
    exit;

}

$id=(int)$_GET['id'];

/* Current Record */

$get=$conn->prepare("

SELECT *

FROM fee_structure

WHERE id=?

LIMIT 1

");

$get->bind_param("i",$id);

$get->execute();

$result=$get->get_result();

if($result->num_rows==0){

    header("Location: manage_fee_structure.php");
    exit;

}

$row=$result->fetch_assoc();

/* Active Faculties */

$faculties=$conn->query("

SELECT *

FROM faculties

WHERE status='Active'

ORDER BY faculty_name ASC

");

/* Active Sessions */

$sessions=$conn->query("

SELECT *

FROM admission_sessions

WHERE status='Active'

ORDER BY session_name ASC

");

$message="";

if(isset($_POST['update'])){

    $faculty_id=(int)$_POST['faculty_id'];

    $program_id=(int)$_POST['program_id'];

    $session_id=(int)$_POST['session_id'];

    $admission_fee=(float)$_POST['admission_fee'];

    $semester=(int)$_POST['semester'];

    $shift=$_POST['shift'];

    $tuition_fee=(float)$_POST['tuition_fee'];

    $exam_fee=(float)$_POST['exam_fee'];

    $library_fee=(float)$_POST['library_fee'];

    $lab_fee=(float)$_POST['lab_fee'];

    $other_fee=(float)$_POST['other_fee'];

    $status=$_POST['status'];

    $update=$conn->prepare("

    UPDATE fee_structure

    SET

    faculty_id=?,

    program_id=?,

    session_id=?,

    semester=?,

    shift=?,

    admission_fee=?,

    tuition_fee=?,

    exam_fee=?,

    library_fee=?,

    lab_fee=?,

    other_fee=?,

    status=?

    WHERE id=?

    ");

    $update->bind_param(

"iiisidddddssi",

$faculty_id,

$program_id,

$session_id,

$semester,

$shift,

$admission_fee,

$tuition_fee,

$exam_fee,

$library_fee,

$lab_fee,

$other_fee,

$status,

$id

);

    if($update->execute()){

        header("Location: manage_fee_structure.php");
        exit;

    }else{

        $message="<div class='alert alert-danger'>Update Failed.</div>";

    }

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Fee Structure</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f4f6f9;

}

.card{

margin-top:40px;

border:none;

border-radius:15px;

box-shadow:0 8px 25px rgba(0,0,0,.10);

}

.card-header{

background:#0d6efd;

color:#fff;

font-size:24px;

font-weight:bold;

padding:18px;

}

.form-control,
.form-select{

height:50px;

border-radius:10px;

}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card">

<div class="card-header">

<i class="fas fa-edit me-2"></i>

Edit Fee Structure

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">Faculty</label>

<select

name="faculty_id"

id="faculty"

class="form-select"

required>

<?php while($faculty=$faculties->fetch_assoc()){ ?>

<option

value="<?php echo $faculty['id']; ?>"

<?php if($faculty['id']==$row['faculty_id']) echo "selected"; ?>>

<?php echo $faculty['faculty_name']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">Program</label>

<select

name="program_id"

id="program"

class="form-select"

required>

<?php

$programs=$conn->prepare("

SELECT *

FROM programs

WHERE faculty_id=?

ORDER BY program_name ASC

");

$programs->bind_param("i",$row['faculty_id']);

$programs->execute();

$list=$programs->get_result();

while($program=$list->fetch_assoc()){

?>

<option

value="<?php echo $program['id']; ?>"

<?php if($program['id']==$row['program_id']) echo "selected"; ?>>

<?php echo $program['program_name']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">Admission Session</label>

<select

name="session_id"

class="form-select"

required>

<?php while($session=$sessions->fetch_assoc()){ ?>

<option

value="<?php echo $session['id']; ?>"

<?php if($session['id']==$row['session_id']) echo "selected"; ?>>

<?php echo $session['session_name']; ?>

</option>

<?php } ?>

</select>

</div>

</div>


<div class="col-md-4 mb-3">

<label class="form-label">

Semester

</label>

<select

name="semester"

class="form-select"

required>

<?php

for($i=1;$i<=8;$i++){

?>

<option

value="<?php echo $i; ?>"

<?php if($row['semester']==$i) echo "selected"; ?>>

Semester <?php echo $i; ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Shift

</label>

<select

name="shift"

class="form-select"

required>

<option

value="Morning"

<?php if($row['shift']=="Morning") echo "selected"; ?>>

Morning

</option>

<option

value="Evening"

<?php if($row['shift']=="Evening") echo "selected"; ?>>

Evening

</option>

<option

value="Replica"

<?php if($row['shift']=="Replica") echo "selected"; ?>>

Replica

</option>

</select>

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Admission Fee

</label>

<input

type="number"

name="admission_fee"

class="form-control"

value="<?php echo $row['admission_fee']; ?>"

step="0.01"

required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Tuition Fee

</label>

<input

type="number"

name="tuition_fee"

class="form-control"

value="<?php echo $row['tuition_fee']; ?>"

step="0.01"

required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Exam Fee

</label>

<input

type="number"

name="exam_fee"

class="form-control"

value="<?php echo $row['exam_fee']; ?>"

step="0.01"

required>

</div>

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Library Fee

</label>

<input

type="number"

name="library_fee"

class="form-control"

value="<?php echo $row['library_fee']; ?>"

step="0.01"

required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Lab Fee

</label>

<input

type="number"

name="lab_fee"

class="form-control"

value="<?php echo $row['lab_fee']; ?>"

step="0.01"

required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Other Fee

</label>

<input

type="number"

name="other_fee"

class="form-control"

value="<?php echo $row['other_fee']; ?>"

step="0.01"

required>

</div>

</div>

<div class="mb-4">

<label class="form-label">

Status

</label>

<select

name="status"

class="form-select">

<option value="Active"

<?php if($row['status']=="Active") echo "selected"; ?>>

Active

</option>

<option value="Inactive"

<?php if($row['status']=="Inactive") echo "selected"; ?>>

Inactive

</option>

</select>

</div>

<div class="d-flex justify-content-between">

<a

href="manage_fee_structure.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="update"

class="btn btn-primary">

<i class="fas fa-save me-2"></i>

Update Fee Structure

</button>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script>

document.getElementById("faculty").addEventListener("change",function(){

let faculty=this.value;

let program=document.getElementById("program");

program.innerHTML="<option>Loading...</option>";

fetch("programs.php?faculty_id="+faculty)

.then(response=>response.text())

.then(data=>{

program.innerHTML=data;

});

});

</script>

</body>

</html>