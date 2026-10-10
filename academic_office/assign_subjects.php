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

$faculty_sql = "
SELECT
    id,
    faculty_name
FROM faculties
WHERE status='Active'
ORDER BY faculty_name ASC
";

$faculty_result = mysqli_query($conn, $faculty_sql);

if ($faculty_result) {
    while ($row = mysqli_fetch_assoc($faculty_result)) {
        $faculties[] = $row;
    }
}

$page_title = "Assign Subjects to Teachers";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?php echo $page_title; ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
        <style>

        body{
            background:#f4f6f9;
            font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
        }

        .page-title{
            font-size:28px;
            font-weight:700;
            color:#0d6efd;
        }

        .card{
            border:none;
            border-radius:12px;
            box-shadow:0 3px 12px rgba(0,0,0,.08);
        }

        .card-header{
            font-weight:600;
            font-size:18px;
        }

        .form-label{
            font-weight:600;
        }

        .required{
            color:red;
        }

        .teacher-profile{
            display:none;
        }

        .assigned-subjects{
            display:none;
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
                <i class="fas fa-book-open me-2"></i>
                Assign Subjects to Teachers
            </h2>

            <p class="text-muted mb-0">
                Academic Office Panel
            </p>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header bg-primary text-white">

            <i class="fas fa-user-plus me-2"></i>

            Select Faculty, Program and Teacher

        </div>

        <div class="card-body">

            <div class="row">

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

                <!-- Subject -->

                <div class="col-lg-8 mb-3">

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

                <div class="col-lg-4 d-flex align-items-end mb-3">

                    <button
                        type="button"
                        class="btn btn-success w-100"
                        id="load_teacher">

                        <i class="fas fa-user-check me-2"></i>

                        Load Teacher Profile

                    </button>

                </div>

            </div>

        </div>

    </div>

        <!-- =========================================
         TEACHER COMPLETE PROFILE
    ========================================== -->

    <div class="row">

        <div class="col-lg-3">

            <div class="text-center mb-3">

                <img
                    src="../assets/images/default_teacher.png"
                    id="teacher_photo"
                    class="img-fluid rounded-circle border"
                    style="width:180px;height:180px;object-fit:cover;">

                <h4 class="mt-3 mb-1" id="teacher_name">
                    Teacher Name
                </h4>

                <span
                    class="badge bg-success"
                    id="teacher_status">

                    Active

                </span>

            </div>

        </div>

        <div class="col-lg-9">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Employee ID
                    </label>

                    <div id="employee_id">
                        -
                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Father Name
                    </label>

                    <div id="father_name">
                        -
                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        CNIC
                    </label>

                    <div id="cnic">
                        -
                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Gender
                    </label>

                    <div id="gender">
                        -
                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Date of Birth
                    </label>

                    <div id="dob">
                        -
                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Faculty
                    </label>

                    <div id="faculty_name">
                        -
                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Designation
                    </label>

                    <div id="designation">
                        -
                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Qualification
                    </label>

                    <div id="qualification">
                        -
                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Joining Date
                    </label>

                    <div id="joining_date">
                        -
                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Employment Type
                    </label>

                    <div id="employment_type">
                        -
                    </div>

                </div>

                                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Role
                    </label>

                    <div id="role">
                        -
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Email Address
                    </label>

                    <div id="email">
                        -
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Phone Number
                    </label>

                    <div id="phone">
                        -
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Address
                    </label>

                    <div id="address">
                        -
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Status
                    </label>

                    <div id="status">
                        -
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Created Date
                    </label>

                    <div id="created_at">
                        -
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- =========================================
     ASSIGN SUBJECT CARD
========================================== -->

<div class="card mb-4">

    <div class="card-header bg-primary text-white">

        <i class="fas fa-book me-2"></i>

        Assign Subject to Teacher

    </div>


    <div class="card-body">


        <div class="row">


            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Selected Teacher

                </label>

                <input 
                    type="text"
                    class="form-control"
                    id="selected_teacher"
                    readonly
                    placeholder="Select Teacher First">

            </div>



            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Selected Subject

                </label>

                <input 
                    type="text"
                    class="form-control"
                    id="selected_subject"
                    readonly
                    placeholder="Select Subject">

            </div>



            <div class="col-md-4 mb-3">

                <label class="form-label">

                    Credit Hours

                </label>

                <input 
                    type="text"
                    class="form-control"
                    id="credit_hours"
                    readonly
                    placeholder="Auto">

            </div>


        </div>



        <div class="row">


            <div class="col-md-12 text-end">


                <button 
                    type="button"
                    class="btn btn-success"
                    id="assign_subject_btn">


                    <i class="fas fa-check-circle me-2"></i>

                    Assign Subject


                </button>


            </div>


        </div>


    </div>


</div>

<!-- =========================================
     ASSIGNED SUBJECTS LIST
========================================== -->

<div class="card mb-4 assigned-subjects" id="assignedSubjectsCard">


    <div class="card-header bg-dark text-white">

        <i class="fas fa-list me-2"></i>

        Already Assigned Subjects

    </div>



    <div class="card-body">


        <div class="d-flex justify-content-between align-items-center mb-3">


            <h5 class="mb-0">

                Teacher Assigned Subjects

            </h5>


            <div>

    <span class="badge bg-primary fs-6 me-2">

        Total Assigned Subjects :
        <span id="total_assigned_subjects">0</span>

    </span>

    <span class="badge bg-success fs-6">

        Teacher :
        <span id="teacher_subject_count">0</span>

    </span>

</div>


        </div>



        <div class="table-responsive">


            <table class="table table-bordered table-hover">


                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Program
                        </th>

                        <th>
                            Semester
                        </th>

                        <th>
                        Shift
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

                        <th>
                            Assigned Date
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>



                <tbody id="assigned_subjects_body">


                    <tr>

                        <td colspan="9" class="text-center">

                            Select Teacher To View Assigned Subjects

                        </td>


                    </tr>


                </tbody>


            </table>


        </div>


    </div>


</div>

<!-- =========================================
     HIDDEN VALUES
========================================== -->

<input 
type="hidden"
id="hidden_teacher_id"
value="">


<input 
type="hidden"
id="hidden_subject_id"
value="">


<input 
type="hidden"
id="hidden_admin_id"
value="<?php echo $admin_id; ?>">



<!-- Footer Submit Area -->

<div class="card mb-4">


    <div class="card-body text-end">


        <button 
            type="button"
            class="btn btn-secondary me-2"
            onclick="window.location.reload();">


            <i class="fas fa-refresh me-2"></i>

            Reset


        </button>


    </div>


</div>


</div>



<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- jQuery -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<script>


/*=========================================
RESET PROFILE WHEN TEACHER CHANGED
=========================================*/

function clearTeacherProfile(){


    $("#teacher_name").html("Teacher Name");

    $("#employee_id").html("-");

    $("#father_name").html("-");

    $("#cnic").html("-");

    $("#gender").html("-");

    $("#dob").html("-");

    $("#faculty_name").html("-");

    $("#designation").html("-");

    $("#qualification").html("-");

    $("#joining_date").html("-");

    $("#employment_type").html("-");

    $("#role").html("-");

    $("#email").html("-");

    $("#phone").html("-");

    $("#address").html("-");

    $("#status").html("-");

    $("#created_at").html("-");


    $("#teacher_photo")
    .attr(
        "src",
        "../assets/images/default_teacher.png"
    );

    $("#selected_teacher").val("Select Teacher First");

}

/*=========================================
LOAD PROGRAMS BY FACULTY
=========================================*/

$("#faculty_id").change(function(){


    let faculty_id = $("#faculty_id").val();
let shift = $("#shift").val();


    $("#program_id").html(
        '<option value="">Loading Programs...</option>'
    );


    if(faculty_id==""){


        $("#program_id").html(
            '<option value="">Select Program</option>'
        );


        $("#teacher_id").html(
            '<option value="">Select Teacher</option>'
        );


        clearTeacherProfile();

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



            $("#program_id").html(
                '<option value="">Select Program</option>'
            );



            if(response.status=="success"){



                $.each(
                    response.data,
                    function(index,program){


                        $("#program_id").append(

                            '<option value="'+program.id+'">'+

                            program.code+
                            ' - '+
                            program.name

                            +

                            '</option>'

                        );


                    }

                );


            }


        },


        error:function(){


            $("#program_id").html(

                '<option value="">Error Loading Programs</option>'

            );


        }


    });



});

