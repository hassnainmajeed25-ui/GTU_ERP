<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

/*=========================================
DEFAULT VARIABLES
=========================================*/

$faculty_id = "";
$program_id = "";
$semester   = "";

/*=========================================
LOAD FACULTIES
=========================================*/

$facultyQuery = mysqli_query($conn,"
SELECT *
FROM faculties
WHERE status='Active'
ORDER BY faculty_name ASC
");
?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Publish Semester Results

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{
background:#EEF4FF;
font-family:'Segoe UI',sans-serif;
}

.main-card{

max-width:1300px;
margin:30px auto;
background:#fff;
border-radius:18px;
overflow:hidden;
box-shadow:0 10px 25px rgba(0,0,0,.18);

}

.header{

background:linear-gradient(135deg,#003366,#0066CC,#F4D03F);
padding:25px;
color:#fff;

}

.section-title{

background:#003366;
color:white;
padding:12px;
border-radius:10px;
font-weight:bold;
margin-bottom:20px;

}

.form-control,
.form-select{

height:45px;

}

.btn-search{

background:#0066CC;
color:white;
font-weight:bold;

}

.btn-search:hover{

background:#004999;
color:white;

}

</style>

</head>

<body>

<div class="container">

<div class="main-card">

<div class="header">

<h2>

<i class="fas fa-upload me-2"></i>

Publish Semester Results

</h2>

<p class="mb-0">

Examination Office Result Publication Panel

</p>

</div>

<div class="p-4">

<div class="section-title">

Search Result

</div>

<form
method="GET"
action="publish_results.php">

<div class="row">

<div class="col-md-4 mb-3">

<label class="fw-bold">

Faculty

</label>

<select
id="faculty"
name="faculty_id"
class="form-select"
required>

<option value="">

Select Faculty

</option>

<?php

mysqli_data_seek($facultyQuery,0);

while($faculty=mysqli_fetch_assoc($facultyQuery)){

?>

<option
value="<?php echo $faculty['id']; ?>">

<?php echo $faculty['faculty_name']; ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Program

</label>

<select
id="program"
name="program_id"
class="form-select"
required>

<option value="">

Select Program

</option>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Semester

</label>

<select
name="semester"
class="form-select"
required>

<option value="">

Select Semester

</option>

<?php

for($i=1;$i<=8;$i++){

?>

<option value="<?php echo $i; ?>">

Semester <?php echo $i; ?>

</option>

<?php

}

?>

</select>

</div>

</div>

<div class="text-end mt-3">

<button
type="submit"
name="search"
class="btn btn-search btn-lg">

<i class="fas fa-search me-2"></i>

Load Students

</button>

</div>

</form>

<hr>

<?php

if(isset($_GET['search'])){

$program_id=(int)$_GET['program_id'];
$semester=(int)$_GET['semester'];

$stmt=$conn->prepare("

SELECT

students.id,
students.roll_no,
students.registration_no,
students.first_name,
students.last_name,

results.percentage,
results.cgpa,
results.result_status

FROM results

INNER JOIN students
ON students.id=results.student_id

WHERE

students.program_id=?
AND students.semester=?

ORDER BY students.roll_no ASC

");

$stmt->bind_param(
"ii",
$program_id,
$semester
);

$stmt->execute();

$result=$stmt->get_result();

?>

<div class="section-title">

Students Ready For Publishing

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>#</th>
<th>Roll No</th>
<th>Registration No</th>
<th>Student Name</th>
<th>Percentage</th>
<th>CGPA</th>
<th>Result</th>

</tr>

</thead>

<tbody>

<?php

$sr=1;

while($row=$result->fetch_assoc()){

?>

<tr>

<td><?php echo $sr++; ?></td>

<td><?php echo $row['roll_no']; ?></td>

<td><?php echo $row['registration_no']; ?></td>

<td><?php echo $row['first_name']." ".$row['last_name']; ?></td>

<td><?php echo number_format($row['percentage'],2); ?>%</td>

<td><?php echo number_format($row['cgpa'],2); ?></td>

<td>

<?php

if($row['result_status']=="Pass"){

echo "<span class='badge bg-success'>Pass</span>";

}else{

echo "<span class='badge bg-danger'>Fail</span>";

}

?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<div class="text-end mt-4">

<form
method="POST"
action="publish_all_results.php">

<input
type="hidden"
name="program_id"
value="<?php echo $program_id; ?>">

<input
type="hidden"
name="semester"
value="<?php echo $semester; ?>">

<button
type="submit"
class="btn btn-success btn-lg">

<i class="fas fa-upload me-2"></i>

Publish Complete Semester Result

</button>

</form>

</div>

<?php

}

?>
