<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

if(!isset($_POST['teacher_id'])){

    echo json_encode([
        "status"=>"error",
        "message"=>"Teacher not selected"
    ]);

    exit;
}

$teacher_id = (int)$_POST['teacher_id'];

$sql = "

SELECT

ts.id,
ts.assigned_at,
ts.status,
ts.semester,
ts.shift,

s.subject_code,
s.subject_name,
s.credit_hours,

p.program_code,
p.program_name

FROM teacher_subjects ts

INNER JOIN subjects s
ON ts.subject_id = s.id

INNER JOIN programs p
ON ts.program_id = p.id

WHERE ts.teacher_id = ?

ORDER BY ts.assigned_at DESC

";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $teacher_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = [];

while($row = mysqli_fetch_assoc($result)){

    $data[] = $row;

}

echo json_encode([

    "status"=>"success",

    "data"=>$data

]);

exit;

?>