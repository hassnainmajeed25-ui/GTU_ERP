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


$page_title="Academic Programs";


?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
<?php echo $page_title; ?>
</title>


<!-- Bootstrap -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<!-- Font Awesome -->

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



.stat-card{

padding:20px;

}



.stat-icon{

font-size:35px;

}



.table th{

background:#0d6efd;
color:white;

}


.btn-view{

background:#0d6efd;
color:white;

}


</style>


</head>


<body>


<div class="container-fluid py-4">



<!-- HEADER -->

<div class="row mb-4">


<div class="col-md-12">


<h2 class="page-title">

<i class="fa-solid fa-graduation-cap me-2"></i>

Academic Programs

</h2>


<p class="text-muted">

Academic Office Program Management

</p>


</div>


</div>





<!-- DASHBOARD CARDS -->


<div class="row mb-4">


<div class="col-md-3">


<div class="card stat-card">


<div class="d-flex justify-content-between">


<div>


<h6>
Total Programs
</h6>


<h3 id="total_programs">
0
</h3>


</div>


<div class="stat-icon text-primary">

<i class="fa-solid fa-book"></i>

</div>


</div>


</div>


</div>





<div class="col-md-3">


<div class="card stat-card">


<div class="d-flex justify-content-between">


<div>


<h6>
Active Programs
</h6>


<h3 id="active_programs">
0
</h3>


</div>


<div class="stat-icon text-success">

<i class="fa-solid fa-circle-check"></i>

</div>


</div>


</div>


</div>





<div class="col-md-3">


<div class="card stat-card">


<div class="d-flex justify-content-between">


<div>


<h6>
Total Subjects
</h6>


<h3 id="total_subjects">
0
</h3>


</div>


<div class="stat-icon text-warning">

<i class="fa-solid fa-book-open"></i>

</div>


</div>


</div>


</div>





<div class="col-md-3">


<div class="card stat-card">


<div class="d-flex justify-content-between">


<div>


<h6>
Total Students
</h6>


<h3 id="total_students">
0
</h3>


</div>


<div class="stat-icon text-danger">

<i class="fa-solid fa-users"></i>

</div>


</div>


</div>


</div>



</div>





<!-- FILTER CARD -->


<div class="card mb-4">


<div class="card-header bg-primary text-white">


<i class="fa-solid fa-filter me-2"></i>

Search Programs


</div>



<div class="card-body">


<div class="row">


<div class="col-md-6">


<label class="fw-bold">

Search Program

</label>


<input 
type="text"
class="form-control"
id="search_program"
placeholder="Search by name or code">


</div>



<div class="col-md-6">


<label class="fw-bold">

Faculty

</label>


<select 
class="form-select"
id="faculty_filter">


<option value="">

All Faculties

</option>


</select>


</div>



</div>


</div>


</div>






<!-- PROGRAM TABLE -->


<div class="card">


<div class="card-header bg-dark text-white">


<i class="fa-solid fa-list me-2"></i>

Programs List


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


<th>
Faculty
</th>


<th>
Duration
</th>


<th>
Subjects
</th>


<th>
Teachers
</th>


<th>
Students
</th>


<th>
Status
</th>


<th>
Action
</th>


</tr>


</thead>



<tbody id="programs_table_body">


<tr>


<td colspan="10" class="text-center">


Loading Programs...


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


    loadPrograms();
    loadFaculties();


});



function loadPrograms(){


    $.ajax({


        url:"ajax/get_programs_list.php",

        type:"POST",

        dataType:"json",


        success:function(response){


            console.log(response);


            let html="";

            let total=0;

            let active=0;



            if(response.status=="success"){


                $.each(response.data,function(index,row){


                    total++;


                    if(row.status=="Active"){

                        active++;

                    }



                    html += `

                    <tr>

                        <td>${total}</td>

                        <td>
                            ${row.program_code ?? '-'}
                        </td>


                        <td>
                            ${row.program_name}
                        </td>


                        <td>
                            ${row.faculty_name}
                        </td>


                        <td>
                            ${row.duration ?? '-'}
                        </td>


                        <td>
                            ${row.total_subjects}
                        </td>


                        <td>
                            ${row.total_teachers}
                        </td>


                        <td>
                            0
                        </td>


                        <td>

                            <span class="badge bg-success">

                                ${row.status}

                            </span>

                        </td>


                        <td>

                            <button 
class="btn btn-primary btn-sm viewProgram"
data-id="${row.id}">

<i class="fa fa-eye"></i>

View

</button>

                        </td>


                    </tr>

                    `;


                });


            }



            $("#programs_table_body").html(html);


            $("#total_programs").html(total);


            $("#active_programs").html(active);



        },


        error:function(xhr){


            console.log(xhr.responseText);


            alert("Programs Load Error");


        }


    });


}

function loadFaculties(){


$.ajax({


url:"ajax/get_faculties_list.php",

type:"POST",

dataType:"json",


success:function(response){


if(response.status=="success"){


$("#faculty_filter").html(
'<option value="">All Faculties</option>'
);



$.each(response.data,function(i,row){


$("#faculty_filter").append(

'<option value="'+row.id+'">'+
row.faculty_name+
'</option>'

);


});


}


}


});


}


$(document).on("click",".viewProgram",function(){

    let program_id=$(this).data("id");

    window.location.href=
    "program_details.php?id="+program_id;

});



</script>



</body>


</html>