<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if (!isset($_SESSION['admin_id'])) {

    header("Location: ../admins/login.php");
    exit;

}

if ($_SESSION['department'] != "Academic Office") {

    header("Location: ../admins/login.php");
    exit;

}

$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

/*=========================================
LOAD ACTIVE FACULTIES
=========================================*/

$faculties = [];

$sql = "

SELECT

id,
faculty_name

FROM faculties

WHERE status='Active'

ORDER BY faculty_name ASC

";

$result = mysqli_query($conn,$sql);

if($result){

    while($row=mysqli_fetch_assoc($result)){

        $faculties[]=$row;

    }

}

$page_title="Teacher Timetable Management";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

<?php echo $page_title; ?>

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{

background:#f4f6f9;
font-family:'Segoe UI',sans-serif;

}

.card{

border:none;
border-radius:12px;
box-shadow:0 3px 10px rgba(0,0,0,.08);

}

.card-header{

font-size:18px;
font-weight:600;

}

.page-title{

font-size:28px;
font-weight:700;
color:#0d6efd;

}

.form-label{

font-weight:600;

}

.required{

color:red;

}

.table th{

background:#0d6efd;
color:#fff;

}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="row mb-4">

<div class="col-md-12">

<h2 class="page-title">

<i class="fas fa-calendar-alt me-2"></i>

Teacher Timetable Management

</h2>

<p class="text-muted">

Academic Office Panel

</p>

<div class="alert alert-info mt-3 mb-4">

<strong>Instructions:</strong>

<ol class="mb-0 mt-2">

<li>Select the <strong>Faculty</strong>.</li>

<li>Select the <strong>Program</strong>.</li>

<li>Select the <strong>Semester</strong> to load the available subjects.</li>

<li>Select the <strong>Subject</strong>.</li>

<li>Select the <strong>Shift</strong>.</li>

<li>Select the <strong>Teacher</strong>.</li>

<li>Select the <strong>Day</strong>, <strong>Start Time</strong>, <strong>End Time</strong>, and enter the <strong>Room Number</strong>.</li>

<li>Click the <strong>Save Timetable</strong> button to assign the timetable.</li>

</ol>

</div>

</div>

</div>

<div class="card mb-4">

<div class="card-header bg-primary text-white">

<i class="fas fa-calendar-plus me-2"></i>

Create Teacher Timetable

</div>

<div class="card-body">

<div class="row">

<!-- Faculty -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Faculty

<span class="required">*</span>

</label>

<select

class="form-select"

id="faculty_id"

name="faculty_id">

<option value="">

Select Faculty

</option>

<?php foreach($faculties as $faculty){ ?>

<option value="<?php echo $faculty['id']; ?>">

<?php echo $faculty['faculty_name']; ?>

</option>

<?php } ?>

</select>

</div>

<!-- Program -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Program

<span class="required">*</span>

</label>

<select
class="form-select"
id="program_id"
name="program_id">

<option value="">

Select Program

</option>

</select>

</div>

<!-- Subject -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Subject

<span class="required">*</span>

</label>

<select
class="form-select"
id="subject_id"
name="subject_id">

<option value="">

Select Subject

</option>

</select>

</div>

<!-- Semester -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Semester

<span class="required">*</span>

</label>

<select
class="form-select"
id="semester"
name="semester">

<option value="">

Select Semester

</option>

<?php for($i=1;$i<=8;$i++){ ?>

<option value="<?php echo $i; ?>">

Semester <?php echo $i; ?>

</option>

<?php } ?>

</select>

</div>

<!-- Shift -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Shift

<span class="required">*</span>

</label>

<select
class="form-select"
id="shift"
name="shift">

<option value="">

Select Shift

</option>

<option value="Morning">

Morning

</option>

<option value="Replica">

Replica

</option>

<option value="Evening">

Evening

</option>

</select>

</div>

<!-- Teacher -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Teacher

<span class="required">*</span>

</label>

<select
class="form-select"
id="teacher_id"
name="teacher_id">

<option value="">

Select Teacher

</option>

</select>

</div>

<!-- Day -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Day

<span class="required">*</span>

</label>

<select
class="form-select"
id="day_name"
name="day_name">

<option value="">

Select Day

</option>

<option value="Monday">

Monday

</option>

<option value="Tuesday">

Tuesday

</option>

<option value="Wednesday">

Wednesday

</option>

<option value="Thursday">

Thursday

