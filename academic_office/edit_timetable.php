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

/*=========================================
CHECK ID
=========================================*/

if(!isset($_GET['id'])){

    header("Location: manage_timetable.php");
    exit;

}

$id = intval($_GET['id']);

/*=========================================
GET TIMETABLE
=========================================*/

$stmt = $conn->prepare("

SELECT *

FROM teacher_timetable

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    header("Location: manage_timetable.php");
    exit;

}

$timetable = $result->fetch_assoc();

/*=========================================
GET TEACHERS
=========================================*/

$teachers = $conn->query("

SELECT

id,
teacher_name,
employee_id

FROM teachers

WHERE status='Active'

ORDER BY teacher_name

");



/*=========================================
GET SUBJECTS
=========================================*/

$subjects = $conn->query("

SELECT

id,
subject_name,
subject_code

FROM subjects

WHERE status='Active'

ORDER BY subject_name

");

/*=========================================
GET PROGRAMS
=========================================*/

$programs = $conn->query("

SELECT

id,
program_name

FROM programs

ORDER BY program_name

");

$page_title="Edit Teacher Timetable";

include '../includes/header.php';

?>

<div class="container-fluid py-4">

<div class="row">

<div class="col-lg-10 mx-auto">

<div class="card shadow border-0">

<div class="card-header bg-warning">

<h3 class="mb-0">

<i class="fas fa-edit me-2"></i>

Edit Teacher Timetable

</h3>

</div>

<div class="card-body">

<form

method="POST"

action="update_timetable.php">

<input
type="hidden"
name="id"
value="<?php echo $timetable['id']; ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label fw-bold">

Teacher

</label>

<select
name="teacher_id"
class="form-select"
required>

<?php

while($teacher=$teachers->fetch_assoc()){

?>

<option
value="<?php echo $teacher['id']; ?>"

<?php

if($teacher['id']==$timetable['teacher_id']){

echo "selected";

}

?>>

<?php

echo $teacher['teacher_name'];

?>

(

<?php

echo $teacher['employee_id'];

?>

)

</option>

<?php

}

?>

</select>

</div>



<div class="col-md-6 mb-3">

<label class="form-label fw-bold">

Subject

</label>

<select
name="subject_id"
class="form-select"
required>

<?php

while($subject=$subjects->fetch_assoc()){

?>

<option
value="<?php echo $subject['id']; ?>"

<?php

if($subject['id']==$timetable['subject_id']){

echo "selected";

}

?>>

<?php

echo $subject['subject_code'];

?>

-

<?php

echo $subject['subject_name'];

?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Program

</label>

<select
name="program_id"
class="form-select"
required>

<?php
while($program=$programs->fetch_assoc()){
?>

<option
value="<?php echo $program['id']; ?>"

<?php
if($program['id']==$timetable['program_id']){
echo "selected";
}
?>>

<?php echo $program['program_name']; ?>

</option>

<?php
}
?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

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

<?php
if($i==$timetable['semester']){
echo "selected";
}
?>>

Semester <?php echo $i; ?>

</option>

<?php
}
?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Shift

</label>

<select

name="shift"

class="form-select"

required>

<option value="Morning"

<?php if($timetable['shift']=="Morning") echo "selected"; ?>>

Morning

</option>

<option value="Replica"

<?php if($timetable['shift']=="Replica") echo "selected"; ?>>

Replica

</option>

<option value="Evening"

<?php if($timetable['shift']=="Evening") echo "selected"; ?>>

Evening

</option>

</select>

</div>


<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Day

</label>

<select
name="day_name"
class="form-select"
required>

<?php

$days=[

"Monday",

"Tuesday",

"Wednesday",

"Thursday",

"Friday",

"Saturday"

];

foreach($days as $day){

?>

<option
value="<?php echo $day; ?>"

<?php

if($day==$timetable['day_name']){

echo "selected";

}

?>>

<?php echo $day; ?>

</option>

<?php

}

?>

</select>

</div>



<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Start Time

</label>

<input

type="time"

name="start_time"

class="form-control"

value="<?php echo $timetable['start_time']; ?>"

required>

</div>



<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

End Time

</label>

<input

type="time"

name="end_time"

class="form-control"

value="<?php echo $timetable['end_time']; ?>"

required>

</div>



<div class="col-md-6 mb-3">

<label class="form-label fw-bold">

Room Number

</label>

<input

type="text"

name="room_no"

class="form-control"

value="<?php echo htmlspecialchars($timetable['room_no']); ?>"

required>

</div>



<div class="col-md-6 mb-3">

<label class="form-label fw-bold">

Status

</label>

<select

name="status"

class="form-select">

<option
value="Active"

<?php if($timetable['status']=="Active") echo "selected"; ?>>

Active

</option>

<option
value="Inactive"

<?php if($timetable['status']=="Inactive") echo "selected"; ?>>

Inactive

</option>

</select>

</div>

<div class="col-12 mt-4">

<button

type="submit"

class="btn btn-success btn-lg">

<i class="fas fa-save me-2"></i>

Update Timetable

</button>

<a

href="manage_timetable.php"

class="btn btn-secondary btn-lg ms-2">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>