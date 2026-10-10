<?php

require_once '../config/config.php';
require_once '../config/database.php';

/*=========================================
LOGIN SECURITY
=========================================*/

if(!isset($_SESSION['admin_id'])){
    header("Location: ../admins/login.php");
    exit;
}

if($_SESSION['department']!="Admission Office"){
    header("Location: ../admins/login.php");
    exit;
}

/*=========================================
CHECK ID
=========================================*/

if(!isset($_GET['id'])){
    header("Location: merit_list.php?error=1");
    exit;
}

$id=(int)$_GET['id'];

/*=========================================
CHECK APPLICATION
=========================================*/

$stmt=$conn->prepare("

SELECT id, application_status

FROM applications

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$id);
$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

    header("Location: merit_list.php?error=1");
    exit;
}

/*=========================================
UPDATE STATUS
=========================================*/

$update=$conn->prepare("

UPDATE applications

SET application_status='Selected'

WHERE id=?

");

$update->bind_param("i",$id);

if($update->execute()){

    header("Location: selected_students.php?success=1");
    exit;

}else{

    header("Location: merit_list.php?error=1");
    exit;
}

?>
