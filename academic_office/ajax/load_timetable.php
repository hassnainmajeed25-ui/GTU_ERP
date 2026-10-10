<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

$sql = "

SELECT

tt.id,
tt.semester,
tt.shift,
tt.day_name,
tt.start_time,
tt.end_time,
tt.room_no,
tt.status,

t.teacher_name,

s.subject_name,

p.program_code

FROM teacher_timetable tt

INNER JOIN teachers t
ON tt.teacher_id = t.id

INNER JOIN subjects s
ON tt.subject_id = s.id

INNER JOIN programs p
ON tt.program_id = p.id

ORDER BY tt.id DESC

";

$result = mysqli_query($conn,$sql);

$data = [];

if($result){

    while($row = mysqli_fetch_assoc($result)){

        $data[] = $row;

    }

}

echo json_encode([

    "status"=>"success",

    "data"=>$data

]);

exit;

?>