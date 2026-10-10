<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

if(!isset($_POST['faculty_id'])){

    echo json_encode([
        "status"=>"error",
        "message"=>"Faculty not selected"
    ]);

    exit;
}

$faculty_id = (int)$_POST['faculty_id'];

$sql = "

SELECT

id,
program_name,
program_code

FROM programs

WHERE faculty_id=?
AND status='Active'

ORDER BY program_name ASC

";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $faculty_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = [];

while($row = mysqli_fetch_assoc($result)){

    $data[] = [

        "id"=>$row['id'],
        "name"=>$row['program_name'],
        "code"=>$row['program_code']

    ];

}

echo json_encode([

    "status"=>"success",

    "data"=>$data

]);

exit;

?>