</option>

<option value="Friday">

Friday

</option>

<option value="Saturday">

Saturday

</option>

</select>

</div>

<!-- Start Time -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Start Time

<span class="required">*</span>

</label>

<input
type="time"
class="form-control"
id="start_time"
name="start_time">

</div>

<!-- End Time -->

<div class="col-lg-4 mb-3">

<label class="form-label">

End Time

<span class="required">*</span>

</label>

<input
type="time"
class="form-control"
id="end_time"
name="end_time">

</div>

<!-- Room No -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Room No

<span class="required">*</span>

</label>

<input
type="text"
class="form-control"
id="room_no"
name="room_no"
placeholder="Room 101">

</div>

<!-- Status -->

<div class="col-lg-4 mb-3">

<label class="form-label">

Status

</label>

<select
class="form-select"
id="status"
name="status">

<option value="Active">

Active

</option>

<option value="Inactive">

Inactive

</option>

</select>

</div>

<!-- Save Button -->

<div class="col-lg-4 d-flex align-items-end mb-3">

<button
type="button"
class="btn btn-success w-100"
id="save_timetable">

<i class="fas fa-save me-2"></i>

Save Timetable

</button>

</div>

</div>

</div>

</div>

<!-- =========================================
TIMETABLE LIST
========================================= -->

<div class="card">

<div class="card-header bg-dark text-white">

<div class="d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="fas fa-table me-2"></i>

Teacher Timetable List

</h5>

<input
type="text"
id="search"
class="form-control"
placeholder="Search Teacher..."
style="width:250px;">

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>#</th>

<th>Teacher</th>

<th>Subject</th>

<th>Program</th>

<th>Semester</th>

<th>Shift</th>

<th>Day</th>

<th>Time</th>

<th>Room</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody id="timetable_body">

<tr>

<td colspan="11" class="text-center text-muted">

No Timetable Found

</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

/*=========================================
LOAD PROGRAMS
=========================================*/

$("#faculty_id").change(function(){

    let faculty_id = $("#faculty_id").val();

    $("#program_id").html(
        '<option value="">Loading Programs...</option>'
    );

    $("#subject_id").html(
        '<option value="">Select Subject</option>'
    );

    $("#teacher_id").html(
        '<option value="">Select Teacher</option>'
    );

    if(faculty_id==""){

        $("#program_id").html(
            '<option value="">Select Program</option>'
        );

        return;

    }

    $.ajax({

        url:"ajax/get_programs.php",

        type:"POST",

        data:{
            faculty_id:faculty_id
        },

        dataType:"json",

        success:function(response){

            console.log(response);

            $("#program_id").html(
                '<option value="">Select Program</option>'
            );

            if(response.status=="success"){

                $.each(response.data,function(i,row){

                    $("#program_id").append(

                        '<option value="'+row.id+'">'+
                        row.code+
                        ' - '+
                        row.name+
                        '</option>'

                    );

                });

            }else{

                alert(response.message);

            }

        },

        error:function(xhr){

            console.log(xhr.responseText);

            alert("Unable to load programs.");

        }

    });

});
/*=========================================
LOAD SUBJECTS BY PROGRAM
=========================================*/

$("#program_id,#semester").change(function(){

    let program_id = $("#program_id").val();
    let semester   = $("#semester").val();

    $("#subject_id").html(
        '<option value="">Loading Subjects...</option>'
    );

    if(program_id=="" || semester==""){

        $("#subject_id").html(
            '<option value="">Select Subject</option>'
        );

        return;

    }

    $.ajax({

        url:"ajax/get_subjects.php",

        type:"POST",

        data:{

            program_id:program_id,
            semester:semester

        },

        dataType:"json",

        success:function(response){

            console.log(response);

            $("#subject_id").html(
                '<option value="">Select Subject</option>'
            );

            if(response.status=="success"){

                $.each(response.data,function(i,row){

                    $("#subject_id").append(

                        '<option value="'+row.id+'">'+
                        row.subject_code+
                        ' | '+
                        row.subject_name+
                        ' ('+
                        row.credit_hours+
                        ' CH)'+
                        '</option>'

                    );

                });

            }else{

                alert(response.message);

            }

        },

        error:function(xhr){

            console.log(xhr.responseText);

            alert("Unable to load subjects.");

        }

    });

});
/*=========================================
SAVE TIMETABLE
=========================================*/

