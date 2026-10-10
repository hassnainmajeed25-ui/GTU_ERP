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

$admin_id=$_SESSION['admin_id'];

/*=========================================
VALIDATION
=========================================*/

if(!isset($_GET['id']) || !isset($_GET['action'])){

header("Location: student_requests.php");

exit;

}

$request_id=(int)$_GET['id'];

$action=$_GET['action'];

if($action!="approve" && $action!="reject"){

header("Location: student_requests.php");

exit;

}

/*=========================================
APPROVE REQUEST
=========================================*/

if($action=="approve"){

$stmt=$conn->prepare("

UPDATE applications

SET

application_status='Approved',

approved_by=?,

approved_at=NOW()

WHERE id=?

");

$stmt->bind_param(

"ii",

$admin_id,

$request_id

);

$stmt->execute();

}

/*=========================================
REJECT REQUEST
=========================================*/

if($action=="reject"){

$stmt=$conn->prepare("

UPDATE applications

SET

application_status='Rejected',

rejected_by=?,

rejected_at=NOW()

WHERE id=?

");

$stmt->bind_param(

"ii",

$admin_id,

$request_id

);

$stmt->execute();

}

/*=========================================
REDIRECT
=========================================*/

header("Location: student_requests.php?success=1");

exit;

?>

