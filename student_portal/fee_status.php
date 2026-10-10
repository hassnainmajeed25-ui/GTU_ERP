<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

/*=========================================
GET STUDENT INFORMATION
=========================================*/

$student = $conn->prepare("

SELECT

students.id,
students.registration_no,
students.roll_no,
students.program_id,
students.semester,
students.shift,
students.session_id,
students.result_status,

programs.program_name,
programs.faculty_id,
faculties.faculty_name

FROM students

LEFT JOIN programs
ON students.program_id = programs.id

LEFT JOIN faculties
ON programs.faculty_id = faculties.id

WHERE students.id=?

LIMIT 1

");

$student->bind_param("i",$student_id);
$student->execute();

$studentData = $student->get_result()->fetch_assoc();

/*=========================================
CURRENT SEMESTER
=========================================*/

$currentSemester = (int)$studentData['semester'];

/*=========================================
NEXT SEMESTER
=========================================*/

$nextSemester = $currentSemester + 1;

/*=========================================
CHECK NEXT SEMESTER FEE
=========================================*/

$nextFee = $conn->prepare("

SELECT *

FROM fee_structure

WHERE

program_id=?
AND semester=?
AND shift=?
AND session_id=?
AND status='Active'

LIMIT 1

");

$nextFee->bind_param(

"iisi",

$studentData['program_id'],
$nextSemester,
$studentData['shift'],
$studentData['session_id']

);

$nextFee->execute();

$nextFeeData = $nextFee->get_result()->fetch_assoc();

/*=========================================
CURRENT SEMESTER FEE
=========================================*/

$fee = $conn->prepare("

SELECT *

FROM fee_structure

WHERE

program_id=?
AND semester=?
AND shift=?
AND session_id=?
AND status='Active'

LIMIT 1

");

$fee->bind_param(

"iisi",

$studentData['program_id'],
$currentSemester,
$studentData['shift'],
$studentData['session_id']

);

$fee->execute();

$feeData = $fee->get_result()->fetch_assoc();

/*=========================================
SHOW POPUP
=========================================*/

$showPopup = false;

if($nextFeeData == NULL){

    $showPopup = true;

}

/*=========================================
GET PAYMENT STATUS
=========================================*/

$payment_status = "Unpaid";

$getPayment = $conn->prepare("

SELECT payment_status

FROM fee_payments

WHERE

student_id=?
AND fee_structure_id=?

ORDER BY id DESC

LIMIT 1

");

$getPayment->bind_param(

"ii",

$student_id,

$feeData['id']

);

$getPayment->execute();

$payment = $getPayment->get_result()->fetch_assoc();

if($payment){

    $payment_status = $payment['payment_status'];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Semester Fee Status</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#000;
    color:#fff;
    font-family:'Segoe UI',sans-serif;
}

.main-card{
    max-width:1200px;
    margin:40px auto;
    background:#111;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 0 30px rgba(255,255,0,.15);
}

.header{
    background:#E6FF00;
    color:#000;
    padding:20px;
    text-align:center;
    font-size:28px;
    font-weight:bold;
}

.student-info{
    background:#181818;
    padding:25px;
    border-bottom:2px solid #E6FF00;
}

.student-info h4{
    color:#E6FF00;
}

.table thead{
    background:#E6FF00;
    color:#000;
}

.table td{
    background:#111;
    color:#fff;
    border-color:#333;
}

.total-box{
    background:#E6FF00;
    color:#000;
    text-align:center;
    font-size:28px;
    font-weight:bold;
    padding:20px;
}

.status-box{
    background:#181818;
    padding:25px;
    text-align:center;
}

.badge-status{
    font-size:20px;
    padding:15px 40px;
}

.footer-buttons{
    background:#111;
    text-align:center;
    padding:25px;
}

.footer-buttons .btn{
    margin:10px;
}

</style>

</head>

<body>
<?php if($showPopup){ ?>

<div class="modal fade"
id="feePopup"
data-bs-backdrop="static"
data-bs-keyboard="false"
tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content"
style="background:#111;border:2px solid #E6FF00;">

<div class="modal-header"
style="background:#E6FF00;color:#000;">

<h4 class="modal-title">

<i class="fas fa-circle-info me-2"></i>

Fee Status Information

</h4>

</div>

<div class="modal-body text-center text-white">

<i class="fas fa-hourglass-half"
style="font-size:70px;color:#E6FF00;"></i>

<h3 class="mt-4 text-warning">

Please Wait

</h3>

<p>

You have already paid your current semester fee.

</p>

<p>

Your next semester fee has not been uploaded yet.

</p>

<p>

Please wait until the Admission Office uploads your next semester fee.

</p>

</div>

<div class="modal-footer">

<a href="dashboard.php"
class="btn btn-warning">

OK

</a>

</div>

</div>

</div>

</div>

<script>

window.onload = function(){

var popup = new bootstrap.Modal(
document.getElementById('feePopup')
);

popup.show();

}

</script>

<?php } ?>

<?php if(!$showPopup){ ?>

<div class="main-card">

<div class="header">

<i class="fas fa-money-check-dollar me-2"></i>

Semester Fee Status

</div>

<div class="student-info">

<h4>

<i class="fas fa-user-graduate me-2"></i>

Student Information

</h4>

<div class="row">

<div class="col-md-6">

<p>
<strong>Registration No :</strong>
<?php echo $studentData['registration_no']; ?>
</p>

<p>
<strong>Roll No :</strong>
<?php echo $studentData['roll_no']; ?>
</p>

<p>
<strong>Faculty :</strong>
<?php echo $studentData['faculty_name']; ?>
</p>

</div>

<div class="col-md-6">

<p>
<strong>Program :</strong>
<?php echo $studentData['program_name']; ?>
</p>

<p>
<strong>Current Semester :</strong>
<?php echo $studentData['semester']; ?>
</p>

<p>
<strong>Next Semester Fee :</strong>

<span class="text-warning">

Semester <?php echo $nextSemester; ?>

</span>

</p>

<p>

<strong>Shift :</strong>

<?php echo $studentData['shift']; ?>

</p>

</div>

</div>

</div>

<div class="table-responsive">

<table class="table table-bordered text-center align-middle">

<thead>

<tr>

<th width="60%">

Fee Head

</th>

<th>

Amount (Rs.)

</th>

</tr>

</thead>

<tbody>

<tr>
<td class="text-start">
<i class="fas fa-file-circle-plus text-warning me-2"></i>
Admission Fee
</td>
<td>
Rs. <?php echo number_format($feeData['admission_fee'],2); ?>
</td>
</tr>

<tr>
<td class="text-start">
<i class="fas fa-graduation-cap text-warning me-2"></i>
Tuition Fee
</td>
<td>
Rs. <?php echo number_format($feeData['tuition_fee'],2); ?>
</td>
</tr>

<tr>
<td class="text-start">
<i class="fas fa-file-signature text-warning me-2"></i>
Examination Fee
</td>
<td>
Rs. <?php echo number_format($feeData['exam_fee'],2); ?>
</td>
</tr>

<tr>
<td class="text-start">
<i class="fas fa-book text-warning me-2"></i>
Library Fee
</td>
<td>
Rs. <?php echo number_format($feeData['library_fee'],2); ?>
</td>
</tr>

<tr>
<td class="text-start">
<i class="fas fa-flask text-warning me-2"></i>
Lab Fee
</td>
<td>
Rs. <?php echo number_format($feeData['lab_fee'],2); ?>
</td>
</tr>

<tr>
<td class="text-start">
<i class="fas fa-coins text-warning me-2"></i>
Other Fee
</td>
<td>
Rs. <?php echo number_format($feeData['other_fee'],2); ?>
</td>
</tr>

</tbody>

</table>

</div>

<?php

$total =
$feeData['admission_fee'] +
$feeData['tuition_fee'] +
$feeData['exam_fee'] +
$feeData['library_fee'] +
$feeData['lab_fee'] +
$feeData['other_fee'];

?>

<div class="total-box">

Grand Total :
Rs. <?php echo number_format($total,2); ?>

</div>

<div class="status-box">

<h3>

Fee Payment Status

</h3>

<?php

if($payment_status=="Verified"){

?>

<span class="badge bg-success badge-status">

<i class="fas fa-circle-check me-2"></i>

PAID

</span>

<p class="mt-4">

Your fee has been verified by Finance Office.

</p>

<?php

}else{

?>

<span class="badge bg-danger badge-status">

<i class="fas fa-circle-xmark me-2"></i>

UNPAID

</span>

<p class="mt-4">

Your next semester fee is available.

</p>

<p>

After paying the fee,

Finance Office will verify your payment.

</p>

<div class="mt-4">

<a
href="payment_details.php"
class="btn btn-warning btn-lg">

<i class="fas fa-upload me-2"></i>

Payment Details

</a>

</div>

<?php

}

?>

</div>

<div class="footer-buttons">

<a
href="dashboard.php"
class="btn btn-warning">

<i class="fas fa-house me-2"></i>

Dashboard

</a>

<a
href="weekly_timetable.php"
class="btn btn-outline-light">

<i class="fas fa-calendar-week me-2"></i>

Weekly Timetable

</a>

</div>

</div>

<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if($showPopup){ ?>

<script>

document.addEventListener("DOMContentLoaded", function(){

    var feePopup = new bootstrap.Modal(
        document.getElementById("feePopup")
    );

    feePopup.show();

});

</script>

<?php } ?>

</body>

</html>