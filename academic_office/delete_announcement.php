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

if($_SESSION['department']!="Academic Office"){

header("Location: ../admins/login.php");
exit;

}

/*=========================================
CHECK ID
=========================================*/

if(!isset($_GET['id'])){

header("Location: manage_announcements.php");
exit;

}

$id = intval($_GET['id']);

/*=========================================
DELETE ANNOUNCEMENT
=========================================*/

$delete = $conn->prepare("

DELETE FROM announcements

WHERE id=?

LIMIT 1

");

$delete->bind_param("i",$id);

if($delete->execute()){

$_SESSION['success']="Announcement Deleted Successfully.";

}else{

$_SESSION['error']="Failed To Delete Announcement.";

}

/*=========================================
REDIRECT
=========================================*/

header("Location: manage_announcements.php");
exit;

?>