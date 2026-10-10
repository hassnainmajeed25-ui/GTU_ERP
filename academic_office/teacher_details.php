<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';


if(!isset($_GET['id'])){

    header("Location: manage_teachers.php");
    exit;

}


$teacher_id=(int)$_GET['id'];



/* ==========================
   TEACHER INFORMATION
========================== */


$sql="

SELECT

t.*,

f.faculty_name

FROM teachers t

LEFT JOIN faculties f

ON t.faculty_id=f.id

WHERE t.id=$teacher_id

";


$result=mysqli_query($conn,$sql);


$teacher=mysqli_fetch_assoc($result);



if(!$teacher){

    echo "Teacher Not Found";
    exit;

}




/* ==========================
   ASSIGNED PROGRAMS
========================== */


$program_sql="

SELECT DISTINCT

p.program_name,

p.program_code


FROM teacher_subjects ts


INNER JOIN subjects s

ON ts.subject_id=s.id


INNER JOIN programs p

ON s.program_id=p.id


WHERE ts.teacher_id=$teacher_id


AND ts.status='Active'


ORDER BY p.program_name ASC


";


$program_result=mysqli_query($conn,$program_sql);


$programs=[];


while($row=mysqli_fetch_assoc($program_result)){


    $programs[]=$row;


}




/* ==========================
   ASSIGNED SUBJECTS
========================== */


$subject_sql="

SELECT

s.subject_code,

s.subject_name,

s.credit_hours,

s.semester


FROM teacher_subjects ts


INNER JOIN subjects s

ON ts.subject_id=s.id


WHERE ts.teacher_id=$teacher_id


AND ts.status='Active'


ORDER BY s.semester ASC


";


$subject_result=mysqli_query($conn,$subject_sql);


$subjects=[];


while($row=mysqli_fetch_assoc($subject_result)){


    $subjects[]=$row;


}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>

GTU Teacher Profile

</title>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">



<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{

background:#f4f6f9;

font-family:'Segoe UI',sans-serif;

}



.gtu-header{

background:white;

padding:20px;

border-radius:15px;

box-shadow:0 4px 15px rgba(0,0,0,0.08);

margin-bottom:25px;

}



.gtu-logo{

height:80px;

width:auto;

}



.page-title{

font-size:28px;

font-weight:700;

color:#0d6efd;

}



.card{

border:none;

border-radius:15px;

box-shadow:0 4px 15px rgba(0,0,0,0.08);

}



.profile-img{

width:150px;

height:150px;

border-radius:50%;

object-fit:cover;

border:5px solid #0d6efd;

}



.info-title{

font-size:18px;

font-weight:700;

color:#0d6efd;

border-bottom:2px solid #0d6efd;

padding-bottom:8px;

}



.label{

font-weight:700;

color:#555;

}



.value{

color:#222;

}



.badge-status{

font-size:14px;

padding:8px 15px;

}



.table th{

background:#0d6efd;

color:white;

}



</style>


</head>



<body>


<div class="container-fluid py-4">



<!-- GTU HEADER -->


<div class="gtu-header">


<div class="row align-items-center">


<div class="col-md-2 text-center">


<img src="../assets/images/logo.png"
class="gtu-logo">


</div>



<div class="col-md-10">


<h2 class="page-title">

<i class="fa-solid fa-user-tie"></i>

Teacher Profile

</h2>


<p class="text-muted mb-0">

Global Tech University - Academic Office

</p>


</div>


</div>


</div>





<!-- PROFILE CARD -->


<div class="card mb-4">


<div class="card-body">



<div class="row align-items-center">



<div class="col-md-3 text-center">



<?php

if($teacher['profile_photo']!=NULL && $teacher['profile_photo']!=""){

?>


<img src="../uploads/teachers/<?php echo $teacher['profile_photo']; ?>"
class="profile-img">


<?php

}else{

?>


<img src="../assets/images/default_teacher.png"
class="profile-img">


<?php

}

?>



<h4 class="mt-3">

<?php echo $teacher['teacher_name']; ?>

