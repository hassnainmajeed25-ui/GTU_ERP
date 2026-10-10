<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location: ../admin/login.php");
    exit;

}

/*=========================================
UPDATE TIMETABLE
=========================================*/

if(isset($_POST['id'])){

    $id         = intval($_POST['id']);
    $teacher_id = intval($_POST['teacher_id']);
    $subject_id = intval($_POST['subject_id']);
    $program_id = intval($_POST['program_id']);
    $semester   = intval($_POST['semester']);
    $shift = trim($_POST['shift']);

    $day_name   = trim($_POST['day_name']);

    $start_time = trim($_POST['start_time']);

    $end_time   = trim($_POST['end_time']);

    $room_no    = trim($_POST['room_no']);

    $status     = trim($_POST['status']);

    /*=========================================
    CHECK DUPLICATE
    =========================================*/

    $check = $conn->prepare("

    SELECT id

    FROM teacher_timetable

    WHERE teacher_id=?

    AND day_name=?

    AND start_time=?

    AND end_time=?

    AND id<>?

    LIMIT 1

    ");

    $check->bind_param(

    "isssi",

    $teacher_id,
    $day_name,
    $start_time,
    $end_time,
    $id

    );

    $check->execute();

    $result = $check->get_result();

    if($result->num_rows>0){

        $_SESSION['error']="This timetable already exists.";

        header("Location: edit_timetable.php?id=".$id);

        exit;

    }

        /*=========================================
    UPDATE TIMETABLE
    =========================================*/

    $stmt = $conn->prepare("

   UPDATE teacher_timetable

SET

teacher_id=?,
subject_id=?,
program_id=?,
semester=?,
shift=?,
day_name=?,
start_time=?,
end_time=?,
room_no=?,
status=?

WHERE id=?

    ");

   $stmt->bind_param(

"iiiissssssi",

$teacher_id,
$subject_id,
$program_id,
$semester,
$shift,
$day_name,
$start_time,
$end_time,
$room_no,
$status,
$id

);
    if($stmt->execute()){

        $_SESSION['success']="Teacher Timetable Updated Successfully.";

    }else{

        $_SESSION['error']="Unable to Update Timetable.";

    }

    header("Location: manage_timetable.php");

    exit;

}else{

    header("Location: manage_timetable.php");

    exit;

}

?>