<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';



if(!isset($_SESSION['admin_id'])){

    header("Location: ../admins/login.php");
    exit;

}


if($_SESSION['department']!="Academic Office"){

    header("Location: ../admins/login.php");
    exit;

}



$page_title="Teacher Assignment Report";




$sql="

SELECT


t.id,

t.employee_id,

t.teacher_name,

t.designation,

t.qualification,

t.status,


f.faculty_name,



COUNT(DISTINCT ts.subject_id) AS total_subjects,



GROUP_CONCAT(

DISTINCT p.program_code

ORDER BY p.program_code

SEPARATOR ', '

) AS programs



FROM teachers t



LEFT JOIN faculties f

ON t.faculty_id=f.id



LEFT JOIN teacher_subjects ts

ON t.id=ts.teacher_id



LEFT JOIN subjects s

ON ts.subject_id=s.id



LEFT JOIN programs p

ON s.program_id=p.id



GROUP BY t.id



ORDER BY t.teacher_name ASC



";



$result=mysqli_query($conn,$sql);



?>



<!DOCTYPE html>

<html lang="en">


<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">



<title>

<?php echo $page_title; ?>

</title>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">



<style>


body{

background:#f4f6f9;

font-family:'Segoe UI',sans-serif;

}



.header-card{

background:white;

padding:20px;

border-radius:15px;

box-shadow:0 4px 15px rgba(0,0,0,.08);

}



.logo{

height:70px;

}



.title{

font-size:28px;

font-weight:700;

color:#0d6efd;

}



.card{

border:none;

border-radius:15px;

box-shadow:0 4px 15px rgba(0,0,0,.08);

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


<div class="header-card mb-4">


<div class="row align-items-center">


<div class="col-md-2 text-center">


<img src="../assets/images/logo.png"
class="logo">


</div>



<div class="col-md-10">


<h2 class="title">

<i class="fa-solid fa-user-tie"></i>

Teacher Assignment Report

</h2>


<p class="text-muted mb-0">

Global Tech University - Academic Office

</p>


</div>


</div>


</div>






<!-- REPORT TABLE -->


<div class="card">



<div class="card-header bg-dark text-white">


<i class="fa-solid fa-list"></i>

All Teachers Assignment Details


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
Employee ID
</th>


<th>
Teacher Name
</th>


<th>
Faculty
</th>


<th>
Designation
</th>


<th>
Qualification
</th>


<th>
Assigned Programs
</th>


<th>
Total Subjects
</th>


<th>
Status
</th>


</tr>


</thead>




<tbody>


<?php


$count=1;



if(mysqli_num_rows($result)>0){



while($row=mysqli_fetch_assoc($result)){



?>


<tr>



<td>

<?php echo $count++; ?>

</td>




<td>

<?php echo $row['employee_id']; ?>

</td>




<td>

<?php echo $row['teacher_name']; ?>

</td>




<td>

<?php echo $row['faculty_name']; ?>

</td>




<td>

<?php echo $row['designation']; ?>

</td>




<td>

<?php echo $row['qualification']; ?>

</td>




<td>

<?php


if($row['programs']!=NULL){

echo $row['programs'];

}else{


echo "No Program Assigned";


}


?>

</td>




<td>


<span class="badge bg-primary">

<?php echo $row['total_subjects']; ?>

</span>


</td>




<td>



<?php

if($row['status']=="Active"){


?>

<span class="badge bg-success">

Active

</span>


<?php


}else{


?>

<span class="badge bg-danger">

Inactive

</span>


<?php


}


?>


</td>




</tr>



<?php


}



}else{


?>


<tr>

<td colspan="9" class="text-center text-danger">

No Teacher Found

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

<!-- ACTION BUTTONS -->


<div class="card mt-4">


<div class="card-body text-center">



<button 
onclick="window.print()"
class="btn btn-dark me-2">


<i class="fa-solid fa-print"></i>

Print Teacher Report


</button>





<a href="reports.php"
class="btn btn-secondary">


<i class="fa-solid fa-arrow-left"></i>

Back To Reports


</a>



</div>


</div>





</div>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>


</html>