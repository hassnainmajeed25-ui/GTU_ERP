<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
EXAMINATION LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

/*=========================================
DEFAULT VARIABLES
=========================================*/

$faculty_id = "";
$program_id = "";
$semester = "";
$shift = "";
$subject_id = "";

$records = [];

/*=========================================
LOAD FACULTIES
=========================================*/

$facultyQuery = $conn->query("
SELECT *
FROM faculties
WHERE status='Active'
ORDER BY faculty_name ASC
");

/*=========================================
LOAD PROGRAMS
=========================================*/

$programQuery = $conn->query("
SELECT *
FROM programs
WHERE status='Active'
ORDER BY program_name ASC
");

/*=========================================
LOAD SUBJECTS
=========================================*/

$subjectQuery = $conn->query("
SELECT *
FROM subjects
WHERE status='Active'
ORDER BY subject_name ASC
");

/*=========================================
SEARCH RECORDS
=========================================*/

if(isset($_GET['search'])){

    $faculty_id = (int)$_GET['faculty_id'];
    $program_id = (int)$_GET['program_id'];
    $semester = (int)$_GET['semester'];
    $shift = trim($_GET['shift']);
    $subject_id = (int)$_GET['subject_id'];

    $stmt = $conn->prepare("

    SELECT

    students.id,
    students.roll_no,
    students.registration_no,
    students.first_name,
    students.last_name,

    marks_details.quiz_marks,
    marks_details.assignment_marks,
    marks_details.presentation_marks,
    marks_details.practical_marks,
    marks_details.mid_marks,
    marks_details.final_marks,
    marks_details.obtained_marks,

    mark_sessions.id AS session_id,
    mark_sessions.status,
    mark_sessions.result_status,

    subjects.subject_name,
    subjects.credit_hours

    FROM marks_details

    INNER JOIN students
    ON students.id = marks_details.student_id

    INNER JOIN mark_sessions
    ON mark_sessions.id = marks_details.mark_session_id

    INNER JOIN subjects
    ON subjects.id = mark_sessions.subject_id

    WHERE

    students.program_id=?
    AND students.semester=?
    AND students.shift=?
    AND mark_sessions.subject_id=?

    ORDER BY students.roll_no ASC

    ");

    $stmt->bind_param(
        "iisi",
        $program_id,
        $semester,
        $shift,
        $subject_id
    );

    $stmt->execute();

    $records = $stmt->get_result();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Verify Marks

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

max-width:1400px;
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

.header h2{

font-weight:bold;

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

.btn-search{

background:#0066CC;
color:white;
font-weight:bold;

}

.btn-search:hover{

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

<i class="fas fa-check-circle me-2"></i>

Verify Student Marks

</h2>

<p class="mb-0">

Examination Office Verification Panel

</p>

</div>

<div class="p-4">

<div class="section-title">

Search Marks

</div>

<form id="searchForm">

<div class="row">

<div class="col-md-3 mb-3">

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

$facultyQuery=mysqli_query(

$conn,

"SELECT * FROM faculties
WHERE status='Active'
ORDER BY faculty_name ASC"

);

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

<div class="col-md-3 mb-3">

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

<div class="col-md-2 mb-3">

<label class="fw-bold">

Semester

</label>

<select
id="semester"
name="semester"
class="form-select"
required>

<option value="">Select</option>

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

<div class="col-md-2 mb-3">

<label class="fw-bold">

Shift

</label>

<select
id="shift"
name="shift"
class="form-select"
required>

<option value="Morning">Morning</option>

<option value="Replica">Replica</option>

<option value="Evening">Evening</option>

</select>

</div>

<div class="col-md-2 mb-3">

<label class="fw-bold">

Subject

</label>

<select
id="subject"
name="subject_id"
class="form-select"
required>

<option value="">

Select Subject

</option>

</select>

</div>

</div>

<div class="text-end">

<button
type="button"
id="btnSearch"
class="btn btn-primary btn-lg">

<i class="fas fa-search me-2"></i>

Load Marks

</button>

</div>

</form>

<hr>

<div id="marksTable"></div>

<hr>

<?php

if(isset($_GET['search'])){

?>

<div class="section-title">

Uploaded Marks

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Roll No</th>

<th>Student Name</th>

<th>Quiz</th>

<th>Assignment</th>

<th>Presentation</th>

<th>Practical</th>

<th>Mid</th>

<th>Final</th>

<th>Total</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

$sr = 1;

while($row = $records->fetch_assoc()){

$total =
$row['quiz_marks'] +
$row['assignment_marks'] +
$row['presentation_marks'] +
$row['practical_marks'] +
$row['mid_marks'] +
$row['final_marks'];

?>

<tr>

<td>

<?php echo $sr++; ?>

</td>

<td>

<?php echo htmlspecialchars($row['roll_no']); ?>

</td>

<td>

<?php

echo htmlspecialchars(

$row['first_name']." ".$row['last_name']

);

?>

</td>

<td>

<?php echo $row['quiz_marks']; ?>

</td>

<td>

<?php echo $row['assignment_marks']; ?>

</td>

<td>

<?php echo $row['presentation_marks']; ?>

</td>

<td>

<?php echo $row['practical_marks']; ?>

</td>

<td>

<?php echo $row['mid_marks']; ?>

</td>

<td>

<?php echo $row['final_marks']; ?>

</td>

<td class="fw-bold text-primary">

<?php echo number_format($total,2); ?>

</td>

<td>

<?php

if($row['status']=="Verified"){

?>

<span class="badge bg-success">

Verified

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark">

Submitted

</span>

<?php

}

?>

</td>

<td>

<?php

if($row['status']=="Submitted"){

?>

<a

href="verify_single_result.php?session_id=<?php echo $row['session_id']; ?>&student_id=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm">

<i class="fas fa-check-circle me-1"></i>

Verify

</a>

<?php

}else{

?>

<span class="text-success fw-bold">

<i class="fas fa-check-circle"></i>

Done

</span>

<?php

}

?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php

}

?>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

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

    .then(response => response.json())

.then(response => {

    let html='<option value="">Select Program</option>';

    if(response.status=="success"){

        response.data.forEach(function(row){

            html += `
            <option value="${row.id}">
                ${row.program_name}
            </option>
            `;

        });

    }

    document.getElementById("program").innerHTML = html;

});

});

document.getElementById("program").addEventListener("change",loadSubjects);
document.getElementById("semester").addEventListener("change",loadSubjects);

function loadSubjects(){

    let program=document.getElementById("program").value;
    let semester=document.getElementById("semester").value;

    if(program=="" || semester=="") return;

    document.getElementById("subject").innerHTML=
    "<option>Loading...</option>";

    fetch("ajax/get_subjects.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:
        "program_id="+program+
        "&semester="+semester

    })

    .then(res=>res.json())
.then(response=>{

    let html='<option value="">Select Subject</option>';

    if(response.status=="success"){

        response.data.forEach(function(row){

            html += `
            <option value="${row.id}">
                ${row.subject_code} - ${row.subject_name}
            </option>
            `;

        });

    }

    document.getElementById("subject").innerHTML = html;

});

}

document.getElementById("btnSearch").addEventListener("click",function(){

    let form=document.getElementById("searchForm");

    let data=new FormData(form);

    fetch("ajax/load_marks.php",{

        method:"POST",

        body:data

    })

    .then(res=>res.text())

    .then(html=>{

        document.getElementById("marksTable").innerHTML=html;

    });

});

</script>

</body>

</html>