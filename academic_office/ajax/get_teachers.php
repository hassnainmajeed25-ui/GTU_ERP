<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

/*=========================================
CHECK REQUEST
=========================================*/

if(
    !isset($_POST['faculty_id']) ||
    !isset($_POST['program_id']) ||
    !isset($_POST['semester']) ||
    !isset($_POST['shift']) ||
    !isset($_POST['subject_id'])
){

    echo json_encode([
        "status" => "error",
        "message" => "Required Data Missing"
    ]);

    exit;
}

$faculty_id = (int)$_POST['faculty_id'];
$program_id = (int)$_POST['program_id'];
$semester   = (int)$_POST['semester'];
$subject_id = (int)$_POST['subject_id'];
$shift      = $_POST['shift'];


/*=========================================
LOAD ASSIGNED TEACHER
=========================================*/

$sql = "

SELECT DISTINCT

    t.id,
    t.employee_id,
    t.teacher_name,
    t.designation,
    t.qualification,
    t.shift

FROM teacher_subjects ts

INNER JOIN teachers t
    ON t.id = ts.teacher_id

WHERE ts.subject_id = ?
AND ts.program_id = ?
AND ts.semester = ?
AND ts.shift = ?

AND t.faculty_id = ?
AND t.shift = ?
AND t.status = 'Active'

AND ts.status = 'Active'

ORDER BY t.teacher_name ASC

";


$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){

    echo json_encode([
        "status" => "error",
        "message" => "Database Query Failed"
    ]);

    exit;
}


mysqli_stmt_bind_param(
    $stmt,
    "iiisis",
    $subject_id,
    $program_id,
    $semester,
    $shift,
    $faculty_id,
    $shift
);


mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$teachers = [];


while($row = mysqli_fetch_assoc($result)){

    $teachers[] = [

        "id" => $row['id'],

        "employee_id" => $row['employee_id'],

        "teacher_name" => $row['teacher_name'],

        "designation" => $row['designation'],

        "qualification" => $row['qualification'],

        "shift" => $row['shift']

    ];

}


if(count($teachers) > 0){

    echo json_encode([
        "status" => "success",
        "data" => $teachers
    ]);

}else{

    echo json_encode([
        "status" => "empty",
        "message" => "No teacher assigned to this subject.",
        "data" => []
    ]);

}

exit;

?>