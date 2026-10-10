<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';



/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location: ../admins/login.php");
    exit;

}


if($_SESSION['department']!="Academic Office"){

    header("Location: ../admins/login.php");
    exit;

}


$page_title="Manage Teachers";


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



.page-title{

font-size:28px;
font-weight:700;
color:#0d6efd;

}



.card{

border:none;
border-radius:15px;
box-shadow:0 3px 12px rgba(0,0,0,.08);

}



.table th{

background:#0d6efd;
color:white;

}


.teacher-img{

width:60px;
height:60px;
border-radius:50%;
object-fit:cover;

}


</style>


</head>


<body>


<div class="container-fluid py-4">



<div class="row mb-4">


<div class="col-md-12">


<h2 class="page-title">


<i class="fa-solid fa-chalkboard-user me-2"></i>

Manage Teachers


</h2>



<p class="text-muted">

Academic Office Teacher Management

</p>


</div>


</div>





<!-- STAT CARDS -->


<div class="row mb-4">


<div class="col-md-4">


<div class="card p-3">


<h6>

Total Teachers

</h6>


<h2 id="total_teachers">

0

</h2>


</div>


</div>



<div class="col-md-4">


<div class="card p-3">


<h6>

Active Teachers

</h6>


<h2 id="active_teachers">

0

</h2>


</div>


</div>



<div class="col-md-4">


<div class="card p-3">


<h6>

Assigned Teachers

</h6>


<h2 id="assigned_teachers">

0

</h2>


</div>


</div>



</div>
<!-- =========================================
SEARCH TEACHER
========================================= -->


<div class="card mb-4">


<div class="card-header bg-primary text-white">


<i class="fa-solid fa-search me-2"></i>

Search Teachers


</div>



<div class="card-body">


<div class="row">


<div class="col-md-6">


<label class="fw-bold">

Search Teacher

</label>


<input 
type="text"
class="form-control"
id="search_teacher"
placeholder="Search by name or employee id">


</div>



</div>


</div>


</div>





<!-- =========================================
TEACHER TABLE
========================================= -->


<div class="card">



<div class="card-header bg-dark text-white">


<i class="fa-solid fa-users me-2"></i>

Teachers List


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
Photo
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
Programs
</th>


<th>
Subjects
</th>


<th>
Status
</th>


<th>
Action
</th>


</tr>


</thead>




<tbody id="teachers_table_body">


<tr>


<td colspan="10" class="text-center">


Loading Teachers...


</td>


</tr>


</tbody>



</table>


</div>



</div>


</div>





</div>





<!-- Bootstrap JS -->


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<!-- jQuery -->


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>



<script>


$(document).ready(function(){


    loadTeachers();


});



function loadTeachers(){


$.ajax({


url:"ajax/get_teachers_list.php",


type:"POST",


dataType:"json",



success:function(response){


console.log(response);



let html="";

let total=0;

let active=0;

let assigned=0;



if(response.status=="success"){



$.each(response.data,function(i,row){



total++;



if(row.status=="Active"){

active++;

}



if(row.total_subjects>0){

assigned++;

}





html+=`

<tr>


<td>

${total}

</td>



<td>


<img src="${row.photo}"

class="teacher-img">


</td>



<td>

${row.employee_id}

</td>



<td>

${row.teacher_name}

</td>



<td>

${row.faculty_name}

</td>



<td>

${row.designation}

</td>



<td>

${row.programs}

</td>



<td>

${row.subjects}

</td>



<td>

<span class="badge bg-success">

${row.status}

</span>


</td>



<td>


<button 

class="btn btn-primary btn-sm viewTeacher"

data-id="${row.id}">


<i class="fa fa-eye"></i>

View


</button>


</td>



</tr>


`;



});



}



$("#teachers_table_body").html(html);



$("#total_teachers").html(total);


$("#active_teachers").html(active);


$("#assigned_teachers").html(assigned);



},


error:function(xhr){


console.log(xhr.responseText);

alert("Teacher Load Error");


}



});



}

$(document).on("click",".viewTeacher",function(){


let teacher_id=$(this).data("id");



window.location.href="teacher_details.php?id="+teacher_id;



});

</script>


</body>


</html>