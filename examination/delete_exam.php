<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*====================================
LOGIN SECURITY
====================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Examination Office"){
    header("Location: ../admins/login.php");
    exit;
}

/*====================================
CHECK ID
====================================*/

if(!isset($_GET['id'])){

header("Location: manage_exams.php");

exit;

}

$id=(int)$_GET['id'];

/*====================================
CHECK RECORD
====================================*/

$check=$conn->prepare("

SELECT id

FROM exams

WHERE id=?

LIMIT 1

");

$check->bind_param("i",$id);

$check->execute();

$result=$check->get_result();

if($result->num_rows==0){

header("Location: manage_exams.php");

exit;

}

/*====================================
DELETE RECORD
====================================*/

$stmt=$conn->prepare("

DELETE FROM exams

WHERE id=?

");

$stmt->bind_param("i",$id);

if($stmt->execute()){

header("Location: manage_exams.php?deleted=1");

exit;

}else{

header("Location: manage_exams.php?error=1");

exit;

}

?>