<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location:login.php");
    exit;

}

$student_id=$_SESSION['student_id'];

/*=========================================
LOAD STUDENT
=========================================*/

$studentQuery=$conn->prepare("

SELECT

students.*,

programs.program_name,

faculties.faculty_name

FROM students

LEFT JOIN programs
ON students.program_id=programs.id

LEFT JOIN faculties
ON programs.faculty_id=faculties.id

WHERE students.id=?

LIMIT 1

");

$studentQuery->bind_param("i",$student_id);

$studentQuery->execute();

$result=$studentQuery->get_result();

if($result->num_rows==0){

die("Student Not Found");

}

$student=$result->fetch_assoc();

$page_title="Student ID Card";

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

*{
margin:0;
padding:0;
box-sizing:border-box;
}

body{

background:#ececec;
font-family:'Segoe UI',sans-serif;

}

/*=========================================
PRINT
=========================================*/

@page{

size:A4;
margin:0;

}

@media print{

body{

margin:0;
padding:0;
background:white;

display:flex;
justify-content:center;
align-items:flex-start;

}

.no-print{

display:none !important;

}

.id-card{

margin:0;

page-break-inside:avoid;
break-inside:avoid;


}

}

/*=========================================
CARD
=========================================*/

.id-card{

width:54mm;
height:86mm;

margin:30px auto;

background:#ffffff;

border:2px solid #0d6efd;

border-radius:10px;

overflow:hidden;

box-shadow:0 8px 20px rgba(0,0,0,.25);

page-break-inside:avoid;
break-inside:avoid;

}

/*=========================================
HEADER
=========================================*/

.header{

height:60px;

background:#0d6efd;

display:flex;

align-items:center;

padding:10px;

color:#fff;

}

.logo{

width:42px;
height:42px;

object-fit:contain;

margin-right:10px;

}

.university h4{

margin:0;

font-size:13px;

font-weight:bold;

}

.university p{

margin:0;

font-size:8px;

letter-spacing:1px;

}

</style>

</head>

<body>

<div class="id-card">

<!--=========================================
HEADER
=========================================-->

<div class="header">

<img
src="../assets/images/logo.png"
class="logo">

<div class="university">

<h4>

Global Tech University

</h4>

<p>

STUDENT IDENTITY CARD

</p>

</div>

</div>

<!--=========================================
PHOTO SECTION
=========================================-->

<div style="text-align:center;padding-top:10px;">

<?php

$photo="../uploads/student_photos/".$student['profile_photo'];

if(!empty($student['profile_photo']) && file_exists($photo)){

?>

<img

src="<?php echo $photo; ?>"

style="width:75px;
height:75px;
border-radius:50%;
object-fit:cover;
border:3px solid #0d6efd;">

<?php

}else{

?>

<img

src="../assets/images/default-user.png"

style="width:75px;
height:75px;
border-radius:50%;
object-fit:cover;
border:3px solid #0d6efd;">

<?php

}

?>

</div>

<!--=========================================
STUDENT INFORMATION
=========================================-->

<div style="padding:10px 14px;">

<table
class="table table-borderless table-sm mb-0"
style="font-size:9px;">

<tr>

<td width="38%">

<strong>Name</strong>

</td>

<td>

<?php

echo htmlspecialchars(
$student['first_name']." ".$student['last_name']
);

?>

</td>

</tr>

<tr>

<td>

<strong>Roll No</strong>

</td>

<td>

<?php echo htmlspecialchars($student['roll_no']); ?>

</td>

</tr>

<tr>

<td>

<strong>Registration No</strong>

</td>

<td>

<?php echo htmlspecialchars($student['registration_no']); ?>

</td>

</tr>

<tr>

<td>

<strong>Program</strong>

</td>

<td>

<?php echo htmlspecialchars($student['program_name']); ?>

</td>

</tr>

<tr>

<td>

<strong>Faculty</strong>

</td>

<td>

<?php echo htmlspecialchars($student['faculty_name']); ?>

</td>

</tr>

<tr>

<td>

<strong>Semester</strong>

</td>

<td>

<?php echo htmlspecialchars($student['semester']); ?>

</td>

</tr>

</table>

</div>

<!--=========================================
FOOTER
=========================================-->

<div
style="position:absolute;
bottom:8px;
left:0;
width:100%;
text-align:center;
font-size:8px;
color:#666;">

Global Tech University

</div>

</div>

<!--=========================================
PRINT BUTTON
=========================================-->

<div class="text-center mt-4 no-print">

<button

class="btn btn-primary btn-lg"

onclick="window.print();">

<i class="fas fa-print"></i>

Print Student ID Card

</button>

</div>

<script>

function printCard(){

window.print();

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>