/*=========================================
LOAD TEACHERS
=========================================*/

$("#program_id,#shift").change(function(){

    let faculty_id = $("#faculty_id").val();
    let program_id = $("#program_id").val();
    let shift = $("#shift").val();

    $("#teacher_id").html(
        '<option value="">Loading Teachers...</option>'
    );

    if(faculty_id=="" || program_id=="" || shift==""){

        $("#teacher_id").html(
            '<option value="">Select Teacher</option>'
        );

        clearTeacherProfile();
        return;
    }

    $.ajax({

        url:"ajax/get_teachers.php",

        type:"POST",

        data:{
            faculty_id:faculty_id,
            program_id:program_id,
            shift:shift
        },

        dataType:"json",

        success:function(response){

            $("#teacher_id").html(
                '<option value="">Select Teacher</option>'
            );

            if(response.status=="success"){

                $.each(response.data,function(index,teacher){

                    $("#teacher_id").append(

                        '<option value="'+teacher.id+'">'+
                        teacher.employee_id+
                        ' | '+
                        teacher.teacher_name+
                        ' | '+
                        teacher.designation+
                        '</option>'

                    );

                });

            }

        },

        error:function(){

            $("#teacher_id").html(
                '<option value="">Error Loading Teachers</option>'
            );

        }

    });

});