$("#save_timetable").click(function(){

let teacher_id = $("#teacher_id").val();
let subject_id = $("#subject_id").val();
let program_id = $("#program_id").val();
let semester   = $("#semester").val();
let shift      = $("#shift").val();
let day_name   = $("#day_name").val();
let start_time = $("#start_time").val();
let end_time   = $("#end_time").val();
let room_no    = $("#room_no").val();
let status     = $("#status").val();

if(
teacher_id=="" ||
subject_id=="" ||
program_id=="" ||
semester=="" ||
shift=="" ||
day_name=="" ||
start_time=="" ||
end_time=="" ||
room_no==""
){

alert("Please Fill All Fields");

return;

}

$.ajax({

url:"save_timetable.php",
type:"POST",

data:{

teacher_id:teacher_id,
subject_id:subject_id,
program_id:program_id,
semester:semester,
shift:shift,
day_name:day_name,
start_time:start_time,
end_time:end_time,
room_no:room_no,
status:status

},

dataType:"json",

success:function(response){

alert(response.message);

if(response.status=="success"){

loadTimetable();

$("#room_no").val("");

}

},

error:function(){

alert("Unable To Save Timetable.");

}

});

});


/*=========================================
LOAD TIMETABLE
=========================================*/

function loadTimetable(){

$.ajax({

url:"ajax/load_timetable.php",

type:"POST",

dataType:"json",

success:function(response){

let html="";
let i=1;

if(response.status=="success"){

$.each(response.data,function(index,row){

html+=`

<tr>

<td>${i++}</td>

<td>${row.teacher_name}</td>

<td>${row.subject_name}</td>

<td>${row.program_code}</td>

<td>Semester ${row.semester}</td>

<td>${row.shift}</td>

<td>${row.day_name}</td>

<td>${row.start_time} - ${row.end_time}</td>

<td>${row.room_no}</td>

<td>

<span class="badge bg-success">

${row.status}

</span>

</td>

<td>

<button

class="btn btn-danger btn-sm deleteTimetable"

data-id="${row.id}">

<i class="fa fa-trash"></i>

</button>

</td>

</tr>

`;

});

}else{

html=`

<tr>

<td colspan="11" class="text-center">

No Timetable Found

</td>

</tr>

`;

}

$("#timetable_body").html(html);

}

});

}


/*=========================================
LOAD PAGE
=========================================*/

loadTimetable();

/*=========================================
DELETE TIMETABLE
=========================================*/

$(document).on("click",".deleteTimetable",function(){

let id=$(this).data("id");

if(confirm("Are you sure you want to delete this timetable?")){

$.ajax({

url:"ajax/delete_timetable.php",

type:"POST",

data:{
id:id
},

dataType:"json",

success:function(response){

alert(response.message);

if(response.status=="success"){

loadTimetable();

}

},

error:function(){

alert("Unable To Delete Timetable.");

}

});

}

});


/*=========================================
SEARCH TIMETABLE
=========================================*/

$("#search").keyup(function(){

let value=$(this).val().toLowerCase();

$("#timetable_body tr").filter(function(){

$(this).toggle(

$(this).text().toLowerCase().indexOf(value)>-1

);

});

});

/*=========================================
LOAD TEACHERS
=========================================*/

$("#faculty_id,#shift").change(function(){

    let faculty_id=$("#faculty_id").val();
    let shift=$("#shift").val();

    $("#teacher_id").html(
        '<option value="">Loading Teachers...</option>'
    );

    if(faculty_id=="" || shift==""){

        $("#teacher_id").html(
            '<option value="">Select Teacher</option>'
        );

        return;

    }

    $.ajax({

        url:"ajax/get_timetable_teachers.php",

        type:"POST",

        data:{
            faculty_id:faculty_id,
            shift:shift
        },

        dataType:"json",

        success:function(response){

            console.log(response);

            $("#teacher_id").html(
                '<option value="">Select Teacher</option>'
            );

            if(response.status=="success"){

                $.each(response.data,function(i,row){

                    $("#teacher_id").append(

                        '<option value="'+row.id+'">'+
                        row.employee_id+
                        ' | '+
                        row.teacher_name+
                        ' | '+
                        row.designation+
                        '</option>'

                    );

                });

            }else{

                alert(response.message);

            }

        },

        error:function(xhr){

            console.log(xhr.responseText);

            alert("Unable to load teachers.");

        }

    });

});


</script>

</body>

</html>