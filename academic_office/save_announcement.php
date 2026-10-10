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

$admin_id = $_SESSION['admin_id'];

/*=========================================
POST CHECK
=========================================*/

if($_SERVER['REQUEST_METHOD']!="POST"){

header("Location: add_announcement.php");
exit;

}

/*=========================================
GET FORM DATA
=========================================*/

$title = trim($_POST['title']);

$description = trim($_POST['description']);

$department = trim($_POST['department']);

$status = trim($_POST['status']);

/*=========================================
VALIDATION
=========================================*/

if(empty($title)){

$_SESSION['error']="Announcement Title Required";

header("Location: add_announcement.php");
exit;

}

if(empty($description)){

$_SESSION['error']="Announcement Description Required";

header("Location: add_announcement.php");
exit;

}

/*=========================================
INSERT ANNOUNCEMENT
=========================================*/

$insert = $conn->prepare("

INSERT INTO announcements(

title,
description,
department,
created_by,
status

)

VALUES(

?,?,?,?,?

)

");

$insert->bind_param(

"sssis",

$title,
$description,
$department,
$admin_id,
$status

);

if($insert->execute()){

$_SESSION['success']="Announcement Published Successfully";

}else{

$_SESSION['error']="Failed To Publish Announcement";

}
/*=========================================
REDIRECT
=========================================*/

header("Location: manage_announcements.php");
exit;

?>