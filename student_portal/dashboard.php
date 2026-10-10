<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location: login.php");
    exit;

}

$student_id = $_SESSION['student_id'];

/*=========================================
LOAD STUDENT INFORMATION
=========================================*/

$sql = "

SELECT

students.*,

programs.program_name,

programs.program_code,

faculties.faculty_name

FROM students

LEFT JOIN programs
ON students.program_id = programs.id

LEFT JOIN faculties
ON programs.faculty_id = faculties.id

WHERE students.id = ?

LIMIT 1

";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$student_id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    session_destroy();

    header("Location:login.php");

    exit;

}

$student = $result->fetch_assoc();


/*=========================================
DATE & TIME
=========================================*/

$today = date("l, d F Y");

$current_time = date("h:i A");

$page_title = "Student Dashboard";

?>

<?php include 'notification_popup.php'; ?>

<?php

/*=========================================
RESULT NOTIFICATION
=========================================*/

$popup = $conn->prepare("
SELECT *
FROM notifications
WHERE student_id=?
AND category='Result'
AND is_read=0
ORDER BY id DESC
LIMIT 1
");

$popup->bind_param("i",$student_id);
$popup->execute();
$popupResult = $popup->get_result();

$popupData = null;

if($popupResult->num_rows > 0){
    $popupData = $popupResult->fetch_assoc();
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
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
font-family:'Segoe UI',sans-serif;
background:#eef4fb;
overflow-x:hidden;
}

/*=========================================
SIDEBAR
=========================================*/

.sidebar{

position:fixed;

top:0;
left:0;

width:270px;
height:100vh;

background:#0B3D91;

overflow-y:auto;

box-shadow:5px 0 20px rgba(0,0,0,.12);

z-index:999;

}

.logo{

padding:25px;

text-align:center;

border-bottom:1px solid rgba(255,255,255,.15);

}

.logo img{

width:80px;
margin-bottom:12px;

}

.logo h4{

color:#fff;

font-weight:700;

margin:0;

}

.logo small{

color:#d9d9d9;

}

.sidebar ul{

list-style:none;

padding:20px 0;

margin:0;

}

.sidebar ul li{

margin-bottom:5px;

}

.sidebar ul li a{

display:block;

padding:15px 25px;

color:#fff;

text-decoration:none;

font-size:15px;

transition:.3s;

}

.sidebar ul li a i{

width:28px;

}

.sidebar ul li a:hover{

background:#198754;

padding-left:35px;

}

.sidebar ul li a.active{

background:#fff;

color:#0B3D91;

font-weight:bold;

}

/*=========================================
MAIN
=========================================*/

.main{

margin-left:270px;

min-height:100vh;

}

/*=========================================
TOPBAR
=========================================*/

.topbar{

background:#fff;

padding:18px 30px;

display:flex;

justify-content:space-between;

align-items:center;

box-shadow:0 3px 12px rgba(0,0,0,.08);

}

.student-box{

display:flex;

align-items:center;

gap:15px;

}

.student-photo{

width:58px;
height:58px;

border-radius:50%;

background:#198754;

display:flex;

align-items:center;

justify-content:center;

overflow:hidden;

color:#fff;

font-size:28px;

}

.student-photo img{

width:100%;
height:100%;
object-fit:cover;
display:block;

}

.student-name{

font-weight:700;

font-size:20px;

margin:0;

color:#0B3D91;

}

.student-roll{

font-size:14px;

color:#666;

margin:0;

}

.right-box{

text-align:right;

}

.right-box h6{

margin:0;

font-weight:bold;

color:#198754;

}

.right-box small{

color:#666;

}

.content{

padding:30px;

}

.card{

border:none;

border-radius:18px;

box-shadow:0 5px 18px rgba(0,0,0,.08);

}

</style>

</head>

<body>

<!-- =========================================
SIDEBAR
========================================= -->

<div class="sidebar">

<div class="logo">

<img src="../assets/images/logo.png">

<h4>GTU ERP</h4>

<small>Student Portal</small>

</div>

<ul>

<li>
<a href="dashboard.php" class="active">
<i class="fas fa-house"></i>
Dashboard
</a>
</li>

<li>
<a href="profile.php">
<i class="fas fa-user"></i>
My Profile
</a>
</li>

<li>
<a href="course_marks.php">
<i class="fas fa-book-open"></i>
My Subjects
</a>
</li>

<li>
<a href="attendance.php">
<i class="fas fa-calendar-check"></i>
Attendance
</a>
</li>

<li>

<a href="student_id_card.php">

<i class="fas fa-id-card"></i>

Student ID Card

</a>

</li>

<li>

<a href="gpa_calculator.php">

<i class="fas fa-id-card"></i>

   GPA Calculator

</a>

</li>


<li>
<a href="student_result.php">
<i class="fas fa-square-poll-vertical"></i>
Results
</a>
</li>

<li>
<a href="feedback.php">
<i class="fas fa-comment-dots"></i>
Course Feedback
</a>
</li>

<li>
<a href="weekly_timetable.php">
<i class="fas fa-square-poll-vertical"></i>
Timetable
</a>
</li>

<li>
<a href="fee_status.php">
<i class="fas fa-money-check-dollar"></i>
Fee Status
</a>
</li>

<li>
<a href="summer_enrollment.php">
<i class="fas fa-sun fa-2x mb-2"></i>
Summer Enrollment
</a>
</li>

<li>
<a href="compose_message.php">
<i class="fas fa-envelope"></i>
Compose Messages
</a>
</li>

<li>
<a href="sent_messages.php">
<i class="fas fa-envelope"></i>
Sent Messages
</a>
</li>

<li>
<a href="inbox.php">
<i class="fas fa-envelope"></i>
Inbox
</a>
</li>

<li>
<a href="announcements.php">
<i class="fas fa-bullhorn"></i>
Announcements
</a>
</li>

<li>
<a href="logout.php">
<i class="fas fa-right-from-bracket"></i>
Logout
</a>
</li>

</ul>

</div>

<!-- =========================================
MAIN
========================================= -->

<div class="main">

<div class="topbar">

<div class="student-box">

<div class="student-photo">

<?php

$image = "uploads/profile/" . $student['profile_photo'];

if(!empty($student['profile_photo']) && file_exists($image)){

?>

<img
src="<?php echo $image; ?>"
style="width:58px;height:58px;border-radius:50%;object-fit:cover;">

<?php

}else{

?>

<i class="fas fa-user-graduate"></i>

<?php

}

?>

</div>

<div>

<h5 class="student-name">

<?php echo htmlspecialchars($student['first_name']." ".$student['last_name']); ?>

</h5>

<p class="student-roll">

Roll No :
<strong>

<?php echo htmlspecialchars($student['roll_no']); ?>

</strong>

</p>

</div>

</div>

<div class="right-box">

<h6>

<?php echo $today; ?>

</h6>

<small>

<?php echo $current_time; ?>

</small>

</div>

</div>

<div class="content">

<!-- =========================================
WELCOME CARD
========================================= -->

<div class="card mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-lg-8">

<h2 class="fw-bold text-primary">

Welcome,

<?php echo htmlspecialchars($student['first_name']." ".$student['last_name']); ?>

👋

</h2>

<p class="text-muted mb-3">

Welcome to

<strong>

Global Tech University ERP Student Portal

</strong>

We wish you success in your academic journey.

</p>

<div class="row">

<div class="col-md-6 mb-2">

<i class="fas fa-id-card text-primary me-2"></i>

<strong>Registration No :</strong>

<?php echo htmlspecialchars($student['registration_no']); ?>

</div>

<div class="col-md-6 mb-2">

<i class="fas fa-user-graduate text-success me-2"></i>

<strong>Roll No :</strong>

<?php echo htmlspecialchars($student['roll_no']); ?>

</div>

<div class="col-md-6 mb-2">

<i class="fas fa-building text-warning me-2"></i>

<strong>Faculty :</strong>

<?php echo htmlspecialchars($student['faculty_name']); ?>

</div>

<div class="col-md-6 mb-2">

<i class="fas fa-book text-info me-2"></i>

<strong>Program :</strong>

<?php echo htmlspecialchars($student['program_name']); ?>

</div>

<div class="col-md-6 mb-2">

<i class="fas fa-layer-group text-success me-2"></i>

<strong>Semester :</strong>

Semester <?php echo $student['semester']; ?>

</div>

<div class="col-md-6 mb-2">

<i class="fas fa-clock text-danger me-2"></i>

<strong>Shift :</strong>

<?php echo htmlspecialchars($student['shift']); ?>

</div>

</div>

</div>

<div class="col-lg-4 text-center">

<i class="fas fa-user-graduate"

style="font-size:120px;color:#0B3D91;opacity:.12;"></i>

</div>

</div>

</div>

</div>

<!-- =========================================
LIVE STATISTICS
========================================= -->

<div class="row">

<div class="col-md-3 mb-4">

<div class="card">

<div class="card-body text-center">

<i class="fas fa-book-open text-primary"

style="font-size:45px;"></i>

<h6 class="mt-3">

Subjects

</h6>

<h2 class="fw-bold text-primary">

0

</h2>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card">

<div class="card-body text-center">

<i class="fas fa-calendar-check text-success"

style="font-size:45px;"></i>

<h6 class="mt-3">

Attendance

</h6>

<h2 class="fw-bold text-success">

0%

</h2>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card">

<div class="card-body text-center">

<i class="fas fa-money-check-dollar text-warning"

style="font-size:45px;"></i>

<h6 class="mt-3">

Fee Status

</h6>

<h2 class="fw-bold text-warning">

Paid

</h2>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card">

<div class="card-body text-center">

<i class="fas fa-envelope text-danger"

style="font-size:45px;"></i>

<h6 class="mt-3">

Messages

</h6>

<h2 class="fw-bold text-danger">

0

</h2>

</div>

</div>

</div>

</div>

<!-- =========================================
QUICK ACCESS
========================================= -->

<div class="row">

<div class="col-lg-8">

<div class="card mb-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-bolt me-2"></i>

Quick Access

</h5>

</div>

<div class="card-body">

<div class="row g-3">

<div class="col-md-3">

<a href="profile.php"
class="btn btn-outline-primary w-100 py-3">

<i class="fas fa-user fa-2x mb-2"></i>

<br>

My Profile

</a>

</div>

<div class="col-md-3">

<a href="course_marks.php"
class="btn btn-outline-success w-100 py-3">

<i class="fas fa-book-open fa-2x mb-2"></i>

<br>

Subjects

</a>

</div>

<div class="col-md-3">

<a href="attendance.php"
class="btn btn-outline-warning w-100 py-3">

<i class="fas fa-calendar-check fa-2x mb-2"></i>

<br>

Attendance

</a>

</div>

<div class="col-md-3">

<a href="student_result.php"
class="btn btn-outline-info w-100 py-3">

<i class="fas fa-square-poll-vertical fa-2x mb-2"></i>

<br>

Result

</a>

</div>

<div class="col-md-3">

<a href="fee_status.php"
class="btn btn-outline-danger w-100 py-3">

<i class="fas fa-money-check-dollar fa-2x mb-2"></i>

<br>

Fee Status

</a>

</div>

<div class="col-md-3">

<a href="compose_message.php"
class="btn btn-outline-secondary w-100 py-3">

<i class="fas fa-envelope fa-2x mb-2"></i>

<br>

Messages

</a>

</div>

<div class="col-md-3">

<a href="announcements.php"
class="btn btn-outline-dark w-100 py-3">

<i class="fas fa-bullhorn fa-2x mb-2"></i>

<br>

Announcements

</a>

</div>

<div class="col-md-3">

<a href="logout.php"
class="btn btn-outline-danger w-100 py-3">

<i class="fas fa-right-from-bracket fa-2x mb-2"></i>

<br>

Logout

</a>

</div>

</div>

</div>

</div>

</div>
<!-- =========================================
TODAY'S TIMETABLE
========================================= -->

<?php

/*=========================================
TODAY TIMETABLE
=========================================*/

/*=========================================
TODAY DAY
=========================================*/

date_default_timezone_set("Asia/Karachi");

$todayDay = date("l");

$timetable = $conn->prepare("

SELECT

teacher_timetable.*,
subjects.subject_name,
teachers.teacher_name

FROM teacher_timetable

LEFT JOIN subjects
ON teacher_timetable.subject_id = subjects.id

LEFT JOIN teachers
ON teacher_timetable.teacher_id = teachers.id

WHERE

teacher_timetable.program_id = ?

AND teacher_timetable.semester = ?

AND teacher_timetable.shift = ?

AND teacher_timetable.day_name = ?

AND teacher_timetable.status='Active'

ORDER BY teacher_timetable.start_time ASC

");

$timetable->bind_param(

"iiss",

$student['program_id'],
$student['semester'],
$student['shift'],
$todayDay

);

$timetable->execute();

$timetableResult = $timetable->get_result();

?>

<div class="row">

<div class="col-lg-8">

<div class="card mb-4">

<div class="card-header bg-info text-white">

<h5 class="mb-0">

<i class="fas fa-calendar-days me-2"></i>

Today's Timetable

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-primary">

<tr>

<th width="70">#</th>

<th>Subject</th>

<th>Teacher</th>

<th>Time</th>

<th>Room</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

if($timetableResult->num_rows>0){

$count=1;

while($row=$timetableResult->fetch_assoc()){

?>

<tr>

<td><?php echo $count++; ?></td>

<td><?php echo $row['subject_name']; ?></td>

<td><?php echo $row['teacher_name']; ?></td>

<td>

<?php

echo date("h:i A",strtotime($row['start_time']));

?>

-

<?php

echo date("h:i A",strtotime($row['end_time']));

?>

</td>

<td>

<?php echo $row['room_no']; ?>

</td>

<td>

<?php

$currentTime = date("H:i:s");

if(
$currentTime >= $row['start_time']
&&
$currentTime <= $row['end_time']
){

?>

<span class="badge bg-success">
Running
</span>

<?php

}
elseif($currentTime < $row['start_time']){

?>

<span class="badge bg-primary">
Upcoming
</span>

<?php

}
else{

?>

<span class="badge bg-secondary">
Completed
</span>

<?php

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center py-5">

<i class="fas fa-calendar-xmark"

style="font-size:55px;color:#d0d0d0;"></i>

<h5 class="mt-3">

No Class Scheduled Today

</h5>

<p class="text-muted">

Today's timetable is empty.

</p>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<?php

/*=========================================
LATEST 2 NOTIFICATIONS
=========================================*/

$latestNotifications = $conn->query("

SELECT

title,
notification_type,
created_at

FROM super_admin_notifications

WHERE status='Active'

ORDER BY id DESC

LIMIT 2

");

/*=========================================
RECENT 4 NOTIFICATIONS
=========================================*/

$recentNotifications = $conn->query("

SELECT

id,
title,
notification_type,
created_at

FROM super_admin_notifications

WHERE status='Active'

ORDER BY id DESC

LIMIT 2,4

");

?>

<div class="col-lg-4">

<!-- =========================================
LATEST NOTIFICATIONS
========================================= -->

<div class="card mb-4 border-0 shadow">

<div class="card-header"

style="background:linear-gradient(135deg,#000,#1a1a1a,#FFD54F);color:#fff;">

<div class="d-flex justify-content-between align-items-center">

<h5 class="mb-0">

<i class="fas fa-star text-warning me-2"></i>

Latest Notifications

</h5>

<a

href="announcements.php"

class="btn btn-sm btn-warning fw-bold">

View All

</a>

</div>

</div>

<div class="card-body p-0" style="min-height:250px;">

<?php

if($latestNotifications->num_rows>0){

while($latest=$latestNotifications->fetch_assoc()){

?>

<div class="border-bottom p-3">

<div class="d-flex justify-content-between align-items-start">

<div>

<span class="badge bg-warning text-dark mb-2">

<?php echo htmlspecialchars($latest['notification_type']); ?>

</span>

<h6 class="fw-bold mb-1">

<?php echo htmlspecialchars($latest['title']); ?>

</h6>

<small class="text-muted">

<i class="fas fa-clock me-1"></i>

<?php echo date("d M Y",strtotime($latest['created_at'])); ?>

</small>

</div>

<i class="fas fa-bell text-warning fa-lg"></i>

</div>

</div>

<?php

}

}else{

?>

<div class="text-center py-4">

<i class="fas fa-bell-slash fa-3x text-secondary mb-3"></i>

<h6>No Latest Notifications</h6>

</div>

<?php

}

?>

</div>

</div>

<!-- =========================================
RECENT NOTIFICATIONS
========================================= -->

<div class="card border-0 shadow">

<div class="card-header"

style="background:linear-gradient(135deg,#000,#0B3D91,#FFD54F);color:#fff;">

<h5 class="mb-0">

<i class="fas fa-history me-2 text-warning"></i>

Recent Notifications

</h5>

</div>

<div class="list-group list-group-flush">

<?php

if($recentNotifications->num_rows>0){

while($recent=$recentNotifications->fetch_assoc()){

?>

<a

href="announcement_details.php?id=<?php echo $recent['id']; ?>"

class="list-group-item list-group-item-action border-0 py-3">

<div class="d-flex justify-content-between align-items-start">

<div>

<span class="badge bg-dark text-warning mb-2">

<?php echo htmlspecialchars($recent['notification_type']); ?>

</span>

<div class="fw-bold text-dark">

<?php echo htmlspecialchars($recent['title']); ?>

</div>

<small class="text-muted">

<i class="fas fa-calendar-alt me-1"></i>

<?php echo date("d M Y",strtotime($recent['created_at'])); ?>

</small>

</div>

<?php

if(strtotime($recent['created_at']) >= strtotime("-1 day")){

?>

<span class="badge bg-danger">

NEW

</span>

<?php

}

?>

</div>

</a>

<?php

}

}else{

?>

<div class="text-center py-5">

<i class="fas fa-bell-slash"

style="font-size:55px;color:#d0d0d0;"></i>

<h5 class="mt-3">

No Recent Notifications

</h5>

<p class="text-muted">

Notifications from Super Admin will appear here.

</p>

</div>

<?php

}

?>

</div>

</div>

</div>

<!-- =========================================
ACADEMIC CALENDAR
========================================= -->

<div class="card">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-calendar-week me-2"></i>

Academic Calendar

</h5>

</div>

<div class="card-body">

<ul class="list-group list-group-flush">

<li class="list-group-item">

<i class="fas fa-circle text-success me-2"></i>

Semester Classes

</li>

<li class="list-group-item">

<i class="fas fa-circle text-primary me-2"></i>

Mid Term Examination

</li>

<li class="list-group-item">

<i class="fas fa-circle text-warning me-2"></i>

Final Examination

</li>

<li class="list-group-item">

<i class="fas fa-circle text-danger me-2"></i>

Result Declaration

</li>

</ul>

</div>

</div>

</div>

</div>

<!-- =========================================
FOOTER
========================================= -->

<footer class="mt-5">

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-6">

<div class="d-flex align-items-center">

<img
src="../assets/images/logo.png"
style="height:55px;"
class="me-3">

<div>

<h5 class="mb-1 text-primary">

Global Tech University ERP

</h5>

<p class="mb-0 text-muted">

Student Portal Dashboard

</p>

</div>

</div>

</div>

<div class="col-md-6 text-md-end mt-3 mt-md-0">

<p class="mb-1">

<strong>

Version 1.0

</strong>

</p>

<p class="mb-1 text-muted">

Developed for

Global Tech University

</p>

<p class="mb-0 text-secondary">

© <?php echo date("Y"); ?>

All Rights Reserved.

</p>

</div>

</div>

</div>

</div>

</footer>

</div>

<!-- CONTENT END -->

<?php

if(isset($popupData)){

?>

<div class="modal fade"

id="resultNotification"

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

style="font-size:75px;color:#0B5ED7;"></i>

<br><br>

<h4>Congratulations!</h4>

<p>

<?php echo nl2br(htmlspecialchars($popupData['message'])); ?>

</p>

</div>

<div class="modal-footer">

<a

href="read_notification.php?id=<?php echo $popupData['id']; ?>"

class="btn btn-success w-100">

<i class="fas fa-check"></i>

View Result

</a>

</div>

</div>

</div>

</div>

<script>

window.addEventListener('load',function(){

var myModal=new bootstrap.Modal(

document.getElementById('resultNotification')

);

myModal.show();

});

</script>

<?php

}

?>

</div>

<!-- MAIN END -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/*=========================================
LIVE CLOCK
=========================================*/

function updateClock(){

let now = new Date();

let options = {

hour:'2-digit',
minute:'2-digit',
second:'2-digit'

};

let time = now.toLocaleTimeString('en-US',options);

let clock = document.getElementById("liveClock");

if(clock){

clock.innerHTML = time;

}

}

setInterval(updateClock,1000);

updateClock();

</script>

<?php if($popupData){ ?>

<div class="modal fade" id="resultNotification" tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">

<div class="modal-header bg-success text-white">

<h5 class="modal-title">
<?php echo htmlspecialchars($popupData['title']); ?>
</h5>

</div>

<div class="modal-body text-center">

<p>
<?php echo nl2br(htmlspecialchars($popupData['message'])); ?>
</p>

</div>

<div class="modal-footer">

<a href="read_notification.php?id=<?php echo $popupData['id']; ?>" class="btn btn-success w-100">

OK

</a>

</div>

</div>

</div>

</div>

<script>
window.onload=function(){
var modal=new bootstrap.Modal(document.getElementById('resultNotification'));
modal.show();
};
</script>

<?php } ?>

</body>

</html>