</h4>


<span class="badge bg-success badge-status">

<?php echo $teacher['status']; ?>

</span>



</div>





<div class="col-md-9">


<h5 class="info-title">

<i class="fa-solid fa-id-card"></i>

Personal Information

</h5>



<div class="row mt-3">



<div class="col-md-4 mb-3">

<span class="label">
Employee ID:
</span>

<br>

<span class="value">

<?php echo $teacher['employee_id']; ?>

</span>

</div>




<div class="col-md-4 mb-3">

<span class="label">
Father Name:
</span>

<br>

<span class="value">

<?php echo $teacher['father_name']; ?>

</span>

</div>




<div class="col-md-4 mb-3">

<span class="label">
Faculty:
</span>

<br>

<span class="value">

<?php echo $teacher['faculty_name']; ?>

</span>

</div>




<div class="col-md-4 mb-3">

<span class="label">
Designation:
</span>

<br>

<span class="value">

<?php echo $teacher['designation']; ?>

</span>

</div>



<div class="col-md-4 mb-3">

<span class="label">
Qualification:
</span>

<br>

<span class="value">

<?php echo $teacher['qualification']; ?>

</span>

</div>



<div class="col-md-4 mb-3">

<span class="label">
Joining Date:
</span>

<br>

<span class="value">

<?php echo $teacher['joining_date']; ?>

</span>

</div>



<div class="col-md-6 mb-3">

<span class="label">
Email:
</span>

<br>

<span class="value">

<?php echo $teacher['email']; ?>

</span>

</div>



<div class="col-md-6 mb-3">

<span class="label">
Phone:
</span>

<br>

<span class="value">

<?php echo $teacher['phone']; ?>

</span>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- ASSIGNED PROGRAMS -->

<div class="card mb-4">


<div class="card-header bg-primary text-white">


<h5 class="mb-0">

<i class="fa-solid fa-graduation-cap"></i>

Assigned Programs

</h5>


</div>



<div class="card-body">


<div class="table-responsive">


<table class="table table-bordered table-hover">


<thead>


<tr>

<th>
#
</th>


<th>
Program Code
</th>


<th>
Program Name
</th>


</tr>


</thead>



<tbody>


<?php


if(count($programs)>0){


$count=1;


foreach($programs as $program){


?>


<tr>


<td>

<?php echo $count++; ?>

</td>



<td>

<?php echo $program['program_code']; ?>

</td>



<td>

<?php echo $program['program_name']; ?>

</td>



</tr>


<?php


}


}else{


?>


<tr>

<td colspan="3" class="text-center text-danger">

No Program Assigned

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






<!-- ASSIGNED SUBJECTS -->


<div class="card mb-4">


<div class="card-header bg-dark text-white">


<h5 class="mb-0">

<i class="fa-solid fa-book-open"></i>

Assigned Subjects

</h5>


</div>




<div class="card-body">


<div class="table-responsive">


<table class="table table-bordered table-hover">


<thead>


<tr>


<th>
#
</th>


<th>
Semester
</th>


<th>
Subject Code
</th>


<th>
Subject Name
</th>


<th>
Credit Hours
</th>


</tr>


</thead>



<tbody>


<?php


if(count($subjects)>0){


$count=1;


foreach($subjects as $subject){


?>


<tr>


<td>

<?php echo $count++; ?>

</td>


<td>

Semester 
<?php echo $subject['semester']; ?>

</td>


<td>

<?php echo $subject['subject_code']; ?>

</td>


<td>

<?php echo $subject['subject_name']; ?>

</td>


<td>

<?php echo $subject['credit_hours']; ?>

CH

</td>



</tr>


<?php


}


}else{


?>


<tr>

<td colspan="5" class="text-center text-danger">

No Subject Assigned

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







<!-- BACK BUTTON -->


<div class="text-center mb-4">


<a href="manage_teachers.php" 
class="btn btn-secondary px-4">


<i class="fa-solid fa-arrow-left"></i>

Back To Teachers


</a>


</div>





</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>


</html>