<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt->bind_param("i",$student_id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

$full_name = $student['first_name']." ".$student['last_name'];


/*==================================
GET STUDENT APPLICATION
==================================*/

$app = $conn->prepare("

SELECT *

FROM applications

WHERE student_id=?

LIMIT 1

");

$app->bind_param("i",$student_id);

$app->execute();

$appResult = $app->get_result();

$application = $appResult->fetch_assoc();


/*==================================
GET PROCESSING FEE STATUS
==================================*/

$processingFeeApproved = false;

if($application){

    $application_id = $application['id'];

    $paymentCheck = $conn->prepare("

    SELECT verification_status

    FROM application_payments

    WHERE application_id=?

    ORDER BY id DESC

    LIMIT 1

    ");

    $paymentCheck->bind_param("i",$application_id);

    $paymentCheck->execute();

    $paymentResult = $paymentCheck->get_result();

    if($paymentResult->num_rows > 0){

        $paymentData = $paymentResult->fetch_assoc();

        if($paymentData['verification_status'] === 'Verified'){

            $processingFeeApproved = true;

        }

    }

}


/*==================================
GET FEE STRUCTURE
==================================*/

$fee = null;

if($application){

    $faculty_id = $application['faculty_id'];

    $program_id = $application['program_id'];

    $feeQuery = $conn->prepare("

    SELECT *

    FROM fee_structure

    WHERE faculty_id=?

    AND program_id=?

    AND shift=?

    AND status='Active'

    LIMIT 1

    ");

    $shift = $application['shift'];

    $feeQuery->bind_param(

        "iis",

        $faculty_id,

        $program_id,

        $shift

    );

    $feeQuery->execute();

    $feeResult = $feeQuery->get_result();

    if($feeResult->num_rows>0){

        $fee = $feeResult->fetch_assoc();

    }

}


/*==================================
UNREAD NOTIFICATIONS
==================================*/

$notificationCount = 0;

$notification = $conn->prepare("

SELECT COUNT(*) AS total

FROM notifications

WHERE student_id=?

AND is_read=0

");

$notification->bind_param("i",$student_id);

$notification->execute();

$countResult = $notification->get_result();

if($countResult->num_rows>0){

    $row = $countResult->fetch_assoc();

    $notificationCount = $row['total'];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Student Dashboard | GTU</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/style.css">

<style>

body{
background:#f4f6f9;
}

.sidebar{
background:#0B1F52;
min-height:100vh;
color:#fff;
padding:25px;
}

.sidebar h4{
font-weight:700;
margin-bottom:25px;
}

.sidebar a{
display:block;
color:#fff;
padding:12px 10px;
margin-bottom:8px;
text-decoration:none;
border-radius:8px;
transition:.3s;
}

.sidebar a:hover{
background:#FDB913;
color:#000;
}

.dashboard-card{
background:#fff;
padding:25px;
border-radius:15px;
box-shadow:0 10px 30px rgba(0,0,0,.08);
transition:.3s;
}

.dashboard-card:hover{
transform:translateY(-5px);
}

.welcome-box{
background:linear-gradient(135deg,#0B1F52,#163A8C);
color:#fff;
padding:30px;
border-radius:15px;
margin-bottom:25px;
}

</style>

</head>

<body>

<div class="container-fluid">

<div class="row">


<!-- SIDEBAR -->

<div class="col-lg-3 sidebar">

<h4>GTU Student Panel</h4>

<p>Welcome</p>

<h5>

<?php echo htmlspecialchars($full_name); ?>

</h5>

<hr>


<a href="#">

<i class="fas fa-home me-2"></i>

Dashboard

</a>


<a href="apply/personal.php">

<i class="fas fa-file-alt me-2"></i>

Apply Admission

</a>


<a href="pay_processing_fee.php">

<i class="fas fa-money-check-dollar me-2"></i>

Processing Fee

</a>


<?php if($processingFeeApproved){ ?>

<a href="pay_semester_fee.php">

<i class="fas fa-wallet me-2"></i>

Semester Fee

</a>

<?php } ?>


<a href="compose_message.php">

<i class="fas fa-paper-plane me-2"></i>

Compose Message

</a>


<a href="inbox.php">

<i class="fas fa-envelope me-2"></i>

Inbox

</a>


<a href="my_profile.php">

<i class="fas fa-user me-2"></i>

My Profile

</a>


<a href="my_applications.php">

<i class="fas fa-folder me-2"></i>

My Applications

</a>


<a href="notifications.php">

<i class="fas fa-bell me-2"></i>

Notifications

<?php if($notificationCount>0){ ?>

<span class="badge bg-danger ms-2">

<?php echo $notificationCount; ?>

</span>

<?php } ?>

</a>


<a href="logout.php">

<i class="fas fa-sign-out-alt me-2"></i>

Logout

</a>

</div>


<!-- MAIN CONTENT -->

<div class="col-lg-9 p-4">


<div class="welcome-box">

<h2>

Welcome <?php echo htmlspecialchars($full_name); ?> 👋

</h2>

<p>

Manage your admission process from your dashboard.

</p>

</div>


<?php

$popup = $conn->prepare("

SELECT *

FROM notifications

WHERE

student_id=?

AND category IN('Admission','Fee')

AND is_read=0

ORDER BY id DESC

LIMIT 1

");

$popup->bind_param("i",$student_id);

$popup->execute();

$popupResult = $popup->get_result();

if($popupResult->num_rows>0){

$popupData = $popupResult->fetch_assoc();

?>


<div class="modal fade"

id="studentNotification"

tabindex="-1"

data-bs-backdrop="static"

data-bs-keyboard="false">


<div class="modal-dialog modal-dialog-centered">


<div class="modal-content">


<div class="modal-header bg-success text-white">

<h5 class="modal-title">

<i class="fas fa-bell"></i>

<?php echo htmlspecialchars($popupData['title']); ?>

</h5>

</div>


<div class="modal-body text-center">

<i class="fas fa-graduation-cap"

style="font-size:70px;color:#0B5ED7;"></i>

<br><br>

<p style="font-size:18px;">

<?php echo nl2br(htmlspecialchars($popupData['message'])); ?>

</p>

</div>


<div class="modal-footer">

<a

href="read_notification.php?id=<?php echo $popupData['id']; ?>"

class="btn btn-success w-100">

<i class="fas fa-check"></i>

OK

</a>

</div>


</div>

</div>

</div>


<script>

window.onload=function(){

var myModal=new bootstrap.Modal(

document.getElementById('studentNotification')

);

myModal.show();

};

</script>


<?php

}

?>


<?php

if(!empty($application['roll_no'])){

?>


<div class="alert alert-success shadow-sm">

<h5>

🎉 Congratulations!

</h5>

<p class="mb-1">

Your Semester Fee has been verified successfully.

</p>

<p class="mb-1">

Your Official University Roll Number has been generated.

</p>

<h4 class="text-primary">

<?php echo htmlspecialchars($application['roll_no']); ?>

</h4>

<p class="mb-0">

Please use this Roll Number for all academic activities, examinations, and official correspondence.

</p>

</div>


<?php

}

?>


<div class="row g-4">


<?php

/*
==================================================
SEMESTER FEE
ONLY SHOW AFTER PROCESSING FEE IS VERIFIED
==================================================
*/

if($fee && $processingFeeApproved){

    $total=

    $fee['admission_fee']+

    $fee['tuition_fee']+

    $fee['exam_fee']+

    $fee['library_fee']+

    $fee['lab_fee']+

    $fee['other_fee'];

?>


<div class="col-lg-12">

<div class="dashboard-card">

<h4 class="mb-3">

Semester Fee Challan

</h4>


<table class="table table-bordered">


<tr>

<th>Admission Fee</th>

<td>

Rs. <?php echo number_format($fee['admission_fee']); ?>

</td>

</tr>


<tr>

<th>Tuition Fee</th>

<td>

Rs. <?php echo number_format($fee['tuition_fee']); ?>

</td>

</tr>


<tr>

<th>Exam Fee</th>

<td>

Rs. <?php echo number_format($fee['exam_fee']); ?>

</td>

</tr>


<tr>

<th>Library Fee</th>

<td>

Rs. <?php echo number_format($fee['library_fee']); ?>

</td>

</tr>


<tr>

<th>Lab Fee</th>

<td>

Rs. <?php echo number_format($fee['lab_fee']); ?>

</td>

</tr>


<tr>

<th>Other Fee</th>

<td>

Rs. <?php echo number_format($fee['other_fee']); ?>

</td>

</tr>


<tr class="table-success">

<th>Total Fee</th>

<td>

<strong>

Rs. <?php echo number_format($total); ?>

</strong>

</td>

</tr>


</table>


<a

href="pay_semester_fee.php"

class="btn btn-success">

<i class="fas fa-money-check-dollar me-2"></i>

Pay Semester Fee

</a>


</div>

</div>


<?php

}

?>


<div class="col-md-4">

<div class="dashboard-card">

<h5>

Application Status

</h5>

<p>

Not Applied Yet

</p>

</div>

</div>


<div class="col-md-4">

<div class="dashboard-card">

<h5>

Profile

</h5>

<p>

Complete your profile

</p>

</div>

</div>


<div class="col-md-4">

<div class="dashboard-card">

<h5>

Notifications

</h5>

<p>

No new alerts

</p>

</div>

</div>


</div>

</div>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>