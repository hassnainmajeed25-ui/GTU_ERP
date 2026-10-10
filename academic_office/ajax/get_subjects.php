<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

if(
    !isset($_POST['program_id']) ||
    !isset($_POST['semester'])
){

    echo json_encode([
        "status"=>"error",
        "message"=>"Required Data Missing"
    ]);

    exit;
}

$program_id = (int)$_POST['program_id'];
$semester   = (int)$_POST['semester'];

$sql="

SELECT

id,
subject_code,
subject_name,
credit_hours

FROM subjects

WHERE program_id=?
AND semester=?
AND status='Active'

ORDER BY subject_code ASC

";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $program_id,
    $semester
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