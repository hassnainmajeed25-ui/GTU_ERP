<?php

require_once '../../config/config.php';
require_once '../../config/database.php';

header("Content-Type: application/json");

if(!isset($_POST['id'])){

    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid Request"
    ]);

    exit;

}

$id=(int)$_POST['id'];

$stmt=$conn->prepare("DELETE FROM teacher_timetable WHERE id=?");
$stmt->bind_param("i",$id);

if($stmt->execute()){

    echo json_encode([
        "status"=>"success",
        "message"=>"Timetable Deleted Successfully"
    ]);

}else{

    echo json_encode([
        "status"=>"error",
        "message"=>$stmt->error
    ]);

}

exit;

?>