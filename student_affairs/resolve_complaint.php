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

if(!isset($_GET['id'])){

header("Location: complaints.php");

exit;

}

$id=(int)$_GET['id'];

/*=========================================
CHECK COMPLAINT
=========================================*/

$stmt=$conn->prepare("

SELECT id,status

FROM complaints

WHERE id=?

LIMIT 1

");

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

if($result->num_rows==0){

header("Location: complaints.php");

exit;

}

$row=$result->fetch_assoc();

/*=========================================
UPDATE STATUS
=========================================*/

if($row['status']!="Resolved"){

$update=$conn->prepare("

UPDATE complaints

SET status='Resolved'

WHERE id=?

");

$update->bind_param("i",$id);

$update->execute();

}

/*=========================================
REDIRECT
=========================================*/

header("Location: complaints.php?success=resolved");

exit;

?>
