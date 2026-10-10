<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit;
}

$student_id=$_SESSION['student_id'];

/*=========================================
GET STUDENT
=========================================*/

$student=$conn->prepare("

SELECT

program_id,
semester,
shift

FROM students

WHERE id=?

LIMIT 1

");

$student->bind_param("i",$student_id);

$student->execute();

$studentData=$student->get_result()->fetch_assoc();

/*=========================================
GET WEEKLY TIMETABLE
=========================================*/

$timetable = $conn->prepare("

SELECT

teacher_timetable.*,

subjects.subject_name,

subjects.subject_code,

teachers.teacher_name

FROM teacher_timetable

LEFT JOIN subjects
ON teacher_timetable.subject_id = subjects.id

LEFT JOIN teachers
ON teacher_timetable.teacher_id = teachers.id

WHERE

teacher_timetable.program_id=?

AND

teacher_timetable.semester=?

AND

teacher_timetable.shift=?

AND

teacher_timetable.status='Active'

ORDER BY

FIELD(

teacher_timetable.day_name,

'Monday',

'Tuesday',

'Wednesday',

'Thursday',

'Friday',

'Saturday'

),

teacher_timetable.start_time ASC

");

$timetable->bind_param(

"iis",

$studentData['program_id'],

$studentData['semester'],

$studentData['shift']

);

$timetable->execute();

$timetableResult = $timetable->get_result();

/*=========================================
MAKE TIMETABLE ARRAY DAY WISE
=========================================*/

$weekTimetable = [];

$days = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday"
];

foreach($days as $day){
    if(!isset($weekTimetable[$day])){
        $weekTimetable[$day] = [];
    }
}

while($row = $timetableResult->fetch_assoc()){

    $weekTimetable[$row['day_name']][] = $row;

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Weekly Timetable</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#edf3ff;
    font-family:'Segoe UI',sans-serif;
}

/*==========================
PAGE TITLE
==========================*/

.page-title{
    background:linear-gradient(135deg,#0B3D91,#1E88E5);
    color:#fff;
    padding:20px 25px;
    border-radius:15px;
    margin-bottom:30px;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.page-title h3{
    margin:0;
    font-weight:700;
}

.back-btn{
    background:#FFD54F;
    color:#000;
    border:none;
    border-radius:10px;
    font-weight:600;
    padding:10px 18px;
}

.back-btn:hover{
    background:#FFC107;
    color:#000;
}

/*==========================
DAY CARD
==========================*/

.day-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    margin-bottom:30px;
    box-shadow:0 8px 25px rgba(0,0,0,.10);
}

.day-header{
    background:linear-gradient(90deg,#0B3D91,#1976D2);
    color:#fff;
    font-size:22px;
    font-weight:bold;
    padding:16px 25px;
}

/*==========================
LECTURE CARD
==========================*/

.lecture-card{

    margin:18px;

    padding:18px;

    background:#ffffff;

    border-left:6px solid #0B3D91;

    border-radius:14px;

    box-shadow:0 4px 15px rgba(0,0,0,.08);

    transition:.3s;

}

.lecture-card:hover{

    transform:translateY(-4px);

    box-shadow:0 10px 22px rgba(0,0,0,.15);

}

/*==========================
SUBJECT
==========================*/

.subject-name{

    color:#0B3D91;

    font-weight:700;

    margin:0;

    font-size:20px;

}

/*==========================
TEACHER
==========================*/

.teacher-name{

    color:#198754;

    font-weight:600;

    font-size:17px;

}

/*==========================
TIME
==========================*/

.lecture-time{

    color:#D32F2F;

    font-size:17px;

    font-weight:bold;

}

/*==========================
ROOM BADGE
==========================*/

.room-badge{

    display:inline-block;

    background:#FFD54F;

    color:#000;

    padding:8px 18px;

    border-radius:30px;

    font-weight:bold;

    font-size:15px;

}

/*==========================
NO CLASS
==========================*/

.no-class-box{

    text-align:center;

    padding:45px;

    color:#888;

}

.no-class-box i{

    font-size:55px;

    color:#c8c8c8;

    margin-bottom:15px;

}

.no-class-box h5{

    font-weight:bold;

}

/*==========================
RESPONSIVE
==========================*/

@media(max-width:768px){

.subject-name{

font-size:18px;

margin-bottom:10px;

}

.teacher-name{

margin-bottom:10px;

}

.lecture-time{

margin-bottom:10px;

}

.room-badge{

margin-top:10px;

}

.day-header{

font-size:20px;

}

}

</style>

</head>

<body>

<div class="container py-4">

<div class="page-title d-flex justify-content-between align-items-center">

<h3>

<i class="fas fa-calendar-week me-2"></i>

Weekly Timetable

</h3>

<a href="dashboard.php" class="btn back-btn">

<i class="fas fa-arrow-left me-2"></i>

Dashboard

</a>

</div>

<?php foreach($days as $day){ ?>

<div class="day-card">

    <div class="day-header">

        <i class="fas fa-calendar-alt me-2"></i>

        <?php echo $day; ?>

    </div>

    <?php

    if(count($weekTimetable[$day])>0){

        foreach($weekTimetable[$day] as $row){

    ?>

    <div class="lecture-card">

        <div class="row align-items-center">

            <div class="col-lg-4">

                <div class="subject-name">

                    <i class="fas fa-book-open me-2"></i>

                    <?php echo htmlspecialchars($row['subject_name']); ?>

                </div>

                <small class="text-muted">

                    <?php echo htmlspecialchars($row['subject_code']); ?>

                </small>

            </div>

            <div class="col-lg-3">

                <div class="teacher-name">

                    <i class="fas fa-user-tie me-2"></i>

                    <?php echo htmlspecialchars($row['teacher_name']); ?>

                </div>

            </div>

            <div class="col-lg-3">

                <div class="lecture-time">

                    <i class="fas fa-clock me-2"></i>

                    <?php echo date("h:i A",strtotime($row['start_time'])); ?>

                    -

                    <?php echo date("h:i A",strtotime($row['end_time'])); ?>

                </div>

            </div>

            <div class="col-lg-2 text-center">

                <span class="room-badge">

                    <i class="fas fa-door-open me-1"></i>

                    Room <?php echo htmlspecialchars($row['room_no']); ?>

                </span>

            </div>

        </div>

    </div>

    <?php

        }

    }else{

    ?>

    <div class="no-class-box">

        <i class="fas fa-calendar-times"></i>

        <h4 class="mt-3">

            No Class Scheduled

        </h4>

        <p>

            There is no lecture scheduled on

            <strong><?php echo $day; ?></strong>.

        </p>

    </div>

    <?php } ?>

</div>

<?php } ?>

<div class="text-center mt-4">

<a href="dashboard.php" class="btn btn-primary">

<i class="fas fa-house me-2"></i>

Dashboard

</a>

</div>

</div>

</body>

</html>