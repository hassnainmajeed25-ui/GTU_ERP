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
GET ANNOUNCEMENT ID
=========================================*/

if(!isset($_GET['id'])){

header("Location: announcements.php");

exit;

}

$id=(int)$_GET['id'];

/*=========================================
CHECK ANNOUNCEMENT
=========================================*/

$check=$conn->prepare("

SELECT id

FROM announcements

WHERE id=?

LIMIT 1

");

$check->bind_param("i",$id);

$check->execute();

$result=$check->get_result();

if($result->num_rows==0){

header("Location: announcements.php");

exit;

}

/*=========================================
DELETE ANNOUNCEMENT
=========================================*/

$delete=$conn->prepare("

DELETE FROM announcements

WHERE id=?

");

$delete->bind_param("i",$id);

$delete->execute();

/*=========================================
REDIRECT
=========================================*/

header("Location: announcements.php?success=deleted");

exit;

?>
