<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['student_id'])){

    header("Location:login.php");
    exit;

}

$student_id=$_SESSION['student_id'];

/*=========================================
CHECK NOTIFICATION ID
=========================================*/

if(!isset($_GET['id'])){

    header("Location:dashboard.php");
    exit;

}

$id=(int)$_GET['id'];

/*=========================================
MARK AS READ
=========================================*/

$update=$conn->prepare("

UPDATE notifications

SET is_read=1

WHERE

id=?

AND student_id=?

");

$update->bind_param(

"ii",

$id,

$student_id

);

$update->execute();

/*=========================================
BACK TO DASHBOARD
=========================================*/

$get = $conn->prepare("

SELECT category

FROM notifications

WHERE id=?

LIMIT 1

");

$get->bind_param("i",$id);

$get->execute();

$data = $get->get_result()->fetch_assoc();

if($data['category']=="Result"){

    header("Location:student_result.php");

    exit;

}

header("Location:dashboard.php");

exit;

?>