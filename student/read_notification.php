<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit;
}

$notification_id = intval($_GET['id']);

$update = $conn->prepare("

UPDATE notifications

SET is_read=1

WHERE id=?

AND student_id=?

");

$update->bind_param(

"ii",

$notification_id,

$student_id

);

$update->execute();

/*=========================================
CHECK NOTIFICATION TYPE
=========================================*/

$get = $conn->prepare("

SELECT title

FROM notifications

WHERE id=?

LIMIT 1

");

$get->bind_param("i",$notification_id);

$get->execute();

$data = $get->get_result()->fetch_assoc();

if($data){

    if(stripos($data['title'],'Result')!==false){

        header("Location: ../student_portal/student_result.php");
        exit;

    }

}

header("Location: dashboard.php");
exit;

?>