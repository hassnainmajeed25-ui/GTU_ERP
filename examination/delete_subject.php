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

if($_SESSION['department']!="Examination Office"){
    header("Location: ../admins/login.php");
    exit;
}

/*=========================================
CHECK ID
=========================================*/

if(!isset($_GET['id'])){
    header("Location: manage_subjects.php");
    exit;
}

$id=(int)$_GET['id'];

/*=========================================
DELETE SUBJECT
=========================================*/

$delete=$conn->prepare("
DELETE FROM subjects
WHERE id=?
LIMIT 1
");

$delete->bind_param("i",$id);

if($delete->execute()){

    header("Location: manage_subjects.php?deleted=1");
    exit;

}else{

    header("Location: manage_subjects.php?deleted=0");
    exit;

}

?>