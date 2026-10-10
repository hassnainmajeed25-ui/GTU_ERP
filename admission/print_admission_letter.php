<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=====================================
LOGIN SECURITY
=====================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Admission Office"){
    header("Location: ../admins/login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location:selected_students.php");
    exit;
}

$id=(int)$_GET['id'];

/*=====================================
GET STUDENT DATA
=====================================*/

$sql=$conn->prepare("

SELECT

applications.application_id,
applications.registration_no,

students.first_name,
students.last_name,
students.father_name,
students.cnic,

faculties.faculty_name,

programs.program_name

FROM applications

INNER JOIN students
ON students.id=applications.student_id

LEFT JOIN faculties
ON faculties.id=applications.faculty_id

LEFT JOIN programs
ON programs.id=applications.program_id

WHERE applications.id=?

LIMIT 1

");

$sql->bind_param("i",$id);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

header("Location:selected_students.php");

exit;

}

$row=$result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Admission Letter</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{

background:#f4f6f9;

font-family:Times New Roman;

}

.letter{

width:900px;

margin:30px auto;

background:#fff;

padding:50px;

border:2px solid #000;

}

.logo{

width:90px;

}

.title{

font-size:30px;

font-weight:bold;

color:#0B1F52;

}

.subtitle{

font-size:18px;

margin-bottom:25px;

}

.heading{

text-align:center;

font-size:24px;

font-weight:bold;

text-decoration:underline;

margin:35px 0;

}

.info-table td{

padding:8px;

}

.footer{

margin-top:60px;

display:flex;

justify-content:space-between;

}

.signature{

text-align:center;

margin-top:70px;

}

@media print{

button{

display:none;

}

body{

background:white;

}

.letter{

border:none;

margin:0;

width:100%;

}

}

</style>

</head>

<body>

<div class="text-center mt-3">

<button onclick="window.print()" class="btn btn-primary">

Print Admission Letter

</button>

</div>

<div class="letter">

<div class="text-center">

<img src="../assets/images/logo.png" class="logo">

<div class="title">

GLOBAL TECH UNIVERSITY

</div>

<div class="subtitle">

Admission Office

</div>

</div>

<div class="heading">

PROVISIONAL ADMISSION LETTER

</div>
<table class="table table-bordered info-table mt-4">

<tr>

<th width="35%">Application ID</th>

<td>

<?php echo htmlspecialchars($row['application_id']); ?>

</td>

</tr>

<tr>

<th>Registration Number</th>

<td>

<strong>

<?php echo htmlspecialchars($row['registration_no']); ?>

</strong>

</td>

</tr>

<tr>

<th>Student Name</th>

<td>

<?php

echo htmlspecialchars($row['first_name']." ".$row['last_name']);

?>

</td>

</tr>

<tr>

<th>Father Name</th>

<td>

<?php echo htmlspecialchars($row['father_name']); ?>

</td>

</tr>

<tr>

<th>CNIC</th>

<td>

<?php echo htmlspecialchars($row['cnic']); ?>

</td>

</tr>

<tr>

<th>Faculty</th>

<td>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</td>

</tr>

<tr>

<th>Program</th>

<td>

<?php echo htmlspecialchars($row['program_name']); ?>

</td>

</tr>

</table>

<br>

<p style="font-size:18px; line-height:32px; text-align:justify;">

Dear

<strong>

<?php echo htmlspecialchars($row['first_name']." ".$row['last_name']); ?>

</strong>,

</p>

<p style="font-size:18px; line-height:32px; text-align:justify;">

Congratulations!

You have been <strong>provisionally selected</strong> for admission to

<strong>

<?php echo htmlspecialchars($row['program_name']); ?>

</strong>

under the

<strong>

<?php echo htmlspecialchars($row['faculty_name']); ?>

</strong>

at <strong>Global Tech University</strong>.

</p>

<p style="font-size:18px; line-height:32px; text-align:justify;">

Your Registration Number is

<strong>

<?php echo htmlspecialchars($row['registration_no']); ?>

</strong>.

Please keep this number safe because it will be required for all future academic records, fee payments, examinations and student services.

</p>

<p style="font-size:18px; line-height:32px; text-align:justify;">

This admission is provisional and subject to verification of your original documents, payment of semester fee and compliance with all university rules and regulations.

</p>
<hr class="mt-5">

<div class="row mt-5">

    <div class="col-6 text-center">

        <br><br><br>

        ___________________________

        <br>

        <strong>Admission Officer</strong>

        <br>

        Global Tech University

    </div>

    <div class="col-6 text-center">

        <br><br><br>

        ___________________________

        <br>

        <strong>Registrar</strong>

        <br>

        Global Tech University

    </div>

</div>

<div class="mt-5">

<p style="font-size:15px;">

<strong>Important Instructions:</strong>

</p>

<ol style="font-size:15px; line-height:28px;">

<li>
Bring all original academic documents at the time of admission.
</li>

<li>
Semester Fee must be deposited before the due date.
</li>

<li>
Failure to pay the fee within the due date may result in cancellation of admission.
</li>

<li>
Students must follow all university rules, regulations and code of conduct.
</li>

<li>
Keep this Admission Letter and Registration Number safe for future use.
</li>

</ol>

</div>

<div class="text-center mt-5">

<p>

<strong>

Global Tech University

</strong>

</p>

<p>

University Road, Sheikhupura, Punjab, Pakistan

</p>

<p>

Phone: +92 343 7860590

</p>

<p>

Email: hassnainmajeed25@gmail.com

</p>

<hr>

<p class="text-muted">

© <?php echo date("Y"); ?>

Global Tech University ERP System

</p>

</div>

</div>

</body>

</html>