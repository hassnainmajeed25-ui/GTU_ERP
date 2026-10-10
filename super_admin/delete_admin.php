<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){

header("Location: login.php");
exit;

}

if(!isset($_GET['id'])){

header("Location: manage_admins.php");
exit;

}

$id=(int)$_GET['id'];

/*==========================
CHECK ADMIN EXISTS
==========================*/

$check=$conn->prepare("

SELECT id

FROM admins

WHERE id=?

LIMIT 1

");

$check->bind_param("i",$id);

$check->execute();

$result=$check->get_result();

if($result->num_rows==0){

header("Location: manage_admins.php");
exit;

}

/*==========================
DELETE ADMIN
==========================*/

$delete=$conn->prepare("

DELETE FROM admins

WHERE id=?

LIMIT 1

");

$delete->bind_param("i",$id);

$delete->execute();

header("Location: manage_admins.php?deleted=1");

exit;

?>

