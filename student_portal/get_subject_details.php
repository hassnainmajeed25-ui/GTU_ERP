<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_GET['id'])){

    echo json_encode([
        "credit_hours"=>0
    ]);

    exit;

}

$subject_id=(int)$_GET['id'];

$query=$conn->prepare("

SELECT

credit_hours

FROM subjects

WHERE id=?

LIMIT 1

");

$query->bind_param("i",$subject_id);

$query->execute();

$result=$query->get_result();

if($result->num_rows>0){

    $subject=$result->fetch_assoc();

    echo json_encode([

        "credit_hours"=>$subject['credit_hours']

    ]);

}else{

    echo json_encode([

        "credit_hours"=>0

    ]);

}

?>

