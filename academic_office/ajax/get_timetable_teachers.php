<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

if(
    !isset($_POST['faculty_id']) ||
    !isset($_POST['shift'])
){

    echo json_encode([
        "status"=>"error",
        "message"=>"Required Data Missing"
    ]);

    exit;

}

$faculty_id=(int)$_POST['faculty_id'];
$shift=$_POST['shift'];

$sql="

SELECT

id,
employee_id,
teacher_name,
designation

FROM teachers

WHERE faculty_id=?
AND shift=?
AND status='Active'

ORDER BY teacher_name ASC

";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "is",
    $faculty_id,
    $shift
);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

$data=[];

while($row=mysqli_fetch_assoc($result)){

    $data[]=$row;

}

echo json_encode([

    "status"=>"success",

    "data"=>$data

]);

exit;

?>