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

$page_title="Manage Student Semester";

include '../includes/header.php';

/*=========================================
GET ASSIGNED STUDENTS
=========================================*/

$sql = $conn->query("

SELECT

student_semesters.id,

student_semesters.semester,

student_semesters.shift,

student_semesters.status,

students.first_name,

students.last_name,

applications.roll_no,

applications.registration_no,

programs.program_name

FROM student_semesters

INNER JOIN students

ON student_semesters.student_id = students.id

INNER JOIN programs

ON student_semesters.program_id = programs.id

LEFT JOIN applications

ON applications.student_id = students.id

ORDER BY

programs.program_name,

student_semesters.semester,

students.first_name

");

?>

<div class="container-fluid py-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<div class="d-flex justify-content-between align-items-center">

<h3>

<i class="fas fa-user-graduate me-2"></i>

Manage Student Semester

</h3>

<a

href="assign_student_semester.php"

class="btn btn-light">

<i class="fas fa-plus me-2"></i>

Assign New

</a>

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Student</th>

<th>Program</th>

<th>Semester</th>

<th>Shift</th>

<th>Status</th>

<th width="150">

Action

</th>

</tr>

</thead>

<tbody>

<?php

$sr=1;

while($row=$sql->fetch_assoc()){

?>

<tr>

<td>

<?php echo $sr++; ?>

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

echo htmlspecialchars($row['program_name']);

?>

</td>

<td>

<span class="badge bg-info">

Semester

<?php

echo $row['semester'];

?>

</span>

</td>

<td>

<?php

if($row['shift']=="Morning"){

    echo "<span class='badge bg-success'>Morning</span>";

}
elseif($row['shift']=="Replica"){

    echo "<span class='badge bg-warning text-dark'>Replica</span>";

}
else{

    echo "<span class='badge bg-primary'>Evening</span>";

}

?>

</td>

<td>

<?php

if($row['status']=="Active"){

    echo "<span class='badge bg-success'>Active</span>";

}else{

    echo "<span class='badge bg-danger'>Inactive</span>";

}

?>

</td>

<td>

<a

href="edit_student_semester.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm">

<i class="fas fa-edit"></i>

</a>

<a

href="delete_student_semester.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this record?');">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<script>

document.addEventListener("DOMContentLoaded",function(){

let table=document.querySelector("table");

let searchBox=document.createElement("input");

searchBox.type="text";

searchBox.className="form-control mb-3";

searchBox.placeholder="Search Student, Roll No, Registration No...";

table.parentNode.insertBefore(searchBox,table);

searchBox.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=table.querySelectorAll("tbody tr");

rows.forEach(function(row){

if(row.innerText.toLowerCase().indexOf(value)>-1){

row.style.display="";

}else{

row.style.display="none";

}

});

});

});

</script>

<?php

include '../includes/footer.php';

?>