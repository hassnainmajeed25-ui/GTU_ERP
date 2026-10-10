<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


if(!isset($_GET['id'])){

    header("Location: manage_programs.php");
    exit;

}


$program_id=(int)$_GET['id'];



$sql="
SELECT

p.*,

f.faculty_name

FROM programs p

INNER JOIN faculties f

ON p.faculty_id=f.id

WHERE p.id=$program_id

";


$result=mysqli_query($conn,$sql);


if(!$result){

    die("Program Query Error: ".mysqli_error($conn));

}



$program=mysqli_fetch_assoc($result);



if(!$program){

    echo "Program Not Found";

    exit;

}




// =====================================
// LOAD PROGRAM TEACHERS
// =====================================


$teacher_sql="

SELECT DISTINCT

t.employee_id,

t.teacher_name,

t.designation


FROM teachers t


INNER JOIN teacher_subjects ts

ON t.id=ts.teacher_id


INNER JOIN subjects s

ON ts.subject_id=s.id


WHERE s.program_id=$program_id


AND ts.status='Active'


ORDER BY t.teacher_name ASC

";



$teacher_result=mysqli_query($conn,$teacher_sql);



$teachers=[];



if($teacher_result){


    while($row=mysqli_fetch_assoc($teacher_result)){


        $teachers[]=$row;


    }


}


?>


<!DOCTYPE html>

<html>

<head>

<title>

Program Details

</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


</head>


<body>


<div class="container py-4">


<h2 class="text-primary">

Program Details

</h2>



<div class="card mt-4">

<div class="card-body">


<h4>

<?php echo $program['program_name']; ?>

</h4>


<hr>


<p>

<b>Program Code:</b>

<?php echo $program['program_code']; ?>

</p>



<p>

<b>Faculty:</b>

<?php echo $program['faculty_name']; ?>

</p>



<p>

<b>Duration:</b>

<?php echo $program['duration']; ?>

</p>



<p>

<b>Status:</b>

<?php echo $program['status']; ?>

</p>

<hr>

<h4 class="text-primary">

Assigned Teachers

</h4>


<table class="table table-bordered mt-3">


<thead class="table-dark">

<tr>

<th>
#
</th>

<th>
Employee ID
</th>

<th>
Teacher Name
</th>

<th>
Designation
</th>

</tr>

</thead>


<tbody>


<?php

if(count($teachers)>0){


$count=1;


foreach($teachers as $teacher){


?>


<tr>


<td>

<?php echo $count++; ?>

</td>


<td>

<?php echo $teacher['employee_id']; ?>

</td>


<td>

<?php echo $teacher['teacher_name']; ?>

</td>


<td>

<?php echo $teacher['designation']; ?>

</td>


</tr>


<?php


}


}else{


?>


<tr>

<td colspan="4" class="text-center text-danger">

No Teacher Assigned

</td>

</tr>


<?php


}


?>


</tbody>


</table>

<a href="manage_programs.php" class="btn btn-secondary">

Back

</a>


</div>

</div>


</div>


</body>

</html>