<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit;
}

$student_id=$_SESSION['student_id'];

$app=$conn->prepare("SELECT * FROM applications WHERE student_id=? LIMIT 1");
$app->bind_param("i",$student_id);
$app->execute();

$result=$app->get_result();

if($result->num_rows==0){
    header("Location: personal.php");
    exit;
}

$application=$result->fetch_assoc();

$app_id=$application['id'];

$faculties=$conn->query("SELECT * FROM faculties WHERE status='Active' ORDER BY faculty_name ASC");


$sessions=$conn->query("
SELECT *
FROM admission_sessions
WHERE status='Active'
ORDER BY session_name ASC
");
if(isset($_POST['save_continue'])){

$faculty_id=(int)$_POST['faculty_id'];
$program_id=(int)$_POST['program_id'];
$session_id=(int)$_POST['session_id'];
$shift=$_POST['shift'];

$update=$conn->prepare("
UPDATE applications
SET
faculty_id=?,
program_id=?,
session_id=?,
shift=?
WHERE id=?
");

$update->bind_param(
"iiisi",
$faculty_id,
$program_id,
$session_id,
$shift,
$app_id
);

    $update->execute();

    header("Location: documents.php");
    exit;

}
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>GTU Admission | Program Selection</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
background:#f4f7fb;
}

.topbar{
background:#0B1F52;
padding:18px;
color:#fff;
}

.progress{
height:10px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.card-header{
background:#0B1F52;
color:#fff;
font-size:22px;
font-weight:bold;
}

.form-select{
height:52px;
border-radius:10px;
}

.info-box{
background:#eef6ff;
border-left:5px solid #0B1F52;
padding:15px;
margin-bottom:25px;
border-radius:10px;
}

.btn-next{
background:#FDB913;
border:none;
height:55px;
border-radius:50px;
font-weight:bold;
font-size:18px;
}

.btn-next:hover{
background:#e3a700;
}

</style>

</head>

<body>

<div class="topbar">

<div class="container">

<h3>Global Tech University (GTU)</h3>

</div>

</div>

<div class="container mt-4">

<div class="progress mb-4">

<div class="progress-bar bg-success" style="width:60%"></div>

</div>

<div class="card">

<div class="card-header">

<i class="fas fa-university me-2"></i>

Program Selection

</div>

<div class="card-body">

<div class="info-box">

<h5>Select Your Faculty & Program</h5>

</div>

<form method="POST">

<div class="mb-4">

<label class="form-label">

Faculty

</label>

<select

name="faculty_id"

id="faculty"

class="form-select"

required>

<option value="">

Select Faculty

</option>

<?php

while($faculty=$faculties->fetch_assoc()){

?>

<option value="<?php echo $faculty['id']; ?>">

<?php echo $faculty['faculty_name']; ?>

</option>

<?php

}

?>

</select>

</div>

<div class="mb-4">

<label class="form-label">

Admission Session

</label>

<select
name="session_id"
class="form-select"
required>

<option value="">

Select Session

</option>

<?php while($session=$sessions->fetch_assoc()){ ?>

<option value="<?php echo $session['id']; ?>">

<?php echo $session['session_name']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-4">

<label class="form-label">

Shift

</label>

<select
name="shift"
class="form-select"
required>

<option value="Morning">

Morning

</option>

<option value="Evening">

Evening

</option>

<option value="Replica">

Replica

</option>

</select>

</div>

<select

name="program_id"

id="program"

class="form-select"

required>

<option value="">

First Select Faculty

</option>

</select>

</div>

<div class="d-flex justify-content-between">

<a href="academic.php"

class="btn btn-secondary px-4">

<i class="fas fa-arrow-left me-2"></i>

Previous

</a>

<button

type="submit"

name="save_continue"

class="btn btn-next px-5">

Save & Continue

<i class="fas fa-arrow-right ms-2"></i>

</button>

</div>

</form>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>