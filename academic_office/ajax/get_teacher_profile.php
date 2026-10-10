<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['teacher_id']) || empty($_POST['teacher_id'])) {

    echo json_encode([
        "status" => "error",
        "message" => "Teacher ID not found"
    ]);

    exit;
}

$teacher_id = (int)$_POST['teacher_id'];

$sql = "

SELECT

    teachers.*,
    faculties.faculty_name

FROM teachers

LEFT JOIN faculties
ON faculties.id = teachers.faculty_id

WHERE teachers.id = ?

LIMIT 1

";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $teacher_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    echo json_encode([
        "status" => "error",
        "message" => "Teacher not found"
    ]);

    exit;
}

$teacher = mysqli_fetch_assoc($result);

echo json_encode([
    "status" => "success",
    "data" => $teacher
]);

exit;
?>