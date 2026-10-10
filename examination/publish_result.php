<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

/*=========================================
LOAD FACULTIES
=========================================*/

$facultyQuery = mysqli_query($conn,"
SELECT *
FROM faculties
WHERE status='Active'
ORDER BY faculty_name ASC
");

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Publish Results

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">

<style>

body{
background:#EEF4FF;
font-family:'Segoe UI',sans-serif;
}

.main-card{

max-width:1300px;
margin:30px auto;
background:white;
border-radius:18px;
overflow:hidden;
box-shadow:0 10px 25px rgba(0,0,0,.18);

}

.header{

background:linear-gradient(135deg,#003366,#0066CC,#F4D03F);
padding:25px;
color:white;

}

.section-title{

background:#003366;
color:white;
padding:12px;
border-radius:10px;
font-weight:bold;
margin-bottom:20px;

}

.form-control,
.form-select{

height:45px;

}

.btn-load{

background:#0066CC;
color:white;
font-weight:bold;

}

.btn-load:hover{

background:#004999;
color:white;

}

</style>

</head>

<body>

<div class="container">

<div class="main-card">

<div class="header">

<h2>

<i class="fas fa-file-circle-check me-2"></i>

Publish Semester Result

</h2>

<p class="mb-0">

Examination Office Result Publication

</p>

</div>

<div class="p-4">

<div class="section-title">

Search Result

</div>

<form id="publishForm">

<div class="row">

<div class="col-md-4 mb-3">

<label class="fw-bold">

Faculty

</label>

<select
id="faculty"
name="faculty_id"
class="form-select"
required>

<option value="">

Select Faculty

</option>

<?php

while($faculty=mysqli_fetch_assoc($facultyQuery)){

?>

<option value="<?php echo $faculty['id']; ?>">

<?php echo $faculty['faculty_name']; ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Program

</label>

<select
id="program"
name="program_id"
class="form-select"
required>

<option value="">

Select Program

</option>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Semester

</label>

<select
id="semester"
name="semester"
class="form-select"
required>

<option value="">

Select Semester

</option>

<?php

for($i=1;$i<=8;$i++){

?>

<option value="<?php echo $i; ?>">

Semester <?php echo $i; ?>

</option>

<?php

}

?>

</select>

</div>

</div>

<div class="col-md-12 text-end mb-3">

    <button
        type="button"
        id="btnLoad"
        class="btn btn-load btn-lg">

        <i class="fas fa-search me-2"></i>

        Load Result

    </button>

</div>

</div>

</form>

<hr>

<div id="resultArea"></div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/*==========================
LOAD PROGRAMS
==========================*/

document.getElementById("faculty").addEventListener("change",function(){

    let faculty=this.value;

    document.getElementById("program").innerHTML=
    "<option>Loading...</option>";

    fetch("ajax/get_programs.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"faculty_id="+faculty

    })

    .then(response=>response.json())

    .then(res=>{

        let html='<option value="">Select Program</option>';

        if(res.status=="success"){

            res.data.forEach(function(row){

                html+=`
                <option value="${row.id}">
                    ${row.program_name}
                </option>
                `;

            });

        }

        document.getElementById("program").innerHTML=html;

    });

});

/*=========================================
LOAD STUDENT RESULTS
=========================================*/

document.getElementById("btnLoad").addEventListener("click",function(){

    let form=document.getElementById("publishForm");

    let data=new FormData(form);

    fetch("ajax/load_publish_results.php",{

        method:"POST",

        body:data

    })

    .then(response=>response.text())

    .then(html=>{

        document.getElementById("resultArea").innerHTML=html;

    });

});

</script>

</body>

</html>