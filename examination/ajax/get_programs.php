<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

/*=========================================
CHECK REQUEST
=========================================*/

if(!isset($_POST['faculty_id'])){

    echo json_encode([
        "status"=>"error",
        "message"=>"Faculty Missing"
    ]);

    exit;

}

$faculty_id = (int)$_POST['faculty_id'];

/*=========================================
LOAD PROGRAMS
=========================================*/

$sql="

SELECT

id,
program_name

FROM programs

WHERE faculty_id=?
AND status='Active'

ORDER BY program_name ASC

";

$stmt=$conn->prepare($sql);

$stmt->bind_param(

"i",

$faculty_id

);

$stmt->execute();

$result=$stmt->get_result();

$data=[];

while($row=$result->fetch_assoc()){

    $data[]=$row;

}

/*=========================================
RETURN JSON
=========================================*/

echo json_encode([

"status"=>"success",

"data"=>$data

]);

exit;

?>