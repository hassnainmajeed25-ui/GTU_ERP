<?php

require_once '../../config/config.php';
require_once '../../config/database.php';


header("Content-Type: application/json");



$sql = "

SELECT

p.id,
p.program_code,
p.program_name,
p.duration,
p.status,

f.faculty_name,


(
    SELECT COUNT(*)
    FROM subjects s
    WHERE s.program_id = p.id
) AS total_subjects,


(
    SELECT COUNT(DISTINCT ts.teacher_id)

    FROM teacher_subjects ts

    INNER JOIN subjects s2

    ON ts.subject_id = s2.id

    WHERE s2.program_id = p.id

    AND ts.status='Active'

) AS total_teachers


FROM programs p


INNER JOIN faculties f

ON p.faculty_id = f.id


ORDER BY p.program_name ASC


";


$result = mysqli_query($conn,$sql);


$data=[];


if($result){


    while($row=mysqli_fetch_assoc($result)){


        $data[]=$row;


    }


}



echo json_encode([

    "status"=>"success",

    "data"=>$data

]);


exit;


?>