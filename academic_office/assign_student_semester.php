<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location: ../admin/login.php");
    exit;

}

$page_title="Assign Student Semester";

include '../includes/header.php';

/*=========================================
GET PROGRAMS
=========================================*/

$programs=$conn->query("

SELECT

id,
program_name,
program_code

FROM programs

WHERE status='Active'

ORDER BY program_name ASC

");

?>

<div class="container-fluid py-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>

<i class="fas fa-user-graduate me-2"></i>

Assign Students To Semester

</h3>

</div>

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Program

</label>

<select

name="program_id"

class="form-select"

required>

<option value="">

Select Program

</option>

<?php

while($program=$programs->fetch_assoc()){

?>

<option

value="<?php echo $program['id']; ?>"

<?php

if(isset($_GET['program_id']) && $_GET['program_id']==$program['id']){

echo "selected";

}

?>

>

<?php

echo $program['program_name'];

?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">

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

<option

value="<?php echo $i; ?>"

<?php

if(isset($_GET['semester']) && $_GET['semester']==$i){

echo "selected";

}

?>

>

Semester <?php echo $i; ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">

Shift

</label>

<select

name="shift"

class="form-select"

required>

<option value="">

Select Shift

</option>

<option value="Morning">Morning</option>

<option value="Replica">Replica</option>

<option value="Evening">Evening</option>

</select>

</div>

<div class="col-md-2 d-grid">

<label>&nbsp;</label>

<button

type="submit"

class="btn btn-primary">

Load Students

</button>

</div>

</div>

</form>

<hr>

<?php

/*=========================================
LOAD STUDENTS
=========================================*/

if(

isset($_GET['program_id'])

&&

isset($_GET['semester'])

&&

isset($_GET['shift'])

){

$program_id = intval($_GET['program_id']);

$semester = intval($_GET['semester']);

$shift = $_GET['shift'];

$stmt = $conn->prepare("

SELECT

students.id,

students.first_name,

students.last_name,

applications.registration_no,

applications.roll_no,

programs.program_name

FROM students

INNER JOIN applications

ON students.id=applications.student_id

INNER JOIN programs

ON applications.program_id=programs.id

WHERE

applications.program_id=?

AND

applications.application_status='Selected'

ORDER BY

students.first_name ASC

");

$stmt->bind_param(

"i",

$program_id

);

$stmt->execute();

$list = $stmt->get_result();

?>

<form

method="POST"

action="save_student_semester.php">

<input

type="hidden"

name="program_id"

value="<?php echo $program_id; ?>">

<input

type="hidden"

name="semester"

value="<?php echo $semester; ?>">

<input

type="hidden"

name="shift"

value="<?php echo $shift; ?>">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th width="60">

<input

type="checkbox"

id="checkAll">

</th>

<th>

Student

</th>

<th>

Program

</th>

</tr>

</thead>

<tbody>

<?php

$sr=1;

while($row=$list->fetch_assoc()){

?>

<tr>

<td>

<input

type="checkbox"

name="students[]"

value="<?php echo $row['id']; ?>"

class="studentCheck">

</td>

<td>

<strong>

<?php

echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);

?>

</strong>

<br>

<small class="text-primary">

Roll No :

<?php

echo htmlspecialchars($row['roll_no']);

?>

</small>

<br>

<small class="text-muted">

Registration :

<?php

echo htmlspecialchars($row['registration_no']);

?>

</small>

</td>

<td>

<?php

echo htmlspecialchars(

$row['program_name']

);

?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<div class="text-end mt-3">

<button

type="submit"

class="btn btn-success btn-lg">

<i class="fas fa-save me-2"></i>

Assign Semester

</button>

</div>

</form>

<script>

document.getElementById('checkAll').onclick=function(){

let c=document.querySelectorAll('.studentCheck');

for(let i=0;i<c.length;i++){

c[i].checked=this.checked;

}

};

</script>

<?php

}

?>