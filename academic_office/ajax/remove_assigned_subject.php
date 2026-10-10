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

$id = (int)$_POST['id'];

$sql = "DELETE FROM teacher_subjects WHERE id=?";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt,"i",$id);

if(mysqli_stmt_execute($stmt)){

    echo json_encode([
        "status"=>"success",
        "message"=>"Assigned Subject Removed Successfully."
    ]);

}else{

    echo json_encode([
        "status"=>"error",
        "message"=>"Unable to Remove Subject."
    ]);

}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>