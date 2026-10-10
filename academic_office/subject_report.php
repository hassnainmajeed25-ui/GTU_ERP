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



$page_title="Subject Report";





$sql="

SELECT


s.id,

s.subject_code,

s.subject_name,

s.credit_hours,

s.semester,

s.status,



p.program_code,

p.program_name,



f.faculty_name,



t.teacher_name,

t.employee_id



FROM subjects s



LEFT JOIN programs p

ON s.program_id=p.id



LEFT JOIN faculties f

ON s.faculty_id=f.id



LEFT JOIN teachers t

ON s.teacher_id=t.id



ORDER BY 

p.program_name ASC,

s.semester ASC,

s.subject_code ASC



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

<i class="fa-solid fa-book-open"></i>

Subject Report

</h2>


<p class="text-muted mb-0">

Global Tech University - Academic Office

</p>


</div>


</div>


</div>







<!-- SUBJECT REPORT TABLE -->


<div class="card">



<div class="card-header bg-dark text-white">


<i class="fa-solid fa-list"></i>

All Subjects Details


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
Subject Code
</th>


<th>
Subject Name
</th>


<th>
Program
</th>


<th>
Faculty
</th>


<th>
Semester
</th>


<th>
Credit Hours
</th>


<th>
Teacher
</th>


<th>
Employee ID
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

<?php echo $row['subject_code']; ?>

</td>




<td>

<?php echo $row['subject_name']; ?>

</td>




<td>

<?php echo $row['program_code']." - ".$row['program_name']; ?>

</td>




<td>

<?php echo $row['faculty_name']; ?>

</td>




<td>

Semester <?php echo $row['semester']; ?>

</td>




<td>


<span class="badge bg-primary">

<?php echo $row['credit_hours']; ?>

CH

</span>


</td>




<td>

<?php


if($row['teacher_name']!=""){


echo $row['teacher_name'];


}else{


echo "Not Assigned";


}



?>

</td>




<td>


<?php


if($row['employee_id']!=""){


echo $row['employee_id'];


}else{


echo "-";


}



?>

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

<td colspan="10" class="text-center text-danger">

No Subject Found

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

Print Subject Report


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