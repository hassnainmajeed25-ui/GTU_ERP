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

if($_SESSION['department']!="Student Affairs"){
    header("Location: ../admins/login.php");
    exit;
}

/*=========================================
VALIDATE DATA
=========================================*/

if(!isset($_GET['id']) || !isset($_GET['status'])){

header("Location: manage_students.php");

exit;

}

$student_id=(int)$_GET['id'];

$status=$_GET['status'];

if($status!="Active" && $status!="Inactive"){

header("Location: manage_students.php");

exit;

}

/*=========================================
UPDATE STATUS
=========================================*/

$stmt=$conn->prepare("

UPDATE students

SET status=?

WHERE id=?

");

$stmt->bind_param(

"si",

$status,

$student_id

);

$stmt->execute();

/*=========================================
REDIRECT
=========================================*/

header("Location: manage_students.php?status_updated=1");

exit;

?>

