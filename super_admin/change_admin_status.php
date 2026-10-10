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

$sql=$conn->prepare("

SELECT status

FROM admins

WHERE id=?

LIMIT 1

");

$sql->bind_param("i",$id);

$sql->execute();

$result=$sql->get_result();

if($result->num_rows==0){

header("Location: manage_admins.php");
exit;

}

$row=$result->fetch_assoc();

$newStatus=($row['status']=="Active") ? "Inactive" : "Active";

$update=$conn->prepare("

UPDATE admins

SET status=?

WHERE id=?

");

$update->bind_param("si",$newStatus,$id);

$update->execute();

header("Location: manage_admins.php?status=changed");

exit;

?>