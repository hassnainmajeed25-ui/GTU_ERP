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