/*=========================================
LOAD TEACHER PROFILE
=========================================*/

$("#teacher_id").change(function(){

    let teacher_id=$(this).val();

    clearTeacherProfile();

    if(teacher_id==""){
        return;
    }

    $.ajax({

        url:"ajax/get_teacher_profile.php",

        type:"POST",

        data:{
            teacher_id:teacher_id
        },

        dataType:"json",

        success:function(response){

            if(response.status=="success"){

                let t=response.data;

                $("#selected_teacher").val(
    t.employee_id + " | " +
    t.teacher_name + " | " +
    t.designation
);

                $("#teacher_name").html(t.teacher_name);
                $("#employee_id").html(t.employee_id);
                $("#father_name").html(t.father_name);
                $("#cnic").html(t.cnic);
                $("#gender").html(t.gender);
                $("#dob").html(t.date_of_birth);
                $("#faculty_name").html(t.faculty_name);
                $("#designation").html(t.designation);
                $("#qualification").html(t.qualification);
                $("#joining_date").html(t.joining_date);
                $("#employment_type").html(t.employment_type);
                $("#role").html(t.role);
                $("#email").html(t.email);
                $("#phone").html(t.phone);
                $("#address").html(t.address);
                $("#status").html(t.status);
                $("#created_at").html(t.created_at);

                if(t.profile_photo!=null && t.profile_photo!=""){

                    $("#teacher_photo").attr(
                        "src",
                        "../uploads/teachers/"+t.profile_photo
                    );

                }else{

                    $("#teacher_photo").attr(
                        "src",
                        "../assets/images/default_teacher.png"
                    );

                }

                 loadAssignedSubjects();
                 
            }

        }

    });

});

/*=========================================
LOAD SUBJECTS
=========================================*/

$("#program_id, #semester").change(function(){

    let program_id=$("#program_id").val();
    let semester=$("#semester").val();

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

            $("#subject_id").html(
                '<option value="">Select Subject</option>'
            );

            if(response.status=="success"){

                $.each(response.data,function(i,s){

                    $("#subject_id").append(

                        '<option value="'+s.id+'" data-credit="'+s.credit_hours+'">'+
                        s.subject_code+
                        ' | '+
                        s.subject_name+
                        ' ('+s.credit_hours+' CH)'+
                        '</option>'

                    );

                });

            }

        },

        error:function(){

            $("#subject_id").html(
                '<option value="">Error Loading Subjects</option>'
            );

        }

    });

});

/*=========================================
SHOW CREDIT HOURS + LOAD ASSIGNED TEACHER
=========================================*/

$("#subject_id").change(function(){

    let subject_id = $("#subject_id").val();
    let program_id = $("#program_id").val();
    let semester   = $("#semester").val();
    let shift      = $("#shift").val();

    let text = $("#subject_id option:selected").text();

    $("#selected_subject").val(text);

    $("#credit_hours").val(
        $("#subject_id option:selected").data("credit")
    );

    $("#assigned_teacher_box").html(`
        <div class="alert alert-secondary">
            Select a subject to see assigned teacher.
        </div>
    `);

    if(
        subject_id == "" ||
        program_id == "" ||
        semester == "" ||
        shift == ""
    ){
        return;
    }

    $("#assigned_teacher_box").html(`
        <div class="alert alert-info">
            Loading assigned teacher...
        </div>
    `);

    $.ajax({

        url: "ajax/get_assigned_teacher.php",

        type: "POST",

        data: {
            subject_id: subject_id,
            program_id: program_id,
            semester: semester,
            shift: shift
        },

        dataType: "json",

        success: function(response){

            if(response.status == "success"){

                let html = "";

                $.each(response.data, function(i, teacher){

                    html += `
                        <div class="card border-success mb-2">

                            <div class="card-body">

                                <h6 class="text-success mb-2">
                                    <i class="fa fa-user"></i>
                                    Assigned Teacher
                                </h6>

                                <strong>
                                    ${teacher.teacher_name}
                                </strong>

                                <br>

                                <small>
                                    Employee ID:
                                    ${teacher.employee_id}
                                </small>

                                <br>

                                <small>
                                    Designation:
                                    ${teacher.designation}
                                </small>

                                <br>

                                <small>
                                    Qualification:
                                    ${teacher.qualification}
                                </small>

                                <br>

                                <span class="badge bg-primary mt-2">
                                    ${teacher.teacher_shift}
                                </span>

                            </div>

                        </div>
                    `;

                });

                $("#assigned_teacher_box").html(html);

            }

            else if(response.status == "empty"){

                $("#assigned_teacher_box").html(`
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        No teacher assigned to this subject
                        for ${shift} shift.
                    </div>
                `);

            }

            else{

                $("#assigned_teacher_box").html(`
                    <div class="alert alert-danger">
                        Unable to load assigned teacher.
                    </div>
                `);

            }

        },

        error: function(xhr){

            console.log(xhr.responseText);

            $("#assigned_teacher_box").html(`
                <div class="alert alert-danger">
                    Error loading assigned teacher.
                </div>
            `);

        }

    });

});
/*=========================================
ASSIGN SUBJECT
=========================================*/

