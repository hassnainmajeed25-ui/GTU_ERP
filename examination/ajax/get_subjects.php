<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

/*=========================================
CHECK REQUEST
=========================================*/

if(

!isset($_POST['program_id']) ||

!isset($_POST['semester'])

){

    echo json_encode([

        "status"=>"error",

        "message"=>"Missing Data"

    ]);

    exit;

}

$program_id=(int)$_POST['program_id'];

$semester=(int)$_POST['semester'];

/*=========================================
LOAD SUBJECTS
=========================================*/

$sql="

SELECT

id,

subject_code,

subject_name,

credit_hours,

teacher_id

FROM subjects

WHERE

program_id=?

AND semester=?

AND status='Active'

ORDER BY subject_code ASC

";

$stmt=$conn->prepare($sql);

$stmt->bind_param(

"ii",

$program_id,

$semester

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