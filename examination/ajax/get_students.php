<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

/*=========================================
CHECK REQUEST
=========================================*/

if(

!isset($_POST['program_id']) ||

!isset($_POST['semester']) ||

!isset($_POST['shift']) ||

!isset($_POST['subject_id'])

){

    echo json_encode([

        "status"=>"error",

        "message"=>"Missing Data"

    ]);

    exit;

}

$program_id=(int)$_POST['program_id'];

$semester=(int)$_POST['semester'];

$shift=trim($_POST['shift']);

$subject_id=(int)$_POST['subject_id'];

/*=========================================
LOAD STUDENTS MARKS
=========================================*/

$sql="

SELECT

students.id,
students.roll_no,
students.registration_no,
students.first_name,
students.last_name,

mark_sessions.id AS session_id,
mark_sessions.status,
mark_sessions.result_status,

marks_details.quiz_marks,
marks_details.assignment_marks,
marks_details.presentation_marks,
marks_details.practical_marks,
marks_details.mid_marks,
marks_details.final_marks,
marks_details.obtained_marks

FROM marks_details

INNER JOIN mark_sessions
ON mark_sessions.id=marks_details.mark_session_id

INNER JOIN students
ON students.id=marks_details.student_id

WHERE

students.program_id=?

AND students.semester=?

AND students.shift=?

AND mark_sessions.subject_id=?

ORDER BY students.roll_no ASC

";

$stmt=$conn->prepare($sql);

$stmt->bind_param(

"iisi",

$program_id,

$semester,

$shift,

$subject_id

);

$stmt->execute();

$result=$stmt->get_result();

$data=[];

while($row=$result->fetch_assoc()){

    $data[]=$row;

}

echo json_encode([

"status"=>"success",

"data"=>$data

]);

exit;

?>