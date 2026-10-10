<?php

require_once '../config/config.php';
require_once '../config/database.php';

if(!isset($_SESSION['super_admin_id'])){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: manage_programs.php");
    exit;
}

$id=(int)$_GET['id'];

$delete=$conn->prepare("DELETE FROM programs WHERE id=?");
$delete->bind_param("i",$id);
$delete->execute();

header("Location: manage_programs.php");
exit;

?>