<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

/*=========================================
CHECK IMAGE
=========================================*/

if(!isset($_FILES['profile_photo'])){
    header("Location: profile.php");
    exit;
}

$file = $_FILES['profile_photo'];

if($file['error'] != 0){
    header("Location: profile.php?error=1");
    exit;
}

/*=========================================
IMAGE TYPE
=========================================*/

$allowed = array(
    "jpg",
    "jpeg",
    "png",
    "webp"
);

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if(!in_array($extension,$allowed)){
    header("Location: profile.php?type=invalid");
    exit;
}

/*=========================================
IMAGE SIZE
=========================================*/

if($file['size'] > 2*1024*1024){

    header("Location: profile.php?size=large");
    exit;

}

/*=========================================
UPLOAD FOLDER
=========================================*/

$upload_folder = "uploads/profile/";

if(!is_dir($upload_folder)){
    mkdir($upload_folder,0777,true);
}

/*=========================================
OLD IMAGE
=========================================*/

$get = $conn->prepare("SELECT profile_photo FROM students WHERE id=?");

$get->bind_param("i",$student_id);

$get->execute();

$result = $get->get_result();

$row = $result->fetch_assoc();

if(!empty($row['profile_photo'])){

    $old = $upload_folder.$row['profile_photo'];

    if(file_exists($old)){
        unlink($old);
    }

}

/*=========================================
NEW FILE NAME
=========================================*/

$new_name = "student_".$student_id."_".time().".".$extension;

$destination = $upload_folder.$new_name;

/*=========================================
MOVE FILE
=========================================*/

if(move_uploaded_file($file['tmp_name'],$destination)){

    $update = $conn->prepare("

        UPDATE students

        SET profile_photo=?

        WHERE id=?

    ");

    $update->bind_param("si",$new_name,$student_id);

    $update->execute();

    header("Location: profile.php?upload=success");
    exit;

}else{

    header("Location: profile.php?upload=failed");
    exit;

}

?>