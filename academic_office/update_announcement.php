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
POST CHECK
=========================================*/

if($_SERVER['REQUEST_METHOD']!="POST"){

header("Location: manage_announcements.php");
exit;

}

/*=========================================
GET FORM DATA
=========================================*/

$id = intval($_POST['id']);

$title = trim($_POST['title']);

$description = trim($_POST['description']);

$department = trim($_POST['department']);

$status = trim($_POST['status']);

/*=========================================
VALIDATION
=========================================*/

if(empty($title)){

$_SESSION['error']="Announcement Title is Required";

header("Location: edit_announcement.php?id=".$id);
exit;

}

if(empty($description)){

$_SESSION['error']="Announcement Description is Required";

header("Location: edit_announcement.php?id=".$id);
exit;

}

/*=========================================
UPDATE ANNOUNCEMENT
=========================================*/

$update = $conn->prepare("

UPDATE announcements

SET

title=?,
description=?,
department=?,
status=?

WHERE id=?

");

$update->bind_param(

"ssssi",

$title,
$description,
$department,
$status,
$id

);

if($update->execute()){

$_SESSION['success']="Announcement Updated Successfully";

}else{

$_SESSION['error']="Failed To Update Announcement";

}
/*=========================================
REDIRECT
=========================================*/

header("Location: manage_announcements.php");
exit;

?>