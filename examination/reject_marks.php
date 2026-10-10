<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
EXAMINATION LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location:login.php");
    exit;

}

/*=========================================
CHECK REQUEST
=========================================*/

if(

!isset($_GET['id']) ||

!isset($_GET['session_id'])

){

    die("Invalid Request");

}

$marks_id=(int)$_GET['id'];

$session_id=(int)$_GET['session_id'];

/*=========================================
UPDATE MARK STATUS
=========================================*/

$update=$conn->prepare("

UPDATE marks_details

SET remarks='Rejected'

WHERE id=?

");

$update->bind_param(

"i",

$marks_id

);

$update->execute();

/*=========================================
GET TEACHER INFORMATION
=========================================*/

$get=$conn->prepare("

SELECT

mark_sessions.teacher_id,

subjects.subject_name

FROM mark_sessions

INNER JOIN subjects
ON subjects.id=mark_sessions.subject_id

INNER JOIN marks_details
ON marks_details.mark_session_id=mark_sessions.id

WHERE marks_details.id=?

LIMIT 1

");

$get->bind_param(

"i",

$marks_id

);

$get->execute();

$data=$get->get_result()->fetch_assoc();

/*=========================================
SEND REJECTION NOTIFICATION TO TEACHER
=========================================*/

if($data){

$title="Marks Rejected";

$message="Examination Office has rejected your marks for subject ".$data['subject_name'].". Please update the marks and submit again.";

$type="danger";

$notify=$conn->prepare("

INSERT INTO teacher_notifications
(
teacher_id,
title,
message,
type
)

VALUES
(
?,?,?,?
)

");

$notify->bind_param(

"isss",

$data['teacher_id'],

$title,

$message,

$type

);

$notify->execute();

}

/*=========================================
SUCCESS MESSAGE
=========================================*/

$_SESSION['success']="Marks Rejected Successfully.";

/*=========================================
REDIRECT BACK
=========================================*/

header("Location:class_marks.php?session_id=".$session_id);

exit;

?>