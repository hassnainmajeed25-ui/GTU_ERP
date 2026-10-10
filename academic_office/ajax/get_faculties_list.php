<?php

require_once '../../config/config.php';
require_once '../../config/database.php';


header("Content-Type: application/json");


$sql="
SELECT 
id,
faculty_name

FROM faculties

WHERE status='Active'

ORDER BY faculty_name ASC
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


exit;

?>