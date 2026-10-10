<?php

require_once '../../config/config.php';
require_once '../../config/database.php';


header("Content-Type: application/json");



$sql="

SELECT

t.id,
t.employee_id,
t.teacher_name,
t.designation,
t.profile_photo,
t.status,

f.faculty_name,


GROUP_CONCAT(DISTINCT p.program_code 
ORDER BY p.program_code SEPARATOR ', ') 
AS programs,


GROUP_CONCAT(DISTINCT s.subject_code 
ORDER BY s.subject_code SEPARATOR ', ') 
AS subjects,


COUNT(DISTINCT ts.subject_id) AS total_subjects


FROM teachers t



LEFT JOIN faculties f

ON t.faculty_id=f.id



LEFT JOIN teacher_subjects ts

ON t.id=ts.teacher_id



LEFT JOIN subjects s

ON ts.subject_id=s.id



LEFT JOIN programs p

ON s.program_id=p.id



GROUP BY t.id



ORDER BY t.teacher_name ASC



";



$result=mysqli_query($conn,$sql);



if(!$result){


echo json_encode([

"status"=>"error",

"message"=>mysqli_error($conn)

]);


exit;


}



$data=[];



while($row=mysqli_fetch_assoc($result)){



if($row['profile_photo']!="" && $row['profile_photo']!=NULL){


$row['photo']="

../uploads/teachers/".$row['profile_photo'];


}else{


$row['photo']="

../assets/images/default_teacher.png";


}



if($row['programs']==NULL){


$row['programs']="No Program Assigned";


}



if($row['subjects']==NULL){


$row['subjects']="No Subject Assigned";


}



$data[]=$row;


}



echo json_encode([


"status"=>"success",

"data"=>$data


]);


exit;


?>