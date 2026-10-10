<?php

require_once '../config/config.php';
require_once '../config/database.php';

header("Content-Type: application/json");


/*=========================================
CHECK REQUEST
=========================================*/

if(

!isset($_POST['teacher_id']) ||
!isset($_POST['subject_id']) ||
!isset($_POST['program_id']) ||
!isset($_POST['semester']) ||
!isset($_POST['shift']) ||
!isset($_POST['day_name']) ||
!isset($_POST['start_time']) ||
!isset($_POST['end_time']) ||
!isset($_POST['room_no'])

){

echo json_encode([

"status"=>"error",

"message"=>"Required Data Missing"

]);

exit;

}



$teacher_id = (int)$_POST['teacher_id'];

$subject_id = (int)$_POST['subject_id'];

$program_id = (int)$_POST['program_id'];

$semester = (int)$_POST['semester'];

$shift = $_POST['shift'];

$day_name = $_POST['day_name'];

$start_time = $_POST['start_time'];

$end_time = $_POST['end_time'];

$room_no = $_POST['room_no'];

$status = $_POST['status'] ?? "Active";



/*=========================================
CHECK DUPLICATE
=========================================*/


$check=$conn->prepare("

SELECT id

FROM teacher_timetable

WHERE teacher_id=?

AND day_name=?

AND start_time=?

AND end_time=?

AND room_no=?

AND status='Active'

LIMIT 1

");


$check->bind_param(

"issss",

$teacher_id,

$day_name,

$start_time,

$end_time,

$room_no

);


$check->execute();


$result=$check->get_result();


if($result->num_rows>0){


echo json_encode([

"status"=>"error",

"message"=>"This timetable already exists."

]);


exit;


}




/*=========================================
INSERT TIMETABLE
=========================================*/


$stmt=$conn->prepare("

INSERT INTO teacher_timetable

(

teacher_id,

subject_id,

program_id,

semester,

shift,

day_name,

start_time,

end_time,

room_no,

status

)

VALUES

(?,?,?,?,?,?,?,?,?,?)

");


$stmt->bind_param(

"iiiissssss",

$teacher_id,

$subject_id,

$program_id,

$semester,

$shift,

$day_name,

$start_time,

$end_time,

$room_no,

$status

);



if($stmt->execute()){


echo json_encode([

"status"=>"success",

"message"=>"Timetable Saved Successfully"

]);


}else{


echo json_encode([

"status"=>"error",

"message"=>$stmt->error

]);


}



exit;


?>