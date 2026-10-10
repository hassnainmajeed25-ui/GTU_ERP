<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){

    header("Location:login.php");
    exit;

}

/*=========================================
CHECK ID
=========================================*/

if(!isset($_GET['id'])){

    die("Invalid Request");

}

$id=(int)$_GET['id'];

/*=========================================
GET SESSION ID
=========================================*/

$get=$conn->prepare("

SELECT

mark_session_id

FROM marks_details

WHERE id=?

LIMIT 1

");

$get->bind_param("i",$id);

$get->execute();

$data=$get->get_result()->fetch_assoc();

if(!$data){

    die("Record Not Found");

}

$session_id=$data['mark_session_id'];

/*=========================================
VERIFY STUDENT
=========================================*/

$verify=$conn->prepare("

UPDATE marks_details

SET remarks='Verified'

WHERE id=?

");

$verify->bind_param("i",$id);
$verify->execute();

/*=========================================
SUCCESS MESSAGE
=========================================*/

$_SESSION['success']="Student Marks Verified Successfully.";

/*=========================================
REDIRECT
=========================================*/

header("Location:verify_marks.php?session_id=".$session_id);

exit;

?>