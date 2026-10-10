<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

    header("Location: login.php");
    exit;

}

if(!isset($_GET['id'])){

    header("Location: manage_sessions.php");
    exit;

}

$id=(int)$_GET['id'];

$get=$conn->prepare("
SELECT status
FROM admission_sessions
WHERE id=?
LIMIT 1
");

$get->bind_param("i",$id);
$get->execute();

$result=$get->get_result();

if($result->num_rows==0){

    header("Location: manage_sessions.php");
    exit;

}

$row=$result->fetch_assoc();

$newStatus=($row['status']=="Active") ? "Inactive" : "Active";

$update=$conn->prepare("
UPDATE admission_sessions
SET status=?
WHERE id=?
");

$update->bind_param("si",$newStatus,$id);
$update->execute();

header("Location: manage_sessions.php");
exit;

?>