<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

$sql="

SELECT

id,
employee_id,
teacher_name,
designation

FROM teachers

WHERE status='Active'

ORDER BY teacher_name ASC

";

$result=mysqli_query($conn,$sql);

$data=[];

while($row=mysqli_fetch_assoc($result)){

    $data[]=$row;

}

echo json_encode([

"status"=>"success",

"data"=>$data

]);

?>