$("#assign_subject_btn").click(function(){

    let teacher_id=$("#teacher_id").val();

    let subject_id=$("#subject_id").val();

    let admin_id=$("#hidden_admin_id").val();

     let program_id = $("#program_id").val();

    let semester = $("#semester").val();

    let shift = $("#shift").val();

    if(teacher_id==""){

        alert("Please Select Teacher");

        return;

    }

    if(subject_id==""){

        alert("Please Select Subject");

        return;

    }

    $.ajax({

    url:"ajax/assign_subject.php",

    type:"POST",

    data:{

        teacher_id:teacher_id,

        subject_id:subject_id,

        program_id:program_id,

        semester:semester,

        shift:shift,

        admin_id:admin_id

    },

    dataType:"json",

        success:function(response){

            if(response.status=="success"){

                alert(response.message);

                loadAssignedSubjects();

            }

            else if(response.status=="duplicate"){

                alert(response.message);

            }

            else{

                alert(response.message);

            }

        }

    });

});

/*=========================================
LOAD ASSIGNED SUBJECTS
=========================================*/

function loadAssignedSubjects(){


    let teacher_id=$("#teacher_id").val();

    if(teacher_id==""){

        $("#assigned_subjects_body").html("");

        $("#total_assigned_subjects").html("0");

        return;

    }



    $.ajax({

    beforeSend:function(){

},

        url:"ajax/get_assigned_subjects.php",

        type:"POST",

        data:{
            teacher_id:teacher_id
        },

        dataType:"json",

        success:function(response){


    let html="";
    let total=0;

    if(response.status=="success" && response.data.length>0){

        $("#assignedSubjectsCard").show();

        $.each(response.data,function(i,row){

            total++;

            html+=`
            <tr>

                <td>${total}</td>

                <td>${row.program_code}</td>

                <td>Semester ${row.semester}</td>

                <td>${row.shift}</td>

                <td>${row.subject_code}</td>

                <td>${row.subject_name}</td>

                <td>${row.credit_hours}</td>

                <td>${row.assigned_at}</td>

                <td>
                    <span class="badge bg-success">
                        ${row.status}
                    </span>
                </td>

                <td>
                    <button
    class="btn btn-danger btn-sm removeSubject"
    data-id="${row.id}">

    <i class="fa fa-trash"></i> Remove

</button>
                </td>

            </tr>
            `;

        });

    }else{

        $("#assignedSubjectsCard").show();

        html=`
        <tr>
            <td colspan="9" class="text-center text-danger">
                No Subject Assigned
            </td>
        </tr>
        `;

    }

    $("#assigned_subjects_body").html(html);
    $("#total_assigned_subjects").html(total);
    $("#teacher_subject_count").html(total);

},

error:function(xhr){

    console.log(xhr.responseText);

}

    });

}

/*=========================================
REMOVE ASSIGNED SUBJECT
=========================================*/

$(document).on("click",".removeSubject",function(){

    let id=$(this).data("id");

    if(confirm("Are you sure you want to remove this assigned subject?")){

        $.ajax({

            url:"ajax/remove_assigned_subject.php",

            type:"POST",

            data:{
                id:id
            },

            dataType:"json",

            success:function(response){

                alert(response.message);

                if(response.status=="success"){

                    loadAssignedSubjects();

                }

            },

            error:function(){

                alert("Unable to remove subject.");

            }

        });

    }

});

</script>


</body>

</html>
